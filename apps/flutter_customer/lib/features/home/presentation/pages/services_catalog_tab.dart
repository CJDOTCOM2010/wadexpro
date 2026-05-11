import 'dart:ui';
import 'package:flutter/material.dart';
import '../../../../core/theme/app_colors.dart';
import 'new_delivery_screen.dart';
import 'reserve_screen.dart';
import 'rent_screen.dart';
import 'ride_booking_map_screen.dart';
import 'hourly_screen.dart';
import 'marketplace_screen.dart';
import 'transport_hub_screen.dart';

class ServicesCatalogTab extends StatefulWidget {
  const ServicesCatalogTab({super.key});

  @override
  State<ServicesCatalogTab> createState() => _ServicesCatalogTabState();
}

class _ServicesCatalogTabState extends State<ServicesCatalogTab> with SingleTickerProviderStateMixin {
  late AnimationController _shimmerCtrl;

  @override
  void initState() {
    super.initState();
    _shimmerCtrl = AnimationController(vsync: this, duration: const Duration(seconds: 3))..repeat();
  }

  @override
  void dispose() {
    _shimmerCtrl.dispose();
    super.dispose();
  }

  // ── Navigation helpers ──────────────────────────────────────────────────
  void _nav(Widget screen) => Navigator.of(context).push(MaterialPageRoute(builder: (_) => screen));
  void _openMarket(String n, IconData i) => _nav(MarketplaceScreen(serviceName: n, icon: i));
  void _openTransport(String n, IconData i) => _nav(TransportHubScreen(serviceName: n, icon: i));

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.obsidianDark,
      body: CustomScrollView(
        physics: const BouncingScrollPhysics(),
        slivers: [
          // ── Hero Header ─────────────────────────────────────────────────
          SliverToBoxAdapter(
            child: SafeArea(
              bottom: false,
              child: Padding(
                padding: const EdgeInsets.fromLTRB(20, 16, 20, 0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text('Explore', style: TextStyle(fontSize: 14, fontWeight: FontWeight.w600, color: Colors.white.withOpacity(0.5), letterSpacing: 2)),
                            const SizedBox(height: 4),
                            const Text('Services', style: TextStyle(fontSize: 34, fontWeight: FontWeight.w900, color: Colors.white, letterSpacing: -1)),
                          ],
                        ),
                        // Animated glow orb
                        AnimatedBuilder(
                          animation: _shimmerCtrl,
                          builder: (_, _) {
                            final val = _shimmerCtrl.value;
                            return Container(
                              width: 48,
                              height: 48,
                              decoration: BoxDecoration(
                                shape: BoxShape.circle,
                                gradient: SweepGradient(
                                  startAngle: val * 6.28,
                                  colors: const [Color(0xFF6C63FF), Color(0xFF00D4AA), Color(0xFFFF6B6B), Color(0xFF6C63FF)],
                                ),
                              ),
                              child: Container(
                                margin: const EdgeInsets.all(2),
                                decoration: BoxDecoration(shape: BoxShape.circle, color: AppColors.obsidianDark),
                                child: const Icon(Icons.widgets_rounded, color: Colors.white, size: 20),
                              ),
                            );
                          },
                        ),
                      ],
                    ),
                    const SizedBox(height: 24),
                    // Search bar
                    ClipRRect(
                      borderRadius: BorderRadius.circular(16),
                      child: BackdropFilter(
                        filter: ImageFilter.blur(sigmaX: 10, sigmaY: 10),
                        child: Container(
                          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                          decoration: BoxDecoration(
                            color: Colors.white.withOpacity(0.08),
                            borderRadius: BorderRadius.circular(16),
                            border: Border.all(color: Colors.white.withOpacity(0.1)),
                          ),
                          child: Row(
                            children: [
                              Icon(Icons.search_rounded, color: Colors.white.withOpacity(0.4), size: 22),
                              const SizedBox(width: 12),
                              Text('Search services…', style: TextStyle(color: Colors.white.withOpacity(0.4), fontSize: 15)),
                            ],
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),

          // ── Featured Rides Carousel ─────────────────────────────────────
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.only(top: 32, bottom: 8),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    child: Row(
                      children: [
                        Container(width: 4, height: 20, decoration: BoxDecoration(color: const Color(0xFF6C63FF), borderRadius: BorderRadius.circular(2))),
                        const SizedBox(width: 10),
                        const Text('GO ANYWHERE', style: TextStyle(fontSize: 13, fontWeight: FontWeight.w800, color: Colors.white54, letterSpacing: 2.5)),
                      ],
                    ),
                  ),
                  const SizedBox(height: 16),
                  SizedBox(
                    height: 185,
                    child: ListView(
                      scrollDirection: Axis.horizontal,
                      physics: const BouncingScrollPhysics(),
                      padding: const EdgeInsets.symmetric(horizontal: 20),
                      children: [
                        _FeaturedCard(
                          title: 'Ride',
                          subtitle: 'Get there fast',
                          icon: Icons.directions_car_rounded,
                          gradient: const [Color(0xFF6C63FF), Color(0xFF4834DF)],
                          onTap: () => _nav(const RideBookingMapScreen()),
                        ),
                        _FeaturedCard(
                          title: 'Reserve',
                          subtitle: 'Book ahead',
                          icon: Icons.calendar_month_rounded,
                          gradient: const [Color(0xFF00D4AA), Color(0xFF00A87D)],
                          onTap: () => _nav(const ReserveScreen()),
                        ),
                        _FeaturedCard(
                          title: 'Hourly',
                          subtitle: 'Your chauffeur',
                          icon: Icons.schedule_rounded,
                          gradient: const [Color(0xFFFF6B6B), Color(0xFFEE5A24)],
                          onTap: () => _nav(const HourlyScreen()),
                        ),
                        _FeaturedCard(
                          title: 'Rent',
                          subtitle: 'Self-drive',
                          icon: Icons.key_rounded,
                          gradient: const [Color(0xFFF9CA24), Color(0xFFE67E22)],
                          onTap: () => _nav(const RentScreen()),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),

          // ── Transport Grid ──────────────────────────────────────────────
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.fromLTRB(20, 24, 20, 0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Container(width: 4, height: 20, decoration: BoxDecoration(color: const Color(0xFF00D4AA), borderRadius: BorderRadius.circular(2))),
                      const SizedBox(width: 10),
                      const Text('TRANSPORT', style: TextStyle(fontSize: 13, fontWeight: FontWeight.w800, color: Colors.white54, letterSpacing: 2.5)),
                    ],
                  ),
                  const SizedBox(height: 16),
                  Row(
                    children: [
                      Expanded(child: _GlassTile(icon: Icons.inventory_2_rounded, label: 'Package', color: const Color(0xFFF9CA24), onTap: () => _nav(const NewDeliveryScreen()))),
                      const SizedBox(width: 12),
                      Expanded(child: _GlassTile(icon: Icons.electric_scooter_rounded, label: '2-Wheels', color: const Color(0xFF00D4AA), onTap: () => _openTransport('2-Wheels', Icons.electric_scooter))),
                      const SizedBox(width: 12),
                      Expanded(child: _GlassTile(icon: Icons.directions_transit_rounded, label: 'Transit', color: const Color(0xFF74B9FF), onTap: () => _openTransport('Transit', Icons.directions_transit))),
                    ],
                  ),
                  const SizedBox(height: 12),
                  Row(
                    children: [
                      Expanded(child: _GlassTile(icon: Icons.bus_alert_rounded, label: 'Charter', color: const Color(0xFFE17055), onTap: () => _openTransport('Charter', Icons.bus_alert))),
                      const SizedBox(width: 12),
                      Expanded(child: _GlassTile(icon: Icons.flight_rounded, label: 'Travel', color: const Color(0xFF6C63FF), onTap: () => _openTransport('Travel', Icons.flight))),
                      const SizedBox(width: 12),
                      Expanded(child: _GlassTile(icon: Icons.explore_rounded, label: 'Explore', color: const Color(0xFFFD79A8), onTap: () => _openMarket('Explore', Icons.explore))),
                    ],
                  ),
                ],
              ),
            ),
          ),

          // ── Delivery Section ────────────────────────────────────────────
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.only(top: 36),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    child: Row(
                      children: [
                        Container(width: 4, height: 20, decoration: BoxDecoration(color: const Color(0xFFFF6B6B), borderRadius: BorderRadius.circular(2))),
                        const SizedBox(width: 10),
                        const Text('GET DELIVERED', style: TextStyle(fontSize: 13, fontWeight: FontWeight.w800, color: Colors.white54, letterSpacing: 2.5)),
                      ],
                    ),
                  ),
                  const SizedBox(height: 16),
                  SizedBox(
                    height: 130,
                    child: ListView(
                      scrollDirection: Axis.horizontal,
                      physics: const BouncingScrollPhysics(),
                      padding: const EdgeInsets.symmetric(horizontal: 20),
                      children: [
                        _DeliveryChip(icon: Icons.restaurant_rounded, label: 'Restaurants', color: const Color(0xFFFF6B6B), onTap: () => _openMarket('Restaurants', Icons.restaurant)),
                        _DeliveryChip(icon: Icons.shopping_basket_rounded, label: 'Grocery', color: const Color(0xFF00D4AA), onTap: () => _openMarket('Grocery', Icons.shopping_basket)),
                        _DeliveryChip(icon: Icons.local_convenience_store_rounded, label: 'Convenience', color: const Color(0xFF6C63FF), onTap: () => _openMarket('Convenience', Icons.local_convenience_store)),
                        _DeliveryChip(icon: Icons.local_bar_rounded, label: 'Alcohol', color: const Color(0xFFF9CA24), onTap: () => _openMarket('Alcohol', Icons.local_bar)),
                        _DeliveryChip(icon: Icons.local_pharmacy_rounded, label: 'Pharmacy', color: const Color(0xFF00B894), onTap: () => _openMarket('Pharmacy', Icons.local_pharmacy)),
                        _DeliveryChip(icon: Icons.child_care_rounded, label: 'Baby', color: const Color(0xFFE17055), onTap: () => _openMarket('Baby', Icons.child_care)),
                        _DeliveryChip(icon: Icons.pets_rounded, label: 'Pet', color: const Color(0xFF74B9FF), onTap: () => _openMarket('Pet', Icons.pets)),
                        _DeliveryChip(icon: Icons.local_florist_rounded, label: 'Flowers', color: const Color(0xFFFD79A8), onTap: () => _openMarket('Flowers', Icons.local_florist)),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),

          // ── Promo Banner ────────────────────────────────────────────────
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.fromLTRB(20, 16, 20, 40),
              child: ClipRRect(
                borderRadius: BorderRadius.circular(24),
                child: BackdropFilter(
                  filter: ImageFilter.blur(sigmaX: 10, sigmaY: 10),
                  child: Container(
                    padding: const EdgeInsets.all(24),
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        colors: [const Color(0xFF6C63FF).withOpacity(0.25), const Color(0xFF00D4AA).withOpacity(0.15)],
                      ),
                      borderRadius: BorderRadius.circular(24),
                      border: Border.all(color: Colors.white.withOpacity(0.12)),
                    ),
                    child: Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.all(14),
                          decoration: BoxDecoration(
                            color: Colors.white.withOpacity(0.15),
                            borderRadius: BorderRadius.circular(16),
                          ),
                          child: const Icon(Icons.local_offer_rounded, color: Colors.white, size: 28),
                        ),
                        const SizedBox(width: 16),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const Text('WADEX Elite Pass', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w800, fontSize: 17)),
                              const SizedBox(height: 4),
                              Text('Save up to 20% on all rides & deliveries', style: TextStyle(color: Colors.white.withOpacity(0.6), fontSize: 13)),
                            ],
                          ),
                        ),
                        Icon(Icons.arrow_forward_ios_rounded, color: Colors.white.withOpacity(0.4), size: 18),
                      ],
                    ),
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

// ── Featured Ride Card ──────────────────────────────────────────────────────
class _FeaturedCard extends StatelessWidget {
  final String title;
  final String subtitle;
  final IconData icon;
  final List<Color> gradient;
  final VoidCallback onTap;

  const _FeaturedCard({required this.title, required this.subtitle, required this.icon, required this.gradient, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        width: 155,
        margin: const EdgeInsets.only(right: 16),
        decoration: BoxDecoration(
          gradient: LinearGradient(begin: Alignment.topLeft, end: Alignment.bottomRight, colors: gradient),
          borderRadius: BorderRadius.circular(24),
          boxShadow: [BoxShadow(color: gradient[0].withOpacity(0.35), blurRadius: 20, offset: const Offset(0, 8))],
        ),
        child: Stack(
          children: [
            // Large faded icon in background
            Positioned(
              right: -15,
              bottom: -15,
              child: Icon(icon, size: 100, color: Colors.white.withOpacity(0.12)),
            ),
            Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    padding: const EdgeInsets.all(10),
                    decoration: BoxDecoration(color: Colors.white.withOpacity(0.2), borderRadius: BorderRadius.circular(14)),
                    child: Icon(icon, color: Colors.white, size: 26),
                  ),
                  const Spacer(),
                  Text(title, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w800, fontSize: 20)),
                  const SizedBox(height: 4),
                  Text(subtitle, style: TextStyle(color: Colors.white.withOpacity(0.75), fontSize: 13, fontWeight: FontWeight.w500)),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

// ── Glass Transport Tile ────────────────────────────────────────────────────
class _GlassTile extends StatelessWidget {
  final IconData icon;
  final String label;
  final Color color;
  final VoidCallback onTap;

  const _GlassTile({required this.icon, required this.label, required this.color, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: ClipRRect(
        borderRadius: BorderRadius.circular(20),
        child: BackdropFilter(
          filter: ImageFilter.blur(sigmaX: 8, sigmaY: 8),
          child: Container(
            height: 100,
            decoration: BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: [color.withOpacity(0.15), color.withOpacity(0.05)],
              ),
              borderRadius: BorderRadius.circular(20),
              border: Border.all(color: color.withOpacity(0.25)),
            ),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: color.withOpacity(0.15),
                    borderRadius: BorderRadius.circular(14),
                  ),
                  child: Icon(icon, color: color, size: 24),
                ),
                const SizedBox(height: 8),
                Text(label, style: const TextStyle(color: Colors.white70, fontWeight: FontWeight.w600, fontSize: 12)),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

// ── Delivery Category Chip ──────────────────────────────────────────────────
class _DeliveryChip extends StatelessWidget {
  final IconData icon;
  final String label;
  final Color color;
  final VoidCallback onTap;

  const _DeliveryChip({required this.icon, required this.label, required this.color, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        width: 95,
        margin: const EdgeInsets.only(right: 14),
        child: Column(
          children: [
            Container(
              width: 72,
              height: 72,
              decoration: BoxDecoration(
                color: color.withOpacity(0.15),
                borderRadius: BorderRadius.circular(22),
                border: Border.all(color: color.withOpacity(0.3)),
              ),
              child: Center(child: Icon(icon, color: color, size: 32)),
            ),
            const SizedBox(height: 10),
            Text(
              label,
              style: const TextStyle(color: Colors.white70, fontWeight: FontWeight.w600, fontSize: 12),
              textAlign: TextAlign.center,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
            ),
          ],
        ),
      ),
    );
  }
}
