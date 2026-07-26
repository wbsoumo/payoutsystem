import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'core/theme/app_theme.dart';
import 'core/theme/theme_provider.dart';
import 'features/auth/presentation/screens/login_screen.dart';
import 'features/auth/presentation/screens/pin_setup_screen.dart';
import 'features/dashboard/presentation/screens/dashboard_screen.dart';
import 'features/money_transfer/presentation/screens/money_transfer_screen.dart';
import 'features/money_transfer/presentation/screens/receipt_screen.dart';
import 'features/beneficiaries/presentation/screens/beneficiaries_screen.dart';
import 'features/beneficiaries/presentation/screens/add_beneficiary_screen.dart';
import 'features/ledger/presentation/screens/ledger_screen.dart';
import 'features/transactions/presentation/screens/transactions_screen.dart';
import 'features/settings/presentation/screens/settings_screen.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  try {
    await Hive.initFlutter();
    await Hive.openBox('beneficiaries_box');
  } catch (e) {
    print('Hive initialization failed: $e');
  }

  runApp(
    const ProviderScope(
      child: MyApp(),
    ),
  );
}

final GoRouter _router = GoRouter(
  initialLocation: '/login',
  routes: <RouteBase>[
    GoRoute(
      path: '/login',
      builder: (BuildContext context, GoRouterState state) => const LoginScreen(),
    ),
    GoRoute(
      path: '/setup-pin',
      builder: (BuildContext context, GoRouterState state) => const PinSetupScreen(),
    ),
    GoRoute(
      path: '/dashboard',
      builder: (BuildContext context, GoRouterState state) => const DashboardScreen(),
    ),
    GoRoute(
      path: '/transfer',
      builder: (BuildContext context, GoRouterState state) => const MoneyTransferScreen(),
    ),
    GoRoute(
      path: '/receipt',
      builder: (BuildContext context, GoRouterState state) {
        final amount = double.tryParse(state.uri.queryParameters['amount'] ?? '0') ?? 0;
        final beneficiary = state.uri.queryParameters['beneficiary'] ?? 'Unknown';
        final ref = state.uri.queryParameters['ref'] ?? 'N/A';
        return ReceiptScreen(amount: amount, beneficiary: beneficiary, referenceId: ref);
      },
    ),
    GoRoute(
      path: '/beneficiaries',
      builder: (BuildContext context, GoRouterState state) => const BeneficiariesScreen(),
    ),
    GoRoute(
      path: '/add-beneficiary',
      builder: (BuildContext context, GoRouterState state) => const AddBeneficiaryScreen(),
    ),
    GoRoute(
      path: '/ledger',
      builder: (BuildContext context, GoRouterState state) => const LedgerScreen(),
    ),
    GoRoute(
      path: '/transactions',
      builder: (BuildContext context, GoRouterState state) => const TransactionsScreen(),
    ),
    GoRoute(
      path: '/settings',
      builder: (BuildContext context, GoRouterState state) => const SettingsScreen(),
    ),
  ],
);

class MyApp extends ConsumerWidget {
  const MyApp({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final themeMode = ref.watch(themeProvider);

    return MaterialApp.router(
      title: 'Novexapay Mobile Merchant Portal',
      theme: AppTheme.lightTheme,
      darkTheme: AppTheme.darkTheme,
      themeMode: themeMode,
      routerConfig: _router,
      debugShowCheckedModeBanner: false,
    );
  }
}
