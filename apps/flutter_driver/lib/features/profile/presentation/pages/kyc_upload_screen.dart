import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:image_picker/image_picker.dart';
import 'dart:io';
import '../../../../core/theme/app_colors.dart';
import '../providers/profile_provider.dart';

class KYCUploadScreen extends ConsumerStatefulWidget {
  const KYCUploadScreen({super.key});

  @override
  ConsumerState<KYCUploadScreen> createState() => _KYCUploadScreenState();
}

class _KYCUploadScreenState extends ConsumerState<KYCUploadScreen> {
  final _formKey = GlobalKey<FormState>();
  final _licenseNumberController = TextEditingController();
  final _licenseClassController = TextEditingController();
  DateTime? _expiresAt;

  XFile? _idCardFront;
  XFile? _idCardBack;
  XFile? _driverPhoto;

  final _picker = ImagePicker();
  bool _isLoading = false;

  @override
  void initState() {
    super.initState();
    // Prefill data if exists
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final user = ref.read(profileProvider).data;
      if (user != null && user['driver'] != null) {
        final driver = user['driver'];
        _licenseNumberController.text = driver['license_number'] ?? '';
        _licenseClassController.text = driver['license_class'] ?? '';
        if (driver['license_expires_at'] != null) {
          _expiresAt = DateTime.tryParse(driver['license_expires_at']);
        }
        setState(() {});
      }
    });
  }

  Future<void> _pickImage(String type) async {
    final XFile? image = await _picker.pickImage(source: ImageSource.gallery, imageQuality: 70);
    if (image != null) {
      setState(() {
        if (type == 'front') _idCardFront = image;
        if (type == 'back') _idCardBack = image;
        if (type == 'photo') _driverPhoto = image;
      });
    }
  }

  Future<void> _selectDate() async {
    final date = await showDatePicker(
      context: context,
      initialDate: _expiresAt ?? DateTime.now().add(const Duration(days: 365)),
      firstDate: DateTime.now(),
      lastDate: DateTime.now().add(const Duration(days: 3650)),
      builder: (context, child) {
        return Theme(
          data: ThemeData.light().copyWith(
            colorScheme: const ColorScheme.light(primary: AppColors.primaryNavy),
          ),
          child: child!,
        );
      },
    );
    if (date != null) {
      setState(() => _expiresAt = date);
    }
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    if (_expiresAt == null) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Please select license expiry date')));
      return;
    }

    setState(() => _isLoading = true);

    try {
      await ref.read(profileProvider.notifier).submitKYC(
        licenseNumber: _licenseNumberController.text.trim(),
        licenseClass: _licenseClassController.text.trim(),
        expiresAt: _expiresAt!.toIso8601String().split('T')[0],
        idCardFront: _idCardFront,
        idCardBack: _idCardBack,
        driverPhoto: _driverPhoto,
      );

      if (mounted) {
        Navigator.pop(context);
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('KYC documents submitted for review!')),
        );
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.toString())));
      }
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('Identity Verification', style: TextStyle(fontWeight: FontWeight.bold)),
        backgroundColor: Colors.white,
        foregroundColor: AppColors.primaryNavy,
        elevation: 0,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text('License Information', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppColors.primaryNavy)),
              const SizedBox(height: 16),
              TextFormField(
                controller: _licenseNumberController,
                decoration: _inputDecoration('License Number'),
                validator: (val) => val == null || val.isEmpty ? 'Required' : null,
              ),
              const SizedBox(height: 16),
              TextFormField(
                controller: _licenseClassController,
                decoration: _inputDecoration('License Class (e.g. C, CDL)'),
                validator: (val) => val == null || val.isEmpty ? 'Required' : null,
              ),
              const SizedBox(height: 16),
              InkWell(
                onTap: _selectDate,
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
                  decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12)),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        _expiresAt == null ? 'License Expiry Date' : '${_expiresAt!.year}-${_expiresAt!.month.toString().padLeft(2, '0')}-${_expiresAt!.day.toString().padLeft(2, '0')}',
                        style: TextStyle(color: _expiresAt == null ? Colors.black54 : Colors.black, fontSize: 16),
                      ),
                      const Icon(Icons.calendar_today, color: AppColors.primaryNavy),
                    ],
                  ),
                ),
              ),
              
              const SizedBox(height: 32),
              const Text('Document Uploads', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppColors.primaryNavy)),
              const SizedBox(height: 8),
              const Text('Please upload clear, legible photos of your documents.', style: TextStyle(color: Colors.black54)),
              const SizedBox(height: 16),

              _buildImagePicker('ID Card / License (Front)', _idCardFront, () => _pickImage('front')),
              const SizedBox(height: 16),
              _buildImagePicker('ID Card / License (Back)', _idCardBack, () => _pickImage('back')),
              const SizedBox(height: 16),
              _buildImagePicker('Clear Driver Photo', _driverPhoto, () => _pickImage('photo')),

              const SizedBox(height: 32),
              SizedBox(
                width: double.infinity,
                height: 56,
                child: ElevatedButton(
                  onPressed: _isLoading ? null : _submit,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF6C63FF),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                  ),
                  child: _isLoading
                      ? const CircularProgressIndicator(color: Colors.white)
                      : const Text('Submit Documents', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.white)),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildImagePicker(String label, XFile? file, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        height: 120,
        width: double.infinity,
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: file != null ? Colors.green : Colors.grey.shade300, width: 2),
        ),
        child: file != null
            ? ClipRRect(
                borderRadius: BorderRadius.circular(10),
                child: Image.file(File(file.path), fit: BoxFit.cover),
              )
            : Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Icon(Icons.camera_alt, color: AppColors.primaryNavy, size: 32),
                  const SizedBox(height: 8),
                  Text(label, style: const TextStyle(color: AppColors.primaryNavy, fontWeight: FontWeight.bold)),
                ],
              ),
      ),
    );
  }

  InputDecoration _inputDecoration(String hint) {
    return InputDecoration(
      hintText: hint,
      filled: true,
      fillColor: Colors.white,
      border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
      enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
      focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: Color(0xFF6C63FF), width: 2)),
      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
    );
  }
}
