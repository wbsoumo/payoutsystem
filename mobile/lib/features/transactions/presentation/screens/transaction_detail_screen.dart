import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/constants/endpoints.dart';

class TransactionDetailScreen extends StatelessWidget {
  final Map<String, dynamic> transaction;

  const TransactionDetailScreen({
    Key? key,
    required this.transaction,
  }) : super(key: key);

  static const Map<String, String> _bankDomains = {
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

  String _getLogoForIfsc(String? ifsc) {
    if (ifsc == null || ifsc.length < 4) {
      return '${Endpoints.baseUrl}/logo/generic-bank.com';
    }
    final prefix = ifsc.substring(0, 4).toUpperCase();
    final domain = _bankDomains[prefix] ?? 'generic-bank.com';
    return '${Endpoints.baseUrl}/logo/$domain';
  }

  void _copyToClipboard(BuildContext context, String text, String label) {
    Clipboard.setData(ClipboardData(text: text));
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text('$label copied to clipboard!'),
        behavior: SnackBarBehavior.floating,
        backgroundColor: const Color(0xFF1E293B),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final status = transaction['status']?.toString().toLowerCase() ?? 'success';
    final isSuccess = status == 'success';
    final isPending = status == 'pending';

    // Color theme based on status
    final themeColor = isSuccess
        ? const Color(0xFF10B981) // Google Pay Green
        : isPending
            ? const Color(0xFFF59E0B) // Amber
            : const Color(0xFFEF4444); // Red

    final statusTitle = isSuccess
        ? 'Transaction Successful'
        : isPending
            ? 'Transaction Pending'
            : 'Transaction Failed';

    final cardColor = isDark ? const Color(0xFF1E2235) : Colors.white;
    final textColor = isDark ? Colors.white : const Color(0xFF1E293B);
    final subTextColor = isDark ? Colors.white60 : Colors.black54;

    return Scaffold(
      backgroundColor: isDark ? const Color(0xFF0F172A) : const Color(0xFFF8FAFC),
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: IconButton(
          icon: Icon(Icons.arrow_back, color: textColor),
          onPressed: () => context.pop(),
        ),
        title: Text(
          'Transaction Details',
          style: TextStyle(color: textColor, fontWeight: FontWeight.bold, fontSize: 16),
        ),
        centerTitle: true,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // 1. Google Pay style Status Header Card
            Container(
              padding: const EdgeInsets.symmetric(vertical: 32, horizontal: 20),
              decoration: BoxDecoration(
                color: cardColor,
                borderRadius: BorderRadius.circular(28),
                border: Border.all(color: isDark ? const Color(0xFF2E3245) : const Color(0xFFE2E8F0)),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.04),
                    blurRadius: 10,
                    offset: const Offset(0, 4),
                  ),
                ],
              ),
              child: Column(
                children: [
                  // Circular Pulsing Status Icon
                  Container(
                    width: 72,
                    height: 72,
                    decoration: BoxDecoration(
                      color: themeColor.withOpacity(0.12),
                      shape: BoxShape.circle,
                    ),
                    child: Center(
                      child: Icon(
                        isSuccess
                            ? Icons.check_circle
                            : isPending
                                ? Icons.access_time_filled
                                : Icons.cancel,
                        color: themeColor,
                        size: 44,
                      ),
                    ),
                  ),
                  const SizedBox(height: 20),
                  Text(
                    statusTitle,
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.w900,
                      color: themeColor,
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    transaction['date'] ?? 'N/A',
                    style: TextStyle(fontSize: 11, color: subTextColor),
                  ),
                  const SizedBox(height: 24),
                  const Divider(height: 1),
                  const SizedBox(height: 20),
                  Text(
                    transaction['amount'] ?? '₹0.00',
                    style: TextStyle(
                      fontSize: 36,
                      fontWeight: FontWeight.bold,
                      color: textColor,
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 20),

            // 2. Beneficiary / Paid To Card
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: cardColor,
                borderRadius: BorderRadius.circular(24),
                border: Border.all(color: isDark ? const Color(0xFF2E3245) : const Color(0xFFE2E8F0)),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'TRANSFER DETAILS',
                    style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: subTextColor, letterSpacing: 1),
                  ),
                  const SizedBox(height: 16),
                  Row(
                    children: [
                      SafeBankLogo(
                        logoUrl: _getLogoForIfsc(transaction['ifsc']),
                        bankName: transaction['bank'] ?? '',
                        size: 48,
                      ),
                      const SizedBox(width: 14),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              transaction['beneficiary'] ?? 'N/A',
                              style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: textColor),
                            ),
                            const SizedBox(height: 4),
                            Text(
                              transaction['bank'] ?? 'N/A',
                              style: const TextStyle(fontSize: 10, color: Colors.grey),
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                            ),
                            const SizedBox(height: 2),
                            Text(
                              'A/C: ${transaction['account'] ?? 'N/A'} • IFSC: ${transaction['ifsc'] ?? 'N/A'}',
                              style: const TextStyle(fontSize: 10, color: Colors.grey),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 20),
                  const Divider(height: 1),
                  const SizedBox(height: 16),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text('Paid From', style: TextStyle(fontSize: 11, color: subTextColor)),
                      Text('Novexapay Wallet', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: textColor)),
                    ],
                  ),
                ],
              ),
            ),
            const SizedBox(height: 20),

            // 3. Reference IDs Card
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: cardColor,
                borderRadius: BorderRadius.circular(24),
                border: Border.all(color: isDark ? const Color(0xFF2E3245) : const Color(0xFFE2E8F0)),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'TRANSACTION DETAILS',
                    style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: subTextColor, letterSpacing: 1),
                  ),
                  const SizedBox(height: 16),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text('Reference ID', style: TextStyle(fontSize: 10, color: subTextColor)),
                            const SizedBox(height: 4),
                            Text(transaction['ref'] ?? 'N/A', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: textColor)),
                          ],
                        ),
                      ),
                      IconButton(
                        icon: const Icon(Icons.copy, size: 16, color: Colors.blueAccent),
                        onPressed: () => _copyToClipboard(context, transaction['ref'] ?? '', 'Reference ID'),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text('UTR (Bank Ref No.)', style: TextStyle(fontSize: 10, color: subTextColor)),
                            const SizedBox(height: 4),
                            Text(
                              transaction['utr'] ?? 'UTR${DateTime.now().millisecondsSinceEpoch}',
                              style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: textColor),
                            ),
                          ],
                        ),
                      ),
                      IconButton(
                        icon: const Icon(Icons.copy, size: 16, color: Colors.blueAccent),
                        onPressed: () => _copyToClipboard(
                          context,
                          transaction['utr'] ?? 'UTR${DateTime.now().millisecondsSinceEpoch}',
                          'UTR',
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
            const SizedBox(height: 32),

            // 4. Action Buttons Row
            Row(
              children: [
                Expanded(
                  child: OutlinedButton.icon(
                    onPressed: () {
                      final benName = transaction['beneficiary'];
                      if (benName != null) {
                        context.push('/transfer?beneficiary_name=${Uri.encodeComponent(benName)}');
                      }
                    },
                    icon: const Icon(Icons.repeat, size: 16),
                    label: const Text('Transfer Again', style: TextStyle(fontSize: 12)),
                    style: OutlinedButton.styleFrom(
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      side: const BorderSide(color: Color(0xFF7C3AED)),
                      foregroundColor: const Color(0xFF7C3AED),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                    ),
                  ),
                ),
                const SizedBox(width: 16),
                Expanded(
                  child: ElevatedButton.icon(
                    onPressed: () {
                      ScaffoldMessenger.of(context).showSnackBar(
                        const SnackBar(
                          content: Text('Invoice / Receipt download started!'),
                          backgroundColor: Colors.green,
                        ),
                      );
                    },
                    icon: const Icon(Icons.download, size: 16),
                    label: const Text('Download Receipt', style: TextStyle(fontSize: 12)),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF7C3AED),
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                    ),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}

class SafeBankLogo extends StatelessWidget {
  final String? logoUrl;
  final String bankName;
  final double size;

  const SafeBankLogo({
    Key? key,
    required this.logoUrl,
    required this.bankName,
    this.size = 48,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    if (logoUrl == null || logoUrl!.isEmpty) {
      return Container(
        width: size,
        height: size,
        decoration: const BoxDecoration(
          color: Color(0xFFEFF6FF),
          shape: BoxShape.circle,
        ),
        child: const Center(
          child: Icon(Icons.business, color: Color(0xFF4361EE), size: 24),
        ),
      );
    }

    return Container(
      width: size,
      height: size,
      decoration: const BoxDecoration(
        color: Color(0xFFEFF6FF),
        shape: BoxShape.circle,
      ),
      child: ClipOval(
        child: Image.network(
          logoUrl!,
          width: size,
          height: size,
          fit: BoxFit.cover,
          errorBuilder: (context, error, stackTrace) {
            final uri = Uri.tryParse(logoUrl!);
            final domain = uri != null && uri.pathSegments.isNotEmpty ? uri.pathSegments.last : 'generic-bank.com';
            
            return Image.network(
              'https://logo.clearbit.com/$domain',
              width: size,
              height: size,
              fit: BoxFit.cover,
              errorBuilder: (context, error2, stackTrace2) {
                return const Center(
                  child: Icon(Icons.business, color: Color(0xFF4361EE), size: 20),
                );
              },
            );
          },
        ),
      ),
    );
  }
}
