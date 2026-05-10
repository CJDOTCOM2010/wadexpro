import 'package:flutter_riverpod/flutter_riverpod.dart';

class TripModel {
  final String id;
  final String date;
  final String status;
  final String vehicleType;
  final String price;
  final String pickupAddress;
  final String dropoffAddress;

  TripModel({
    required this.id,
    required this.date,
    required this.status,
    required this.vehicleType,
    required this.price,
    required this.pickupAddress,
    required this.dropoffAddress,
  });
}

class ActivityState {
  final List<TripModel> pastTrips;
  final bool isLoading;
  final String? error;

  ActivityState({this.pastTrips = const [], this.isLoading = false, this.error});

  ActivityState copyWith({List<TripModel>? pastTrips, bool? isLoading, String? error}) {
    return ActivityState(
      pastTrips: pastTrips ?? this.pastTrips,
      isLoading: isLoading ?? this.isLoading,
      error: error ?? this.error,
    );
  }
}

final activityProvider = StateNotifierProvider<ActivityNotifier, ActivityState>((ref) {
  return ActivityNotifier();
});

class ActivityNotifier extends StateNotifier<ActivityState> {
  ActivityNotifier() : super(ActivityState()) {
    _loadMockHistory();
  }

  Future<void> _loadMockHistory() async {
    state = state.copyWith(isLoading: true);
    await Future.delayed(const Duration(seconds: 1)); // Simulate network call

    final mockTrips = [
      TripModel(
        id: 'TRP-001',
        date: 'Today, 10:45 AM',
        status: 'Completed',
        vehicleType: 'WADEXPRO Ride',
        price: 'GH₵2,450',
        pickupAddress: 'East Legon, Accra',
        dropoffAddress: 'Accra Mall',
      ),
      TripModel(
        id: 'TRP-002',
        date: 'Yesterday, 6:30 PM',
        status: 'Canceled',
        vehicleType: 'WADEXPRO Ride',
        price: 'GH₵0',
        pickupAddress: 'Osu',
        dropoffAddress: 'Cantonments',
      ),
      TripModel(
        id: 'PKG-003',
        date: 'Oct 15, 2:00 PM',
        status: 'Delivered',
        vehicleType: 'WADEXPRO Package',
        price: 'GH₵1,200',
        pickupAddress: 'Spintex',
        dropoffAddress: 'Tema',
      ),
    ];

    state = state.copyWith(pastTrips: mockTrips, isLoading: false);
  }

  Future<void> refreshHistory() async {
    await _loadMockHistory();
  }
}
