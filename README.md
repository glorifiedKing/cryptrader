# Cryptrader - Cryptocurrency Trading Platform

A real-time cryptocurrency trading platform built with Laravel and Vue.js, featuring limit orders, order matching, commission handling, and live updates via Pusher.

## Features

- **User Authentication**: Secure registration and login with Laravel Fortify
- **Wallet Management**: Track USD balance and cryptocurrency assets (supported symbols are configurable in .env file)
- **Limit Orders**: Create buy and sell orders with custom price and amount
- **Order Matching**: Automatic full-match order execution via background jobs
- **Commission System**: Configurable 1.5% (configurable in .env file) commission on trades
- **Real-time Updates**: Live order status, balance, and asset updates via Pusher
- **Order History**: View and filter all orders by symbol, side, and status
- **Orderbook**: Real-time display of open buy and sell orders

## Tech Stack

- **Backend**: Laravel 12, PHP 8.3
- **Frontend**: Vue.js 3 (Composition API), TypeScript, Tailwind CSS 4
- **Database**: SQLite (easily switchable to MySQL/PostgreSQL)
- **Real-time**: Pusher, Laravel Echo
- **Queue**: Database queue driver

## Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- SQLite (or MySQL/PostgreSQL)
- Pusher account (free tier available)

## Installation

### 1. Clone and Install Dependencies

```bash
git clone https://github.com/glorifiedking/cryptrader.git
cd cryptrader
composer install
npm install
```

### 2. Environment Configuration

Copy the example environment file:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

### 3. Configure Pusher

Sign up for a free Pusher account at https://pusher.com and create a new app.

Update your `.env` file with your Pusher credentials:

```env
BROADCAST_CONNECTION=pusher

PUSHER_APP_ID=your-app-id
PUSHER_APP_KEY=your-app-key
PUSHER_APP_SECRET=your-app-secret
PUSHER_APP_CLUSTER=your-cluster
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME="https"
PUSHER_APP_CLUSTER="mt1"
```

Also update the Vite environment variables:

```env
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
```

### 4. Database Setup

Run migrations:

```bash
php artisan migrate:fresh
```

Seed demo data (optional but recommended):

```bash
php artisan db:seed 
```

This creates 5 demo users with balances and assets, plus sample orders.

**Demo Credentials**:
- Email: `trader1@cryptrader.com` to `trader5@cryptrader.com`
- Password: `password`

### 5. Build Frontend Assets

```bash
npm run build
```

## Running the Application

### Development Mode (Recommended)

The easiest way to run the application is using the built-in dev script that starts all services concurrently:

```bash
composer dev
```

This starts:
- Laravel development server (http://localhost:8000)
- Queue worker (for order matching)
- Log viewer (Pail)
- Vite dev server (for hot module replacement)

### Manual Mode

Alternatively, run each service in separate terminals:

**Terminal 1 - Laravel Server**:
```bash
php artisan serve
```

**Terminal 2 - Queue Worker**:
```bash
php artisan queue:listen
```

**Terminal 3 - Vite Dev Server**:
```bash
npm run dev
```

## Usage

1. **Register/Login**: Create an account or use a demo account
2. **Navigate to Trading**: Visit http://localhost:8000/trading
3. **View Wallet**: visit http://localhost:8000/wallet to Check your USD balance and cryptocurrency assets
4. **Place Orders**:
   - Select symbol (BTC/ETH)
   - Choose side (Buy/Sell)
   - Enter price and amount
   - Click "Place Order"
5. **Watch Real-time Updates**: Orders automatically match and balances update instantly
6. **Manage Orders**: View, filter, and cancel your orders in the Order History panel

## Configuration

### Commission Settings

Edit `config/trading.php` or set environment variables:

```env
TRADING_COMMISSION_RATE=0.015  # 1.5%
TRADING_COMMISSION_PAID_BY=buyer  # buyer|seller|split
```

### Supported Symbols

Add more symbols in `config/trading.php` set environment variables::

```php
'supported_symbols' => ['BTC', 'ETH', 'SOL', 'ADA'],
```

```env
TRADING_SUPPORTED_SYMBOLS=BTC,ETH
```

### Initial Balance

Edit `config/trading.php` or set environment variables:

```php
'initial_balance' => 10,
```

```env
TRADING_INITIAL_BALANCE=10
```

## Architecture

### Backend

- **Models**: User, Asset, Order, Trade
- **Enums**: OrderStatus (Open/Filled/Cancelled), OrderSide (Buy/Sell)
- **Actions**: CreateBuyOrderAction, CreateSellOrderAction, ExecuteTradeAction, CancelOrderAction
- **Jobs**: MatchOrderJob (queued for async processing)
- **Events**: OrderMatched (broadcasts to private user channels)
- **API Endpoints**:
  - `GET /api/profile` - User balance and assets
  - `GET /api/orders?symbol=BTC` - Orderbook
  - `POST /api/orders` - Create order
  - `POST /api/orders/{id}/cancel` - Cancel order
  - `POST /api/orders/{id}/match` - Trigger matching

### Frontend

- **Pages**: Trading.vue
- **Components**: LimitOrderForm, OrderBook, WalletOverview, OrderHistory
- **Composables**: useTrading, useTradingApi
- **Real-time**: Laravel Echo integration with Pusher

## screenshots
![homepage](/public/screenshot1.png)
![trading](/public/screenshot2.png)
![wallet](/public/screenshot3.png)

### Security Features

- Pessimistic locking on balance/asset updates (prevents race conditions)
- Database transactions for all financial operations
- Authorization checks on order cancellation
- Input validation on all API endpoints
- Sanctum authentication for API routes

## Testing

Run the test suite:

```bash
php artisan test
```

## Troubleshooting

### Queue not processing
Make sure the queue worker is running:
```bash
php artisan queue:listen
```

### Real-time updates not working
1. Check Pusher credentials in `.env`
2. Verify `BROADCAST_CONNECTION=pusher`
3. Check browser console for WebSocket errors

### Orders not matching
1. Ensure queue worker is running
2. Check that prices overlap (buy price >= sell price)
3. Verify amounts match exactly (full match only)

## License

This project is open-sourced software licensed under the MIT license.
