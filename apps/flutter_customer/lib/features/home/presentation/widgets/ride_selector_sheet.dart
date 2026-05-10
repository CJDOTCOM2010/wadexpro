import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/providers/localization_provider.dart';
import '../providers/ride_booking_provider.dart';
import '../providers/ride_booking_state.dart';

class RideSelectorSheet extends ConsumerStatefulWidget {
  const RideSelectorSheet({super.key});

  @override
  ConsumerState<RideSelectorSheet> createState() => _RideSelectorSheetState();
}

class _RideSelectorSheetState extends ConsumerState<RideSelectorSheet> {
  final TextEditingController _promoController = TextEditingController();
  bool _isPromoInputVisible = false;

  @override
  void dispose() {
    _promoController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final bookingState = ref.watch(rideBookingProvider);
    final isActive = bookingState.status == BookingStatus.ready;

    return Container(
      padding: const EdgeInsets.symmetric(vertical: 20),
      decoration: const BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
        boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 15, offset: Offset(0, -5))],
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Container(
            width: 50,
            height: 5,
            decoration: BoxDecoration(color: Colors.grey.shade300, borderRadius: BorderRadius.circular(10)),
          ),
          const SizedBox(height: 20),
          if (bookingState.status == BookingStatus.estimating)
            const Padding(
              padding: EdgeInsets.all(40.0),
              child: Column(
                children: [
                  CircularProgressIndicator(color: AppColors.primary),
                  SizedBox(height: 16),
                  Text('Calculating best WADEXPRO fares...', style: TextStyle(color: AppColors.textMuted)),
                ],
              ),
            )
          else if (isActive) ...[
            _buildRideList(bookingState.estimations),
            _buildPromoSection(bookingState.promoCode),
          ] else
            _buildPrompt(context),
          
          if (isActive) ...[
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 8),
              child: SizedBox(
                width: double.infinity,
                height: 54,
                child: ElevatedButton(
                  onPressed: () => ref.read(rideBookingProvider.notifier).confirmBooking(),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppColors.primary,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                    elevation: 0,
                  ),
                  child: const Text(
                    'Confirm WADEXPRO Ride', 
                    style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)
                  ),
                ),
              ),
            ),
          ]
        ],
      ),
    );
  }

  Widget _buildRideList(List<dynamic> estimations) {
    return SizedBox(
      height: 150,
      child: ListView.builder(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 16),
        itemCount: estimations.length,
        itemBuilder: (context, index) {
          final ride = estimations[index];
          final double multiplier = (ride['surge_multiplier'] as num?)?.toDouble() ?? 1.0;
          final bool isSurge = multiplier > 1.0;
          final double discount = (ride['discount_amount'] as num?)?.toDouble() ?? 0.0;
          final bool hasPromo = discount > 0;

          final loc = ref.watch(localizationProvider);

          return Container(
            width: 150,
            margin: const EdgeInsets.symmetric(horizontal: 8, vertical: 8),
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: isSurge ? Colors.amber.shade50 : (hasPromo ? Colors.green.shade50 : Colors.white),
              border: Border.all(
                color: isSurge ? Colors.amber.shade400 : (hasPromo ? Colors.green.shade400 : AppColors.primary.withOpacity(0.1)),
                width: (isSurge || hasPromo) ? 2 : 1,
              ),
              borderRadius: BorderRadius.circular(20),
            ),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                if (isSurge)
                  _buildBadge('HIGH DEMAND', Colors.amber.shade700)
                else if (hasPromo)
                  _buildBadge('SAVINGS APPLIED', Colors.green.shade700),
                
                const SizedBox(height: 4),
                const Icon(Icons.directions_car_filled, size: 36, color: AppColors.primary),
                const SizedBox(height: 8),
                Text(
                  (ride['vehicle_type'] as String).toUpperCase(), 
                  style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 11, letterSpacing: 0.8)
                ),
                if (hasPromo)
                  Text(
                    '${loc.currencySymbol}${ride['base_price']}', 
                    style: const TextStyle(
                      color: Colors.grey, 
                      fontSize: 10, 
                      decoration: TextDecoration.lineThrough
                    )
                  ),
                Text(
                  '${loc.currencySymbol}${ride['estimated_price']}', 
                  style: TextStyle(
                    color: isSurge ? Colors.amber.shade900 : (hasPromo ? Colors.green.shade900 : AppColors.primary),
                    fontWeight: FontWeight.w900,
                    fontSize: 18,
                  )
                ),
              ],
            ),
          );
        },
      ),
    );
  }

  Widget _buildBadge(String text, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
      decoration: BoxDecoration(color: color, borderRadius: BorderRadius.circular(4)),
      child: Text(text, style: const TextStyle(color: Colors.white, fontSize: 8, fontWeight: FontWeight.bold)),
    );
  }

  Widget _buildPromoSection(String? currentCode) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
      child: Column(
        children: [
          if (_isPromoInputVisible)
            Row(
              children: [
                Expanded(
                  child: TextField(
                    controller: _promoController,
                    decoration: InputDecoration(
                      hintText: 'Enter code (e.g. WADEXP10)',
                      hintStyle: const TextStyle(fontSize: 12),
                      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
                      filled: true,
                      fillColor: Colors.grey.shade100,
                    ),
                    textCapitalization: TextCapitalization.characters,
                  ),
                ),
                const SizedBox(width: 12),
                TextButton(
                  onPressed: () {
                    ref.read(rideBookingProvider.notifier).setPromoCode(_promoController.text.trim());
                    setState(() => _isPromoInputVisible = false);
                  },
                  child: const Text('Apply', style: TextStyle(fontWeight: FontWeight.bold)),
                )
              ],
            )
          else
            GestureDetector(
              onTap: () => setState(() => _isPromoInputVisible = true),
              child: Row(
                children: [
                  const Icon(Icons.confirmation_num_outlined, size: 18, color: AppColors.primary),
                  const SizedBox(width: 8),
                  Text(
                    currentCode != null && currentCode.isNotEmpty 
                      ? 'Promo: $currentCode' 
                      : 'Have a promo code?',
                    style: const TextStyle(color: AppColors.primary, fontWeight: FontWeight.bold, fontSize: 13),
                  ),
                ],
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildPrompt(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 20),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(color: AppColors.primary.withOpacity(0.1), shape: BoxShape.circle),
            child: const Icon(Icons.search, color: AppColors.primary),
          ),
          const SizedBox(width: 16),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'Where to?',
                style: Theme.of(context).textTheme.titleLarge?.copyWith(color: AppColors.primary, fontWeight: FontWeight.bold),
              ),
              const Text('Enter destination to see WADEXPRO rates', style: TextStyle(color: AppColors.textMuted, fontSize: 12)),
            ],
          ),
        ],
      ),
    );
  }
}
