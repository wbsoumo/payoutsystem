import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:mobile/main.dart';

void main() {
  testWidgets('App renders login screen successfully', (WidgetTester tester) async {
    // Build our app and trigger a frame.
    await tester.pumpWidget(
      const ProviderScope(
        child: MyApp(),
      ),
    );

    // Verify that splash screen elements exist immediately on load
    expect(find.text('NovexaPay'), findsOneWidget);
    expect(find.text('Powering Business Payouts Across India'), findsOneWidget);

    // Drains splash timer so the test can finish without pending timers
    await tester.pump(const Duration(seconds: 3));
  });
}
