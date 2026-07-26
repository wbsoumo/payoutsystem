import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:hive_flutter/hive_flutter.dart';

class BeneficiaryNotifier extends StateNotifier<List<Map<String, String>>> {
  BeneficiaryNotifier() : super([]) {
    _loadBeneficiaries();
  }

  void _loadBeneficiaries() {
    final box = Hive.box('beneficiaries_box');
    final List<dynamic>? stored = box.get('list');
    
    if (stored == null || stored.isEmpty) {
      final defaults = [
        {
          'name': 'Vijay Kumar',
          'bank': 'State Bank of India',
          'account': '••••4556',
          'ifsc': 'SBIN0004556',
          'logo': 'https://logo.clearbit.com/sbi.co.in'
        },
        {
          'name': 'Sanjay Singh',
          'bank': 'HDFC Bank',
          'account': '••••8990',
          'ifsc': 'HDFC0001020',
          'logo': 'https://logo.clearbit.com/hdfcbank.com'
        },
        {
          'name': 'Priya Sharma',
          'bank': 'ICICI Bank',
          'account': '••••1122',
          'ifsc': 'ICIC0000045',
          'logo': 'https://logo.clearbit.com/icicibank.com'
        },
      ];
      state = defaults;
      box.put('list', defaults);
    } else {
      state = stored.map((item) => Map<String, String>.from(item)).toList();
    }
  }

  void addBeneficiary(Map<String, String> newBen) {
    final updated = [...state, newBen];
    state = updated;
    Hive.box('beneficiaries_box').put('list', updated);
  }

  void removeBeneficiary(Map<String, String> ben) {
    final updated = state.where((item) => item['account'] != ben['account'] || item['name'] != ben['name']).toList();
    state = updated;
    Hive.box('beneficiaries_box').put('list', updated);
  }
}

final beneficiaryProvider = StateNotifierProvider<BeneficiaryNotifier, List<Map<String, String>>>((ref) {
  return BeneficiaryNotifier();
});
