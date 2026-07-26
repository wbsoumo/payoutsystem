import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:dio/dio.dart';
import '../providers/beneficiary_provider.dart';
import '../../../../core/constants/endpoints.dart';

class AddBeneficiaryScreen extends ConsumerStatefulWidget {
  const AddBeneficiaryScreen({Key? key}) : super(key: key);

  @override
  ConsumerState<AddBeneficiaryScreen> createState() => _AddBeneficiaryScreenState();
}

class _AddBeneficiaryScreenState extends ConsumerState<AddBeneficiaryScreen> {
  final _formKey = GlobalKey<FormState>();
  final nameController = TextEditingController();
  final bankController = TextEditingController();
  final accountController = TextEditingController();
  final ifscController = TextEditingController();

  bool isFetchingIfsc = false;
  String resolvedLogo = '';
  String fetchError = '';

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

  @override
  void dispose() {
    nameController.dispose();
    bankController.dispose();
    accountController.dispose();
    ifscController.dispose();
    super.dispose();
  }

  Future<void> _fetchBankDetails(String ifsc) async {
    if (ifsc.length != 11) return;

    setState(() {
      isFetchingIfsc = true;
      fetchError = '';
      resolvedLogo = '';
    });

    try {
      final response = await Dio().get('https://ifsc.razorpay.com/${ifsc.toUpperCase()}');
      final bankName = response.data['BANK'] ?? '';
      final branch = response.data['BRANCH'] ?? '';
      
      final prefix = ifsc.substring(0, 4).toUpperCase();
      final domain = _bankDomains[prefix] ?? 'generic-bank.com';

      setState(() {
        bankController.text = '$bankName - $branch';
        resolvedLogo = '${Endpoints.baseUrl}/logo/$domain';
        isFetchingIfsc = false;
      });
    } catch (e) {
      setState(() {
        isFetchingIfsc = false;
        fetchError = 'Could not resolve bank details. Check IFSC code.';
      });
    }
  }

  void _saveBeneficiary() {
    if (_formKey.currentState!.validate()) {
      if (bankController.text.isEmpty) {
        setState(() {
          fetchError = 'Please fetch bank details via valid IFSC code first.';
        });
        return;
      }

      final newBen = {
        'name': nameController.text.trim(),
        'bank': bankController.text.trim(),
        'account': '••••' + accountController.text.substring(accountController.text.length - 4),
        'ifsc': ifscController.text.toUpperCase().trim(),
        'logo': resolvedLogo.isNotEmpty ? resolvedLogo : '${Endpoints.baseUrl}/logo/generic-bank.com',
      };

      ref.read(beneficiaryProvider.notifier).addBeneficiary(newBen);
      
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Beneficiary added successfully!'), backgroundColor: Colors.green),
      );
      Navigator.pop(context, newBen);
    }
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final textColor = isDark ? Colors.white : const Color(0xFF1E293B);
    final cardColor = isDark ? const Color(0xFF1E2235) : Colors.white;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Add Beneficiary', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // 1. Bank preview card at top
              AnimatedContainer(
                duration: const Duration(milliseconds: 300),
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: isDark ? const Color(0xFF1E2235) : Colors.white,
                  borderRadius: BorderRadius.circular(24),
                  border: Border.all(
                    color: fetchError.isNotEmpty 
                        ? Colors.red.withOpacity(0.3) 
                        : isDark ? const Color(0xFF2E3245) : const Color(0xFFE2E8F0),
                  ),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withOpacity(0.02),
                      blurRadius: 10,
                      offset: const Offset(0, 4),
                    ),
                  ],
                ),
                child: Row(
                  children: [
                    SafeBankLogo(
                      logoUrl: resolvedLogo,
                      bankName: bankController.text,
                      size: 54,
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          if (isFetchingIfsc)
                            const SizedBox(
                              width: 16,
                              height: 16,
                              child: CircularProgressIndicator(strokeWidth: 2),
                            )
                          else if (bankController.text.isNotEmpty) ...[
                            Text(
                              bankController.text,
                              style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: textColor),
                              maxLines: 2,
                              overflow: TextOverflow.ellipsis,
                            ),
                            const SizedBox(height: 4),
                            Text(
                              'IFSC: ${ifscController.text.toUpperCase()}',
                              style: const TextStyle(fontSize: 10, color: Colors.grey, fontWeight: FontWeight.w600),
                            ),
                          ] else ...[
                            Text(
                              'Enter IFSC details below',
                              style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: Colors.grey.shade400),
                            ),
                            const SizedBox(height: 4),
                            const Text(
                              'Bank information will auto-resolve',
                              style: TextStyle(fontSize: 10, color: Colors.grey),
                            ),
                          ],
                        ],
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 28),

              // 2. Beneficiary Name
              TextFormField(
                controller: nameController,
                style: TextStyle(color: textColor),
                decoration: InputDecoration(
                  labelText: 'Beneficiary Name',
                  prefixIcon: const Icon(Icons.person_outline),
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(16)),
                ),
                validator: (val) {
                  if (val == null || val.trim().isEmpty) {
                    return 'Please enter beneficiary name';
                  }
                  return null;
                },
              ),
              const SizedBox(height: 20),

              // 3. IFSC Code
              TextFormField(
                controller: ifscController,
                style: TextStyle(color: textColor),
                maxLength: 11,
                textCapitalization: TextCapitalization.characters,
                decoration: InputDecoration(
                  labelText: 'IFSC Code',
                  prefixIcon: const Icon(Icons.account_balance_outlined),
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(16)),
                  counterText: '',
                  suffixIcon: isFetchingIfsc
                      ? const Padding(
                          padding: EdgeInsets.all(12),
                          child: CircularProgressIndicator(strokeWidth: 2),
                        )
                      : null,
                ),
                onChanged: (val) {
                  if (val.length == 11) {
                    _fetchBankDetails(val);
                  }
                },
                validator: (val) {
                  if (val == null || val.trim().isEmpty) {
                    return 'Please enter IFSC code';
                  }
                  if (val.length < 11) {
                    return 'IFSC code must be exactly 11 characters';
                  }
                  return null;
                },
              ),
              if (fetchError.isNotEmpty) ...[
                const SizedBox(height: 6),
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 4),
                  child: Text(
                    fetchError,
                    style: const TextStyle(color: Colors.red, fontSize: 11, fontWeight: FontWeight.bold),
                  ),
                ),
              ],
              const SizedBox(height: 20),

              // 4. Bank Name (pre-filled, read-only)
              TextFormField(
                controller: bankController,
                enabled: false,
                style: const TextStyle(color: Colors.grey),
                decoration: InputDecoration(
                  labelText: 'Resolved Bank Name',
                  prefixIcon: const Icon(Icons.location_city_outlined),
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(16)),
                  disabledBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(16),
                    borderSide: BorderSide(color: isDark ? const Color(0xFF2E3245) : const Color(0xFFE2E8F0)),
                  ),
                  fillColor: isDark ? const Color(0xFF141727) : const Color(0xFFF8FAFC),
                  filled: true,
                ),
              ),
              const SizedBox(height: 20),

              // 5. Account Number
              TextFormField(
                controller: accountController,
                keyboardType: TextInputType.number,
                style: TextStyle(color: textColor),
                inputFormatters: [FilteringTextInputFormatter.digitsOnly],
                decoration: InputDecoration(
                  labelText: 'Bank Account Number',
                  prefixIcon: const Icon(Icons.password_outlined),
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(16)),
                ),
                validator: (val) {
                  if (val == null || val.trim().isEmpty) {
                    return 'Please enter account number';
                  }
                  if (val.length < 8) {
                    return 'Account number should be at least 8 digits';
                  }
                  return null;
                },
              ),
              const SizedBox(height: 36),

              // 6. Action Button
              ElevatedButton(
                onPressed: _saveBeneficiary,
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF7C3AED),
                  foregroundColor: Colors.white,
                  minimumSize: const Size.fromHeight(54),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                  elevation: 0,
                ),
                child: const Text('Save Beneficiary', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
              ),
            ],
          ),
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
          child: Icon(Icons.business, color: Color(0xFF4361EE), size: 24),
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
