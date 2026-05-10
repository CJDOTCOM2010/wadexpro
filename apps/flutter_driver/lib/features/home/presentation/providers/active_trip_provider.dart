import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/network/providers.dart';
import '../../../wallet/presentation/providers/wallet_provider.dart';
import '../../data/logistics_repository.dart';

enum ActiveTripStatus {
  none,
  enRouteToPickup,
  arrived,
  enRouteToDropoff,
  completed,
}

class ActiveTripState {
  final ActiveTripStatus status;
  final Map<String, dynamic>? rideData;
  final int currentStopIndex;
  final bool isDelivery;

  ActiveTripState({
    this.status = ActiveTripStatus.none,
    this.rideData,
    this.currentStopIndex = 0,
    this.isDelivery = false,
  });

  ActiveTripState copyWith({
    ActiveTripStatus? status,
    Map<String, dynamic>? rideData,
    int? currentStopIndex,
    bool? isDelivery,
  }) {
    return ActiveTripState(
      status: status ?? this.status,
      rideData: rideData ?? this.rideData,
      currentStopIndex: currentStopIndex ?? this.currentStopIndex,
      isDelivery: isDelivery ?? this.isDelivery,
    );
  }
}

final logisticsRepositoryProvider = Provider((ref) {
  final apiClient = ref.watch(apiClientProvider);
  return LogisticsRepository(apiClient);
});

final activeTripProvider = StateNotifierProvider<ActiveTripNotifier, ActiveTripState>((ref) {
  return ActiveTripNotifier(ref);
});

class ActiveTripNotifier extends StateNotifier<ActiveTripState> {
  final Ref _ref;

  ActiveTripNotifier(this._ref) : super(ActiveTripState());

  void setTrip(Map<String, dynamic> rideData) {
    state = state.copyWith(
      status: ActiveTripStatus.enRouteToPickup,
      rideData: rideData,
      currentStopIndex: 0,
      isDelivery: rideData['type'] == 'delivery',
    );
  }

  Future<void> updateStopStatus(String stopId, String status, {String? notes}) async {
    final repo = _ref.read(logisticsRepositoryProvider);
    try {
      await repo.updateStopStatus(stopId, status, notes: notes);
      
      // Update local state if needed
      final stops = List<Map<String, dynamic>>.from(state.rideData?['stops'] ?? []);
      final index = stops.indexWhere((s) => s['id'] == stopId);
      if (index != -1) {
        stops[index]['status'] = status;
        state = state.copyWith(
          rideData: {
            ...state.rideData!,
            'stops': stops,
          }
        );
      }

      // Check if this was the last stop
      if (status == 'delivered' && index == stops.length - 1) {
        await updateStatus('completed');
      }
    } catch (e) {
      print('Failed to update stop status: $e');
    }
  }

  Future<void> updateStatus(String status) async {
    if (state.rideData == null) return;
    
    _ref.read(socketServiceProvider).emit('ride:status_update', {
      'rideId': state.rideData!['rideId'],
      'status': status,
    });

    switch (status) {
      case 'driver_arrived':
        state = state.copyWith(status: ActiveTripStatus.arrived);
        break;
      case 'in_progress':
        state = state.copyWith(status: ActiveTripStatus.enRouteToDropoff);
        break;
      case 'completed':
        state = state.copyWith(status: ActiveTripStatus.completed);
        
        // Finalize Settlement
        _ref.read(walletProvider.notifier).refresh();

        // Reset after a delay
        Future.delayed(const Duration(seconds: 3), () {
          state = ActiveTripState();
        });
        break;
    }
  }

  void reset() {
    state = ActiveTripState();
  }
}
