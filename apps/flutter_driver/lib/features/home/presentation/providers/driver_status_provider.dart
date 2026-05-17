import 'dart:async';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:geolocator/geolocator.dart';
import '../../../../core/services/socket_service.dart';
import '../../../../core/services/location_service.dart';

import '../../../../core/network/providers.dart';
import '../../../../core/config/app_config.dart';
import '../../../auth/presentation/providers/auth_provider.dart';

final locationServiceProvider = Provider((ref) => LocationService());

class DriverStatusState {
  final bool isOnline;
  final Position? currentPosition;
  final String? activeRideId;

  DriverStatusState({this.isOnline = false, this.currentPosition, this.activeRideId});

  DriverStatusState copyWith({bool? isOnline, Position? currentPosition, String? activeRideId}) {
    return DriverStatusState(
      isOnline: isOnline ?? this.isOnline,
      currentPosition: currentPosition ?? this.currentPosition,
      activeRideId: activeRideId ?? this.activeRideId,
    );
  }
}

final driverStatusProvider = StateNotifierProvider<DriverStatusNotifier, DriverStatusState>((ref) {
  final socketService = ref.watch(socketServiceProvider);
  final locationService = ref.watch(locationServiceProvider);
  final apiClient = ref.watch(apiClientProvider);
  
  // Try to get authenticated driver ID from auth provider, or fallback
  final authState = ref.watch(authProvider);
  final driverId = authState.driver?['id']?.toString() ?? authState.driver?['phone'] ?? 'WADEX-NODE-T1';

  return DriverStatusNotifier(socketService, locationService, apiClient, driverId);
});

class DriverStatusNotifier extends StateNotifier<DriverStatusState> {
  final SocketService _socketService;
  final LocationService _locationService;
  final _apiClient;
  final String _driverId;
  StreamSubscription<Position>? _locationSubscription;
  DateTime? _lastUpdateTimestamp;

  DriverStatusNotifier(this._socketService, this._locationService, this._apiClient, this._driverId) : super(DriverStatusState());

  Future<void> toggleStatus() async {
    if (state.isOnline) {
      _goOffline();
    } else {
      await _goOnline();
    }
  }

  Future<void> _goOnline() async {
    try {
      final config = AppConfig.instance;
      _socketService.connect(config.socketUrl, '/driver');
      
      final position = await _locationService.getCurrentLocation();
      
      if (position != null) {
        _socketService.goOnline(position.latitude, position.longitude, 'economy', 'Driver');
        state = state.copyWith(isOnline: true, currentPosition: position);
        
        // Start streaming location with WADEX-Guard throttling (5s intervals)
        _locationSubscription = _locationService.getLocationStream().listen((newPosition) {
          state = state.copyWith(currentPosition: newPosition);
          
          final now = DateTime.now();
          if (_lastUpdateTimestamp == null || 
              now.difference(_lastUpdateTimestamp!).inSeconds >= 5) {
            
            _socketService.updateLocation(
              newPosition.latitude, 
              newPosition.longitude,
              heading: newPosition.heading,
              speed: newPosition.speed,
              rideId: state.activeRideId,
            );
            
            // Reverb telemetry via API
            try {
              _apiClient.post('/v1/telemetry/location', data: {
                'driverId': _driverId, 
                'latitude': newPosition.latitude,
                'longitude': newPosition.longitude,
                'heading': newPosition.heading,
                'isBusy': state.activeRideId != null,
              });
            } catch (e) {
              // Ignore telemetry failures to prevent crashing map
            }
            
            _lastUpdateTimestamp = now;
          }
        });
      }
    } catch (e) {
      print('Failed to go online: $e');
    }
  }

  void _goOffline() {
    _locationSubscription?.cancel();
    _socketService.goOffline();
    _socketService.disconnect();
    state = state.copyWith(isOnline: false);
  }

  @override
  void dispose() {
    _locationSubscription?.cancel();
    super.dispose();
  }
}
