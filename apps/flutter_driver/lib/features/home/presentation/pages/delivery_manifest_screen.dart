import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:lucide_icons/lucide_icons.dart';
import '../providers/active_trip_provider.dart';
import '../../../../core/network/providers.dart';

class DeliveryManifestScreen extends ConsumerStatefulWidget {
  final Map<String, dynamic> orderData;

  const DeliveryManifestScreen({
    super.key,
    required this.orderData,
  });

  @override
  ConsumerState<DeliveryManifestScreen> createState() => _DeliveryManifestScreenState();
}

class _DeliveryManifestScreenState extends ConsumerState<DeliveryManifestScreen> {
  bool _isLoading = false;

  Future<void> _updateStop(String stopId, String status) async {
    setState(() => _isLoading = true);
    try {
      await ref.read(rideRepositoryProvider).updateStopStatus(stopId, status);
      // Data will refresh via the order stops event or a manual reload
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Stop status updated: $status')),
      );
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Error: $e'), backgroundColor: Colors.red),
      );
    } finally {
      setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final stops = widget.orderData['stops'] as List<dynamic>? ?? [];

    return Scaffold(
      backgroundColor: const Color(0xFF0F172A),
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        title: const Text(
          'DELIVERY MANIFEST',
          style: TextStyle(
            fontWeight: FontWeight.w900,
            letterSpacing: 1.5,
            fontSize: 16,
          ),
        ),
        centerTitle: true,
      ),
      body: Column(
        children: [
          // Order Header
          Container(
            padding: const EdgeInsets.all(24),
            margin: const EdgeInsets.symmetric(horizontal: 16),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.05),
              borderRadius: BorderRadius.circular(30),
              border: Border.all(color: Colors.white.withOpacity(0.1)),
            ),
            child: Row(
              children: [
                const Icon(LucideIcons.package, color: Color(0xFF3B82F6), size: 32),
                const SizedBox(width: 16),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        widget.orderData['reference']?.toString().toUpperCase() ?? 'WAD-2026-X',
                        style: const TextStyle(
                          color: Colors.white,
                          fontWeight: FontWeight.w900,
                          fontSize: 18,
                        ),
                      ),
                      const Text(
                        'MULTI-STOP LOGISTICS MISSION',
                        style: TextStyle(
                          color: Color(0xFF94A3B8),
                          fontWeight: FontWeight.bold,
                          fontSize: 10,
                          letterSpacing: 1,
                        ),
                      ),
                    ],
                  ),
                ),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                  decoration: BoxDecoration(
                    color: const Color(0xFF3B82F6).withOpacity(0.1),
                    borderRadius: BorderRadius.circular(100),
                  ),
                  child: const Text(
                    'ON ROUTE',
                    style: TextStyle(
                      color: Color(0xFF3B82F6),
                      fontWeight: FontWeight.w900,
                      fontSize: 10,
                    ),
                  ),
                ),
              ],
            ),
          ),

          const SizedBox(height: 32),

          // Steps / Timeline
          Expanded(
            child: ListView.builder(
              padding: const EdgeInsets.symmetric(horizontal: 24),
              itemCount: stops.length,
              itemBuilder: (context, index) {
                final stop = stops[index];
                final isLast = index == stops.length - 1;
                final status = stop['status'] ?? 'pending';
                final isCompleted = status == 'delivered' || status == 'picked_up';

                return IntrinsicHeight(
                  child: Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Timeline Path
                      Column(
                        children: [
                          Container(
                            width: 24,
                            height: 24,
                            decoration: BoxDecoration(
                              shape: BoxShape.circle,
                              color: isCompleted ? const Color(0xFF10B981) : Colors.white.withOpacity(0.1),
                              border: Border.all(
                                color: isCompleted ? Colors.transparent : Colors.white.withOpacity(0.3),
                                width: 2,
                              ),
                            ),
                            child: isCompleted
                                ? const Icon(LucideIcons.check, size: 14, color: Colors.white)
                                : Center(
                                    child: Text(
                                      '${index + 1}',
                                      style: TextStyle(
                                        color: Colors.white.withOpacity(0.5),
                                        fontSize: 10,
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                                  ),
                          ),
                          if (!isLast)
                            Expanded(
                              child: Container(
                                width: 2,
                                color: isCompleted ? const Color(0xFF10B981) : Colors.white.withOpacity(0.1),
                              ),
                            ),
                        ],
                      ),
                      const SizedBox(width: 20),
                      // Stop Info
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              stop['address'] ?? 'Loading Address...',
                              maxLines: 2,
                              overflow: TextOverflow.ellipsis,
                              style: TextStyle(
                                color: isCompleted ? const Color(0xFF94A3B8) : Colors.white,
                                fontWeight: FontWeight.bold,
                                fontSize: 14,
                                decoration: isCompleted ? TextDecoration.lineThrough : null,
                              ),
                            ),
                            const SizedBox(height: 4),
                            Text(
                              'CONTACT: ${stop['contact_name'] ?? 'N/A'} • ${stop['contact_phone'] ?? ''}',
                              style: TextStyle(
                                color: Colors.white.withOpacity(0.4),
                                fontWeight: FontWeight.bold,
                                fontSize: 10,
                              ),
                            ),
                            
                            if (!isCompleted) ...[
                              const SizedBox(height: 16),
                              Row(
                                children: [
                                  _StopActionButton(
                                    label: status == 'arrived' ? 'COMPLETE' : 'ARRIVED',
                                    color: status == 'arrived' ? const Color(0xFF10B981) : const Color(0xFF3B82F6),
                                    onPressed: () => _updateStop(
                                      stop['id'].toString(),
                                      status == 'arrived' ? 'delivered' : 'arrived'
                                    ),
                                  ),
                                  const SizedBox(width: 12),
                                  _StopActionButton(
                                    label: 'ISSUE',
                                    color: Colors.white.withOpacity(0.1),
                                    onPressed: () {},
                                  ),
                                ],
                              ),
                            ],
                            const SizedBox(height: 32),
                          ],
                        ),
                      ),
                    ],
                  ),
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}

class _StopActionButton extends StatelessWidget {
  final String label;
  final Color color;
  final VoidCallback onPressed;

  const _StopActionButton({
    required this.label,
    required this.color,
    required this.onPressed,
  });

  @override
  Widget build(BuildContext context) {
    return Material(
      color: color,
      borderRadius: BorderRadius.circular(12),
      child: InkWell(
        onPressed: onPressed,
        borderRadius: BorderRadius.circular(12),
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
          child: Text(
            label,
            style: const TextStyle(
              color: Colors.white,
              fontWeight: FontWeight.w900,
              fontSize: 10,
              letterSpacing: 1,
            ),
          ),
        ),
      ),
    );
  }
}
