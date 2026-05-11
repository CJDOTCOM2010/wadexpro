import 'package:flutter/material.dart';
import '../../../../core/theme/app_colors.dart';

class CourierSearchScreen extends StatefulWidget {
  const CourierSearchScreen({super.key});

  @override
  State<CourierSearchScreen> createState() => _CourierSearchScreenState();
}

class _CourierSearchScreenState extends State<CourierSearchScreen> {
  final TextEditingController _pickupController = TextEditingController(text: 'Current Location');
  final TextEditingController _dropoffController = TextEditingController();
  String _pickupLabel = 'Pick-up now';
  bool _isNow = true;

  void _showScheduleModal() {
    final now = DateTime.now();
    final days = List.generate(7, (i) => now.add(Duration(days: i)));
    int selectedDayIndex = 0;
    String selectedTimeSlot = '23:30 - 00:00';

    final timeSlots = [
      '08:00 - 08:30', '08:30 - 09:00', '09:00 - 09:30', '09:30 - 10:00',
      '10:00 - 10:30', '10:30 - 11:00', '11:00 - 11:30', '11:30 - 12:00',
      '12:00 - 12:30', '12:30 - 13:00', '13:00 - 13:30', '13:30 - 14:00',
      '14:00 - 14:30', '14:30 - 15:00', '15:00 - 15:30', '15:30 - 16:00',
      '16:00 - 16:30', '16:30 - 17:00', '17:00 - 17:30', '17:30 - 18:00',
      '18:00 - 18:30', '18:30 - 19:00', '19:00 - 19:30', '19:30 - 20:00',
      '20:00 - 20:30', '20:30 - 21:00', '21:00 - 21:30', '21:30 - 22:00',
      '22:00 - 22:30', '22:30 - 23:00', '23:00 - 23:30', '23:30 - 00:00',
    ];

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: AppColors.obsidianDark,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      builder: (context) {
        return StatefulBuilder(builder: (context, setModalState) {
          final selectedDay = days[selectedDayIndex];
          final dayLabel = selectedDayIndex == 0 ? 'Today' : selectedDayIndex == 1 ? 'Tomorrow' : _weekday(selectedDay.weekday);

          return Container(
            height: MediaQuery.of(context).size.height * 0.85,
            padding: const EdgeInsets.fromLTRB(20, 10, 20, 30),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: Colors.white24, borderRadius: BorderRadius.circular(2)))),
                const SizedBox(height: 16),
                Row(
                  children: [
                    IconButton(icon: const Icon(Icons.close, color: Colors.white), onPressed: () => Navigator.pop(context)),
                    const Expanded(
                      child: Text('Schedule pick-up', textAlign: TextAlign.center, style: TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)),
                    ),
                    const SizedBox(width: 48),
                  ],
                ),
                const SizedBox(height: 24),

                // Day selector
                SizedBox(
                  height: 80,
                  child: ListView.builder(
                    scrollDirection: Axis.horizontal,
                    itemCount: days.length,
                    itemBuilder: (ctx, i) {
                      final d = days[i];
                      final label = i == 0 ? 'Today' : i == 1 ? 'Tomorrow' : _weekday(d.weekday);
                      final dateStr = '${_month(d.month)} ${d.day}';
                      final isSelected = selectedDayIndex == i;
                      return GestureDetector(
                        onTap: () => setModalState(() => selectedDayIndex = i),
                        child: Container(
                          width: 110,
                          margin: const EdgeInsets.only(right: 12),
                          padding: const EdgeInsets.all(14),
                          decoration: BoxDecoration(
                            color: Colors.transparent,
                            borderRadius: BorderRadius.circular(16),
                            border: Border.all(color: isSelected ? Colors.white : Colors.white24, width: isSelected ? 2 : 1),
                          ),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Text(label, style: TextStyle(color: isSelected ? Colors.white : Colors.white54, fontWeight: FontWeight.bold, fontSize: 15)),
                              const SizedBox(height: 2),
                              Text(dateStr, style: TextStyle(color: isSelected ? Colors.white70 : Colors.white38, fontSize: 13)),
                            ],
                          ),
                        ),
                      );
                    },
                  ),
                ),

                const SizedBox(height: 24),
                const Text('SELECT TIME', style: TextStyle(color: Colors.white54, fontSize: 12, fontWeight: FontWeight.bold, letterSpacing: 1)),
                const SizedBox(height: 12),

                Expanded(
                  child: ListView.separated(
                    itemCount: timeSlots.length,
                    separatorBuilder: (_, _) => const Divider(color: Colors.white10, height: 1),
                    itemBuilder: (ctx, i) {
                      final slot = timeSlots[i];
                      final isSelected = selectedTimeSlot == slot;
                      return ListTile(
                        contentPadding: EdgeInsets.zero,
                        title: Text(slot, style: TextStyle(color: isSelected ? Colors.white : Colors.white70, fontSize: 16, fontWeight: isSelected ? FontWeight.bold : FontWeight.normal)),
                        trailing: Icon(
                          isSelected ? Icons.radio_button_checked : Icons.radio_button_unchecked,
                          color: isSelected ? Colors.white : Colors.white24,
                        ),
                        onTap: () => setModalState(() => selectedTimeSlot = slot),
                      );
                    },
                  ),
                ),

                const SizedBox(height: 16),
                ElevatedButton(
                  onPressed: () {
                    setState(() {
                      _isNow = false;
                      _pickupLabel = '$dayLabel, $selectedTimeSlot';
                    });
                    Navigator.pop(context);
                  },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.white,
                    foregroundColor: Colors.black,
                    minimumSize: const Size(double.infinity, 56),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                  ),
                  child: const Text('Select time', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                ),
                const SizedBox(height: 12),
                TextButton(
                  onPressed: () {
                    setState(() {
                      _isNow = true;
                      _pickupLabel = 'Pick-up now';
                    });
                    Navigator.pop(context);
                  },
                  style: TextButton.styleFrom(
                    minimumSize: const Size(double.infinity, 56),
                    backgroundColor: Colors.white10,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                  ),
                  child: const Text('Cancel', style: TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.bold)),
                ),
              ],
            ),
          );
        });
      },
    );
  }

  String _weekday(int d) {
    const days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    return days[d - 1];
  }

  String _month(int m) {
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    return months[m - 1];
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.obsidianDark,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: Colors.white),
          onPressed: () => Navigator.pop(context),
        ),
        title: const Text("Where's it going?", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
        centerTitle: true,
      ),
      body: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Pick-up pill
                GestureDetector(
                  onTap: _showScheduleModal,
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                    decoration: BoxDecoration(
                      color: _isNow ? Colors.white12 : AppColors.primary.withOpacity(0.2),
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(Icons.schedule, color: _isNow ? Colors.white : AppColors.primary, size: 16),
                        const SizedBox(width: 8),
                        Text(_pickupLabel, style: TextStyle(color: _isNow ? Colors.white : Colors.white, fontWeight: FontWeight.w600)),
                        const SizedBox(width: 4),
                        Icon(Icons.keyboard_arrow_down, color: _isNow ? Colors.white : Colors.white, size: 16),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 16),

                // Timeline + fields
                Row(
                  children: [
                    SizedBox(
                      width: 24,
                      height: 100,
                      child: Stack(
                        alignment: Alignment.center,
                        children: [
                          Positioned(top: 20, bottom: 20, child: Container(width: 1, color: Colors.white30)),
                          Positioned(top: 14, child: Container(width: 8, height: 8, decoration: const BoxDecoration(color: Colors.white54, shape: BoxShape.circle))),
                          Positioned(bottom: 14, child: Container(width: 8, height: 8, decoration: const BoxDecoration(color: Colors.white, shape: BoxShape.rectangle))),
                        ],
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        children: [
                          Container(
                            height: 48,
                            decoration: BoxDecoration(color: Colors.white12, borderRadius: BorderRadius.circular(8)),
                            child: TextField(
                              controller: _pickupController,
                              style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w600),
                              decoration: const InputDecoration(
                                border: InputBorder.none,
                                contentPadding: EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                              ),
                            ),
                          ),
                          const SizedBox(height: 8),
                          Container(
                            height: 48,
                            decoration: BoxDecoration(color: Colors.white12, borderRadius: BorderRadius.circular(8)),
                            child: TextField(
                              controller: _dropoffController,
                              autofocus: true,
                              style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w600),
                              decoration: const InputDecoration(
                                hintText: "Choose recipient's location",
                                hintStyle: TextStyle(color: Colors.white54),
                                border: InputBorder.none,
                                contentPadding: EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),

          const SizedBox(height: 20),
          const Divider(color: Colors.white12, height: 1),

          Expanded(
            child: ListView(
              padding: EdgeInsets.zero,
              children: [
                ListTile(
                  leading: const Icon(Icons.star_border, color: Colors.white),
                  title: const Text('Saved places', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                  onTap: () {},
                ),
                const Divider(color: Colors.white12, height: 1, indent: 56),
                _buildRecentPlace('Achimota Mall', 'Accra - Kumasi Rd, Accra', '2.0 mi'),
                const Divider(color: Colors.white12, height: 1, indent: 56),
                _buildRecentPlace('Cedi Conference Centre', 'Accra', '6.2 mi'),
                const Divider(color: Colors.white12, height: 1, indent: 56),
                _buildRecentPlace('Victory Bible Church, Headquarters', 'HPPG+553, Jarben Okai Ln, Awoshie', '7.4 mi'),
                const Divider(color: Colors.white12, height: 1, indent: 56),
                _buildRecentPlace('Airport West Hotel', 'N Airport Rd, Accra', '7.5 mi'),
                const Divider(color: Colors.white12, height: 1, indent: 56),
                _buildRecentPlace('Taifa Bus Station', 'Taifa - Burkina Rd', '1.3 mi'),
                const Divider(color: Colors.white12, height: 1, indent: 56),
                ListTile(
                  leading: Container(
                    padding: const EdgeInsets.all(6),
                    decoration: const BoxDecoration(color: Colors.white12, shape: BoxShape.circle),
                    child: const Icon(Icons.location_on_outlined, color: Colors.white, size: 20),
                  ),
                  title: const Text('Set location on map', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                  onTap: () {
                    Navigator.pop(context);
                  },
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildRecentPlace(String title, String subtitle, String distance) {
    return ListTile(
      leading: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          const Icon(Icons.schedule, color: Colors.white54, size: 20),
          const SizedBox(height: 4),
          Text(distance, style: const TextStyle(color: Colors.white54, fontSize: 10)),
        ],
      ),
      title: Text(title, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w600)),
      subtitle: Text(subtitle, style: const TextStyle(color: Colors.white54, fontSize: 13)),
      onTap: () {
        _dropoffController.text = title;
        Navigator.pop(context, title);
      },
    );
  }
}
