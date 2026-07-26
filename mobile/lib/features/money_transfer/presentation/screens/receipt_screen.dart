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
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final textColor = isDark ? Colors.white : const Color(0xFF1E293B);
    final cardColor = isDark ? const Color(0xFF1E2235) : Colors.white;

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
            // Animated Success Header Indicator
            Center(
              child: Column(
                children: [
                  const AnimatedCheckmark(),
                  const SizedBox(height: 16),
                  Text('Payout Transfer Successful', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: textColor)),
                  const SizedBox(height: 4),
                  Text('Ref: $referenceId', style: const TextStyle(fontSize: 11, color: Colors.grey)),
                ],
              ),
            ),
            const SizedBox(height: 36),

            // Serrated Ticket Card
            ClipPath(
              clipper: TicketClipper(),
              child: Container(
                color: cardColor,
                padding: const EdgeInsets.fromLTRB(24, 24, 24, 40),
                child: Column(
                  children: [
                    ReceiptRow(label: 'Beneficiary Name', value: beneficiary, textColor: textColor),
                    const Divider(height: 24),
                    ReceiptRow(label: 'Destination Bank', value: 'State Bank of India', textColor: textColor),
                    const Divider(height: 24),
                    ReceiptRow(label: 'Account Number', value: '••••••••4556', textColor: textColor),
                    const Divider(height: 24),
                    ReceiptRow(label: 'Transfer Amount', value: '₹$amount', isBold: true, textColor: textColor),
                    const Divider(height: 24),
                    ReceiptRow(label: 'Convenience Charge', value: '₹5.00', textColor: textColor),
                    const Divider(height: 24),
                    ReceiptRow(label: 'Commission Earned', value: '₹1.25', valueColor: Colors.green, textColor: textColor),
                    const Divider(height: 24),
                    ReceiptRow(label: 'Timestamp', value: DateTime.now().toString().substring(0, 16), textColor: textColor),
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
                      backgroundColor: const Color(0xFF7C3AED),
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

class AnimatedCheckmark extends StatefulWidget {
  const AnimatedCheckmark({Key? key}) : super(key: key);

  @override
  State<AnimatedCheckmark> createState() => _AnimatedCheckmarkState();
}

class _AnimatedCheckmarkState extends State<AnimatedCheckmark> with SingleTickerProviderStateMixin {
  late AnimationController _controller;
  late Animation<double> _checkAnimation;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(vsync: this, duration: const Duration(milliseconds: 800));
    _checkAnimation = CurvedAnimation(parent: _controller, curve: Curves.elasticOut);
    _controller.forward();
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return ScaleTransition(
      scale: _checkAnimation,
      child: const CircleAvatar(
        radius: 36,
        backgroundColor: Color(0xFFDCFCE7),
        child: Icon(Icons.check, color: Colors.green, size: 36),
      ),
    );
  }
}

class TicketClipper extends CustomClipper<Path> {
  @override
  Path getClip(Size size) {
    final path = Path();
    path.lineTo(0, size.height);

    double x = 0;
    double y = size.height;
    double width = size.width;
    double serrationSize = 8.0;

    while (x < width) {
      path.lineTo(x + serrationSize / 2, y - serrationSize);
      path.lineTo(x + serrationSize, y);
      x += serrationSize;
    }

    path.lineTo(width, 0);
    path.close();
    return path;
  }

  @override
  bool shouldReclip(CustomClipper<Path> oldClipper) => false;
}

class ReceiptRow extends StatelessWidget {
  final String label;
  final String value;
  final bool isBold;
  final Color? valueColor;
  final Color textColor;

  const ReceiptRow({
    Key? key,
    required this.label,
    required this.value,
    this.isBold = false,
    this.valueColor,
    required this.textColor,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(label, style: const TextStyle(fontSize: 12, color: Colors.grey)),
        Text(
          value,
          style: TextStyle(
            fontSize: 12,
            fontWeight: isBold ? FontWeight.w800 : FontWeight.bold,
            color: valueColor ?? textColor,
          ),
        ),
      ],
    );
  }
}
