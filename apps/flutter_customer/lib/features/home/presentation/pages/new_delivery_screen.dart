import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:wadexpro_customer/core/theme/app_colors.dart';
import 'package:wadexpro_customer/core/services/location_service.dart';
import 'package:wadexpro_customer/core/network/api_provider.dart';
import 'package:wadexpro_customer/features/home/presentation/pages/search_screen.dart';
import 'package:wadexpro_customer/features/home/presentation/providers/ride_booking_provider.dart';
import 'package:wadexpro_customer/features/auth/presentation/providers/auth_provider.dart';

class NewDeliveryScreen extends ConsumerStatefulWidget {
  const NewDeliveryScreen({super.key});

  @override
  ConsumerState<NewDeliveryScreen> createState() => _NewDeliveryScreenState();
}

class _NewDeliveryScreenState extends ConsumerState<NewDeliveryScreen> {
  final _formKey = GlobalKey<FormState>();
  
  // Basic Info
  final _pickupController = TextEditingController();
  LatLng? _pickupLatLng;
  final _contactNameController = TextEditingController();
  final _contactPhoneController = TextEditingController();
  final _packageDescController = TextEditingController();
  String _packageCategory = 'General';
  
  // Stops
  final List<Map<String, dynamic>> _stops = [
    {
      'address': '',
      'lat': 0.0,
      'lng': 0.0,
      'contact_name': '',
      'contact_phone': '',
      'stop_type': 'dropoff',
    }
  ];

  bool _isLoading = false;

  @override
  void initState() {
    super.initState();
    _setInitialData();
  }

  void _setInitialData() async {
    final user = ref.read(authProvider).user;
    if (user != null) {
      _contactNameController.text = user.name;
    }

    try {
      final pos = await LocationService().getCurrentLocation();
      if (pos != null && mounted) {
        setState(() {
          _pickupLatLng = LatLng(pos.latitude, pos.longitude);
          _pickupController.text = 'Current Location';
        });
      }
    } catch (e) {}
  }

  void _pickAddress(bool isPickup, {int? stopIndex}) {
    Navigator.of(context).push(
      MaterialPageRoute(
        builder: (_) => SearchScreen(
          title: isPickup ? 'Set Pickup Point' : 'Set Stop Location',
          onSelect: (address, LatLng location) {
            setState(() {
              if (isPickup) {
                _pickupController.text = address;
                _pickupLatLng = location;
              } else if (stopIndex != null) {
                _stops[stopIndex]['address'] = address;
                _stops[stopIndex]['lat'] = location.latitude;
                _stops[stopIndex]['lng'] = location.longitude;
              }
            });
          },
        ),
      ),
    );
  }

  void _addStop() {
    setState(() {
      _stops.add({
        'address': '',
        'lat': 5.6037,
        'lng': -0.1870,
        'contact_name': '',
        'contact_phone': '',
        'stop_type': 'dropoff',
      });
    });
  }

  void _removeStop(int index) {
    if (_stops.length > 1) {
      setState(() {
        _stops.removeAt(index);
      });
    }
  }

  Future<void> _submitOrder() async {
    if (_pickupLatLng == null) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Please select a pickup location')));
      return;
    }
    if (!_formKey.currentState!.validate()) return;
    
    setState(() => _isLoading = true);
    
    try {
      final order = await ref.read(rideRepositoryProvider).createOrder(
        pickupAddress: _pickupController.text,
        pickupLat: _pickupLatLng!.latitude,
        pickupLng: _pickupLatLng!.longitude,
        contactName: _contactNameController.text,
        contactPhone: _contactPhoneController.text,
        packageDescription: '[$_packageCategory] ${_packageDescController.text}',
        stops: _stops,
        priority: 'express',
      );

      // Trigger Real-Time Socket Broadcast
      ref.read(socketServiceProvider).requestOrder({
        'orderId': order['id'].toString(),
        'pickupAddress': _pickupController.text,
        'pickupLat': _pickupLatLng!.latitude,
        'pickupLng': _pickupLatLng!.longitude,
        'packageDescription': '[$_packageCategory] ${_packageDescController.text}',
        'stops': _stops,
        'priority': 'express',
        'estimatedPrice': order['total_price'] ?? 0.0,
      });
      
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Delivery Booked Successfully!'),
            backgroundColor: AppColors.primary,
          ),
        );
        Navigator.pop(context);
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error: $e'), backgroundColor: Colors.red),
        );
      }
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        title: const Text(
          'BOOK WADEXPRO DELIVERY',
          style: TextStyle(
            color: AppColors.primary,
            fontWeight: FontWeight.w900,
            fontSize: 16,
            letterSpacing: 1,
          ),
        ),
        leading: IconButton(
          icon: const Icon(Icons.chevron_left, color: Color(0xFF0F172A)),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: Form(
        key: _formKey,
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(24),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Section: Pickup Info
              _buildSectionHeader('PICKUP DETAILS', Icons.location_on_outlined),
              const SizedBox(height: 16),
              _buildTextField(_pickupController, 'Pickup Address', Icons.search, readOnly: true, onTap: () => _pickAddress(true)),
              const SizedBox(height: 12),
              Row(
                children: [
                  Expanded(child: _buildTextField(_contactNameController, 'Name', Icons.person_outline)),
                  const SizedBox(width: 12),
                  Expanded(child: _buildTextField(_contactPhoneController, 'Phone', Icons.phone_outlined)),
                ],
              ),
              
              const SizedBox(height: 32),
              _buildSectionHeader('PACKAGE INFO', Icons.inventory_2_outlined),
              const SizedBox(height: 16),
              _buildCategorySelector(),
              const SizedBox(height: 16),
              _buildTextField(_packageDescController, 'What are we delivering?', Icons.info_outline),

              const SizedBox(height: 32),
              
              // Section: Stops
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                   _buildSectionHeader('DESTINATIONS', Icons.near_me_outlined),
                   TextButton.icon(
                     onPressed: _addStop,
                     icon: const Icon(Icons.add_circle_outline, size: 16),
                     label: const Text('ADD STOP', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 12)),
                   ),
                ],
              ),
              const SizedBox(height: 16),
              
              ListView.separated(
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                itemCount: _stops.length,
                separatorBuilder: (_, _) => const SizedBox(height: 24),
                itemBuilder: (context, index) {
                  return Container(
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(24),
                      border: Border.all(color: const Color(0xFFE2E8F0)),
                      boxShadow: [
                        BoxShadow(
                          color: const Color(0xFF0F172A).withOpacity(0.05),
                          blurRadius: 10,
                          offset: const Offset(0, 4),
                        )
                      ],
                    ),
                    child: Column(
                      children: [
                        Row(
                          children: [
                             CircleAvatar(
                               radius: 12,
                               backgroundColor: const Color(0xFF3B82F6),
                               child: Text('${index + 1}', style: const TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.w900)),
                             ),
                             const SizedBox(width: 12),
                             Expanded(
                               child: _buildTextField(
                                 null, 
                                 _stops[index]['address'].isEmpty ? 'Drop-off Address' : _stops[index]['address'], 
                                 null, 
                                 readOnly: true,
                                 onTap: () => _pickAddress(false, stopIndex: index),
                               )
                             ),
                             if (_stops.length > 1)
                               IconButton(
                                 onPressed: () => _removeStop(index),
                                 icon: const Icon(Icons.delete_outline, size: 18, color: Color(0xFFEF4444)),
                               )
                          ],
                        ),
                        const SizedBox(height: 12),
                        Row(
                          children: [
                            Expanded(child: _buildTextField(null, 'Name', null, onChanged: (v) => _stops[index]['contact_name'] = v)),
                            const SizedBox(width: 12),
                            Expanded(child: _buildTextField(null, 'Phone', null, onChanged: (v) => _stops[index]['contact_phone'] = v)),
                          ],
                        ),
                      ],
                    ),
                  );
                },
              ),

              const SizedBox(height: 48),

              // Action Button
              SizedBox(
                width: double.infinity,
                height: 64,
                child: ElevatedButton(
                  onPressed: _isLoading ? null : _submitOrder,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppColors.primary,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                    elevation: 0,
                  ),
                  child: _isLoading 
                    ? const CircularProgressIndicator(color: Colors.white)
                    : const Text(
                        'INITIALIZE EXPRESS DISPATCH',
                        style: TextStyle(
                          color: Colors.white,
                          fontWeight: FontWeight.w900,
                          letterSpacing: 1.5,
                        ),
                      ),
                ),
              ),
              const SizedBox(height: 40),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildSectionHeader(String title, IconData icon) {
    return Row(
      children: [
        Icon(icon, size: 18, color: const Color(0xFF64748B)),
        const SizedBox(width: 8),
        Text(
          title,
          style: const TextStyle(
            color: Color(0xFF64748B),
            fontWeight: FontWeight.w900,
            fontSize: 11,
            letterSpacing: 1.2,
          ),
        ),
      ],
    );
  }

  Widget _buildCategorySelector() {
    final categories = ['General', 'Documents', 'Food', 'Electronics', 'Fragile'];
    return SingleChildScrollView(
      scrollDirection: Axis.horizontal,
      child: Row(
        children: categories.map((cat) {
          final isSelected = _packageCategory == cat;
          return GestureDetector(
            onTap: () => setState(() => _packageCategory = cat),
            child: Container(
              margin: const EdgeInsets.only(right: 8),
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
              decoration: BoxDecoration(
                color: isSelected ? AppColors.primary : Colors.white,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: isSelected ? AppColors.primary : const Color(0xFFE2E8F0)),
              ),
              child: Text(
                cat,
                style: TextStyle(
                  color: isSelected ? Colors.white : const Color(0xFF64748B),
                  fontWeight: FontWeight.bold,
                  fontSize: 12,
                ),
              ),
            ),
          );
        }).toList(),
      ),
    );
  }

  Widget _buildTextField(TextEditingController? controller, String hint, IconData? icon, {Function(String)? onChanged, bool readOnly = false, VoidCallback? onTap}) {
    return Container(
      decoration: BoxDecoration(
        color: const Color(0xFFF1F5F9),
        borderRadius: BorderRadius.circular(16),
      ),
      child: TextFormField(
        controller: controller,
        onChanged: onChanged,
        readOnly: readOnly,
        onTap: onTap,
        style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
        decoration: InputDecoration(
          hintText: hint,
          hintStyle: TextStyle(color: const Color(0xFF94A3B8), fontWeight: FontWeight.w500, fontSize: 14),
          prefixIcon: icon != null ? Icon(icon, size: 18, color: const Color(0xFF94A3B8)) : null,
          border: InputBorder.none,
          contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
        ),
        validator: (v) => (controller == null && hint.contains('Address')) ? null : (v == null || v.isEmpty ? 'Required' : null),
      ),
    );
  }
}
