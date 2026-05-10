import 'dart:async';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:wadexpro_customer/core/services/socket_service.dart';
import 'package:wadexpro_customer/core/network/api_provider.dart';

class NearbyDriver {
  final String id;
  final LatLng location;
  final double? heading;
  final String vehicleType;

  NearbyDriver({
    required this.id,
    required this.location,
    required this.vehicleType,
    this.heading,
  });
}

final nearbyDriversProvider = StateNotifierProvider<NearbyDriversNotifier, List<NearbyDriver>>((ref) {
  final socketService = ref.watch(socketServiceProvider);
  return NearbyDriversNotifier(socketService);
});

class NearbyDriversNotifier extends StateNotifier<List<NearbyDriver>> {
  final SocketService _socketService;
  Timer? _queryTimer;

  NearbyDriversNotifier(this._socketService) : super([]) {
    _initializeListener();
  }

  void _initializeListener() {
    _socketService.on('drivers:nearby', (data) {
      if (data is List) {
        final drivers = data.map((d) => NearbyDriver(
          id: d['driverId'].toString(),
          location: LatLng(d['lat'], d['lng']),
          heading: (d['heading'] as num?)?.toDouble(),
          vehicleType: d['vehicleType'] ?? 'economy',
        )).toList();
        state = drivers;
      }
    });
  }

  void startPolling(LatLng center) {
    _queryTimer?.cancel();
    _queryTimer = Timer.periodic(const Duration(seconds: 10), (timer) {
      if (_socketService.isConnected) {
        _socketService.emit('drivers:query_nearby', {
          'lat': center.latitude,
          'lng': center.longitude,
          'radius': 5000, // 5km
        });
      }
    });
  }

  void stopPolling() {
    _queryTimer?.cancel();
    _queryTimer = null;
  }

  @override
  void dispose() {
    _queryTimer?.cancel();
    super.dispose();
  }
}
