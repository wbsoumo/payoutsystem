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

  // Pagination states
  int _currentPage = 1;
  final int _itemsPerPage = 5;

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

    StringBuffer csv = StringBuffer();
    csv.writeln('Date,Description,Type,Amount,Balance');
    for (final log in logs) {
      csv.writeln('"${log['date']}","${log['desc']}","${log['type']}","${log['amount']}","${log['bal']}"');
    }

    HapticFeedback.mediumImpact();
    if (kIsWeb) {
      await Share.share(csv.toString(), subject: 'Wallet Ledger Logs CSV');
    } else {
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
        _currentPage = 1; // Reset to page 1 on filter change
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
        _currentPage = 1; // Reset to page 1 on filter change
      });
    }
  }

  void _clearDateFilter() {
    setState(() {
      _startDate = null;
      _endDate = null;
      _currentPage = 1;
    });
  }

  String _formatDisplayDate(DateTime dt) {
    final months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    return '${dt.day} ${months[dt.month - 1]} ${dt.year}';
  }

  String _formatDate(String? rawDateStr) {
    if (rawDateStr == null) return '';
    try {
      final dt = DateTime.parse(rawDateStr);
      final months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
      final month = months[dt.month - 1];
      final day = dt.day.toString().padLeft(2, '0');
      final year = dt.year;
      
      int hour = dt.hour;
      final ampm = hour >= 12 ? 'PM' : 'AM';
      hour = hour % 12;
      if (hour == 0) hour = 12;
      final hourStr = hour.toString().padLeft(2, '0');
      final minuteStr = dt.minute.toString().padLeft(2, '0');
      
      return '$day $month $year, $hourStr:$minuteStr $ampm';
    } catch (_) {
      return '';
    }
  }

  Widget _buildFilterButton(String label, String typeKey, IconData icon, Color iconColor) {
    final isSelected = _selectedType == typeKey;
    final isDark = Theme.of(context).brightness == Brightness.dark;
    
    final activeColor = const Color(0xFF4F46E5); // Indigo
    final btnBg = isSelected 
        ? activeColor 
        : (isDark ? const Color(0xFF1E2235) : Colors.white);
    final borderStyleColor = isDark ? const Color(0xFF2E3245) : const Color(0xFFE2E8F0);
    final textColor = isSelected 
        ? Colors.white 
        : (isDark ? Colors.white70 : const Color(0xFF1E293B));

    return GestureDetector(
      onTap: () {
        setState(() {
          _selectedType = typeKey;
          _currentPage = 1;
        });
      },
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
        decoration: BoxDecoration(
          color: btnBg,
          borderRadius: BorderRadius.circular(30),
          border: Border.all(
            color: isSelected ? activeColor : borderStyleColor,
            width: 1,
          ),
          boxShadow: isSelected ? [
            BoxShadow(
              color: activeColor.withOpacity(0.2),
              blurRadius: 8,
              offset: const Offset(0, 4),
            )
          ] : [],
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              padding: const EdgeInsets.all(4),
              decoration: BoxDecoration(
                color: isSelected ? Colors.white24 : iconColor.withOpacity(0.1),
                shape: BoxShape.circle,
              ),
              child: Icon(
                icon, 
                size: 12, 
                color: isSelected ? Colors.white : iconColor
              ),
            ),
            const SizedBox(width: 8),
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

  Widget _buildPaginationButton(String text, bool isSelected, bool isEnabled, VoidCallback onTap) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    
    return GestureDetector(
      onTap: isEnabled ? onTap : null,
      child: Container(
        margin: const EdgeInsets.symmetric(horizontal: 4),
        width: 36,
        height: 36,
        alignment: Alignment.center,
        decoration: BoxDecoration(
          color: isSelected 
              ? const Color(0xFF4F46E5) 
              : (isDark ? const Color(0xFF1E2235) : const Color(0xFFF1F5F9)),
          shape: BoxShape.circle,
        ),
        child: Text(
          text,
          style: TextStyle(
            color: isSelected 
                ? Colors.white 
                : (isEnabled ? (isDark ? Colors.white70 : const Color(0xFF1E293B)) : Colors.grey),
            fontSize: 12,
            fontWeight: FontWeight.bold,
          ),
        ),
      ),
    );
  }

  Widget _buildSummaryInfo(String title, String val, bool isLeft, double screenWidth) {
    return Column(
      crossAxisAlignment: isLeft ? CrossAxisAlignment.start : CrossAxisAlignment.end,
      children: [
        Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(
              title, 
              style: const TextStyle(fontSize: 10, color: Colors.white70, fontWeight: FontWeight.w500)
            ),
            const SizedBox(width: 3),
            const Icon(Icons.info_outline, size: 10, color: Colors.white54),
          ],
        ),
        const SizedBox(height: 6),
        Text(
          val, 
          style: const TextStyle(
            fontSize: 20, 
            fontWeight: FontWeight.w800, 
            color: Colors.white,
            letterSpacing: -0.5
          )
        ),
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final textColor = isDark ? Colors.white : const Color(0xFF1E293B);
    final cardColor = isDark ? const Color(0xFF1E2235) : Colors.white;
    final borderStyleColor = isDark ? const Color(0xFF2E3245) : const Color(0xFFE2E8F0);
    final searchBgColor = isDark ? const Color(0xFF1E2235) : const Color(0xFFF8FAFC);
    final screenWidth = MediaQuery.of(context).size.width;

    // Advanced Filters + Search
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

    // Slicing for Pagination
    final int totalPages = (filtered.length / _itemsPerPage).ceil();
    final int activePage = _currentPage > totalPages ? 1 : _currentPage;
    final int startIndex = (activePage - 1) * _itemsPerPage;
    final List<Map<String, dynamic>> paginatedLogs = filtered.skip(startIndex).take(_itemsPerPage).toList();

    return Scaffold(
      backgroundColor: isDark ? const Color(0xFF0F172A) : const Color(0xFFF8FAFC),
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: IconButton(
          icon: Icon(Icons.arrow_back_ios_new, color: textColor, size: 18),
          onPressed: () => Navigator.pop(context),
        ),
        title: Text(
          'Wallet Ledger Logs',
          style: TextStyle(color: textColor, fontWeight: FontWeight.bold, fontSize: 16),
        ),
        centerTitle: true,
        actions: [
          IconButton(
            icon: Icon(Icons.file_download_outlined, color: textColor, size: 22),
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
                      Container(height: 140, decoration: BoxDecoration(color: cardColor, borderRadius: BorderRadius.circular(24))),
                      const SizedBox(height: 24),
                      Expanded(
                        child: ListView.separated(
                          itemCount: 3,
                          separatorBuilder: (c, i) => const SizedBox(height: 12),
                          itemBuilder: (context, index) => Container(height: 72, decoration: BoxDecoration(color: cardColor, borderRadius: BorderRadius.circular(20))),
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
                padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    // Premium Purple Gradient Wallet Balance Card
                    Container(
                      height: 140,
                      decoration: BoxDecoration(
                        gradient: const LinearGradient(
                          colors: [Color(0xFF6366F1), Color(0xFF4F46E5), Color(0xFF3730A3)],
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                        ),
                        borderRadius: BorderRadius.circular(24),
                        boxShadow: [
                          BoxShadow(
                            color: const Color(0xFF4F46E5).withOpacity(0.3),
                            blurRadius: 16,
                            offset: const Offset(0, 8),
                          )
                        ],
                      ),
                      child: Stack(
                        children: [
                          // Vector Wallet Graphic element on the right
                          Positioned(
                            right: -15,
                            bottom: -15,
                            child: Opacity(
                              opacity: 0.15,
                              child: Icon(Icons.wallet, size: 160, color: Colors.white.withOpacity(0.8)),
                            ),
                          ),
                          Padding(
                            padding: const EdgeInsets.all(24),
                            child: Row(
                              children: [
                                Expanded(child: _buildSummaryInfo('Opening Balance', _openingBalance, true, screenWidth)),
                                Container(
                                  width: 36,
                                  height: 36,
                                  decoration: BoxDecoration(
                                    color: Colors.white.withOpacity(0.2),
                                    shape: BoxShape.circle,
                                  ),
                                  child: const Icon(Icons.arrow_forward_outlined, color: Colors.white, size: 16),
                                ),
                                const SizedBox(width: 8),
                                Expanded(child: _buildSummaryInfo('Closing Balance', _closingBalance, false, screenWidth)),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 24),

                    // Search input + Filter Icon Button Row
                    Row(
                      children: [
                        Expanded(
                          child: Container(
                            decoration: BoxDecoration(
                              color: searchBgColor,
                              borderRadius: BorderRadius.circular(14),
                              border: Border.all(color: borderStyleColor),
                            ),
                            child: TextField(
                              style: TextStyle(color: textColor, fontSize: 12),
                              decoration: const InputDecoration(
                                hintText: 'Search by reference ID or description...',
                                hintStyle: TextStyle(color: Colors.grey, fontSize: 12),
                                prefixIcon: Icon(Icons.search, color: Colors.grey, size: 18),
                                border: InputBorder.none,
                                contentPadding: EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                              ),
                              onChanged: (val) {
                                setState(() {
                                  _searchQuery = val;
                                  _currentPage = 1;
                                });
                              },
                            ),
                          ),
                        ),
                        const SizedBox(width: 10),
                        Container(
                          width: 46,
                          height: 46,
                          decoration: BoxDecoration(
                            color: const Color(0xFF4F46E5).withOpacity(0.08),
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: const Icon(Icons.filter_list_outlined, color: Color(0xFF4F46E5), size: 20),
                        ),
                      ],
                    ),
                    const SizedBox(height: 12),

                    // Start/End Date Picker Selection Cards
                    Row(
                      children: [
                        Expanded(
                          child: GestureDetector(
                            onTap: _selectStartDate,
                            child: Container(
                              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                              decoration: BoxDecoration(
                                color: cardColor,
                                borderRadius: BorderRadius.circular(12),
                                border: Border.all(color: borderStyleColor),
                              ),
                              child: Row(
                                children: [
                                  const Icon(Icons.calendar_month_outlined, size: 16, color: Colors.grey),
                                  const SizedBox(width: 8),
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        const Text('Start Date', style: TextStyle(fontSize: 9, color: Colors.grey)),
                                        const SizedBox(height: 2),
                                        Text(
                                          _startDate == null ? 'Select Date' : _formatDisplayDate(_startDate!),
                                          style: TextStyle(fontSize: 11, color: textColor, fontWeight: FontWeight.bold),
                                        ),
                                      ],
                                    ),
                                  ),
                                  const Icon(Icons.keyboard_arrow_down, size: 16, color: Colors.grey),
                                ],
                              ),
                            ),
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: GestureDetector(
                            onTap: _selectEndDate,
                            child: Container(
                              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                              decoration: BoxDecoration(
                                color: cardColor,
                                borderRadius: BorderRadius.circular(12),
                                border: Border.all(color: borderStyleColor),
                              ),
                              child: Row(
                                children: [
                                  const Icon(Icons.calendar_month_outlined, size: 16, color: Colors.grey),
                                  const SizedBox(width: 8),
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        const Text('End Date', style: TextStyle(fontSize: 9, color: Colors.grey)),
                                        const SizedBox(height: 2),
                                        Text(
                                          _endDate == null ? 'Select Date' : _formatDisplayDate(_endDate!),
                                          style: TextStyle(fontSize: 11, color: textColor, fontWeight: FontWeight.bold),
                                        ),
                                      ],
                                    ),
                                  ),
                                  const Icon(Icons.keyboard_arrow_down, size: 16, color: Colors.grey),
                                ],
                              ),
                            ),
                          ),
                        ),
                        if (_startDate != null || _endDate != null) ...[
                          const SizedBox(width: 8),
                          IconButton(
                            onPressed: _clearDateFilter,
                            icon: const Icon(Icons.cancel_outlined, size: 20, color: Colors.redAccent),
                          )
                        ]
                      ],
                    ),
                    const SizedBox(height: 16),

                    // Pill Filters Row
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        _buildFilterButton('All Logs', 'all', Icons.list_alt, const Color(0xFF4F46E5)),
                        _buildFilterButton('Credits (+)', 'credit', Icons.arrow_upward, Colors.green),
                        _buildFilterButton('Debits (-)', 'debit', Icons.arrow_downward, Colors.red),
                      ],
                    ),
                    const SizedBox(height: 24),

                    // Journal Transactions count header
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(
                          'Journal Transactions', 
                          style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: textColor)
                        ),
                        Text(
                          'Total ${filtered.length} Transactions', 
                          style: const TextStyle(fontSize: 10, color: Colors.grey)
                        ),
                      ],
                    ),
                    const SizedBox(height: 12),

                    if (paginatedLogs.isEmpty)
                      Container(
                        padding: const EdgeInsets.all(40),
                        decoration: BoxDecoration(
                          color: cardColor,
                          borderRadius: BorderRadius.circular(24),
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
                    else ...[
                      // Transactions Card List container
                      Container(
                        decoration: BoxDecoration(
                          color: cardColor,
                          borderRadius: BorderRadius.circular(24),
                          border: Border.all(color: borderStyleColor),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withOpacity(0.01),
                              blurRadius: 10,
                              offset: const Offset(0, 4),
                            )
                          ],
                        ),
                        child: ListView.separated(
                          shrinkWrap: true,
                          physics: const NeverScrollableScrollPhysics(),
                          itemCount: paginatedLogs.length,
                          separatorBuilder: (context, index) => Divider(height: 1, color: borderStyleColor),
                          itemBuilder: (context, index) {
                            final log = paginatedLogs[index];
                            final isCredit = log['type'] == 'credit' || log['type'] == 'refund';
                            final formattedTime = _formatDate(log['raw_date']);

                            return Padding(
                              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                              child: Row(
                                children: [
                                  // Credit / Debit Indicator Circle
                                  CircleAvatar(
                                    radius: 20,
                                    backgroundColor: isCredit ? const Color(0xFFE8F5E9) : const Color(0xFFFFEBEE),
                                    child: Icon(
                                      isCredit ? Icons.arrow_upward : Icons.arrow_downward,
                                      color: isCredit ? Colors.green : Colors.red,
                                      size: 16,
                                    ),
                                  ),
                                  const SizedBox(width: 12),
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Text(
                                          log['desc'] ?? 'N/A', 
                                          style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: textColor),
                                          maxLines: 1,
                                          overflow: TextOverflow.ellipsis,
                                        ),
                                        const SizedBox(height: 4),
                                        Text(
                                          formattedTime, 
                                          style: const TextStyle(fontSize: 10, color: Colors.grey)
                                        ),
                                        const SizedBox(height: 2),
                                        Text(
                                          'Balance: ${log['bal']}', 
                                          style: const TextStyle(fontSize: 10, color: Colors.grey)
                                        ),
                                      ],
                                    ),
                                  ),
                                  Column(
                                    crossAxisAlignment: CrossAxisAlignment.end,
                                    children: [
                                      Text(
                                        '${isCredit ? '+' : '-'}${log['amount']}',
                                        style: TextStyle(
                                          fontWeight: FontWeight.w800,
                                          fontSize: 13,
                                          color: isCredit ? Colors.green : Colors.red,
                                        ),
                                      ),
                                      const SizedBox(height: 4),
                                      Text(
                                        isCredit ? 'Credit' : 'Debit',
                                        style: const TextStyle(fontSize: 9, color: Colors.grey),
                                      ),
                                    ],
                                  ),
                                ],
                              ),
                            );
                          },
                        ),
                      ),
                      
                      // Pagination controls bar
                      if (totalPages > 1) ...[
                        const SizedBox(height: 24),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            // Prev page button
                            _buildPaginationButton(
                              '<', 
                              false, 
                              activePage > 1, 
                              () => setState(() => _currentPage = activePage - 1)
                            ),
                            
                            // Page buttons
                            ...List.generate(totalPages, (index) {
                              final p = index + 1;
                              // Show first, last, and pages around active page
                              if (p == 1 || p == totalPages || (p - activePage).abs() <= 1) {
                                return _buildPaginationButton(
                                  '$p', 
                                  activePage == p, 
                                  true, 
                                  () => setState(() => _currentPage = p)
                                );
                              }
                              // Ellipsis
                              if (p == 2 || p == totalPages - 1) {
                                return const Padding(
                                  padding: EdgeInsets.symmetric(horizontal: 4),
                                  child: Text('...', style: TextStyle(color: Colors.grey)),
                                );
                              }
                              return const SizedBox.shrink();
                            }),

                            // Next page button
                            _buildPaginationButton(
                              '>', 
                              false, 
                              activePage < totalPages, 
                              () => setState(() => _currentPage = activePage + 1)
                            ),
                          ],
                        ),
                      ],
                    ],
                    const SizedBox(height: 32),
                  ],
                ),
              ),
            ),
    );
  }
}
