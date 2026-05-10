import 'dart:async';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:socket_io_client/socket_io_client.dart' as io;

class SocketService {
  io.Socket? _socket;
  final _storage = const FlutterSecureStorage();
  
  final _connectionController = StreamController<bool>.broadcast();
  Stream<bool> get connectionStream => _connectionController.stream;

  final _eventController = StreamController<Map<String, dynamic>>.broadcast();
  Stream<Map<String, dynamic>> get eventStream => _eventController.stream;

  bool get isConnected => _socket?.connected ?? false;

  void connect(String url, String namespace, {Map<String, dynamic>? query}) async {
    final token = await _storage.read(key: 'access_token');
    
    _socket = io.io('$url$namespace', io.OptionBuilder()
      .setTransports(['websocket'])
      .setAuth({'token': token})
      .setQuery(query ?? {})
      .enableAutoConnect()
      .build());

    _socket!.onConnect((_) {
      print('Socket connected to $namespace');
      _connectionController.add(true);
    });

    _socket!.onDisconnect((_) {
      print('Socket disconnected from $namespace');
      _connectionController.add(false);
    });

    _socket!.onConnectError((err) => print('Socket connection error: $err'));
    
    _socket!.onAny((event, data) {
      _eventController.add({
        'event': event,
        'data': data,
      });
    });
  }

  void on(String event, Function(dynamic) handler) {
    _socket?.on(event, handler);
  }

  void off(String event) {
    _socket?.off(event);
  }

  void emit(String event, dynamic data) {
    if (_socket?.connected ?? false) {
      _socket!.emit(event, data);
    }
  }

  // --- Specialized Logistics Helpers ---

  void goOnline(double lat, double lng, String vehicleType, String name) {
    emit('driver:online', {
      'lat': lat,
      'lng': lng,
      'vehicleType': vehicleType,
      'name': name,
      'status': 'online',
    });
  }

  void goOffline() {
    emit('driver:offline', {});
  }

  void updateLocation(double lat, double lng, {double? heading, double? speed, String? rideId}) {
    emit('driver:location', {
      'lat': lat,
      'lng': lng,
      'heading': heading,
      'speed': speed,
      'rideId': rideId,
    });
  }

  void requestRide(Map<String, dynamic> data) {
    emit('ride:request', data);
  }

  // --- Chat Synchronization ---

  /// Send a real-time chat message.
  void sendChatMessage(String rideId, String content) {
    emit('chat:message', {
      'rideId': rideId,
      'content': content,
      'type': 'text',
    });
  }

  /// Listen for incoming chat messages.
  void onChatMessage(Function(Map<String, dynamic>) handler) {
    on('chat:message', (data) => handler(Map<String, dynamic>.from(data)));
  }

  void disconnect() {
    _socket?.disconnect();
    _socket?.dispose();
    _socket = null;
  }

  void dispose() {
    _connectionController.close();
    _eventController.close();
    disconnect();
  }
}
