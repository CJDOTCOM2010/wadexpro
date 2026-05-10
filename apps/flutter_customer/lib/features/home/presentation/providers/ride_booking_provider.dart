import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'live_ride_provider.dart';
import '../providers/ride_booking_state.dart';
import 'package:wadexpro_customer/features/home/data/ride_repository.dart';
import 'package:wadexpro_customer/core/network/api_provider.dart';
import 'package:wadexpro_customer/core/services/socket_service.dart'; // Needed for SocketService type

final rideRepositoryProvider = Provider((ref) {
  final apiClient = ref.watch(apiClientProvider);
  return RideRepository(apiClient);
});

final rideBookingProvider = StateNotifierProvider<RideBookingNotifier, RideBookingState>((ref) {
  final repository = ref.watch(rideRepositoryProvider);
  final socketService = ref.watch(socketServiceProvider);
  final liveRideNotifier = ref.watch(liveRideProvider.notifier);
  return RideBookingNotifier(repository, socketService, liveRideNotifier);
});

class RideBookingNotifier extends StateNotifier<RideBookingState> {
  final RideRepository _repository;
  final SocketService _socketService;
  final LiveRideNotifier _liveRideNotifier;

  RideBookingNotifier(this._repository, this._socketService, this._liveRideNotifier) : super(RideBookingState()) {
    _recoverState();
  }

  Future<void> _recoverState() async {
    try {
      final activeRide = await _repository.getActiveRide();
      if (activeRide != null) {
        state = state.copyWith(
          currentRideId: activeRide['id'].toString(),
          status: _mapStatusToBookingStatus(activeRide['status']),
          pickup: LatLng(
            double.parse(activeRide['pickup_lat'].toString()), 
            double.parse(activeRide['pickup_lng'].toString())
          ),
          pickupAddress: activeRide['pickup_address'],
          dropoff: LatLng(
            double.parse(activeRide['destination_lat'].toString()), 
            double.parse(activeRide['destination_lng'].toString())
          ),
          dropoffAddress: activeRide['destination_address'],
          selectedType: activeRide['vehicle_type'],
        );
        
        if (state.status != BookingStatus.idle) {
          _liveRideNotifier.startTracking();
        }
      }
    } catch (e) {
      // Fail silently for background recovery
    }
  }

  BookingStatus _mapStatusToBookingStatus(String status) {
    switch (status) {
      case 'searching': return BookingStatus.searching;
      case 'accepted': return BookingStatus.ready; // Or mapping to a 'tracking' sub-state
      case 'arrived': return BookingStatus.ready;
      case 'in_progress': return BookingStatus.ready;
      case 'completed': return BookingStatus.idle;
      default: return BookingStatus.idle;
    }
  }

  void setPickup(LatLng location, {String? address}) {
    state = state.copyWith(pickup: location, pickupAddress: address);
  }

  void setDropoff(LatLng location, {String? address}) {
    state = state.copyWith(
      dropoff: location, 
      dropoffAddress: address,
      status: BookingStatus.estimating,
    );
    _calculateFares();
  }

  void selectVehicle(String type) {
    state = state.copyWith(selectedType: type);
  }

  void setPromoCode(String code) {
    state = state.copyWith(promoCode: code, status: BookingStatus.estimating);
    _calculateFares();
  }

  Future<void> confirmBooking() async {
    if (state.pickup == null || state.dropoff == null || state.selectedType == null) return;

    state = state.copyWith(status: BookingStatus.requesting);

    try {
      final rideData = await _repository.createRideRequest(
        pickup: state.pickup!,
        dropoff: state.dropoff!,
        pickupAddress: state.pickupAddress ?? 'Current Location',
        dropoffAddress: state.dropoffAddress ?? 'Destination',
        vehicleType: state.selectedType!,
        promoCode: state.promoCode,
      );

      final rideId = rideData['id'].toString();
      
      // Emit socket event for real-time broadcasting
      _socketService.requestRide({
        'rideId': rideId,
        'pickupLat': state.pickup!.latitude,
        'pickupLng': state.pickup!.longitude,
        'pickupAddress': state.pickupAddress,
        'dropoffLat': state.dropoff!.latitude,
        'dropoffLng': state.dropoff!.longitude,
        'dropoffAddress': state.dropoffAddress,
        'vehicleType': state.selectedType,
        'estimatedPrice': state.estimations.firstWhere(
          (e) => e['vehicle_type'] == state.selectedType,
          orElse: () => {'total_price': 0},
        )['total_price'],
      });

      // Trigger tracking state machine
      _liveRideNotifier.startTracking();

      state = state.copyWith(
        status: BookingStatus.searching,
        currentRideId: rideId,
      );
    } catch (e) {
      state = state.copyWith(
        status: BookingStatus.error,
        errorMessage: e.toString(),
      );
    }
  }

  Future<void> _calculateFares() async {
    if (state.pickup == null || state.dropoff == null) return;
    
    try {
      final estimations = await _repository.getFareEstimations(
        pickup: state.pickup!,
        dropoff: state.dropoff!,
        promoCode: state.promoCode,
      );
      state = state.copyWith(
        status: BookingStatus.ready,
        estimations: estimations,
      );
    } catch (e) {
      state = state.copyWith(status: BookingStatus.error);
    }
  }

  Future<void> triggerSOS(LatLng currentPosition) async {
    if (state.currentRideId == null) return;

    try {
      await _repository.triggerSOS(
        lat: currentPosition.latitude,
        lng: currentPosition.longitude,
        rideId: state.currentRideId,
      );
      // Optional: Update local state to show a "Help is on the way" alert
    } catch (e) {
      print('SOS Dispatch Failed: $e');
    }
  }

  void reset() {
    state = RideBookingState();
  }
}
