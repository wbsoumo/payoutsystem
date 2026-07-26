import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class ThemeNotifier extends StateNotifier<ThemeMode> {
  final _secureStorage = const FlutterSecureStorage();

  ThemeNotifier() : super(ThemeMode.dark) {
    _loadTheme();
  }

  Future<void> _loadTheme() async {
    final themeStr = await _secureStorage.read(key: 'theme_mode');
    if (themeStr == 'light') {
      state = ThemeMode.light;
    } else {
      state = ThemeMode.dark; // Default to dark premium theme matching user's image
    }
  }

  Future<void> toggleTheme(bool isDark) async {
    state = isDark ? ThemeMode.dark : ThemeMode.light;
    await _secureStorage.write(key: 'theme_mode', value: isDark ? 'dark' : 'light');
  }
}

final themeProvider = StateNotifierProvider<ThemeNotifier, ThemeMode>((ref) {
  return ThemeNotifier();
});
