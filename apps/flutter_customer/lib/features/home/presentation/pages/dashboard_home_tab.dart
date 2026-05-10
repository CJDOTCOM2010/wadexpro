import 'package:flutter/material.dart';
import '../../../../core/theme/app_colors.dart';
import 'package:wadexpro_customer/features/home/presentation/pages/new_delivery_screen.dart';
import 'ride_booking_map_screen.dart';
import 'reserve_screen.dart';
import 'rent_screen.dart';
import 'hourly_screen.dart';
import 'search_screen.dart';
import '../widgets/story_slider.dart';

class DashboardHomeTab extends StatelessWidget {
  const DashboardHomeTab({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 8.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _buildTopTabs(context),
              const SizedBox(height: 24),
              _buildSearchBox(context),
              const SizedBox(height: 16),
              _buildSavedPlaces(),
              const SizedBox(height: 32),
              _buildSuggestionsSection(context),
              const SizedBox(height: 32),
              _buildPromoSection(
                context, 
                'Save every day', 
                [
                  _PromoCardData(
                    title: 'Add a stop or 5',
                    subtitle: 'Pick something up along the way',
                    imagePath: 'https://images.unsplash.com/photo-1524613032530-449a5d94c285?q=80&w=2070&auto=format&fit=crop',
                    onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const RideBookingMapScreen())),
                  ),
                  _PromoCardData(
                    title: 'Wadex Moto trips',
                    subtitle: 'Affordable motorcycle rides',
                    imagePath: 'https://images.unsplash.com/photo-1558981403-c5f91cbba527?q=80&w=2070&auto=format&fit=crop',
                    onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const RideBookingMapScreen())),
                  ),
                ],
              ),
              const SizedBox(height: 32),
              _buildPromoSection(
                context, 
                'Deliver with courier', 
                [
                  _PromoCardData(
                    title: 'Send a package',
                    subtitle: 'Same-day delivery to your door',
                    imagePath: 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?q=80&w=2070&auto=format&fit=crop',
                    onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const NewDeliveryScreen())),
                  ),
                  _PromoCardData(
                    title: 'Forgotten something?',
                    subtitle: 'Forgot your wallet? We can help.',
                    imagePath: 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?q=80&w=1974&auto=format&fit=crop',
                    onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const NewDeliveryScreen())),
                  ),
                ],
              ),
              const SizedBox(height: 32),
              _buildPromoSection(
                context, 
                'More ways to use WADEXPRO', 
                [
                  _PromoCardData(
                    title: 'Reserve a ride',
                    subtitle: 'Advance booking for peace of mind',
                    imagePath: 'https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?q=80&w=2070&auto=format&fit=crop',
                    onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const ReserveScreen())),
                  ),
                  _PromoCardData(
                    title: 'Rent a car',
                    subtitle: 'Flexible hourly and daily rentals',
                    imagePath: 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?q=80&w=2070&auto=format&fit=crop',
                    onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const RentScreen())),
                  ),
                ],
              ),
              const SizedBox(height: 48),
              const Divider(color: Colors.black12, thickness: 1, indent: 32, endIndent: 32),
              const SizedBox(height: 32),
              _buildStorySection(context),
              const SizedBox(height: 48),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildTopTabs(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
          decoration: const BoxDecoration(
            border: Border(bottom: BorderSide(color: Colors.black, width: 2)),
          ),
          child: const Row(
            children: [
              Icon(Icons.directions_car, size: 20),
              SizedBox(width: 8),
              Text('Rides', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
            ],
          ),
        ),
        GestureDetector(
          onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const NewDeliveryScreen())),
          child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
            child: const Row(
              children: [
                Icon(Icons.fastfood, size: 20, color: Colors.grey),
                SizedBox(width: 8),
                Text('Delivery', style: TextStyle(fontWeight: FontWeight.w500, fontSize: 16, color: Colors.grey)),
              ],
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildSearchBox(BuildContext context) {
    return GestureDetector(
      onTap: () {
        Navigator.of(context).push(
          MaterialPageRoute(builder: (_) => SearchScreen(fromDashboard: true)),
        );
      },
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.grey.shade100,
          borderRadius: BorderRadius.circular(30),
        ),
        child: Row(
          children: [
            const Icon(Icons.search, size: 28, color: Colors.black),
            const SizedBox(width: 12),
            const Text('Where to?', style: TextStyle(fontSize: 20, fontWeight: FontWeight.w600, color: Colors.black)),
            const Spacer(),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(20),
                boxShadow: const [BoxShadow(color: Colors.black12, blurRadius: 4, offset: Offset(0, 2))],
              ),
              child: const Row(
                children: [
                  Icon(Icons.access_time_filled, size: 16),
                  SizedBox(width: 4),
                  Text('Now', style: TextStyle(fontWeight: FontWeight.bold)),
                  Icon(Icons.keyboard_arrow_down, size: 16),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSavedPlaces() {
    return Column(
      children: [
        ListTile(
          contentPadding: EdgeInsets.zero,
          leading: Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(color: Colors.grey.shade200, shape: BoxShape.circle),
            child: const Icon(Icons.work, color: Colors.black),
          ),
          title: const Text('Work', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
          subtitle: const Text('1455 Market St', style: TextStyle(color: Colors.grey)),
        ),
        ListTile(
          contentPadding: EdgeInsets.zero,
          leading: Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(color: Colors.grey.shade200, shape: BoxShape.circle),
            child: const Icon(Icons.home, color: Colors.black),
          ),
          title: const Text('Home', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
          subtitle: const Text('903 Sunrose Terr', style: TextStyle(color: Colors.grey)),
        ),
      ],
    );
  }

  Widget _buildStorySection(BuildContext context) {
    return StorySlider(
      stories: [
        StoryData(
          title: 'Arrive in Comfort',
          subtitle: 'Experience premium logistics with our luxury fleet.',
          imagePath: 'https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?q=80&w=2070&auto=format&fit=crop',
          ctaText: 'Request now',
          onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const RideBookingMapScreen())),
        ),
        StoryData(
          title: 'Safety First',
          subtitle: 'Our new 24/7 support and real-time tracking is now active.',
          imagePath: 'https://images.unsplash.com/photo-1557683316-973673baf926?q=80&w=2029&auto=format&fit=crop',
          ctaText: 'Learn more',
          onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const RideBookingMapScreen())),
        ),
        StoryData(
          title: 'Go Green',
          subtitle: 'Try our new electric scooters for short urban trips.',
          imagePath: 'https://images.unsplash.com/photo-1593941707882-a5bba14938c7?q=80&w=2072&auto=format&fit=crop',
          ctaText: 'Explore fleet',
          onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const RideBookingMapScreen())),
        ),
      ],
    );
  }

  Widget _buildSuggestionsSection(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            const Text('Suggestions', style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
            TextButton(
              onPressed: () {},
              child: const Text('See all', style: TextStyle(color: Colors.black)),
            ),
          ],
        ),
        const SizedBox(height: 16),
        SingleChildScrollView(
          scrollDirection: Axis.horizontal,
          physics: const BouncingScrollPhysics(),
          child: Row(
            children: [
              _buildSuggestionCard(context, 'Ride', Icons.directions_car, true, 
                onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const RideBookingMapScreen()))),
              const SizedBox(width: 12),
              _buildSuggestionCard(context, 'Package', Icons.inventory_2, false, 
                onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const NewDeliveryScreen()))),
              const SizedBox(width: 12),
              _buildSuggestionCard(context, 'Reserve', Icons.calendar_month, false, 
                onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const ReserveScreen()))),
              const SizedBox(width: 12),
              _buildSuggestionCard(context, 'Rent', Icons.key, false, 
                onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const RentScreen()))),
              const SizedBox(width: 12),
              _buildSuggestionCard(context, 'Hourly', Icons.timer, false, 
                onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const HourlyScreen()))),
              const SizedBox(width: 16),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildSuggestionCard(BuildContext context, String title, IconData icon, bool highlighted, {VoidCallback? onTap}) {
    return GestureDetector(
      onTap: onTap,
      child: Column(
        children: [
          Container(
            width: 75,
            height: 70,
            decoration: BoxDecoration(
              color: highlighted ? AppColors.primary.withOpacity(0.1) : Colors.grey.shade100,
              borderRadius: BorderRadius.circular(16),
            ),
            child: Center(
              child: Icon(icon, size: 36, color: highlighted ? AppColors.primary : Colors.black87),
            ),
          ),
          const SizedBox(height: 8),
          Text(title, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13)),
        ],
      ),
    );
  }

  Widget _buildPromoSection(BuildContext context, String title, List<_PromoCardData> cards) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(title, style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
        const SizedBox(height: 16),
        SingleChildScrollView(
          scrollDirection: Axis.horizontal,
          physics: const BouncingScrollPhysics(),
          child: Row(
            children: cards.map((card) => Padding(
              padding: const EdgeInsets.only(right: 16),
              child: GestureDetector(
                onTap: card.onTap,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Container(
                      width: 280,
                      height: 160,
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(16),
                        image: DecorationImage(
                          image: NetworkImage(card.imagePath),
                          fit: BoxFit.cover,
                        ),
                      ),
                    ),
                    const SizedBox(height: 12),
                    Text(card.title, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                    const SizedBox(height: 4),
                    Text(card.subtitle, style: const TextStyle(color: Colors.grey, fontSize: 14)),
                  ],
                ),
              ),
            )).toList(),
          ),
        ),
      ],
    );
  }
}

class _PromoCardData {
  final String title;
  final String subtitle;
  final String imagePath;
  final VoidCallback onTap;

  _PromoCardData({
    required this.title,
    required this.subtitle,
    required this.imagePath,
    required this.onTap,
  });
}
