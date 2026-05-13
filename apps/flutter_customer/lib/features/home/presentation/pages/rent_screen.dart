import 'package:flutter/material.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/widgets/wadex_snackbar.dart';
import '../../../../core/network/api_client.dart';
import 'package:dio/dio.dart';

class RentScreen extends StatefulWidget {
  const RentScreen({super.key});

  @override
  State<RentScreen> createState() => _RentScreenState();
}

class _RentScreenState extends State<RentScreen> {
  final _pickupController = TextEditingController(text: 'Accra Central');
  String _dateRange = '12 May - 15 May';
  bool _isLoading = true;
  Map<String, dynamic> _rentalData = {};

  @override
  void initState() {
    super.initState();
    _fetchRentals();
  }

  Future<void> _fetchRentals() async {
    try {
      final response = await ApiClient(Dio()).instance.get('/v1/logistics/rentals');
      setState(() {
        _rentalData = response.data['data'];
        _isLoading = false;
      });
    } catch (e) {
      // Fallback
      setState(() {
        _isLoading = false;
      });
    }
  }

  void _pickDates() async {
    final now = DateTime.now();
    final DateTimeRange? range = await showDateRangePicker(
      context: context,
      firstDate: now,
      lastDate: now.add(const Duration(days: 90)),
      builder: (context, child) => Theme(
        data: ThemeData.dark().copyWith(colorScheme: const ColorScheme.dark(primary: Colors.white, surface: Color(0xFF1A1A2E))),
        child: child!,
      ),
    );
    if (range != null) {
      setState(() {
        _dateRange = '${range.start.day} ${_month(range.start.month)} - ${range.end.day} ${_month(range.end.month)}';
      });
    }
  }

  String _month(int m) {
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    return months[m - 1];
  }

  void _bookVehicle(String name, String price, int id) async {
    // Show loading
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (_) => const Center(child: CircularProgressIndicator(color: Colors.white)),
    );

    try {
      await ApiClient(Dio()).instance.post('/v1/logistics/book', data: {
        'service_type': 'rent',
        'item_id': id,
      });
      if (mounted) Navigator.pop(context); // hide loading

      _showBookingSheet(name, price);
    } catch (e) {
      if (mounted) Navigator.pop(context);
      WadexSnackBar.showError(context, 'Failed to book vehicle');
    }
  }

  void _showBookingSheet(String name, String price) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: AppColors.obsidianDark,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      builder: (ctx) => Padding(
        padding: const EdgeInsets.fromLTRB(24, 20, 24, 40),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: Colors.white24, borderRadius: BorderRadius.circular(2)))),
            const SizedBox(height: 20),
            Text('Book $name', style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold, color: Colors.white)),
            const SizedBox(height: 16),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                  const Text('Pick-up Location', style: TextStyle(color: Colors.white54, fontSize: 12)),
                  Text(_pickupController.text, style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
                ]),
                Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
                  const Text('Dates', style: TextStyle(color: Colors.white54, fontSize: 12)),
                  Text(_dateRange, style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
                ]),
              ],
            ),
            const SizedBox(height: 16),
            const Divider(color: Colors.white12),
            const SizedBox(height: 16),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text('Daily Rate', style: TextStyle(fontSize: 16, color: Colors.white70)),
                Text(price, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.white)),
              ],
            ),
            const SizedBox(height: 8),
            const Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text('Insurance & Fees', style: TextStyle(fontSize: 16, color: Colors.white70)),
                Text('Included', style: TextStyle(fontSize: 16, color: Colors.green, fontWeight: FontWeight.w600)),
              ],
            ),
            const SizedBox(height: 24),
            ElevatedButton(
              onPressed: () {
                Navigator.pop(ctx);
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(
                    content: Text('$name rental confirmed for $_dateRange!'),
                    backgroundColor: Colors.green,
                    behavior: SnackBarBehavior.floating,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                    duration: const Duration(seconds: 4),
                  ),
                );
              },
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.white,
                foregroundColor: Colors.black,
                minimumSize: const Size(double.infinity, 56),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
              ),
              child: const Text('Confirm Rental', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.obsidianDark,
      appBar: AppBar(
        title: const Text('Car Rentals', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: Colors.white),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: Colors.white))
          : Column(
              children: [
                _buildSearchHeader(),
                Expanded(
                  child: ListView(
                    padding: const EdgeInsets.all(20),
                    children: _buildDynamicList(),
                  ),
                ),
              ],
            ),
    );
  }

  List<Widget> _buildDynamicList() {
    if (_rentalData.isEmpty) {
      return [const Center(child: Text('No rentals available', style: TextStyle(color: Colors.white54)))];
    }
    List<Widget> children = [];
    _rentalData.forEach((key, categoryData) {
      children.add(_buildVehicleCategory(categoryData['category'], categoryData['subtitle']));
      for (var vehicle in categoryData['vehicles']) {
        children.add(_buildVehicleCard(
          vehicle['name'],
          vehicle['specs'],
          vehicle['price_display'],
          vehicle['price_raw'],
          vehicle['image'],
          vehicle['id']
        ));
      }
      children.add(const SizedBox(height: 24));
    });
    return children;
  }

  Widget _buildSearchHeader() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: const BoxDecoration(border: Border(bottom: BorderSide(color: Colors.white12))),
      child: Column(
        children: [
          Row(
            children: [
              Expanded(child: _buildEditableField(Icons.location_on, 'Pick-up Location', _pickupController)),
              const SizedBox(width: 12),
              Expanded(child: _buildDateField()),
            ],
          ),
          const SizedBox(height: 16),
          GestureDetector(
            onTap: () => setState(() {}),
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12)),
              child: const Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.search, color: Colors.black, size: 20),
                  SizedBox(width: 8),
                  Text('Update Search', style: TextStyle(color: Colors.black, fontWeight: FontWeight.bold)),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildEditableField(IconData icon, String label, TextEditingController controller) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: const TextStyle(fontSize: 11, color: Colors.white54, fontWeight: FontWeight.bold)),
        const SizedBox(height: 4),
        TextField(
          controller: controller,
          style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: Colors.white),
          decoration: InputDecoration(
            isDense: true,
            contentPadding: EdgeInsets.zero,
            prefixIcon: Icon(icon, size: 14, color: Colors.white54),
            prefixIconConstraints: const BoxConstraints(minWidth: 20),
            border: InputBorder.none,
            enabledBorder: InputBorder.none,
            focusedBorder: InputBorder.none,
          ),
        ),
      ],
    );
  }

  Widget _buildDateField() {
    return GestureDetector(
      onTap: _pickDates,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('Dates', style: TextStyle(fontSize: 11, color: Colors.white54, fontWeight: FontWeight.bold)),
          const SizedBox(height: 4),
          Row(
            children: [
              const Icon(Icons.calendar_today, size: 14, color: Colors.white54),
              const SizedBox(width: 6),
              Expanded(child: Text(_dateRange, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: Colors.white), overflow: TextOverflow.ellipsis)),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildVehicleCategory(String title, String subtitle) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 16, top: 8),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Text(title, style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w900, color: Colors.white)),
            Text(subtitle, style: const TextStyle(fontSize: 12, color: Colors.white54)),
          ]),
          const Icon(Icons.tune, color: Colors.white54),
        ],
      ),
    );
  }

  Widget _buildVehicleCard(String name, String specs, String price, String rawPrice, String imageUrl, int id) {
    return GestureDetector(
      onTap: () => _bookVehicle(name, rawPrice, id),
      child: Container(
        margin: const EdgeInsets.only(bottom: 20),
        decoration: BoxDecoration(
          color: Colors.white.withOpacity(0.08),
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: Colors.white12),
        ),
        child: Column(
          children: [
            ClipRRect(
              borderRadius: const BorderRadius.vertical(top: Radius.circular(20)),
              child: Image.network(
                imageUrl,
                height: 180,
                width: double.infinity,
                fit: BoxFit.cover,
                errorBuilder: (c, e, s) => Container(
                  height: 180,
                  color: Colors.white12,
                  child: const Center(child: Icon(Icons.directions_car, size: 60, color: Colors.white24)),
                ),
              ),
            ),
            Padding(
              padding: const EdgeInsets.all(20),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                    Text(name, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.white)),
                    const SizedBox(height: 4),
                    Text(specs, style: const TextStyle(fontSize: 13, color: Colors.white54)),
                  ]),
                  Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
                    Text(price, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: Colors.white)),
                    const Text('Inc. Tax & Insurance', style: TextStyle(fontSize: 10, color: Colors.white38)),
                  ]),
                ],
              ),
            ),
            Padding(
              padding: const EdgeInsets.fromLTRB(20, 0, 20, 20),
              child: ElevatedButton(
                onPressed: () => _bookVehicle(name, rawPrice, id),
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.white,
                  foregroundColor: Colors.black,
                  minimumSize: const Size(double.infinity, 44),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  elevation: 0,
                ),
                child: Text('Rent $name', style: const TextStyle(fontWeight: FontWeight.bold)),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
