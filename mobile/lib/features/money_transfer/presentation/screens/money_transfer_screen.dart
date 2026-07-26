import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:dio/dio.dart';
import '../../../auth/presentation/providers/auth_provider.dart';
import '../../../beneficiaries/presentation/providers/beneficiary_provider.dart';
import '../../../../core/network/api_client.dart';

class MoneyTransferScreen extends ConsumerStatefulWidget {
  final String? initialBeneficiaryName;
  const MoneyTransferScreen({Key? key, this.initialBeneficiaryName}) : super(key: key);

  @override
  ConsumerState<MoneyTransferScreen> createState() => _MoneyTransferScreenState();
}

class _MoneyTransferScreenState extends ConsumerState<MoneyTransferScreen> {
  final _amountController = TextEditingController(text: '');
  Map<String, String>? _selectedBeneficiaryData;
  String _selectedBeneficiary = '';
  
  String _availableBalance = '₹0.00';
  bool _isLoadingBalance = true;
  double _numAmount = 0.00;

  @override
  void initState() {
    parentInitState();
    super.initState();
  }

  void parentInitState() {
    _loadBalance();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final beneficiaries = ref.read(beneficiaryProvider);
      if (widget.initialBeneficiaryName != null) {
        for (final b in beneficiaries) {
          if (b['name'] == widget.initialBeneficiaryName) {
            setState(() {
              _selectedBeneficiaryData = Map<String, String>.from(b);
              _selectedBeneficiary = b['name']!;
            });
            break;
          }
        }
      }
      
      if (_selectedBeneficiaryData == null && beneficiaries.isNotEmpty) {
        setState(() {
          _selectedBeneficiaryData = Map<String, String>.from(beneficiaries.first);
          _selectedBeneficiary = beneficiaries.first['name']!;
        });
      }
    });
  }

  Future<void> _loadBalance() async {
    final client = ApiClient();
    try {
      final response = await client.dio.get('/wallet/balance');
      if (response.data['success'] == true) {
        final double bal = double.tryParse(response.data['balance'].toString()) ?? 0.00;
        if (mounted) {
          setState(() {
            _availableBalance = '₹' + bal.toStringAsFixed(2);
            _isLoadingBalance = false;
          });
        }
      }
    } catch (e) {
      if (mounted) {
        setState(() => _isLoadingBalance = false);
      }
    }
  }

  void _addQuickAmount(double value) {
    HapticFeedback.lightImpact();
    setState(() {
      _numAmount += value;
      _amountController.text = _numAmount.toStringAsFixed(2);
    });
  }

  Future<void> _showAddBeneficiarySheet() async {
    final result = await context.push('/add-beneficiary');
    if (result != null && result is Map) {
      setState(() {
        _selectedBeneficiaryData = Map<String, String>.from(result.cast<String, String>());
        _selectedBeneficiary = result['name']!;
      });
    }
  }

  void _showSelectBeneficiarySheet() {
    final beneficiaries = ref.read(beneficiaryProvider);
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final textColor = isDark ? Colors.white : const Color(0xFF1E293B);
    final cardColor = isDark ? const Color(0xFF2E3245) : const Color(0xFFF1F5F9);

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: isDark ? const Color(0xFF1E2235) : Colors.white,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      builder: (context) {
        String searchQuery = '';
        return StatefulBuilder(
          builder: (context, setModalState) {
            final filtered = beneficiaries.where((b) {
              final name = (b['name'] ?? '').toLowerCase();
              final bank = (b['bank'] ?? '').toLowerCase();
              final q = searchQuery.toLowerCase();
              return name.contains(q) || bank.contains(q);
            }).toList();

            return Padding(
              padding: EdgeInsets.fromLTRB(24, 20, 24, MediaQuery.of(context).viewInsets.bottom + 24),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  Center(
                    child: Container(
                      width: 48,
                      height: 5,
                      decoration: BoxDecoration(color: Colors.grey.withOpacity(0.3), borderRadius: BorderRadius.circular(10)),
                    ),
                  ),
                  const SizedBox(height: 20),
                  Text(
                    'Select Beneficiary',
                    style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: textColor),
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 16),
                  
                  // Search Bar inside sheet
                  Container(
                    decoration: BoxDecoration(
                      color: cardColor,
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: TextField(
                      style: TextStyle(color: textColor, fontSize: 12),
                      decoration: InputDecoration(
                        hintText: 'Search beneficiary name or bank...',
                        hintStyle: const TextStyle(color: Colors.grey, fontSize: 12),
                        prefixIcon: const Icon(Icons.search, color: Colors.grey, size: 18),
                        border: InputBorder.none,
                        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                      ),
                      onChanged: (val) {
                        setModalState(() {
                          searchQuery = val;
                        });
                      },
                    ),
                  ),
                  const SizedBox(height: 16),

                  ConstrainedBox(
                    constraints: BoxConstraints(maxHeight: MediaQuery.of(context).size.height * 0.4),
                    child: filtered.isEmpty
                        ? const Padding(
                            padding: EdgeInsets.symmetric(vertical: 32),
                            child: Text('No beneficiaries match your search.', textAlign: TextAlign.center, style: TextStyle(color: Colors.grey, fontSize: 12)),
                          )
                        : ListView.builder(
                            shrinkWrap: true,
                            itemCount: filtered.length,
                            itemBuilder: (context, index) {
                              final b = filtered[index];
                              return ListTile(
                                leading: _buildLetterAvatar(b['name'] ?? ''),
                                title: Text(
                                  b['name']!, 
                                  style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: textColor)
                                ),
                                subtitle: Text(
                                  '${b['bank']} • ${b['account']}', 
                                  style: const TextStyle(fontSize: 10, color: Colors.grey)
                                ),
                                onTap: () {
                                  setState(() {
                                    _selectedBeneficiaryData = Map<String, String>.from(b);
                                    _selectedBeneficiary = b['name']!;
                                  });
                                  Navigator.pop(context);
                                },
                              );
                            },
                          ),
                  ),
                ],
              ),
            );
          },
        );
      },
    );
  }

  Widget _buildLetterAvatar(String name) {
    final initial = name.isNotEmpty ? name[0].toUpperCase() : 'B';
    return Container(
      width: 44,
      height: 44,
      decoration: const BoxDecoration(
        color: Color(0xFF7C3AED),
        shape: BoxShape.circle,
      ),
      alignment: Alignment.center,
      child: Text(
        initial,
        style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16),
      ),
    );
  }

  void _triggerPinValidation() {
    if (_numAmount <= 0) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please enter a valid transfer amount.'), backgroundColor: Colors.red),
      );
      return;
    }

    if (_selectedBeneficiaryData == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please select or add a beneficiary first.'), backgroundColor: Colors.red),
      );
      return;
    }

    // Show the Transaction PIN verification bottom sheet
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final textColor = isDark ? Colors.white : const Color(0xFF1E293B);
    String enteredPin = '';
    String validationError = '';

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: isDark ? const Color(0xFF0F172A) : Colors.white,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      builder: (context) {
        return StatefulBuilder(
          builder: (context, setModalState) {
            void handleKeyPress(String key) async {
              if (enteredPin.length < 6) {
                HapticFeedback.mediumImpact();
                setModalState(() {
                  enteredPin += key;
                  validationError = '';
                });

                if (enteredPin.length == 6) {
                  // Trigger validation automatically
                  final errorMsg = await ref.read(authProvider.notifier).verifyPin(enteredPin);
                  if (errorMsg == null) {
                    Navigator.pop(context); // Close bottom sheet
                    _processPayout(_numAmount);
                  } else {
                    HapticFeedback.vibrate();
                    setModalState(() {
                      enteredPin = '';
                      validationError = errorMsg;
                    });
                  }
                }
              }
            }

            void handleBackspace() {
              if (enteredPin.isNotEmpty) {
                HapticFeedback.lightImpact();
                setModalState(() {
                  enteredPin = enteredPin.substring(0, enteredPin.length - 1);
                  validationError = '';
                });
              }
            }

            return Padding(
              padding: EdgeInsets.fromLTRB(24, 20, 24, MediaQuery.of(context).viewInsets.bottom + 24),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  Center(
                    child: Container(
                      width: 48,
                      height: 5,
                      decoration: BoxDecoration(color: Colors.grey.withOpacity(0.3), borderRadius: BorderRadius.circular(10)),
                    ),
                  ),
                  const SizedBox(height: 20),
                  Text('Enter Transaction PIN', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: textColor), textAlign: TextAlign.center),
                  const SizedBox(height: 8),
                  Text('Enter your 6-digit secure PIN to authorize transfer of ₹${_numAmount.toStringAsFixed(2)}', style: const TextStyle(fontSize: 11, color: Colors.grey), textAlign: TextAlign.center),
                  const SizedBox(height: 28),

                  // Pin visual indicators (6 circles)
                  Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: List.generate(6, (index) {
                      final isFilled = index < enteredPin.length;
                      return AnimatedContainer(
                        duration: const Duration(milliseconds: 150),
                        margin: const EdgeInsets.symmetric(horizontal: 8),
                        width: 14,
                        height: 14,
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          color: isFilled ? const Color(0xFF7C3AED) : Colors.transparent,
                          border: Border.all(color: isFilled ? const Color(0xFF7C3AED) : Colors.grey.shade400, width: 2),
                        ),
                      );
                    }),
                  ),
                  const SizedBox(height: 16),
                  if (validationError.isNotEmpty)
                    Text(validationError, style: const TextStyle(color: Colors.redAccent, fontSize: 11, fontWeight: FontWeight.bold), textAlign: TextAlign.center),
                  const SizedBox(height: 28),

                  // Tactile Keypad
                  Column(
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceAround,
                        children: ['1', '2', '3'].map((digit) => _buildTactileKey(digit, () => handleKeyPress(digit))).toList(),
                      ),
                      const SizedBox(height: 12),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceAround,
                        children: ['4', '5', '6'].map((digit) => _buildTactileKey(digit, () => handleKeyPress(digit))).toList(),
                      ),
                      const SizedBox(height: 12),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceAround,
                        children: ['7', '8', '9'].map((digit) => _buildTactileKey(digit, () => handleKeyPress(digit))).toList(),
                      ),
                      const SizedBox(height: 12),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceAround,
                        children: [
                          const SizedBox(width: 64, height: 60),
                          _buildTactileKey('0', () => handleKeyPress('0')),
                          IconButton(
                            icon: const Text('⌫', style: TextStyle(fontSize: 20, color: Colors.grey, fontWeight: FontWeight.bold)),
                            onPressed: handleBackspace,
                          ),
                        ],
                      ),
                    ],
                  ),
                ],
              ),
            );
          },
        );
      },
    );
  }

  Widget _buildTactileKey(String digit, VoidCallback onTap) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(12),
        child: Container(
          width: 64,
          height: 60,
          alignment: Alignment.center,
          decoration: BoxDecoration(
            color: isDark ? const Color(0xFF1E2235) : const Color(0xFFF1F5F9),
            borderRadius: BorderRadius.circular(12),
          ),
          child: Text(
            digit,
            style: TextStyle(
              fontSize: 20,
              fontWeight: FontWeight.bold,
              color: isDark ? Colors.white : const Color(0xFF0F172A),
            ),
          ),
        ),
      ),
    );
  }

  void _processPayout(double amount) async {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => const Center(child: CircularProgressIndicator(color: Colors.white)),
    );

    final client = ApiClient();
    try {
      final response = await client.dio.post(
        '/payouts',
        data: {
          'client_reference_id': 'ref_${DateTime.now().millisecondsSinceEpoch}',
          'amount': amount,
          'bank_name': _selectedBeneficiaryData!['bank'],
          'bank_account_number': _selectedBeneficiaryData!['account']!.replaceAll('••••', '9999'),
          'bank_ifsc': _selectedBeneficiaryData!['ifsc'],
          'bank_holder_name': _selectedBeneficiaryData!['name'],
        },
      );

      Navigator.pop(context); // Dismiss spinner

      if (response.data['success'] == true) {
        final refId = response.data['reference_id'] ?? 'TXN${DateTime.now().millisecondsSinceEpoch}';
        context.pushReplacement(
          '/receipt?amount=$amount&beneficiary=$_selectedBeneficiary&ref=$refId',
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(response.data['error'] ?? response.data['error_message'] ?? 'Payout failed.'), backgroundColor: Colors.red),
        );
      }
    } catch (e) {
      Navigator.pop(context); // Dismiss spinner
      String errorMsg = 'An error occurred during payout.';
      if (e is DioException && e.response != null) {
        final data = e.response!.data;
        if (data is Map) {
          errorMsg = data['error'] ?? data['error_message'] ?? data['message'] ?? errorMsg;
        }
      }
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(errorMsg), backgroundColor: Colors.red),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final textColor = isDark ? Colors.white : const Color(0xFF1E293B);
    final cardColor = isDark ? const Color(0xFF1E2235) : Colors.white;
    final borderStyleColor = isDark ? const Color(0xFF2E3245) : const Color(0xFFE2E8F0);

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
          'New Money Transfer',
          style: TextStyle(color: textColor, fontWeight: FontWeight.bold, fontSize: 16),
        ),
        centerTitle: true,
        actions: [
          Container(
            margin: const EdgeInsets.only(right: 16),
            padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
            decoration: BoxDecoration(
              color: Colors.green.withOpacity(0.1),
              borderRadius: BorderRadius.circular(8),
            ),
            child: const Row(
              children: [
                Icon(Icons.shield_outlined, color: Colors.green, size: 14),
                SizedBox(width: 4),
                Text('Secure', style: TextStyle(color: Colors.green, fontSize: 10, fontWeight: FontWeight.bold)),
              ],
            ),
          )
        ],
      ),
      body: Column(
        children: [
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(24),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  // 2. Select Beneficiary Header
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        'Select Beneficiary',
                        style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: textColor),
                      ),
                      TextButton(
                        onPressed: _showAddBeneficiarySheet,
                        style: TextButton.styleFrom(padding: EdgeInsets.zero, minimumSize: Size.zero, tapTargetSize: MaterialTapTargetSize.shrinkWrap),
                        child: const Text(
                          '+ Add New',
                          style: TextStyle(color: Color(0xFF7C3AED), fontWeight: FontWeight.bold, fontSize: 12),
                        ),
                      )
                    ],
                  ),
                  const SizedBox(height: 12),

                  // Selected Beneficiary Display Card
                  if (_selectedBeneficiaryData != null)
                    Container(
                      decoration: BoxDecoration(
                        color: cardColor,
                        borderRadius: BorderRadius.circular(20),
                        border: Border.all(color: borderStyleColor),
                      ),
                      child: ListTile(
                        contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
                        leading: _buildLetterAvatar(_selectedBeneficiaryData!['name'] ?? ''),
                        title: Row(
                          children: [
                            Text(
                              _selectedBeneficiaryData!['name'] ?? '',
                              style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: textColor),
                            ),
                            const SizedBox(width: 8),
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                              decoration: BoxDecoration(
                                color: const Color(0xFF7C3AED).withOpacity(0.1),
                                borderRadius: BorderRadius.circular(6),
                              ),
                              child: const Text('Saved', style: TextStyle(color: Color(0xFF7C3AED), fontSize: 9, fontWeight: FontWeight.bold)),
                            )
                          ],
                        ),
                        subtitle: Padding(
                          padding: const EdgeInsets.only(top: 4),
                          child: Text(
                            '${_selectedBeneficiaryData!['bank']}\nIFSC: ${_selectedBeneficiaryData!['ifsc']}',
                            style: const TextStyle(fontSize: 10, color: Colors.grey, height: 1.3),
                          ),
                        ),
                        trailing: Icon(Icons.keyboard_arrow_down, color: textColor),
                        onTap: _showSelectBeneficiarySheet,
                      ),
                    ),
                  const SizedBox(height: 12),

                  // Verified Alert Box
                  Container(
                    padding: const EdgeInsets.all(14),
                    decoration: BoxDecoration(
                      color: const Color(0xFF7C3AED).withOpacity(0.05),
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(color: const Color(0xFF7C3AED).withOpacity(0.15)),
                    ),
                    child: Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.all(8),
                          decoration: BoxDecoration(
                            color: const Color(0xFF7C3AED).withOpacity(0.1),
                            borderRadius: BorderRadius.circular(10),
                          ),
                          child: const Icon(Icons.verified_user_outlined, color: Color(0xFF7C3AED), size: 18),
                        ),
                        const SizedBox(width: 12),
                        const Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text('Verified Beneficiary', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 11, color: Color(0xFF7C3AED))),
                              SizedBox(height: 2),
                              Text('This beneficiary is verified and secure', style: TextStyle(fontSize: 9, color: Colors.grey)),
                            ],
                          ),
                        ),
                        const Icon(Icons.check_circle, color: Colors.green, size: 16),
                      ],
                    ),
                  ),

                  const SizedBox(height: 32),

                  // 3. Transfer Amount Header
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        'Transfer Amount',
                        style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: textColor),
                      ),
                      Text(
                        'Available Balance: $_availableBalance',
                        style: const TextStyle(color: Colors.grey, fontSize: 11),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),

                  // Amount Card Input Layout
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 20),
                    decoration: BoxDecoration(
                      color: cardColor,
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(color: borderStyleColor),
                    ),
                    child: Column(
                      children: [
                        Row(
                          crossAxisAlignment: CrossAxisAlignment.center,
                          children: [
                            const Text(
                              '₹',
                              style: TextStyle(fontSize: 28, fontWeight: FontWeight.bold, color: Color(0xFF7C3AED)),
                            ),
                            const SizedBox(width: 12),
                            Expanded(
                              child: TextField(
                                controller: _amountController,
                                keyboardType: const TextInputType.numberWithOptions(decimal: true),
                                style: TextStyle(fontSize: 28, fontWeight: FontWeight.bold, color: textColor),
                                decoration: const InputDecoration(
                                  border: InputBorder.none,
                                  isDense: true,
                                  hintText: '0.00',
                                  hintStyle: TextStyle(color: Colors.grey),
                                ),
                                onChanged: (val) {
                                  setState(() {
                                    _numAmount = double.tryParse(val) ?? 0.00;
                                  });
                                },
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 20),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [100, 500, 1000, 5000].map((val) {
                            return OutlinedButton(
                              onPressed: () => _addQuickAmount(val.toDouble()),
                              style: OutlinedButton.styleFrom(
                                side: BorderSide(color: const Color(0xFF7C3AED).withOpacity(0.3)),
                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                              ),
                              child: Text(
                                '+ ₹$val',
                                style: const TextStyle(fontSize: 10, color: Color(0xFF7C3AED), fontWeight: FontWeight.bold),
                              ),
                            );
                          }).toList(),
                        ),
                      ],
                    ),
                  ),

                  const SizedBox(height: 20),

                  // Fee-Free alert message bar
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                    decoration: BoxDecoration(
                      color: Colors.green.withOpacity(0.05),
                      borderRadius: BorderRadius.circular(14),
                      border: Border.all(color: Colors.green.withOpacity(0.15)),
                    ),
                    child: Row(
                      children: [
                        const Icon(Icons.verified, color: Colors.green, size: 16),
                        const SizedBox(width: 8),
                        const Expanded(
                          child: Text(
                            'No transaction fee on bank transfers',
                            style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.green),
                          ),
                        ),
                        Icon(Icons.chevron_right, color: Colors.green.withOpacity(0.5), size: 16),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),

          // Unified Sticky Footer Layout
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 20),
            decoration: BoxDecoration(
              color: cardColor,
              border: Border(top: BorderSide(color: borderStyleColor)),
            ),
            child: Row(
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      const Text(
                        'You are sending',
                        style: TextStyle(color: Colors.grey, fontSize: 10),
                      ),
                      const SizedBox(height: 4),
                      Text(
                        '₹${_numAmount.toStringAsFixed(2)}',
                        style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: textColor),
                      ),
                    ],
                  ),
                ),
                ElevatedButton(
                  onPressed: _triggerPinValidation,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF7C3AED),
                    padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                  ),
                  child: const Row(
                    children: [
                      Text('Continue to PIN Validation', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 12)),
                      SizedBox(width: 8),
                      Icon(Icons.arrow_forward, size: 14, color: Colors.white),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
