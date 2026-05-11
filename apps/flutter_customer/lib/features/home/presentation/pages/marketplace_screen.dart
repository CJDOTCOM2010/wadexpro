import 'package:flutter/material.dart';
import 'package:flutter_animate/flutter_animate.dart';
import '../../../../core/theme/app_colors.dart';
import 'store_detail_screen.dart';

class MarketplaceScreen extends StatelessWidget {
  final String serviceName;
  final IconData icon;

  const MarketplaceScreen({
    super.key,
    required this.serviceName,
    required this.icon,
  });

  // ── Category theme colors ────────────────────────────────────────────
  Color _accentColor() {
    switch (serviceName) {
      case 'Restaurants': return const Color(0xFFFF6B6B);
      case 'Pharmacy':    return const Color(0xFF00B894);
      case 'Grocery':     return const Color(0xFF00D4AA);
      case 'Convenience': return const Color(0xFF6C63FF);
      case 'Alcohol':     return const Color(0xFFF9CA24);
      case 'Baby':        return const Color(0xFFE17055);
      case 'Pet':         return const Color(0xFF74B9FF);
      case 'Flowers':     return const Color(0xFFFD79A8);
      default:            return const Color(0xFF6C63FF);
    }
  }

  @override
  Widget build(BuildContext context) {
    final categoryData = _getCategorySpecificData(serviceName);
    final accent = _accentColor();

    return Scaffold(
      backgroundColor: AppColors.obsidianDark,
      body: CustomScrollView(
        physics: const BouncingScrollPhysics(),
        slivers: [
          // ── Premium Hero Header ──────────────────────────────────────
          SliverAppBar(
            expandedHeight: 300.0,
            floating: false,
            pinned: true,
            backgroundColor: AppColors.obsidianDark,
            elevation: 0,
            leading: Container(
              margin: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: Colors.black.withOpacity(0.4),
                shape: BoxShape.circle,
              ),
              child: IconButton(
                icon: const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white, size: 18),
                onPressed: () => Navigator.pop(context),
              ),
            ),
            actions: [
              Container(
                margin: const EdgeInsets.only(right: 16),
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: Colors.black.withOpacity(0.4),
                  shape: BoxShape.circle,
                ),
                child: const Icon(Icons.search_rounded, color: Colors.white, size: 20),
              ),
            ],
            flexibleSpace: FlexibleSpaceBar(
              titlePadding: const EdgeInsets.only(left: 20, bottom: 16),
              title: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Icon(icon, color: accent, size: 18),
                  const SizedBox(width: 8),
                  Text(
                    serviceName,
                    style: const TextStyle(
                      color: Colors.white,
                      fontWeight: FontWeight.w900,
                      fontSize: 20,
                      letterSpacing: -0.3,
                    ),
                  ),
                ],
              ),
              background: Stack(
                fit: StackFit.expand,
                children: [
                  Image.network(
                    categoryData['bannerImage'],
                    fit: BoxFit.cover,
                    errorBuilder: (context, error, stackTrace) => Container(
                      decoration: BoxDecoration(
                        gradient: LinearGradient(
                          colors: [accent.withOpacity(0.3), AppColors.obsidianDark],
                          begin: Alignment.topCenter,
                          end: Alignment.bottomCenter,
                        ),
                      ),
                      child: Center(child: Icon(icon, color: Colors.white12, size: 80)),
                    ),
                  ),
                  // Strong bottom gradient for text readability
                  Container(
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        begin: Alignment.topCenter,
                        end: Alignment.bottomCenter,
                        stops: const [0.0, 0.4, 0.75, 1.0],
                        colors: [
                          Colors.black.withOpacity(0.3),
                          Colors.transparent,
                          AppColors.obsidianDark.withOpacity(0.7),
                          AppColors.obsidianDark,
                        ],
                      ),
                    ),
                  ),
                  // Category badge
                  Positioned(
                    bottom: 60,
                    left: 20,
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
                      decoration: BoxDecoration(
                        color: accent.withOpacity(0.2),
                        borderRadius: BorderRadius.circular(30),
                        border: Border.all(color: accent.withOpacity(0.4)),
                      ),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Icon(Icons.verified_rounded, color: accent, size: 14),
                          const SizedBox(width: 6),
                          Text(
                            'WADEX CERTIFIED',
                            style: TextStyle(color: accent, fontSize: 11, fontWeight: FontWeight.w900, letterSpacing: 1.5),
                          ),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),

          // ── Subcategories ────────────────────────────────────────────
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.only(top: 24.0, bottom: 8.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    child: Row(
                      children: [
                        Container(width: 4, height: 18, decoration: BoxDecoration(color: accent, borderRadius: BorderRadius.circular(2))),
                        const SizedBox(width: 10),
                        const Text('CATEGORIES', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w900, color: Colors.white38, letterSpacing: 2)),
                      ],
                    ),
                  ),
                  const SizedBox(height: 16),
                  SizedBox(
                    height: 100,
                    child: ListView(
                      scrollDirection: Axis.horizontal,
                      physics: const BouncingScrollPhysics(),
                      padding: const EdgeInsets.symmetric(horizontal: 20),
                      children: (categoryData['subCategories'] as List<Map<String, dynamic>>).map((sub) {
                        return _buildCategoryItem(sub['name'], sub['icon'], accent);
                      }).toList().animate(interval: 50.ms).slideX(begin: 0.2, duration: 400.ms, curve: Curves.easeOutCubic).fadeIn(duration: 400.ms),
                    ),
                  ),
                ],
              ),
            ),
          ),

          // ── Partner stores header ────────────────────────────────────
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.fromLTRB(20, 16, 20, 16),
              child: Row(
                children: [
                  Container(width: 4, height: 18, decoration: BoxDecoration(color: accent, borderRadius: BorderRadius.circular(2))),
                  const SizedBox(width: 10),
                  const Text('PREMIUM PARTNERS', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w900, color: Colors.white38, letterSpacing: 2)),
                  const Spacer(),
                  Text('${(categoryData['stores'] as List).length} available', style: TextStyle(color: accent.withOpacity(0.7), fontSize: 12, fontWeight: FontWeight.w600)),
                ],
              ),
            ),
          ),

          // ── Stores List ──────────────────────────────────────────────
          SliverPadding(
            padding: const EdgeInsets.symmetric(horizontal: 20),
            sliver: SliverList(
              delegate: SliverChildBuilderDelegate(
                (context, index) {
                  final store = categoryData['stores'][index];
                  return _buildStoreCard(context, store, accent)
                      .animate()
                      .fadeIn(duration: 500.ms, delay: (index * 100).ms)
                      .slideY(begin: 0.1, duration: 500.ms, curve: Curves.easeOutCubic, delay: (index * 100).ms);
                },
                childCount: (categoryData['stores'] as List).length,
              ),
            ),
          ),
          const SliverToBoxAdapter(child: SizedBox(height: 80)),
        ],
      ),
    );
  }

  // ── Full category data with all types ──────────────────────────────────
  Map<String, dynamic> _getCategorySpecificData(String name) {
    switch (name) {
      case 'Restaurants':
        return {
          'bannerImage': 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?q=80&w=2070&auto=format&fit=crop',
          'subCategories': [
            {'name': 'Local', 'icon': Icons.soup_kitchen},
            {'name': 'Pizza', 'icon': Icons.local_pizza},
            {'name': 'Burgers', 'icon': Icons.lunch_dining},
            {'name': 'Asian', 'icon': Icons.ramen_dining},
            {'name': 'Desserts', 'icon': Icons.icecream},
          ],
          'stores': [
            {'name': 'Accra Continental Hub', 'meta': '4.9 ★ • 20 min', 'image': 'https://images.unsplash.com/photo-1514933651103-005eec06c04b?q=80&w=1974&auto=format&fit=crop', 'address': '12 Independence Ave, Accra', 'phone': '+233 24 555 0123'},
            {'name': 'Wadex Kitchen Elite', 'meta': '4.8 ★ • 15 min', 'image': 'https://images.unsplash.com/photo-1552566626-52f8b828add9?q=80&w=2070&auto=format&fit=crop', 'address': 'Oxford St, Osu, Accra', 'phone': '+233 24 555 0456'},
            {'name': 'Golden Tulip Dining', 'meta': '4.7 ★ • 30 min', 'image': 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?q=80&w=2070&auto=format&fit=crop', 'address': 'Liberation Rd, Accra', 'phone': '+233 24 555 0789'},
          ]
        };
      case 'Pharmacy':
        return {
          'bannerImage': 'https://images.unsplash.com/photo-1631549916768-4119b2e5f926?q=80&w=2069&auto=format&fit=crop',
          'subCategories': [
            {'name': 'Meds', 'icon': Icons.medication},
            {'name': 'Vitamins', 'icon': Icons.medical_services},
            {'name': 'First Aid', 'icon': Icons.health_and_safety},
            {'name': 'Beauty', 'icon': Icons.face_retouching_natural},
          ],
          'stores': [
            {'name': 'HealthPlus Ghana', 'meta': 'Verified • 24/7', 'image': 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?q=80&w=2070&auto=format&fit=crop', 'address': 'Spintex Rd, Accra', 'phone': '+233 24 555 9999'},
            {'name': 'MedExpress Pharmacy', 'meta': 'Fast Delivery', 'image': 'https://images.unsplash.com/photo-1585435557343-3b092031a831?q=80&w=2070&auto=format&fit=crop', 'address': 'East Legon, Accra', 'phone': '+233 24 555 0000'},
          ]
        };
      case 'Grocery':
        return {
          'bannerImage': 'https://images.unsplash.com/photo-1542838132-92c53300491e?q=80&w=2070&auto=format&fit=crop',
          'subCategories': [
            {'name': 'Fresh', 'icon': Icons.eco},
            {'name': 'Meat', 'icon': Icons.kebab_dining},
            {'name': 'Dairy', 'icon': Icons.egg},
            {'name': 'Bakery', 'icon': Icons.bakery_dining},
          ],
          'stores': [
            {'name': 'WADEX Fresh Mart', 'meta': 'Daily Fresh', 'image': 'https://images.unsplash.com/photo-1578916171728-46686eac8d58?q=80&w=1974&auto=format&fit=crop', 'address': 'Airport Residential, Accra', 'phone': '+233 24 555 7777'},
            {'name': 'Shoprite Ghana', 'meta': '4.6 ★ • 25 min', 'image': 'https://images.unsplash.com/photo-1604719312566-8912e9227c6a?q=80&w=2074&auto=format&fit=crop', 'address': 'Accra Mall, Tetteh Quarshie', 'phone': '+233 24 555 3333'},
          ]
        };
      case 'Convenience':
        return {
          'bannerImage': 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?q=80&w=2070&auto=format&fit=crop',
          'subCategories': [
            {'name': 'Snacks', 'icon': Icons.cookie},
            {'name': 'Drinks', 'icon': Icons.local_cafe},
            {'name': 'Essentials', 'icon': Icons.shopping_bag},
            {'name': 'Ice Cream', 'icon': Icons.icecream},
          ],
          'stores': [
            {'name': 'WADEX QuickStop', 'meta': 'Open 24/7', 'image': 'https://images.unsplash.com/photo-1604709177225-055f99402ea3?q=80&w=2070&auto=format&fit=crop', 'address': 'Circle, Accra', 'phone': '+233 24 555 2222'},
            {'name': 'Shell Select', 'meta': '4.5 ★ • 10 min', 'image': 'https://images.unsplash.com/photo-1601599561213-832382fd07ba?q=80&w=2064&auto=format&fit=crop', 'address': 'Cantonments, Accra', 'phone': '+233 24 555 4444'},
          ]
        };
      case 'Alcohol':
        return {
          'bannerImage': 'https://images.unsplash.com/photo-1470337458703-46ad1756a187?q=80&w=2069&auto=format&fit=crop',
          'subCategories': [
            {'name': 'Wine', 'icon': Icons.wine_bar},
            {'name': 'Beer', 'icon': Icons.sports_bar},
            {'name': 'Spirits', 'icon': Icons.local_bar},
            {'name': 'Cocktails', 'icon': Icons.nightlife},
          ],
          'stores': [
            {'name': 'Premium Spirits Hub', 'meta': 'Elite Choice', 'image': 'https://images.unsplash.com/photo-1569937756447-1d44f657dc69?q=80&w=1974&auto=format&fit=crop', 'address': 'Labone, Accra', 'phone': '+233 24 555 8888'},
            {'name': 'Wine Cellar GH', 'meta': '4.9 ★ • 20 min', 'image': 'https://images.unsplash.com/photo-1506377247377-2a5b3b417ebb?q=80&w=2070&auto=format&fit=crop', 'address': 'Ridge, Accra', 'phone': '+233 24 555 6666'},
          ]
        };
      case 'Baby':
        return {
          'bannerImage': 'https://images.unsplash.com/photo-1515488042361-ee00e0ddd4e4?q=80&w=2075&auto=format&fit=crop',
          'subCategories': [
            {'name': 'Diapers', 'icon': Icons.baby_changing_station},
            {'name': 'Food', 'icon': Icons.child_care},
            {'name': 'Toys', 'icon': Icons.smart_toy},
            {'name': 'Clothing', 'icon': Icons.checkroom},
          ],
          'stores': [
            {'name': 'Baby World Ghana', 'meta': 'Trusted • 4.8 ★', 'image': 'https://images.unsplash.com/photo-1566004100477-7b3b6a12f7c9?q=80&w=2070&auto=format&fit=crop', 'address': 'East Legon, Accra', 'phone': '+233 24 555 1234'},
            {'name': 'Little Angels Shop', 'meta': 'Free Delivery', 'image': 'https://images.unsplash.com/photo-1596461404969-9ae70f2830c1?q=80&w=2070&auto=format&fit=crop', 'address': 'Cantonments, Accra', 'phone': '+233 24 555 5678'},
          ]
        };
      case 'Pet':
        return {
          'bannerImage': 'https://images.unsplash.com/photo-1450778869180-41d0601e046e?q=80&w=2086&auto=format&fit=crop',
          'subCategories': [
            {'name': 'Dog Food', 'icon': Icons.pets},
            {'name': 'Cat Food', 'icon': Icons.catching_pokemon},
            {'name': 'Toys', 'icon': Icons.smart_toy},
            {'name': 'Grooming', 'icon': Icons.cut},
          ],
          'stores': [
            {'name': 'PetZone Accra', 'meta': '4.7 ★ • 30 min', 'image': 'https://images.unsplash.com/photo-1601758228041-f3b2795255f1?q=80&w=2070&auto=format&fit=crop', 'address': 'Osu, Accra', 'phone': '+233 24 555 9012'},
            {'name': 'Happy Paws GH', 'meta': 'Vet Available', 'image': 'https://images.unsplash.com/photo-1548199973-03cce0bbc87b?q=80&w=2069&auto=format&fit=crop', 'address': 'Airport Hills, Accra', 'phone': '+233 24 555 3456'},
          ]
        };
      case 'Flowers':
        return {
          'bannerImage': 'https://images.unsplash.com/photo-1490750967868-88aa4f44baee?q=80&w=2070&auto=format&fit=crop',
          'subCategories': [
            {'name': 'Roses', 'icon': Icons.local_florist},
            {'name': 'Bouquets', 'icon': Icons.spa},
            {'name': 'Plants', 'icon': Icons.yard},
            {'name': 'Gifts', 'icon': Icons.card_giftcard},
          ],
          'stores': [
            {'name': 'Bloom & Petal', 'meta': 'Same Day • 4.9 ★', 'image': 'https://images.unsplash.com/photo-1487530811176-3780de880c2d?q=80&w=2070&auto=format&fit=crop', 'address': 'Labone, Accra', 'phone': '+233 24 555 7890'},
            {'name': 'WADEX Florals', 'meta': 'Premium Blooms', 'image': 'https://images.unsplash.com/photo-1561181286-d3fee7d55364?q=80&w=2070&auto=format&fit=crop', 'address': 'Cantonments, Accra', 'phone': '+233 24 555 2345'},
          ]
        };
      default: // Explore + any other
        return {
          'bannerImage': 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?q=80&w=2070&auto=format&fit=crop',
          'subCategories': [
            {'name': 'Trending', 'icon': Icons.trending_up},
            {'name': 'New', 'icon': Icons.new_releases},
            {'name': 'Top Rated', 'icon': Icons.star},
          ],
          'stores': [
            {'name': 'WADEX General Store', 'meta': 'All-in-one', 'image': 'https://images.unsplash.com/photo-1534723452862-4c874018d66d?q=80&w=2070&auto=format&fit=crop', 'address': 'East Legon, Accra', 'phone': '+233 24 555 1111'},
          ]
        };
    }
  }

  // ── Subcategory pill ───────────────────────────────────────────────────
  Widget _buildCategoryItem(String name, IconData catIcon, Color accent) {
    return Padding(
      padding: const EdgeInsets.only(right: 16),
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: accent.withOpacity(0.12),
              borderRadius: BorderRadius.circular(20),
              border: Border.all(color: accent.withOpacity(0.25)),
            ),
            child: Icon(catIcon, color: accent, size: 28),
          ),
          const SizedBox(height: 10),
          Text(name, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 13, color: Colors.white70)),
        ],
      ),
    );
  }

  // ── Store card (dark themed, all text visible) ─────────────────────────
  Widget _buildStoreCard(BuildContext context, Map<String, dynamic> store, Color accent) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 20),
      child: GestureDetector(
        onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => StoreDetailScreen(
          name: store['name'],
          meta: store['meta'],
          image: store['image'],
          address: store['address'],
          phone: store['phone'],
        ))),
        child: Container(
          decoration: BoxDecoration(
            color: Colors.white.withOpacity(0.05),
            borderRadius: BorderRadius.circular(24),
            border: Border.all(color: Colors.white.withOpacity(0.08)),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Store image
              ClipRRect(
                borderRadius: const BorderRadius.vertical(top: Radius.circular(24)),
                child: SizedBox(
                  height: 170,
                  width: double.infinity,
                  child: Stack(
                    fit: StackFit.expand,
                    children: [
                      Image.network(
                        store['image'],
                        fit: BoxFit.cover,
                        errorBuilder: (ctx, err, st) => Container(
                          color: accent.withOpacity(0.1),
                          child: Center(child: Icon(Icons.storefront_rounded, color: accent.withOpacity(0.3), size: 48)),
                        ),
                      ),
                      // Subtle overlay for better readability
                      Positioned(
                        bottom: 0,
                        left: 0,
                        right: 0,
                        height: 60,
                        child: Container(
                          decoration: BoxDecoration(
                            gradient: LinearGradient(
                              begin: Alignment.topCenter,
                              end: Alignment.bottomCenter,
                              colors: [Colors.transparent, Colors.black.withOpacity(0.5)],
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
              // Store info
              Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        Expanded(
                          child: Text(
                            store['name'],
                            style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: Colors.white),
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                          decoration: BoxDecoration(
                            color: accent.withOpacity(0.15),
                            borderRadius: BorderRadius.circular(10),
                          ),
                          child: Text(
                            store['meta'],
                            style: TextStyle(color: accent, fontWeight: FontWeight.w700, fontSize: 11),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 8),
                    Row(
                      children: [
                        Icon(Icons.location_on_rounded, color: Colors.white.withOpacity(0.35), size: 14),
                        const SizedBox(width: 4),
                        Expanded(
                          child: Text(
                            store['address'],
                            style: TextStyle(color: Colors.white.withOpacity(0.45), fontSize: 13),
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                        const SizedBox(width: 8),
                        Icon(Icons.arrow_forward_ios_rounded, color: Colors.white.withOpacity(0.2), size: 12),
                      ],
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
