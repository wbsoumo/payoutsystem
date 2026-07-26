import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter/foundation.dart';
import 'package:dio/dio.dart';
import 'package:shimmer/shimmer.dart';
import 'package:share_plus/share_plus.dart';
import 'package:path_provider/path_provider.dart';
import 'dart:io';
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
  String _selectedType = 'all'; // 'all', 'credit', 'debit'
  String _searchQuery = '';
  
  DateTime? _startDate;
  DateTime? _endDate;

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

  Future<void> _downloadLedgerCsv(List<Map<String, dynamic>> logs) async {
    if (logs.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('No logs available to download.'), backgroundColor: Colors.amber),
      );
      return;
    }

    // Generate CSV content
    StringBuffer csv = StringBuffer();
    csv.writeln('Date,Description,Type,Amount,Balance');
    for (final log in logs) {
      csv.writeln('"${log['date']}","${log['desc']}","${log['type']}","${log['amount']}","${log['bal']}"');
    }

    HapticFeedback.mediumImpact();
    if (kIsWeb) {
      // For Web, share the string directly or show share dialog
      await Share.share(csv.toString(), subject: 'Wallet Ledger Logs CSV');
    } else {
      // For Android, write to file and share
      try {
        final tempDir = await getTemporaryDirectory();
        final file = File('${tempDir.path}/ledger_logs.csv');
        await file.writeAsString(csv.toString());
        await Share.shareXFiles([XFile(file.path)], text: 'Wallet Ledger Logs');
      } catch (e) {
        await Share.share(csv.toString(), subject: 'Wallet Ledger Logs CSV');
      }
    }
  }

  Future<void> _selectStartDate() async {
    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: _startDate ?? DateTime.now(),
      firstDate: DateTime(2020),
      lastDate: DateTime.now().add(const Duration(days: 1)),
    );
    if (picked != null) {
      setState(() {
        _startDate = picked;
      });
    }
  }

  Future<void> _selectEndDate() async {
    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: _endDate ?? DateTime.now(),
      firstDate: DateTime(2020),
      lastDate: DateTime.now().add(const Duration(days: 1)),
    );
    if (picked != null) {
      setState(() {
        _endDate = picked;
      });
    }
  }

  void _clearDateFilter() {
    setState(() {
      _startDate = null;
      _endDate = null;
    });
  }

  Widget _buildFilterButton(String label, String typeKey) {
    final isSelected = _selectedType == typeKey;
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final activeColor = const Color(0xFF2563EB); // Royal Blue
    final borderColor = isDark ? const Color(0xFF2E3245) : const Color(0xFF0F172A);
    final textColor = isSelected 
        ? Colors.white 
        : (isDark ? Colors.white : const Color(0xFF0F172A));

    return GestureDetector(
      onTap: () {
        setState(() {
          _selectedType = typeKey;
        });
      },
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
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
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final textColor = isDark ? Colors.white : const Color(0xFF0F172A);
    final cardColor = isDark ? const Color(0xFF1E2235) : Colors.white;
    final borderStyleColor = isDark ? const Color(0xFF2E3245) : const Color(0xFFE2E8F0);
    final searchBgColor = isDark ? const Color(0xFF1E2235) : const Color(0xFFF1F5F9);
    
    // Dynamic styles for Opening/Closing Summary Box
    final summaryCardBg = isDark ? const Color(0xFF1E293B) : const Color(0xFFEFF6FF);
    final summaryTitleColor = isDark ? Colors.white70 : Colors.black54;
    final summaryValColor = isDark ? Colors.white : Colors.black;

    // Filtering & Searching Logic
    final filtered = _ledgerLogs.where((l) {
      final type = (l['type'] ?? '').toString().toLowerCase();
      final desc = (l['desc'] ?? '').toString().toLowerCase();
      
      final isCredit = type == 'credit' || type == 'refund';
      final matchesFilter = _selectedType == 'all' || 
          (_selectedType == 'credit' && isCredit) ||
          (_selectedType == 'debit' && !isCredit);
          
      final query = _searchQuery.toLowerCase();
      final matchesSearch = desc.contains(query);

      // Date matching
      bool matchesDate = true;
      if (l['raw_date'] != null) {
        try {
          final logDate = DateTime.parse(l['raw_date']);
          if (_startDate != null) {
            final startOfDay = DateTime(_startDate!.year, _startDate!.month, _startDate!.day);
            if (logDate.isBefore(startOfDay)) {
              matchesDate = false;
            }
          }
          if (_endDate != null) {
            final endOfDay = DateTime(_endDate!.year, _endDate!.month, _endDate!.day, 23, 59, 59);
            if (logDate.isAfter(endOfDay)) {
              matchesDate = false;
            }
          }
        } catch (_) {}
      }
      
      return matchesFilter && matchesSearch && matchesDate;
    }).toList();

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
          'Wallet Ledger logs',
          style: TextStyle(color: textColor, fontWeight: FontWeight.bold, fontSize: 16),
        ),
        centerTitle: true,
        actions: [
          IconButton(
            icon: Icon(Icons.download, color: textColor),
            tooltip: 'Export CSV',
            onPressed: () => _downloadLedgerCsv(filtered),
          ),
        ],
      ),
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
          : RefreshIndicator(
              onRefresh: _loadLedgerLogs,
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                padding: const EdgeInsets.all(24),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    // Opening/Closing summary card
                    Container(
                      padding: const EdgeInsets.all(20),
                      decoration: BoxDecoration(
                        color: summaryCardBg,
                        borderRadius: BorderRadius.circular(20),
                        border: Border.all(color: isDark ? const Color(0xFF334155) : const Color(0xFFBFDBFE)),
                      ),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text('Opening Balance', style: TextStyle(fontSize: 10, color: summaryTitleColor, fontWeight: FontWeight.w600)),
                              const SizedBox(height: 6),
                              Text(_openingBalance, style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: summaryValColor)),
                            ],
                          ),
                          Icon(Icons.arrow_forward, color: isDark ? Colors.white38 : Colors.grey, size: 20),
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.end,
                            children: [
                              Text('Closing Balance', style: TextStyle(fontSize: 10, color: summaryTitleColor, fontWeight: FontWeight.w600)),
                              const SizedBox(height: 6),
                              Text(_closingBalance, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: Color(0xFF4361EE))),
                            ],
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 20),

                    // Search input
                    Container(
                      margin: const EdgeInsets.only(bottom: 12),
                      decoration: BoxDecoration(
                        color: searchBgColor,
                        borderRadius: BorderRadius.circular(14),
                        border: Border.all(color: borderStyleColor),
                      ),
                      child: TextField(
                        style: TextStyle(color: textColor, fontSize: 12),
                        decoration: InputDecoration(
                          hintText: 'Search by reference ID or description...',
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

                    // Smooth Date Selector Row
                    Container(
                      margin: const EdgeInsets.only(bottom: 16),
                      child: Row(
                        children: [
                          Expanded(
                            child: GestureDetector(
                              onTap: _selectStartDate,
                              child: Container(
                                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                                decoration: BoxDecoration(
                                  color: cardColor,
                                  borderRadius: BorderRadius.circular(10),
                                  border: Border.all(color: borderStyleColor),
                                ),
                                child: Row(
                                  children: [
                                    const Icon(Icons.calendar_today, size: 12, color: Colors.grey),
                                    const SizedBox(width: 8),
                                    Expanded(
                                      child: Text(
                                        _startDate == null 
                                            ? 'Start Date' 
                                            : '${_startDate!.year}-${_startDate!.month.toString().padLeft(2, '0')}-${_startDate!.day.toString().padLeft(2, '0')}',
                                        style: TextStyle(fontSize: 10, color: textColor, fontWeight: FontWeight.bold),
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            ),
                          ),
                          const SizedBox(width: 8),
                          Expanded(
                            child: GestureDetector(
                              onTap: _selectEndDate,
                              child: Container(
                                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                                decoration: BoxDecoration(
                                  color: cardColor,
                                  borderRadius: BorderRadius.circular(10),
                                  border: Border.all(color: borderStyleColor),
                                ),
                                child: Row(
                                  children: [
                                    const Icon(Icons.calendar_today, size: 12, color: Colors.grey),
                                    const SizedBox(width: 8),
                                    Expanded(
                                      child: Text(
                                        _endDate == null 
                                            ? 'End Date' 
                                            : '${_endDate!.year}-${_endDate!.month.toString().padLeft(2, '0')}-${_endDate!.day.toString().padLeft(2, '0')}',
                                        style: TextStyle(fontSize: 10, color: textColor, fontWeight: FontWeight.bold),
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            ),
                          ),
                          if (_startDate != null || _endDate != null) ...[
                            const SizedBox(width: 8),
                            IconButton(
                              onPressed: _clearDateFilter,
                              icon: const Icon(Icons.clear, size: 18, color: Colors.redAccent),
                              tooltip: 'Clear Date Filter',
                            )
                          ]
                        ],
                      ),
                    ),

                    // Filter Buttons
                    SingleChildScrollView(
                      scrollDirection: Axis.horizontal,
                      child: Row(
                        children: [
                          _buildFilterButton('All Logs', 'all'),
                          const SizedBox(width: 8),
                          _buildFilterButton('Credits (+)', 'credit'),
                          const SizedBox(width: 8),
                          _buildFilterButton('Debits (-)', 'debit'),
                        ],
                      ),
                    ),
                    const SizedBox(height: 24),

                    Text('JOURNAL TRANSACTIONS', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: isDark ? Colors.white30 : Colors.grey)),
                    const SizedBox(height: 12),

                    if (filtered.isEmpty)
                      Container(
                        padding: const EdgeInsets.all(32),
                        decoration: BoxDecoration(
                          color: cardColor,
                          borderRadius: BorderRadius.circular(20),
                          border: Border.all(color: borderStyleColor),
                        ),
                        child: Column(
                          children: [
                            Icon(Icons.library_books_outlined, color: isDark ? Colors.white10 : Colors.grey.shade300, size: 48),
                            const SizedBox(height: 12),
                            const Text('No matching ledger entries found.', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12, color: Colors.grey)),
                          ],
                        ),
                      )
                    else
                      Container(
                        decoration: BoxDecoration(
                          color: cardColor,
                          borderRadius: BorderRadius.circular(20),
                          border: Border.all(color: borderStyleColor),
                        ),
                        child: ListView.separated(
                          shrinkWrap: true,
                          physics: const NeverScrollableScrollPhysics(),
                          itemCount: filtered.length,
                          separatorBuilder: (context, index) => Divider(height: 1, color: borderStyleColor),
                          itemBuilder: (context, index) {
                            final log = filtered[index];
                            final isCredit = log['type'] == 'credit' || log['type'] == 'refund';

                            return ListTile(
                              contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 6),
                              leading: CircleAvatar(
                                radius: 18,
                                backgroundColor: isCredit ? const Color(0xFFDCFCE7) : const Color(0xFFFEE2E2),
                                child: Icon(
                                  isCredit ? Icons.add : Icons.remove,
                                  color: isCredit ? Colors.green : Colors.red,
                                  size: 16,
                                ),
                              ),
                              title: Text(
                                log['desc'] ?? 'N/A', 
                                style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: textColor)
                              ),
                              subtitle: Text(
                                'Bal: ${log['bal']} • ${log['date']}', 
                                style: const TextStyle(fontSize: 10, color: Colors.grey)
                              ),
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
            ),
    );
  }
}
