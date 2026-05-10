import 'package:socket_io_client/socket_io_client.dart' as IO;
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class SocketService {
  IO.Socket? _socket;
  final String _url = 'http://localhost:3002/rider'; // Local Node Server
  final _storage = const FlutterSecureStorage();

  IO.Socket get socket => _socket!;

  Future<void> connect(String url, String namespace) async {
    final token = await _storage.read(key: 'access_token');
    
    _socket = IO.io('$url$namespace', IO.OptionBuilder()
      .setTransports(['websocket'])
      .setAuth({'token': token})
      .disableAutoConnect()
      .build());

    _socket!.connect();

    _socket!.onConnect((_) {
      print('Connected to WADEXP Real-time Engine');
    });

    _socket!.onDisconnect((_) {
      print('Disconnected from Real-time Engine');
    });

    _socket!.onConnectError((err) => print('Socket connection error: $err'));
  }

  void subscribeToRide(String rideId) {
    _socket?.emit('ride:subscribe', rideId);
  }

  void unsubscribeFromRide(String rideId) {
    _socket?.emit('ride:unsubscribe', rideId);
  }

  void disconnect() {
    _socket?.disconnect();
  }

  void requestRide(Map<String, dynamic> data) {
    _socket?.emit('ride:request', data);
  }

  void requestOrder(Map<String, dynamic> data) {
    _socket?.emit('order:request', data);
  }

  /// Listen for specific events.
  void on(String event, Function(dynamic) handler) {
    _socket?.on(event, handler);
  }

  /// Emit events.
  void emit(String event, dynamic data) {
    _socket?.emit(event, data);
  }
}
