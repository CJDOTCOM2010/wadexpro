import 'package:google_maps_flutter/google_maps_flutter.dart';

enum LiveRideStatus {
  idle,
  searching,
  matched,
  enRoute,
  arrived,
  inProgress,
  completed,
  cancelled,
  error,
}

class LiveRideState {
  final LiveRideStatus status;
  final String? rideId;
  final LatLng? driverLocation;
  final double? driverHeading;
  final Map<String, dynamic>? driverData;
  final String? errorMessage;
  final int driversNotified;
  final double surgeMultiplier;

  LiveRideState({
    this.status = LiveRideStatus.idle,
    this.rideId,
    this.driverLocation,
    this.driverHeading,
    this.driverData,
    this.errorMessage,
    this.driversNotified = 0,
    this.surgeMultiplier = 1.0,
  });

  LiveRideState copyWith({
    LiveRideStatus? status,
    String? rideId,
    LatLng? driverLocation,
    double? driverHeading,
    Map<String, dynamic>? driverData,
    String? errorMessage,
    int? driversNotified,
    double? surgeMultiplier,
  }) {
    return LiveRideState(
      status: status ?? this.status,
      rideId: rideId ?? this.rideId,
      driverLocation: driverLocation ?? this.driverLocation,
      driverHeading: driverHeading ?? this.driverHeading,
      driverData: driverData ?? this.driverData,
      errorMessage: errorMessage ?? this.errorMessage,
      driversNotified: driversNotified ?? this.driversNotified,
      surgeMultiplier: surgeMultiplier ?? this.surgeMultiplier,
    );
  }
}
