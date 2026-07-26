import 'package:flutter/material.dart';

class SettingsScreen extends StatelessWidget {
  const SettingsScreen({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
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
                  onTap: () {},
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
                  value: false,
                  onChanged: (val) {},
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
