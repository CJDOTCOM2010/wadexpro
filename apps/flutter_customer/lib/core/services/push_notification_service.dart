import 'dart:convert';
import 'dart:io';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../network/api_client.dart';

/// Top-level background message handler (must be top-level function)
@pragma('vm:entry-point')
Future<void> firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  debugPrint('WADEXPRO: Background message received: ${message.messageId}');
}

/// Core push notification service.
/// Handles FCM token registration, foreground/background notification display,
/// and deep-link routing when a notification is tapped.
class PushNotificationService {
  final ApiClient _apiClient;
  final FlutterLocalNotificationsPlugin _localNotifications = FlutterLocalNotificationsPlugin();

  PushNotificationService(this._apiClient);

  /// Initialize the entire push notification pipeline.
  Future<void> initialize() async {
    if (kIsWeb) {
      debugPrint('WADEXPRO: Push notifications not supported on web.');
      return;
    }

    // 1. Request permission (iOS and Android 13+)
    final settings = await FirebaseMessaging.instance.requestPermission(
      alert: true,
      announcement: false,
      badge: true,
      carPlay: false,
      criticalAlert: false,
      provisional: false,
      sound: true,
    );

    debugPrint('WADEXPRO: Push permission status: ${settings.authorizationStatus}');

    if (settings.authorizationStatus == AuthorizationStatus.denied) {
      debugPrint('WADEXPRO: Push notifications denied by user.');
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

    debugPrint('WADEXPRO: Push notification service initialized successfully.');
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
        // Handle tap on local notification
        if (details.payload != null) {
          final data = jsonDecode(details.payload!);
          debugPrint('WADEXPRO: Local notification tapped with data: $data');
        }
      },
    );

    // Create high-priority Android notification channel
    if (Platform.isAndroid) {
      const channel = AndroidNotificationChannel(
        'wadexpro_high',
        'WADEXPRO Notifications',
        description: 'Important notifications from WADEXPRO',
        importance: Importance.high,
        playSound: true,
        enableVibration: true,
      );
      await _localNotifications
          .resolvePlatformSpecificImplementation<AndroidFlutterLocalNotificationsPlugin>()
          ?.createNotificationChannel(channel);
    }
  }

  /// Get the FCM device token and send it to our backend.
  Future<void> _registerToken() async {
    try {
      final token = await FirebaseMessaging.instance.getToken();
      if (token != null) {
        debugPrint('WADEXPRO: FCM Token: ${token.substring(0, 20)}...');
        await _sendTokenToServer(token);
      }
    } catch (e) {
      debugPrint('WADEXPRO: Failed to get FCM token: $e');
    }
  }

  /// Send the FCM token to the Laravel backend.
  Future<void> _sendTokenToServer(String token) async {
    try {
      await _apiClient.instance.patch('/v1/auth/fcm-token', data: {
        'fcm_token': token,
      });
      debugPrint('WADEXPRO: FCM token registered with server.');
    } catch (e) {
      debugPrint('WADEXPRO: Failed to register FCM token: $e');
    }
  }

  /// Handle messages received while the app is in the foreground.
  void _handleForegroundMessage(RemoteMessage message) {
    debugPrint('WADEXPRO: Foreground message: ${message.notification?.title}');

    final notification = message.notification;
    if (notification == null) return;

    // Show as a local notification so the user sees it
    _localNotifications.show(
      message.hashCode,
      notification.title ?? 'WADEXPRO',
      notification.body ?? '',
      NotificationDetails(
        android: const AndroidNotificationDetails(
          'wadexpro_high',
          'WADEXPRO Notifications',
          channelDescription: 'Important notifications from WADEXPRO',
          importance: Importance.high,
          priority: Priority.high,
          icon: '@mipmap/ic_launcher',
          playSound: true,
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

  /// Handle when a user taps on a notification (background or terminated state).
  void _handleNotificationTap(RemoteMessage message) {
    debugPrint('WADEXPRO: Notification tap — data: ${message.data}');
    // Deep-link routing can be implemented here based on message.data
    // e.g., navigate to ride tracking, support chat, order details, etc.
    final type = message.data['type'];
    switch (type) {
      case 'ride_accepted':
        debugPrint('WADEXPRO: Navigate to ride tracking');
        break;
      case 'support_message':
        debugPrint('WADEXPRO: Navigate to support chat');
        break;
      case 'order_update':
        debugPrint('WADEXPRO: Navigate to order detail');
        break;
      default:
        debugPrint('WADEXPRO: No deep link for type: $type');
    }
  }
}

/// Riverpod provider for the push notification service.
final pushNotificationServiceProvider = Provider<PushNotificationService>((ref) {
  final apiClient = ref.watch(apiClientProvider);
  return PushNotificationService(apiClient);
});
