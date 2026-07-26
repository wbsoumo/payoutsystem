import 'package:flutter/material.dart';

class BeneficiariesScreen extends StatefulWidget {
  const BeneficiariesScreen({Key? key}) : super(key: key);

  @override
  State<BeneficiariesScreen> createState() => _BeneficiariesScreenState();
}

class _BeneficiariesScreenState extends State<BeneficiariesScreen> {
  final List<Map<String, String>> _beneficiaries = [
    {'name': 'Vijay Kumar', 'bank': 'State Bank of India', 'account': '••••4556', 'ifsc': 'SBIN0004556'},
    {'name': 'Sanjay Singh', 'bank': 'HDFC Bank', 'account': '••••8990', 'ifsc': 'HDFC0001020'},
    {'name': 'Priya Sharma', 'bank': 'ICICI Bank', 'account': '••••1122', 'ifsc': 'ICIC0000045'},
  ];
  String _searchQuery = '';

  void _addBeneficiaryDialog() {
    final nameController = TextEditingController();
    final bankController = TextEditingController();
    final accountController = TextEditingController();
    final ifscController = TextEditingController();

    showDialog(
      context: context,
      builder: (context) {
        return AlertDialog(
          title: const Text('Add Beneficiary', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                TextField(controller: nameController, decoration: const InputDecoration(labelText: 'Holder Name')),
                const SizedBox(height: 8),
                TextField(controller: bankController, decoration: const InputDecoration(labelText: 'Bank Name')),
                const SizedBox(height: 8),
                TextField(controller: accountController, decoration: const InputDecoration(labelText: 'Account Number')),
                const SizedBox(height: 8),
                TextField(controller: ifscController, decoration: const InputDecoration(labelText: 'IFSC Code')),
              ],
            ),
          ),
          actions: [
            TextButton(onPressed: () => Navigator.pop(context), child: const Text('Cancel')),
            ElevatedButton(
              onPressed: () {
                if (nameController.text.isNotEmpty && accountController.text.isNotEmpty) {
                  setState(() {
                    _beneficiaries.add({
                      'name': nameController.text,
                      'bank': bankController.text.isEmpty ? 'Generic Bank' : bankController.text,
                      'account': '••••' + accountController.text.substring(accountController.text.length - 4),
                      'ifsc': ifscController.text.toUpperCase(),
                    });
                  });
                  Navigator.pop(context);
                }
              },
              child: const Text('Save'),
            ),
          ],
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    final filtered = _beneficiaries.where((b) {
      return b['name']!.toLowerCase().contains(_searchQuery.toLowerCase()) ||
          b['bank']!.toLowerCase().contains(_searchQuery.toLowerCase());
    }).toList();

    return Scaffold(
      appBar: AppBar(
        title: const Text('Beneficiaries Directory', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
        actions: [
          IconButton(icon: const Icon(Icons.add), onPressed: _addBeneficiaryDialog),
        ],
      ),
      body: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          children: [
            // Search Input
            TextField(
              decoration: const InputDecoration(
                hintText: 'Search beneficiary by name...',
                prefixIcon: Icon(Icons.search),
                border: OutlineInputBorder(borderRadius: BorderRadius.all(Radius.circular(12))),
              ),
              onChanged: (val) {
                setState(() {
                  _searchQuery = val;
                });
              },
            ),
            const SizedBox(height: 20),

            Expanded(
              child: ListView.builder(
                itemCount: filtered.length,
                itemBuilder: (context, index) {
                  final b = filtered[index];
                  return Card(
                    child: ListTile(
                      leading: const CircleAvatar(
                        backgroundColor: Color(0xFFEFF6FF),
                        child: Icon(Icons.person, color: Color(0xFF4361EE)),
                      ),
                      title: Text(b['name']!, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                      subtitle: Text('${b['bank']} • ${b['account']}\nIFSC: ${b['ifsc']}', style: const TextStyle(fontSize: 11)),
                      isThreeLine: true,
                      trailing: IconButton(
                        icon: const Icon(Icons.delete_outline, color: Colors.redAccent, size: 20),
                        onPressed: () {
                          setState(() {
                            _beneficiaries.remove(b);
                          });
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
