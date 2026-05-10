import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/theme/app_colors.dart';
import '../providers/active_trip_provider.dart';
import '../widgets/sos_button.dart';
import 'package:url_launcher/url_launcher.dart';

class ActiveDeliveryScreen extends ConsumerWidget {
  const ActiveDeliveryScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final tripState = ref.watch(activeTripProvider);
    final order = tripState.rideData;

    if (order == null) return const Scaffold(body: Center(child: CircularProgressIndicator()));

    final stops = List<Map<String, dynamic>>.from(order['stops'] ?? []);

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('DELIVERY MISSION', style: TextStyle(fontWeight: FontWeight.w900, letterSpacing: 1.2, fontSize: 14)),
        centerTitle: true,
        backgroundColor: AppColors.primaryNavy,
        foregroundColor: Colors.white,
        elevation: 0,
        actions: const [SOSButton(), SizedBox(width: 16)],
      ),
      body: Column(
        children: [
          // Order Summary Header
          Container(
            padding: const EdgeInsets.all(24),
            decoration: const BoxDecoration(
              color: AppColors.primaryNavy,
              borderRadius: BorderRadius.vertical(bottom: Radius.circular(32)),
            ),
            child: Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: Colors.white10,
                    borderRadius: BorderRadius.circular(16),
                  ),
                  child: const Icon(Icons.inventory_2, color: AppColors.accent, size: 32),
                ),
                const SizedBox(width: 16),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        order['packageDescription'] ?? 'Courier Package',
                        style: const TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold),
                      ),
                      const SizedBox(height: 4),
                      Text(
                        'Ref: ${order['rideId'].toString().substring(0, 8).toUpperCase()}',
                        style: const TextStyle(color: Colors.white54, fontSize: 12, fontWeight: FontWeight.bold),
                      ),
                    ],
                  ),
                ),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: [
                    const Text('EARNING', style: TextStyle(color: Colors.white54, fontSize: 10, fontWeight: FontWeight.bold)),
                    Text(
                      'GH₵${order['estimatedPrice']}',
                      style: const TextStyle(color: AppColors.accent, fontSize: 20, fontWeight: FontWeight.w900),
                    ),
                  ],
                ),
              ],
            ),
          ),

          // Stops Timeline
          Expanded(
            child: ListView.builder(
              padding: const EdgeInsets.all(24),
              itemCount: stops.length,
              itemBuilder: (context, index) {
                final stop = stops[index];
                final isCompleted = stop['status'] == 'delivered' || stop['status'] == 'picked_up';
                final isCurrent = !isCompleted && (index == 0 || stops[index - 1]['status'] == 'delivered' || stops[index - 1]['status'] == 'picked_up');

                return _buildStopItem(context, ref, stop, index, isCompleted, isCurrent);
              },
            ),
          ),

          // Help / Support Bar
          Padding(
            padding: const EdgeInsets.all(24),
            child: Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(20),
                boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10)],
              ),
              child: Row(
                children: [
                  const Icon(Icons.help_outline, color: AppColors.textMuted),
                  const SizedBox(width: 12),
                  const Expanded(
                    child: Text(
                      'Need help with this delivery?',
                      style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13),
                    ),
                  ),
                  TextButton(
                    onPressed: () {},
                    child: const Text('CONTACT OPS', style: TextStyle(color: AppColors.primary, fontWeight: FontWeight.bold)),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStopItem(BuildContext context, WidgetRef ref, Map<String, dynamic> stop, int index, bool isCompleted, bool isCurrent) {
    return IntrinsicHeight(
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Timeline indicator
          Column(
            children: [
              Container(
                width: 32,
                height: 32,
                decoration: BoxDecoration(
                  color: isCompleted ? Colors.green : (isCurrent ? AppColors.primary : Colors.grey.shade200),
                  shape: BoxShape.circle,
                ),
                child: Center(
                  child: isCompleted 
                    ? const Icon(Icons.check, color: Colors.white, size: 16)
                    : Text('${index + 1}', style: TextStyle(color: isCurrent ? Colors.white : Colors.grey, fontWeight: FontWeight.bold)),
                ),
              ),
              Expanded(
                child: Container(
                  width: 2,
                  color: Colors.grey.shade200,
                ),
              ),
            ],
          ),
          const SizedBox(width: 16),
          // Content
          Expanded(
            child: Opacity(
              opacity: isCompleted ? 0.6 : 1.0,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Text(
                        stop['stop_type']?.toString().toUpperCase() ?? 'DROPOFF',
                        style: TextStyle(
                          color: isCurrent ? AppColors.primary : AppColors.textMuted,
                          fontSize: 10,
                          fontWeight: FontWeight.w900,
                          letterSpacing: 1,
                        ),
                      ),
                      const Spacer(),
                      if (isCurrent)
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                          decoration: BoxDecoration(
                            color: AppColors.primary.withOpacity(0.1),
                            borderRadius: BorderRadius.circular(4),
                          ),
                          child: const Text('CURRENT', style: TextStyle(color: AppColors.primary, fontSize: 8, fontWeight: FontWeight.bold)),
                        ),
                    ],
                  ),
                  const SizedBox(height: 4),
                  Text(
                    stop['address'],
                    style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    'Contact: ${stop['contact_name']}',
                    style: const TextStyle(color: AppColors.textMuted, fontSize: 12),
                  ),
                  const SizedBox(height: 12),
                  if (isCurrent) ...[
                    Row(
                      children: [
                        _ActionButton(
                          icon: Icons.phone,
                          color: Colors.green,
                          onTap: () => launchUrl(Uri.parse('tel:${stop['contact_phone']}')),
                        ),
                        const SizedBox(width: 12),
                        _ActionButton(
                          icon: Icons.chat_bubble_outline,
                          color: AppColors.primary,
                          onTap: () {},
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: ElevatedButton(
                            onPressed: () {
                              HapticFeedback.heavyImpact();
                              ref.read(activeTripProvider.notifier).updateStopStatus(
                                stop['id'].toString(), 
                                index == 0 ? 'picked_up' : 'delivered'
                              );
                            },
                            style: ElevatedButton.styleFrom(
                              backgroundColor: AppColors.primary,
                              foregroundColor: Colors.white,
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                              padding: const EdgeInsets.symmetric(vertical: 12),
                            ),
                            child: Text(
                              index == 0 ? 'CONFIRM PICKUP' : 'MARK DELIVERED',
                              style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 12),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ],
                  const SizedBox(height: 32),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _ActionButton extends StatelessWidget {
  final IconData icon;
  final Color color;
  final VoidCallback onTap;

  const _ActionButton({required this.icon, required this.color, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
          color: color.withOpacity(0.1),
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: color.withOpacity(0.2)),
        ),
        child: Icon(icon, color: color, size: 20),
      ),
    );
  }
}
