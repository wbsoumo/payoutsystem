import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:dio/dio.dart';
import 'package:shimmer/shimmer.dart';
import '../../../auth/presentation/providers/auth_provider.dart';
import '../../../../core/network/api_client.dart';
import '../../../beneficiaries/presentation/providers/beneficiary_provider.dart';
import '../../../../core/constants/endpoints.dart';

class DashboardScreen extends ConsumerStatefulWidget {
  const DashboardScreen({Key? key}) : super(key: key);

  @override
  ConsumerState<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends ConsumerState<DashboardScreen> {
  bool _showBalance = true;
  String _balance = '₹0.00';
  bool _isLoadingBalance = true;
  List<Map<String, dynamic>> _recentTransactions = [];
  bool _isLoadingTx = true;

  final Map<String, String> _bankDomains = {
    'SBIN': 'sbi.co.in',
    'HDFC': 'hdfcbank.com',
    'ICIC': 'icicibank.com',
    'UTIB': 'axisbank.com',
    'BARB': 'bankofbaroda.in',
    'PUNB': 'pnbindia.in',
    'KKBK': 'kotak.com',
    'YESB': 'yesbank.in',
    'UBIN': 'unionbankofindia.co.in',
    'CNRB': 'canarabank.com',
    'IDIB': 'indianbank.in',
  };

  @override
  void initState() {
    super.initState();
    _loadBalance();
    _loadRecentTransactions();
  }

  Future<void> _loadBalance() async {
    final client = ApiClient();
    try {
      final response = await client.dio.get('/wallet/balance');
      if (response.data['success'] == true) {
        final double bal = double.tryParse(response.data['balance'].toString()) ?? 0.00;
        if (mounted) {
          setState(() {
            _balance = '₹' + bal.toStringAsFixed(2);
            _isLoadingBalance = false;
          });
        }
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _isLoadingBalance = false;
        });
      }
    }
  }

  Future<void> _loadRecentTransactions() async {
    final client = ApiClient();
    try {
      final response = await client.dio.get('/payouts');
      if (response.data['success'] == true) {
        final List<dynamic> list = response.data['payouts'] ?? [];
        if (mounted) {
          setState(() {
            _recentTransactions = list.take(3).map((t) => Map<String, dynamic>.from(t)).toList();
            _isLoadingTx = false;
          });
        }
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _isLoadingTx = false;
        });
      }
    }
  }

  String _getLogoForIfsc(String? ifsc) {
    if (ifsc == null || ifsc.length < 4) {
      return '${Endpoints.baseUrl}/logo/generic-bank.com';
    }
    final prefix = ifsc.substring(0, 4).toUpperCase();
    final domain = _bankDomains[prefix] ?? 'generic-bank.com';
    return '${Endpoints.baseUrl}/logo/$domain';
  }

  void _showNotificationsDrawer(BuildContext context) {
    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(28))),
      builder: (context) {
        final isDark = Theme.of(context).brightness == Brightness.dark;
        final textColor = isDark ? Colors.white : const Color(0xFF1E293B);

        final List<Map<String, String>> mockNotifications = [
          {
            'title': 'Payout Processed Successfully',
            'body': 'Your payout request to Soumojit Saha for ₹100.00 was dispatched.',
            'time': 'Just now',
            'type': 'success'
          },
          {
            'title': 'Wallet Security Alert',
            'body': 'Merchant session validated on staging Chrome environment.',
            'time': '10 mins ago',
            'type': 'security'
          },
          {
            'title': 'Stating Server Synced',
            'body': 'Staging cPanel server and routing caches cleared successfully.',
            'time': '1 hour ago',
            'type': 'info'
          },
          {
            'title': 'IFSC Lookup Success',
            'body': 'Razorpay open IFSC resolver API verified successfully.',
            'time': '2 hours ago',
            'type': 'success'
          }
        ];

        return Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text('System Notifications', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: textColor)),
                  TextButton(
                    onPressed: () => Navigator.pop(context),
                    child: const Text('Mark all read', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.blueAccent)),
                  ),
                ],
              ),
              const SizedBox(height: 16),
              Flexible(
                child: ListView.separated(
                  shrinkWrap: true,
                  itemCount: mockNotifications.length,
                  separatorBuilder: (c, i) => const Divider(height: 20),
                  itemBuilder: (context, index) {
                    final item = mockNotifications[index];
                    IconData icon = Icons.info_outline;
                    Color iconColor = Colors.blue;

                    if (item['type'] == 'success') {
                      icon = Icons.check_circle_outline;
                      iconColor = Colors.green;
                    } else if (item['type'] == 'security') {
                      icon = Icons.security_outlined;
                      iconColor = Colors.orange;
                    }

                    return Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        CircleAvatar(
                          radius: 18,
                          backgroundColor: iconColor.withOpacity(0.12),
                          child: Icon(icon, color: iconColor, size: 18),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(item['title']!, style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12, color: textColor)),
                              const SizedBox(height: 4),
                              Text(item['body']!, style: const TextStyle(fontSize: 10, color: Colors.grey)),
                              const SizedBox(height: 6),
                              Text(item['time']!, style: const TextStyle(fontSize: 8, color: Colors.grey, fontWeight: FontWeight.bold)),
                            ],
                          ),
                        ),
                      ],
                    );
                  },
                ),
              ),
            ],
          ),
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    final user = ref.watch(authProvider).user;
    final beneficiaries = ref.watch(beneficiaryProvider);
    final isDark = Theme.of(context).brightness == Brightness.dark;

    final bgColor = isDark ? const Color(0xFF0B0E1E) : const Color(0xFFF8FAFC);
    final cardColor = isDark ? const Color(0xFF1E2235) : Colors.white;
    final textColor = isDark ? Colors.white : const Color(0xFF1E293B);
    final subTextColor = isDark ? Colors.white60 : Colors.black54;

    return Scaffold(
      backgroundColor: bgColor,
      body: SafeArea(
        child: RefreshIndicator(
          onRefresh: () async {
            await Future.wait([
              _loadBalance(),
              _loadRecentTransactions(),
            ]);
          },
          child: SingleChildScrollView(
            physics: const AlwaysScrollableScrollPhysics(),
            padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
            child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // 1. Top Bar Profile Info
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Row(
                    children: [
                      const CircleAvatar(
                        radius: 22,
                        backgroundImage: NetworkImage('https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80'),
                      ),
                      const SizedBox(width: 12),
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'Hi, ${user?.name ?? 'Tony Stark'}',
                            style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: textColor),
                          ),
                          const SizedBox(height: 2),
                          Text(
                            'Good Morning!',
                            style: TextStyle(fontSize: 11, color: subTextColor),
                          ),
                        ],
                      ),
                    ],
                  ),
                  // Notification bell
                  GestureDetector(
                    onTap: () => _showNotificationsDrawer(context),
                    child: Container(
                      width: 44,
                      height: 44,
                      decoration: BoxDecoration(
                        color: isDark ? const Color(0xFF1E2235) : Colors.white,
                        shape: BoxShape.circle,
                        border: Border.all(color: isDark ? const Color(0xFF2E3245) : const Color(0xFFE2E8F0)),
                      ),
                      child: Stack(
                        alignment: Alignment.center,
                        children: [
                          Icon(Icons.notifications_none_outlined, color: textColor, size: 20),
                          Positioned(
                            top: 12,
                            right: 12,
                            child: Container(
                              width: 8,
                              height: 8,
                              decoration: const BoxDecoration(
                                color: Colors.redAccent,
                                shape: BoxShape.circle,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 24),

              Container(
                padding: const EdgeInsets.all(24),
                decoration: BoxDecoration(
                  gradient: const LinearGradient(
                    colors: [Color(0xFF7C3AED), Color(0xFF2563EB)],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                  borderRadius: BorderRadius.circular(28),
                  boxShadow: [
                    BoxShadow(
                      color: const Color(0xFF7C3AED).withOpacity(0.3),
                      blurRadius: 15,
                      offset: const Offset(0, 8),
                    ),
                  ],
                ),
                child: _isLoadingBalance
                    ? Shimmer.fromColors(
                        baseColor: Colors.white.withOpacity(0.15),
                        highlightColor: Colors.white.withOpacity(0.35),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Container(width: 80, height: 10, decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(4))),
                            const SizedBox(height: 12),
                            Container(width: 160, height: 32, decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(6))),
                            const SizedBox(height: 20),
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                Container(width: 110, height: 10, decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(4))),
                                Container(width: 50, height: 16, decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(6))),
                              ],
                            )
                          ],
                        ),
                      )
                    : Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text(
                            'Total Balance',
                            style: TextStyle(color: Colors.white70, fontSize: 11, fontWeight: FontWeight.w500),
                          ),
                          const SizedBox(height: 8),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Text(
                                _showBalance ? _balance : '•••••••',
                                style: const TextStyle(color: Colors.white, fontSize: 28, fontWeight: FontWeight.bold, fontFamily: 'monospace'),
                              ),
                              IconButton(
                                icon: Icon(
                                  _showBalance ? Icons.visibility : Icons.visibility_off,
                                  color: Colors.white70,
                                ),
                                onPressed: () {
                                  setState(() {
                                    _showBalance = !_showBalance;
                                  });
                                },
                              ),
                            ],
                          ),
                          const SizedBox(height: 16),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Text(
                                user?.companyName ?? 'Stark Industries Ltd',
                                style: const TextStyle(color: Colors.white70, fontSize: 11),
                              ),
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                                decoration: BoxDecoration(
                                  color: Colors.white24,
                                  borderRadius: BorderRadius.circular(8),
                                ),
                                child: const Text('ACTIVE', style: TextStyle(color: Colors.white, fontSize: 9, fontWeight: FontWeight.bold)),
                              ),
                            ],
                          ),
                        ],
                      ),
              ),
              const SizedBox(height: 24),

              // 3. Action Shortcuts Row
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  CircleActionItem(
                    icon: Icons.add_circle,
                    label: 'Top Up',
                    isPrimary: true,
                    onTap: () => context.push('/transfer'),
                  ),
                  CircleActionItem(
                    icon: Icons.send,
                    label: 'Send',
                    isDark: isDark,
                    onTap: () => context.push('/transfer'),
                  ),
                  CircleActionItem(
                    icon: Icons.repeat,
                    label: 'Request',
                    isDark: isDark,
                    onTap: () => context.push('/ledger'),
                  ),
                  CircleActionItem(
                    icon: Icons.account_balance_wallet,
                    label: 'Withdraw',
                    isDark: isDark,
                    onTap: () => context.push('/transactions'),
                  ),
                ],
              ),
              const SizedBox(height: 28),

              // 4. Quick Send (Horizontal Beneficiary Avatars)
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    'Quick Send',
                    style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: textColor),
                  ),
                  TextButton(
                    onPressed: () => context.push('/beneficiaries'),
                    child: const Text('See all', style: TextStyle(fontSize: 12, color: Colors.blueAccent, fontWeight: FontWeight.bold)),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              SizedBox(
                height: 80,
                child: ListView(
                  scrollDirection: Axis.horizontal,
                  children: [
                    // Add Button
                    GestureDetector(
                      onTap: () => context.push('/add-beneficiary'),
                      child: Padding(
                        padding: const EdgeInsets.only(right: 16),
                        child: Column(
                          children: [
                            Container(
                              width: 50,
                              height: 50,
                              decoration: BoxDecoration(
                                shape: BoxShape.circle,
                                border: Border.all(color: Colors.grey.withOpacity(0.5), width: 1.5, style: BorderStyle.solid),
                              ),
                              child: Icon(Icons.add, color: textColor),
                            ),
                            const SizedBox(height: 6),
                            Text('Add', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: textColor)),
                          ],
                        ),
                      ),
                    ),
                    // Quick Send Beneficiaries mapped from provider
                    ...beneficiaries.take(5).map((b) {
                      final name = b['name'] ?? 'N/A';
                      final firstName = name.isNotEmpty ? name.split(' ').first : 'N/A';
                      return AvatarSendItem(
                        name: firstName,
                        logoUrl: b['logo'],
                        bankName: b['bank'] ?? '',
                        onTap: () => context.push('/transfer?beneficiary_name=${Uri.encodeComponent(name)}'),
                      );
                    }),
                  ],
                ),
              ),
              const SizedBox(height: 24),

              // 5. Recent Transactions
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    'Recent Transactions',
                    style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: textColor),
                  ),
                  TextButton(
                    onPressed: () => context.push('/transactions'),
                    child: const Text('See all', style: TextStyle(fontSize: 12, color: Colors.blueAccent, fontWeight: FontWeight.bold)),
                  ),
                ],
              ),
              const SizedBox(height: 12),

              if (_isLoadingTx)
                Shimmer.fromColors(
                  baseColor: isDark ? const Color(0xFF1E2235) : Colors.grey.shade300,
                  highlightColor: isDark ? const Color(0xFF2E3245) : Colors.grey.shade100,
                  child: Column(
                    children: List.generate(
                      2,
                      (index) => Container(
                        height: 72,
                        margin: const EdgeInsets.only(bottom: 12),
                        decoration: BoxDecoration(color: cardColor, borderRadius: BorderRadius.circular(20)),
                      ),
                    ),
                  ),
                )
              else if (_recentTransactions.isEmpty)
                Card(
                  child: Padding(
                    padding: const EdgeInsets.all(32),
                    child: Column(
                      children: [
                        Icon(Icons.payment_outlined, color: Colors.grey.shade400, size: 44),
                        const SizedBox(height: 12),
                        const Text('No recent transactions.', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: Colors.grey)),
                      ],
                    ),
                  ),
                )
              else
                ListView.separated(
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  itemCount: _recentTransactions.length,
                  separatorBuilder: (context, index) => const SizedBox(height: 12),
                  itemBuilder: (context, index) {
                    final t = _recentTransactions[index];
                    final isSuccess = t['status'] == 'success';
                    final isPending = t['status'] == 'pending';

                    return GestureDetector(
                      onTap: () => context.push('/transaction-detail', extra: t),
                      child: Container(
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: cardColor,
                          borderRadius: BorderRadius.circular(20),
                          border: Border.all(color: isDark ? const Color(0xFF2E3245) : const Color(0xFFE2E8F0)),
                        ),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Row(
                              children: [
                                SafeBankLogo(
                                  logoUrl: _getLogoForIfsc(t['ifsc']),
                                  bankName: t['bank'] ?? '',
                                  size: 40,
                                ),
                                const SizedBox(width: 12),
                                Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(t['beneficiary'] ?? 'N/A', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: textColor)),
                                    const SizedBox(height: 4),
                                    Text(t['date'] ?? 'N/A', style: TextStyle(fontSize: 10, color: subTextColor)),
                                  ],
                                ),
                              ],
                            ),
                            Row(
                              children: [
                                Text(
                                  t['amount'] ?? '₹0.00',
                                  style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: textColor),
                                ),
                                const SizedBox(width: 8),
                                CircleAvatar(
                                  radius: 10,
                                  backgroundColor: isSuccess 
                                      ? const Color(0xFFDCFCE7) 
                                      : isPending ? const Color(0xFFFEF3C7) : const Color(0xFFFEE2E2),
                                  child: Icon(
                                    isSuccess 
                                        ? Icons.check 
                                        : isPending ? Icons.access_time : Icons.close,
                                    color: isSuccess 
                                        ? Colors.green 
                                        : isPending ? Colors.amber.shade800 : Colors.red,
                                    size: 10,
                                  ),
                                ),
                              ],
                            ),
                          ],
                        ),
                      ),
                    );
                  },
                ),
              const SizedBox(height: 24),
            ],
          ),
        ),
      ),
    ),
      bottomNavigationBar: Container(
        margin: const EdgeInsets.fromLTRB(24, 0, 24, 24),
        padding: const EdgeInsets.symmetric(vertical: 8),
        decoration: BoxDecoration(
          color: isDark ? const Color(0xFF1E2235) : Colors.white,
          borderRadius: BorderRadius.circular(30),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.08),
              blurRadius: 20,
              offset: const Offset(0, 10),
            ),
          ],
          border: Border.all(color: isDark ? const Color(0xFF2E3245) : const Color(0xFFE2E8F0)),
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceAround,
          children: [
            IconButton(icon: const Icon(Icons.home, color: Color(0xFF7C3AED)), onPressed: () {}),
            IconButton(icon: const Icon(Icons.send_outlined, color: Colors.grey), onPressed: () => context.push('/transfer')),
            IconButton(icon: const Icon(Icons.receipt_long_outlined, color: Colors.grey), onPressed: () => context.push('/ledger')),
            IconButton(icon: const Icon(Icons.history_outlined, color: Colors.grey), onPressed: () => context.push('/transactions')),
            IconButton(icon: const Icon(Icons.settings_outlined, color: Colors.grey), onPressed: () => context.push('/settings')),
          ],
        ),
      ),
    );
  }
}

class CircleActionItem extends StatelessWidget {
  final IconData icon;
  final String label;
  final bool isPrimary;
  final bool isDark;
  final VoidCallback onTap;

  const CircleActionItem({
    Key? key,
    required this.icon,
    required this.label,
    this.isPrimary = false,
    this.isDark = false,
    required this.onTap,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final bgColor = isPrimary
        ? const Color(0xFF7C3AED)
        : isDark
            ? const Color(0xFF1E2235)
            : Colors.white;
    final iconColor = isPrimary
        ? Colors.white
        : isDark
            ? Colors.white70
            : const Color(0xFF475569);
    final borderColor = isPrimary
        ? Colors.transparent
        : isDark
            ? const Color(0xFF2E3245)
            : const Color(0xFFE2E8F0);

    return GestureDetector(
      onTap: onTap,
      child: Column(
        children: [
          Container(
            width: 60,
            height: 60,
            decoration: BoxDecoration(
              color: bgColor,
              shape: BoxShape.circle,
              border: Border.all(color: borderColor),
              boxShadow: isPrimary
                  ? [
                      BoxShadow(
                        color: const Color(0xFF7C3AED).withOpacity(0.3),
                        blurRadius: 10,
                        offset: const Offset(0, 4),
                      ),
                    ]
                  : null,
            ),
            child: Icon(icon, color: iconColor, size: 22),
          ),
          const SizedBox(height: 8),
          Text(
            label,
            style: TextStyle(
              fontSize: 11,
              fontWeight: FontWeight.w600,
              color: isDark ? Colors.white70 : const Color(0xFF475569),
            ),
          ),
        ],
      ),
    );
  }
}

class AvatarSendItem extends StatelessWidget {
  final String name;
  final String? logoUrl;
  final String bankName;
  final VoidCallback onTap;

  const AvatarSendItem({
    Key? key,
    required this.name,
    required this.logoUrl,
    required this.bankName,
    required this.onTap,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    return GestureDetector(
      onTap: onTap,
      child: Padding(
        padding: const EdgeInsets.only(right: 16),
        child: Column(
          children: [
            SafeBankLogo(
              logoUrl: logoUrl,
              bankName: bankName,
              size: 50,
            ),
            const SizedBox(height: 6),
            Text(
              name,
              style: TextStyle(
                fontSize: 10,
                fontWeight: FontWeight.bold,
                color: isDark ? Colors.white70 : Colors.black87,
              ),
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
            ),
          ],
        ),
      ),
    );
  }
}

class SafeBankLogo extends StatelessWidget {
  final String? logoUrl;
  final String bankName;
  final double size;

  const SafeBankLogo({
    Key? key,
    required this.logoUrl,
    required this.bankName,
    this.size = 48,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    if (logoUrl == null || logoUrl!.isEmpty) {
      return Container(
        width: size,
        height: size,
        decoration: const BoxDecoration(
          color: Color(0xFFEFF6FF),
          shape: BoxShape.circle,
        ),
        child: const Center(
          child: Icon(Icons.business, color: Color(0xFF4361EE), size: 24),
        ),
      );
    }

    return Container(
      width: size,
      height: size,
      decoration: const BoxDecoration(
        color: Color(0xFFEFF6FF),
        shape: BoxShape.circle,
      ),
      child: ClipOval(
        child: Image.network(
          logoUrl!,
          width: size,
          height: size,
          fit: BoxFit.cover,
          errorBuilder: (context, error, stackTrace) {
            final uri = Uri.tryParse(logoUrl!);
            final domain = uri != null && uri.pathSegments.isNotEmpty ? uri.pathSegments.last : 'generic-bank.com';
            
            return Image.network(
              'https://logo.clearbit.com/$domain',
              width: size,
              height: size,
              fit: BoxFit.cover,
              errorBuilder: (context, error2, stackTrace2) {
                return const Center(
                  child: Icon(Icons.business, color: Color(0xFF4361EE), size: 20),
                );
              },
            );
          },
        ),
      ),
    );
  }
}
