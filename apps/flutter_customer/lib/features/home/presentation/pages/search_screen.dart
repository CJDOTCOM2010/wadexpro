import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import '../../../../core/theme/app_colors.dart';
import '../providers/search_provider.dart';
import '../providers/ride_booking_provider.dart';

class SearchScreen extends ConsumerWidget {
  final String? title;
  final bool fromDashboard;
  final void Function(String address, LatLng location)? onSelect;

  const SearchScreen({
    super.key,
    this.title,
    this.fromDashboard = false,
    this.onSelect,
  });

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final suggestions = ref.watch(searchProvider);

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: Text(title ?? 'Plans Your Journey'),
        backgroundColor: AppColors.primary,
        foregroundColor: Colors.white,
      ),
      body: Column(
        children: [
          // Input Section
          Container(
            padding: const EdgeInsets.all(20),
            color: Colors.white,
            child: Column(
              children: [
                _buildSearchInput(
                  context,
                  hint: 'Current Location',
                  icon: Icons.my_location,
                  iconColor: Colors.blue,
                  readOnly: true,
                ),
                const SizedBox(height: 12),
                _buildSearchInput(
                  context,
                  hint: 'Where to?',
                  icon: Icons.location_on,
                  iconColor: Colors.red,
                  autoFocus: true,
                  onChanged: (val) => ref.read(searchProvider.notifier).onQueryChanged(val),
                ),
              ],
            ),
          ),
          
          // Suggestions Section
          Expanded(
            child: suggestions.isEmpty 
              ? _buildEmptyState()
              : ListView.separated(
                  itemCount: suggestions.length,
                  separatorBuilder: (_, _) => const Divider(height: 1),
                  itemBuilder: (context, index) {
                    final place = suggestions[index];
                    return ListTile(
                      leading: Icon(Icons.location_on_outlined, color: AppColors.textMuted),
                      title: Text(place.mainText, style: const TextStyle(fontWeight: FontWeight.w600)),
                      subtitle: Text(place.secondaryText, overflow: TextOverflow.ellipsis),
                      onTap: () async {
                        final latLng = await ref.read(locationRepositoryProvider).getPlaceDetails(place.placeId);
                        if (latLng != null) {
                          if (onSelect != null) {
                            onSelect!(place.mainText, latLng);
                          } else {
                            ref.read(rideBookingProvider.notifier).setDropoff(latLng);
                          }
                          Navigator.pop(context);
                        }
                      },
                    );
                  },
                ),
          ),
        ],
      ),
    );
  }

  Widget _buildSearchInput(
    BuildContext context, {
    required String hint, 
    required IconData icon, 
    required Color iconColor,
    bool readOnly = false,
    bool autoFocus = false,
    Function(String)? onChanged,
  }) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.grey.shade100,
        borderRadius: BorderRadius.circular(12),
      ),
      child: TextField(
        readOnly: readOnly,
        autofocus: autoFocus,
        onChanged: onChanged,
        decoration: InputDecoration(
          hintText: hint,
          prefixIcon: Icon(icon, color: iconColor, size: 20),
          border: InputBorder.none,
          enabledBorder: InputBorder.none,
          focusedBorder: InputBorder.none,
        ),
      ),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.history, size: 64, color: Colors.grey.shade300),
          const SizedBox(height: 16),
          Text('Your recent searches will appear here', style: TextStyle(color: AppColors.textMuted)),
        ],
      ),
    );
  }
}
