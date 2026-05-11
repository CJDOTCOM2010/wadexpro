import 'dart:io';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:image_picker/image_picker.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/config/brand_config.dart';
import '../providers/kyc_provider.dart';

class KYCScreen extends ConsumerStatefulWidget {
  const KYCScreen({super.key});

  @override
  ConsumerState<KYCScreen> createState() => _KYCScreenState();
}

class _KYCScreenState extends ConsumerState<KYCScreen> {
  int _currentStep = 0;
  final _picker = ImagePicker();
  final _formKey = GlobalKey<FormState>();

  // STEP 1: Basic Profile
  final _nameController = TextEditingController();
  final _emailController = TextEditingController();
  final _cityController = TextEditingController();

  // STEP 2: Personal Info
  final _dobController = TextEditingController();
  final _nationalIdController = TextEditingController();
  final _addressController = TextEditingController();
  final _emergencyContactController = TextEditingController();

  // STEP 3: Documents
  XFile? _licenseFront;
  XFile? _vehicleReg;
  XFile? _insurance;
  XFile? _roadworthy;
  XFile? _profilePhoto;

  // STEP 4: Vehicle Info
  final _brandController = TextEditingController();
  final _modelController = TextEditingController();
  final _yearController = TextEditingController();
  final _plateController = TextEditingController();
  final _colorController = TextEditingController();
  XFile? _carPhotoFront;
  XFile? _carPhotoInterior;

  // STEP 5: Safety & Training
  bool _agreedToSafety = false;
  bool _completedTutorial = false;

  Future<void> _pickImage(Function(XFile?) onPicked) async {
    final pickedFile = await _picker.pickImage(source: ImageSource.gallery, imageQuality: 70);
    if (pickedFile != null) {
      setState(() => onPicked(pickedFile));
    }
  }

  void _handleSubmit() async {
    // Validate text fields
    if (!_formKey.currentState!.validate()) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please fill all required text fields.'), backgroundColor: Colors.red),
      );
      return;
    }
    
    // Validate required uploads
    if (_profilePhoto == null || _licenseFront == null || _vehicleReg == null || _carPhotoFront == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please upload all required photos (Profile, License, Reg, Car).'), backgroundColor: Colors.red),
      );
      return;
    }
    
    // Validate safety checkboxes
    if (!_agreedToSafety || !_completedTutorial) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('You must acknowledge the safety guidelines and tutorial.'), backgroundColor: Colors.red),
      );
      return;
    }

    final success = await ref.read(kycProvider.notifier).submitApplication(
      name: _nameController.text,
      email: _emailController.text,
      city: _cityController.text,
      dob: _dobController.text,
      nationalId: _nationalIdController.text,
      address: _addressController.text,
      emergencyContact: _emergencyContactController.text,
      vehicleBrand: _brandController.text,
      vehicleModel: _modelController.text,
      vehicleYear: _yearController.text,
      vehiclePlate: _plateController.text,
      vehicleColor: _colorController.text,
      profilePhoto: _profilePhoto,
      licenseFront: _licenseFront,
      vehicleReg: _vehicleReg,
      insurance: _insurance,
      roadworthy: _roadworthy,
      carPhotoFront: _carPhotoFront,
      carPhotoInterior: _carPhotoInterior,
    );

    if (success && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Application submitted successfully! Our team will review it shortly.'),
          backgroundColor: Colors.green,
          duration: Duration(seconds: 4),
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final kycState = ref.watch(kycProvider);

    if (kycState.isPendingReview) {
      return _buildPendingUI();
    }

    return Scaffold(
      backgroundColor: AppColors.obsidianDark,
      appBar: AppBar(
        title: Text('Driver Onboarding', style: GoogleFonts.outfit(color: Colors.white, fontWeight: FontWeight.bold)),
        backgroundColor: AppColors.obsidianDark,
        elevation: 0,
        centerTitle: true,
      ),
      body: kycState.isLoading
        ? const Center(child: CircularProgressIndicator(color: AppColors.accent))
        : Theme(
            data: Theme.of(context).copyWith(
              colorScheme: ColorScheme.dark(
                primary: AppColors.accent,
                secondary: AppColors.accent,
                surface: Colors.white10,
              ),
            ),
            child: Form(
              key: _formKey,
              child: Stepper(
                type: StepperType.vertical,
                currentStep: _currentStep,
                physics: const ClampingScrollPhysics(),
                controlsBuilder: (context, details) {
                  final isLastStep = _currentStep == 4;
                  return Padding(
                    padding: const EdgeInsets.only(top: 16),
                    child: Row(
                      children: [
                        Expanded(
                          child: ElevatedButton(
                            onPressed: details.onStepContinue,
                            style: ElevatedButton.styleFrom(
                              backgroundColor: isLastStep ? AppColors.accent : AppColors.primary,
                              foregroundColor: isLastStep ? Colors.black : Colors.white,
                              padding: const EdgeInsets.symmetric(vertical: 14),
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                            ),
                            child: Text(
                              isLastStep ? 'SUBMIT APPLICATION' : 'CONTINUE',
                              style: GoogleFonts.outfit(fontWeight: FontWeight.w900, fontSize: 14),
                            ),
                          ),
                        ),
                        if (_currentStep > 0) ...[
                          const SizedBox(width: 12),
                          TextButton(
                            onPressed: details.onStepCancel,
                            child: Text('BACK', style: GoogleFonts.outfit(color: Colors.white54)),
                          ),
                        ],
                      ],
                    ),
                  );
                },
                onStepContinue: () {
                  if (_currentStep < 4) {
                    setState(() => _currentStep++);
                  } else {
                    _handleSubmit();
                  }
                },
                onStepCancel: () {
                  if (_currentStep > 0) setState(() => _currentStep--);
                },
                onStepTapped: (step) {
                  // Allow tapping back to previous steps
                  if (step <= _currentStep) {
                    setState(() => _currentStep = step);
                  }
                },
                steps: [
                  Step(
                    title: Text('Account Info', style: GoogleFonts.outfit(color: Colors.white, fontWeight: FontWeight.w600)),
                    subtitle: Text('Name, Email, City', style: TextStyle(color: Colors.white38)),
                    isActive: _currentStep >= 0,
                    state: _currentStep > 0 ? StepState.complete : StepState.indexed,
                    content: _buildStep1(),
                  ),
                  Step(
                    title: Text('Personal Details', style: GoogleFonts.outfit(color: Colors.white, fontWeight: FontWeight.w600)),
                    subtitle: Text('ID, Address, Emergency', style: TextStyle(color: Colors.white38)),
                    isActive: _currentStep >= 1,
                    state: _currentStep > 1 ? StepState.complete : StepState.indexed,
                    content: _buildStep2(),
                  ),
                  Step(
                    title: Text('Document Upload', style: GoogleFonts.outfit(color: Colors.white, fontWeight: FontWeight.w600)),
                    subtitle: Text('License, Registration, Photo', style: TextStyle(color: Colors.white38)),
                    isActive: _currentStep >= 2,
                    state: _currentStep > 2 ? StepState.complete : StepState.indexed,
                    content: _buildStep3(),
                  ),
                  Step(
                    title: Text('Vehicle Info', style: GoogleFonts.outfit(color: Colors.white, fontWeight: FontWeight.w600)),
                    subtitle: Text('Brand, Model, Plate', style: TextStyle(color: Colors.white38)),
                    isActive: _currentStep >= 3,
                    state: _currentStep > 3 ? StepState.complete : StepState.indexed,
                    content: _buildStep4(),
                  ),
                  Step(
                    title: Text('Safety & Training', style: GoogleFonts.outfit(color: Colors.white, fontWeight: FontWeight.w600)),
                    subtitle: Text('Guidelines, Acknowledgement', style: TextStyle(color: Colors.white38)),
                    isActive: _currentStep >= 4,
                    state: _currentStep == 4 ? StepState.editing : StepState.indexed,
                    content: _buildStep5(),
                  ),
                ],
              ),
            ),
          ),
    );
  }

  Widget _buildStep1() {
    return Column(
      children: [
        _buildTextField(_nameController, 'Full Name'),
        const SizedBox(height: 12),
        _buildTextField(_emailController, 'Email Address', keyboardType: TextInputType.emailAddress),
        const SizedBox(height: 12),
        _buildTextField(_cityController, 'City / Region'),
      ],
    );
  }

  Widget _buildStep2() {
    return Column(
      children: [
        _buildTextField(_dobController, 'Date of Birth (YYYY-MM-DD)'),
        const SizedBox(height: 12),
        _buildTextField(_nationalIdController, 'National ID / Ghana Card Number'),
        const SizedBox(height: 12),
        _buildTextField(_addressController, 'Residential Address'),
        const SizedBox(height: 12),
        _buildTextField(_emergencyContactController, 'Emergency Contact Phone', keyboardType: TextInputType.phone),
      ],
    );
  }

  Widget _buildStep3() {
    return Column(
      children: [
        _uploadBox('Profile Photo (Clear Selfie)', _profilePhoto, (f) => _profilePhoto = f),
        const SizedBox(height: 12),
        _uploadBox("Driver's License (Front)", _licenseFront, (f) => _licenseFront = f),
        const SizedBox(height: 12),
        _uploadBox("Vehicle Registration", _vehicleReg, (f) => _vehicleReg = f),
        const SizedBox(height: 12),
        _uploadBox("Insurance Sticker (Optional)", _insurance, (f) => _insurance = f),
        const SizedBox(height: 12),
        _uploadBox("Roadworthiness Certificate (Optional)", _roadworthy, (f) => _roadworthy = f),
      ],
    );
  }

  Widget _buildStep4() {
    return Column(
      children: [
        Row(
          children: [
            Expanded(child: _buildTextField(_brandController, 'Brand (e.g. Toyota)')),
            const SizedBox(width: 12),
            Expanded(child: _buildTextField(_modelController, 'Model (e.g. Corolla)')),
          ],
        ),
        const SizedBox(height: 12),
        Row(
          children: [
            Expanded(child: _buildTextField(_yearController, 'Year (e.g. 2018)')),
            const SizedBox(width: 12),
            Expanded(child: _buildTextField(_colorController, 'Color')),
          ],
        ),
        const SizedBox(height: 12),
        _buildTextField(_plateController, 'Plate Number'),
        const SizedBox(height: 16),
        _uploadBox("Car Photo (Exterior Front/Plate)", _carPhotoFront, (f) => _carPhotoFront = f),
        const SizedBox(height: 12),
        _uploadBox("Car Photo (Interior) [Optional]", _carPhotoInterior, (f) => _carPhotoInterior = f),
      ],
    );
  }

  Widget _buildStep5() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: AppColors.primaryNavy,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: AppColors.accent.withOpacity(0.5)),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text('${BrandConfig.appName} Standards', style: GoogleFonts.outfit(color: AppColors.accent, fontWeight: FontWeight.bold, fontSize: 18)),
              const SizedBox(height: 8),
              const Text(
                '1. Zero Tolerance for Discrimination\n2. Maintain a clean and safe vehicle\n3. Follow all local traffic laws\n4. Professional Customer Service is mandatory',
                style: TextStyle(color: Colors.white70, height: 1.5),
              ),
            ],
          ),
        ),
        const SizedBox(height: 16),
        CheckboxListTile(
          title: const Text('I have completed the virtual app tutorial.', style: TextStyle(color: Colors.white)),
          value: _completedTutorial,
          activeColor: AppColors.accent,
          checkColor: Colors.black,
          onChanged: (val) => setState(() => _completedTutorial = val ?? false),
        ),
        CheckboxListTile(
          title: const Text('I agree to the Safety Guidelines and Policies.', style: TextStyle(color: Colors.white)),
          value: _agreedToSafety,
          activeColor: AppColors.accent,
          checkColor: Colors.black,
          onChanged: (val) => setState(() => _agreedToSafety = val ?? false),
        ),
      ],
    );
  }

  Widget _buildTextField(TextEditingController controller, String label, {TextInputType? keyboardType}) {
    return TextFormField(
      controller: controller,
      keyboardType: keyboardType,
      style: const TextStyle(color: Colors.white, fontSize: 16),
      decoration: InputDecoration(
        labelText: label,
        labelStyle: const TextStyle(color: Colors.white54),
        filled: true,
        fillColor: Colors.white10,
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
        enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
        focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppColors.accent)),
      ),
      validator: (val) {
        if (label.contains('Optional')) return null;
        if (val == null || val.trim().isEmpty) return 'Required';
        return null;
      },
    );
  }

  Widget _uploadBox(String label, XFile? file, Function(XFile?) onPicked) {
    return InkWell(
      onTap: () => _pickImage(onPicked),
      child: Container(
        height: 120,
        width: double.infinity,
        decoration: BoxDecoration(
          border: Border.all(color: file != null ? AppColors.accent : Colors.white24),
          borderRadius: BorderRadius.circular(12),
          color: file != null ? AppColors.accent.withOpacity(0.1) : Colors.white10,
        ),
        child: file != null
          ? ClipRRect(
              borderRadius: BorderRadius.circular(12),
              child: Stack(
                fit: StackFit.expand,
                children: [
                  kIsWeb 
                    ? Image.network(file.path, fit: BoxFit.cover,
                        errorBuilder: (c, e, s) => const Icon(Icons.broken_image, color: Colors.white54, size: 40))
                    : Image.file(File(file.path), fit: BoxFit.cover),
                  Positioned(
                    top: 4, right: 4,
                    child: Container(
                      padding: const EdgeInsets.all(4),
                      decoration: const BoxDecoration(color: Colors.green, shape: BoxShape.circle),
                      child: const Icon(Icons.check, color: Colors.white, size: 16),
                    ),
                  ),
                ],
              ),
            )
          : Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Icon(Icons.cloud_upload_outlined, size: 32, color: AppColors.accent),
                const SizedBox(height: 8),
                Text(label, style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.white70), textAlign: TextAlign.center),
              ],
            ),
      ),
    );
  }

  Widget _buildPendingUI() {
    return Scaffold(
      backgroundColor: AppColors.obsidianDark,
      body: Center(
        child: Padding(
          padding: const EdgeInsets.all(40.0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Container(
                padding: const EdgeInsets.all(24),
                decoration: BoxDecoration(
                  color: AppColors.accent.withOpacity(0.1),
                  shape: BoxShape.circle,
                  border: Border.all(color: AppColors.accent.withOpacity(0.3)),
                ),
                child: const Icon(Icons.hourglass_top_rounded, size: 64, color: AppColors.accent),
              ),
              const SizedBox(height: 32),
              Text('Application Submitted!',
                style: GoogleFonts.outfit(fontSize: 28, fontWeight: FontWeight.bold, color: Colors.white)),
              const SizedBox(height: 16),
              const Text(
                'Our admin team is currently reviewing your documents and background check.\n\nYou will be notified once your account is activated and ready to drive.',
                textAlign: TextAlign.center,
                style: TextStyle(color: Colors.white70, height: 1.6, fontSize: 15),
              ),
              const SizedBox(height: 40),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton.icon(
                  onPressed: () => ref.read(kycProvider.notifier).checkStatus(),
                  icon: const Icon(Icons.refresh),
                  label: Text('Refresh Status', style: GoogleFonts.outfit(fontWeight: FontWeight.bold, fontSize: 16)),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppColors.accent,
                    foregroundColor: Colors.black,
                    padding: const EdgeInsets.symmetric(vertical: 16),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
