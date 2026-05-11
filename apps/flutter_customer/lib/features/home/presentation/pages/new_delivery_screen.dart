import 'package:flutter/material.dart';
import 'package:flutter/cupertino.dart';
import 'courier_search_screen.dart';
import 'marketplace_screen.dart';

class NewDeliveryScreen extends StatefulWidget {
  const NewDeliveryScreen({super.key});

  @override
  State<NewDeliveryScreen> createState() => _NewDeliveryScreenState();
}

class _NewDeliveryScreenState extends State<NewDeliveryScreen> {
  String? _selectedDestination;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: SafeArea(
        child: Column(
          children: [
            // Top Tabs (Uber | Courier)
            Container(
              padding: const EdgeInsets.only(left: 20, right: 20, top: 12),
              decoration: BoxDecoration(
                color: Colors.white,
                border: Border(bottom: BorderSide(color: Colors.grey.shade200, width: 2)),
              ),
              child: Row(
                children: [
                  Expanded(
                    child: GestureDetector(
                      onTap: () => Navigator.pop(context), // Go back to Home
                      child: Container(
                        padding: const EdgeInsets.symmetric(vertical: 12),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            const Icon(Icons.directions_car, color: Colors.black54),
                            const SizedBox(width: 8),
                            const Text('Ride', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18, color: Colors.black54)),
                          ],
                        ),
                      ),
                    ),
                  ),
                  Expanded(
                    child: Container(
                      padding: const EdgeInsets.symmetric(vertical: 12),
                      decoration: const BoxDecoration(
                        border: Border(bottom: BorderSide(color: Colors.black, width: 3)),
                      ),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Image.network(
                            'https://cdn3d.iconscout.com/3d/premium/thumb/box-4996383-4160472.png',
                            height: 24,
                            errorBuilder: (c, e, s) => const Icon(Icons.inventory_2, color: Colors.black),
                          ),
                          const SizedBox(width: 8),
                          const Text('Courier', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18, color: Colors.black)),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),

            Expanded(
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(20),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Search Bar
                    GestureDetector(
                      onTap: _navCourier,
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                        decoration: BoxDecoration(
                          color: Colors.grey.shade100,
                          borderRadius: BorderRadius.circular(30),
                        ),
                        child: Row(
                          children: [
                            const Icon(Icons.search, color: Colors.black, size: 24),
                            const SizedBox(width: 12),
                            Expanded(
                              child: Text(
                                _selectedDestination ?? 'Deliver to?',
                                style: TextStyle(
                                  color: _selectedDestination != null ? Colors.black : Colors.black87,
                                  fontSize: 18,
                                  fontWeight: _selectedDestination != null ? FontWeight.bold : FontWeight.w600,
                                ),
                                overflow: TextOverflow.ellipsis,
                              ),
                            ),
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
                            const Text('On-demand delivery', style: TextStyle(fontSize: 22, fontWeight: FontWeight.w900, color: Colors.black)),
                            const SizedBox(height: 4),
                            Text('Fast, affordable couriers', style: TextStyle(fontSize: 14, color: Colors.grey.shade600, fontWeight: FontWeight.w500)),
                          ],
                        ),
                        // Bike Image
                        Image.network(
                          'https://cdn3d.iconscout.com/3d/premium/thumb/delivery-bike-4996417-4160506.png',
                          height: 60,
                          errorBuilder: (c, e, s) => const Icon(Icons.motorcycle, size: 40, color: Colors.black),
                        ),
                      ],
                    ),
                    const SizedBox(height: 16),

                    // Send / Receive Card
                    Container(
                      decoration: BoxDecoration(
                        border: Border.all(color: Colors.grey.shade300, width: 1.5),
                        borderRadius: BorderRadius.circular(16),
                      ),
                      child: Column(
                        children: [
                          _buildDeliveryOption(
                            icon: Icons.subdirectory_arrow_right,
                            title: 'Send',
                            onTap: _navCourier,
                          ),
                          Divider(height: 1, color: Colors.grey.shade300, indent: 80),
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
                    const Text('Save yourself a trip', style: TextStyle(fontSize: 20, fontWeight: FontWeight.w900, color: Colors.black)),
                    const SizedBox(height: 16),
                    GestureDetector(
                      onTap: _navCourier,
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 20),
                        decoration: BoxDecoration(
                          border: Border.all(color: Colors.grey.shade300, width: 1.5),
                          borderRadius: BorderRadius.circular(16),
                        ),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                const Text('Store pick-ups', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.black)),
                                const SizedBox(height: 4),
                                Text('Get purchases delivered', style: TextStyle(fontSize: 14, color: Colors.grey.shade600, fontWeight: FontWeight.w500)),
                              ],
                            ),
                            const Icon(Icons.shopping_bag, color: Colors.red, size: 36),
                          ],
                        ),
                      ),
                    ),

                    const SizedBox(height: 32),

                    // Your tasks done fast
                    const Text('Your tasks done fast', style: TextStyle(fontSize: 20, fontWeight: FontWeight.w900, color: Colors.black)),
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
                    Container(
                      padding: const EdgeInsets.all(24),
                      decoration: BoxDecoration(
                        color: const Color(0xFFF0F5FF), // Light blue background
                        borderRadius: BorderRadius.circular(20),
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text('Delivery for personal and business needs', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.black)),
                          const SizedBox(height: 20),
                          _buildCheckItem('Live tracking and PIN verification'),
                          const SizedBox(height: 12),
                          _buildCheckItem('Instant or scheduled pick-ups'),
                          const SizedBox(height: 12),
                          _buildCheckItem('Detailed delivery instructions'),
                          const SizedBox(height: 24),
                          ElevatedButton(
                            onPressed: _navCourier,
                            style: ElevatedButton.styleFrom(
                              backgroundColor: Colors.black,
                              foregroundColor: Colors.white,
                              minimumSize: const Size(double.infinity, 56),
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                            ),
                            child: const Text('Book a courier', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 40),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
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
                color: const Color(0xFFF0F5FF),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Icon(icon, color: Colors.black, size: 28),
            ),
            const SizedBox(width: 16),
            Expanded(child: Text(title, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.black))),
            const Icon(Icons.chevron_right, color: Colors.black54),
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
          color: Colors.grey.shade100,
          borderRadius: BorderRadius.circular(24),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(icon, size: 18, color: Colors.black87),
            const SizedBox(width: 8),
            Text(label, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13, color: Colors.black87)),
          ],
        ),
      ),
    );
  }

  void _navCourier() async {
    final result = await Navigator.of(context).push<String>(CupertinoPageRoute(builder: (_) => const CourierSearchScreen()));
    if (result != null && mounted) {
      setState(() => _selectedDestination = result);
    }
  }

  Widget _buildCheckItem(String text) {
    return Row(
      children: [
        const Icon(Icons.check, color: Colors.black, size: 24),
        const SizedBox(width: 12),
        Expanded(child: Text(text, style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w500, color: Colors.black87))),
      ],
    );
  }
}
