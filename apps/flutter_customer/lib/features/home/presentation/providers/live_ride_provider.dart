import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:wadexpro_customer/core/services/socket_service.dart';
import 'package:wadexpro_customer/core/network/api_provider.dart';
import 'live_ride_state.dart';
import '../../data/ride_repository.dart';
import 'ride_booking_provider.dart';

final liveRideProvider = StateNotifierProvider<LiveRideNotifier, LiveRideState>((ref) {
  final socketService = ref.watch(socketServiceProvider);
  final repository = ref.watch(rideRepositoryProvider);
  return LiveRideNotifier(socketService, repository);
});

class LiveRideNotifier extends StateNotifier<LiveRideState> {
  final SocketService _socketService;
  final RideRepository _repository;

  LiveRideNotifier(this._socketService, this._repository) : super(LiveRideState());

  Future<void> requestRide(Map<String, dynamic> details) async {
    state = state.copyWith(status: LiveRideStatus.searching);
    
    try {
      final ride = await _repository.createRideRequest(
        pickup: details['pickup'],
        dropoff: details['dropoff'],
        pickupAddress: details['pickupAddress'] ?? "Current Location",
        dropoffAddress: details['dropoffAddress'] ?? "Destination",
        vehicleType: details['vehicleType'],
        promoCode: details['promoCode'],
      );

      state = state.copyWith(
        rideId: ride['id'],
        surgeMultiplier: (ride['surge_multiplier'] as num?)?.toDouble() ?? 1.0,
      );

      // Start socket loop
      _initializeListeners();
      
    } catch (e) {
      state = state.copyWith(status: LiveRideStatus.error, errorMessage: e.toString());
    }
  }

  void startTracking() {
    _initializeListeners();
  }

  void _initializeListeners() {

    _socketService.on('ride:searching', (data) {
      state = state.copyWith(
        status: LiveRideStatus.searching,
        driversNotified: data['driversNotified'],
      );
    });

    _socketService.on('ride:driver_assigned', (data) {
      state = state.copyWith(
        status: LiveRideStatus.matched,
        driverData: data,
      );
      // Data contains the rideId, subscribe to tracking room
      _socketService.subscribeToRide(data['rideId']?.toString() ?? '');
    });

    _socketService.on('driver:location_update', (data) {
      state = state.copyWith(
        driverLocation: LatLng(data['lat'], data['lng']),
        driverHeading: (data['heading'] as num?)?.toDouble(),
      );
    });

    _socketService.on('ride:status_change', (data) {
      final status = _mapStatus(data['status']);
      state = state.copyWith(status: status);
    });

    _socketService.on('ride:error', (data) {
      state = state.copyWith(
        status: LiveRideStatus.error,
        errorMessage: data['message'],
      );
    });

    // Logistics / Delivery Listeners
    _socketService.on('order:stop_updated', (data) {
       // We can trigger a snackbar or sound in the UI
       // and refresh the order data if necessary
    });

    _socketService.on('order:status_update', (data) {
      final status = _mapStatus(data['status']);
      state = state.copyWith(status: status);
    });
  }

  void cancelRide() {
    if (state.rideId != null) {
      _socketService.emit('ride:cancel', {'rideId': state.rideId});
    }
    state = LiveRideState(); // Reset to idle
  }

  LiveRideStatus _mapStatus(String backendStatus) {
    switch (backendStatus) {
      case 'driver_arrived': return LiveRideStatus.arrived;
      case 'in_progress': return LiveRideStatus.inProgress;
      case 'completed': return LiveRideStatus.completed;
      case 'cancelled': return LiveRideStatus.cancelled;
      default: return state.status;
    }
  }

  @override
  void dispose() {
    _socketService.disconnect();
    super.dispose();
  }
}
