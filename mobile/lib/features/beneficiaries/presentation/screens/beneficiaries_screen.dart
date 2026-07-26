import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:dio/dio.dart';
import '../providers/beneficiary_provider.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/constants/endpoints.dart';

class BeneficiariesScreen extends ConsumerStatefulWidget {
  const BeneficiariesScreen({Key? key}) : super(key: key);

  @override
  ConsumerState<BeneficiariesScreen> createState() => _BeneficiariesScreenState();
}

class _BeneficiariesScreenState extends ConsumerState<BeneficiariesScreen> {
  final Map<String, String> _bankDomains = {
    'SBIN': 'sbi.co.in',
    'HDFC': 'hdfcbank.com',
    'ICIC': 'icicibank.com',
    'UTIB': 'axisbank.com',
    'BARB': 'bankofbaroda.in',
    'PUNB': 'pnbindia.in',
    'KKBK': 'kotak.com',
    'YESB': 'yesbank.in',
    'UBIN': 'unionbankofindia.co.in',
    'CNRB': 'canarabank.com',
    'IDIB': 'indianbank.in',
  };

  String _searchQuery = '';

  void _addBeneficiaryBottomSheet() {
    context.push('/add-beneficiary');
  }

  @override
  Widget build(BuildContext context) {
    final beneficiaries = ref.watch(beneficiaryProvider);
    final filtered = beneficiaries.where((b) {
      return b['name']!.toLowerCase().contains(_searchQuery.toLowerCase()) ||
          b['bank']!.toLowerCase().contains(_searchQuery.toLowerCase());
    }).toList();

    final isDark = Theme.of(context).brightness == Brightness.dark;
    final textColor = isDark ? Colors.white : const Color(0xFF1E293B);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Beneficiaries Directory', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
        actions: [
          IconButton(
            icon: const Icon(Icons.add, size: 22),
            onPressed: _addBeneficiaryBottomSheet,
          ),
        ],
      ),
      body: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
        child: Column(
          children: [
            // Search Input
            TextField(
              decoration: InputDecoration(
                hintText: 'Search beneficiary by name...',
                prefixIcon: const Icon(Icons.search),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(16)),
                contentPadding: const EdgeInsets.symmetric(vertical: 14),
              ),
              onChanged: (val) {
                setState(() {
                  _searchQuery = val;
                });
              },
            ),
            const SizedBox(height: 24),

            Expanded(
              child: ListView.separated(
                itemCount: filtered.length,
                separatorBuilder: (context, index) => const SizedBox(height: 12),
                itemBuilder: (context, index) {
                  final b = filtered[index];
                  return Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: isDark ? const Color(0xFF1E2235) : Colors.white,
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(color: isDark ? const Color(0xFF2E3245) : const Color(0xFFE2E8F0)),
                    ),
                    child: ListTile(
                      contentPadding: EdgeInsets.zero,
                      leading: SafeBankLogo(logoUrl: b['logo'], bankName: b['bank'] ?? ''),
                      title: Text(b['name']!, style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: textColor)),
                      subtitle: Padding(
                        padding: const EdgeInsets.only(top: 4),
                        child: Text('${b['bank']} • ${b['account']}\nIFSC: ${b['ifsc']}', style: const TextStyle(fontSize: 11, height: 1.3)),
                      ),
                      trailing: IconButton(
                        icon: const Icon(Icons.delete_outline, color: Colors.redAccent, size: 20),
                        onPressed: () {
                          ref.read(beneficiaryProvider.notifier).removeBeneficiary(b);
                        },
                      ),
                    ),
                  );
                },
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class SafeBankLogo extends StatelessWidget {
  final String? logoUrl;
  final String bankName;
  final double size;

  const SafeBankLogo({
    Key? key,
    required this.logoUrl,
    required this.bankName,
    this.size = 48,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    if (logoUrl == null || logoUrl!.isEmpty) {
      return Container(
        width: size,
        height: size,
        decoration: const BoxDecoration(
          color: Color(0xFFEFF6FF),
          shape: BoxShape.circle,
        ),
        child: const Center(
          child: Icon(Icons.person, color: Color(0xFF4361EE), size: 20),
        ),
      );
    }

    return Container(
      width: size,
      height: size,
      decoration: const BoxDecoration(
        color: Color(0xFFEFF6FF),
        shape: BoxShape.circle,
      ),
      child: ClipOval(
        child: Image.network(
          logoUrl!,
          width: size,
          height: size,
          fit: BoxFit.cover,
          errorBuilder: (context, error, stackTrace) {
            final uri = Uri.tryParse(logoUrl!);
            final domain = uri != null && uri.pathSegments.isNotEmpty ? uri.pathSegments.last : 'generic-bank.com';
            
            return Image.network(
              'https://logo.clearbit.com/$domain',
              width: size,
              height: size,
              fit: BoxFit.cover,
              errorBuilder: (context, error2, stackTrace2) {
                return const Center(
                  child: Icon(Icons.business, color: Color(0xFF4361EE), size: 20),
                );
              },
            );
          },
        ),
      ),
    );
  }
}
