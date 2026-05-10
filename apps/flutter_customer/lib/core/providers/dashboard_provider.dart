import 'package:flutter_riverpod/flutter_riverpod.dart';

/// Provider to manage the active index of the Main Dashboard
final dashboardIndexProvider = StateProvider<int>((ref) => 0);
