import 'package:flutter/material.dart';

class TransactionsScreen extends StatefulWidget {
  const TransactionsScreen({Key? key}) : super(key: key);

  @override
  State<TransactionsScreen> createState() => _TransactionsScreenState();
}

class _TransactionsScreenState extends State<TransactionsScreen> {
  final List<Map<String, dynamic>> _transactions = [
    {'ref': 'TXN178923011', 'beneficiary': 'Vijay Kumar', 'amount': '₹5,000.00', 'status': 'success', 'date': 'Today, 14:20'},
    {'ref': 'TXN178923012', 'beneficiary': 'Sanjay Singh', 'amount': '₹2,500.00', 'status': 'pending', 'date': 'Today, 11:05'},
    {'ref': 'TXN178923013', 'beneficiary': 'Priya Sharma', 'amount': '₹1,200.00', 'status': 'failed', 'date': 'Yesterday, 17:35'},
  ];
  String _selectedStatus = 'all';

  void _showTransactionDetails(Map<String, dynamic> txn) {
    showDialog(
      context: context,
      builder: (context) {
        return AlertDialog(
          title: Text(txn['ref'], style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              ListTile(title: const Text('Beneficiary'), subtitle: Text(txn['beneficiary'])),
              ListTile(title: const Text('Amount'), subtitle: Text(txn['amount'])),
              ListTile(title: const Text('Status'), subtitle: Text(txn['status'].toString().toUpperCase())),
              ListTile(title: const Text('Timestamp'), subtitle: Text(txn['date'])),
            ],
          ),
          actions: [
            TextButton(onPressed: () => Navigator.pop(context), child: const Text('Close')),
          ],
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    final filtered = _transactions.where((t) {
      if (_selectedStatus == 'all') return true;
      return t['status'] == _selectedStatus;
    }).toList();

    return Scaffold(
      appBar: AppBar(title: const Text('Payout Transaction History', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16))),
      body: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          children: [
            // Filter chips
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceEvenly,
              children: [
                FilterChip(
                  label: const Text('All'),
                  selected: _selectedStatus == 'all',
                  onSelected: (val) => setState(() => _selectedStatus = 'all'),
                ),
                FilterChip(
                  label: const Text('Success'),
                  selected: _selectedStatus == 'success',
                  onSelected: (val) => setState(() => _selectedStatus = 'success'),
                ),
                FilterChip(
                  label: const Text('Pending'),
                  selected: _selectedStatus == 'pending',
                  onSelected: (val) => setState(() => _selectedStatus = 'pending'),
                ),
                FilterChip(
                  label: const Text('Failed'),
                  selected: _selectedStatus == 'failed',
                  onSelected: (val) => setState(() => _selectedStatus = 'failed'),
                ),
              ],
            ),
            const SizedBox(height: 20),

            Expanded(
              child: ListView.builder(
                itemCount: filtered.length,
                itemBuilder: (context, index) {
                  final t = filtered[index];
                  final isSuccess = t['status'] == 'success';
                  final isPending = t['status'] == 'pending';

                  return Card(
                    child: ListTile(
                      leading: CircleAvatar(
                        backgroundColor: isSuccess
                            ? const Color(0xFFDCFCE7)
                            : isPending
                                ? const Color(0xFFFEF3C7)
                                : const Color(0xFFFEE2E2),
                        child: Icon(
                          isSuccess
                              ? Icons.check
                              : isPending
                                  ? Icons.access_time
                                  : Icons.close,
                          color: isSuccess
                              ? Colors.green
                              : isPending
                                  ? Colors.amber
                                  : Colors.red,
                          size: 18,
                        ),
                      ),
                      title: Text(t['beneficiary'], style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                      subtitle: Text('${t['ref']} • ${t['date']}', style: const TextStyle(fontSize: 10)),
                      trailing: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        crossAxisAlignment: CrossAxisAlignment.end,
                        children: [
                          Text(t['amount'], style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                          Text(
                            t['status'].toString().toUpperCase(),
                            style: TextStyle(
                              fontSize: 9,
                              fontWeight: FontWeight.bold,
                              color: isSuccess
                                  ? Colors.green
                                  : isPending
                                      ? Colors.amber
                                      : Colors.red,
                            ),
                          ),
                        ],
                      ),
                      onTap: () => _showTransactionDetails(t),
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
