import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/network/api_client.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({Key? key}) : super(key: key);

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  Map<String, dynamic> _profile = {};
  bool _isLoading = true;

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
        if (mounted) {
          setState(() {
            _profile = response.data['profile'] ?? {};
            _isLoading = false;
          });
        }
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _isLoading = false;
        });
      }
    }
  }

  void _showProfileDetailSheet(BuildContext context, String title, List<Widget> details) {
    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(28))),
      builder: (context) {
        final isDark = Theme.of(context).brightness == Brightness.dark;
        final textColor = isDark ? Colors.white : const Color(0xFF1E293B);
        return Container(
          padding: const EdgeInsets.all(24),
          color: isDark ? const Color(0xFF1E2235) : Colors.white,
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(title, style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: textColor)),
                  IconButton(
                    icon: Icon(Icons.close, color: textColor),
                    onPressed: () => Navigator.pop(context),
                  ),
                ],
              ),
              const SizedBox(height: 16),
              ...details,
              const SizedBox(height: 12),
            ],
          ),
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final cardColor = isDark ? const Color(0xFF1E2235) : Colors.white;
    final textColor = isDark ? Colors.white : const Color(0xFF1E293B);
    final subTextColor = isDark ? Colors.white60 : Colors.black54;

    final List<Map<String, dynamic>> menuItems = [
      {
        'title': 'My Profile',
        'icon': Icons.person_outline,
        'action': 'profile',
      },
      {
        'title': 'Business Details',
        'icon': Icons.business_outlined,
        'action': 'business',
      },
      {
        'title': 'Bank Account',
        'icon': Icons.account_balance_outlined,
        'action': 'bank',
      },
      {
        'title': 'Security',
        'icon': Icons.lock_outline,
        'action': 'security',
      },
      {
        'title': 'Login History',
        'icon': Icons.history_outlined,
        'action': 'login_history',
      },
      {
        'title': 'Support',
        'icon': Icons.contact_support_outlined,
        'action': 'support',
      },
      {
        'title': 'KYC Status',
        'icon': Icons.verified_user_outlined,
        'action': 'kyc',
      },
      {
        'title': 'Settings',
        'icon': Icons.settings_outlined,
        'action': 'settings',
      },
      {
        'title': 'Logout',
        'icon': Icons.logout_outlined,
        'action': 'logout',
        'isDestructive': true,
      },
    ];

    final String name = _profile['name'] ?? 'Tony Stark';
    final String email = _profile['email'] ?? 'tony@stark.com';
    final String phone = _profile['phone'] ?? '+91 9876543210';
    final String kycStatus = (_profile['kyc_status'] ?? 'pending').toString().toUpperCase();

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
          'Profile & Settings',
          style: TextStyle(color: textColor, fontWeight: FontWeight.bold, fontSize: 16),
        ),
        centerTitle: true,
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: Color(0xFF7C3AED)))
          : SingleChildScrollView(
              padding: const EdgeInsets.all(24),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  // Profile Info Header Card
                  Container(
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      color: cardColor,
                      borderRadius: BorderRadius.circular(24),
                      border: Border.all(color: isDark ? const Color(0xFF2E3245) : const Color(0xFFE2E8F0)),
                    ),
                    child: Row(
                      children: [
                        const CircleAvatar(
                          radius: 30,
                          backgroundImage: NetworkImage('https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80'),
                        ),
                        const SizedBox(width: 16),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                name,
                                style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: textColor),
                              ),
                              const SizedBox(height: 4),
                              Text(
                                email,
                                style: TextStyle(fontSize: 12, color: subTextColor),
                              ),
                            ],
                          ),
                        ),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                          decoration: BoxDecoration(
                            color: kycStatus == 'APPROVED' 
                                ? Colors.green.withOpacity(0.1) 
                                : Colors.amber.withOpacity(0.1),
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: Text(
                            kycStatus,
                            style: TextStyle(
                              color: kycStatus == 'APPROVED' ? Colors.green : Colors.amber, 
                              fontWeight: FontWeight.bold, 
                              fontSize: 9
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 20),

                  // Profile List Menu Card
                  Container(
                    decoration: BoxDecoration(
                      color: cardColor,
                      borderRadius: BorderRadius.circular(24),
                      border: Border.all(color: isDark ? const Color(0xFF2E3245) : const Color(0xFFE2E8F0)),
                    ),
                    child: ListView.separated(
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      itemCount: menuItems.length,
                      separatorBuilder: (context, index) => Divider(
                        height: 1,
                        color: isDark ? const Color(0xFF2E3245) : const Color(0xFFF1F5F9),
                        indent: 60,
                        endIndent: 20,
                      ),
                      itemBuilder: (context, index) {
                        final item = menuItems[index];
                        final isDestructive = item['isDestructive'] == true;
                        final iconColor = isDestructive 
                            ? Colors.red 
                            : (isDark ? Colors.blueAccent : const Color(0xFF7C3AED));

                        return ListTile(
                          contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 4),
                          leading: Container(
                            padding: const EdgeInsets.all(8),
                            decoration: BoxDecoration(
                              color: iconColor.withOpacity(0.1),
                              borderRadius: BorderRadius.circular(10),
                            ),
                            child: Icon(
                              item['icon'] as IconData,
                              color: iconColor,
                              size: 20,
                            ),
                          ),
                          title: Text(
                            item['title'] as String,
                            style: TextStyle(
                              fontWeight: FontWeight.w600,
                              fontSize: 13,
                              color: isDestructive ? Colors.red : textColor,
                            ),
                          ),
                          trailing: Icon(
                            Icons.chevron_right,
                            size: 18,
                            color: isDark ? Colors.white30 : Colors.grey.shade400,
                          ),
                          onTap: () {
                            final act = item['action'];
                            if (act == 'profile') {
                              _showProfileDetailSheet(context, 'My Profile', [
                                ListTile(contentPadding: EdgeInsets.zero, title: const Text('Name'), subtitle: Text(name, style: TextStyle(color: textColor, fontWeight: FontWeight.bold))),
                                ListTile(contentPadding: EdgeInsets.zero, title: const Text('Email'), subtitle: Text(email, style: TextStyle(color: textColor, fontWeight: FontWeight.bold))),
                                ListTile(contentPadding: EdgeInsets.zero, title: const Text('Phone'), subtitle: Text(phone, style: TextStyle(color: textColor, fontWeight: FontWeight.bold))),
                              ]);
                            } else if (act == 'business') {
                              _showProfileDetailSheet(context, 'Business Details', [
                                ListTile(contentPadding: EdgeInsets.zero, title: const Text('Company Name'), subtitle: Text(_profile['company_name'] ?? 'N/A', style: TextStyle(color: textColor, fontWeight: FontWeight.bold))),
                                ListTile(contentPadding: EdgeInsets.zero, title: const Text('Type'), subtitle: Text(_profile['business_type'] ?? 'N/A', style: TextStyle(color: textColor, fontWeight: FontWeight.bold))),
                                ListTile(contentPadding: EdgeInsets.zero, title: const Text('GSTIN'), subtitle: Text(_profile['gstin'] ?? 'N/A', style: TextStyle(color: textColor, fontWeight: FontWeight.bold))),
                                ListTile(contentPadding: EdgeInsets.zero, title: const Text('PAN'), subtitle: Text(_profile['pan'] ?? 'N/A', style: TextStyle(color: textColor, fontWeight: FontWeight.bold))),
                                ListTile(contentPadding: EdgeInsets.zero, title: const Text('Website'), subtitle: Text(_profile['website'] ?? 'N/A', style: TextStyle(color: textColor, fontWeight: FontWeight.bold))),
                              ]);
                            } else if (act == 'bank') {
                              _showProfileDetailSheet(context, 'Bank Account', [
                                ListTile(contentPadding: EdgeInsets.zero, title: const Text('Bank Name'), subtitle: Text(_profile['bank_name'] ?? 'N/A', style: TextStyle(color: textColor, fontWeight: FontWeight.bold))),
                                ListTile(contentPadding: EdgeInsets.zero, title: const Text('Account Number'), subtitle: Text(_profile['bank_account_number'] ?? 'N/A', style: TextStyle(color: textColor, fontWeight: FontWeight.bold))),
                                ListTile(contentPadding: EdgeInsets.zero, title: const Text('IFSC'), subtitle: Text(_profile['bank_ifsc'] ?? 'N/A', style: TextStyle(color: textColor, fontWeight: FontWeight.bold))),
                                ListTile(contentPadding: EdgeInsets.zero, title: const Text('Holder Name'), subtitle: Text(_profile['bank_holder_name'] ?? 'N/A', style: TextStyle(color: textColor, fontWeight: FontWeight.bold))),
                              ]);
                            } else if (act == 'security') {
                              context.push('/settings');
                            } else if (act == 'login_history') {
                              _showProfileDetailSheet(context, 'Recent Login History', [
                                ListTile(contentPadding: EdgeInsets.zero, leading: const Icon(Icons.laptop, color: Colors.green), title: const Text('Chrome (MacOS) • Staging environment'), subtitle: Text('27 Jul, 01:25 • IP: 127.0.0.1', style: TextStyle(color: textColor))),
                                ListTile(contentPadding: EdgeInsets.zero, leading: const Icon(Icons.laptop, color: Colors.grey), title: const Text('Chrome (MacOS) • Web portal'), subtitle: Text('26 Jul, 18:30 • IP: 127.0.0.1', style: TextStyle(color: textColor))),
                              ]);
                            } else if (act == 'support') {
                              _showProfileDetailSheet(context, 'Support Desk', [
                                const Text('Need assistance with your wallet payouts? Contact our merchant support center directly:'),
                                const SizedBox(height: 12),
                                ListTile(contentPadding: EdgeInsets.zero, leading: const Icon(Icons.email, color: Colors.blueAccent), title: const Text('Email Support'), subtitle: Text('support@novexapay.com', style: TextStyle(color: textColor, fontWeight: FontWeight.bold))),
                                ListTile(contentPadding: EdgeInsets.zero, leading: const Icon(Icons.phone, color: Colors.green), title: const Text('Merchant Helpline'), subtitle: Text('+91 1800-102-PAY', style: TextStyle(color: textColor, fontWeight: FontWeight.bold))),
                              ]);
                            } else if (act == 'kyc') {
                              _showProfileDetailSheet(context, 'KYC Status', [
                                ListTile(
                                  contentPadding: EdgeInsets.zero, 
                                  title: const Text('Status'), 
                                  subtitle: Row(
                                    children: [
                                      Container(
                                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                                        decoration: BoxDecoration(
                                          color: kycStatus == 'APPROVED' ? Colors.green.withOpacity(0.1) : Colors.amber.withOpacity(0.1), 
                                          borderRadius: BorderRadius.circular(8)
                                        ),
                                        child: Text(
                                          kycStatus, 
                                          style: TextStyle(
                                            color: kycStatus == 'APPROVED' ? Colors.green : Colors.amber, 
                                            fontWeight: FontWeight.bold, 
                                            fontSize: 10
                                          )
                                        ),
                                      ),
                                    ],
                                  )
                                ),
                                ListTile(contentPadding: EdgeInsets.zero, title: const Text('Verification Documents'), subtitle: Text('GSTIN & Business PAN Checked', style: TextStyle(color: textColor, fontWeight: FontWeight.bold))),
                              ]);
                            } else if (act == 'settings') {
                              context.push('/settings');
                            } else if (act == 'logout') {
                              context.go('/');
                            }
                          },
                        );
                      },
                    ),
                  ),
                ],
              ),
            ),
    );
  }
}
