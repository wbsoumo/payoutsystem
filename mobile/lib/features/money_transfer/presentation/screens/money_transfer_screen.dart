import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:dio/dio.dart';
import '../../../auth/presentation/providers/auth_provider.dart';

class MoneyTransferScreen extends ConsumerStatefulWidget {
  const MoneyTransferScreen({Key? key}) : super(key: key);

  @override
  ConsumerState<MoneyTransferScreen> createState() => _MoneyTransferScreenState();
}

class _MoneyTransferScreenState extends ConsumerState<MoneyTransferScreen> {
  final _amountController = TextEditingController();
  final _pinController = TextEditingController();

  final List<Map<String, String>> _beneficiaries = [
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

  late Map<String, String> _selectedBeneficiaryData;
  String _selectedBeneficiary = 'Vijay Kumar';
  double _chargeRate = 5.00;
  double _commissionRate = 1.25;

  @override
  void initState() {
    super.initState();
    _selectedBeneficiaryData = _beneficiaries.first;
  }

  void _showAddBeneficiarySheet() {
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
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      builder: (context) {
        return StatefulBuilder(
          builder: (BuildContext context, StateSetter setModalState) {
            return Padding(
              padding: EdgeInsets.fromLTRB(24, 24, 24, MediaQuery.of(context).viewInsets.bottom + 24),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  const Text('Add New Beneficiary', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold), textAlign: TextAlign.center),
                  const SizedBox(height: 16),
                  
                  TextField(controller: nameController, decoration: const InputDecoration(labelText: 'Holder Name', border: OutlineInputBorder(borderRadius: BorderRadius.all(Radius.circular(12))))),
                  const SizedBox(height: 12),
                  
                  TextField(
                    controller: ifscController,
                    maxLength: 11,
                    textCapitalization: TextCapitalization.characters,
                    decoration: InputDecoration(
                      labelText: 'IFSC Code',
                      hintText: 'e.g. SBIN0004556',
                      counterText: '',
                      border: const OutlineInputBorder(borderRadius: BorderRadius.all(Radius.circular(12))),
                      suffixIcon: isFetchingIfsc ? const SizedBox(width: 20, height: 20, child: Padding(padding: EdgeInsets.all(12), child: CircularProgressIndicator(strokeWidth: 2))) : null,
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
                    Text(fetchError, style: const TextStyle(color: Colors.red, fontSize: 10, fontWeight: FontWeight.bold)),
                  ],
                  const SizedBox(height: 12),

                  // Bank details populated
                  Row(
                    children: [
                      if (resolvedLogo.isNotEmpty) ...[
                        ClipRRect(
                          borderRadius: BorderRadius.circular(8),
                          child: Image.network(resolvedLogo, width: 36, height: 36, fit: BoxFit.cover, errorBuilder: (c, o, s) => const Icon(Icons.account_balance, size: 36)),
                        ),
                        const SizedBox(width: 12),
                      ],
                      Expanded(
                        child: TextField(
                          controller: bankController,
                          decoration: const InputDecoration(
                            labelText: 'Bank Name & Branch',
                            border: OutlineInputBorder(borderRadius: BorderRadius.all(Radius.circular(12))),
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),

                  TextField(controller: accountController, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Account Number', border: OutlineInputBorder(borderRadius: BorderRadius.all(Radius.circular(12))))),
                  const SizedBox(height: 24),

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
                        setState(() {
                          _beneficiaries.add(newBen);
                          _selectedBeneficiaryData = newBen;
                          _selectedBeneficiary = newBen['name']!;
                        });
                        Navigator.pop(context);
                      }
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF4361EE),
                      foregroundColor: Colors.white,
                      minimumSize: const Size.fromHeight(50),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                    ),
                    child: const Text('Save & Select', style: TextStyle(fontWeight: FontWeight.bold)),
                  ),
                ],
              ),
            );
          },
        );
      },
    );
  }

  void _showSelectBeneficiarySheet() {
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
                  itemCount: _beneficiaries.length,
                  itemBuilder: (context, index) {
                    final b = _beneficiaries[index];
                    return ListTile(
                      leading: CircleAvatar(
                        backgroundColor: const Color(0xFFEFF6FF),
                        backgroundImage: b['logo'] != null ? NetworkImage(b['logo']!) : null,
                        child: b['logo'] == null ? const Icon(Icons.person, color: Color(0xFF4361EE)) : null,
                      ),
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

    // Show secure Transaction PIN modal confirmation
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      builder: (context) {
        return Padding(
          padding: EdgeInsets.fromLTRB(24, 24, 24, MediaQuery.of(context).viewInsets.bottom + 24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              const Text('Enter Transaction PIN', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold), textAlign: TextAlign.center),
              const SizedBox(height: 8),
              const Text('Enter your 6-digit secure PIN to authorize this transfer.', style: TextStyle(fontSize: 11, color: Colors.grey), textAlign: TextAlign.center),
              const SizedBox(height: 24),
              TextField(
                controller: _pinController,
                obscureText: true,
                keyboardType: TextInputType.number,
                maxLength: 6,
                textAlign: TextAlign.center,
                style: const TextStyle(fontSize: 24, letterSpacing: 16, fontWeight: FontWeight.bold),
                decoration: const InputDecoration(
                  counterText: '',
                  border: OutlineInputBorder(borderRadius: BorderRadius.all(Radius.circular(12))),
                  hintText: '••••••',
                ),
              ),
              const SizedBox(height: 24),
              ElevatedButton(
                onPressed: () async {
                  final pin = _pinController.text;
                  final isValid = await ref.read(authProvider.notifier).verifyPin(pin);

                  if (isValid) {
                    Navigator.pop(context); // Close sheet
                    _processPayout(amount);
                  } else {
                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(content: Text('Invalid Transaction PIN! Please try again.'), backgroundColor: Colors.red),
                    );
                  }
                },
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF4361EE),
                  foregroundColor: Colors.white,
                  minimumSize: const Size.fromHeight(50),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
                child: const Text('Authorize Transfer', style: TextStyle(fontWeight: FontWeight.bold)),
              ),
            ],
          ),
        );
      },
    );
  }

  void _processPayout(double amount) async {
    // Show premium processing spinner loading state
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => const Center(child: CircularProgressIndicator(color: Colors.white)),
    );

    await Future.delayed(const Duration(seconds: 2)); // Simulate API roundtrip
    Navigator.pop(context); // Dismiss spinner

    // Navigate to shareable receipt view
    context.pushReplacement(
      '/receipt?amount=$amount&beneficiary=$_selectedBeneficiary&ref=TXN${DateTime.now().millisecondsSinceEpoch}',
    );
  }

  @override
  Widget build(BuildContext context) {
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
            Card(
              child: ListTile(
                leading: CircleAvatar(
                  backgroundColor: const Color(0xFFEFF6FF),
                  backgroundImage: _selectedBeneficiaryData['logo'] != null ? NetworkImage(_selectedBeneficiaryData['logo']!) : null,
                  child: _selectedBeneficiaryData['logo'] == null ? const Icon(Icons.person, color: Color(0xFF4361EE)) : null,
                ),
                title: Text(_selectedBeneficiaryData['name']!, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                subtitle: Text('${_selectedBeneficiaryData['bank']} • ${_selectedBeneficiaryData['account']}', style: const TextStyle(fontSize: 11)),
                trailing: const Icon(Icons.arrow_drop_down),
                onTap: _showSelectBeneficiarySheet,
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
