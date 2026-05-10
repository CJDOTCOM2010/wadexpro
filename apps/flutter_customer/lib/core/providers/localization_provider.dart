import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'dart:ui' as ui;

class LocalizationState {
  final String locale;
  final String currencyCode;
  final String currencySymbol;

  LocalizationState({
    required this.locale,
    required this.currencyCode,
    required this.currencySymbol,
  });

  LocalizationState copyWith({
    String? locale,
    String? currencyCode,
    String? currencySymbol,
  }) {
    return LocalizationState(
      locale: locale ?? this.locale,
      currencyCode: currencyCode ?? this.currencyCode,
      currencySymbol: currencySymbol ?? this.currencySymbol,
    );
  }
}

final localizationProvider = StateNotifierProvider<LocalizationNotifier, LocalizationState>((ref) {
  return LocalizationNotifier();
});

class LocalizationNotifier extends StateNotifier<LocalizationState> {
  final FlutterSecureStorage _storage = const FlutterSecureStorage();

  LocalizationNotifier() : super(
    LocalizationState(
      locale: ui.window.locale.languageCode,
      currencyCode: 'GHS',
      currencySymbol: 'GH₵',
    )
  ) {
    _loadSettings();
  }

  Future<void> _loadSettings() async {
    final savedLocale = await _storage.read(key: 'preferred_locale');
    final savedCurrency = await _storage.read(key: 'preferred_currency');
    final savedSymbol = await _storage.read(key: 'preferred_symbol');

    if (savedLocale != null) {
      state = state.copyWith(
        locale: savedLocale,
        currencyCode: savedCurrency,
        currencySymbol: savedSymbol,
      );
    }
  }

  Future<void> setRegion(Map<String, dynamic> region) async {
    state = state.copyWith(
      currencyCode: region['currency_code'],
      currencySymbol: region['currency_symbol'],
    );
    await _storage.write(key: 'preferred_currency', value: region['currency_code']);
    await _storage.write(key: 'preferred_symbol', value: region['currency_symbol']);
  }

  Future<void> setLocale(String locale) async {
    state = state.copyWith(locale: locale);
    await _storage.write(key: 'preferred_locale', value: locale);
  }
}
