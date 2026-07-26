# Novexapay Installation & cPanel Deployment Guide

This document describes setup, database structures, integration patterns, and hosting instructions for Novexapay.

---

## 1. Quick Local Installation

### Prerequisites
*   PHP 8.3 or PHP 8.5
*   Composer
*   MySQL 8 (e.g. from XAMPP or native brew)

### Step-by-Step Setup
1.  Clone/copy the codebase into your web server root.
2.  Install dependencies:
    ```bash
    composer install --no-interaction --optimize-autoloader
    ```
3.  Configure environment variables. Copy `.env.example` to `.env` or edit the existing `.env` file:
    ```env
    APP_NAME=Novexapay
    APP_ENV=local
    APP_URL=http://localhost:8000
    
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=novexapay_db
    DB_USERNAME=root
    DB_PASSWORD=
    ```
4.  Generate application encryption key:
    ```bash
    php artisan key:generate
    ```
5.  Create database in MySQL console:
    ```sql
    CREATE DATABASE IF NOT EXISTS novexapay_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ```
6.  Run migrations to setup database tables:
    ```bash
    php artisan migrate
    ```
7.  Run seeders to populate initial Admins, Roles, and default Commissions:
    ```bash
    php artisan db:seed
    ```
8.  Start local development server:
    ```bash
    php artisan serve
    ```

---

## 2. cPanel Hosting Deployment Guide

To deploy Novexapay on shared cPanel hosting:

1.  **Prepare zip file**: Compress your local project folder (exclude `node_modules`, `tests`, and cache/log archives).
2.  **Upload to File Manager**: Upload the zip file onto cPanel.
3.  **Extract files**: Extract files outside of `public_html` (e.g., in `/home/username/novexapay`). This keeps configuration files private and protects against public file leaks.
4.  **Expose public directory**: Move or link the contents of `/home/username/novexapay/public` into `public_html`.
5.  **Edit index.php**: Update paths in `public_html/index.php` pointing back to vendor autoloading:
    ```php
    // In public_html/index.php
    require __DIR__.'/../novexapay/vendor/autoload.php';
    $app = require_once __DIR__.'/../novexapay/bootstrap/app.php';
    ```
6.  **Create Database**: Create MySQL database & database user via cPanel MySQL Database Wizard. Assign all privileges.
7.  **Update .env**: Edit `/home/username/novexapay/.env` to reflect the production URL and cPanel database credentials.
8.  **Setup cron schedule**: Go to cPanel Cron Jobs and register a cron job running every minute:
    ```bash
    * * * * * cd /home/username/novexapay && php artisan schedule:run >> /dev/null 2>&1
    ```

---

## 3. Database Schema Overview

The database uses strict UUID primary keys and foreign keys:

*   `admins`: Holds system administrative profiles (Super Admin, Risk Admin).
*   `merchants`: Core merchant record (Company name, KYC status, business parameters).
*   `merchant_users`: Login credentials linked to merchants.
*   `merchant_profiles`: Bank account numbers, PAN cards, GST numbers, and document paths.
*   `merchant_api_keys`: Encrypted key pairs (`api_key_hash`, `secret_key_encrypted`, `webhook_secret_encrypted`).
*   `merchant_ip_whitelists`: Restricts REST API gateway access to whitelisted source IPs.
*   `wallets`: Stores ledger balances (`balance` and `frozen_balance`).
*   `wallet_ledgers`: Complete transaction ledger logging credit/debit opening/closing balances.
*   `commissions`: Slab fees, global default rates, and merchant overrides.
*   `transactions`: Real-time transaction metadata, commission, and IP details.
*   `api_logs`: Request and response body logging.
*   `audit_logs`: User portal audit logs.
*   `login_histories`: Geolocation, timezone, and device logs.

---

## 4. API Integration Examples

To sign API requests, combine the request parameters and generate an HMAC-SHA256 signature using your private `secret_key`.

### Headers
*   `x-api-key`: `nvx_pk_live_your_key_here`
*   `x-signature`: calculated HMAC-SHA256 signature
*   `x-timestamp`: Unix timestamp of the request (e.g. `1785089201`)
*   `x-nonce`: unique UUID random string (re-used nonces are rejected)

### String to Sign Format
`timestamp + "." + nonce + "." + request_body_json`

### Node.js Example
```javascript
const crypto = require('crypto');
const axios = require('axios');

const apiKey = 'nvx_pk_live_xxxx';
const secretKey = 'nvx_sk_live_xxxx';
const timestamp = Math.floor(Date.now() / 1000).toString();
const nonce = crypto.randomUUID();

const payload = {
    client_reference_id: "req_pay_1001",
    amount: 2500.00,
    bank_name: "ICICI Bank",
    bank_account_number: "10020030040",
    bank_ifsc: "ICIC0001234",
    bank_holder_name: "Raju Kumar"
};

const bodyString = JSON.stringify(payload);
const stringToSign = `${timestamp}.${nonce}.${bodyString}`;
const signature = crypto
    .createHmac('sha256', secretKey)
    .update(stringToSign)
    .digest('hex');

axios.post('https://novexapay.com/api/v1/payouts', payload, {
    headers: {
        'x-api-key': apiKey,
        'x-signature': signature,
        'x-timestamp': timestamp,
        'x-nonce': nonce,
        'Content-Type': 'application/json'
    }
}).then(res => console.log(res.data))
  .catch(err => console.error(err.response.data));
```
