import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../../domain/models/user.dart';

class AuthState {
  final User? user;
  final bool isLoading;
  final String? error;
  final bool hasSetPin; // Indicates if Transaction PIN has been set

  AuthState({
    this.user,
    this.isLoading = false,
    this.error,
    this.hasSetPin = false,
  });

  AuthState copyWith({
    User? user,
    bool? isLoading,
    String? error,
    bool? hasSetPin,
  }) {
    return AuthState(
      user: user ?? this.user,
      isLoading: isLoading ?? this.isLoading,
      error: error,
      hasSetPin: hasSetPin ?? this.hasSetPin,
    );
  }
}

class AuthNotifier extends StateNotifier<AuthState> {
  final _secureStorage = const FlutterSecureStorage();

  AuthNotifier() : super(AuthState()) {
    _checkInitialSession();
  }

  Future<void> _checkInitialSession() async {
    final token = await _secureStorage.read(key: 'session_token');
    final pin = await _secureStorage.read(key: 'transaction_pin');
    
    if (token != null) {
      state = AuthState(
        user: User(id: '1', name: 'Tony Stark', email: 'tony@stark.com', companyName: 'Stark Industries Ltd'),
        hasSetPin: pin != null,
      );
    }
  }

  Future<bool> login(String email, String password) async {
    state = state.copyWith(isLoading: true);
    await Future.delayed(const Duration(seconds: 1)); // Simulate server roundtrip
    
    if (email.contains('@') && password.length >= 6) {
      await _secureStorage.write(key: 'session_token', value: 'session_token_example');
      final pin = await _secureStorage.read(key: 'transaction_pin');
      
      state = AuthState(
        user: User(id: '1', name: 'Tony Stark', email: email, companyName: 'Stark Industries Ltd'),
        hasSetPin: pin != null,
      );
      return true;
    } else {
      state = state.copyWith(isLoading: false, error: 'Invalid login details. Use password with at least 6 characters.');
      return false;
    }
  }

  Future<void> registerPin(String pin) async {
    await _secureStorage.write(key: 'transaction_pin', value: pin);
    state = state.copyWith(hasSetPin: true);
  }

  Future<bool> verifyPin(String pin) async {
    final savedPin = await _secureStorage.read(key: 'transaction_pin');
    return savedPin == pin;
  }

  Future<void> logout() async {
    await _secureStorage.delete(key: 'session_token');
    state = AuthState(user: null, hasSetPin: false);
  }
}

final authProvider = StateNotifierProvider<AuthNotifier, AuthState>((ref) {
  return AuthNotifier();
});
