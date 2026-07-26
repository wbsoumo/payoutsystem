# Novexapay FinTech Merchant Money Transfer Mobile Application

This is a premium, secure, and production-ready Flutter mobile application designed for FinTech Money Transfer operations.

## Tech Stack
*   **Framework**: Flutter (Latest Stable)
*   **State Management**: Riverpod (`flutter_riverpod`)
*   **Routing**: GoRouter (`go_router`)
*   **Http Client**: Dio (`dio`)
*   **Storage**: Hive (`hive_flutter`), Secure Storage (`flutter_secure_storage`)
*   **Design & Theme**: Material 3, custom gradients, Outfit and Plus Jakarta Sans fonts.

---

## Getting Started

### Prerequisites
Make sure you have Flutter installed and configured on your system:
```bash
flutter doctor
```

### Installation
1. Navigate into the mobile directory:
   ```bash
   cd mobile
   ```
2. Pull all dependency packages:
   ```bash
   flutter pub get
   ```
3. Run the code generator (if extending with riverpod code generation):
   ```bash
   flutter pub run build_runner build --delete-conflicting-outputs
   ```
4. Compile and launch the app:
   ```bash
   flutter run
   ```

---

## App Flow
1. **Login Screen**: Authenticate with merchant email and password credentials.
2. **First Login Setup**: Force user to setup and confirm a 6-digit Transaction PIN.
3. **Dashboard**: View wallet balance, quick actions grid, stats, and recent payout list.
4. **Money Transfer Flow**: Select beneficiary, enter payout amount, calculate convenience fees and commission earned, authorize via secure 6-digit PIN, show success/failure receipt screen.
