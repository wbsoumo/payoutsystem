import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../providers/auth_provider.dart';

class PinSetupScreen extends ConsumerStatefulWidget {
  const PinSetupScreen({Key? key}) : super(key: key);

  @override
  ConsumerState<PinSetupScreen> createState() => _PinSetupScreenState();
}

class _PinSetupScreenState extends ConsumerState<PinSetupScreen> {
  final _pinController = TextEditingController();
  final _confirmPinController = TextEditingController();
  bool _isConfirming = false;

  void _handlePinSubmit() async {
    final pin = _pinController.text;
    final confirmPin = _confirmPinController.text;

    if (pin.length < 6) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('PIN must be exactly 6 digits.'), backgroundColor: Colors.red),
      );
      return;
    }

    if (!_isConfirming) {
      setState(() {
        _isConfirming = true;
      });
      return;
    }

    if (pin != confirmPin) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('PINs do not match. Restarting setup...'), backgroundColor: Colors.red),
      );
      setState(() {
        _isConfirming = false;
        _pinController.clear();
        _confirmPinController.clear();
      });
      return;
    }

    // Save transaction pin in secure storage and redirect to dashboard
    await ref.read(authProvider.notifier).registerPin(pin);
    context.go('/dashboard');
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Setup Transaction PIN', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
        centerTitle: true,
      ),
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              const SizedBox(height: 12),
              Icon(Icons.security, size: 64, color: Theme.of(context).primaryColor),
              const SizedBox(height: 24),
              Text(
                _isConfirming ? 'Confirm your 6-digit PIN' : 'Create 6-digit Transaction PIN',
                textAlign: TextAlign.center,
                style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 8),
              const Text(
                'This PIN is mandatory to authorize payout money transfers securely.',
                textAlign: TextAlign.center,
                style: TextStyle(fontSize: 12, color: Colors.black54),
              ),
              const SizedBox(height: 48),

              // PIN Code fields
              TextField(
                controller: _isConfirming ? _confirmPinController : _pinController,
                obscureText: true,
                keyboardType: TextInputType.number,
                maxLength: 6,
                textAlign: TextAlign.center,
                style: const TextStyle(fontSize: 28, letterSpacing: 16, fontWeight: FontWeight.bold),
                decoration: const InputDecoration(
                  counterText: '',
                  border: OutlineInputBorder(borderRadius: BorderRadius.all(Radius.circular(12))),
                  hintText: '••••••',
                ),
              ),
              const SizedBox(height: 48),

              ElevatedButton(
                onPressed: _handlePinSubmit,
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF4361EE),
                  foregroundColor: Colors.white,
                  minimumSize: const Size.fromHeight(50),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
                child: Text(_isConfirming ? 'Confirm PIN' : 'Next', style: const TextStyle(fontWeight: FontWeight.bold)),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
