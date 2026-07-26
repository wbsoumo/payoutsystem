import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../providers/auth_provider.dart';

class PinSetupScreen extends ConsumerStatefulWidget {
  const PinSetupScreen({Key? key}) : super(key: key);

  @override
  ConsumerState<PinSetupScreen> createState() => _PinSetupScreenState();
}

class _PinSetupScreenState extends ConsumerState<PinSetupScreen> {
  String _pin = '';
  String _confirmPin = '';
  bool _isConfirming = false;

  void _handleKeyPress(String key) {
    if (_isConfirming) {
      if (_confirmPin.length < 6) {
        HapticFeedback.lightImpact();
        setState(() {
          _confirmPin += key;
        });
      }
    } else {
      if (_pin.length < 6) {
        HapticFeedback.lightImpact();
        setState(() {
          _pin += key;
        });
      }
    }
  }

  void _handleBackspace() {
    HapticFeedback.lightImpact();
    setState(() {
      if (_isConfirming) {
        if (_confirmPin.isNotEmpty) {
          _confirmPin = _confirmPin.substring(0, _confirmPin.length - 1);
        }
      } else {
        if (_pin.isNotEmpty) {
          _pin = _pin.substring(0, _pin.length - 1);
        }
      }
    });
  }

  void _handleSubmit() async {
    final activePin = _isConfirming ? _confirmPin : _pin;

    if (activePin.length < 6) {
      HapticFeedback.vibrate();
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('PIN must be exactly 6 digits.'),
          backgroundColor: Colors.redAccent,
        ),
      );
      return;
    }

    if (!_isConfirming) {
      HapticFeedback.mediumImpact();
      setState(() {
        _isConfirming = true;
      });
      return;
    }

    if (_pin != _confirmPin) {
      HapticFeedback.vibrate();
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('PINs do not match. Restarting setup...'),
          backgroundColor: Colors.redAccent,
        ),
      );
      setState(() {
        _isConfirming = false;
        _pin = '';
        _confirmPin = '';
      });
      return;
    }

    HapticFeedback.mediumImpact();
    await ref.read(authProvider.notifier).registerPin(_pin);
    if (mounted) {
      context.go('/dashboard');
    }
  }

  Widget _buildKeypadButton(String label, VoidCallback onTap, {bool isAction = false, IconData? icon}) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final btnBgColor = isDark 
        ? (isAction ? const Color(0xFF1E293B) : const Color(0xFF1E2235))
        : (isAction ? const Color(0xFFE2E8F0) : const Color(0xFFF1F5F9));
    final textColor = isDark ? Colors.white : const Color(0xFF0F172A);

    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(16),
        child: Container(
          width: 72,
          height: 72,
          alignment: Alignment.center,
          decoration: BoxDecoration(
            color: btnBgColor,
            borderRadius: BorderRadius.circular(16),
            border: Border.all(
              color: isDark ? const Color(0xFF2E3245) : const Color(0xFFE2E8F0),
              width: 1,
            ),
          ),
          child: icon != null 
              ? Icon(icon, color: textColor, size: 24)
              : Text(
                  label,
                  style: TextStyle(
                    fontSize: 24,
                    fontWeight: FontWeight.bold,
                    color: textColor,
                  ),
                ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final textColor = isDark ? Colors.white : const Color(0xFF0F172A);
    final descColor = isDark ? Colors.white70 : Colors.black54;
    final activePin = _isConfirming ? _confirmPin : _pin;

    return Scaffold(
      backgroundColor: isDark ? const Color(0xFF0F172A) : const Color(0xFFF8FAFC),
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: _isConfirming 
            ? IconButton(
                icon: Icon(Icons.arrow_back, color: textColor),
                onPressed: () {
                  setState(() {
                    _isConfirming = false;
                    _confirmPin = '';
                  });
                },
              )
            : null,
        title: Text(
          'Setup Transaction PIN',
          style: TextStyle(color: textColor, fontWeight: FontWeight.bold, fontSize: 16),
        ),
        centerTitle: true,
      ),
      body: SafeArea(
        child: Column(
          children: [
            Expanded(
              child: SingleChildScrollView(
                padding: const EdgeInsets.symmetric(horizontal: 24),
                child: Column(
                  children: [
                    const SizedBox(height: 32),
                    // Elegant Premium Custom Shield Graphic
                    Container(
                      width: 90,
                      height: 90,
                      decoration: BoxDecoration(
                        color: const Color(0xFF7C3AED).withOpacity(0.1),
                        shape: BoxShape.circle,
                        border: Border.all(
                          color: const Color(0xFF7C3AED).withOpacity(0.3),
                          width: 2,
                        ),
                      ),
                      child: const Center(
                        child: Icon(
                          Icons.lock_outline,
                          size: 40,
                          color: Color(0xFF7C3AED),
                        ),
                      ),
                    ),
                    const SizedBox(height: 24),
                    Text(
                      _isConfirming ? 'Confirm your 6-digit PIN' : 'Create 6-digit Transaction PIN',
                      textAlign: TextAlign.center,
                      style: TextStyle(
                        fontSize: 20,
                        fontWeight: FontWeight.bold,
                        color: textColor,
                      ),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      'This PIN is mandatory to authorize payout money transfers securely.',
                      textAlign: TextAlign.center,
                      style: TextStyle(
                        fontSize: 12,
                        color: descColor,
                        height: 1.4,
                      ),
                    ),
                    const SizedBox(height: 48),

                    // Elegant Code slots
                    Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: List.generate(6, (index) {
                        final isFilled = index < activePin.length;
                        return Container(
                          margin: const EdgeInsets.symmetric(horizontal: 8),
                          width: 48,
                          height: 56,
                          decoration: BoxDecoration(
                            color: isDark ? const Color(0xFF1E2235) : Colors.white,
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(
                              color: isFilled
                                  ? const Color(0xFF7C3AED)
                                  : (isDark ? const Color(0xFF2E3245) : const Color(0xFFE2E8F0)),
                              width: isFilled ? 2 : 1,
                            ),
                            boxShadow: isFilled
                                ? [
                                    BoxShadow(
                                      color: const Color(0xFF7C3AED).withOpacity(0.15),
                                      blurRadius: 8,
                                      offset: const Offset(0, 4),
                                    )
                                  ]
                                : [],
                          ),
                          child: Center(
                            child: isFilled
                                ? Container(
                                    width: 14,
                                    height: 14,
                                    decoration: const BoxDecoration(
                                      color: Color(0xFF7C3AED),
                                      shape: BoxShape.circle,
                                    ),
                                  )
                                : Container(
                                    width: 6,
                                    height: 6,
                                    decoration: BoxDecoration(
                                      color: isDark ? Colors.white30 : Colors.grey.shade300,
                                      shape: BoxShape.circle,
                                    ),
                                  ),
                          ),
                        );
                      }),
                    ),
                  ],
                ),
              ),
            ),

            // Tactical Numerical Keypad
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 20),
              child: Column(
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                    children: [
                      _buildKeypadButton('1', () => _handleKeyPress('1')),
                      _buildKeypadButton('2', () => _handleKeyPress('2')),
                      _buildKeypadButton('3', () => _handleKeyPress('3')),
                    ],
                  ),
                  const SizedBox(height: 12),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                    children: [
                      _buildKeypadButton('4', () => _handleKeyPress('4')),
                      _buildKeypadButton('5', () => _handleKeyPress('5')),
                      _buildKeypadButton('6', () => _handleKeyPress('6')),
                    ],
                  ),
                  const SizedBox(height: 12),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                    children: [
                      _buildKeypadButton('7', () => _handleKeyPress('7')),
                      _buildKeypadButton('8', () => _handleKeyPress('8')),
                      _buildKeypadButton('9', () => _handleKeyPress('9')),
                    ],
                  ),
                  const SizedBox(height: 12),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                    children: [
                      _buildKeypadButton('C', () {
                        HapticFeedback.lightImpact();
                        setState(() {
                          if (_isConfirming) {
                            _confirmPin = '';
                          } else {
                            _pin = '';
                          }
                        });
                      }, isAction: true),
                      _buildKeypadButton('0', () => _handleKeyPress('0')),
                      _buildKeypadButton(
                        '', 
                        _handleBackspace, 
                        isAction: true, 
                        icon: Icons.backspace_outlined
                      ),
                    ],
                  ),
                  const SizedBox(height: 24),
                  
                  // Submit Button
                  ElevatedButton(
                    onPressed: activePin.length == 6 ? _handleSubmit : null,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF7C3AED),
                      disabledBackgroundColor: isDark ? const Color(0xFF1E2235) : const Color(0xFFE2E8F0),
                      disabledForegroundColor: isDark ? Colors.white30 : Colors.grey.shade400,
                      minimumSize: const Size.fromHeight(56),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                      elevation: 0,
                    ),
                    child: Text(
                      _isConfirming ? 'Confirm and Save PIN' : 'Next Step',
                      style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15, color: Colors.white),
                    ),
                  ),
                  const SizedBox(height: 16),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
