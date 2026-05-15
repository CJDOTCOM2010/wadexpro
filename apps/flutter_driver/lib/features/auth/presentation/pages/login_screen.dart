import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:google_fonts/google_fonts.dart';
import '../providers/auth_provider.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/config/app_config.dart';
import '../../../../core/config/brand_config.dart';
import 'otp_screen.dart';

class LoginScreen extends ConsumerStatefulWidget {
  const LoginScreen({super.key});

  @override
  ConsumerState<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends ConsumerState<LoginScreen> with SingleTickerProviderStateMixin {
  final TextEditingController _phoneController = TextEditingController();
  final _formKey = GlobalKey<FormState>();
  int _selectedTabIndex = 0; 
  late AnimationController _fadeController;
  late Animation<double> _fadeAnimation;

  @override
  void initState() {
    super.initState();
    _fadeController = AnimationController(vsync: this, duration: const Duration(milliseconds: 400));
    _fadeAnimation = Tween<double>(begin: 0.0, end: 1.0).animate(CurvedAnimation(parent: _fadeController, curve: Curves.easeOut));
    _fadeController.forward();
  }

  @override
  void dispose() {
    _phoneController.dispose();
    _fadeController.dispose();
    super.dispose();
  }

  void _handleAuthAction() {
    if (_formKey.currentState!.validate()) {
      FocusScope.of(context).unfocus();
      final phone = _phoneController.text.trim();
      ref.read(authProvider.notifier).login(phone);
    }
  }

  @override
  Widget build(BuildContext context) {
    final authState = ref.watch(authProvider);

    ref.listen(authProvider, (previous, next) {
      if (next.status == AuthStatus.otpSent) {
        Navigator.of(context).push(MaterialPageRoute(builder: (_) => const OtpScreen()));
      } else if (next.status == AuthStatus.error) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(next.errorMessage ?? 'Authentication failed'),
            backgroundColor: AppColors.error,
            behavior: SnackBarBehavior.floating,
          ),
        );
      }
    });

    return Scaffold(
      backgroundColor: const Color(0xFF000000), // Solid Black for max contrast
      body: Stack(
        children: [
          // Background accents (Moved behind everything)
          Positioned(
            top: -50,
            right: -50,
            child: Container(
              width: 250,
              height: 250,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: AppColors.primary.withOpacity(0.2),
              ),
            ),
          ),
          
          SafeArea(
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(24.0),
              child: Form(
                key: _formKey,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const SizedBox(height: 20),
                    // Logo Header
                    Row(
                      children: [
                        Container(
                          width: 50,
                          height: 50,
                          decoration: BoxDecoration(
                            color: AppColors.primary,
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: const Icon(Icons.local_shipping, color: Colors.white),
                        ),
                        const SizedBox(width: 15),
                        Text(
                          BrandConfig.appName,
                          style: GoogleFonts.outfit(
                            fontSize: 24,
                            fontWeight: FontWeight.bold,
                            color: Colors.white,
                            letterSpacing: 2,
                          ),
                        ),
                      ],
                    ),
                    
                    const SizedBox(height: 40),
                    
                    // Switcher
                    Container(
                      padding: const EdgeInsets.all(4),
                      decoration: BoxDecoration(
                        color: Colors.white12,
                        borderRadius: BorderRadius.circular(16),
                      ),
                      child: Row(
                        children: [
                          _buildTabButton('Sign In', 0),
                          _buildTabButton('Apply', 1),
                        ],
                      ),
                    ),
                    
                    const SizedBox(height: 40),
                    
                    Text(
                      _selectedTabIndex == 0 ? 'Welcome Back' : 'Join the Team',
                      style: GoogleFonts.outfit(fontSize: 32, fontWeight: FontWeight.bold, color: Colors.white),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      'Enter your phone number to proceed.',
                      style: GoogleFonts.outfit(fontSize: 16, color: Colors.white70),
                    ),
                    
                    const SizedBox(height: 40),
                    
                    // HIGH CONTRAST INPUT FIELD
                    Text(
                      'PHONE NUMBER',
                      style: GoogleFonts.outfit(fontSize: 12, fontWeight: FontWeight.bold, color: AppColors.eliteGold, letterSpacing: 1.5),
                    ),
                    const SizedBox(height: 10),
                    Container(
                      decoration: BoxDecoration(
                        color: Colors.white, // SOLID WHITE BOX FOR 100% VISIBILITY
                        borderRadius: BorderRadius.circular(16),
                        border: Border.all(color: AppColors.accent, width: 2),
                      ),
                      child: TextFormField(
                        controller: _phoneController,
                        keyboardType: TextInputType.phone,
                        autofocus: true,
                        style: const TextStyle(
                          color: Colors.black, // BLACK TEXT
                          fontSize: 22, 
                          fontWeight: FontWeight.w900,
                        ),
                        decoration: const InputDecoration(
                          filled: false,
                          hintText: '000 000 0000',
                          hintStyle: TextStyle(color: Colors.black26),
                          prefixIcon: Icon(Icons.phone, color: AppColors.primary),
                          prefixText: '+233 ',
                          prefixStyle: TextStyle(color: AppColors.primary, fontWeight: FontWeight.w900, fontSize: 22),
                          contentPadding: EdgeInsets.symmetric(horizontal: 20, vertical: 20),
                          border: InputBorder.none,
                          enabledBorder: InputBorder.none,
                          focusedBorder: InputBorder.none,
                        ),
                        validator: (v) => (v == null || v.isEmpty) ? 'Required' : null,
                      ),
                    ),
                    
                    const SizedBox(height: 30),
                    
                    // BRIGHT ACTION BUTTON
                    SizedBox(
                      width: double.infinity,
                      height: 60,
                      child: ElevatedButton(
                        onPressed: authState.status == AuthStatus.loading ? null : _handleAuthAction,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: AppColors.primary,
                          foregroundColor: Colors.white,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                          elevation: 8,
                        ),
                        child: authState.status == AuthStatus.loading
                            ? const CircularProgressIndicator(color: Colors.white)
                            : Text(
                                _selectedTabIndex == 0 ? 'LOGIN' : 'START APPLICATION',
                                style: GoogleFonts.outfit(fontSize: 18, fontWeight: FontWeight.w900),
                              ),
                      ),
                    ),
                    
                    const SizedBox(height: 40),
                    Center(
                      child: Text(
                        '${BrandConfig.appName} v${AppConfig.currentAppVersion}',
                        style: const TextStyle(color: Colors.white24, fontSize: 12),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildTabButton(String label, int index) {
    bool selected = _selectedTabIndex == index;
    return Expanded(
      child: GestureDetector(
        onTap: () => setState(() => _selectedTabIndex = index),
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 12),
          decoration: BoxDecoration(
            color: selected ? AppColors.primary : Colors.transparent,
            borderRadius: BorderRadius.circular(12),
          ),
          child: Center(
            child: Text(
              label,
              style: GoogleFonts.outfit(
                color: selected ? Colors.white : Colors.white60,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
        ),
      ),
    );
  }
}
