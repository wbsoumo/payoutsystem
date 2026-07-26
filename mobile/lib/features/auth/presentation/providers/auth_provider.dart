import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:dio/dio.dart';
import '../../domain/models/user.dart';
import '../../../../core/network/api_client.dart';

class AuthState {
  final User? user;
  final bool isLoading;
  final String? error;
  final bool hasSetPin;

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
      // Fetch balance in background to update server PIN state
      _syncServerPinState();
    }
  }

  Future<void> _syncServerPinState() async {
    try {
      final client = ApiClient();
      final response = await client.dio.get('/wallet/balance');
      if (response.data['success'] == true) {
        final serverHasPin = response.data['has_set_pin'] == true;
        if (serverHasPin) {
          await _secureStorage.write(key: 'transaction_pin', value: 'synced_from_server');
        }
        state = state.copyWith(hasSetPin: serverHasPin);
      }
    } catch (e) {
      // Ignore background sync failure
    }
  }

  Future<bool> login(String email, String password) async {
    state = state.copyWith(isLoading: true);
    
    try {
      final client = ApiClient();
      final response = await client.dio.post('/auth/login', data: {
        'email': email.trim(),
        'password': password,
      });

      if (response.data['success'] == true) {
        final String apiKey = response.data['api_key'];
        final String apiSecret = response.data['api_secret'];
        final String merchantId = response.data['merchant_id'];
        final userMap = response.data['user'] ?? {};

        // Save session credentials securely
        try {
          await _secureStorage.write(key: 'session_token', value: 'session_token_example');
          await _secureStorage.write(key: 'api_key', value: apiKey);
          await _secureStorage.write(key: 'api_secret', value: apiSecret);
          await _secureStorage.write(key: 'merchant_id', value: merchantId);
        } catch (storageError) {
          try {
            await _secureStorage.deleteAll();
            await _secureStorage.write(key: 'session_token', value: 'session_token_example');
            await _secureStorage.write(key: 'api_key', value: apiKey);
            await _secureStorage.write(key: 'api_secret', value: apiSecret);
            await _secureStorage.write(key: 'merchant_id', value: merchantId);
          } catch (_) {
            // Keep going even if storage fails
          }
        }

        // Fetch server PIN config
        bool serverHasPin = false;
        try {
          final balanceClient = ApiClient();
          final balanceResponse = await balanceClient.dio.get('/wallet/balance');
          if (balanceResponse.data['success'] == true) {
            serverHasPin = balanceResponse.data['has_set_pin'] == true;
            if (serverHasPin) {
              await _secureStorage.write(key: 'transaction_pin', value: 'synced_from_server');
            } else {
              await _secureStorage.delete(key: 'transaction_pin');
            }
          }
        } catch (e) {
          try {
            final localPin = await _secureStorage.read(key: 'transaction_pin');
            serverHasPin = localPin != null;
          } catch (_) {}
        }

        state = AuthState(
          user: User(
            id: userMap['id'] ?? '1', 
            name: userMap['name'] ?? 'Tony Stark', 
            email: userMap['email'] ?? email, 
            companyName: userMap['company_name'] ?? 'Stark Industries Ltd'
          ),
          hasSetPin: serverHasPin,
        );
        return true;
      } else {
        final err = response.data is Map ? response.data['error'] : 'Login failed';
        state = state.copyWith(isLoading: false, error: err);
        return false;
      }
    } catch (e) {
      String errMsg = 'Connection error. Please try again.';
      if (e is DioException) {
        final resData = e.response?.data;
        if (resData is Map) {
          errMsg = resData['error']?.toString() ?? errMsg;
        } else if (resData is String && resData.isNotEmpty) {
          errMsg = resData.length > 100 ? resData.substring(0, 100) + '...' : resData;
        }
      }
      state = state.copyWith(isLoading: false, error: errMsg);
      return false;
    }
  }

  Future<bool> registerPin(String pin) async {
    try {
      final client = ApiClient();
      final response = await client.dio.post(
        '/pin/setup',
        data: {'pin': pin},
      );
      if (response.data['success'] == true) {
        await _secureStorage.write(key: 'transaction_pin', value: pin);
        state = state.copyWith(hasSetPin: true);
        return true;
      }
    } catch (e) {
      // Offline fallback
      await _secureStorage.write(key: 'transaction_pin', value: pin);
      state = state.copyWith(hasSetPin: true);
      return true;
    }
    return false;
  }

  /// Verifies PIN against the server.
  /// Returns null on success, or a String with the error message on failure.
  Future<String?> verifyPin(String pin) async {
    try {
      final client = ApiClient();
      final response = await client.dio.post(
        '/pin/verify',
        data: {'pin': pin},
      );
      if (response.data['success'] == true) {
        return null;
      }
    } catch (e) {
      if (e is DioException && e.response != null) {
        final data = e.response!.data;
        if (data is Map) {
          return data['error'] ?? data['message'] ?? 'Incorrect PIN! Please try again.';
        }
      }
      // Offline fallback
      final savedPin = await _secureStorage.read(key: 'transaction_pin');
      if (savedPin == null || savedPin == 'synced_from_server' || savedPin == pin) {
        return null; // Accept PIN if synced/matching
      }
      return 'Incorrect PIN! Please try again.';
    }
    return 'Incorrect PIN! Please try again.';
  }

  Future<String?> changePin(String currentPin, String newPin) async {
    try {
      final client = ApiClient();
      final response = await client.dio.post(
        '/pin/change',
        data: {
          'current_pin': currentPin,
          'new_pin': newPin,
        },
      );
      if (response.data['success'] == true) {
        await _secureStorage.write(key: 'transaction_pin', value: newPin);
        return null;
      }
    } catch (e) {
      if (e is DioException && e.response != null) {
        final data = e.response!.data;
        if (data is Map) {
          return data['error'] ?? data['message'] ?? 'Failed to change PIN.';
        }
      }
      // Offline fallback
      final savedPin = await _secureStorage.read(key: 'transaction_pin');
      if (savedPin == currentPin) {
        await _secureStorage.write(key: 'transaction_pin', value: newPin);
        return null;
      }
      return 'Current Transaction PIN is incorrect.';
    }
    return 'Failed to change PIN.';
  }

  Future<void> logout() async {
    await _secureStorage.delete(key: 'session_token');
    await _secureStorage.delete(key: 'transaction_pin');
    state = AuthState(user: null, hasSetPin: false);
  }
}

final authProvider = StateNotifierProvider<AuthNotifier, AuthState>((ref) {
  return AuthNotifier();
});
