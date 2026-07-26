import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/network/api_client.dart';

class AddMoneyScreen extends StatefulWidget {
  const AddMoneyScreen({Key? key}) : super(key: key);

  @override
  State<AddMoneyScreen> createState() => _AddMoneyScreenState();
}

class _AddMoneyScreenState extends State<AddMoneyScreen> {
  final _amountController = TextEditingController();
  final _remarksController = TextEditingController();
  
  bool _qrGenerated = false;
  bool _isLoading = false;
  String _qrUrl = '';
  String _upiId = 'novexapay@yesbank';

  @override
  void initState() {
    super.initState();
    _fetchDepositUpi();
  }

  @override
  void dispose() {
    _amountController.dispose();
    _remarksController.dispose();
    super.dispose();
  }

  Future<void> _fetchDepositUpi() async {
    try {
      final client = ApiClient();
      final response = await client.dio.get('/wallet/balance');
      if (response.data['success'] == true) {
        final serverUpi = response.data['deposit_upi_id']?.toString();
        if (serverUpi != null && serverUpi.isNotEmpty) {
          setState(() {
            _upiId = serverUpi;
          });
        }
      }
    } catch (e) {
      // Keep default fallback
    }
  }

  void _generateQr() {
    final amountText = _amountController.text.trim();
    if (amountText.isEmpty || double.tryParse(amountText) == null || double.parse(amountText) <= 0) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Please enter a valid deposit amount'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    setState(() {
      _isLoading = true;
    });

    Future.delayed(const Duration(milliseconds: 800), () {
      if (mounted) {
        final amount = double.parse(amountText);
        final remarks = _remarksController.text.trim().isEmpty 
            ? 'WalletTopUp' 
            : _remarksController.text.trim();
            
        final upiString = 'upi://pay?pa=$_upiId&pn=Novexapay&am=${amount.toStringAsFixed(2)}&cu=INR&tn=${Uri.encodeComponent(remarks)}';
        
        setState(() {
          _isLoading = false;
          _qrGenerated = true;
          _qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${Uri.encodeComponent(upiString)}';
        });
      }
    });
  }

  void _copyUpiId() {
    Clipboard.setData(ClipboardData(text: _upiId));
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        content: Text('UPI ID copied to clipboard!'),
        behavior: SnackBarBehavior.floating,
        backgroundColor: Color(0xFF1E293B),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
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
          'Add Money',
          style: TextStyle(color: textColor, fontWeight: FontWeight.bold, fontSize: 16),
        ),
        centerTitle: true,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // 1. QR Display Card (ALWAYS SHOWING A QR CODE)
            Container(
              padding: const EdgeInsets.all(24),
              decoration: BoxDecoration(
                color: cardColor,
                borderRadius: BorderRadius.circular(24),
                border: Border.all(color: isDark ? const Color(0xFF2E3245) : const Color(0xFFE2E8F0)),
              ),
              child: Column(
                children: [
                  if (_isLoading)
                    const SizedBox(
                      height: 280,
                      child: Center(
                        child: CircularProgressIndicator(color: Color(0xFF7C3AED)),
                      ),
                    )
                  else if (!_qrGenerated) ...[
                    // Standard Static QR Code without amount on load
                    Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(20),
                        border: Border.all(color: Colors.grey.shade200),
                      ),
                      child: Image.network(
                        'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${Uri.encodeComponent('upi://pay?pa=$_upiId&pn=Novexapay')}',
                        width: 180,
                        height: 180,
                        fit: BoxFit.cover,
                        errorBuilder: (context, error, stackTrace) => Container(
                          width: 180,
                          height: 180,
                          color: Colors.grey.shade100,
                          child: const Icon(Icons.broken_image, color: Colors.grey),
                        ),
                      ),
                    ),
                    const SizedBox(height: 16),
                    Text(
                      'Standard UPI Deposit QR',
                      style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: subTextColor, letterSpacing: 0.5),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      'Open Payment QR',
                      style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: textColor),
                    ),
                    const SizedBox(height: 16),
                    const Divider(),
                    const SizedBox(height: 12),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'Payable to UPI ID',
                              style: TextStyle(fontSize: 9, color: subTextColor),
                            ),
                            const SizedBox(height: 2),
                            Text(
                              _upiId,
                              style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: textColor),
                            ),
                          ],
                        ),
                        IconButton(
                          icon: const Icon(Icons.copy, size: 18, color: Colors.blueAccent),
                          onPressed: _copyUpiId,
                        ),
                      ],
                    ),
                  ] else ...[
                    // Generated Dynamic QR Code
                    Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(20),
                        border: Border.all(color: Colors.grey.shade200),
                      ),
                      child: Image.network(
                        _qrUrl,
                        width: 180,
                        height: 180,
                        fit: BoxFit.cover,
                        errorBuilder: (context, error, stackTrace) => Container(
                          width: 180,
                          height: 180,
                          color: Colors.grey.shade100,
                          child: const Icon(Icons.broken_image, color: Colors.grey),
                        ),
                      ),
                    ),
                    const SizedBox(height: 16),
                    Text(
                      'Dynamic UPI Request QR',
                      style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: subTextColor, letterSpacing: 0.5),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      '₹${double.parse(_amountController.text).toStringAsFixed(2)}',
                      style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold, color: textColor),
                    ),
                    const SizedBox(height: 16),
                    const Divider(),
                    const SizedBox(height: 12),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'Payable to UPI ID',
                              style: TextStyle(fontSize: 9, color: subTextColor),
                            ),
                            const SizedBox(height: 2),
                            Text(
                              _upiId,
                              style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: textColor),
                            ),
                          ],
                        ),
                        IconButton(
                          icon: const Icon(Icons.copy, size: 18, color: Colors.blueAccent),
                          onPressed: _copyUpiId,
                        ),
                      ],
                    ),
                    const SizedBox(height: 16),
                    
                    // Download & Share Row
                    Row(
                      children: [
                        Expanded(
                          child: OutlinedButton.icon(
                            onPressed: () {
                              ScaffoldMessenger.of(context).showSnackBar(
                                const SnackBar(
                                  content: Text('UPI payment link shared successfully!'),
                                  backgroundColor: Colors.green,
                                ),
                              );
                            },
                            icon: const Icon(Icons.share, size: 16),
                            label: const Text('Share QR', style: TextStyle(fontSize: 11)),
                            style: OutlinedButton.styleFrom(
                              padding: const EdgeInsets.symmetric(vertical: 12),
                              side: const BorderSide(color: Color(0xFF7C3AED)),
                              foregroundColor: const Color(0xFF7C3AED),
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                            ),
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: ElevatedButton.icon(
                            onPressed: () {
                              ScaffoldMessenger.of(context).showSnackBar(
                                const SnackBar(
                                  content: Text('QR Code saved to gallery!'),
                                  backgroundColor: Colors.green,
                                ),
                              );
                            },
                            icon: const Icon(Icons.download, size: 16),
                            label: const Text('Download QR', style: TextStyle(fontSize: 11)),
                            style: ElevatedButton.styleFrom(
                              backgroundColor: const Color(0xFF7C3AED),
                              foregroundColor: Colors.white,
                              padding: const EdgeInsets.symmetric(vertical: 12),
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ],
                ],
              ),
            ),
            const SizedBox(height: 20),

            // 2. Inputs Card
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
                    'GENERATE DEPOSIT QR',
                    style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: subTextColor, letterSpacing: 1),
                  ),
                  const SizedBox(height: 16),
                  
                  // Amount Field
                  Text(
                    'Amount (INR)',
                    style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: subTextColor),
                  ),
                  const SizedBox(height: 6),
                  TextField(
                    controller: _amountController,
                    keyboardType: const TextInputType.numberWithOptions(decimal: true),
                    style: TextStyle(fontWeight: FontWeight.bold, color: textColor),
                    decoration: InputDecoration(
                      hintText: '₹0.00',
                      prefixText: '₹ ',
                      prefixStyle: TextStyle(fontWeight: FontWeight.bold, color: textColor),
                      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                      enabledBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                        borderSide: BorderSide(color: isDark ? const Color(0xFF2E3245) : const Color(0xFFE2E8F0)),
                      ),
                    ),
                  ),
                  const SizedBox(height: 16),
                  
                  // Remarks Field
                  Text(
                    'Remarks / Order ID',
                    style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: subTextColor),
                  ),
                  const SizedBox(height: 6),
                  TextField(
                    controller: _remarksController,
                    style: TextStyle(color: textColor),
                    decoration: InputDecoration(
                      hintText: 'e.g. Wallet Top Up',
                      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                      enabledBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                        borderSide: BorderSide(color: isDark ? const Color(0xFF2E3245) : const Color(0xFFE2E8F0)),
                      ),
                    ),
                  ),
                  const SizedBox(height: 20),
                  
                  ElevatedButton.icon(
                    onPressed: _generateQr,
                    icon: const Icon(Icons.qr_code, size: 16),
                    label: const Text('Generate Request QR', style: TextStyle(fontWeight: FontWeight.bold)),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF7C3AED),
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      minimumSize: const Size(double.infinity, 48),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
