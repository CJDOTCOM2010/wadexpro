import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:shared_preferences/shared_preferences.dart';

const _kNotificationsKey = 'wadexpro_notifications_enabled';
const _kLocationKey = 'wadexpro_location_always_on';

class UserSettings {
  final bool notificationsEnabled;
  final bool locationAlwaysOn;

  UserSettings({
    required this.notificationsEnabled,
    required this.locationAlwaysOn,
  });

  UserSettings copyWith({
    bool? notificationsEnabled,
    bool? locationAlwaysOn,
  }) {
    return UserSettings(
      notificationsEnabled: notificationsEnabled ?? this.notificationsEnabled,
      locationAlwaysOn: locationAlwaysOn ?? this.locationAlwaysOn,
    );
  }
}

final settingsProvider = StateNotifierProvider<SettingsNotifier, UserSettings>((ref) {
  return SettingsNotifier();
});

class SettingsNotifier extends StateNotifier<UserSettings> {
  SettingsNotifier() : super(UserSettings(notificationsEnabled: true, locationAlwaysOn: false)) {
    _loadSettings();
  }

  Future<void> _loadSettings() async {
    final prefs = await SharedPreferences.getInstance();
    state = UserSettings(
      notificationsEnabled: prefs.getBool(_kNotificationsKey) ?? true,
      locationAlwaysOn: prefs.getBool(_kLocationKey) ?? false,
    );
  }

  Future<void> toggleNotifications(bool value) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setBool(_kNotificationsKey, value);
    state = state.copyWith(notificationsEnabled: value);
  }

  Future<void> toggleLocation(bool value) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setBool(_kLocationKey, value);
    state = state.copyWith(locationAlwaysOn: value);
  }
}
