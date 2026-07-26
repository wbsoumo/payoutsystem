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

    // Settle splash redirect delay
    await tester.pumpAndSettle(const Duration(seconds: 3));

    // Verify that login page elements exist
    expect(find.text('NovexaPay'), findsOneWidget);
    expect(find.text('Login Securely'), findsOneWidget);
  });
}
