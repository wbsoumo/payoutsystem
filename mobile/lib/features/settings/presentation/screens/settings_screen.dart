import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/theme/theme_provider.dart';
import '../../../../core/network/api_client.dart';
import '../../../auth/presentation/providers/auth_provider.dart';

class SettingsScreen extends ConsumerWidget {
  const SettingsScreen({Key? key}) : super(key: key);

  void _navigateToPage(BuildContext context, String title, Widget child) {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => _FullPageWrapper(title: title, child: child),
      ),
    );
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final themeMode = ref.watch(themeProvider);
    final isDark = themeMode == ThemeMode.dark;
    final textColor = isDark ? Colors.white : const Color(0xFF1E293B);
    final cardColor = isDark ? const Color(0xFF1E2235) : Colors.white;

    final List<Map<String, dynamic>> settingsItems = [
      {
        'title': 'Account Settings',
        'icon': Icons.person_outline,
        'page': const _AccountSettingsPage(),
      },
      {
        'title': 'Change Password',
        'icon': Icons.lock_open_outlined,
        'page': const _ChangePasswordPage(),
      },
      {
        'title': 'Change Transaction PIN',
        'icon': Icons.pin_outlined,
        'page': const _ChangePinPage(),
      },
      {
        'title': 'Biometric Login',
        'icon': Icons.fingerprint_outlined,
        'page': const _BiometricLoginPage(),
      },
      {
        'title': 'Notifications',
        'icon': Icons.notifications_none_outlined,
        'page': const _NotificationsSettingsPage(),
      },
      {
        'title': 'Appearance (Light/Dark)',
        'icon': Icons.palette_outlined,
        'page': const _AppearanceSettingsPage(),
      },
      {
        'title': 'Language',
        'icon': Icons.translate_outlined,
        'page': const _LanguageSettingsPage(),
      },
      {
        'title': 'Privacy & Security',
        'icon': Icons.shield_outlined,
        'page': const _PrivacySecurityPage(),
      },
      {
        'title': 'Terms & Privacy Policy',
        'icon': Icons.description_outlined,
        'page': const _TermsPrivacyPage(),
      },
      {
        'title': 'About App',
        'icon': Icons.info_outline,
        'page': const _AboutAppPage(),
      },
    ];

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
          'Settings',
          style: TextStyle(color: textColor, fontWeight: FontWeight.bold, fontSize: 16),
        ),
        centerTitle: true,
      ),
      body: ListView.separated(
        padding: const EdgeInsets.all(24),
        itemCount: settingsItems.length,
        separatorBuilder: (context, index) => const SizedBox(height: 12),
        itemBuilder: (context, index) {
          final item = settingsItems[index];
          return Container(
            decoration: BoxDecoration(
              color: cardColor,
              borderRadius: BorderRadius.circular(20),
              border: Border.all(color: isDark ? const Color(0xFF2E3245) : const Color(0xFFE2E8F0)),
            ),
            child: ListTile(
              contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 4),
              leading: Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: const Color(0xFF7C3AED).withOpacity(0.1),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Icon(
                  item['icon'] as IconData,
                  color: const Color(0xFF7C3AED),
                  size: 20,
                ),
              ),
              title: Text(
                item['title'] as String,
                style: TextStyle(
                  fontWeight: FontWeight.w600,
                  fontSize: 13,
                  color: textColor,
                ),
              ),
              trailing: Icon(
                Icons.chevron_right,
                size: 18,
                color: isDark ? Colors.white30 : Colors.grey.shade400,
              ),
              onTap: () => _navigateToPage(context, item['title'] as String, item['page'] as Widget),
            ),
          );
        },
      ),
    );
  }
}

// Reusable Page Wrapper
class _FullPageWrapper extends StatelessWidget {
  final String title;
  final Widget child;

  const _FullPageWrapper({
    Key? key,
    required this.title,
    required this.child,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final textColor = isDark ? Colors.white : const Color(0xFF1E293B);

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
          title,
          style: TextStyle(color: textColor, fontWeight: FontWeight.bold, fontSize: 16),
        ),
        centerTitle: true,
      ),
      body: SafeArea(child: child),
    );
  }
}

// 1. Account Settings Page (CONNECT WITH SERVER)
class _AccountSettingsPage extends StatefulWidget {
  const _AccountSettingsPage({Key? key}) : super(key: key);

  @override
  State<_AccountSettingsPage> createState() => _AccountSettingsPageState();
}

class _AccountSettingsPageState extends State<_AccountSettingsPage> {
  final _nameController = TextEditingController();
  final _emailController = TextEditingController();
  final _phoneController = TextEditingController();
  bool _isLoading = true;
  bool _isSaving = false;

  @override
  void initState() {
    super.initState();
    _loadProfile();
  }

  Future<void> _loadProfile() async {
    final client = ApiClient();
    try {
      final response = await client.dio.get('/profile');
      if (response.data['success'] == true) {
        final profile = response.data['profile'] ?? {};
        setState(() {
          _nameController.text = profile['name'] ?? '';
          _emailController.text = profile['email'] ?? '';
          _phoneController.text = profile['phone'] ?? '';
          _isLoading = false;
        });
      }
    } catch (e) {
      setState(() => _isLoading = false);
    }
  }

  Future<void> _updateProfile() async {
    setState(() => _isSaving = true);
    final client = ApiClient();
    try {
      final response = await client.dio.post('/profile/update', data: {
        'name': _nameController.text.trim(),
        'email': _emailController.text.trim(),
        'phone': _phoneController.text.trim(),
      });
      if (mounted) {
        setState(() => _isSaving = false);
        if (response.data['success'] == true) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Account updated successfully!'), backgroundColor: Colors.green),
          );
          Navigator.pop(context);
        }
      }
    } catch (e) {
      setState(() => _isSaving = false);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Failed to update account details'), backgroundColor: Colors.red),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final textColor = isDark ? Colors.white : const Color(0xFF1E293B);

    if (_isLoading) {
      return const Center(child: CircularProgressIndicator(color: Color(0xFF7C3AED)));
    }

    return SingleChildScrollView(
      padding: const EdgeInsets.all(24),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          TextField(
            controller: _nameController,
            style: TextStyle(color: textColor),
            decoration: const InputDecoration(labelText: 'Name', border: OutlineInputBorder()),
          ),
          const SizedBox(height: 16),
          TextField(
            controller: _emailController,
            style: TextStyle(color: textColor),
            decoration: const InputDecoration(labelText: 'Email Address', border: OutlineInputBorder()),
          ),
          const SizedBox(height: 16),
          TextField(
            controller: _phoneController,
            style: TextStyle(color: textColor),
            decoration: const InputDecoration(labelText: 'Phone Number', border: OutlineInputBorder()),
          ),
          const SizedBox(height: 24),
          ElevatedButton(
            onPressed: _isSaving ? null : _updateProfile,
            style: ElevatedButton.styleFrom(
              backgroundColor: const Color(0xFF7C3AED),
              padding: const EdgeInsets.symmetric(vertical: 16),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            ),
            child: _isSaving 
                ? const SizedBox(width: 16, height: 16, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                : const Text('Update Profile', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
          )
        ],
      ),
    );
  }
}

// 2. Change Password Page (CONNECT WITH SERVER)
class _ChangePasswordPage extends StatefulWidget {
  const _ChangePasswordPage({Key? key}) : super(key: key);

  @override
  State<_ChangePasswordPage> createState() => _ChangePasswordPageState();
}

class _ChangePasswordPageState extends State<_ChangePasswordPage> {
  final _currentController = TextEditingController();
  final _newController = TextEditingController();
  final _confirmController = TextEditingController();
  bool _isSaving = false;

  Future<void> _updatePassword() async {
    final newPass = _newController.text.trim();
    final confirmPass = _confirmController.text.trim();

    if (newPass.isEmpty || confirmPass.isEmpty || _currentController.text.trim().isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please fill all fields'), backgroundColor: Colors.red),
      );
      return;
    }

    if (newPass != confirmPass) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('New passwords do not match'), backgroundColor: Colors.red),
      );
      return;
    }

    setState(() => _isSaving = true);
    final client = ApiClient();
    try {
      final response = await client.dio.post('/profile/password', data: {
        'current_password': _currentController.text,
        'new_password': newPass,
      });
      if (mounted) {
        setState(() => _isSaving = false);
        if (response.data['success'] == true) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Password updated successfully!'), backgroundColor: Colors.green),
          );
          Navigator.pop(context);
        }
      }
    } catch (e) {
      setState(() => _isSaving = false);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Password change failed. Please verify current password.'), backgroundColor: Colors.red),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final textColor = isDark ? Colors.white : const Color(0xFF1E293B);

    return SingleChildScrollView(
      padding: const EdgeInsets.all(24),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          TextField(
            controller: _currentController,
            obscureText: true,
            style: TextStyle(color: textColor),
            decoration: const InputDecoration(labelText: 'Current Password', border: OutlineInputBorder()),
          ),
          const SizedBox(height: 16),
          TextField(
            controller: _newController,
            obscureText: true,
            style: TextStyle(color: textColor),
            decoration: const InputDecoration(labelText: 'New Password', border: OutlineInputBorder()),
          ),
          const SizedBox(height: 16),
          TextField(
            controller: _confirmController,
            obscureText: true,
            style: TextStyle(color: textColor),
            decoration: const InputDecoration(labelText: 'Confirm Password', border: OutlineInputBorder()),
          ),
          const SizedBox(height: 24),
          ElevatedButton(
            onPressed: _isSaving ? null : _updatePassword,
            style: ElevatedButton.styleFrom(
              backgroundColor: const Color(0xFF7C3AED),
              padding: const EdgeInsets.symmetric(vertical: 16),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            ),
            child: _isSaving
                ? const SizedBox(width: 16, height: 16, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                : const Text('Change Password', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
          )
        ],
      ),
    );
  }
}

// 3. Change Transaction PIN Page
class _ChangePinPage extends ConsumerStatefulWidget {
  const _ChangePinPage({Key? key}) : super(key: key);

  @override
  ConsumerState<_ChangePinPage> createState() => _ChangePinPageState();
}

class _ChangePinPageState extends ConsumerState<_ChangePinPage> {
  final _currentController = TextEditingController();
  final _newController = TextEditingController();
  final _confirmController = TextEditingController();
  final _formKey = GlobalKey<FormState>();
  bool _isLoading = false;
  String? _error;

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final textColor = isDark ? Colors.white : const Color(0xFF1E293B);

    return SingleChildScrollView(
      padding: const EdgeInsets.all(24),
      child: Form(
        key: _formKey,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            if (_error != null) ...[
              Text(_error!, style: const TextStyle(color: Colors.red, fontWeight: FontWeight.bold)),
              const SizedBox(height: 16),
            ],
            TextFormField(
              controller: _currentController,
              obscureText: true,
              keyboardType: TextInputType.number,
              maxLength: 6,
              style: TextStyle(color: textColor),
              inputFormatters: [FilteringTextInputFormatter.digitsOnly],
              decoration: const InputDecoration(labelText: 'Current PIN', border: OutlineInputBorder()),
              validator: (val) => (val?.length != 6) ? 'Enter 6 digits' : null,
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _newController,
              obscureText: true,
              keyboardType: TextInputType.number,
              maxLength: 6,
              style: TextStyle(color: textColor),
              inputFormatters: [FilteringTextInputFormatter.digitsOnly],
              decoration: const InputDecoration(labelText: 'New PIN', border: OutlineInputBorder()),
              validator: (val) => (val?.length != 6) ? 'Enter 6 digits' : null,
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _confirmController,
              obscureText: true,
              keyboardType: TextInputType.number,
              maxLength: 6,
              style: TextStyle(color: textColor),
              inputFormatters: [FilteringTextInputFormatter.digitsOnly],
              decoration: const InputDecoration(labelText: 'Confirm New PIN', border: OutlineInputBorder()),
              validator: (val) => (val != _newController.text) ? 'PINs do not match' : null,
            ),
            const SizedBox(height: 24),
            ElevatedButton(
              onPressed: _isLoading
                  ? null
                  : () async {
                      if (_formKey.currentState!.validate()) {
                        setState(() {
                          _isLoading = true;
                          _error = null;
                        });
                        final err = await ref
                            .read(authProvider.notifier)
                            .changePin(_currentController.text, _newController.text);
                        if (mounted) {
                          setState(() {
                            _isLoading = false;
                          });
                          if (err == null) {
                            Navigator.pop(context);
                            ScaffoldMessenger.of(context).showSnackBar(
                              const SnackBar(content: Text('Transaction PIN updated successfully!'), backgroundColor: Colors.green),
                            );
                          } else {
                            setState(() {
                              _error = err;
                            });
                          }
                        }
                      }
                    },
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFF7C3AED),
                padding: const EdgeInsets.symmetric(vertical: 16),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              ),
              child: _isLoading
                  ? const SizedBox(width: 16, height: 16, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                  : const Text('Change PIN', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
            ),
          ],
        ),
      ),
    );
  }
}

// 4. Biometric Login Page
class _BiometricLoginPage extends StatefulWidget {
  const _BiometricLoginPage({Key? key}) : super(key: key);

  @override
  State<_BiometricLoginPage> createState() => _BiometricLoginPageState();
}

class _BiometricLoginPageState extends State<_BiometricLoginPage> {
  bool _biometricEnabled = true;

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final cardColor = isDark ? const Color(0xFF1E2235) : Colors.white;

    return Padding(
      padding: const EdgeInsets.all(24),
      child: Column(
        children: [
          Container(
            decoration: BoxDecoration(
              color: cardColor,
              borderRadius: BorderRadius.circular(20),
            ),
            child: SwitchListTile(
              title: const Text('Fingerprint / Face ID Login', style: TextStyle(fontWeight: FontWeight.bold)),
              subtitle: const Text('Access your wallet instantly using biometrics'),
              value: _biometricEnabled,
              onChanged: (val) {
                setState(() {
                  _biometricEnabled = val;
                });
              },
            ),
          )
        ],
      ),
    );
  }
}

// 5. Notifications Settings Page (CONNECT WITH SERVER)
class _NotificationsSettingsPage extends StatefulWidget {
  const _NotificationsSettingsPage({Key? key}) : super(key: key);

  @override
  State<_NotificationsSettingsPage> createState() => _NotificationsSettingsPageState();
}

class _NotificationsSettingsPageState extends State<_NotificationsSettingsPage> {
  bool _email = true;
  bool _push = true;
  bool _sms = false;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadNotificationPreferences();
  }

  Future<void> _loadNotificationPreferences() async {
    final client = ApiClient();
    try {
      final response = await client.dio.get('/profile');
      if (response.data['success'] == true) {
        final notifications = response.data['profile']['notifications'] ?? {};
        setState(() {
          _email = notifications['email'] ?? true;
          _push = notifications['push'] ?? true;
          _sms = notifications['sms'] ?? false;
          _isLoading = false;
        });
      }
    } catch (e) {
      setState(() => _isLoading = false);
    }
  }

  Future<void> _savePreferences() async {
    final client = ApiClient();
    try {
      await client.dio.post('/profile/notifications', data: {
        'email': _email,
        'push': _push,
        'sms': _sms,
      });
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Preferences saved successfully!'), backgroundColor: Colors.green),
      );
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Failed to save notification preferences'), backgroundColor: Colors.red),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final cardColor = isDark ? const Color(0xFF1E2235) : Colors.white;

    if (_isLoading) {
      return const Center(child: CircularProgressIndicator(color: Color(0xFF7C3AED)));
    }

    return ListView(
      padding: const EdgeInsets.all(24),
      children: [
        Container(
          decoration: BoxDecoration(color: cardColor, borderRadius: BorderRadius.circular(20)),
          child: Column(
            children: [
              SwitchListTile(
                title: const Text('Email Alerts'),
                value: _email,
                onChanged: (val) {
                  setState(() => _email = val);
                  _savePreferences();
                },
              ),
              const Divider(height: 1),
              SwitchListTile(
                title: const Text('Push Notifications'),
                value: _push,
                onChanged: (val) {
                  setState(() => _push = val);
                  _savePreferences();
                },
              ),
              const Divider(height: 1),
              SwitchListTile(
                title: const Text('SMS Transactions'),
                value: _sms,
                onChanged: (val) {
                  setState(() => _sms = val);
                  _savePreferences();
                },
              ),
            ],
          ),
        )
      ],
    );
  }
}

// 6. Appearance Settings Page
class _AppearanceSettingsPage extends ConsumerWidget {
  const _AppearanceSettingsPage({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final themeMode = ref.watch(themeProvider);
    final isDark = themeMode == ThemeMode.dark;
    final cardColor = isDark ? const Color(0xFF1E2235) : Colors.white;

    return Padding(
      padding: const EdgeInsets.all(24),
      child: Container(
        decoration: BoxDecoration(color: cardColor, borderRadius: BorderRadius.circular(20)),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            RadioListTile<ThemeMode>(
              title: const Text('Light Mode'),
              value: ThemeMode.light,
              groupValue: themeMode,
              onChanged: (val) {
                if (val != null) {
                  ref.read(themeProvider.notifier).toggleTheme(false);
                }
              },
            ),
            const Divider(height: 1),
            RadioListTile<ThemeMode>(
              title: const Text('Dark Mode'),
              value: ThemeMode.dark,
              groupValue: themeMode,
              onChanged: (val) {
                if (val != null) {
                  ref.read(themeProvider.notifier).toggleTheme(true);
                }
              },
            ),
          ],
        ),
      ),
    );
  }
}

// 7. Language Settings Page
class _LanguageSettingsPage extends StatefulWidget {
  const _LanguageSettingsPage({Key? key}) : super(key: key);

  @override
  State<_LanguageSettingsPage> createState() => _LanguageSettingsPageState();
}

class _LanguageSettingsPageState extends State<_LanguageSettingsPage> {
  String _lang = 'en';

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final cardColor = isDark ? const Color(0xFF1E2235) : Colors.white;

    return Padding(
      padding: const EdgeInsets.all(24),
      child: Container(
        decoration: BoxDecoration(color: cardColor, borderRadius: BorderRadius.circular(20)),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            RadioListTile<String>(
              title: const Text('English'),
              value: 'en',
              groupValue: _lang,
              onChanged: (val) => setState(() => _lang = val!),
            ),
            const Divider(height: 1),
            RadioListTile<String>(
              title: const Text('हिंदी (Hindi)'),
              value: 'hi',
              groupValue: _lang,
              onChanged: (val) => setState(() => _lang = val!),
            ),
            const Divider(height: 1),
            RadioListTile<String>(
              title: const Text('বাংলা (Bengali)'),
              value: 'bn',
              groupValue: _lang,
              onChanged: (val) => setState(() => _lang = val!),
            ),
          ],
        ),
      ),
    );
  }
}

// 8. Privacy & Security Page
class _PrivacySecurityPage extends StatelessWidget {
  const _PrivacySecurityPage({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final textColor = isDark ? Colors.white : const Color(0xFF1E293B);

    return Padding(
      padding: const EdgeInsets.all(24),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text('Data Protection & Security', style: TextStyle(fontWeight: FontWeight.bold, color: textColor, fontSize: 14)),
          const SizedBox(height: 12),
          Text(
            'Novexapay secures all transactional interactions using state-of-the-art API signatures. Under no circumstances are raw transaction PINs or credentials stored in plain text. Multi-factor verification is enforced dynamically.',
            style: TextStyle(color: isDark ? Colors.white60 : Colors.black54, fontSize: 12, height: 1.4),
          )
        ],
      ),
    );
  }
}

// 9. Terms & Privacy Policy Page
class _TermsPrivacyPage extends StatelessWidget {
  const _TermsPrivacyPage({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final textColor = isDark ? Colors.white : const Color(0xFF1E293B);

    return SingleChildScrollView(
      padding: const EdgeInsets.all(24),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text('Terms of Service', style: TextStyle(fontWeight: FontWeight.bold, color: textColor, fontSize: 14)),
          const SizedBox(height: 8),
          Text(
            'By using the Novexapay mobile merchant application, you agree to all merchant settlement rates, commissions, and API security guidelines. System abuse or unauthorized impersonation triggers immediate service limitation.',
            style: TextStyle(color: isDark ? Colors.white60 : Colors.black54, fontSize: 12, height: 1.4),
          ),
          const SizedBox(height: 20),
          Text('Privacy Policy', style: TextStyle(fontWeight: FontWeight.bold, color: textColor, fontSize: 14)),
          const SizedBox(height: 8),
          Text(
            'Your business metrics, bank details, and personal attributes are securely stored and encrypted. We do not sell merchant metrics or activity logs to third-party providers.',
            style: TextStyle(color: isDark ? Colors.white60 : Colors.black54, fontSize: 12, height: 1.4),
          ),
        ],
      ),
    );
  }
}

// 10. About App Page
class _AboutAppPage extends StatelessWidget {
  const _AboutAppPage({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final textColor = isDark ? Colors.white : const Color(0xFF1E293B);

    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.shield_outlined, size: 72, color: const Color(0xFF7C3AED)),
          const SizedBox(height: 16),
          Text('Novexapay', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 20, color: textColor)),
          const SizedBox(height: 4),
          const Text('Merchant Management Portal', style: TextStyle(fontSize: 12, color: Colors.grey)),
          const SizedBox(height: 12),
          const Text('Version v1.2.4', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.blueAccent)),
          const SizedBox(height: 4),
          const Text('© 2026 Novexapay Inc.', style: TextStyle(fontSize: 10, color: Colors.grey)),
        ],
      ),
    );
  }
}
