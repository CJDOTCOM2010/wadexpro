require('dotenv').config();
const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const Redis = require('ioredis');
const jwt = require('jsonwebtoken');

const app = express();
app.use(express.json());

const server = http.createServer(app);

// ---------------------------------------------------------------------------
// CORS Configuration
// ---------------------------------------------------------------------------
const ALLOWED_ORIGINS = (process.env.CORS_ORIGINS || '*').split(',');

const io = new Server(server, {
  cors: {
    origin: ALLOWED_ORIGINS,
    methods: ['GET', 'POST'],
    credentials: true
  },
  pingTimeout: 60000,
  pingInterval: 25000,
});

// ---------------------------------------------------------------------------
// Redis Connections (subscriber + publisher + geospatial)
// ---------------------------------------------------------------------------
const redisConfig = {
  host: process.env.REDIS_HOST || '127.0.0.1',
  port: parseInt(process.env.REDIS_PORT || '6379'),
  password: process.env.REDIS_PASSWORD || undefined,
};

const redisSub = new Redis(redisConfig);
const redisPub = new Redis(redisConfig);
const redisGeo = new Redis(redisConfig);

// Geospatial keys for driver and demand tracking
const DRIVER_GEO_KEY = 'drivers:geo:locations';
const DRIVER_DATA_KEY = 'drivers:data:';
const DEMAND_GEO_KEY = 'demand:intent:geo'; // Redis key for search intent tracking

// ---------------------------------------------------------------------------
// Health Check Endpoint
// ---------------------------------------------------------------------------
app.get('/health', (req, res) => {
  res.json({
    status: 'ok',
    uptime: process.uptime(),
    connections: io.engine.clientsCount,
    timestamp: new Date().toISOString()
  });
});

// ---------------------------------------------------------------------------
// JWT Authentication Middleware for Socket.IO
// ---------------------------------------------------------------------------
const authenticateSocket = async (socket, next) => {
  try {
    const token = socket.handshake.auth?.token ||
      socket.handshake.headers?.authorization?.replace('Bearer ', '');

    if (!token) {
      return next(new Error('Authentication required'));
    }

    // Verify the token against Laravel Sanctum tokens in Redis/DB
    // For Sanctum, we verify against the hashed token stored in personal_access_tokens
    const decoded = await verifyToken(token);

    if (!decoded) {
      return next(new Error('Invalid or expired token'));
    }

    socket.userId = decoded.userId;
    socket.userType = decoded.userType;
    socket.userName = decoded.userName;

    next();
  } catch (err) {
    console.error('Socket auth error:', err.message);
    next(new Error('Authentication failed'));
  }
};

/**
 * Verify a Sanctum token by checking Redis cache or database.
 * In production, you'd verify against the personal_access_tokens table.
 * For the socket server, we use a shared JWT secret.
 */
async function verifyToken(token) {
  try {
    // Check if this is a JWT token (issued by our Auth module)
    const secret = process.env.JWT_SECRET || process.env.APP_KEY || 'fallback-secret';
    const decoded = jwt.verify(token, secret);
    return {
      userId: decoded.sub || decoded.user_id,
      userType: decoded.user_type || 'customer',
      userName: decoded.name || 'Unknown',
    };
  } catch (e) {
    // Fallback: Check Sanctum token hash in Redis
    const cachedUser = await redisGeo.get(`sanctum_token:${token.substring(0, 40)}`);
    if (cachedUser) {
      return JSON.parse(cachedUser);
    }
    return null;
  }
}

// Apply authentication to all connections
io.use(authenticateSocket);

// ---------------------------------------------------------------------------
// Socket.IO Namespaces
// ---------------------------------------------------------------------------

// =================== RIDER NAMESPACE ===================
const riderNs = io.of('/rider');
riderNs.use(authenticateSocket);

riderNs.on('connection', (socket) => {
  console.log(`Rider connected: ${socket.userId} (${socket.id})`);

  // Join personal room for targeted events
  socket.join(`user:${socket.userId}`);

  // Subscribe to a ride's updates
  socket.on('ride:subscribe', (rideId) => {
    socket.join(`ride:${rideId}`);
    console.log(`Rider ${socket.userId} subscribed to ride:${rideId}`);
  });

  // Unsubscribe from ride tracking
  socket.on('ride:unsubscribe', (rideId) => {
    socket.leave(`ride:${rideId}`);
  });

  // Query nearby available drivers
  socket.on('drivers:query_nearby', async (data) => {
    try {
      const { lat, lng, radius = 5000 } = data;
      // Search Redis Geo index
      const driverIds = await redisGeo.georadius(DRIVER_GEO_KEY, lng, lat, radius, 'm');
      
      const nearbyDrivers = [];
      for (const driverId of driverIds) {
        const driverDataStr = await redisGeo.get(`${DRIVER_DATA_KEY}${driverId}`);
        if (driverDataStr) {
          const driverData = JSON.parse(driverDataStr);
          // Only include if active in last 60 seconds
          if (Date.now() - driverData.lastUpdate < 60000) {
            nearbyDrivers.push({
              driverId,
              lat: driverData.lat,
              lng: driverData.lng,
              heading: driverData.heading,
              vehicleType: driverData.vehicleType || 'economy',
            });
          }
        }
      }
      socket.emit('drivers:nearby', nearbyDrivers);
    } catch (err) {
      console.error('Error querying nearby drivers:', err);
    }
  });

  // Track search intent (Real-time Demand Monitoring)
  socket.on('ride:search', async (data) => {
    try {
      const { lat, lng } = data;
      const timestamp = Date.now();
      const intentId = `search:${socket.userId}:${timestamp}`;

      // Store in Redis Geo index for heatmap & surge calculation
      await redisGeo.geoadd(DEMAND_GEO_KEY, lng, lat, intentId);
      
      // Also store in a sorted set for expiry tracking (score = timestamp)
      await redisGeo.zadd('demand:intent:expiry', timestamp, intentId);

      // Notify admin namespace for real-time heatmap updates
      adminNs.emit('demand:new_intent', {
        lat,
        lng,
        userId: socket.userId,
        timestamp
      });

      console.log(`Demand intent logged: ${socket.userId} at ${lat},${lng}`);
    } catch (err) {
      console.error('Error logging search intent:', err);
    }
  });

  // Request a ride (broadcast to nearby drivers)
  socket.on('ride:request', async (data) => {
    console.log(`Ride requested by ${socket.userId}:`, data);

    const rideEvent = {
      rideId: data.rideId,
      type: 'ride',
      customerId: socket.userId,
      customerName: socket.userName,
      pickupAddress: data.pickupAddress,
      pickupLat: data.pickupLat,
      pickupLng: data.pickupLng,
      dropoffAddress: data.dropoffAddress,
      dropoffLat: data.dropoffLat,
      dropoffLng: data.dropoffLng,
      vehicleType: data.vehicleType,
      estimatedPrice: data.estimatedPrice,
      timestamp: new Date().toISOString(),
    };

    // Find nearby drivers from Redis geospatial index
    try {
      const nearbyDrivers = await redisGeo.georadius(
        DRIVER_GEO_KEY,
        data.pickupLng,
        data.pickupLat,
        10, // 10 km radius
        'km',
        'ASC',
        'COUNT', 10
      );

      // Broadcast to nearby driver rooms
      for (const driverId of nearbyDrivers) {
        driverNs.to(`driver:${driverId}`).emit('ride:incoming', rideEvent);
      }

      // Also broadcast to admin live map
      adminNs.emit('ride:new_request', rideEvent);

      socket.emit('ride:searching', {
        rideId: data.rideId,
        driversNotified: nearbyDrivers.length,
        message: 'Looking for nearby drivers...',
      });
    } catch (err) {
      console.error('Error finding nearby drivers:', err);
      socket.emit('ride:error', { message: 'Failed to find nearby drivers.' });
    }
  });

  // Cancel a ride
  socket.on('ride:cancel', (data) => {
    io.of('/driver').to(`ride:${data.rideId}`).emit('ride:cancelled', {
      rideId: data.rideId,
      cancelledBy: 'rider',
      reason: data.reason,
    });
    adminNs.emit('ride:cancelled', { rideId: data.rideId, cancelledBy: 'rider' });
  });

  // Chat message from rider to driver
  socket.on('chat:send', (data) => {
    driverNs.to(`ride:${data.rideId}`).emit('chat:message', {
      from: socket.userId,
      fromName: socket.userName,
      fromType: 'rider',
      message: data.message,
      timestamp: new Date().toISOString(),
    });
  });

  // SOS emergency
  socket.on('sos:trigger', (data) => {
    adminNs.emit('sos:alert', {
      userId: socket.userId,
      userName: socket.userName,
      rideId: data.rideId,
      lat: data.lat,
      lng: data.lng,
      timestamp: new Date().toISOString(),
    });
    socket.emit('sos:confirmed', { message: 'Emergency alert sent. Help is on the way.' });
  });

  // Multistop Order Request
  socket.on('order:request', async (data) => {
    console.log(`Order requested by ${socket.userId}:`, data);
    
    const orderEvent = {
      rideId: data.orderId, // unified ID
      type: 'delivery',
      customerId: socket.userId,
      customerName: socket.userName,
      pickupAddress: data.pickupAddress,
      pickupLat: data.pickupLat,
      pickupLng: data.pickupLng,
      packageDescription: data.packageDescription,
      stops: data.stops, // List of {address, lat, lng, contact_name, contact_phone}
      priority: data.priority,
      estimatedPrice: data.estimatedPrice,
      timestamp: new Date().toISOString(),
    };

    try {
      const nearbyDrivers = await redisGeo.georadius(
        DRIVER_GEO_KEY,
        data.pickupLng,
        data.pickupLat,
        15, // 15 km radius
        'km',
        'ASC',
        'COUNT', 20
      );

      for (const driverId of nearbyDrivers) {
        driverNs.to(`driver:${driverId}`).emit('ride:incoming', orderEvent);
      }

      adminNs.emit('order:new_request', orderEvent);
      
      socket.emit('ride:searching', {
        rideId: data.orderId,
        driversNotified: nearbyDrivers.length,
      });
    } catch (err) {
       socket.emit('ride:error', { message: 'Failed to broadcast delivery order.' });
    }
  });

  socket.on('disconnect', () => {
    console.log(`Rider disconnected: ${socket.userId}`);
  });
});

// =================== DRIVER NAMESPACE ===================
const driverNs = io.of('/driver');
driverNs.use(authenticateSocket);

driverNs.on('connection', (socket) => {
  console.log(`Driver connected: ${socket.userId} (${socket.id})`);

  // Join personal driver room
  socket.join(`driver:${socket.userId}`);

  // Driver goes online
  socket.on('driver:online', async (data) => {
    try {
      await redisGeo.geoadd(DRIVER_GEO_KEY, data.lng, data.lat, socket.userId);
      await redisGeo.set(`${DRIVER_DATA_KEY}${socket.userId}`, JSON.stringify({
        name: socket.userName,
        vehicleType: data.vehicleType,
        lat: data.lat,
        lng: data.lng,
        lastUpdate: Date.now(),
      }));

      adminNs.emit('driver:status_change', {
        driverId: socket.userId,
        driverName: socket.userName,
        status: 'online',
        lat: data.lat,
        lng: data.lng,
      });

      console.log(`Driver ${socket.userId} is online at ${data.lat}, ${data.lng}`);
    } catch (err) {
      console.error('Error setting driver online:', err);
    }
  });

  // Driver goes offline
  socket.on('driver:offline', async () => {
    try {
      await redisGeo.zrem(DRIVER_GEO_KEY, socket.userId);
      await redisGeo.del(`${DRIVER_DATA_KEY}${socket.userId}`);

      adminNs.emit('driver:status_change', {
        driverId: socket.userId,
        driverName: socket.userName,
        status: 'offline',
      });
    } catch (err) {
      console.error('Error setting driver offline:', err);
    }
  });

  // GPS location update (high frequency — throttled to max 1/second)
  let lastLocationUpdate = 0;
  socket.on('driver:location', async (data) => {
    const now = Date.now();
    if (now - lastLocationUpdate < 1000) return; // Throttle
    lastLocationUpdate = now;

    try {
      // Update Redis geospatial index
      await redisGeo.geoadd(DRIVER_GEO_KEY, data.lng, data.lat, socket.userId);
      await redisGeo.set(`${DRIVER_DATA_KEY}${socket.userId}`, JSON.stringify({
        name: socket.userName,
        lat: data.lat,
        lng: data.lng,
        heading: data.heading,
        speed: data.speed,
        lastUpdate: now,
      }));

      // If driver is on active ride, broadcast to rider
      if (data.rideId) {
        riderNs.to(`ride:${data.rideId}`).emit('driver:location_update', {
          driverId: socket.userId,
          lat: data.lat,
          lng: data.lng,
          heading: data.heading,
          speed: data.speed,
          timestamp: new Date().toISOString(),
        });
      }

      // Broadcast to admin live map (throttled per driver on client)
      adminNs.emit('driver:location_update', {
        driverId: socket.userId,
        lat: data.lat,
        lng: data.lng,
        heading: data.heading,
      });

    } catch (err) {
      console.error('Error updating driver location:', err);
    }
  });

  // Accept a ride request
  socket.on('ride:accept', (data) => {
    const rideId = data.rideId;

    // Join the ride room
    socket.join(`ride:${rideId}`);

    // Notify the rider
    riderNs.to(`ride:${rideId}`).emit('ride:driver_assigned', {
      rideId: rideId,
      driverId: socket.userId,
      driverName: socket.userName,
      vehiclePlate: data.vehiclePlate,
      vehicleModel: data.vehicleModel,
      vehicleColor: data.vehicleColor,
      driverRating: data.driverRating,
      driverPhoto: data.driverPhoto,
      estimatedArrival: data.estimatedArrival,
    });

    // Notify admin
    adminNs.emit('ride:accepted', {
      rideId: rideId,
      driverId: socket.userId,
      driverName: socket.userName,
    });

    console.log(`Driver ${socket.userId} accepted ride ${rideId}`);
  });

  // Ride status updates
  socket.on('ride:status_update', (data) => {
    const statusEvent = {
      rideId: data.rideId,
      driverId: socket.userId,
      status: data.status, // 'driver_arrived', 'in_progress', 'completed'
      timestamp: new Date().toISOString(),
      metadata: data.metadata || {},
    };

    riderNs.to(`ride:${data.rideId}`).emit('ride:status_change', statusEvent);
    adminNs.emit('ride:status_change', statusEvent);

    // If completed, leave the ride room
    if (data.status === 'completed') {
      socket.leave(`ride:${data.rideId}`);
    }
  });

  // Reject a ride request
  socket.on('ride:reject', (data) => {
    adminNs.emit('ride:rejected', {
      rideId: data.rideId,
      driverId: socket.userId,
      reason: data.reason,
    });
  });

  // Chat message from driver to rider
  socket.on('chat:send', (data) => {
    riderNs.to(`ride:${data.rideId}`).emit('chat:message', {
      from: socket.userId,
      fromName: socket.userName,
      fromType: 'driver',
      message: data.message,
      timestamp: new Date().toISOString(),
    });
  });

  // Cleanup on disconnect
  socket.on('disconnect', async () => {
    console.log(`Driver disconnected: ${socket.userId}`);
    try {
      await redisGeo.zrem(DRIVER_GEO_KEY, socket.userId);
      await redisGeo.del(`${DRIVER_DATA_KEY}${socket.userId}`);
      adminNs.emit('driver:status_change', {
        driverId: socket.userId,
        status: 'offline',
      });
    } catch (err) {
      console.error('Error cleaning up driver:', err);
    }
  });
});

// =================== ADMIN NAMESPACE ===================
const adminNs = io.of('/admin');
adminNs.use(authenticateSocket);

adminNs.on('connection', (socket) => {
  // Only allow admin/super_admin users
  if (!['admin', 'super_admin'].includes(socket.userType)) {
    socket.disconnect(true);
    return;
  }

  console.log(`Admin connected: ${socket.userId} (${socket.id})`);

  // Request current driver positions for live map
  socket.on('map:request_drivers', async () => {
    try {
      const allDrivers = await redisGeo.zrange(DRIVER_GEO_KEY, 0, -1);
      const driverPositions = [];

      for (const driverId of allDrivers) {
        const pos = await redisGeo.geopos(DRIVER_GEO_KEY, driverId);
        const data = await redisGeo.get(`${DRIVER_DATA_KEY}${driverId}`);

        if (pos && pos[0]) {
          driverPositions.push({
            driverId,
            lng: parseFloat(pos[0][0]),
            lat: parseFloat(pos[0][1]),
            ...(data ? JSON.parse(data) : {}),
          });
        }
      }

      socket.emit('map:driver_positions', driverPositions);
    } catch (err) {
      console.error('Error fetching driver positions:', err);
    }
  });

  // Broadcast system-wide messages
  socket.on('system:broadcast', (data) => {
    riderNs.emit('system:notification', data);
    driverNs.emit('system:notification', data);
  });

  socket.on('disconnect', () => {
    console.log(`Admin disconnected: ${socket.userId}`);
  });
});

// ---------------------------------------------------------------------------
// Redis Subscriber — Listen for Laravel Broadcast Events
// ---------------------------------------------------------------------------
redisSub.psubscribe('*', (err, count) => {
  if (err) {
    console.error('Failed to subscribe to Redis channels:', err);
  } else {
    console.log(`Subscribed to ${count} Redis channel patterns.`);
  }
});

redisSub.on('pmessage', (pattern, channel, message) => {
  let payload = {};
  try {
    payload = JSON.parse(message);
  } catch (e) {
    return;
  }

  const eventName = payload.event || 'unknown';
  const eventData = payload.data || {};

  // Route Laravel events to appropriate Socket.IO rooms
  if (channel.startsWith('order_tracking:')) {
    const orderId = channel.split(':')[1];
    // Route to multi-stop subscribers in all namespaces
    riderNs.to(`ride:${orderId}`).emit(eventName, eventData);
    driverNs.to(`ride:${orderId}`).emit(eventName, eventData);
    adminNs.emit(`order:update:${orderId}`, eventData);
  }

  if (channel.startsWith('ride:')) {
    const rideId = channel.split(':')[1];
    riderNs.to(`ride:${rideId}`).emit(eventName, eventData);
    driverNs.to(`ride:${rideId}`).emit(eventName, eventData);
  }

  if (channel.startsWith('user:') || channel.startsWith('user_notifications:')) {
    const userId = channel.split(':')[1];
    riderNs.to(`user:${userId}`).emit(eventName, eventData);
  }

  if (channel.startsWith('driver_notifications:')) {
    const driverId = channel.split(':')[1];
    driverNs.to(`driver:${driverId}`).emit(eventName, eventData);
  }

  // Global admin events
  if (channel === 'admin:notifications') {
    adminNs.emit(eventName, eventData);
  }
});

// ---------------------------------------------------------------------------
// Graceful Shutdown
// ---------------------------------------------------------------------------
const gracefulShutdown = async () => {
  console.log('Shutting down gracefully...');

  io.close();
  await redisSub.quit();
  await redisPub.quit();
  await redisGeo.quit();

  server.close(() => {
    console.log('Server closed.');
    process.exit(0);
  });

  // Force shutdown after 10 seconds
  setTimeout(() => process.exit(1), 10000);
};

process.on('SIGTERM', gracefulShutdown);
process.on('SIGINT', gracefulShutdown);

// ---------------------------------------------------------------------------
// Periodic Cleanup Tasks
// ---------------------------------------------------------------------------
setInterval(async () => {
  const fiveMinutesAgo = Date.now() - (5 * 60 * 1000);
  try {
    // Get expired intent IDs
    const expiredIds = await redisGeo.zrangebyscore('demand:intent:expiry', '-inf', fiveMinutesAgo);
    
    if (expiredIds.length > 0) {
      // Remove from Geo index and Expiry set
      await redisGeo.zremrangebyscore('demand:intent:expiry', '-inf', fiveMinutesAgo);
      await redisGeo.zrem(DEMAND_GEO_KEY, ...expiredIds);
      console.log(`Pruned ${expiredIds.length} expired search intents.`);
    }
  } catch (err) {
    console.error('Error in demand pruning task:', err);
  }
}, 60000); // Run every minute

// ---------------------------------------------------------------------------
// Start Server
// ---------------------------------------------------------------------------
const PORT = process.env.SOCKET_PORT || process.env.PORT || 3002;
server.listen(PORT, () => {
  console.log(`WadExp Real-Time Socket Server running on port ${PORT}`);
  console.log(`Health check: http://localhost:${PORT}/health`);
});
