# Farm Inventory System

Laravel app for managing farm products, fields, stock movements, reports, and role-based access.

## Local Setup

1. Install PHP extensions and tools on Fedora:

```bash
sudo dnf install -y php php-cli php-common php-mbstring php-xml php-bcmath php-curl php-mysqlnd php-sqlite3 php-gd php-zip unzip composer sqlite
```

2. Install dependencies:

```bash
composer install
npm install
```

3. Create the local environment and SQLite database:

```bash
cp .env.example .env
touch database/database.sqlite
php artisan key:generate
php artisan migrate --seed
npm run build
```

4. Start the app:

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Open `http://127.0.0.1:8000/login`.

Default seeded admin:

```text
Email: oualid.zine@uit.ac.ma
Password: password
```

## Production Notes

Use MySQL or MariaDB for production by changing `.env`:

```text
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=farm_inventory
DB_USERNAME=your_user
DB_PASSWORD=your_password
```

Run `php artisan migrate --force` after configuring the database. Use a real mail driver for password reset emails.

## Improvements Included

- Password reset flow with stronger password rules.
- Administrator-only management routes for manager creation, categories, field changes, destructive product deletes, and audit logs.
- Local Vite-managed Bootstrap, Font Awesome, and Chart.js assets.
- Dashboard metrics for product count, low-stock alerts, inventory value, stock movement, and recent transactions.
- Configurable low-stock thresholds per product.
- FIFO stock usage validation that prevents negative stock and avoids duplicate usage records.
- Audit log table and administrator audit view for product/category/field/transaction changes.
- SQLite-first local setup with MySQL production notes.
- Feature tests for auth redirects, permissions, stock validation, and audit creation.

## Test And Build

```bash
php artisan test
npm run build
```
