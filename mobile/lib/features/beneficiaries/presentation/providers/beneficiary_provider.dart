import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:hive_flutter/hive_flutter.dart';
import '../../../../core/constants/endpoints.dart';
import '../../../../core/network/api_client.dart';

class BeneficiaryNotifier extends StateNotifier<List<Map<String, String>>> {
  BeneficiaryNotifier() : super([]) {
    _loadBeneficiariesFromHive();
    syncBeneficiaries();
  }

  void _loadBeneficiariesFromHive() {
    final defaults = [
      {
        'name': 'Vijay Kumar',
        'bank': 'State Bank of India',
        'account': '••••4556',
        'ifsc': 'SBIN0004556',
        'logo': '${Endpoints.baseUrl}/logo/sbi.co.in'
      },
      {
        'name': 'Sanjay Singh',
        'bank': 'HDFC Bank',
        'account': '••••8990',
        'ifsc': 'HDFC0001020',
        'logo': '${Endpoints.baseUrl}/logo/hdfcbank.com'
      },
      {
        'name': 'Priya Sharma',
        'bank': 'ICICI Bank',
        'account': '••••1122',
        'ifsc': 'ICIC0000045',
        'logo': '${Endpoints.baseUrl}/logo/icicibank.com'
      },
    ];

    try {
      if (Hive.isBoxOpen('beneficiaries_box')) {
        final box = Hive.box('beneficiaries_box');
        final List<dynamic>? stored = box.get('list');
        if (stored == null || stored.isEmpty) {
          state = defaults;
          box.put('list', defaults);
        } else {
          state = stored.map((item) => Map<String, String>.from(
            Map<dynamic, dynamic>.from(item).map((k, v) => MapEntry(k.toString(), v.toString()))
          )).toList();
        }
      } else {
        state = defaults;
      }
    } catch (e) {
      state = defaults;
    }
  }

  Future<void> syncBeneficiaries() async {
    try {
      final client = ApiClient();
      final response = await client.dio.get('/beneficiaries');
      if (response.data['success'] == true) {
        final List<dynamic> list = response.data['beneficiaries'] ?? [];
        final fetched = list.map((item) => Map<String, String>.from(
          Map<dynamic, dynamic>.from(item).map((k, v) => MapEntry(k.toString(), v.toString()))
        )).toList();

        state = fetched;
        if (Hive.isBoxOpen('beneficiaries_box')) {
          await Hive.box('beneficiaries_box').put('list', fetched);
        }
      }
    } catch (e) {
      // Fail silently and keep local Hive state
    }
  }

  Future<bool> addBeneficiary(Map<String, String> newBen) async {
    // 1. Optimistic local update
    final updated = [...state, newBen];
    state = updated;
    try {
      if (Hive.isBoxOpen('beneficiaries_box')) {
        Hive.box('beneficiaries_box').put('list', updated);
      }
    } catch (e) {
      // Ignore local write issues
    }

    // 2. Persist in MySQL on server
    try {
      final client = ApiClient();
      final response = await client.dio.post(
        '/beneficiaries',
        data: {
          'name': newBen['name'],
          'bank_name': newBen['bank'],
          'account_number': newBen['account'],
          'ifsc': newBen['ifsc'],
          'logo_url': newBen['logo'],
        },
      );
      if (response.data['success'] == true) {
        final serverBen = Map<String, String>.from(
          Map<dynamic, dynamic>.from(response.data['beneficiary']).map((k, v) => MapEntry(k.toString(), v.toString()))
        );
        state = state.map((b) => b['account'] == newBen['account'] && b['ifsc'] == newBen['ifsc'] ? serverBen : b).toList();
        if (Hive.isBoxOpen('beneficiaries_box')) {
          Hive.box('beneficiaries_box').put('list', state);
        }
        return true;
      }
    } catch (e) {
      // Fail silently and keep optimistic local record
    }
    return false;
  }

  Future<bool> removeBeneficiary(Map<String, String> ben) async {
    final updated = state.where((item) => item['account'] != ben['account'] || item['name'] != ben['name']).toList();
    state = updated;
    try {
      if (Hive.isBoxOpen('beneficiaries_box')) {
        Hive.box('beneficiaries_box').put('list', updated);
      }
    } catch (e) {}

    final id = ben['id'];
    if (id != null) {
      try {
        final client = ApiClient();
        final response = await client.dio.delete('/beneficiaries/$id');
        return response.data['success'] == true;
      } catch (e) {
        // Fail silently
      }
    }
    return false;
  }
}

final beneficiaryProvider = StateNotifierProvider<BeneficiaryNotifier, List<Map<String, String>>>((ref) {
  return BeneficiaryNotifier();
});
