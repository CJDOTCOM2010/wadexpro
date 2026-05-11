import 'package:flutter/material.dart';
import 'package:flutter/rendering.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'dashboard_home_tab.dart';
import 'services_catalog_tab.dart';
import 'package:wadexpro_customer/features/activity/presentation/pages/activity_tab.dart';
import 'package:wadexpro_customer/features/profile/presentation/pages/account_tab.dart';
import '../../../../core/providers/dashboard_provider.dart';
import '../../../../core/theme/app_colors.dart';

class MainDashboardScreen extends ConsumerStatefulWidget {
  const MainDashboardScreen({super.key});

  @override
  ConsumerState<MainDashboardScreen> createState() => _MainDashboardScreenState();
}

class _MainDashboardScreenState extends ConsumerState<MainDashboardScreen> {
  bool _isVisible = true;

  final List<Widget> _tabs = const [
    DashboardHomeTab(),
    ServicesCatalogTab(),
    ActivityTab(),
    AccountTab(),
  ];

  @override
  Widget build(BuildContext context) {
    final currentIndex = ref.watch(dashboardIndexProvider);

    return Scaffold(
      backgroundColor: AppColors.obsidianDark,
      extendBody: true, // Prevents body resizing when nav bar slides out
      body: NotificationListener<UserScrollNotification>(
        onNotification: (notification) {
          if (notification.direction == ScrollDirection.reverse) {
            if (_isVisible) setState(() => _isVisible = false);
          } else if (notification.direction == ScrollDirection.forward) {
            if (!_isVisible) setState(() => _isVisible = true);
          }
          return false;
        },
        child: IndexedStack(
          index: currentIndex,
          children: _tabs,
        ),
      ),
      bottomNavigationBar: AnimatedSlide(
        duration: const Duration(milliseconds: 300),
        offset: _isVisible ? Offset.zero : const Offset(0, 1),
        child: Container(
          decoration: const BoxDecoration(
            color: AppColors.obsidianDark,
            border: Border(top: BorderSide(color: Colors.white12, width: 1)),
          ),
          child: SafeArea(
            child: BottomNavigationBar(
              currentIndex: currentIndex,
              onTap: (index) => ref.read(dashboardIndexProvider.notifier).state = index,
              type: BottomNavigationBarType.fixed,
              backgroundColor: AppColors.obsidianDark,
              elevation: 0,
              selectedItemColor: Colors.white,
              unselectedItemColor: Colors.white54,
              showSelectedLabels: true,
              showUnselectedLabels: true,
              selectedFontSize: 12,
              unselectedFontSize: 12,
              selectedLabelStyle: const TextStyle(fontWeight: FontWeight.w600),
              unselectedLabelStyle: const TextStyle(fontWeight: FontWeight.w500),
              items: const [
                BottomNavigationBarItem(
                  icon: Padding(padding: EdgeInsets.only(bottom: 4), child: Icon(Icons.home_filled)),
                  label: 'Home',
                ),
                BottomNavigationBarItem(
                  icon: Padding(padding: EdgeInsets.only(bottom: 4), child: Icon(Icons.grid_view_rounded)),
                  label: 'Services',
                ),
                BottomNavigationBarItem(
                  icon: Padding(padding: EdgeInsets.only(bottom: 4), child: Icon(Icons.receipt_long)),
                  label: 'Activity',
                ),
                BottomNavigationBarItem(
                  icon: Padding(padding: EdgeInsets.only(bottom: 4), child: Icon(Icons.person)),
                  label: 'Account',
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
