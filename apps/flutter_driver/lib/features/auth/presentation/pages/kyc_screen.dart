import 'dart:io';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:image_picker/image_picker.dart';
import '../../../../core/theme/app_colors.dart';
import '../providers/kyc_provider.dart';

class KYCScreen extends ConsumerStatefulWidget {
  const KYCScreen({super.key});

  @override
  ConsumerState<KYCScreen> createState() => _KYCScreenState();
}

class _KYCScreenState extends ConsumerState<KYCScreen> {
  final _formKey = GlobalKey<FormState>();
  final _licenseController = TextEditingController();
  final _expiryController = TextEditingController();
  final _classController = TextEditingController();
  
  XFile? _idFront;
  XFile? _idBack;
  XFile? _photo;
  
  int _currentStep = 0;
  final _picker = ImagePicker();

  Future<void> _pickImage(String type) async {
    final pickedFile = await _picker.pickImage(source: ImageSource.camera, imageQuality: 70);
    if (pickedFile != null) {
      setState(() {
        if (type == 'front') _idFront = pickedFile;
        if (type == 'back') _idBack = pickedFile;
        if (type == 'photo') _photo = pickedFile;
      });
    }
  }

  void _handleSubmit() async {
    if (_idFront == null || _photo == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please upload all required photos.')),
      );
      return;
    }

    final success = await ref.read(kycProvider.notifier).uploadKYC(
      licenseNumber: _licenseController.text,
      licenseExpiresAt: _expiryController.text,
      licenseClass: _classController.text,
      idCardFront: _idFront,
      idCardBack: _idBack,
      driverPhoto: _photo,
    );

    if (success && mounted) {
      // Logic to redirect or show success
    }
  }

  @override
  Widget build(BuildContext context) {
    final kycState = ref.watch(kycProvider);

    if (kycState.isPendingReview) {
      return _buildPendingUI();
    }

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text('Verify Identity', style: TextStyle(color: AppColors.primary, fontWeight: FontWeight.bold)),
        backgroundColor: Colors.white,
        elevation: 0,
        centerTitle: true,
      ),
      body: kycState.isLoading 
        ? const Center(child: CircularProgressIndicator())
        : Stepper(
            type: StepperType.horizontal,
            currentStep: _currentStep,
            onStepContinue: () {
              if (_currentStep < 2) {
                setState(() => _currentStep++);
              } else {
                _handleSubmit();
              }
            },
            onStepCancel: () {
              if (_currentStep > 0) setState(() => _currentStep--);
            },
            steps: [
              Step(
                title: const Text('License'),
                isActive: _currentStep >= 0,
                content: _buildLicenseForm(),
              ),
              Step(
                title: const Text('ID Card'),
                isActive: _currentStep >= 1,
                content: _buildIDUpload(),
              ),
              Step(
                title: const Text('Selfie'),
                isActive: _currentStep >= 2,
                content: _buildPhotoUpload(),
              ),
            ],
          ),
    );
  }

  Widget _buildLicenseForm() {
    return Form(
      key: _formKey,
      child: Column(
        children: [
          const Text('Provide your driving license details as shown on your card.', 
            style: TextStyle(color: Colors.grey, fontSize: 13)),
          const SizedBox(height: 20),
          TextFormField(
            controller: _licenseController,
            decoration: const InputDecoration(labelText: 'License Number', border: OutlineInputBorder()),
          ),
          const SizedBox(height: 16),
          TextFormField(
            controller: _expiryController,
            decoration: const InputDecoration(labelText: 'Expiry Date (YYYY-MM-DD)', border: OutlineInputBorder()),
          ),
          const SizedBox(height: 16),
          TextFormField(
            controller: _classController,
            decoration: const InputDecoration(labelText: 'License Class (e.g. B, C)', border: OutlineInputBorder()),
          ),
        ],
      ),
    );
  }

  Widget _buildIDUpload() {
    return Column(
      children: [
        const Text('Upload a clear photo of your Ghana Card or Passport.', style: TextStyle(color: Colors.grey)),
        const SizedBox(height: 20),
        _uploadBox('ID Card Front', _idFront, () => _pickImage('front')),
        const SizedBox(height: 16),
        _uploadBox('ID Card Back (Optional)', _idBack, () => _pickImage('back')),
      ],
    );
  }

  Widget _buildPhotoUpload() {
    return Column(
      children: [
        const Text('Final step! Take a clear selfie of yourself.', style: TextStyle(color: Colors.grey)),
        const SizedBox(height: 20),
        _uploadBox('Your Profile Photo', _photo, () => _pickImage('photo')),
      ],
    );
  }

  Widget _uploadBox(String label, XFile? file, VoidCallback onTap) {
    return InkWell(
      onTap: onTap,
      child: Container(
        height: 160,
        width: double.infinity,
        decoration: BoxDecoration(
          border: Border.all(color: Colors.grey.shade300),
          borderRadius: BorderRadius.circular(12),
          color: Colors.grey.shade50,
        ),
        child: file != null 
          ? _KYCImagePreview(file: file)
          : Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Icon(Icons.camera_alt_outlined, size: 40, color: AppColors.primary),
                const SizedBox(height: 8),
                Text(label, style: const TextStyle(fontWeight: FontWeight.bold)),
              ],
            ),
      ),
    );
  }

  Widget _buildPendingUI() {
    return Scaffold(
      body: Center(
        child: Padding(
          padding: const EdgeInsets.all(40.0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.hourglass_empty, size: 80, color: AppColors.accent),
              const SizedBox(height: 24),
              const Text('Verification in Progress', 
                style: TextStyle(fontSize: 22, fontWeight: FontWeight.bold, color: AppColors.primary)),
              const SizedBox(height: 12),
              const Text(
                'Our team is currently reviewing your documents. This usually takes 24-48 hours. We will notify you once you are ready to drive!',
                textAlign: TextAlign.center,
                style: TextStyle(color: AppColors.textMuted),
              ),
              const SizedBox(height: 40),
              ElevatedButton(
                onPressed: () => ref.read(kycProvider.notifier).checkStatus(),
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppColors.primary,
                  minimumSize: const Size(double.infinity, 50),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
                child: const Text('Check Status'),
              )
            ],
          ),
        ),
      ),
    );
  }
}

class _KYCImagePreview extends StatelessWidget {
  final XFile file;
  const _KYCImagePreview({required this.file});

  @override
  Widget build(BuildContext context) {
    return ClipRRect(
      borderRadius: BorderRadius.circular(12), 
      child: kIsWeb 
        ? Image.network(file.path, fit: BoxFit.cover)
        : Image.file(File(file.path), fit: BoxFit.cover),
    );
  }
}
