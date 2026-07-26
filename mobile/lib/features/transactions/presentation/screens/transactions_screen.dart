import 'package:flutter/material.dart';
import 'package:shimmer/shimmer.dart';
import 'package:dio/dio.dart';
import '../../../../core/network/api_client.dart';

class TransactionsScreen extends StatefulWidget {
  const TransactionsScreen({Key? key}) : super(key: key);

  @override
  State<TransactionsScreen> createState() => _TransactionsScreenState();
}

class _TransactionsScreenState extends State<TransactionsScreen> {
  final List<Map<String, dynamic>> _transactions = [];
  
  String _selectedStatus = 'all';
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadTransactions();
  }

  Future<void> _loadTransactions() async {
    final client = ApiClient();
    try {
      final response = await client.dio.get('/payouts');
      if (response.data['success'] == true) {
        final List<dynamic> payouts = response.data['payouts'] ?? [];
        if (mounted) {
          setState(() {
            _transactions.clear();
            _transactions.addAll(payouts.map((p) => Map<String, dynamic>.from(p)));
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

  void _showTransactionDetails(Map<String, dynamic> txn) {
    showDialog(
      context: context,
      builder: (context) {
        final isDark = Theme.of(context).brightness == Brightness.dark;
        final textColor = isDark ? Colors.white : const Color(0xFF1E293B);

        return AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
          title: Text(txn['ref'], style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: textColor)),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              ListTile(contentPadding: EdgeInsets.zero, title: const Text('Beneficiary', style: TextStyle(fontSize: 12)), subtitle: Text(txn['beneficiary'], style: TextStyle(fontWeight: FontWeight.bold, color: textColor))),
              ListTile(contentPadding: EdgeInsets.zero, title: const Text('Amount', style: TextStyle(fontSize: 12)), subtitle: Text(txn['amount'], style: TextStyle(fontWeight: FontWeight.bold, color: textColor))),
              ListTile(contentPadding: EdgeInsets.zero, title: const Text('Status', style: TextStyle(fontSize: 12)), subtitle: Text(txn['status'].toString().toUpperCase(), style: TextStyle(fontWeight: FontWeight.bold, color: textColor))),
              ListTile(contentPadding: EdgeInsets.zero, title: const Text('Timestamp', style: TextStyle(fontSize: 12)), subtitle: Text(txn['date'], style: TextStyle(fontWeight: FontWeight.bold, color: textColor))),
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

    final isDark = Theme.of(context).brightness == Brightness.dark;
    final textColor = isDark ? Colors.white : const Color(0xFF1E293B);
    final cardColor = isDark ? const Color(0xFF1E2235) : Colors.white;

    return Scaffold(
      appBar: AppBar(title: const Text('Payout Transaction History', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16))),
      body: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
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
                  onSelected: (val) => setState(() => _selectedStatus == 'success'),
                ),
                FilterChip(
                  label: const Text('Pending'),
                  selected: _selectedStatus == 'pending',
                  onSelected: (val) => setState(() => _selectedStatus == 'pending'),
                ),
                FilterChip(
                  label: const Text('Failed'),
                  selected: _selectedStatus == 'failed',
                  onSelected: (val) => setState(() => _selectedStatus == 'failed'),
                ),
              ],
            ),
            const SizedBox(height: 24),

            Expanded(
              child: _isLoading
                  ? ListView.separated(
                      itemCount: 4,
                      separatorBuilder: (c, i) => const SizedBox(height: 12),
                      itemBuilder: (context, index) {
                        return Shimmer.fromColors(
                          baseColor: isDark ? const Color(0xFF1E2235) : Colors.grey.shade300,
                          highlightColor: isDark ? const Color(0xFF2E3245) : Colors.grey.shade100,
                          child: Container(
                            height: 72,
                            decoration: BoxDecoration(
                              color: cardColor,
                              borderRadius: BorderRadius.circular(20),
                            ),
                          ),
                        );
                      },
                    )
                  : ListView.separated(
                      itemCount: filtered.length,
                      separatorBuilder: (c, i) => const SizedBox(height: 12),
                      itemBuilder: (context, index) {
                        final t = filtered[index];
                        final isSuccess = t['status'] == 'success';
                        final isPending = t['status'] == 'pending';

                        return Container(
                          padding: const EdgeInsets.all(12),
                          decoration: BoxDecoration(
                            color: cardColor,
                            borderRadius: BorderRadius.circular(20),
                            border: Border.all(color: isDark ? const Color(0xFF2E3245) : const Color(0xFFE2E8F0)),
                          ),
                          child: ListTile(
                            contentPadding: EdgeInsets.zero,
                            leading: CircleAvatar(
                              radius: 20,
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
                            title: Text(t['beneficiary'], style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: textColor)),
                            subtitle: Text('${t['ref']} • ${t['date']}', style: const TextStyle(fontSize: 10)),
                            trailing: Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              crossAxisAlignment: CrossAxisAlignment.end,
                              children: [
                                Text(t['amount'], style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: textColor)),
                                const SizedBox(height: 2),
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
