import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:share_plus/share_plus.dart';

class ReceiptScreen extends StatelessWidget {
  final double amount;
  final String beneficiary;
  final String referenceId;

  const ReceiptScreen({
    Key? key,
    required this.amount,
    required this.beneficiary,
    required this.referenceId,
  }) : super(key: key);

  void _shareReceipt() {
    Share.share(
      'Novexapay Payout Receipt\n'
      '-------------------------\n'
      'Beneficiary: $beneficiary\n'
      'Amount: ₹$amount\n'
      'Reference ID: $referenceId\n'
      'Status: SUCCESS\n'
      'Thank you for using Novexapay!',
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Transaction Receipt', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
        centerTitle: true,
        leading: IconButton(
          icon: const Icon(Icons.close),
          onPressed: () => context.go('/dashboard'),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // Success Header Indicator
            Center(
              child: Column(
                children: [
                  const CircleAvatar(
                    radius: 36,
                    backgroundColor: Color(0xFFDCFCE7),
                    child: Icon(Icons.check, color: Colors.green, size: 36),
                  ),
                  const SizedBox(height: 16),
                  const Text('Payout Transfer Successful', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                  const SizedBox(height: 4),
                  Text('Ref: $referenceId', style: const TextStyle(fontSize: 11, color: Colors.grey)),
                ],
              ),
            ),
            const SizedBox(height: 36),

            // Receipt Parameters Card
            Card(
              child: Padding(
                padding: const EdgeInsets.all(24),
                child: Column(
                  children: [
                    ReceiptRow(label: 'Beneficiary Name', value: beneficiary),
                    const Divider(height: 24),
                    ReceiptRow(label: 'Destination Bank', value: 'State Bank of India'),
                    const Divider(height: 24),
                    ReceiptRow(label: 'Account Number', value: '••••••••4556'),
                    const Divider(height: 24),
                    ReceiptRow(label: 'Transfer Amount', value: '₹$amount', isBold: true),
                    const Divider(height: 24),
                    const ReceiptRow(label: 'Convenience Charge', value: '₹5.00'),
                    const Divider(height: 24),
                    const ReceiptRow(label: 'Commission Earned', value: '₹1.25', valueColor: Colors.green),
                    const Divider(height: 24),
                    ReceiptRow(label: 'Timestamp', value: DateTime.now().toString().substring(0, 16)),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 36),

            Row(
              children: [
                Expanded(
                  child: OutlinedButton.icon(
                    onPressed: _shareReceipt,
                    icon: const Icon(Icons.share),
                    label: const Text('Share Receipt', style: TextStyle(fontWeight: FontWeight.bold)),
                    style: OutlinedButton.styleFrom(
                      minimumSize: const Size.fromHeight(50),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                    ),
                  ),
                ),
                const SizedBox(width: 16),
                Expanded(
                  child: ElevatedButton(
                    onPressed: () => context.go('/dashboard'),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF4361EE),
                      foregroundColor: Colors.white,
                      minimumSize: const Size.fromHeight(50),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                    ),
                    child: const Text('Done', style: TextStyle(fontWeight: FontWeight.bold)),
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

class ReceiptRow extends StatelessWidget {
  final String label;
  final String value;
  final bool isBold;
  final Color? valueColor;

  const ReceiptRow({
    Key? key,
    required this.label,
    required this.value,
    this.isBold = false,
    this.valueColor,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(label, style: const TextStyle(fontSize: 12, color: Colors.black54)),
        Text(
          value,
          style: TextStyle(
            fontSize: 12,
            fontWeight: isBold ? FontWeight.w800 : FontWeight.bold,
            color: valueColor ?? Colors.black87,
          ),
        ),
      ],
    );
  }
}
