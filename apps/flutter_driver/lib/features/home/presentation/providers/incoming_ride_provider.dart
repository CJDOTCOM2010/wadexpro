import 'dart:async';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/network/providers.dart';
import '../../../../core/services/socket_service.dart';
import 'active_trip_provider.dart';
import 'package:wadexpro_driver/features/profile/presentation/providers/profile_provider.dart';

class IncomingRideState {
  final Map<String, dynamic>? rideData;
  final int countdown;
  final bool isShowing;

  IncomingRideState({
    this.rideData,
    this.countdown = 30,
    this.isShowing = false,
  });

  IncomingRideState copyWith({
    Map<String, dynamic>? rideData,
    int? countdown,
    bool? isShowing,
  }) {
    return IncomingRideState(
      rideData: rideData ?? this.rideData,
      countdown: countdown ?? this.countdown,
      isShowing: isShowing ?? this.isShowing,
    );
  }
}

final incomingRideProvider = StateNotifierProvider<IncomingRideNotifier, IncomingRideState>((ref) {
  final socketService = ref.watch(socketServiceProvider);
  return IncomingRideNotifier(socketService);
});

class IncomingRideNotifier extends StateNotifier<IncomingRideState> {
  final SocketService _socketService;
  Timer? _timer;

  IncomingRideNotifier(this._socketService) : super(IncomingRideState()) {
    _initializeListener();
  }

  void _initializeListener() {
    _socketService.on('ride:incoming', (data) {
      if (!state.isShowing) {
        _showNewRequest(data);
      }
    });
  }

  void _showNewRequest(Map<String, dynamic> data) {
    state = state.copyWith(
      rideData: data,
      isShowing: true,
      countdown: 30,
    );

    _timer?.cancel();
    _timer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (state.countdown > 0) {
        state = state.copyWith(countdown: state.countdown - 1);
      } else {
        rejectRide();
      }
    });
  }

  void acceptRide(WidgetRef ref) {
    if (state.rideData != null) {
      final rideData = state.rideData!;
      final profileState = ref.read(profileProvider);
      
      final driverName = profileState.data?['name'] ?? 'Driver';
      final vehicleModel = profileState.data?['vehicle_model'] ?? 'Toyota Corolla';
      final vehiclePlate = profileState.data?['vehicle_plate'] ?? 'ABC-123';
      final driverRating = profileState.data?['rating'] ?? 4.8;
      final driverPhoto = profileState.data?['photo_url'];

      _socketService.emit('ride:accept', {
        'rideId': rideData['rideId'],
        'driverId': profileState.data?['id'],
        'driverName': driverName,
        'vehiclePlate': vehiclePlate,
        'vehicleModel': vehicleModel,
        'driverRating': driverRating,
        'driverPhoto': driverPhoto,
        'estimatedArrival': 5, // 5 minutes (mock for now)
      });
      
      // Hand over to Active Trip Provider
      ref.read(activeTripProvider.notifier).setTrip(rideData);

      _dismiss();
    }
  }

  void rejectRide() {
    if (state.rideData != null) {
      _socketService.emit('ride:reject', {'rideId': state.rideData!['rideId']});
    }
    _dismiss();
  }

  void _dismiss() {
    _timer?.cancel();
    state = IncomingRideState();
  }

  @override
  void dispose() {
    _timer?.cancel();
    super.dispose();
  }
}
