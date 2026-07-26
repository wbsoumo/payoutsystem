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

  // Draw custom lock without relying on font icons
  Widget _buildCustomLockIcon(Color primaryColor) {
    return SizedBox(
      width: 48,
      height: 48,
      child: Stack(
        alignment: Alignment.center,
        children: [
          // Lock shackle (top curve)
          Positioned(
            top: 4,
            child: Container(
              width: 24,
              height: 24,
              decoration: BoxDecoration(
                border: Border.all(color: primaryColor, width: 3.5),
                borderRadius: const BorderRadius.only(
                  topLeft: Radius.circular(12),
                  topRight: Radius.circular(12),
                ),
              ),
            ),
          ),
          // Lock body
          Positioned(
            bottom: 4,
            child: Container(
              width: 34,
              height: 26,
              decoration: BoxDecoration(
                color: primaryColor,
                borderRadius: BorderRadius.circular(6),
              ),
              child: Center(
                // Keyhole dot
                child: Container(
                  width: 5,
                  height: 5,
                  decoration: const BoxDecoration(
                    color: Colors.white,
                    shape: BoxShape.circle,
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildKeypadButton(String label, VoidCallback onTap, {bool isAction = false, String? customText}) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final btnBgColor = isDark 
        ? (isAction ? const Color(0xFF1E293B) : const Color(0xFF1E2235))
        : (isAction ? const Color(0xFFE2E8F0) : const Color(0xFFF1F5F9));
    final textColor = isDark ? Colors.white : const Color(0xFF0F172A);

    return Expanded(
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 6),
        child: Material(
          color: Colors.transparent,
          child: InkWell(
            onTap: onTap,
            borderRadius: BorderRadius.circular(14),
            child: Container(
              height: 58, // reduced height for responsiveness
              alignment: Alignment.center,
              decoration: BoxDecoration(
                color: btnBgColor,
                borderRadius: BorderRadius.circular(14),
                border: Border.all(
                  color: isDark ? const Color(0xFF2E3245) : const Color(0xFFE2E8F0),
                  width: 1,
                ),
              ),
              child: Text(
                customText ?? label,
                style: TextStyle(
                  fontSize: (customText != null) ? 18 : 22,
                  fontWeight: FontWeight.bold,
                  color: textColor,
                ),
              ),
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
    final screenHeight = MediaQuery.of(context).size.height;

    // Responsive scaling based on viewport height
    final double topSpacing = screenHeight < 650 ? 12 : 24;
    final double middleSpacing = screenHeight < 650 ? 20 : 36;

    return Scaffold(
      backgroundColor: isDark ? const Color(0xFF0F172A) : const Color(0xFFF8FAFC),
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: _isConfirming 
            ? IconButton(
                icon: const Icon(Icons.arrow_back),
                color: textColor,
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
        child: LayoutBuilder(
          builder: (context, constraints) {
            return SingleChildScrollView(
              padding: const EdgeInsets.symmetric(horizontal: 24),
              child: ConstrainedBox(
                constraints: BoxConstraints(
                  minHeight: constraints.maxHeight,
                ),
                child: IntrinsicHeight(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      SizedBox(height: topSpacing),
                      
                      // Lock Graphic
                      Center(
                        child: Container(
                          width: 80,
                          height: 80,
                          decoration: BoxDecoration(
                            color: const Color(0xFF7C3AED).withOpacity(0.1),
                            shape: BoxShape.circle,
                            border: Border.all(
                              color: const Color(0xFF7C3AED).withOpacity(0.2),
                              width: 1.5,
                            ),
                          ),
                          child: _buildCustomLockIcon(const Color(0xFF7C3AED)),
                        ),
                      ),
                      const SizedBox(height: 16),
                      
                      Text(
                        _isConfirming ? 'Confirm your 6-digit PIN' : 'Create 6-digit Transaction PIN',
                        textAlign: TextAlign.center,
                        style: TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                          color: textColor,
                        ),
                      ),
                      const SizedBox(height: 6),
                      Text(
                        'This PIN is mandatory to authorize payout money transfers securely.',
                        textAlign: TextAlign.center,
                        style: TextStyle(
                          fontSize: 11,
                          color: descColor,
                          height: 1.4,
                        ),
                      ),
                      
                      SizedBox(height: middleSpacing),

                      // Code Display Slots
                      Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: List.generate(6, (index) {
                          final isFilled = index < activePin.length;
                          return Container(
                            margin: const EdgeInsets.symmetric(horizontal: 6),
                            width: 42,
                            height: 50,
                            decoration: BoxDecoration(
                              color: isDark ? const Color(0xFF1E2235) : Colors.white,
                              borderRadius: BorderRadius.circular(10),
                              border: Border.all(
                                color: isFilled
                                    ? const Color(0xFF7C3AED)
                                    : (isDark ? const Color(0xFF2E3245) : const Color(0xFFE2E8F0)),
                                width: isFilled ? 2 : 1,
                              ),
                              boxShadow: isFilled
                                  ? [
                                      BoxShadow(
                                        color: const Color(0xFF7C3AED).withOpacity(0.1),
                                        blurRadius: 6,
                                        offset: const Offset(0, 3),
                                      )
                                    ]
                                  : [],
                            ),
                            child: Center(
                              child: isFilled
                                  ? Container(
                                      width: 12,
                                      height: 12,
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
                      
                      const Spacer(), // Pushes Keypad down responsive to constraints

                      // Numerical Keypad Area
                      Column(
                        children: [
                          Row(
                            children: [
                              _buildKeypadButton('1', () => _handleKeyPress('1')),
                              _buildKeypadButton('2', () => _handleKeyPress('2')),
                              _buildKeypadButton('3', () => _handleKeyPress('3')),
                            ],
                          ),
                          const SizedBox(height: 10),
                          Row(
                            children: [
                              _buildKeypadButton('4', () => _handleKeyPress('4')),
                              _buildKeypadButton('5', () => _handleKeyPress('5')),
                              _buildKeypadButton('6', () => _handleKeyPress('6')),
                            ],
                          ),
                          const SizedBox(height: 10),
                          Row(
                            children: [
                              _buildKeypadButton('7', () => _handleKeyPress('7')),
                              _buildKeypadButton('8', () => _handleKeyPress('8')),
                              _buildKeypadButton('9', () => _handleKeyPress('9')),
                            ],
                          ),
                          const SizedBox(height: 10),
                          Row(
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
                                customText: '⌫'
                              ),
                            ],
                          ),
                          
                          const SizedBox(height: 20),
                          
                          // Action Button
                          ElevatedButton(
                            onPressed: activePin.length == 6 ? _handleSubmit : null,
                            style: ElevatedButton.styleFrom(
                              backgroundColor: const Color(0xFF7C3AED),
                              disabledBackgroundColor: isDark ? const Color(0xFF1E2235) : const Color(0xFFE2E8F0),
                              disabledForegroundColor: isDark ? Colors.white30 : Colors.grey.shade400,
                              minimumSize: const Size.fromHeight(52),
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                              elevation: 0,
                            ),
                            child: Text(
                              _isConfirming ? 'Confirm and Save PIN' : 'Next Step',
                              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: Colors.white),
                            ),
                          ),
                          const SizedBox(height: 16),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
            );
          },
        ),
      ),
    );
  }
}
