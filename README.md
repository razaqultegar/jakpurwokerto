<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo"></a></p>

# the Jakmania Purwokerto

Web application for **the Jakmania Purwokerto** — the official Persija Jakarta supporters community chapter in Purwokerto. It handles member registration, ticket and merchandise sales, event check-in, and club news.

## Features

- **Membership** — public member registration with province/regency/district cascading address data, admin approval workflow, and status history logging.
- **Ticketing & Merchandise** — public storefronts for match tickets and club merchandise, with a full checkout flow (order, payment/DP proof upload, settlement, pickup or shipping).
- **Admin dashboard** — order management (status, shipping, pickup, payment verification, invoice/export), ticket and merchandise management, member management with Excel import/template download.
- **Event check-in** — PIN-protected check-in station with QR code ticket scanning, lookup, confirm, and undo.
- **Berita (news/articles)** — public article listing and detail pages.
- **Member statistics** — public stats page backed by an API endpoint.

## Tech Stack

- [Laravel 13](https://laravel.com/docs) on PHP 8.4
- MySQL, with [Laravel Fortify](https://laravel.com/docs/fortify) for authentication
- [Tailwind CSS 4](https://tailwindcss.com) + [Vite](https://vitejs.dev)
- jQuery, DataTables, Select2, Flatpickr, SweetAlert2, ApexCharts, Swiper, and [qr-scanner](https://github.com/nimiq/qr-scanner) for the admin/check-in UI
- [barryvdh/laravel-dompdf](https://github.com/barryvdh/laravel-dompdf) for invoices, [phpoffice/phpspreadsheet](https://github.com/PHPOffice/PhpSpreadsheet) for import/export, [endroid/qr-code](https://github.com/endroid/qr-code) for ticket QR codes

## Getting Started

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

# configure DB_* credentials in .env, then:
php artisan migrate

npm run dev
# in another terminal
php artisan serve
```

Set `CHECKIN_PIN` in `.env` to the PIN used to unlock the event check-in scanner at `/checkin`.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
