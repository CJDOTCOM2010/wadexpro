import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/widgets/dynamic_glass_card.dart';
import '../providers/live_ride_provider.dart';
import 'dart:ui';

class SearchingOverlay extends ConsumerStatefulWidget {
  const SearchingOverlay({super.key});

  @override
  ConsumerState<SearchingOverlay> createState() => _SearchingOverlayState();
}

class _SearchingOverlayState extends ConsumerState<SearchingOverlay> with SingleTickerProviderStateMixin {
  late AnimationController _controller;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 2),
    )..repeat();
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final liveRideState = ref.watch(liveRideProvider);

    return BackdropFilter(
      filter: ImageFilter.blur(sigmaX: 15, sigmaY: 15),
      child: Container(
        color: AppColors.obsidianDark.withOpacity(0.7),
        width: double.infinity,
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
          // Pulsing Radar
          Stack(
            alignment: Alignment.center,
            children: [
              AnimatedBuilder(
                animation: _controller,
                builder: (context, child) {
                  return Container(
                    width: 280 * _controller.value,
                    height: 280 * _controller.value,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      border: Border.all(
                        color: AppColors.primary.withOpacity(1 - _controller.value),
                        width: 2,
                      ),
                    ),
                  );
                },
              ),
              AnimatedBuilder(
                animation: _controller,
                builder: (context, child) {
                  final secondaryPulse = (_controller.value + 0.5) % 1.0;
                  return Container(
                    width: 280 * secondaryPulse,
                    height: 280 * secondaryPulse,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      border: Border.all(
                        color: AppColors.primary.withOpacity(1 - secondaryPulse),
                        width: 1,
                      ),
                    ),
                  );
                },
              ),
              Container(
                width: 120,
                height: 120,
                decoration: BoxDecoration(
                  color: AppColors.primary,
                  shape: BoxShape.circle,
                  boxShadow: [
                    BoxShadow(
                      color: AppColors.primary.withOpacity(0.5),
                      blurRadius: 30,
                      spreadRadius: 10,
                    ),
                  ],
                ),
                child: const Icon(Icons.location_searching_rounded, color: Colors.white, size: 48),
              ),
            ],
          ),
          const SizedBox(height: 60),
          const Text(
            'FINDING YOUR DRIVER',
            style: TextStyle(
              color: Colors.white, 
              fontSize: 22, 
              fontWeight: FontWeight.bold,
              letterSpacing: 2,
            ),
          ),
          const SizedBox(height: 12),
          Text(
            'Notifying nearby WADEXPRO drivers...',
            style: TextStyle(color: Colors.white.withOpacity(0.7), fontSize: 16),
          ),
          if (liveRideState.driversNotified > 0) ...[
            const SizedBox(height: 8),
            Text(
              '${liveRideState.driversNotified} drivers notified',
              style: const TextStyle(color: AppColors.primary, fontWeight: FontWeight.bold),
            ),
          ],
          if (liveRideState.surgeMultiplier > 1.0) ...[
            const SizedBox(height: 20),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
              decoration: BoxDecoration(
                color: Colors.amber.shade700,
                borderRadius: BorderRadius.circular(20),
                boxShadow: [
                  BoxShadow(
                    color: Colors.amber.withOpacity(0.3),
                    blurRadius: 10,
                    spreadRadius: 2,
                  ),
                ],
              ),
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  const Icon(Icons.trending_up, color: Colors.white, size: 16),
                  const SizedBox(width: 8),
                  Text(
                    'HIGH DEMAND: ${liveRideState.surgeMultiplier}x',
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 12,
                      fontWeight: FontWeight.w900,
                      letterSpacing: 1,
                    ),
                  ),
                ],
              ),
            ),
          ],
          const SizedBox(height: 80),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 40),
            child: GestureDetector(
              onTap: () => ref.read(liveRideProvider.notifier).cancelRide(),
              child: DynamicGlassCard(
                padding: const EdgeInsets.symmetric(vertical: 18),
                child: const Center(
                  child: Text(
                    'CANCEL REQUEST',
                    style: TextStyle(letterSpacing: 1.5, fontWeight: FontWeight.bold, color: Colors.white70),
                  ),
                ),
              ),
          ),
            ),
          ],
        ),
      ),
    );
  }
}
