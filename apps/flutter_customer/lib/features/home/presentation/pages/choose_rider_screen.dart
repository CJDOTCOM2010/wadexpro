import 'package:flutter/material.dart';
import '../../../../core/theme/app_colors.dart';

class ChooseRiderScreen extends StatefulWidget {
  const ChooseRiderScreen({super.key});

  @override
  State<ChooseRiderScreen> createState() => _ChooseRiderScreenState();
}

class _ChooseRiderScreenState extends State<ChooseRiderScreen> {
  String _searchQuery = '';
  String? _selectedContactName;

  final List<Map<String, String>> _contacts = [
    {'name': 'Becky Becky', 'phone': '+233549595963', 'initials': 'BB', 'color': 'blue'},
    {'name': 'A CHRISTOPHE', 'phone': '+233247275886', 'initials': 'AC', 'color': 'green'},
    {'name': 'A G AZA', 'phone': '+233246381447', 'initials': 'AA', 'color': 'purple'},
    {'name': 'A G NAA', 'phone': '+233244203474', 'initials': 'AN', 'color': 'orange'},
    {'name': 'A GODSWAY', 'phone': '+233541916330', 'initials': 'AG', 'color': 'teal'},
    {'name': 'A NUNANAA', 'phone': '+233542699166', 'initials': 'AN', 'color': 'red'},
    {'name': 'A PLUZ', 'phone': '+233549806138', 'initials': 'AP', 'color': 'blue'},
    {'name': 'B KOFI', 'phone': '+233201234567', 'initials': 'BK', 'color': 'green'},
    {'name': 'C MENSAH', 'phone': '+233267891234', 'initials': 'CM', 'color': 'purple'},
  ];

  Color _avatarColor(String colorKey) {
    switch (colorKey) {
      case 'green': return Colors.green.shade700;
      case 'purple': return Colors.purple.shade600;
      case 'orange': return Colors.orange.shade700;
      case 'teal': return Colors.teal.shade600;
      case 'red': return Colors.red.shade600;
      default: return AppColors.primary;
    }
  }

  List<Map<String, String>> get _filtered {
    if (_searchQuery.isEmpty) return _contacts;
    return _contacts.where((c) =>
      c['name']!.toLowerCase().contains(_searchQuery.toLowerCase()) ||
      c['phone']!.contains(_searchQuery)
    ).toList();
  }

  void _selectContact(Map<String, String> contact) {
    setState(() => _selectedContactName = contact['name']);
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(
      content: Text('${contact['name']} selected as rider'),
      backgroundColor: Colors.green,
      behavior: SnackBarBehavior.floating,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      action: SnackBarAction(
        label: 'Confirm',
        textColor: Colors.white,
        onPressed: () => Navigator.pop(context),
      ),
    ));
  }

  void _showAddContactModal() {
    final firstCtrl = TextEditingController();
    final lastCtrl = TextEditingController();
    final phoneCtrl = TextEditingController();
    final formKey = GlobalKey<FormState>();

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: AppColors.obsidianDark,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      builder: (ctx) => Padding(
        padding: EdgeInsets.only(bottom: MediaQuery.of(ctx).viewInsets.bottom, left: 20, right: 20, top: 24),
        child: Form(
          key: formKey,
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: Colors.white24, borderRadius: BorderRadius.circular(2)))),
              const SizedBox(height: 20),
              const Text('Add new contact', style: TextStyle(fontSize: 22, fontWeight: FontWeight.bold, color: Colors.white)),
              const SizedBox(height: 24),
              _darkField(controller: firstCtrl, label: 'First name', validator: (v) => v!.isEmpty ? 'Required' : null),
              const SizedBox(height: 16),
              _darkField(controller: lastCtrl, label: 'Last name', validator: (v) => v!.isEmpty ? 'Required' : null),
              const SizedBox(height: 16),
              _darkField(
                controller: phoneCtrl,
                label: 'Phone number',
                prefix: '+233 ',
                keyboardType: TextInputType.phone,
                validator: (v) => v!.isEmpty ? 'Required' : null,
              ),
              const SizedBox(height: 24),
              ElevatedButton(
                onPressed: () {
                  if (formKey.currentState!.validate()) {
                    final fullName = '${firstCtrl.text.trim()} ${lastCtrl.text.trim()}';
                    final initials = '${firstCtrl.text.isNotEmpty ? firstCtrl.text[0] : ''}${lastCtrl.text.isNotEmpty ? lastCtrl.text[0] : ''}'.toUpperCase();
                    setState(() {
                      _contacts.insert(0, {
                        'name': fullName,
                        'phone': '+233${phoneCtrl.text.trim()}',
                        'initials': initials,
                        'color': 'green',
                      });
                    });
                    Navigator.pop(ctx);
                    ScaffoldMessenger.of(context).showSnackBar(SnackBar(
                      content: Text('$fullName added successfully!'),
                      backgroundColor: Colors.green,
                      behavior: SnackBarBehavior.floating,
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                    ));
                  }
                },
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.white,
                  foregroundColor: Colors.black,
                  minimumSize: const Size(double.infinity, 56),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                ),
                child: const Text('Save contact', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
              ),
              const SizedBox(height: 24),
            ],
          ),
        ),
      ),
    );
  }

  Widget _darkField({required TextEditingController controller, required String label, String? prefix, TextInputType? keyboardType, String? Function(String?)? validator}) {
    return TextFormField(
      controller: controller,
      keyboardType: keyboardType,
      validator: validator,
      style: const TextStyle(color: Colors.white),
      decoration: InputDecoration(
        labelText: label,
        labelStyle: const TextStyle(color: Colors.white54),
        prefixText: prefix,
        prefixStyle: const TextStyle(color: Colors.white),
        enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: Colors.white24)),
        focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: Colors.white)),
        errorBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: Colors.redAccent)),
        focusedErrorBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: Colors.redAccent)),
        errorStyle: const TextStyle(color: Colors.redAccent),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final filtered = _filtered;
    final frequent = filtered.where((c) => c['name'] == 'Becky Becky').toList();
    final others = filtered.where((c) => c['name'] != 'Becky Becky').toList();

    return Scaffold(
      backgroundColor: AppColors.obsidianDark,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: Colors.white, size: 28),
          onPressed: () => Navigator.pop(context),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.person_add, color: Colors.white, size: 26),
            tooltip: 'Add new contact',
            onPressed: _showAddContactModal,
          ),
          const SizedBox(width: 8),
        ],
      ),
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Padding(
              padding: EdgeInsets.symmetric(horizontal: 20, vertical: 8),
              child: Text('Choose a rider', style: TextStyle(fontSize: 28, fontWeight: FontWeight.bold, color: Colors.white)),
            ),
            const SizedBox(height: 16),
            // Search field
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 20),
              child: Container(
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: Colors.white24, width: 1.5),
                  color: Colors.white12,
                ),
                child: TextField(
                  style: const TextStyle(color: Colors.white),
                  onChanged: (v) => setState(() => _searchQuery = v),
                  decoration: InputDecoration(
                    hintText: 'Search name or number',
                    hintStyle: const TextStyle(color: Colors.white54),
                    prefixIcon: const Icon(Icons.search, color: Colors.white54, size: 24),
                    border: InputBorder.none,
                    contentPadding: const EdgeInsets.symmetric(vertical: 16),
                  ),
                ),
              ),
            ),

            if (frequent.isNotEmpty) ...[
              const SizedBox(height: 32),
              const Padding(
                padding: EdgeInsets.symmetric(horizontal: 20),
                child: Text('Frequent contacts', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w600, color: Colors.white54)),
              ),
              const SizedBox(height: 12),
              ...frequent.map((c) => _contactTile(c)),
            ],

            const SizedBox(height: 24),
            const Padding(
              padding: EdgeInsets.symmetric(horizontal: 20),
              child: Text('Device contacts', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w600, color: Colors.white54)),
            ),
            const SizedBox(height: 16),

            if (others.isEmpty)
              Center(child: Padding(
                padding: const EdgeInsets.all(40),
                child: Text('No contacts found', style: const TextStyle(color: Colors.white38)),
              ))
            else
              ListView.separated(
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                itemCount: others.length,
                separatorBuilder: (_, _) => const Divider(color: Colors.white12, height: 1, indent: 80),
                itemBuilder: (ctx, i) => _contactTile(others[i]),
              ),

            const SizedBox(height: 40),
          ],
        ),
      ),
    );
  }

  Widget _contactTile(Map<String, String> contact) {
    final isSelected = _selectedContactName == contact['name'];
    return ListTile(
      contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 6),
      leading: Stack(children: [
        CircleAvatar(
          radius: 24,
          backgroundColor: _avatarColor(contact['color'] ?? 'blue'),
          child: Text(contact['initials']!, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 14)),
        ),
        if (isSelected) Positioned(right: 0, bottom: 0, child: Container(width: 12, height: 12, decoration: const BoxDecoration(color: Colors.green, shape: BoxShape.circle))),
      ]),
      title: Text(contact['name']!, style: TextStyle(fontWeight: FontWeight.w600, fontSize: 16, color: isSelected ? Colors.white : Colors.white70)),
      subtitle: Text(contact['phone']!, style: const TextStyle(color: Colors.white38, fontSize: 13)),
      trailing: isSelected ? const Icon(Icons.check_circle, color: Colors.green, size: 22) : null,
      onTap: () => _selectContact(contact),
    );
  }
}
