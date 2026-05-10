import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../../../core/theme/app_colors.dart';
import '../providers/active_trip_provider.dart';
import '../../../chat/presentation/pages/chat_screen.dart';

class ActiveTripPanel extends ConsumerWidget {
  const ActiveTripPanel({super.key});

  Future<void> _launchNavigation(double lat, double lng) async {
    final url = 'google.navigation:q=$lat,$lng&mode=d';
    if (await canLaunchUrl(Uri.parse(url))) {
      await launchUrl(Uri.parse(url));
    } else {
      final mapsUrl = 'https://www.google.com/maps/search/?api=1&query=$lat,$lng';
      await launchUrl(Uri.parse(mapsUrl));
    }
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final tripState = ref.watch(activeTripProvider);
    if (tripState.status == ActiveTripStatus.none) return const SizedBox.shrink();

    final ride = tripState.rideData;
    if (ride == null) return const SizedBox.shrink();

    final isDelivery = ride['type'] == 'delivery';
    final stops = ride['stops'] as List?;
    final currentStop = (isDelivery && stops != null) ? stops[tripState.currentStopIndex] : null;

    final isPickup = tripState.status == ActiveTripStatus.enRouteToPickup || 
                     tripState.status == ActiveTripStatus.arrived;

    return Container(
      padding: const EdgeInsets.all(24),
      decoration: const BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
        boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 10, spreadRadius: 1, offset: Offset(0, -2))],
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          _buildTripHeader(ride),
          const SizedBox(height: 20),
          if (isDelivery && !isPickup) ...[
            _buildStopIndicator(tripState.currentStopIndex + 1, stops?.length ?? 0),
            const SizedBox(height: 8),
          ],
          _buildAddressSection(
            isPickup 
              ? ride['pickupAddress'] 
              : (isDelivery ? currentStop['address'] : ride['dropoffAddress']), 
            isPickup
          ),
          const SizedBox(height: 24),
          Row(
            children: [
              Expanded(
                flex: 2,
                child: ElevatedButton.icon(
                  onPressed: () {
                    final lat = isPickup 
                      ? ride['pickupLat'] 
                      : (isDelivery ? currentStop['lat'] : ride['dropoffLat']);
                    final lng = isPickup 
                      ? ride['pickupLng'] 
                      : (isDelivery ? currentStop['lng'] : ride['dropoffLng']);
                    _launchNavigation(lat, lng);
                  },
                  icon: const Icon(Icons.navigation),
                  label: const Text('NAVIGATE'),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppColors.primary,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                flex: 1,
                child: IconButton(
                  onPressed: () {
                    Navigator.push(context, MaterialPageRoute(
                      builder: (_) => ChatScreen(
                        rideId: ride['rideId'],
                        otherPartyName: ride['customerName'] ?? 'Customer',
                      ),
                    ));
                  },
                  icon: const Icon(Icons.message),
                  style: IconButton.styleFrom(
                    backgroundColor: AppColors.primaryNavy.withOpacity(0.1),
                    padding: const EdgeInsets.all(16),
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          _buildActionButton(ref, tripState.status),
        ],
      ),
    );
  }

  Widget _buildTripHeader(Map<String, dynamic> ride) {
    final isDelivery = ride['type'] == 'delivery';
    return Row(
      children: [
        CircleAvatar(
          radius: 24, 
          backgroundColor: AppColors.primary, 
          child: Icon(isDelivery ? Icons.inventory_2 : Icons.person, color: Colors.white)
        ),
        const SizedBox(width: 16),
        Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              isDelivery ? 'Delivery Request' : (ride['customerName'] ?? 'Customer'), 
              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 18)
            ),
            if (isDelivery)
              Text(ride['packageDescription'] ?? 'Goods', style: TextStyle(color: Colors.grey.shade600, fontSize: 13))
            else
              const Row(children: [Icon(Icons.star, color: Colors.orange, size: 14), Text(' 4.9')]),
          ],
        ),
        const Spacer(),
        Text(
          'GH₵${ride['estimatedPrice']}', 
          style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: AppColors.primary)
        ),
      ],
    );
  }

  Widget _buildStopIndicator(int current, int total) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.symmetric(vertical: 4),
      alignment: Alignment.centerLeft,
      child: Text(
        'STOP $current OF $total',
        style: const TextStyle(
          color: AppColors.primary, 
          fontWeight: FontWeight.w900, 
          fontSize: 10,
          letterSpacing: 1,
        ),
      ),
    );
  }

  Widget _buildAddressSection(String? address, bool isPickup) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(12)),
      child: Row(
        children: [
          Icon(
            isPickup ? Icons.radio_button_checked : Icons.location_on, 
            color: isPickup ? AppColors.primary : Colors.red
          ),
          const SizedBox(width: 12),
          Expanded(child: Text(address ?? 'Unknown Location', style: const TextStyle(fontWeight: FontWeight.w500))),
        ],
      ),
    );
  }

  Widget _buildActionButton(WidgetRef ref, ActiveTripStatus status) {
    String label;
    Color color;
    String statusUpdate;

    final tripState = ref.read(activeTripProvider);
    final isDelivery = tripState.rideData?['type'] == 'delivery';
    final hasMoreStops = isDelivery && 
        (tripState.rideData?['stops'] as List?) != null &&
        tripState.currentStopIndex < (tripState.rideData?['stops'] as List).length - 1;

    switch (status) {
      case ActiveTripStatus.enRouteToPickup:
        label = 'I HAVE ARRIVED';
        color = AppColors.primary;
        statusUpdate = 'driver_arrived';
        break;
      case ActiveTripStatus.arrived:
        label = 'START TRIP';
        color = Colors.green;
        statusUpdate = 'in_progress';
        break;
      case ActiveTripStatus.enRouteToDropoff:
        if (hasMoreStops) {
          label = 'DELIVERED STOP';
          statusUpdate = 'stop_complete';
        } else {
          label = 'COMPLETE TRIP';
          statusUpdate = 'completed';
        }
        color = AppColors.primary;
        break;
      default:
        return const SizedBox.shrink();
    }

    return SizedBox(
      width: double.infinity,
      height: 56,
      child: ElevatedButton(
        onPressed: () => ref.read(activeTripProvider.notifier).updateStatus(statusUpdate),
        style: ElevatedButton.styleFrom(
          backgroundColor: color,
          foregroundColor: Colors.white,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          elevation: 0,
        ),
        child: Text(label, style: const TextStyle(fontWeight: FontWeight.bold, letterSpacing: 1)),
      ),
    );
  }
}
