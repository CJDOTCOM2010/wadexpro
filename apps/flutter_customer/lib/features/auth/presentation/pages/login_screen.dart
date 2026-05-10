import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:package_info_plus/package_info_plus.dart';
import '../../../../core/theme/app_colors.dart';
import '../providers/auth_provider.dart';
import '../providers/auth_state.dart';
import 'otp_screen.dart';

class LoginScreen extends ConsumerStatefulWidget {
  const LoginScreen({super.key});

  @override
  ConsumerState<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends ConsumerState<LoginScreen> {
  final TextEditingController _phoneController = TextEditingController();
  final _formKey = GlobalKey<FormState>();

  @override
  void dispose() {
    _phoneController.dispose();
    super.dispose();
  }

  void _handleLogin() {
    if (_formKey.currentState!.validate()) {
      final identifier = _phoneController.text.trim();
      ref.read(authProvider.notifier).requestOtp(identifier);
    }
  }

  @override
  Widget build(BuildContext context) {
    final authState = ref.watch(authProvider);

    // Navigation and Error Listeners
    ref.listen(authProvider, (previous, next) {
      if (next.status == AuthStatus.codeSent) {
        Navigator.of(context).push(
          MaterialPageRoute(
            builder: (_) => OtpScreen(identifier: next.identifier!),
          ),
        );
      } else if (next.status == AuthStatus.error) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(next.errorMessage ?? 'Authentication failed'),
            backgroundColor: AppColors.error,
          ),
        );
      }
    });

    return Scaffold(
      backgroundColor: AppColors.primaryNavy,
      body: Stack(
        children: [
          // Background Branding Elements
          Positioned(
            top: -100,
            right: -50,
            child: Container(
              width: 300,
              height: 300,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: AppColors.secondaryGold.withValues(alpha: 0.05),
              ),
            ),
          ),
          
          SafeArea(
            child: SingleChildScrollView(
              padding: const EdgeInsets.symmetric(horizontal: 24.0, vertical: 40.0),
              child: Form(
                key: _formKey,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const SizedBox(height: 40),
                    
                    // Brand Header
                    Text(
                      'WADEXPRO',
                      style: GoogleFonts.outfit(
                        fontSize: 42,
                        fontWeight: FontWeight.w900,
                        color: Colors.white,
                        letterSpacing: -1,
                      ),
                    ),
                    Text(
                      'RIDE THE FUTURE.',
                      style: GoogleFonts.outfit(
                        fontSize: 16,
                        fontWeight: FontWeight.w300,
                        color: AppColors.secondaryGold,
                        letterSpacing: 4,
                      ),
                    ),
                    
                    const SizedBox(height: 60),
                    
                    Text(
                      'Sign In',
                      style: GoogleFonts.outfit(
                        fontSize: 28,
                        fontWeight: FontWeight.bold,
                        color: Colors.white,
                      ),
                    ),
                    const SizedBox(height: 12),
                    Text(
                      'Enter your phone number to continue your premium journey.',
                      style: GoogleFonts.outfit(
                        fontSize: 15,
                        color: Colors.white70,
                      ),
                    ),
                    
                    const SizedBox(height: 40),
                    
                    // Modern Input Field
                    TextFormField(
                      controller: _phoneController,
                      keyboardType: TextInputType.phone,
                      style: GoogleFonts.outfit(color: Colors.white, fontSize: 18, fontWeight: FontWeight.w600),
                      decoration: InputDecoration(
                        hintText: 'Phone Number',
                        hintStyle: GoogleFonts.outfit(color: Colors.white38, fontSize: 16),
                        filled: true,
                        fillColor: Colors.white.withOpacity(0.05),
                        prefixIcon: const Icon(Icons.phone_iphone_rounded, color: AppColors.secondaryGold, size: 22),
                        prefixText: '+233 ',
                        prefixStyle: GoogleFonts.outfit(color: AppColors.secondaryGold, fontWeight: FontWeight.w900, fontSize: 18),
                        contentPadding: const EdgeInsets.symmetric(horizontal: 24, vertical: 20),
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(20),
                          borderSide: const BorderSide(color: Colors.white12),
                        ),
                        enabledBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(20),
                          borderSide: const BorderSide(color: Colors.white12),
                        ),
                        focusedBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(20),
                          borderSide: const BorderSide(color: AppColors.secondaryGold, width: 2),
                        ),
                        errorBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(20),
                          borderSide: const BorderSide(color: AppColors.error),
                        ),
                      ),
                      validator: (value) {
                        if (value == null || value.isEmpty) return 'Please enter your phone number';
                        if (value.length < 10) return 'Enter a valid phone number';
                        return null;
                      },
                    ),
                    
                    const SizedBox(height: 32),
                    
                    // Main Action Button
                    Container(
                      width: double.infinity,
                      height: 64,
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(20),
                        boxShadow: [
                          BoxShadow(
                            color: AppColors.secondaryGold.withOpacity(0.3),
                            blurRadius: 20,
                            offset: const Offset(0, 10),
                          ),
                        ],
                      ),
                      child: ElevatedButton(
                        onPressed: authState.status == AuthStatus.loading ? null : _handleLogin,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: AppColors.secondaryGold,
                          foregroundColor: AppColors.primaryNavy,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                          elevation: 0,
                        ),
                        child: authState.status == AuthStatus.loading
                          ? const SizedBox(
                              height: 24,
                              width: 24,
                              child: CircularProgressIndicator(color: AppColors.primaryNavy, strokeWidth: 3),
                            )
                          : Text(
                              'CONTINUE',
                              style: GoogleFonts.outfit(
                                fontSize: 16, 
                                fontWeight: FontWeight.w900, 
                                letterSpacing: 2,
                              ),
                            ),
                      ),
                    ),
                    
                    const SizedBox(height: 40),
                    
                    // Social Dividers
                    Row(
                      children: [
                        const Expanded(child: Divider(color: Colors.white10)),
                        Padding(
                          padding: const EdgeInsets.symmetric(horizontal: 16),
                          child: Text('OR CONNECT WITH', style: GoogleFonts.outfit(fontSize: 11, color: Colors.white24, letterSpacing: 1)),
                        ),
                        const Expanded(child: Divider(color: Colors.white10)),
                      ],
                    ),
                    
                    const SizedBox(height: 40),
                    
                    // Social Buttons
                    Row(
                      children: [
                        Expanded(
                          child: _SocialButton(
                            label: 'Google',
                            icon: 'https://cdn1.iconfinder.com/data/icons/google_j_icons_1/512/Google_G_Logo-512.png',
                            onTap: () => ref.read(authProvider.notifier).signInWithGoogle(),
                          ),
                        ),
                        const SizedBox(width: 16),
                        Expanded(
                          child: _SocialButton(
                            label: 'Apple',
                            icon: 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fa/Apple_logo_black.svg/505px-Apple_logo_black.svg.png',
                            onTap: () => ref.read(authProvider.notifier).signInWithFacebook(), // Mapping to Facebook logic for now or update provider
                          ),
                        ),
                      ],
                    ),
                    
                    const SizedBox(height: 60),
                    
                    // Footer
                    Center(
                      child: Text(
                        'By continuing, you agree to our Terms and Privacy Policy.',
                        textAlign: TextAlign.center,
                        style: GoogleFonts.outfit(fontSize: 12, color: Colors.white24),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
          
          // Version info
          Positioned(
            bottom: 20,
            left: 0,
            right: 0,
            child: Center(
              child: FutureBuilder<PackageInfo>(
                future: PackageInfo.fromPlatform(),
                builder: (context, snapshot) {
                  return Text(
                    'VERSION ${snapshot.data?.version ?? '1.2.0'}',
                    style: GoogleFonts.outfit(fontSize: 10, color: Colors.white12, letterSpacing: 2),
                  );
                },
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _SocialButton extends StatelessWidget {
  final String icon;
  final String label;
  final VoidCallback onTap;
  const _SocialButton({required this.icon, required this.label, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(20),
        child: Container(
          height: 64,
          decoration: BoxDecoration(
            color: Colors.white.withOpacity(0.05),
            borderRadius: BorderRadius.circular(20),
            border: Border.all(color: Colors.white12),
          ),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Image.network(
                icon, 
                height: 24,
                color: label == 'Apple' ? Colors.white : null,
                errorBuilder: (context, error, stackTrace) => Icon(
                  label == 'Apple' ? Icons.apple : Icons.g_mobiledata,
                  color: Colors.white,
                  size: 32,
                ),
              ),
              const SizedBox(width: 12),
              Text(
                label,
                style: GoogleFonts.outfit(
                  color: Colors.white,
                  fontSize: 14,
                  fontWeight: FontWeight.w600,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
