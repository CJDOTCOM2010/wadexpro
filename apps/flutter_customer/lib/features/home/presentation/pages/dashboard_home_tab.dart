import 'dart:io';
import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter/cupertino.dart';
import 'courier_search_screen.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/providers/dashboard_provider.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/widgets/dynamic_glass_card.dart';
import 'ride_booking_map_screen.dart';
import 'reserve_screen.dart';
import 'rent_screen.dart';
import 'hourly_screen.dart';
import 'search_screen.dart';
import '../widgets/story_slider.dart';
import 'marketplace_screen.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import '../providers/ride_booking_provider.dart';
import 'package:wadexpro_customer/features/auth/presentation/providers/auth_provider.dart';
import '../../../../core/providers/app_config_provider.dart';
import '../../../../core/widgets/platform_media_widget.dart';
import '../../../../core/config/brand_config.dart';

class DashboardHomeTab extends ConsumerStatefulWidget {
  const DashboardHomeTab({super.key});

  @override
  ConsumerState<DashboardHomeTab> createState() => _DashboardHomeTabState();
}

class _DashboardHomeTabState extends ConsumerState<DashboardHomeTab> {
  bool _isDelivery = false;
  String? _selectedDestination;
  bool _showLogo = false;
  Timer? _avatarTimer;

  @override
  void initState() {
    super.initState();
    _avatarTimer = Timer.periodic(const Duration(seconds: 4), (_) {
      if (mounted) setState(() => _showLogo = !_showLogo);
    });
  }

  @override
  void dispose() {
    _avatarTimer?.cancel();
    super.dispose();
  }

  Widget _buildAnimatedAvatar() {
    final user = ref.watch(authProvider).user;
    final hasPhoto = user?.avatarUrl != null && user!.avatarUrl!.isNotEmpty;

    // The user's photo widget
    Widget photoWidget = Container(
      key: const ValueKey('user_photo'),
      width: 36,
      height: 36,
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        border: Border.all(color: const Color(0xFF6C63FF).withOpacity(0.5), width: 1.5),
        boxShadow: [
          BoxShadow(color: const Color(0xFF6C63FF).withOpacity(0.2), blurRadius: 8, spreadRadius: 1),
        ],
        image: hasPhoto
          ? DecorationImage(
              image: user!.avatarUrl!.startsWith('http')
                ? NetworkImage(user.avatarUrl!) as ImageProvider
                : FileImage(File(user.avatarUrl!)),
              fit: BoxFit.cover,
            )
          : null,
      ),
      child: !hasPhoto
        ? const Center(child: Icon(Icons.person, size: 18, color: Colors.white70))
        : null,
    );

    // Fetch dynamic logo from Admin Dashboard via provider
    final appConfig = ref.watch(appConfigProvider).value;
    final hasCompanyLogo = appConfig?.logoUrl != null && appConfig!.logoUrl!.isNotEmpty;

    // The company logo widget
    Widget logoWidget = Container(
      key: const ValueKey('company_logo'),
      width: 36,
      height: 36,
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        color: hasCompanyLogo ? Colors.transparent : const Color(0xFF1B3B2B), // Dark Green fallback
        border: Border.all(color: AppColors.secondaryGold.withOpacity(0.5), width: 1.5),
        boxShadow: [
          BoxShadow(color: AppColors.secondaryGold.withOpacity(0.2), blurRadius: 8, spreadRadius: 1),
        ],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(18),
        child: hasCompanyLogo
            ? PlatformMediaWidget(
                url: appConfig.logoUrl!,
                mediaType: appConfig.logoMediaType,
                fit: BoxFit.cover,
                errorWidget: _buildFallbackLogo(),
              )
            : _buildFallbackLogo(),
      ),
    );

    return GestureDetector(
      onTap: () => ref.read(dashboardIndexProvider.notifier).state = 3,
      child: AnimatedSwitcher(
        duration: const Duration(milliseconds: 800),
        switchInCurve: Curves.easeInOut,
        switchOutCurve: Curves.easeInOut,
        transitionBuilder: (child, animation) {
          return FadeTransition(
            opacity: animation,
            child: ScaleTransition(
              scale: Tween<double>(begin: 0.7, end: 1.0).animate(animation),
              child: child,
            ),
          );
        },
        child: _showLogo ? logoWidget : photoWidget,
      ),
    );
  }

  Widget _buildFallbackLogo() {
    return Center(
      child: Row(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.baseline,
        textBaseline: TextBaseline.alphabetic,
        children: [
          const Text('W', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w900, fontSize: 16, letterSpacing: -1.5)),
          Text('X', style: TextStyle(color: AppColors.secondaryGold, fontWeight: FontWeight.w900, fontSize: 16)),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.obsidianDark,
      body: SafeArea(
        child: CustomScrollView(
          physics: const BouncingScrollPhysics(),
          slivers: [
            // Top Tab Switcher
            SliverAppBar(
              pinned: true,
              toolbarHeight: 48,
              backgroundColor: AppColors.obsidianDark,
              elevation: 0,
              title: Row(
                children: [
                  // Rides Tab
                  GestureDetector(
                    onTap: () {
                      setState(() => _isDelivery = false);
                    },
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Row(
                          children: [
                            Image.network('https://cdn3d.iconscout.com/3d/premium/thumb/car-6369046-5259461.png', height: 22, errorBuilder: (c, e, s) => Icon(Icons.directions_car, color: !_isDelivery ? Colors.white : Colors.white54, size: 20)),
                            const SizedBox(width: 8),
                            Text('Rides', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18, color: !_isDelivery ? Colors.white : Colors.white54)),
                          ],
                        ),
                        const SizedBox(height: 4),
                        Container(height: 2, width: 70, decoration: BoxDecoration(color: !_isDelivery ? Colors.white : Colors.transparent, borderRadius: BorderRadius.circular(2))),
                      ],
                    ),
                  ),
                  const SizedBox(width: 24),
                  // Delivery Tab
                  GestureDetector(
                    onTap: () {
                      setState(() => _isDelivery = true);
                    },
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Row(
                          children: [
                            Image.network('https://cdn3d.iconscout.com/3d/premium/thumb/box-4996383-4160472.png', height: 22, errorBuilder: (c, e, s) => Icon(Icons.inventory_2, color: _isDelivery ? Colors.white : Colors.white54, size: 20)),
                            const SizedBox(width: 8),
                            Text('Delivery', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18, color: _isDelivery ? Colors.white : Colors.white54)),
                          ],
                        ),
                        const SizedBox(height: 4),
                        Container(height: 2, width: 85, decoration: BoxDecoration(color: _isDelivery ? Colors.white : Colors.transparent, borderRadius: BorderRadius.circular(2))),
                      ],
                    ),
                  ),
                ],
              ),
              actions: [
                Padding(
                  padding: const EdgeInsets.only(right: 20.0),
                  child: _buildAnimatedAvatar(),
                ),
              ],
            ),

            // Main Content Area
            SliverToBoxAdapter(
              child: Padding(
                padding: const EdgeInsets.all(20.0),
                child: AnimatedSwitcher(
                  duration: const Duration(milliseconds: 300),
                  child: _isDelivery ? _buildDeliveryContent() : _buildRidesContent(),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildRidesContent() {
    return Column(
      key: const ValueKey('rides_content'),
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // Search Bar
        GestureDetector(
          onTap: () {
            Navigator.of(context).push(MaterialPageRoute(builder: (_) => const SearchScreen(fromDashboard: true, isPlanningLater: false)));
          },
          child: DynamicGlassCard(
            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
            child: Row(
              children: [
                const Icon(Icons.search, color: AppColors.primary, size: 24),
                const SizedBox(width: 16),
                const Text('Where to?', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.white)),
                const Spacer(),
                GestureDetector(
                  onTap: () {
                    Navigator.of(context).push(MaterialPageRoute(builder: (_) => const SearchScreen(fromDashboard: true, isPlanningLater: true)));
                  },
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                    decoration: BoxDecoration(color: Colors.white24, borderRadius: BorderRadius.circular(20)),
                    child: const Row(
                      children: [
                        Icon(Icons.schedule, size: 14, color: Colors.white),
                        SizedBox(width: 4),
                        Text('Later', style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
                      ],
                    ),
                  ),
                ),
              ],
            ),
          ),
        ),
        const SizedBox(height: 32),

        // Saved Places
        const Text('Recent & Saved', style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: Colors.white)),
        const SizedBox(height: 16),
        _buildSavedPlaces(),
        const SizedBox(height: 32),

        // Suggestions / Other Services
        const Text('More Services', style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: Colors.white)),
        const SizedBox(height: 16),
        _buildOtherServices(context),
        const SizedBox(height: 32),

        // Promo Channels / Posts
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
              title: '${BrandConfig.shortName} Moto trips',
              subtitle: 'Affordable motorcycle rides',
              imagePath: 'https://images.unsplash.com/photo-1469037464505-842f63625bb3?q=80&w=2070&auto=format&fit=crop',
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
              onTap: () {
                setState(() => _isDelivery = true);
              },
            ),
            _PromoCardData(
              title: 'Forgotten something?',
              subtitle: 'Forgot your wallet? We can help.',
              imagePath: 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?q=80&w=1974&auto=format&fit=crop',
              onTap: () {
                setState(() => _isDelivery = true);
              },
            ),
          ],
        ),
        const SizedBox(height: 32),
        _buildPromoSection(
          context, 
          'More ways to use ${BrandConfig.appName}', 
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
        const SizedBox(height: 32),

        // Story Section
        const Text('Discover', style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: Colors.white)),
        const SizedBox(height: 16),
        _buildStorySection(context),
        
        const SizedBox(height: 120), // Padding for bottom nav bar
      ],
    );
  }

  Widget _buildDeliveryContent() {
    return Column(
      key: const ValueKey('delivery_content'),
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // Search Bar
        GestureDetector(
          onTap: () {
            Navigator.of(context).push(CupertinoPageRoute(builder: (_) => const CourierSearchScreen()));
          },
          child: DynamicGlassCard(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
            child: Row(
              children: [
                const Icon(Icons.search, color: Colors.white, size: 24),
                const SizedBox(width: 12),
                const Text('Deliver to?', style: TextStyle(fontSize: 18, color: Colors.white, fontWeight: FontWeight.w600)),
              ],
            ),
          ),
        ),
        const SizedBox(height: 32),

        // On-demand delivery
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          crossAxisAlignment: CrossAxisAlignment.end,
          children: [
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text('On-demand delivery', style: TextStyle(fontSize: 22, fontWeight: FontWeight.w900, color: Colors.white)),
                const SizedBox(height: 4),
                Text('Fast, affordable couriers', style: TextStyle(fontSize: 14, color: Colors.white54, fontWeight: FontWeight.w500)),
              ],
            ),
            Image.network(
              'https://cdn3d.iconscout.com/3d/premium/thumb/delivery-bike-4996417-4160506.png',
              height: 60,
              errorBuilder: (c, e, s) => const Icon(Icons.motorcycle, size: 40, color: Colors.white),
            ),
          ],
        ),
        const SizedBox(height: 16),

        // Send / Receive Card
        DynamicGlassCard(
          padding: EdgeInsets.zero,
          child: Column(
            children: [
              _buildDeliveryOption(
                icon: Icons.subdirectory_arrow_right,
                title: 'Send',
                onTap: _navCourier,
              ),
              const Divider(height: 1, color: Colors.white12, indent: 80),
              _buildDeliveryOption(
                icon: Icons.subdirectory_arrow_left,
                title: 'Receive',
                onTap: _navCourier,
              ),
            ],
          ),
        ),

        const SizedBox(height: 32),

        // Save yourself a trip
        const Text('Save yourself a trip', style: TextStyle(fontSize: 20, fontWeight: FontWeight.w900, color: Colors.white)),
        const SizedBox(height: 16),
        GestureDetector(
          onTap: () => _navCourier(),
          child: DynamicGlassCard(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 20),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text('Store pick-ups', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.white)),
                    const SizedBox(height: 4),
                    Text('Get purchases delivered', style: TextStyle(fontSize: 14, color: Colors.white54, fontWeight: FontWeight.w500)),
                  ],
                ),
                const Icon(Icons.shopping_bag, color: AppColors.primary, size: 36),
              ],
            ),
          ),
        ),

        const SizedBox(height: 32),

        // Your tasks done fast
        const Text('Your tasks done fast', style: TextStyle(fontSize: 20, fontWeight: FontWeight.w900, color: Colors.white)),
        const SizedBox(height: 16),
        Wrap(
          spacing: 8,
          runSpacing: 12,
          children: [
            _buildTaskChip(Icons.insert_drive_file_outlined, 'Send documents', onTap: () => _navCourier()),
            _buildTaskChip(Icons.help_outline, 'Forgot an item?', onTap: () => _navCourier()),
            _buildTaskChip(Icons.storefront_outlined, 'Marketplace', onTap: () => Navigator.of(context).push(CupertinoPageRoute(builder: (_) => const MarketplaceScreen(serviceName: 'Marketplace', icon: Icons.storefront_outlined)))),
            _buildTaskChip(Icons.store_mall_directory_outlined, 'Pick up vendor supplies', onTap: () => _navCourier()),
            _buildTaskChip(Icons.shopping_bag_outlined, 'Deliver customer orders', onTap: () => _navCourier()),
            _buildTaskChip(Icons.inventory_2_outlined, 'Receive supplies', onTap: () => _navCourier()),
            _buildTaskChip(Icons.card_giftcard, 'Send a gift', onTap: () => _navCourier()),
            _buildTaskChip(Icons.shopping_cart_outlined, 'Pick up online orders', onTap: () => _navCourier()),
          ],
        ),

        const SizedBox(height: 32),

        // Bottom Summary
        DynamicGlassCard(
          padding: const EdgeInsets.all(24),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text('Delivery for personal and business needs', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.white)),
              const SizedBox(height: 20),
              _buildCheckItem('Live tracking and PIN verification'),
              const SizedBox(height: 12),
              _buildCheckItem('Instant or scheduled pick-ups'),
              const SizedBox(height: 12),
              _buildCheckItem('Detailed delivery instructions'),
              const SizedBox(height: 24),
              ElevatedButton(
                onPressed: () => _navCourier(),
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppColors.primary,
                  foregroundColor: Colors.white,
                  minimumSize: const Size(double.infinity, 56),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                ),
                child: const Text('Book a courier', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
              ),
            ],
          ),
        ),
        const SizedBox(height: 120), // Bottom padding
      ],
    );
  }

  Widget _buildDeliveryOption({required IconData icon, required String title, required VoidCallback onTap}) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(16),
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.white12,
                borderRadius: BorderRadius.circular(12),
              ),
              child: Icon(icon, color: Colors.white, size: 28),
            ),
            const SizedBox(width: 16),
            Expanded(child: Text(title, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.white))),
            const Icon(Icons.chevron_right, color: Colors.white54),
          ],
        ),
      ),
    );
  }

  Widget _buildTaskChip(IconData icon, String label, {VoidCallback? onTap}) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
        decoration: BoxDecoration(
          color: Colors.white12,
          borderRadius: BorderRadius.circular(24),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(icon, size: 18, color: Colors.white),
            const SizedBox(width: 8),
            Text(label, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13, color: Colors.white)),
          ],
        ),
      ),
    );
  }

  void _navCourier() async {
    final result = await Navigator.of(context).push<String>(CupertinoPageRoute(builder: (_) => const CourierSearchScreen()));
    if (result != null && mounted) {
      setState(() {
        _selectedDestination = result;
        // Optionally switch to a "Delivery Summary" view or navigate to NewDeliveryScreen
      });
    }
  }

  Widget _buildCheckItem(String text) {
    return Row(
      children: [
        const Icon(Icons.check, color: AppColors.primary, size: 24),
        const SizedBox(width: 12),
        Expanded(child: Text(text, style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w500, color: Colors.white))),
      ],
    );
  }

  Widget _buildSavedPlaces() {
    return Column(
      children: [
        ListTile(
          contentPadding: EdgeInsets.zero,
          leading: Container(padding: const EdgeInsets.all(8), decoration: const BoxDecoration(color: Colors.white10, shape: BoxShape.circle), child: const Icon(Icons.work, color: Colors.white)),
          title: const Text('Work', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: Colors.white)),
          subtitle: const Text('1455 Market St', style: TextStyle(color: Colors.white54)),
          trailing: const Icon(Icons.arrow_forward_ios, size: 14, color: Colors.white54),
          onTap: () {
            ref.read(rideBookingProvider.notifier).setDropoff(
              LatLng(5.6037, -0.1870), // Mock coordinate
              address: '1455 Market St',
            );
            Navigator.of(context).push(MaterialPageRoute(builder: (_) => const RideBookingMapScreen()));
          },
        ),
        const Divider(color: Colors.white10, height: 1),
        ListTile(
          contentPadding: EdgeInsets.zero,
          leading: Container(padding: const EdgeInsets.all(8), decoration: const BoxDecoration(color: Colors.white10, shape: BoxShape.circle), child: const Icon(Icons.home, color: Colors.white)),
          title: const Text('Home', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: Colors.white)),
          subtitle: const Text('903 Sunrose Terr', style: TextStyle(color: Colors.white54)),
          trailing: const Icon(Icons.arrow_forward_ios, size: 14, color: Colors.white54),
          onTap: () {
            ref.read(rideBookingProvider.notifier).setDropoff(
              LatLng(5.6148, -0.2058), // Mock coordinate
              address: '903 Sunrose Terr',
            );
            Navigator.of(context).push(MaterialPageRoute(builder: (_) => const RideBookingMapScreen()));
          },
        ),
      ],
    );
  }

  Widget _buildOtherServices(BuildContext context) {
    return SingleChildScrollView(
      scrollDirection: Axis.horizontal,
      physics: const BouncingScrollPhysics(),
      clipBehavior: Clip.none,
      child: Row(
        children: [
          _buildSuggestionCard(context, 'Reserve', Icons.calendar_month, false, onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const ReserveScreen()))),
          const SizedBox(width: 16),
          _buildSuggestionCard(context, 'Rent', Icons.key, false, onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const RentScreen()))),
          const SizedBox(width: 16),
          _buildSuggestionCard(context, 'Hourly', Icons.timer, false, onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const HourlyScreen()))),
        ],
      ),
    );
  }

  Widget _buildSuggestionCard(BuildContext context, String title, IconData icon, bool highlighted, {VoidCallback? onTap}) {
    return GestureDetector(
      onTap: onTap,
      child: DynamicGlassCard(
        padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 16),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(icon, size: 24, color: highlighted ? AppColors.accent : Colors.white),
            const SizedBox(width: 8),
            Text(title, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14, color: Colors.white)),
          ],
        ),
      ),
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

  Widget _buildPromoSection(BuildContext context, String title, List<_PromoCardData> cards) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(title, style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: Colors.white)),
        const SizedBox(height: 16),
        SingleChildScrollView(
          scrollDirection: Axis.horizontal,
          physics: const BouncingScrollPhysics(),
          clipBehavior: Clip.none,
          child: Row(
            children: cards.map((card) => Padding(
              padding: const EdgeInsets.only(right: 16),
              child: GestureDetector(
                onTap: card.onTap,
                child: DynamicGlassCard(
                  padding: EdgeInsets.zero,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      ClipRRect(
                        borderRadius: const BorderRadius.vertical(top: Radius.circular(24)),
                        child: Image.network(
                          card.imagePath,
                          width: 280,
                          height: 160,
                          fit: BoxFit.cover,
                          errorBuilder: (context, error, stackTrace) {
                            return Container(
                              width: 280,
                              height: 160,
                              color: Colors.white10,
                              child: const Icon(Icons.image_not_supported, color: Colors.white24, size: 40),
                            );
                          },
                          loadingBuilder: (context, child, loadingProgress) {
                            if (loadingProgress == null) return child;
                            return Container(
                              width: 280,
                              height: 160,
                              color: Colors.white10,
                              child: const Center(child: CircularProgressIndicator(strokeWidth: 2, color: AppColors.primary)),
                            );
                          },
                        ),
                      ),
                      Padding(
                        padding: const EdgeInsets.all(16.0),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(card.title, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: Colors.white)),
                            const SizedBox(height: 4),
                            SizedBox(
                              width: 248,
                              child: Text(card.subtitle, style: const TextStyle(color: Colors.white54, fontSize: 13), maxLines: 2),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
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
