import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:wadexpro_customer/core/theme/app_colors.dart';
import 'package:wadexpro_customer/core/theme/theme_provider.dart';

/// Shows the Appearance bottom sheet matching the reference design.
/// Allows switching between Light mode, Dark mode, and System default.
void showAppearanceModal(BuildContext context, WidgetRef ref) {
  final currentMode = ref.read(themeModeProvider);

  showModalBottomSheet(
    context: context,
    isScrollControlled: true,
    backgroundColor: Colors.transparent,
    builder: (_) => _AppearanceSheet(initialMode: currentMode),
  );
}

class _AppearanceSheet extends ConsumerStatefulWidget {
  final ThemeMode initialMode;
  const _AppearanceSheet({required this.initialMode});

  @override
  ConsumerState<_AppearanceSheet> createState() => _AppearanceSheetState();
}

class _AppearanceSheetState extends ConsumerState<_AppearanceSheet> {
  late ThemeMode _selected;

  @override
  void initState() {
    super.initState();
    _selected = widget.initialMode;
  }

  bool get _isDark {
    final brightness = MediaQuery.of(context).platformBrightness;
    if (_selected == ThemeMode.dark) return true;
    if (_selected == ThemeMode.system && brightness == Brightness.dark) return true;
    return false;
  }

  @override
  Widget build(BuildContext context) {
    // Derive colors based on the CURRENT live theme so user sees the preview
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final bgColor = isDark ? AppColors.obsidianDark : Colors.white;
    final textColor = isDark ? Colors.white : Colors.black;
    final subtitleColor = isDark ? Colors.white54 : Colors.black54;
    final dividerColor = isDark ? Colors.white12 : Colors.grey.shade200;
    final radioActive = isDark ? Colors.white : Colors.black;
    final saveEnabled = _selected != widget.initialMode;

    return Container(
      decoration: BoxDecoration(
        color: bgColor,
        borderRadius: const BorderRadius.vertical(top: Radius.circular(24)),
      ),
      child: SafeArea(
        top: false,
        child: Padding(
          padding: const EdgeInsets.fromLTRB(24, 16, 24, 24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              // Handle bar
              Container(
                width: 40,
                height: 4,
                decoration: BoxDecoration(
                  color: isDark ? Colors.white24 : Colors.grey.shade300,
                  borderRadius: BorderRadius.circular(2),
                ),
              ),
              const SizedBox(height: 20),

              // Title
              Text(
                'Appearance',
                style: TextStyle(fontSize: 22, fontWeight: FontWeight.bold, color: textColor),
              ),
              const SizedBox(height: 24),
              Divider(height: 1, color: dividerColor),

              // ── Light Mode ──
              _buildOption(
                title: 'Light mode',
                value: ThemeMode.light,
                textColor: textColor,
                dividerColor: dividerColor,
                radioActive: radioActive,
              ),
              Divider(height: 1, color: dividerColor),

              // ── Dark Mode ──
              _buildOption(
                title: 'Dark mode',
                value: ThemeMode.dark,
                textColor: textColor,
                dividerColor: dividerColor,
                radioActive: radioActive,
              ),
              Divider(height: 1, color: dividerColor),

              // ── System ──
              _buildOption(
                title: 'Use device settings',
                subtitle: "We'll follow your device display theme.",
                value: ThemeMode.system,
                textColor: textColor,
                subtitleColor: subtitleColor,
                dividerColor: dividerColor,
                radioActive: radioActive,
              ),

              const SizedBox(height: 24),

              // ── Save Button ──
              SizedBox(
                width: double.infinity,
                height: 56,
                child: ElevatedButton(
                  onPressed: saveEnabled
                      ? () {
                          ref.read(themeModeProvider.notifier).setThemeMode(_selected);
                          Navigator.pop(context);
                        }
                      : null,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: saveEnabled
                        ? (isDark ? Colors.white : Colors.black)
                        : (isDark ? Colors.white12 : Colors.grey.shade200),
                    foregroundColor: saveEnabled
                        ? (isDark ? Colors.black : Colors.white)
                        : (isDark ? Colors.white38 : Colors.grey),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                    elevation: 0,
                  ),
                  child: Text(
                    'Save',
                    style: TextStyle(
                      fontWeight: FontWeight.bold,
                      fontSize: 16,
                      color: saveEnabled
                          ? (isDark ? Colors.black : Colors.white)
                          : (isDark ? Colors.white38 : Colors.grey.shade400),
                    ),
                  ),
                ),
              ),
              const SizedBox(height: 12),

              // ── Cancel Button ──
              TextButton(
                onPressed: () => Navigator.pop(context),
                child: Text(
                  'Cancel',
                  style: TextStyle(color: textColor, fontWeight: FontWeight.bold, fontSize: 16),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildOption({
    required String title,
    String? subtitle,
    required ThemeMode value,
    required Color textColor,
    Color? subtitleColor,
    required Color dividerColor,
    required Color radioActive,
  }) {
    final isSelected = _selected == value;

    return InkWell(
      onTap: () => setState(() => _selected = value),
      child: Padding(
        padding: const EdgeInsets.symmetric(vertical: 20),
        child: Row(
          children: [
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(title, style: TextStyle(fontSize: 17, fontWeight: FontWeight.w600, color: textColor)),
                  if (subtitle != null) ...[
                    const SizedBox(height: 4),
                    Text(subtitle, style: TextStyle(fontSize: 14, color: subtitleColor ?? Colors.grey)),
                  ],
                ],
              ),
            ),
            Container(
              width: 28,
              height: 28,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                border: Border.all(
                  color: isSelected ? radioActive : Colors.grey,
                  width: isSelected ? 2.5 : 1.5,
                ),
              ),
              child: isSelected
                  ? Center(
                      child: Container(
                        width: 14,
                        height: 14,
                        decoration: BoxDecoration(shape: BoxShape.circle, color: radioActive),
                      ),
                    )
                  : null,
            ),
          ],
        ),
      ),
    );
  }
}
