import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:dio/dio.dart';
import '../providers/beneficiary_provider.dart';

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
    final nameController = TextEditingController();
    final bankController = TextEditingController();
    final accountController = TextEditingController();
    final ifscController = TextEditingController();

    bool isFetchingIfsc = false;
    String resolvedLogo = '';
    String fetchError = '';

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Theme.of(context).scaffoldBackgroundColor,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(28)),
      ),
      builder: (context) {
        final isDark = Theme.of(context).brightness == Brightness.dark;
        final textColor = isDark ? Colors.white : const Color(0xFF1E293B);

        return StatefulBuilder(
          builder: (BuildContext context, StateSetter setModalState) {
            return Padding(
              padding: EdgeInsets.fromLTRB(24, 24, 24, MediaQuery.of(context).viewInsets.bottom + 24),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  Center(
                    child: Container(
                      width: 48,
                      height: 5,
                      decoration: BoxDecoration(
                        color: Colors.grey.withOpacity(0.3),
                        borderRadius: BorderRadius.circular(10),
                      ),
                    ),
                  ),
                  const SizedBox(height: 20),
                  Text(
                    'Add New Beneficiary',
                    style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: textColor),
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 24),

                  // Holder Name
                  TextField(
                    controller: nameController,
                    decoration: InputDecoration(
                      labelText: 'Holder Name',
                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(14)),
                      prefixIcon: const Icon(Icons.person_outline),
                    ),
                  ),
                  const SizedBox(height: 16),

                  // IFSC Code lookup
                  TextField(
                    controller: ifscController,
                    maxLength: 11,
                    textCapitalization: TextCapitalization.characters,
                    decoration: InputDecoration(
                      labelText: 'IFSC Code',
                      hintText: 'e.g. SBIN0004556',
                      counterText: '',
                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(14)),
                      prefixIcon: const Icon(Icons.code),
                      suffixIcon: isFetchingIfsc
                          ? const SizedBox(
                              width: 20,
                              height: 20,
                              child: Padding(
                                padding: EdgeInsets.all(12),
                                child: CircularProgressIndicator(strokeWidth: 2),
                              ),
                            )
                          : null,
                    ),
                    onChanged: (val) async {
                      if (val.length == 11) {
                        setModalState(() {
                          isFetchingIfsc = true;
                          fetchError = '';
                        });
                        try {
                          final response = await Dio().get('https://ifsc.razorpay.com/${val.toUpperCase()}');
                          final bankName = response.data['BANK'] ?? '';
                          final branch = response.data['BRANCH'] ?? '';

                          final prefix = val.substring(0, 4).toUpperCase();
                          final domain = _bankDomains[prefix] ?? 'generic-bank.com';

                          setModalState(() {
                            bankController.text = '$bankName - $branch';
                            resolvedLogo = 'https://logo.clearbit.com/$domain';
                            isFetchingIfsc = false;
                          });
                        } catch (e) {
                          setModalState(() {
                            isFetchingIfsc = false;
                            fetchError = 'Could not resolve bank details. Check IFSC code.';
                          });
                        }
                      }
                    },
                  ),
                  if (fetchError.isNotEmpty) ...[
                    const SizedBox(height: 6),
                    Text(fetchError, style: const TextStyle(color: Colors.redAccent, fontSize: 10, fontWeight: FontWeight.bold)),
                  ],
                  const SizedBox(height: 16),

                  // Bank details
                  Row(
                    children: [
                      if (resolvedLogo.isNotEmpty) ...[
                        ClipRRect(
                          borderRadius: BorderRadius.circular(10),
                          child: Image.network(
                            resolvedLogo,
                            width: 44,
                            height: 44,
                            fit: BoxFit.cover,
                            errorBuilder: (c, o, s) => const Icon(Icons.account_balance, size: 44),
                          ),
                        ),
                        const SizedBox(width: 12),
                      ],
                      Expanded(
                        child: TextField(
                          controller: bankController,
                          decoration: InputDecoration(
                            labelText: 'Bank Name & Branch',
                            border: OutlineInputBorder(borderRadius: BorderRadius.circular(14)),
                            prefixIcon: resolvedLogo.isEmpty ? const Icon(Icons.account_balance_outlined) : null,
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 16),

                  // Account Number
                  TextField(
                    controller: accountController,
                    keyboardType: TextInputType.number,
                    decoration: InputDecoration(
                      labelText: 'Account Number',
                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(14)),
                      prefixIcon: const Icon(Icons.account_box_outlined),
                    ),
                  ),
                  const SizedBox(height: 28),

                  ElevatedButton(
                    onPressed: () {
                      if (nameController.text.isNotEmpty && accountController.text.isNotEmpty && bankController.text.isNotEmpty) {
                        final newBen = {
                          'name': nameController.text,
                          'bank': bankController.text,
                          'account': '••••' + accountController.text.substring(accountController.text.length - 4),
                          'ifsc': ifscController.text.toUpperCase(),
                          'logo': resolvedLogo.isNotEmpty ? resolvedLogo : 'https://logo.clearbit.com/generic-bank.com',
                        };
                        
                        ref.read(beneficiaryProvider.notifier).addBeneficiary(newBen);
                        Navigator.pop(context);
                      }
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF7C3AED),
                      foregroundColor: Colors.white,
                      minimumSize: const Size.fromHeight(54),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                      elevation: 0,
                    ),
                    child: const Text('Add Beneficiary', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
                  ),
                ],
              ),
            );
          },
        );
      },
    );
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
                      leading: CircleAvatar(
                        radius: 24,
                        backgroundColor: const Color(0xFFEFF6FF),
                        backgroundImage: b['logo'] != null ? NetworkImage(b['logo']!) : null,
                        child: b['logo'] == null ? const Icon(Icons.person, color: Color(0xFF4361EE)) : null,
                      ),
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
