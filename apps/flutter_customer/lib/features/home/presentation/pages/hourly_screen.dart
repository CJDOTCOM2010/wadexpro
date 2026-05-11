import 'package:flutter/material.dart';
import '../../../../core/theme/app_colors.dart';

class HourlyScreen extends StatefulWidget {
  const HourlyScreen({super.key});

  @override
  State<HourlyScreen> createState() => _HourlyScreenState();
}

class _HourlyScreenState extends State<HourlyScreen> {
  int _selectedHours = 4;
  bool _isBooking = false;

  final List<int> _hourOptions = [1, 2, 3, 4, 6, 8];

  void _bookHourly() async {
    setState(() => _isBooking = true);
    await Future.delayed(const Duration(seconds: 2));
    if (mounted) {
      setState(() => _isBooking = false);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('$_selectedHours-Hour Chauffeur Booking Confirmed! A driver will be assigned shortly.'),
          backgroundColor: Colors.green,
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          duration: const Duration(seconds: 4),
        ),
      );
      Navigator.pop(context);
    }
  }

  @override
  Widget build(BuildContext context) {
    final estimatedCost = (_selectedHours * 45.0).toStringAsFixed(2);

    return Scaffold(
      backgroundColor: AppColors.obsidianDark,
      appBar: AppBar(
        title: const Text('Hourly Booking', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: Colors.white),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: Padding(
        padding: const EdgeInsets.all(24.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text('Keep a driver for hours', style: TextStyle(fontSize: 28, fontWeight: FontWeight.w900, color: Colors.white)),
            const SizedBox(height: 8),
            const Text('Perfect for errands, meetings, or sightseeing.', style: TextStyle(fontSize: 16, color: Colors.white54)),
            const SizedBox(height: 40),

            const Text('SELECT DURATION', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12, color: Colors.white54, letterSpacing: 1.2)),
            const SizedBox(height: 16),
            _buildHourSelector(),

            const SizedBox(height: 32),

            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: Colors.white.withOpacity(0.08),
                borderRadius: BorderRadius.circular(20),
                border: Border.all(color: Colors.white12),
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text('Estimated Cost', style: TextStyle(color: Colors.white54, fontSize: 13)),
                      const SizedBox(height: 4),
                      Text('GHS $estimatedCost', style: const TextStyle(fontSize: 24, fontWeight: FontWeight.w900, color: Colors.white)),
                    ],
                  ),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      const Text('Duration', style: TextStyle(color: Colors.white54, fontSize: 13)),
                      const SizedBox(height: 4),
                      Text(
                        '$_selectedHours ${_selectedHours == 1 ? 'Hour' : 'Hours'}',
                        style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: Colors.white),
                      ),
                    ],
                  ),
                ],
              ),
            ),

            const SizedBox(height: 32),
            _buildFeatureRow(Icons.timer, 'Flexible Time', 'Extend your booking at any time from the app.'),
            const SizedBox(height: 16),
            _buildFeatureRow(Icons.pin_drop, 'Unlimited Stops', 'The driver is yours for the entire duration.'),
            const SizedBox(height: 16),
            _buildFeatureRow(Icons.shield_outlined, 'Fully Insured', 'All trips are covered by WADEXPRO insurance.'),
            const Spacer(),
            ElevatedButton(
              onPressed: _isBooking ? null : _bookHourly,
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.white,
                disabledBackgroundColor: Colors.white24,
                foregroundColor: Colors.black,
                minimumSize: const Size(double.infinity, 56),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
              ),
              child: _isBooking
                  ? const SizedBox(height: 20, width: 20, child: CircularProgressIndicator(color: Colors.black, strokeWidth: 2))
                  : Text(
                      'Book $_selectedHours-Hour Chauffeur • GHS $estimatedCost',
                      style: const TextStyle(color: Colors.black, fontWeight: FontWeight.bold),
                    ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildHourSelector() {
    return Wrap(
      spacing: 12,
      runSpacing: 12,
      children: _hourOptions.map((hours) {
        final isSelected = _selectedHours == hours;
        return GestureDetector(
          onTap: () => setState(() => _selectedHours = hours),
          child: Container(
            width: 80,
            height: 80,
            decoration: BoxDecoration(
              color: isSelected ? Colors.white : Colors.white12,
              borderRadius: BorderRadius.circular(20),
              border: isSelected ? Border.all(color: Colors.white, width: 2) : Border.all(color: Colors.white12),
            ),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Text('$hours', style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold, color: isSelected ? Colors.black : Colors.white)),
                Text(hours == 1 ? 'Hr' : 'Hrs', style: TextStyle(fontSize: 14, color: isSelected ? Colors.black54 : Colors.white54)),
              ],
            ),
          ),
        );
      }).toList(),
    );
  }

  Widget _buildFeatureRow(IconData icon, String title, String subtitle) {
    return Row(
      children: [
        Icon(icon, color: Colors.white),
        const SizedBox(width: 16),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(title, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: Colors.white)),
              Text(subtitle, style: const TextStyle(fontSize: 13, color: Colors.white54)),
            ],
          ),
        ),
      ],
    );
  }
}
