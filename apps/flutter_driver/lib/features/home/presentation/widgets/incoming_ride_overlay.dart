import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/theme/app_colors.dart';
import '../providers/incoming_ride_provider.dart';

class IncomingRideOverlay extends ConsumerWidget {
  const IncomingRideOverlay({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final state = ref.watch(incomingRideProvider);
    if (!state.isShowing) return const SizedBox.shrink();

    final ride = state.rideData!;

    return Scaffold(
      backgroundColor: AppColors.primary.withOpacity(0.98),
      body: SafeArea(
        child: Column(
          children: [
            const SizedBox(height: 40),
            // Header
            Text(
              ride['type'] == 'delivery' ? 'NEW DELIVERY REQUEST' : 'NEW RIDE REQUEST',
              style: const TextStyle(
                color: Colors.white,
                letterSpacing: 4,
                fontWeight: FontWeight.bold,
                fontSize: 14,
              ),
            ),
            const SizedBox(height: 10),
            Text(
              '${state.countdown}s remaining',
              style: TextStyle(color: Colors.white.withOpacity(0.7), fontSize: 16),
            ),
            const Spacer(),
            
            // Ride Details Card
            Container(
              margin: const EdgeInsets.symmetric(horizontal: 24),
              padding: const EdgeInsets.all(24),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(20),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.2),
                    blurRadius: 20,
                    offset: const Offset(0, 10),
                  ),
                ],
              ),
              child: Column(
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        'GH₵${ride['estimatedPrice']}',
                        style: const TextStyle(fontSize: 32, fontWeight: FontWeight.bold, color: AppColors.primary),
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                        decoration: BoxDecoration(color: AppColors.primary, borderRadius: BorderRadius.circular(8)),
                        child: Text(
                          ride['vehicleType']?.toString().toUpperCase() ?? 'ECONOMY',
                          style: const TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold),
                        ),
                      ),
                    ],
                  ),
                  const Divider(height: 32),
                  _buildLocationRow(Icons.radio_button_checked, AppColors.primary, 'PICKUP', ride['pickupAddress']),
                  const SizedBox(height: 16),
                  if (ride['type'] == 'delivery' && ride['stops'] != null) ...[
                    _buildLocationRow(
                      Icons.location_on, 
                      Colors.redAccent, 
                      'FINAL DROPOFF (${(ride['stops'] as List).length} STOPS)', 
                      (ride['stops'] as List).last['address']
                    ),
                    const Divider(height: 32),
                    _buildLocationRow(Icons.inventory_2, Colors.amber.shade700, 'PACKAGE', ride['packageDescription']),
                  ] else ...[
                    _buildLocationRow(Icons.location_on, Colors.redAccent, 'DROPOFF', ride['dropoffAddress']),
                  ],
                ],
              ),
            ),
            
            const Spacer(),

            // Actions
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 40),
              child: Column(
                children: [
                  // Accepting Instruction
                  Text(
                    'Slide to Accept Request', 
                    style: TextStyle(color: Colors.white.withOpacity(0.6), fontWeight: FontWeight.w500)
                  ),
                  const SizedBox(height: 20),
                  
                  // Interactive Accept Area
                  GestureDetector(
                    onHorizontalDragEnd: (details) {
                      if (details.primaryVelocity! > 500) {
                        ref.read(incomingRideProvider.notifier).acceptRide(ref);
                      }
                    },
                    child: Container(
                      width: double.infinity,
                      height: 72,
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(36),
                      ),
                      child: Stack(
                        children: [
                          Positioned(
                            left: 8,
                            top: 8,
                            child: Container(
                              width: 56,
                              height: 56,
                              decoration: const BoxDecoration(
                                color: AppColors.primary,
                                shape: BoxShape.circle,
                              ),
                              child: const Icon(Icons.keyboard_double_arrow_right_rounded, color: Colors.white, size: 28),
                            ),
                          ),
                          const Center(
                            child: Text(
                              'GO TO PICKUP',
                              style: TextStyle(
                                color: AppColors.primary,
                                fontWeight: FontWeight.bold,
                                letterSpacing: 1.2,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                  
                  const SizedBox(height: 24),
                  TextButton(
                    onPressed: () => ref.read(incomingRideProvider.notifier).rejectRide(),
                    child: Text(
                      'IGNORE REQUEST', 
                      style: TextStyle(color: Colors.white.withOpacity(0.5), letterSpacing: 1)
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildLocationRow(IconData icon, Color color, String label, String? address) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Icon(icon, color: color, size: 20),
        const SizedBox(width: 16),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(label, style: const TextStyle(color: AppColors.textMuted, fontSize: 10, fontWeight: FontWeight.bold)),
              Text(
                address ?? 'Unknown Address',
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
                style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 15),
              ),
            ],
          ),
        ),
      ],
    );
  }
}
