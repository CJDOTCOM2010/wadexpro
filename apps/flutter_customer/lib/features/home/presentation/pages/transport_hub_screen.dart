import 'package:flutter/material.dart';
import '../../../../core/theme/app_colors.dart';

class TransportHubScreen extends StatelessWidget {
  final String serviceName;
  final IconData icon;

  const TransportHubScreen({
    super.key,
    required this.serviceName,
    required this.icon,
  });

  @override
  Widget build(BuildContext context) {
    final transportData = _getTransportSpecificData(serviceName);

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
            // Header Image
            Container(
              height: 200,
              width: double.infinity,
              decoration: BoxDecoration(
                image: DecorationImage(
                  image: NetworkImage(transportData['headerImage']),
                  fit: BoxFit.cover,
                ),
              ),
              child: Container(
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.bottomCenter,
                    colors: [Colors.black.withOpacity(0.9), Colors.transparent],
                  ),
                ),
                padding: const EdgeInsets.all(24),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.end,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Elite $serviceName',
                      style: const TextStyle(color: Colors.white, fontSize: 28, fontWeight: FontWeight.bold),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      transportData['tagline'],
                      style: TextStyle(color: Colors.white.withOpacity(0.8), fontSize: 16),
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 24),
            // Terminals / Hubs
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: Row(
                children: [
                  const Icon(Icons.location_on, color: Colors.redAccent, size: 20),
                  const SizedBox(width: 8),
                  Text('WADEX $serviceName Hubs', style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
                ],
              ),
            ),
            const SizedBox(height: 16),
            ...(transportData['hubs'] as List<Map<String, dynamic>>).map((hub) {
              return _buildHubCard(context, hub);
            }).toList(),
            const SizedBox(height: 32),
            // Safety
            Container(
              margin: const EdgeInsets.symmetric(horizontal: 16),
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: AppColors.primary.withOpacity(0.05),
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: AppColors.primary.withOpacity(0.1)),
              ),
              child: Row(
                children: [
                  Icon(Icons.verified_user, color: AppColors.primary, size: 32),
                  const SizedBox(width: 16),
                  const Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('WADEX Safe-Travel', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                        SizedBox(height: 4),
                        Text('All vehicles and hubs are verified and tracked 24/7.', style: TextStyle(color: Colors.grey, fontSize: 14)),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 40),
          ],
        ),
      ),
    );
  }

  Map<String, dynamic> _getTransportSpecificData(String name) {
    switch (name) {
      case 'Rent':
        return {
          'headerImage': 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?q=80&w=2070&auto=format&fit=crop',
          'tagline': 'Premium car rentals across major cities',
          'hubs': [
            {'name': 'Accra Airport Hub', 'address': 'Kotoka Intl Airport, Accra', 'phone': '+233 24 555 1001', 'info': 'Sedans, SUVs, Luxury'},
            {'name': 'Kumasi City Hub', 'address': 'Asokwa, Kumasi', 'phone': '+233 24 555 1002', 'info': 'Economy & Mid-range'},
          ]
        };
      case 'Transit':
        return {
          'headerImage': 'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?q=80&w=2069&auto=format&fit=crop',
          'tagline': 'Inter-city bus and shuttle services',
          'hubs': [
            {'name': 'Wadex-STC Terminal', 'address': 'Lamptey Ave, Accra', 'phone': '+233 24 555 2001', 'info': 'Executive Coaches'},
            {'name': 'VIP Circle Station', 'address': 'Kwame Nkrumah Circle, Accra', 'phone': '+233 24 555 2002', 'info': 'Economy Shuttles'},
          ]
        };
      case 'Charter':
        return {
          'headerImage': 'https://images.unsplash.com/photo-1540962351504-03099e0a754b?q=80&w=1974&auto=format&fit=crop',
          'tagline': 'Private bus and corporate rentals',
          'hubs': [
            {'name': 'Elite Charter Center', 'address': 'Airport Residential, Accra', 'phone': '+233 24 555 3001', 'info': 'Corporate & Events'},
          ]
        };
      default:
        return {
          'headerImage': 'https://images.unsplash.com/photo-1436491865332-7a61a109c0f2?q=80&w=2070&auto=format&fit=crop',
          'tagline': 'Advanced transportation and logistics',
          'hubs': [
            {'name': 'WADEX Logistics Hub', 'address': 'Tema Harbor Area, Tema', 'phone': '+233 24 555 4001', 'info': 'Full Service Logistics'},
          ]
        };
    }
  }

  Widget _buildHubCard(BuildContext context, Map<String, dynamic> hub) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12, left: 16, right: 16),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(12)),
            child: Icon(icon, color: Colors.black87, size: 28),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(hub['name'], style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                const SizedBox(height: 4),
                Text(hub['address'], style: const TextStyle(color: Colors.grey, fontSize: 13)),
                const SizedBox(height: 4),
                Text(hub['info'], style: TextStyle(color: AppColors.primary, fontWeight: FontWeight.bold, fontSize: 12)),
              ],
            ),
          ),
          IconButton(
            icon: Icon(Icons.info_outline, color: AppColors.primary),
            onPressed: () {
              showModalBottomSheet(
                context: context,
                shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
                builder: (_) => Container(
                  padding: const EdgeInsets.all(24),
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(hub['name'], style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold)),
                      const SizedBox(height: 16),
                      _buildDetailRow(Icons.location_on, hub['address']),
                      _buildDetailRow(Icons.phone, hub['phone']),
                      _buildDetailRow(Icons.info, hub['info']),
                      const SizedBox(height: 24),
                      ElevatedButton(
                        onPressed: () => Navigator.pop(context),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: AppColors.primary,
                          minimumSize: const Size(double.infinity, 56),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                        ),
                        child: const Text('Book at this Hub', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                      ),
                    ],
                  ),
                ),
              );
            },
          ),
        ],
      ),
    );
  }

  Widget _buildDetailRow(IconData icon, String text) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Row(
        children: [
          Icon(icon, color: Colors.grey, size: 20),
          const SizedBox(width: 12),
          Text(text, style: const TextStyle(fontSize: 16)),
        ],
      ),
    );
  }
}
