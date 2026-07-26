import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
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
    {'name': 'Vijay Kumar', 'bank': 'State Bank of India', 'account': '••••4556', 'ifsc': 'SBIN0004556'},
    {'name': 'Sanjay Singh', 'bank': 'HDFC Bank', 'account': '••••8990', 'ifsc': 'HDFC0001020'},
    {'name': 'Priya Sharma', 'bank': 'ICICI Bank', 'account': '••••1122', 'ifsc': 'ICIC0000045'},
  ];

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
              const Text('Add New Beneficiary', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold), textAlign: TextAlign.center),
              const SizedBox(height: 16),
              TextField(controller: nameController, decoration: const InputDecoration(labelText: 'Holder Name', border: OutlineInputBorder())),
              const SizedBox(height: 12),
              TextField(controller: bankController, decoration: const InputDecoration(labelText: 'Bank Name', border: OutlineInputBorder())),
              const SizedBox(height: 12),
              TextField(controller: accountController, decoration: const InputDecoration(labelText: 'Account Number', border: OutlineInputBorder())),
              const SizedBox(height: 12),
              TextField(controller: ifscController, decoration: const InputDecoration(labelText: 'IFSC Code', border: OutlineInputBorder())),
              const SizedBox(height: 24),
              ElevatedButton(
                onPressed: () {
                  if (nameController.text.isNotEmpty && accountController.text.isNotEmpty) {
                    final newBen = {
                      'name': nameController.text,
                      'bank': bankController.text.isEmpty ? 'Generic Bank' : bankController.text,
                      'account': '••••' + accountController.text.substring(accountController.text.length - 4),
                      'ifsc': ifscController.text.toUpperCase(),
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
                      leading: const CircleAvatar(backgroundColor: Color(0xFFEFF6FF), child: Icon(Icons.person, color: Color(0xFF4361EE))),
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
                leading: const CircleAvatar(backgroundColor: Color(0xFFEFF6FF), child: Icon(Icons.person, color: Color(0xFF4361EE))),
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
