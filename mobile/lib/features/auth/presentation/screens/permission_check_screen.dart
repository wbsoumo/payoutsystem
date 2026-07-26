import 'package:flutter/material.dart';
import 'package:flutter/foundation.dart';
import 'package:permission_handler/permission_handler.dart';
import 'package:go_router/go_router.dart';

class PermissionCheckScreen extends StatefulWidget {
  const PermissionCheckScreen({Key? key}) : super(key: key);

  @override
  State<PermissionCheckScreen> createState() => _PermissionCheckScreenState();
}

class _PermissionCheckScreenState extends State<PermissionCheckScreen> {
  bool _locationGranted = false;
  bool _storageGranted = false;
  bool _checking = true;

  @override
  void initState() {
    super.initState();
    _checkPermissions();
  }

  Future<void> _checkPermissions() async {
    if (kIsWeb) {
      // In Flutter Web, we don't have standard Android storage permissions
      // We only prompt for Location during login or via Geolocator.
      if (mounted) {
        setState(() {
          _locationGranted = true;
          _storageGranted = true;
          _checking = false;
        });
        WidgetsBinding.instance.addPostFrameCallback((_) {
          context.go('/login');
        });
      }
      return;
    }

    final locStatus = await Permission.location.status;
    final storeStatus = await Permission.manageExternalStorage.status;

    setState(() {
      _locationGranted = locStatus.isGranted;
      _storageGranted = storeStatus.isGranted;
      _checking = false;
    });

    if (locStatus.isGranted && storeStatus.isGranted) {
      if (mounted) {
        WidgetsBinding.instance.addPostFrameCallback((_) {
          context.go('/login');
        });
      }
    }
  }

  Future<void> _requestPermissions() async {
    if (kIsWeb) {
      WidgetsBinding.instance.addPostFrameCallback((_) {
        context.go('/login');
      });
      return;
    }

    final locReq = await Permission.location.request();
    final storeReq = await Permission.manageExternalStorage.request();

    setState(() {
      _locationGranted = locReq.isGranted;
      _storageGranted = storeReq.isGranted;
    });

    if (locReq.isGranted && storeReq.isGranted) {
      if (mounted) {
        WidgetsBinding.instance.addPostFrameCallback((_) {
          context.go('/login');
        });
      }
    } else {
      // If permanently denied, prompt user to open app settings
      if (locReq.isPermanentlyDenied || storeReq.isPermanentlyDenied) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Permissions denied permanently. Please enable them in app settings.'),
            action: SnackBarAction(
              label: 'Settings',
              onPressed: openAppSettings,
            ),
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final textColor = isDark ? Colors.white : const Color(0xFF0F172A);
    final cardColor = isDark ? const Color(0xFF1E2235) : Colors.white;
    final borderStyleColor = isDark ? const Color(0xFF2E3245) : const Color(0xFFE2E8F0);

    if (_checking) {
      return Scaffold(
        backgroundColor: isDark ? const Color(0xFF0F172A) : const Color(0xFFF8FAFC),
        body: const Center(
          child: CircularProgressIndicator(color: Color(0xFF4F46E5)),
        ),
      );
    }

    return Scaffold(
      backgroundColor: isDark ? const Color(0xFF0F172A) : const Color(0xFFF8FAFC),
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 28, vertical: 24),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              const Spacer(),
              // Icon Header
              Center(
                child: Container(
                  width: 80,
                  height: 80,
                  decoration: BoxDecoration(
                    color: const Color(0xFF4F46E5).withOpacity(0.1),
                    shape: BoxShape.circle,
                  ),
                  child: const Icon(
                    Icons.security_outlined, 
                    color: Color(0xFF4F46E5), 
                    size: 40
                  ),
                ),
              ),
              const SizedBox(height: 32),
              
              // Title & Description
              Text(
                'Permissions Required',
                textAlign: TextAlign.center,
                style: TextStyle(
                  fontSize: 24, 
                  fontWeight: FontWeight.bold, 
                  color: textColor
                ),
              ),
              const SizedBox(height: 12),
              const Text(
                'To secure your merchant wallet, NovexaPay requires Location and File Storage permissions. This helps us verify login attempts and export your ledger logs.',
                textAlign: TextAlign.center,
                style: TextStyle(
                  fontSize: 13, 
                  color: Colors.grey, 
                  height: 1.5
                ),
              ),
              const SizedBox(height: 32),

              // Permissions List Card
              Container(
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: cardColor,
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(color: borderStyleColor),
                ),
                child: Column(
                  children: [
                    // Location Row
                    Row(
                      children: [
                        CircleAvatar(
                          radius: 18,
                          backgroundColor: _locationGranted 
                              ? const Color(0xFFDCFCE7) 
                              : const Color(0xFFFEE2E2),
                          child: Icon(
                            _locationGranted ? Icons.check : Icons.location_on_outlined,
                            color: _locationGranted ? Colors.green : Colors.red,
                            size: 18,
                          ),
                        ),
                        const SizedBox(width: 14),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                'Location Access',
                                style: TextStyle(
                                  fontWeight: FontWeight.bold, 
                                  fontSize: 13, 
                                  color: textColor
                                ),
                              ),
                              const SizedBox(height: 2),
                              const Text(
                                'Required for secure login verification.',
                                style: TextStyle(fontSize: 10, color: Colors.grey),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                    const Padding(
                      padding: EdgeInsets.symmetric(vertical: 14),
                      child: Divider(height: 1),
                    ),
                    // Storage Row
                    Row(
                      children: [
                        CircleAvatar(
                          radius: 18,
                          backgroundColor: _storageGranted 
                              ? const Color(0xFFDCFCE7) 
                              : const Color(0xFFFEE2E2),
                          child: Icon(
                            _storageGranted ? Icons.check : Icons.folder_open_outlined,
                            color: _storageGranted ? Colors.green : Colors.red,
                            size: 18,
                          ),
                        ),
                        const SizedBox(width: 14),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                'File Storage Access',
                                style: TextStyle(
                                  fontWeight: FontWeight.bold, 
                                  fontSize: 13, 
                                  color: textColor
                                ),
                              ),
                              const SizedBox(height: 2),
                              const Text(
                                'Required to download and save ledger reports.',
                                style: TextStyle(fontSize: 10, color: Colors.grey),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
              const Spacer(),

              // Action button
              ElevatedButton(
                onPressed: _requestPermissions,
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF4F46E5),
                  foregroundColor: Colors.white,
                  minimumSize: const Size.fromHeight(54),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(16)
                  ),
                  elevation: 0,
                ),
                child: const Text(
                  'Grant Access',
                  style: TextStyle(
                    fontWeight: FontWeight.bold, 
                    fontSize: 14
                  ),
                ),
              ),
              
              if (!_locationGranted || !_storageGranted) ...[
                const SizedBox(height: 12),
                TextButton(
                  onPressed: openAppSettings,
                  child: const Text(
                    'Open App Settings',
                    style: TextStyle(
                      color: Color(0xFF4F46E5), 
                      fontWeight: FontWeight.bold,
                      fontSize: 12
                    ),
                  ),
                ),
              ],
              const SizedBox(height: 16),
            ],
          ),
        ),
      ),
    );
  }
}
