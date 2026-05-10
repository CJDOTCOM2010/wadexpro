import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/network/providers.dart';
import '../../../../core/models/splash_config.dart';
import '../../../../core/config/app_config.dart';
import '../../../../core/config/environment_config.dart';
import '../../../../core/widgets/platform_media_widget.dart';

class SplashScreen extends ConsumerStatefulWidget {
  final Widget Function() destinationBuilder;

  const SplashScreen({super.key, required this.destinationBuilder});

  @override
  ConsumerState<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends ConsumerState<SplashScreen>
    with TickerProviderStateMixin {
  late AnimationController _logoController;
  late AnimationController _rippleController;
  late AnimationController _textController;
  late AnimationController _taglineController;

  late Animation<double> _logoScale;
  late Animation<double> _logoOpacity;
  late Animation<double> _rippleScale;
  late Animation<double> _rippleOpacity;
  late Animation<Offset> _textSlide;
  late Animation<double> _textOpacity;
  late Animation<double> _taglineOpacity;
  late Animation<Offset> _taglineSlide;

  SplashConfig? _config;
  bool _isLoadingConfig = true;

  @override
  void initState() {
    super.initState();

    SystemChrome.setSystemUIOverlayStyle(
      const SystemUiOverlayStyle(
        statusBarColor: Colors.transparent,
        statusBarIconBrightness: Brightness.light,
      ),
    );

    // --- Logo Animation ---
    _logoController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1200),
    );
    _logoScale = Tween<double>(begin: 0.0, end: 1.0).animate(
      CurvedAnimation(parent: _logoController, curve: Curves.elasticOut),
    );
    _logoOpacity = Tween<double>(begin: 0.0, end: 1.0).animate(
      CurvedAnimation(parent: _logoController, curve: const Interval(0.0, 0.5, curve: Curves.easeIn)),
    );

    // --- Ripple ---
    _rippleController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 2000),
    )..repeat();
    _rippleScale = Tween<double>(begin: 0.5, end: 2.5).animate(
      CurvedAnimation(parent: _rippleController, curve: Curves.easeOut),
    );
    _rippleOpacity = Tween<double>(begin: 0.6, end: 0.0).animate(
      CurvedAnimation(parent: _rippleController, curve: Curves.easeOut),
    );

    // --- Text ---
    _textController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 800),
    );
    _textSlide = Tween<Offset>(begin: const Offset(0, 0.5), end: Offset.zero).animate(
      CurvedAnimation(parent: _textController, curve: Curves.easeOutCubic),
    );
    _textOpacity = Tween<double>(begin: 0.0, end: 1.0).animate(
      CurvedAnimation(parent: _textController, curve: Curves.easeIn),
    );

    // --- Tagline ---
    _taglineController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 600),
    );
    _taglineOpacity = Tween<double>(begin: 0.0, end: 1.0).animate(_taglineController);
    _taglineSlide = Tween<Offset>(begin: const Offset(0, 0.3), end: Offset.zero).animate(
      CurvedAnimation(parent: _taglineController, curve: Curves.easeOutCubic),
    );

    // Fetch config and start sequence
    _loadConfigAndStart();
  }

  Future<void> _loadConfigAndStart() async {
    try {
      final apiClient = ref.read(apiClientProvider);
      final baseUrl = EnvironmentConfig.baseUrl;
      final response = await apiClient.instance.get('/platform/splash/driver').timeout(const Duration(seconds: 3));
      
      if (mounted) {
        setState(() {
          _config = SplashConfig.fromJson(response.data, baseUrl);
          _isLoadingConfig = false;
        });
      }
    } catch (e) {
      debugPrint('Error loading splash config: $e');
      if (mounted) {
        setState(() {
          _isLoadingConfig = false;
        });
      }
    }

    _startSequence();
  }

  void _startSequence() async {
    await Future.delayed(const Duration(milliseconds: 300));
    _logoController.forward();

    await Future.delayed(const Duration(milliseconds: 800));
    _textController.forward();

    await Future.delayed(const Duration(milliseconds: 400));
    _taglineController.forward();

    // Use dynamic duration if available, else default to 3s
    final duration = _config?.durationMs ?? 3000;
    await Future.delayed(Duration(milliseconds: duration - 1500));
    
    if (mounted) {
      Navigator.of(context).pushReplacement(
        PageRouteBuilder(
          pageBuilder: (_, _, _) => widget.destinationBuilder(),
          transitionsBuilder: (_, animation, _, child) {
            return FadeTransition(opacity: animation, child: child);
          },
          transitionDuration: const Duration(milliseconds: 800),
        ),
      );
    }
  }

  @override
  void dispose() {
    _logoController.dispose();
    _rippleController.dispose();
    _textController.dispose();
    _taglineController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    // Dynamic styles from config
    final backgroundColor = _config?.backgroundColor ?? const Color(0xFF000814);
    final accentColor = _config?.accentColor ?? AppColors.accent;
    final showRipple = _config?.showRipple ?? true;
    final showLogo = _config?.showLogo ?? true;
    final showBackground = _config?.showBackground ?? true;
    final tagline = _config?.tagline ?? 'Drive. Earn. Grow.';

    return Scaffold(
      body: Stack(
        children: [
          // 1. Background Layer (Media or Gradient)
          Positioned.fill(
            child: Container(
              color: backgroundColor,
              child: (showBackground && _config?.backgroundUrl != null)
                  ? Stack(
                      children: [
                        PlatformMediaWidget(
                          url: _config!.backgroundUrl!,
                          mediaType: _config!.backgroundMediaType,
                          fit: BoxFit.cover,
                        ),
                        // Darken overlay
                        Container(
                          color: backgroundColor.withValues(alpha: 0.7),
                        ),
                      ],
                    )
                  : Container(
                      decoration: BoxDecoration(
                        gradient: LinearGradient(
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                          colors: [
                            backgroundColor.withValues(alpha: 0.8),
                            backgroundColor,
                            backgroundColor.withValues(alpha: 0.9),
                          ],
                          stops: const [0.0, 0.5, 1.0],
                        ),
                      ),
                    ),
            ),
          ),

          // 2. Decorative elements (Only if no background media)
          if (showBackground && _config?.backgroundUrl == null) ...[
            Positioned(
              top: -100,
              right: -80,
              child: Container(
                width: 300,
                height: 300,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: accentColor.withValues(alpha: 0.05),
                ),
              ),
            ),
            Positioned(
              bottom: -120,
              left: -100,
              child: Container(
                width: 400,
                height: 400,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: accentColor.withValues(alpha: 0.03),
                ),
              ),
            ),
          ],

          // 3. Ripple effect behind logo
          if (showRipple && showLogo)
            Center(
              child: AnimatedBuilder(
                animation: _rippleController,
                builder: (_, _) => Transform.scale(
                  scale: _rippleScale.value,
                  child: Container(
                    width: 120,
                    height: 120,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      border: Border.all(
                        color: accentColor.withValues(alpha: _rippleOpacity.value),
                        width: 2,
                      ),
                    ),
                  ),
                ),
              ),
            ),

          // 4. Center content (Logo & Text)
          Center(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                // Logo Shield
                if (showLogo)
                  AnimatedBuilder(
                    animation: _logoController,
                    builder: (_, _) => Opacity(
                      opacity: _logoOpacity.value,
                      child: Transform.scale(
                        scale: _logoScale.value,
                        child: Container(
                          width: 120,
                          height: 120,
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            color: _config?.logoUrl != null ? Colors.white : null,
                            gradient: _config?.logoUrl == null ? LinearGradient(
                              begin: Alignment.topLeft,
                              end: Alignment.bottomRight,
                              colors: [
                                accentColor,
                                accentColor.withValues(alpha: 0.8),
                              ],
                            ) : null,
                            boxShadow: [
                              BoxShadow(
                                color: accentColor.withValues(alpha: 0.4),
                                blurRadius: 40,
                                spreadRadius: 4,
                              ),
                            ],
                          ),
                          clipBehavior: Clip.antiAlias,
                          child: Center(
                            child: _config?.logoUrl != null 
                                ? PlatformMediaWidget(
                                    url: _config!.logoUrl!,
                                    mediaType: _config!.logoMediaType,
                                    fit: BoxFit.contain,
                                    width: 80,
                                    height: 80,
                                  )
                                : const Icon(
                                    Icons.local_shipping_rounded,
                                    color: Colors.white,
                                    size: 50,
                                  ),
                          ),
                        ),
                      ),
                    ),
                  ),

                const SizedBox(height: 32),

                // Brand Text
                SlideTransition(
                  position: _textSlide,
                  child: FadeTransition(
                    opacity: _textOpacity,
                    child: const Text(
                      'WADEXPRO',
                      style: TextStyle(
                        fontSize: 36,
                        fontWeight: FontWeight.w900,
                        color: Colors.white,
                        letterSpacing: 6,
                      ),
                    ),
                  ),
                ),

                const SizedBox(height: 12),

                // Tagline
                SlideTransition(
                  position: _taglineSlide,
                  child: FadeTransition(
                    opacity: _taglineOpacity,
                    child: Text(
                      tagline,
                      textAlign: TextAlign.center,
                      style: TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.w300,
                        color: Colors.white.withValues(alpha: 0.7),
                        letterSpacing: 2,
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),

          // 5. Bottom version
          Positioned(
            bottom: 40,
            left: 0,
            right: 0,
            child: Center(
              child: FadeTransition(
                opacity: _taglineOpacity,
                child: Consumer(
                  builder: (context, ref, _) {
                    return Text(
                      'v${AppConfig.currentAppVersion ?? '1.0.0'}',
                      style: TextStyle(
                        color: Colors.white.withValues(alpha: 0.3),
                        fontSize: 12,
                      ),
                    );
                  }
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
