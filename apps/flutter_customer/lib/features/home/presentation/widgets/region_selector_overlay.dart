import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/providers/localization_provider.dart';

class RegionSelectorOverlay extends ConsumerWidget {
  const RegionSelectorOverlay({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final loc = ref.watch(localizationProvider);

    return Container(
      margin: const EdgeInsets.all(16),
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
      decoration: BoxDecoration(
        color: AppColors.primaryNavy,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.3), blurRadius: 15)],
      ),
      child: Row(
        children: [
          const Icon(Icons.public, color: AppColors.accent, size: 20),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'REGION: ${loc.currencyCode}',
                  style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w900, fontSize: 10, letterSpacing: 1),
                ),
                const Text(
                  'Prices localized tailored for you.',
                  style: TextStyle(color: Colors.white70, fontSize: 11),
                ),
              ],
            ),
          ),
          TextButton(
            onPressed: () => _showRegionPicker(context, ref),
            child: const Text('SWITCH', style: TextStyle(color: AppColors.accent, fontWeight: FontWeight.bold, fontSize: 12)),
          ),
        ],
      ),
    );
  }

  void _showRegionPicker(BuildContext context, WidgetRef ref) {
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      builder: (context) => Container(
        padding: const EdgeInsets.all(32),
        decoration: const BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.vertical(top: Radius.circular(32)),
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text('SELECT MARKET', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 18, letterSpacing: 1)),
            const SizedBox(height: 8),
            const Text('Switching region will update pricing and available services.', style: TextStyle(color: AppColors.textMuted)),
            const SizedBox(height: 24),
            _RegionTile(
              name: 'Ghana (Accra)',
              code: 'GHS',
              symbol: 'GH₵',
              onTap: () {
                ref.read(localizationProvider.notifier).setRegion({'currency_code': 'GHS', 'currency_symbol': 'GH₵'});
                Navigator.pop(context);
              },
            ),
            _RegionTile(
              name: 'Ghana (Accra)',
              code: 'GHS',
              symbol: 'GH₵',
              onTap: () {
                ref.read(localizationProvider.notifier).setRegion({'currency_code': 'GHS', 'currency_symbol': 'GH₵'});
                Navigator.pop(context);
              },
            ),
            _RegionTile(
              name: 'USA / International',
              code: 'USD',
              symbol: '\$',
              onTap: () {
                ref.read(localizationProvider.notifier).setRegion({'currency_code': 'USD', 'currency_symbol': '\$'});
                Navigator.pop(context);
              },
            ),
            const SizedBox(height: 24),
          ],
        ),
      ),
    );
  }
}

class _RegionTile extends StatelessWidget {
  final String name;
  final String code;
  final String symbol;
  final VoidCallback onTap;

  const _RegionTile({required this.name, required this.code, required this.symbol, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return ListTile(
      onTap: onTap,
      contentPadding: EdgeInsets.zero,
      leading: Container(
        padding: const EdgeInsets.all(10),
        decoration: BoxDecoration(color: AppColors.primary.withOpacity(0.05), borderRadius: BorderRadius.circular(12)),
        child: Text(symbol, style: const TextStyle(fontWeight: FontWeight.bold, color: AppColors.primary)),
      ),
      title: Text(name, style: const TextStyle(fontWeight: FontWeight.bold)),
      subtitle: Text(code, style: const TextStyle(fontSize: 12)),
      trailing: const Icon(Icons.chevron_right, size: 20),
    );
  }
}
