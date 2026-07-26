import 'package:flutter/material.dart';
import 'package:shimmer/shimmer.dart';
import 'package:dio/dio.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/network/api_client.dart';

class TransactionsScreen extends StatefulWidget {
  const TransactionsScreen({Key? key}) : super(key: key);

  @override
  State<TransactionsScreen> createState() => _TransactionsScreenState();
}

class _TransactionsScreenState extends State<TransactionsScreen> {
  final List<Map<String, dynamic>> _transactions = [];
  String _selectedStatus = 'all';
  String _searchQuery = '';
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
    context.push('/transaction-detail', extra: txn);
  }

  Widget _buildFilterButton(String label, String statusKey) {
    final isSelected = _selectedStatus == statusKey;
    final isDark = Theme.of(context).brightness == Brightness.dark;
    
    final activeColor = const Color(0xFF2563EB); // Royal Blue
    final borderColor = isDark ? const Color(0xFF2E3245) : const Color(0xFF0F172A);
    final textColor = isSelected 
        ? Colors.white 
        : (isDark ? Colors.white : const Color(0xFF0F172A));

    return GestureDetector(
      onTap: () {
        setState(() {
          _selectedStatus = statusKey;
        });
      },
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 10),
        decoration: BoxDecoration(
          color: isSelected ? activeColor : Colors.transparent,
          borderRadius: BorderRadius.circular(10),
          border: Border.all(
            color: isSelected ? activeColor : borderColor,
            width: 1.2,
          ),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            if (isSelected) ...[
              const Icon(Icons.check, size: 14, color: Colors.white),
              const SizedBox(width: 6),
            ],
            Text(
              label,
              style: TextStyle(
                fontSize: 12,
                fontWeight: FontWeight.bold,
                color: textColor,
              ),
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final filtered = _transactions.where((t) {
      final tStatus = (t['status'] ?? '').toString().toLowerCase();
      final matchesStatus = _selectedStatus == 'all' || tStatus == _selectedStatus;
      
      final beneficiary = (t['beneficiary'] ?? '').toString().toLowerCase();
      final refId = (t['ref'] ?? '').toString().toLowerCase();
      final query = _searchQuery.toLowerCase();
      final matchesSearch = beneficiary.contains(query) || refId.contains(query);
      
      return matchesStatus && matchesSearch;
    }).toList();

    final isDark = Theme.of(context).brightness == Brightness.dark;
    final textColor = isDark ? Colors.white : const Color(0xFF1E293B);
    final cardColor = isDark ? const Color(0xFF1E2235) : Colors.white;
    final borderStyleColor = isDark ? const Color(0xFF2E3245) : const Color(0xFFE2E8F0);
    final searchBgColor = isDark ? const Color(0xFF1E2235) : const Color(0xFFF1F5F9);

    return Scaffold(
      backgroundColor: isDark ? const Color(0xFF0F172A) : const Color(0xFFF8FAFC),
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: IconButton(
          icon: Icon(Icons.arrow_back, color: textColor),
          onPressed: () => Navigator.pop(context),
        ),
        title: Text(
          'Payout Transaction History',
          style: TextStyle(color: textColor, fontWeight: FontWeight.bold, fontSize: 16),
        ),
        centerTitle: true,
      ),
      body: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 8),
        child: Column(
          children: [
            // Search Input field
            Container(
              margin: const EdgeInsets.only(bottom: 16),
              decoration: BoxDecoration(
                color: searchBgColor,
                borderRadius: BorderRadius.circular(14),
                border: Border.all(color: borderStyleColor),
              ),
              child: TextField(
                style: TextStyle(color: textColor, fontSize: 12),
                decoration: InputDecoration(
                  hintText: 'Search by beneficiary name or reference ID...',
                  hintStyle: const TextStyle(color: Colors.grey, fontSize: 12),
                  prefixIcon: const Icon(Icons.search, color: Colors.grey, size: 18),
                  suffixIcon: _searchQuery.isNotEmpty
                      ? IconButton(
                          icon: const Icon(Icons.clear, color: Colors.grey, size: 16),
                          onPressed: () {
                            setState(() {
                              _searchQuery = '';
                            });
                          },
                        )
                      : null,
                  border: InputBorder.none,
                  contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                ),
                onChanged: (val) {
                  setState(() {
                    _searchQuery = val;
                  });
                },
              ),
            ),

            // Premium Custom Filter Buttons Row
            SingleChildScrollView(
              scrollDirection: Axis.horizontal,
              child: Row(
                children: [
                  _buildFilterButton('All', 'all'),
                  const SizedBox(width: 8),
                  _buildFilterButton('Success', 'success'),
                  const SizedBox(width: 8),
                  _buildFilterButton('Pending', 'pending'),
                  const SizedBox(width: 8),
                  _buildFilterButton('Failed', 'failed'),
                ],
              ),
            ),
            const SizedBox(height: 20),

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
                  : RefreshIndicator(
                      onRefresh: _loadTransactions,
                      child: filtered.isEmpty
                          ? ListView(
                              children: const [
                                SizedBox(height: 64),
                                Center(
                                  child: Text(
                                    'No transactions found matching criteria.',
                                    style: TextStyle(color: Colors.grey, fontSize: 12),
                                    textAlign: TextAlign.center,
                                  ),
                                )
                              ],
                            )
                          : ListView.separated(
                              physics: const AlwaysScrollableScrollPhysics(),
                              itemCount: filtered.length,
                              separatorBuilder: (c, i) => const SizedBox(height: 12),
                              itemBuilder: (context, index) {
                                final t = filtered[index];
                                final statusVal = (t['status'] ?? '').toString().toLowerCase();
                                final isSuccess = statusVal == 'success';
                                final isPending = statusVal == 'pending';

                                return GestureDetector(
                                  onTap: () => _showTransactionDetails(t),
                                  child: Container(
                                    padding: const EdgeInsets.all(16),
                                    decoration: BoxDecoration(
                                      color: cardColor,
                                      borderRadius: BorderRadius.circular(20),
                                      border: Border.all(color: borderStyleColor),
                                    ),
                                    child: Row(
                                      children: [
                                        CircleAvatar(
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
                                        const SizedBox(width: 12),
                                        Expanded(
                                          child: Column(
                                            crossAxisAlignment: CrossAxisAlignment.start,
                                            children: [
                                              Text(
                                                t['beneficiary'] ?? 'Unknown Beneficiary', 
                                                style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: textColor)
                                              ),
                                              const SizedBox(height: 4),
                                              Text(
                                                '${t['ref']} • ${t['date']}', 
                                                style: const TextStyle(fontSize: 10, color: Colors.grey)
                                              ),
                                            ],
                                          ),
                                        ),
                                        Column(
                                          crossAxisAlignment: CrossAxisAlignment.end,
                                          children: [
                                            Text(
                                              t['amount'] ?? '₹0.00', 
                                              style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: textColor)
                                            ),
                                            const SizedBox(height: 4),
                                            Text(
                                              statusVal.toUpperCase(),
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
                                      ],
                                    ),
                                  ),
                                );
                              },
                            ),
                    ),
            ),
          ],
        ),
      ),
    );
  }
}
