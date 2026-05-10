import 'package:flutter/material.dart';
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

  @override
  Widget build(BuildContext context) {
    final categoryData = _getCategorySpecificData(serviceName);

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: Colors.black),
          onPressed: () => Navigator.pop(context),
        ),
        title: Text(
          serviceName,
          style: const TextStyle(color: Colors.black, fontWeight: FontWeight.bold),
        ),
      ),
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Promo Banner
            Container(
              margin: const EdgeInsets.all(16),
              height: 180,
              width: double.infinity,
              decoration: BoxDecoration(
                color: AppColors.primary.withOpacity(0.1),
                borderRadius: BorderRadius.circular(20),
                image: DecorationImage(
                  image: NetworkImage(categoryData['bannerImage']),
                  fit: BoxFit.cover,
                ),
              ),
              child: Container(
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(20),
                  gradient: LinearGradient(
                    begin: Alignment.bottomRight,
                    colors: [Colors.black.withOpacity(0.8), Colors.transparent],
                  ),
                ),
                padding: const EdgeInsets.all(20),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.end,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'WADEX PICK',
                      style: TextStyle(color: AppColors.secondaryGold, fontSize: 24, fontWeight: FontWeight.w900),
                    ),
                    Text(
                      'Top Recommended ${serviceName}',
                      style: const TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold),
                    ),
                  ],
                ),
              ),
            ),
            // Categories
            const Padding(
              padding: EdgeInsets.symmetric(horizontal: 16),
              child: Text('Specialties', style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
            ),
            const SizedBox(height: 16),
            SizedBox(
              height: 100,
              child: ListView(
                scrollDirection: Axis.horizontal,
                padding: const EdgeInsets.symmetric(horizontal: 16),
                children: (categoryData['subCategories'] as List<Map<String, dynamic>>).map((sub) {
                  return _buildCategoryItem(sub['name'], sub['icon']);
                }).toList(),
              ),
            ),
            const SizedBox(height: 32),
            // Recommended Stores
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: Row(
                children: [
                  const Icon(Icons.verified, color: Colors.blue, size: 20),
                  const SizedBox(width: 8),
                  Text('Wadex Recommended $serviceName', style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
                ],
              ),
            ),
            const SizedBox(height: 16),
            ...(categoryData['stores'] as List<Map<String, dynamic>>).map((store) {
              return _buildStoreCard(context, store);
            }).toList(),
            const SizedBox(height: 40),
          ],
        ),
      ),
    );
  }

  Map<String, dynamic> _getCategorySpecificData(String name) {
    switch (name) {
      case 'Restaurants':
        return {
          'bannerImage': 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?q=80&w=2070&auto=format&fit=crop',
          'subCategories': [
            {'name': 'Local', 'icon': Icons.soup_kitchen},
            {'name': 'Pizza', 'icon': Icons.local_pizza},
            {'name': 'Burgers', 'icon': Icons.lunch_dining},
          ],
          'stores': [
            {'name': 'Accra Continental Hub', 'meta': '4.9 • 20 min', 'image': 'https://images.unsplash.com/photo-1514933651103-005eec06c04b?q=80&w=1974&auto=format&fit=crop', 'address': '12 Independence Ave, Accra', 'phone': '+233 24 555 0123'},
            {'name': 'Wadex Kitchen Elite', 'meta': '4.8 • 15 min', 'image': 'https://images.unsplash.com/photo-1552566626-52f8b828add9?q=80&w=2070&auto=format&fit=crop', 'address': 'Oxford St, Osu, Accra', 'phone': '+233 24 555 0456'},
          ]
        };
      case 'Pharmacy':
        return {
          'bannerImage': 'https://images.unsplash.com/photo-1587854692152-cbe660dbbb88?q=80&w=2069&auto=format&fit=crop',
          'subCategories': [
            {'name': 'Meds', 'icon': Icons.medication},
            {'name': 'Care', 'icon': Icons.health_and_safety},
          ],
          'stores': [
            {'name': 'HealthPlus Ghana', 'meta': 'Verified • 24/7', 'image': 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?q=80&w=2070&auto=format&fit=crop', 'address': 'Spintex Rd, Accra', 'phone': '+233 24 555 9999'},
            {'name': 'MedExpress ₵', 'meta': 'Fast Delivery', 'image': 'https://images.unsplash.com/photo-1586015555751-63bb77f4322a?q=80&w=1974&auto=format&fit=crop', 'address': 'East Legon, Accra', 'phone': '+233 24 555 0000'},
          ]
        };
      case 'Grocery':
        return {
          'bannerImage': 'https://images.unsplash.com/photo-1542838132-92c53300491e?q=80&w=2070&auto=format&fit=crop',
          'subCategories': [
            {'name': 'Fresh', 'icon': Icons.eco},
            {'name': 'Meat', 'icon': Icons.kebab_dining},
          ],
          'stores': [
            {'name': 'WADEX Fresh Mart', 'meta': 'Daily Fresh', 'image': 'https://images.unsplash.com/photo-1578916171728-46686eac8d58?q=80&w=1974&auto=format&fit=crop', 'address': 'Airport Residential, Accra', 'phone': '+233 24 555 7777'},
          ]
        };
      case 'Alcohol':
        return {
          'bannerImage': 'https://images.unsplash.com/photo-1470337458703-46ad1756a187?q=80&w=2069&auto=format&fit=crop',
          'subCategories': [
            {'name': 'Wine', 'icon': Icons.wine_bar},
            {'name': 'Beer', 'icon': Icons.sports_bar},
          ],
          'stores': [
            {'name': 'Premium Spirits Hub', 'meta': 'Elite Choice', 'image': 'https://images.unsplash.com/photo-1569937756447-1d44f657dc69?q=80&w=1974&auto=format&fit=crop', 'address': 'Labone, Accra', 'phone': '+233 24 555 8888'},
          ]
        };
      default:
        return {
          'bannerImage': 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?q=80&w=2070&auto=format&fit=crop',
          'subCategories': [
            {'name': 'Wadex Select', 'icon': Icons.auto_awesome},
          ],
          'stores': [
            {'name': 'WADEX General Store', 'meta': 'All-in-one', 'image': 'https://images.unsplash.com/photo-1534723452862-4c874018d66d?q=80&w=2070&auto=format&fit=crop', 'address': 'East Legon, Accra', 'phone': '+233 24 555 1111'},
          ]
        };
    }
  }

  Widget _buildCategoryItem(String name, IconData icon) {
    return Padding(
      padding: const EdgeInsets.only(right: 20),
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Colors.grey.shade100,
              shape: BoxShape.circle,
            ),
            child: Icon(icon, color: AppColors.primary, size: 28),
          ),
          const SizedBox(height: 8),
          Text(name, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13)),
        ],
      ),
    );
  }

  Widget _buildStoreCard(BuildContext context, Map<String, dynamic> store) {
    return GestureDetector(
      onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => StoreDetailScreen(
        name: store['name'],
        meta: store['meta'],
        image: store['image'],
        address: store['address'],
        phone: store['phone'],
      ))),
      child: Container(
        margin: const EdgeInsets.only(bottom: 16, left: 16, right: 16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              height: 160,
              width: double.infinity,
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(16),
                image: DecorationImage(image: NetworkImage(store['image']), fit: BoxFit.cover),
              ),
            ),
            const SizedBox(height: 12),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(store['name'], style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                  decoration: BoxDecoration(color: Colors.green.shade50, borderRadius: BorderRadius.circular(8)),
                  child: Text(store['meta'], style: const TextStyle(color: Colors.green, fontWeight: FontWeight.bold, fontSize: 12)),
                ),
              ],
            ),
            const SizedBox(height: 4),
            Text(store['address'], style: const TextStyle(color: Colors.grey, fontSize: 14)),
          ],
        ),
      ),
    );
  }
}
