import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:dio/dio.dart';
import '../../../auth/presentation/providers/auth_provider.dart';
import '../../../beneficiaries/presentation/providers/beneficiary_provider.dart';
import '../../../../core/network/api_client.dart';
import '../../../../core/constants/endpoints.dart';

class MoneyTransferScreen extends ConsumerStatefulWidget {
  final String? initialBeneficiaryName;
  const MoneyTransferScreen({Key? key, this.initialBeneficiaryName}) : super(key: key);

  @override
  ConsumerState<MoneyTransferScreen> createState() => _MoneyTransferScreenState();
}

class _MoneyTransferScreenState extends ConsumerState<MoneyTransferScreen> {
  final _amountController = TextEditingController();
  final _pinController = TextEditingController();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (widget.initialBeneficiaryName != null) {
        final beneficiaries = ref.read(beneficiaryProvider);
        for (final b in beneficiaries) {
          if (b['name'] == widget.initialBeneficiaryName) {
            setState(() {
              _selectedBeneficiaryData = Map<String, String>.from(b);
              _selectedBeneficiary = b['name']!;
            });
            break;
          }
        }
      }
    });
  }

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

  Map<String, String>? _selectedBeneficiaryData;
  String _selectedBeneficiary = 'Vijay Kumar';
  double _chargeRate = 5.00;
  double _commissionRate = 1.25;

  Future<void> _showAddBeneficiarySheet() async {
    final result = await context.push('/add-beneficiary');
    if (result != null && result is Map) {
      setState(() {
        _selectedBeneficiaryData = Map<String, String>.from(result.cast<String, String>());
        _selectedBeneficiary = result['name']!;
      });
    }
  }

  void _showSelectBeneficiarySheet() {
    final beneficiaries = ref.read(beneficiaryProvider);

    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      builder: (context) {
        return Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              const Text('Select Beneficiary', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold), textAlign: TextAlign.center),
              const SizedBox(height: 16),
              Expanded(
                child: ListView.builder(
                  shrinkWrap: true,
                  itemCount: beneficiaries.length,
                  itemBuilder: (context, index) {
                    final b = beneficiaries[index];
                    return ListTile(
                      leading: SafeBankLogo(logoUrl: b['logo'], bankName: b['bank'] ?? ''),
                      title: Text(b['name']!, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                      subtitle: Text('${b['bank']} • ${b['account']}', style: const TextStyle(fontSize: 10)),
                      onTap: () {
                        setState(() {
                          _selectedBeneficiaryData = b;
                          _selectedBeneficiary = b['name']!;
                        });
                        Navigator.pop(context);
                      },
                    );
                  },
                ),
              ),
            ],
          ),
        );
      },
    );
  }

  void _confirmTransfer() {
    if (_amountController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please enter an amount.')),
      );
      return;
    }

    final amount = double.tryParse(_amountController.text) ?? 0;
    if (amount <= 0) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please enter a valid amount.')),
      );
      return;
    }

    // Show secure Transaction PIN modal confirmation with custom numeric keypad
    String enteredPin = '';
    String validationError = '';

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Theme.of(context).scaffoldBackgroundColor,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(28))),
      builder: (context) {
        return StatefulBuilder(
          builder: (BuildContext context, StateSetter setModalState) {
            final isDark = Theme.of(context).brightness == Brightness.dark;
            final textColor = isDark ? Colors.white : const Color(0xFF1E293B);

            void handleKeyPress(String key) async {
              if (enteredPin.length < 6) {
                HapticFeedback.mediumImpact();
                setModalState(() {
                  enteredPin += key;
                  validationError = '';
                });

                if (enteredPin.length == 6) {
                  // Trigger validation automatically
                  final errorMsg = await ref.read(authProvider.notifier).verifyPin(enteredPin);
                  if (errorMsg == null) {
                    Navigator.pop(context); // Close bottom sheet
                    _processPayout(amount);
                  } else {
                    HapticFeedback.vibrate();
                    setModalState(() {
                      enteredPin = '';
                      validationError = errorMsg;
                    });
                  }
                }
              }
            }

            void handleBackspace() {
              if (enteredPin.isNotEmpty) {
                HapticFeedback.lightImpact();
                setModalState(() {
                  enteredPin = enteredPin.substring(0, enteredPin.length - 1);
                  validationError = '';
                });
              }
            }

            return Padding(
              padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 24),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  Center(
                    child: Container(
                      width: 48,
                      height: 5,
                      decoration: BoxDecoration(color: Colors.grey.withOpacity(0.3), borderRadius: BorderRadius.circular(10)),
                    ),
                  ),
                  const SizedBox(height: 20),
                  Text('Enter Transaction PIN', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: textColor), textAlign: TextAlign.center),
                  const SizedBox(height: 8),
                  Text('Enter your 6-digit secure PIN to authorize transfer of ₹${amount.toStringAsFixed(2)}', style: const TextStyle(fontSize: 11, color: Colors.grey), textAlign: TextAlign.center),
                  const SizedBox(height: 28),

                  // Pin visual indicators (6 circles)
                  Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: List.generate(6, (index) {
                      final isFilled = index < enteredPin.length;
                      return AnimatedContainer(
                        duration: const Duration(milliseconds: 150),
                        margin: const EdgeInsets.symmetric(horizontal: 10),
                        width: 14,
                        height: 14,
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          color: isFilled ? const Color(0xFF7C3AED) : Colors.transparent,
                          border: Border.all(color: isFilled ? const Color(0xFF7C3AED) : Colors.grey.shade400, width: 2),
                        ),
                      );
                    }),
                  ),
                  const SizedBox(height: 16),
                  if (validationError.isNotEmpty)
                    Text(validationError, style: const TextStyle(color: Colors.redAccent, fontSize: 11, fontWeight: FontWeight.bold), textAlign: TextAlign.center),
                  const SizedBox(height: 28),

                  // Grid Custom Keypad
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 12),
                    child: Column(
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceAround,
                          children: ['1', '2', '3'].map((digit) => KeypadButton(label: digit, onTap: () => handleKeyPress(digit))).toList(),
                        ),
                        const SizedBox(height: 16),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceAround,
                          children: ['4', '5', '6'].map((digit) => KeypadButton(label: digit, onTap: () => handleKeyPress(digit))).toList(),
                        ),
                        const SizedBox(height: 16),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceAround,
                          children: ['7', '8', '9'].map((digit) => KeypadButton(label: digit, onTap: () => handleKeyPress(digit))).toList(),
                        ),
                        const SizedBox(height: 16),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceAround,
                          children: [
                            const SizedBox(width: 64, height: 64), // Empty space for layout balance
                            KeypadButton(label: '0', onTap: () => handleKeyPress('0')),
                            IconButton(
                              icon: const Icon(Icons.backspace_outlined, size: 24, color: Colors.grey),
                              onPressed: handleBackspace,
                              style: IconButton.styleFrom(
                                minimumSize: const Size(64, 64),
                                shape: const CircleBorder(),
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            );
          },
        );
      },
    );
  }

  void _processPayout(double amount) async {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => const Center(child: CircularProgressIndicator(color: Colors.white)),
    );

    final client = ApiClient();
    try {
      final response = await client.dio.post(
        '/payouts',
        data: {
          'client_reference_id': 'ref_${DateTime.now().millisecondsSinceEpoch}',
          'amount': amount,
          'bank_name': _selectedBeneficiaryData!['bank'],
          'bank_account_number': _selectedBeneficiaryData!['account']!.replaceAll('••••', '9999'),
          'bank_ifsc': _selectedBeneficiaryData!['ifsc'],
          'bank_holder_name': _selectedBeneficiaryData!['name'],
        },
      );

      Navigator.pop(context); // Dismiss spinner

      if (response.data['success'] == true) {
        final refId = response.data['reference_id'] ?? 'TXN${DateTime.now().millisecondsSinceEpoch}';
        context.pushReplacement(
          '/receipt?amount=$amount&beneficiary=$_selectedBeneficiary&ref=$refId',
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(response.data['error'] ?? response.data['error_message'] ?? 'Payout failed.'), backgroundColor: Colors.red),
        );
      }
    } catch (e) {
      Navigator.pop(context); // Dismiss spinner
      String errorMsg = 'An error occurred during payout.';
      if (e is DioException && e.response != null) {
        final data = e.response!.data;
        if (data is Map) {
          errorMsg = data['error'] ?? data['error_message'] ?? data['message'] ?? errorMsg;
        }
      }
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(errorMsg), backgroundColor: Colors.red),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final beneficiaries = ref.watch(beneficiaryProvider);
    if (_selectedBeneficiaryData == null && beneficiaries.isNotEmpty) {
      _selectedBeneficiaryData = beneficiaries.first;
      _selectedBeneficiary = beneficiaries.first['name']!;
    }

    return Scaffold(
      appBar: AppBar(title: const Text('New Money Transfer', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16))),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // Beneficiary Selection card
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text('SELECT BENEFICIARY', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey)),
                TextButton.icon(
                  icon: const Icon(Icons.add, size: 14),
                  label: const Text('Add New', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold)),
                  onPressed: _showAddBeneficiarySheet,
                  style: TextButton.styleFrom(padding: EdgeInsets.zero, minimumSize: Size.zero, tapTargetSize: MaterialTapTargetSize.shrinkWrap),
                ),
              ],
            ),
            const SizedBox(height: 8),
            if (_selectedBeneficiaryData != null)
              Card(
                child: ListTile(
                  leading: SafeBankLogo(logoUrl: _selectedBeneficiaryData!['logo'], bankName: _selectedBeneficiaryData!['bank'] ?? ''),
                  title: Text(_selectedBeneficiaryData!['name']!, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                  subtitle: Text('${_selectedBeneficiaryData!['bank']} • ${_selectedBeneficiaryData!['account']}', style: const TextStyle(fontSize: 11)),
                  trailing: const Icon(Icons.arrow_drop_down),
                  onTap: _showSelectBeneficiarySheet,
                ),
              )
            else
              Card(
                child: ListTile(
                  leading: const CircleAvatar(backgroundColor: Color(0xFFF3F4F6), child: Icon(Icons.person_add, color: Colors.grey)),
                  title: const Text('No Beneficiary Selected', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: Colors.grey)),
                  subtitle: const Text('Tap "Add New" above to configure', style: TextStyle(fontSize: 11)),
                  onTap: _showAddBeneficiarySheet,
                ),
              ),
            const SizedBox(height: 24),

            // Amount input Card
            Card(
              child: Padding(
                padding: const EdgeInsets.all(20),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    const Text('TRANSFER AMOUNT', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey)),
                    const SizedBox(height: 12),
                    TextField(
                      controller: _amountController,
                      keyboardType: const TextInputType.numberWithOptions(decimal: true),
                      style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
                      decoration: const InputDecoration(
                        prefixText: '₹ ',
                        hintText: '0.00',
                        border: InputBorder.none,
                      ),
                      onChanged: (value) {
                        setState(() {});
                      },
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 24),

            // Charges & Commission info block
            if (_amountController.text.isNotEmpty)
              Card(
                color: const Color(0xFFF8FAFC),
                child: Padding(
                  padding: const EdgeInsets.all(20),
                  child: Column(
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Text('Transaction Payout Amount', style: TextStyle(fontSize: 11, color: Colors.black54)),
                          Text('₹${_amountController.text}', style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold)),
                        ],
                      ),
                      const SizedBox(height: 12),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Text('Convenience Charges (+)', style: TextStyle(fontSize: 11, color: Colors.redAccent)),
                          Text('₹$_chargeRate', style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold)),
                        ],
                      ),
                      const SizedBox(height: 12),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Text('Commission Earned (-)', style: TextStyle(fontSize: 11, color: Colors.green)),
                          Text('₹$_commissionRate', style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold)),
                        ],
                      ),
                      const Divider(height: 24),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Text('Net Wallet Debit', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold)),
                          Text('₹${(double.tryParse(_amountController.text) ?? 0) + _chargeRate - _commissionRate}',
                               style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w800, color: Color(0xFF4361EE))),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
            const SizedBox(height: 36),

            ElevatedButton(
              onPressed: _confirmTransfer,
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFF4361EE),
                foregroundColor: Colors.white,
                minimumSize: const Size.fromHeight(50),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              ),
              child: const Text('Continue to PIN Validation', style: TextStyle(fontWeight: FontWeight.bold)),
            ),
          ],
        ),
      ),
    );
  }
}

class KeypadButton extends StatelessWidget {
  final String label;
  final VoidCallback onTap;

  const KeypadButton({
    Key? key,
    required this.label,
    required this.onTap,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(40),
      child: Container(
        width: 68,
        height: 68,
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          color: isDark ? const Color(0xFF1E2235) : Colors.grey.shade100,
          border: Border.all(color: isDark ? const Color(0xFF2E3245) : Colors.grey.shade200),
        ),
        alignment: Alignment.center,
        child: Text(
          label,
          style: TextStyle(
            fontSize: 22,
            fontWeight: FontWeight.w600,
            color: isDark ? Colors.white : const Color(0xFF1E293B),
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
