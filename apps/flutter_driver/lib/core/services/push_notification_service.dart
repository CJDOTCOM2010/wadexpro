import 'dart:convert';
import 'dart:io';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../network/api_client.dart';
import '../network/providers.dart';

/// Top-level background message handler (must be top-level function)
@pragma('vm:entry-point')
Future<void> firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  debugPrint('WADEXPRO Driver: Background message received: ${message.messageId}');
}

/// Core push notification service for the Driver app.
/// Handles FCM token registration, foreground/background notification display,
/// and deep-link routing when a notification is tapped.
class PushNotificationService {
  final ApiClient _apiClient;
  final FlutterLocalNotificationsPlugin _localNotifications = FlutterLocalNotificationsPlugin();

  PushNotificationService(this._apiClient);

  /// Initialize the entire push notification pipeline.
  Future<void> initialize() async {
    if (kIsWeb) {
      debugPrint('WADEXPRO Driver: Push notifications not supported on web.');
      return;
    }

    // 1. Request permission (iOS and Android 13+)
    final settings = await FirebaseMessaging.instance.requestPermission(
      alert: true,
      announcement: false,
      badge: true,
      carPlay: false,
      criticalAlert: true, // Drivers need critical alerts for ride requests
      provisional: false,
      sound: true,
    );

    debugPrint('WADEXPRO Driver: Push permission status: ${settings.authorizationStatus}');

    if (settings.authorizationStatus == AuthorizationStatus.denied) {
      debugPrint('WADEXPRO Driver: Push notifications denied by user.');
      return;
    }

    // 2. Configure local notifications for foreground display
    await _setupLocalNotifications();

    // 3. Get and register the FCM token with our backend
    await _registerToken();

    // 4. Listen for token refreshes
    FirebaseMessaging.instance.onTokenRefresh.listen((newToken) {
      _sendTokenToServer(newToken);
    });

    // 5. Handle foreground messages
    FirebaseMessaging.onMessage.listen(_handleForegroundMessage);

    // 6. Handle notification taps (when app is in background/terminated)
    FirebaseMessaging.onMessageOpenedApp.listen(_handleNotificationTap);

    // 7. Check if the app was opened from a terminated state via notification
    final initialMessage = await FirebaseMessaging.instance.getInitialMessage();
    if (initialMessage != null) {
      _handleNotificationTap(initialMessage);
    }

    debugPrint('WADEXPRO Driver: Push notification service initialized successfully.');
  }

  /// Setup the local notification plugin for foreground display.
  Future<void> _setupLocalNotifications() async {
    const androidSettings = AndroidInitializationSettings('@mipmap/ic_launcher');
    const iosSettings = DarwinInitializationSettings(
      requestAlertPermission: false,
      requestBadgePermission: false,
      requestSoundPermission: false,
    );

    await _localNotifications.initialize(
      const InitializationSettings(android: androidSettings, iOS: iosSettings),
      onDidReceiveNotificationResponse: (details) {
        if (details.payload != null) {
          final data = jsonDecode(details.payload!);
          debugPrint('WADEXPRO Driver: Local notification tapped with data: $data');
        }
      },
    );

    // Create high-priority Android notification channel for ride requests
    if (Platform.isAndroid) {
      const rideChannel = AndroidNotificationChannel(
        'wadexpro_high',
        'Ride Requests',
        description: 'High-priority alerts for incoming ride requests',
        importance: Importance.max,
        playSound: true,
        enableVibration: true,
      );
      
      const generalChannel = AndroidNotificationChannel(
        'wadexpro_general',
        'General Notifications',
        description: 'Order updates and support messages',
        importance: Importance.high,
        playSound: true,
      );

      final androidPlugin = _localNotifications.resolvePlatformSpecificImplementation<AndroidFlutterLocalNotificationsPlugin>();
      await androidPlugin?.createNotificationChannel(rideChannel);
      await androidPlugin?.createNotificationChannel(generalChannel);
    }
  }

  /// Get the FCM device token and send it to our backend.
  Future<void> _registerToken() async {
    try {
      final token = await FirebaseMessaging.instance.getToken();
      if (token != null) {
        debugPrint('WADEXPRO Driver: FCM Token: ${token.substring(0, 20)}...');
        await _sendTokenToServer(token);
      }
    } catch (e) {
      debugPrint('WADEXPRO Driver: Failed to get FCM token: $e');
    }
  }

  /// Send the FCM token to the Laravel backend.
  Future<void> _sendTokenToServer(String token) async {
    try {
      await _apiClient.instance.patch('/v1/auth/fcm-token', data: {
        'fcm_token': token,
      });
      debugPrint('WADEXPRO Driver: FCM token registered with server.');
    } catch (e) {
      debugPrint('WADEXPRO Driver: Failed to register FCM token: $e');
    }
  }

  /// Handle messages received while the app is in the foreground.
  void _handleForegroundMessage(RemoteMessage message) {
    debugPrint('WADEXPRO Driver: Foreground message: ${message.notification?.title}');

    final notification = message.notification;
    if (notification == null) return;

    // Determine channel based on message type
    final type = message.data['type'] ?? '';
    final isRideRequest = type == 'ride_request' || type == 'ride_incoming';
    final channelId = isRideRequest ? 'wadexpro_high' : 'wadexpro_general';
    final channelName = isRideRequest ? 'Ride Requests' : 'General Notifications';

    _localNotifications.show(
      message.hashCode,
      notification.title ?? 'WADEXPRO',
      notification.body ?? '',
      NotificationDetails(
        android: AndroidNotificationDetails(
          channelId,
          channelName,
          importance: isRideRequest ? Importance.max : Importance.high,
          priority: Priority.high,
          icon: '@mipmap/ic_launcher',
          playSound: true,
          enableVibration: true,
          fullScreenIntent: isRideRequest, // Full-screen for ride requests
        ),
        iOS: const DarwinNotificationDetails(
          presentAlert: true,
          presentBadge: true,
          presentSound: true,
        ),
      ),
      payload: jsonEncode(message.data),
    );
  }

  /// Handle when a user taps on a notification.
  void _handleNotificationTap(RemoteMessage message) {
    debugPrint('WADEXPRO Driver: Notification tap — data: ${message.data}');
    final type = message.data['type'];
    switch (type) {
      case 'ride_request':
      case 'ride_incoming':
        debugPrint('WADEXPRO Driver: Navigate to ride request');
        break;
      case 'payout_processed':
        debugPrint('WADEXPRO Driver: Navigate to wallet');
        break;
      case 'kyc_approved':
        debugPrint('WADEXPRO Driver: Navigate to profile');
        break;
      case 'support_message':
        debugPrint('WADEXPRO Driver: Navigate to support');
        break;
      default:
        debugPrint('WADEXPRO Driver: No deep link for type: $type');
    }
  }
}

/// Riverpod provider for the push notification service.
final pushNotificationServiceProvider = Provider<PushNotificationService>((ref) {
  final apiClient = ref.watch(apiClientProvider);
  return PushNotificationService(apiClient);
});
