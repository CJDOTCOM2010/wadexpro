import 'package:flutter/material.dart';
import '../../../../core/theme/app_colors.dart';
import 'new_delivery_screen.dart';
import 'reserve_screen.dart';
import 'rent_screen.dart';
import 'ride_booking_map_screen.dart';
import 'hourly_screen.dart';

class ServicesCatalogTab extends StatelessWidget {
  const ServicesCatalogTab({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 16.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text('Services', style: TextStyle(fontSize: 32, fontWeight: FontWeight.bold, color: Colors.black)),
              const SizedBox(height: 24),
              const Text('Go anywhere', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.black)),
              const SizedBox(height: 16),
              _buildGoAnywhereGrid(context),
              const SizedBox(height: 32),
              const Divider(thickness: 1, color: Colors.black12),
              const SizedBox(height: 32),
              const Text('Get anything delivered', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.black)),
              const SizedBox(height: 16),
              _buildDeliveryGrid(context),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildGoAnywhereGrid(BuildContext context) {
    return GridView.count(
      physics: const NeverScrollableScrollPhysics(),
      shrinkWrap: true,
      crossAxisCount: 4,
      mainAxisSpacing: 16,
      crossAxisSpacing: 16,
      childAspectRatio: 0.85,
      children: [
        _buildServiceIcon(context, 'Ride', Icons.directions_car, onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const RideBookingMapScreen()))),
        _buildServiceIcon(context, 'Package', Icons.inventory_2, onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const NewDeliveryScreen()))),
        _buildServiceIcon(context, 'Reserve', Icons.calendar_month, onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const ReserveScreen()))),
        _buildServiceIcon(context, 'Hourly', Icons.schedule, onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const HourlyScreen()))),
        _buildServiceIcon(context, 'Rent', Icons.key, onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const RentScreen()))),
        _buildServiceIcon(context, '2-Wheels', Icons.electric_scooter),
        _buildServiceIcon(context, 'Transit', Icons.directions_transit),
        _buildServiceIcon(context, 'Charter', Icons.bus_alert),
        _buildServiceIcon(context, 'Explore', Icons.explore),
        _buildServiceIcon(context, 'Travel', Icons.flight),
      ],
    );
  }

  Widget _buildDeliveryGrid(BuildContext context) {
    return GridView.count(
      physics: const NeverScrollableScrollPhysics(),
      shrinkWrap: true,
      crossAxisCount: 4,
      mainAxisSpacing: 16,
      crossAxisSpacing: 16,
      childAspectRatio: 0.85,
      children: [
        _buildServiceIcon(context, 'Restaurants', Icons.restaurant),
        _buildServiceIcon(context, 'Grocery', Icons.shopping_basket),
        _buildServiceIcon(context, 'Convenience', Icons.local_convenience_store),
        _buildServiceIcon(context, 'Alcohol', Icons.local_bar),
        _buildServiceIcon(context, 'Pharmacy', Icons.local_pharmacy),
        _buildServiceIcon(context, 'Baby', Icons.child_care),
        _buildServiceIcon(context, 'Pet', Icons.pets),
        _buildServiceIcon(context, 'Flowers', Icons.local_florist),
      ],
    );
  }

  Widget _buildServiceIcon(BuildContext context, String title, IconData icon, {VoidCallback? onTap}) {
    return GestureDetector(
      onTap: onTap,
      child: Column(
        children: [
          Expanded(
            child: Container(
              width: double.infinity,
              decoration: BoxDecoration(
                color: Colors.grey.shade100,
                borderRadius: BorderRadius.circular(16),
              ),
              child: Center(
                child: Icon(icon, size: 32, color: AppColors.primary),
              ),
            ),
          ),
          const SizedBox(height: 8),
          Text(title, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 12), textAlign: TextAlign.center),
        ],
      ),
    );
  }
}
