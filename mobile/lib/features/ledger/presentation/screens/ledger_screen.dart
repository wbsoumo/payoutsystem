import 'package:flutter/material.dart';

class LedgerScreen extends StatelessWidget {
  const LedgerScreen({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final List<Map<String, dynamic>> ledgerLogs = [
      {'type': 'credit', 'amount': '₹10,000.00', 'desc': 'Bank Settlement Credit', 'date': 'Today, 14:23', 'bal': '₹11,100.00'},
      {'type': 'debit', 'amount': '₹5,005.00', 'desc': 'Payout HDFC bank', 'date': 'Yesterday, 18:10', 'bal': '₹1,100.00'},
      {'type': 'credit', 'amount': '₹12.50', 'desc': 'Commission Earned Refund', 'date': 'Yesterday, 18:10', 'bal': '₹6,105.00'},
    ];

    return Scaffold(
      appBar: AppBar(title: const Text('Wallet Ledger logs', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16))),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // Opening/Closing summary card
            Card(
              color: const Color(0xFFEFF6FF),
              child: const Padding(
                padding: EdgeInsets.all(20),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('Opening Balance', style: TextStyle(fontSize: 10, color: Colors.black54)),
                        SizedBox(height: 4),
                        Text('₹6,105.00', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                      ],
                    ),
                    Icon(Icons.arrow_forward, color: Colors.grey),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.end,
                      children: [
                        Text('Closing Balance', style: TextStyle(fontSize: 10, color: Colors.black54)),
                        SizedBox(height: 4),
                        Text('₹11,100.00', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Color(0xFF4361EE))),
                      ],
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 28),

            const Text('JOURNAL TRANSACTIONS', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Colors.grey)),
            const SizedBox(height: 12),

            Card(
              child: ListView.separated(
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                itemCount: ledgerLogs.length,
                separatorBuilder: (context, index) => const Divider(height: 1),
                itemBuilder: (context, index) {
                  final log = ledgerLogs[index];
                  final isCredit = log['type'] == 'credit';

                  return ListTile(
                    leading: CircleAvatar(
                      backgroundColor: isCredit ? const Color(0xFFDCFCE7) : const Color(0xFFFEE2E2),
                      child: Icon(
                        isCredit ? Icons.add_circle_outline : Icons.remove_circle_outline,
                        color: isCredit ? Colors.green : Colors.red,
                        size: 20,
                      ),
                    ),
                    title: Text(log['desc'], style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                    subtitle: Text('Bal: ${log['bal']} • ${log['date']}', style: const TextStyle(fontSize: 10)),
                    trailing: Text(
                      '${isCredit ? '+' : '-'}${log['amount']}',
                      style: TextStyle(
                        fontWeight: FontWeight.bold,
                        fontSize: 13,
                        color: isCredit ? Colors.green : Colors.red,
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
