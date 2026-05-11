import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/models/onboarding_config.dart';
import '../../../../core/widgets/platform_media_widget.dart';
import '../../../../core/config/brand_config.dart';
import '../providers/onboarding_provider.dart';

/// Stores whether first launch has occurred.
final hasSeenOnboardingProvider = StateProvider<bool>((ref) => false);

class OnboardingScreen extends ConsumerStatefulWidget {
  final VoidCallback onComplete;

  const OnboardingScreen({super.key, required this.onComplete});

  @override
  ConsumerState<OnboardingScreen> createState() => _OnboardingScreenState();
}

class _OnboardingScreenState extends ConsumerState<OnboardingScreen> {
  final PageController _pageController = PageController();
  int _currentPage = 0;

  void _nextPage(int totalPages) {
    if (_currentPage < totalPages - 1) {
      _pageController.nextPage(
        duration: const Duration(milliseconds: 500),
        curve: Curves.easeInOutCubic,
      );
    } else {
      _completeOnboarding();
    }
  }

  void _prevPage() {
    if (_currentPage > 0) {
      _pageController.previousPage(
        duration: const Duration(milliseconds: 500),
        curve: Curves.easeInOutCubic,
      );
    }
  }

  void _completeOnboarding() {
    ref.read(hasSeenOnboardingProvider.notifier).state = true;
    widget.onComplete();
  }

  @override
  Widget build(BuildContext context) {
    final configAsync = ref.watch(onboardingConfigProvider);

    SystemChrome.setSystemUIOverlayStyle(
      const SystemUiOverlayStyle(
        statusBarColor: Colors.transparent,
        statusBarIconBrightness: Brightness.light,
      ),
    );

    return Scaffold(
      backgroundColor: Colors.black,
      body: configAsync.when(
        loading: () => const Center(
          child: CircularProgressIndicator(color: Color(0xFFFFCC00)),
        ),
        error: (_, _) => _buildFallbackError(),
        data: (config) {
          if (config.pages.isEmpty) {
            // WADEX-Guard: Auto-complete if no slides found
            WidgetsBinding.instance.addPostFrameCallback((_) => _completeOnboarding());
            return const Center(child: CircularProgressIndicator(color: Color(0xFFFFCC00)));
          }
          return _buildOnboarding(config);
        },
      ),
    );
  }

  Widget _buildFallbackError() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          const Icon(Icons.error_outline, color: Colors.white54, size: 48),
          const SizedBox(height: 16),
          const Text('Could not load onboarding',
              style: TextStyle(color: Colors.white54, fontSize: 16)),
          const SizedBox(height: 24),
          ElevatedButton(
            onPressed: _completeOnboarding,
            style: ElevatedButton.styleFrom(
              backgroundColor: const Color(0xFFFFCC00),
              foregroundColor: AppColors.primaryNavy,
            ),
            child: const Text('Continue to App'),
          ),
        ],
      ),
    );
  }

  Widget _buildOnboarding(OnboardingConfig config) {
    final pageConfig = config.pages[_currentPage];
    
    return Stack(
      children: [
        // Slide Content
        PageView.builder(
          controller: _pageController,
          itemCount: config.pages.length,
          onPageChanged: (i) => setState(() => _currentPage = i),
          itemBuilder: (context, index) {
            return _OnboardingSlideTemplate(
              data: config.pages[index],
              onAction: () => _nextPage(config.pages.length),
            );
          },
        ),

        // Global Overlays (Skip, Logo, Indicators)
        _buildGlobalOverlays(config),
      ],
    );
  }

  Widget _buildGlobalOverlays(OnboardingConfig config) {
    final pageConfig = config.pages[_currentPage];
    final isDarkLayout = ['dark_premium', 'full_bleed', 'floating_card', 'glassmorphic', 'industrial'].contains(pageConfig.layoutStyle);
    final textColor = pageConfig.textColor;
    final accentColor = pageConfig.buttonColor;

    return Stack(
      children: [
        // Top Logo
        Positioned(
          top: MediaQuery.of(context).padding.top + 16,
          left: 0,
          right: 0,
          child: Center(
            child: Text.rich(
              TextSpan(children: [
                TextSpan(
                  text: BrandConfig.shortName,
                  style: TextStyle(
                    color: textColor,
                    fontSize: 22,
                    fontWeight: FontWeight.w900,
                    letterSpacing: -0.5,
                  ),
                ),
                if (BrandConfig.appName.length > BrandConfig.shortName.length)
                  TextSpan(
                    text: BrandConfig.appName.substring(BrandConfig.shortName.length),
                    style: TextStyle(
                      color: accentColor,
                      fontSize: 22,
                      fontWeight: FontWeight.w900,
                      letterSpacing: -0.5,
                    ),
                  ),
              ]),
            ),
          ),
        ),

        // Skip button
        Positioned(
          top: MediaQuery.of(context).padding.top + 16,
          right: 24,
          child: AnimatedOpacity(
            opacity: _currentPage < config.pages.length - 1 ? 1.0 : 0.0,
            duration: const Duration(milliseconds: 300),
            child: GestureDetector(
              onTap: _completeOnboarding,
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                decoration: BoxDecoration(
                  color: textColor.withValues(alpha: 0.15),
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Text(
                  config.skipText,
                  style: TextStyle(
                    color: textColor.withValues(alpha: 0.8),
                    fontSize: 13,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ),
            ),
          ),
        ),

        // Bottom Nav Controls
        Positioned(
          bottom: MediaQuery.of(context).padding.bottom + 32,
          left: 32,
          right: 32,
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              // Page indicators
              Row(
                children: List.generate(
                  config.pages.length,
                  (i) => AnimatedContainer(
                    duration: const Duration(milliseconds: 400),
                    curve: Curves.easeInOut,
                    margin: const EdgeInsets.symmetric(horizontal: 3),
                    width: _currentPage == i ? 28 : 8,
                    height: 8,
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(4),
                      color: _currentPage == i
                          ? accentColor
                          : textColor.withValues(alpha: 0.25),
                    ),
                  ),
                ),
              ),

              // Nav arrows
              if (pageConfig.buttonType == 'bottom_arrow')
                Row(
                  children: [
                  GestureDetector(
                    onTap: _prevPage,
                    child: Container(
                      width: 48,
                      height: 48,
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        border: Border.all(
                          color: textColor.withValues(alpha: _currentPage > 0 ? 0.3 : 0.1),
                          width: 2,
                        ),
                      ),
                      child: Icon(
                        Icons.chevron_left_rounded,
                        color: textColor.withValues(alpha: _currentPage > 0 ? 0.7 : 0.2),
                        size: 24,
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  GestureDetector(
                    onTap: () => _nextPage(config.pages.length),
                    child: Container(
                      width: 48,
                      height: 48,
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        color: accentColor.withValues(alpha: 0.15),
                        border: Border.all(
                          color: accentColor,
                          width: 2,
                        ),
                      ),
                      child: Icon(
                        _currentPage < config.pages.length - 1
                            ? Icons.chevron_right_rounded
                            : Icons.check_rounded,
                        color: accentColor,
                        size: 24,
                      ),
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ],
    );
  }
}

// --- Slide Template Factory ---
class _OnboardingSlideTemplate extends StatelessWidget {
  final OnboardingPageConfig data;
  final VoidCallback onAction;
  const _OnboardingSlideTemplate({required this.data, required this.onAction});

  @override
  Widget build(BuildContext context) {
    switch (data.layoutStyle) {
      case 'top_image': return _TopImageTemplate(data: data, onAction: onAction);
      case 'bottom_image': return _BottomImageTemplate(data: data, onAction: onAction);
      case 'floating_card': return _FloatingCardTemplate(data: data, onAction: onAction);
      case 'centered_mini': return _CenteredMiniTemplate(data: data, onAction: onAction);
      case 'dark_premium': return _DarkPremiumTemplate(data: data, onAction: onAction);
      case 'glassmorphic': return _GlassmorphicTemplate(data: data, onAction: onAction);
      case 'industrial': return _IndustrialTemplate(data: data, onAction: onAction);
      case 'side_by_side': return _SideBySideTemplate(data: data, onAction: onAction);
      case 'clean_vector': return _CleanVectorTemplate(data: data, onAction: onAction);
      case 'full_bleed':
      default:
        return _FullBleedTemplate(data: data, onAction: onAction);
    }
  }
}

// Helper for Background Media (supports Image, GIF, Video)
class _SlideBgImage extends StatelessWidget {
  final OnboardingPageConfig data;
  final BoxFit fit;
  const _SlideBgImage({required this.data, this.fit = BoxFit.cover});

  @override
  Widget build(BuildContext context) {
    if (data.imagePath.isEmpty) {
      return _buildPlaceholder();
    }

    return PlatformMediaWidget(
      url: data.imagePath,
      mediaType: data.mediaType,
      fit: fit,
      placeholderColor: data.backgroundColor,
      errorWidget: _buildPlaceholder(),
    );
  }

  Widget _buildPlaceholder() {
    return Container(
      width: double.infinity,
      height: double.infinity,
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            data.backgroundColor,
            data.buttonColor.withValues(alpha: 0.15),
            data.backgroundColor,
          ],
        ),
      ),
      child: Center(
        child: Icon(
          Icons.image_outlined,
          size: 80,
          color: data.textColor.withValues(alpha: 0.15),
        ),
      ),
    );
  }
}

// --- 1. FULL BLEED ---
class _FullBleedTemplate extends StatelessWidget {
  final OnboardingPageConfig data;
  final VoidCallback onAction;
  const _FullBleedTemplate({required this.data, required this.onAction});

  @override
  Widget build(BuildContext context) {
    return Stack(
      children: [
        _SlideBgImage(data: data),
        Container(
          decoration: BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topCenter,
              end: Alignment.bottomCenter,
              colors: [Colors.transparent, data.backgroundColor.withValues(alpha: 0.8), data.backgroundColor],
            ),
          ),
        ),
        _ContentBlock(data: data, align: Alignment.bottomLeft, textColor: data.textColor, onAction: onAction),
      ],
    );
  }
}

// --- 2. TOP IMAGE ---
class _TopImageTemplate extends StatelessWidget {
  final OnboardingPageConfig data;
  final VoidCallback onAction;
  const _TopImageTemplate({required this.data, required this.onAction});

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Expanded(
          flex: 6,
          child: ClipRRect(
            borderRadius: const BorderRadius.only(bottomLeft: Radius.circular(60)),
            child: _SlideBgImage(data: data),
          ),
        ),
        Expanded(
          flex: 4,
          child: Container(
            color: data.backgroundColor,
            child: _ContentBlock(data: data, textColor: data.textColor, onAction: onAction),
          ),
        ),
      ],
    );
  }
}

// --- 3. BOTTOM IMAGE ---
class _BottomImageTemplate extends StatelessWidget {
  final OnboardingPageConfig data;
  final VoidCallback onAction;
  const _BottomImageTemplate({required this.data, required this.onAction});

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Expanded(
          flex: 4,
          child: Container(
            color: data.backgroundColor,
            child: _ContentBlock(data: data, textColor: data.textColor, onAction: onAction),
          ),
        ),
        Expanded(
          flex: 6,
          child: ClipRRect(
            borderRadius: const BorderRadius.only(topRight: Radius.circular(60)),
            child: _SlideBgImage(data: data),
          ),
        ),
      ],
    );
  }
}

// --- 4. FLOATING CARD ---
class _FloatingCardTemplate extends StatelessWidget {
  final OnboardingPageConfig data;
  final VoidCallback onAction;
  const _FloatingCardTemplate({required this.data, required this.onAction});

  @override
  Widget build(BuildContext context) {
    return Stack(
      children: [
        _SlideBgImage(data: data),
        Container(color: Colors.black.withValues(alpha: 0.3)),
        Center(
          child: Container(
            margin: const EdgeInsets.all(32),
            padding: const EdgeInsets.all(24),
            decoration: BoxDecoration(
              color: data.backgroundColor.withValues(alpha: 0.95),
              borderRadius: BorderRadius.circular(32),
              boxShadow: [
                BoxShadow(color: Colors.black.withValues(alpha: 0.2), blurRadius: 40, offset: const Offset(0, 20)),
              ],
            ),
            child: _ContentBlock(data: data, textColor: data.textColor, centered: true, onAction: onAction),
          ),
        ),
      ],
    );
  }
}

// --- 5. CENTERED MINI ---
class _CenteredMiniTemplate extends StatelessWidget {
  final OnboardingPageConfig data;
  final VoidCallback onAction;
  const _CenteredMiniTemplate({required this.data, required this.onAction});

  @override
  Widget build(BuildContext context) {
    return Container(
      color: data.backgroundColor,
      padding: const EdgeInsets.all(32),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            width: 200,
            height: 200,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              border: Border.all(color: data.buttonColor.withValues(alpha: 0.3), width: 8),
            ),
            clipBehavior: Clip.antiAlias,
            child: _SlideBgImage(data: data),
          ),
          const SizedBox(height: 48),
          _ContentBlock(data: data, textColor: data.textColor, centered: true, onAction: onAction),
        ],
      ),
    );
  }
}

// --- 6. DARK PREMIUM ---
class _DarkPremiumTemplate extends StatelessWidget {
  final OnboardingPageConfig data;
  final VoidCallback onAction;
  const _DarkPremiumTemplate({required this.data, required this.onAction});

  @override
  Widget build(BuildContext context) {
    return Container(
      color: data.backgroundColor,
      child: Column(
        children: [
          const Spacer(),
          Container(
            width: 250,
            height: 250,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              border: Border.all(color: data.buttonColor, width: 4),
            ),
            clipBehavior: Clip.antiAlias,
            child: _SlideBgImage(data: data),
          ),
          const Spacer(),
          _ContentBlock(data: data, textColor: data.textColor, centered: true, onAction: onAction),
          const SizedBox(height: 140),
        ],
      ),
    );
  }
}

// --- 7. GLASSMORPHIC ---
class _GlassmorphicTemplate extends StatelessWidget {
  final OnboardingPageConfig data;
  final VoidCallback onAction;
  const _GlassmorphicTemplate({required this.data, required this.onAction});

  @override
  Widget build(BuildContext context) {
    return Stack(
      children: [
        _SlideBgImage(data: data),
        Center(
          child: Container(
            margin: const EdgeInsets.all(24),
            padding: const EdgeInsets.all(32),
            decoration: BoxDecoration(
              color: data.backgroundColor.withValues(alpha: 0.1),
              borderRadius: BorderRadius.circular(40),
              border: Border.all(color: data.textColor.withValues(alpha: 0.2)),
            ),
            child: _ContentBlock(data: data, textColor: data.textColor, centered: true, onAction: onAction),
          ),
        ),
      ],
    );
  }
}

// --- 8. INDUSTRIAL ---
class _IndustrialTemplate extends StatelessWidget {
  final OnboardingPageConfig data;
  final VoidCallback onAction;
  const _IndustrialTemplate({required this.data, required this.onAction});

  @override
  Widget build(BuildContext context) {
    return Stack(
      children: [
        Transform.scale(
          scale: 1.2,
          child: Transform.rotate(angle: -0.1, child: _SlideBgImage(data: data)),
        ),
        Container(color: data.backgroundColor.withValues(alpha: 0.7)),
        _ContentBlock(data: data, align: Alignment.centerLeft, textColor: data.textColor, onAction: onAction),
      ],
    );
  }
}

// --- 9. SIDE BY SIDE ---
class _SideBySideTemplate extends StatelessWidget {
  final OnboardingPageConfig data;
  final VoidCallback onAction;
  const _SideBySideTemplate({required this.data, required this.onAction});

  @override
  Widget build(BuildContext context) {
    return Container(
      color: data.backgroundColor,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Expanded(child: _SlideBgImage(data: data)),
          _ContentBlock(data: data, textColor: data.textColor, onAction: onAction),
          const SizedBox(height: 120),
        ],
      ),
    );
  }
}

// --- 10. CLEAN VECTOR ---
class _CleanVectorTemplate extends StatelessWidget {
  final OnboardingPageConfig data;
  final VoidCallback onAction;
  const _CleanVectorTemplate({required this.data, required this.onAction});

  @override
  Widget build(BuildContext context) {
    return Container(
      color: data.backgroundColor,
      child: Column(
        children: [
          const SizedBox(height: 100),
          Expanded(child: Padding(padding: const EdgeInsets.all(48), child: _SlideBgImage(data: data, fit: BoxFit.contain))),
          _ContentBlock(data: data, textColor: data.textColor, centered: true, onAction: onAction),
          const SizedBox(height: 140),
        ],
      ),
    );
  }
}

// Common Content Block
class _ContentBlock extends StatelessWidget {
  final OnboardingPageConfig data;
  final Alignment align;
  final Color textColor;
  final bool centered;
  final VoidCallback onAction;

  const _ContentBlock({
    required this.data,
    required this.onAction,
    this.align = Alignment.centerLeft,
    this.textColor = Colors.white,
    this.centered = false,
  });

  @override
  Widget build(BuildContext context) {
    return Align(
      alignment: align,
      child: SingleChildScrollView(
        physics: const BouncingScrollPhysics(),
        child: Padding(
          padding: const EdgeInsets.only(left: 32, right: 32, bottom: 120),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: centered ? CrossAxisAlignment.center : CrossAxisAlignment.start,
            children: [
            Text(
              data.title,
              textAlign: centered ? TextAlign.center : TextAlign.start,
              style: TextStyle(
                fontSize: 32,
                fontWeight: FontWeight.w900,
                color: textColor,
                letterSpacing: -1,
                height: 1.1,
              ),
            ),
            if (data.description.isNotEmpty) ...[
              const SizedBox(height: 16),
              Text(
                data.description,
                textAlign: centered ? TextAlign.center : TextAlign.start,
                style: TextStyle(
                  fontSize: 15,
                  fontWeight: FontWeight.w400,
                  color: textColor.withValues(alpha: 0.6),
                  height: 1.5,
                ),
              ),
            ],
            if (data.buttonType == 'action_below_text') ...[
              const SizedBox(height: 32),
              GestureDetector(
              onTap: onAction,
              child: Container(
                width: centered ? 200 : double.infinity,
                padding: const EdgeInsets.symmetric(vertical: 16),
                decoration: BoxDecoration(
                  color: data.buttonColor,
                  borderRadius: BorderRadius.circular(16),
                  boxShadow: [
                    BoxShadow(
                      color: data.buttonColor.withValues(alpha: 0.3),
                      blurRadius: 20,
                      offset: const Offset(0, 10),
                    ),
                  ],
                ),
                child: Center(
                  child: Text(
                    data.buttonText.toUpperCase(),
                    style: TextStyle(
                      color: data.buttonColor.computeLuminance() > 0.5 ? AppColors.primaryNavy : Colors.white,
                      fontSize: 14,
                      fontWeight: FontWeight.w900,
                      letterSpacing: 1.5,
                    ),
                  ),
                ),
              ),
              ),
            ],
          ],
          ),
        ),
      ),
    );
  }
}
