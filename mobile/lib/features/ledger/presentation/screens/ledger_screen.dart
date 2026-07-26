import 'package:flutter/material.dart';
import 'package:dio/dio.dart';
import 'package:shimmer/shimmer.dart';
import '../../../../core/network/api_client.dart';

class LedgerScreen extends StatefulWidget {
  const LedgerScreen({Key? key}) : super(key: key);

  @override
  State<LedgerScreen> createState() => _LedgerScreenState();
}

class _LedgerScreenState extends State<LedgerScreen> {
  final List<Map<String, dynamic>> _ledgerLogs = [];
  String _openingBalance = '₹0.00';
  String _closingBalance = '₹0.00';
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadLedgerLogs();
  }

  Future<void> _loadLedgerLogs() async {
    final client = ApiClient();
    try {
      final response = await client.dio.get('/wallet/ledger');
      if (response.data['success'] == true) {
        final List<dynamic> logs = response.data['logs'] ?? [];
        if (mounted) {
          setState(() {
            _ledgerLogs.clear();
            _ledgerLogs.addAll(logs.map((l) => Map<String, dynamic>.from(l)));
            _openingBalance = response.data['opening_balance'] ?? '₹0.00';
            _closingBalance = response.data['closing_balance'] ?? '₹0.00';
            _isLoading = false;
          });
        }
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _isLoading = false;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final cardColor = isDark ? const Color(0xFF1E2235) : Colors.white;

    return Scaffold(
      appBar: AppBar(title: const Text('Wallet Ledger logs', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16))),
      body: _isLoading
          ? Center(
              child: Shimmer.fromColors(
                baseColor: isDark ? const Color(0xFF1E2235) : Colors.grey.shade300,
                highlightColor: isDark ? const Color(0xFF2E3245) : Colors.grey.shade100,
                child: Padding(
                  padding: const EdgeInsets.all(24),
                  child: Column(
                    children: [
                      Container(height: 80, decoration: BoxDecoration(color: cardColor, borderRadius: BorderRadius.circular(16))),
                      const SizedBox(height: 24),
                      Expanded(
                        child: ListView.separated(
                          itemCount: 3,
                          separatorBuilder: (c, i) => const SizedBox(height: 12),
                          itemBuilder: (context, index) => Container(height: 64, decoration: BoxDecoration(color: cardColor, borderRadius: BorderRadius.circular(16))),
                        ),
                      )
                    ],
                  ),
                ),
              ),
            )
          : SingleChildScrollView(
              padding: const EdgeInsets.all(24),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  // Opening/Closing summary card
                  Card(
                    color: const Color(0xFFEFF6FF),
                    child: Padding(
                      padding: const EdgeInsets.all(20),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const Text('Opening Balance', style: TextStyle(fontSize: 10, color: Colors.black54)),
                              const SizedBox(height: 4),
                              Text(_openingBalance, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.black)),
                            ],
                          ),
                          const Icon(Icons.arrow_forward, color: Colors.grey),
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.end,
                            children: [
                              const Text('Closing Balance', style: TextStyle(fontSize: 10, color: Colors.black54)),
                              const SizedBox(height: 4),
                              Text(_closingBalance, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Color(0xFF4361EE))),
                            ],
                          ),
                        ],
                      ),
                    ),
                  ),
                  const SizedBox(height: 28),

                  const Text('JOURNAL TRANSACTIONS', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Colors.grey)),
                  const SizedBox(height: 12),

                  if (_ledgerLogs.isEmpty)
                    Card(
                      child: Padding(
                        padding: const EdgeInsets.all(32),
                        child: Column(
                          children: [
                            Icon(Icons.library_books_outlined, color: Colors.grey.shade400, size: 48),
                            const SizedBox(height: 12),
                            const Text('No ledger entries recorded yet.', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: Colors.grey)),
                          ],
                        ),
                      ),
                    )
                  else
                    Card(
                      child: ListView.separated(
                        shrinkWrap: true,
                        physics: const NeverScrollableScrollPhysics(),
                        itemCount: _ledgerLogs.length,
                        separatorBuilder: (context, index) => const Divider(height: 1),
                        itemBuilder: (context, index) {
                          final log = _ledgerLogs[index];
                          final isCredit = log['type'] == 'credit' || log['type'] == 'refund';

                          return ListTile(
                            leading: CircleAvatar(
                              backgroundColor: isCredit ? const Color(0xFFDCFCE7) : const Color(0xFFFEE2E2),
                              child: Icon(
                                isCredit ? Icons.add_circle_outline : Icons.remove_circle_outline,
                                color: isCredit ? Colors.green : Colors.red,
                                size: 20,
                              ),
                            ),
                            title: Text(log['desc'] ?? 'N/A', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
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
