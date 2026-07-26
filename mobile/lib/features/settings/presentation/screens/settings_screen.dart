import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/theme/theme_provider.dart';
import '../../../auth/presentation/providers/auth_provider.dart';

class SettingsScreen extends ConsumerWidget {
  const SettingsScreen({Key? key}) : super(key: key);

  void _showChangePinDialog(BuildContext context, WidgetRef ref) {
    final currentController = TextEditingController();
    final newController = TextEditingController();
    final confirmController = TextEditingController();
    final formKey = GlobalKey<FormState>();
    bool isLoading = false;
    String? dialogError;

    showDialog(
      context: context,
      builder: (context) {
        final isDark = Theme.of(context).brightness == Brightness.dark;
        final textColor = isDark ? Colors.white : const Color(0xFF1E293B);

        return StatefulBuilder(
          builder: (context, setDialogState) {
            return AlertDialog(
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
              title: const Text('Change Transaction PIN', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
              content: Form(
                key: formKey,
                child: SingleChildScrollView(
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      const Text(
                        'Confirm your current 6-digit PIN and choose a secure new one.',
                        style: TextStyle(fontSize: 11, color: Colors.grey),
                      ),
                      const SizedBox(height: 20),
                      if (dialogError != null) ...[
                        Text(
                          dialogError!,
                          style: const TextStyle(color: Colors.red, fontSize: 11, fontWeight: FontWeight.bold),
                        ),
                        const SizedBox(height: 12),
                      ],
                      // Current PIN
                      TextFormField(
                        controller: currentController,
                        obscureText: true,
                        keyboardType: TextInputType.number,
                        maxLength: 6,
                        style: TextStyle(color: textColor),
                        inputFormatters: [FilteringTextInputFormatter.digitsOnly],
                        decoration: InputDecoration(
                          labelText: 'Current PIN',
                          counterText: '',
                          prefixIcon: const Icon(Icons.lock_outline),
                          border: OutlineInputBorder(borderRadius: BorderRadius.circular(14)),
                        ),
                        validator: (val) {
                          if (val == null || val.length != 6) {
                            return 'Enter 6 digits';
                          }
                          return null;
                        },
                      ),
                      const SizedBox(height: 16),
                      // New PIN
                      TextFormField(
                        controller: newController,
                        obscureText: true,
                        keyboardType: TextInputType.number,
                        maxLength: 6,
                        style: TextStyle(color: textColor),
                        inputFormatters: [FilteringTextInputFormatter.digitsOnly],
                        decoration: InputDecoration(
                          labelText: 'New PIN',
                          counterText: '',
                          prefixIcon: const Icon(Icons.vpn_key_outlined),
                          border: OutlineInputBorder(borderRadius: BorderRadius.circular(14)),
                        ),
                        validator: (val) {
                          if (val == null || val.length != 6) {
                            return 'Enter 6 digits';
                          }
                          if (val == currentController.text) {
                            return 'Must be different';
                          }
                          return null;
                        },
                      ),
                      const SizedBox(height: 16),
                      // Confirm New PIN
                      TextFormField(
                        controller: confirmController,
                        obscureText: true,
                        keyboardType: TextInputType.number,
                        maxLength: 6,
                        style: TextStyle(color: textColor),
                        inputFormatters: [FilteringTextInputFormatter.digitsOnly],
                        decoration: InputDecoration(
                          labelText: 'Confirm New PIN',
                          counterText: '',
                          prefixIcon: const Icon(Icons.done_all_outlined),
                          border: OutlineInputBorder(borderRadius: BorderRadius.circular(14)),
                        ),
                        validator: (val) {
                          if (val != newController.text) {
                            return 'PINs do not match';
                          }
                          return null;
                        },
                      ),
                    ],
                  ),
                ),
              ),
              actionsPadding: const EdgeInsets.fromLTRB(16, 0, 16, 16),
              actions: [
                TextButton(
                  onPressed: isLoading ? null : () => Navigator.pop(context),
                  child: const Text('Cancel'),
                ),
                ElevatedButton(
                  onPressed: isLoading
                      ? null
                      : () async {
                          if (formKey.currentState!.validate()) {
                            setDialogState(() {
                              isLoading = true;
                              dialogError = null;
                            });

                            final error = await ref
                                .read(authProvider.notifier)
                                .changePin(currentController.text, newController.text);

                            if (error == null) {
                              Navigator.pop(context);
                              ScaffoldMessenger.of(context).showSnackBar(
                                const SnackBar(
                                  content: Text('Transaction PIN updated successfully!'),
                                  backgroundColor: Colors.green,
                                ),
                              );
                            } else {
                              setDialogState(() {
                                isLoading = false;
                                dialogError = error;
                              });
                            }
                          }
                        },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF7C3AED),
                    foregroundColor: Colors.white,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                  child: isLoading
                      ? const SizedBox(width: 16, height: 16, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                      : const Text('Change PIN'),
                ),
              ],
            );
          },
        );
      },
    );
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final themeMode = ref.watch(themeProvider);
    final isDark = themeMode == ThemeMode.dark;

    return Scaffold(
      appBar: AppBar(title: const Text('System Preferences', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16))),
      body: ListView(
        padding: const EdgeInsets.all(20),
        children: [
          const Text('SECURITY SETTINGS', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey)),
          const SizedBox(height: 8),
          Card(
            child: Column(
              children: [
                SwitchListTile(
                  title: const Text('Enable Biometric Login', style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold)),
                  subtitle: const Text('Use Fingerprint or Face ID to unlock', style: TextStyle(fontSize: 10)),
                  value: true,
                  onChanged: (val) {},
                ),
                const Divider(height: 1),
                ListTile(
                  title: const Text('Change Transaction PIN', style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold)),
                  trailing: const Icon(Icons.arrow_forward_ios, size: 14),
                  onTap: () => _showChangePinDialog(context, ref),
                ),
              ],
            ),
          ),
          const SizedBox(height: 24),

          const Text('THEMING & DISPLAY', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey)),
          const SizedBox(height: 8),
          Card(
            child: Column(
              children: [
                SwitchListTile(
                  title: const Text('Dark Mode Display', style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold)),
                  value: isDark,
                  onChanged: (val) {
                    ref.read(themeProvider.notifier).toggleTheme(val);
                  },
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
