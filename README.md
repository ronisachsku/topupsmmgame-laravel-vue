# TopUp SMM Game - Laravel 12 + Vue.js

🚀 Modern platform untuk layanan top-up game dan social media marketing dengan arsitektur terpisah antara frontend dan backend.

## 🏗️ Arsitektur

- **Backend**: Laravel 12 REST API
- **Frontend**: Vue.js 3 dengan Composition API
- **Authentication**: Laravel Sanctum + OAuth (Google & Facebook)
- **Payment Gateway**: Midtrans
- **Database**: MySQL

## 📁 Struktur Project

```
.
├── backend/          # Laravel 12 API
│   ├── app/
│   ├── database/
│   ├── routes/
│   └── ...
├── frontend/         # Vue.js 3 SPA
│   ├── src/
│   ├── public/
│   └── ...
└── README.md
```

## 🔑 Fitur Utama

### Backend (Laravel 12)
- ✅ RESTful API dengan resource controllers
- ✅ Authentication dengan Sanctum
- ✅ OAuth Google & Facebook (Socialite)
- ✅ Midtrans payment integration
- ✅ Order management system
- ✅ Voucher & discount system
- ✅ Wallet & points system
- ✅ Email notifications (PHPMailer)
- ✅ WhatsApp & Telegram notifications
- ✅ Cron jobs untuk automated tasks
- ✅ API rate limiting
- ✅ Request validation

### Frontend (Vue.js 3)
- ✅ Single Page Application (SPA)
- ✅ Vue Router untuk navigation
- ✅ Pinia untuk state management
- ✅ Axios untuk API calls
- ✅ Social login buttons (Google & Facebook)
- ✅ Responsive design
- ✅ Real-time order tracking
- ✅ Shopping cart
- ✅ Payment integration

## 🚀 Getting Started

### Prerequisites
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8.0+
- Redis (optional, for queue)

### Backend Setup

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

### Frontend Setup

```bash
cd frontend
npm install
npm run dev
```

## 🔐 Environment Configuration

### Backend (.env)
```env
APP_NAME="TopUp SMM Game"
APP_ENV=local
APP_KEY=
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=topupsmmgame
DB_USERNAME=root
DB_PASSWORD=

# OAuth Google
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=${APP_URL}/auth/google/callback

# OAuth Facebook
FACEBOOK_CLIENT_ID=
FACEBOOK_CLIENT_SECRET=
FACEBOOK_REDIRECT_URI=${APP_URL}/auth/facebook/callback

# Midtrans
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=false

# API Settings
API_KEY=
API_URL=
```

### Frontend (.env)
```env
VITE_API_BASE_URL=http://localhost:8000/api
VITE_APP_NAME="TopUp SMM Game"
```

## 📚 API Documentation

API endpoint tersedia di: `http://localhost:8000/api`

### Authentication
- `POST /api/register` - Register user baru
- `POST /api/login` - Login
- `POST /api/logout` - Logout
- `GET /api/auth/google` - OAuth Google
- `GET /api/auth/google/callback` - Google callback
- `GET /api/auth/facebook` - OAuth Facebook
- `GET /api/auth/facebook/callback` - Facebook callback

### Services
- `GET /api/services` - List semua services
- `GET /api/services/{id}` - Detail service
- `GET /api/categories` - List categories

### Orders
- `POST /api/orders` - Create order
- `GET /api/orders/{orderNumber}` - Track order
- `GET /api/user/orders` - User order history

### Payments
- `POST /api/payments/process` - Process payment
- `POST /api/payments/notification` - Midtrans notification webhook

### Vouchers
- `POST /api/vouchers/validate` - Validate voucher code

### Wallet
- `GET /api/wallet/balance` - Get wallet balance
- `POST /api/wallet/topup` - Top up wallet

## 🗄️ Database Schema

Database migrations sudah include untuk semua tabel:
- users
- services
- service_duration_other
- sosmed (untuk Social Media services)
- orders
- vouchers
- wallets
- wallet_transactions
- api_settings
- payment_channels

## 🔄 Migration dari PHP Native

Project ini merupakan hasil migrasi dari PHP native (topupsmmgame-v4) dengan improvement:
- ✅ Modern framework (Laravel 12)
- ✅ Separation of concerns (Backend/Frontend)
- ✅ Better security practices
- ✅ Scalable architecture
- ✅ OAuth integration
- ✅ Better code organization
- ✅ API-first approach

## 📝 License

Private project

## 👨‍💻 Developer

Developed by ronisachsku
