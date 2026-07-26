import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:hive_flutter/hive_flutter.dart';
import '../../../../core/constants/endpoints.dart';

class BeneficiaryNotifier extends StateNotifier<List<Map<String, String>>> {
  BeneficiaryNotifier() : super([]) {
    _loadBeneficiaries();
  }

  void _loadBeneficiaries() {
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
          state = stored.map((item) => Map<String, String>.from(item)).toList();
        }
      } else {
        state = defaults;
      }
    } catch (e) {
      state = defaults;
    }
  }

  void addBeneficiary(Map<String, String> newBen) {
    final updated = [...state, newBen];
    state = updated;
    try {
      if (Hive.isBoxOpen('beneficiaries_box')) {
        Hive.box('beneficiaries_box').put('list', updated);
      }
    } catch (e) {
      print('Failed to save beneficiary: $e');
    }
  }

  void removeBeneficiary(Map<String, String> ben) {
    final updated = state.where((item) => item['account'] != ben['account'] || item['name'] != ben['name']).toList();
    state = updated;
    try {
      if (Hive.isBoxOpen('beneficiaries_box')) {
        Hive.box('beneficiaries_box').put('list', updated);
      }
    } catch (e) {
      print('Failed to remove beneficiary: $e');
    }
  }
}

final beneficiaryProvider = StateNotifierProvider<BeneficiaryNotifier, List<Map<String, String>>>((ref) {
  return BeneficiaryNotifier();
});
