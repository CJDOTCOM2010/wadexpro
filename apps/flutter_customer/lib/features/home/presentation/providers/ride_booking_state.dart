import 'package:google_maps_flutter/google_maps_flutter.dart';

enum BookingStatus {
  idle,
  pickingUp,
  selectingDropoff,
  estimating,
  ready,
  requesting,
  searching,
  driverAssigned,
  arrived,
  inProgress,
  completed,
  cancelled,
  error,
}

class RideBookingState {
  final BookingStatus status;
  final LatLng? pickup;
  final LatLng? dropoff;
  final String? pickupAddress;
  final String? dropoffAddress;
  final List<dynamic> estimations;
  final String? selectedType;
  final String? promoCode;
  final String? currentRideId;
  final Map<String, dynamic>? driverInfo;
  final String? errorMessage;

  RideBookingState({
    this.status = BookingStatus.idle,
    this.pickup,
    this.dropoff,
    this.pickupAddress,
    this.dropoffAddress,
    this.estimations = const [],
    this.selectedType,
    this.promoCode,
    this.currentRideId,
    this.driverInfo,
    this.errorMessage,
  });

  RideBookingState copyWith({
    BookingStatus? status,
    LatLng? pickup,
    LatLng? dropoff,
    String? pickupAddress,
    String? dropoffAddress,
    List<dynamic>? estimations,
    String? selectedType,
    String? promoCode,
    String? currentRideId,
    Map<String, dynamic>? driverInfo,
    String? errorMessage,
  }) {
    return RideBookingState(
      status: status ?? this.status,
      pickup: pickup ?? this.pickup,
      dropoff: dropoff ?? this.dropoff,
      pickupAddress: pickupAddress ?? this.pickupAddress,
      dropoffAddress: dropoffAddress ?? this.dropoffAddress,
      estimations: estimations ?? this.estimations,
      selectedType: selectedType ?? this.selectedType,
      promoCode: promoCode ?? this.promoCode,
      currentRideId: currentRideId ?? this.currentRideId,
      driverInfo: driverInfo ?? this.driverInfo,
      errorMessage: errorMessage ?? this.errorMessage,
    );
  }
}
