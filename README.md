# MyMoneyBox - Contribution Collection Platform

A modern Laravel-based platform for creating and managing money boxes (contribution collections) with payment integration, QR codes, and social sharing.

## 🚀 Features

### Core Functionality
- **Money Box Management**: Create, edit, and manage multiple money boxes
- **Flexible Contribution Rules**:
  - Variable amounts
  - Fixed amounts
  - Minimum/Maximum/Range limits
  - Anonymous or identified contributors
- **Payment Integration**: Paystack integration with extensible manager pattern for additional providers
- **QR Code Generation**: Automatic QR code creation for each money box
- **Social Sharing**: WhatsApp, Facebook, and Twitter sharing
- **Progress Tracking**: Real-time progress bars and goal tracking
- **Statistics & Analytics**: Detailed contribution tracking and reporting

### Public Features
- Browse public money boxes
- Filter by category and search
- Contribute to money boxes
- View contribution history

### User Features (Authenticated)
- Personal dashboard with statistics
- Create and manage money boxes
- View detailed analytics
- Share money boxes
- Track contributions
- Export-ready data views

## 🛠 Technology Stack

- **Backend**: Laravel 12
- **Frontend**: Blade Templates + Tailwind CSS 4
- **Payments**: Paystack (extensible for Stripe, Flutterwave)
- **QR Codes**: endroid/qr-code
- **Authentication**: Laravel Fortify
- **Database**: MySQL/PostgreSQL/SQLite
- **Architecture**: Action Pattern with Events/Listeners

## 🎨 Color Scheme

MyMoneyBox uses a green color palette to represent growth and money:

- **Primary Color**: Deeper Green (#16a34a) - Used for main branding, CTAs, and important UI elements
- **Secondary Color**: Lighter Green (#4ade80) - Used for secondary actions and accents

See [COLOR_SCHEME.md](COLOR_SCHEME.md) for full color palette and usage guidelines.

## 📋 Requirements

- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL 5.7+ / PostgreSQL 9.6+ / SQLite 3.8+

## 🔧 Installation

1. **Clone the repository**
```bash
git clone <repository-url>
cd mymoneybox
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database**
Edit `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mymoneybox
DB_USERNAME=root
DB_PASSWORD=
```

5. **Configure payment provider**
Add your Paystack credentials to `.env`:
```env
PAYMENT_PROVIDER=paystack
PAYSTACK_PUBLIC_KEY=your_public_key
PAYSTACK_SECRET_KEY=your_secret_key
```

6. **Run migrations**
```bash
php artisan migrate
```
This will create all tables and seed initial data (countries and categories).

7. **Create storage link**
```bash
php artisan storage:link
```

8. **Build assets**
```bash
npm run build
```

9. **Start development server**
```bash
php artisan serve
```

Visit `http://localhost:8000` to see the application.

## 📁 Project Structure

```
app/
├── Actions/              # Action classes (CreateMoneyBox, ProcessContribution, etc.)
├── Enums/               # Enum classes (Visibility, AmountType, PaymentStatus, etc.)
├── Events/              # Event classes
├── Listeners/           # Event listeners
├── Models/              # Eloquent models
├── Payment/             # Payment manager and providers
├── Http/
│   ├── Controllers/     # Controllers
│   └── Livewire/       # Livewire components (optional)
└── Policies/            # Authorization policies

resources/
└── views/
    ├── public/          # Public pages
    ├── money-boxes/     # Authenticated money box pages
    └── components/      # Reusable Blade components
```

## 🎯 Key Design Patterns

### Action Pattern
Instead of traditional Service classes, we use Actions that:
- Execute a single responsibility
- Fire events upon completion
- Are easily testable
- Provide clear audit trails

Example:
```php
$moneyBox = $createMoneyBoxAction->execute($data);
// Fires: MoneyBoxCreated event
// Generates: QR code automatically
```

### Manager Pattern (Payments)
Easily switch between payment providers:
```php
$payment = app(PaymentManager::class)
    ->provider('paystack')
    ->initializePayment($data);
```

### Event-Driven Architecture
All major actions fire events that trigger listeners:
- `MoneyBoxCreated` → Send welcome email
- `ContributionProcessed` → Thank you email + Owner notification
- `MoneyBoxStatsUpdated` → Update analytics

## 🔐 Authorization

Authorization is handled via Laravel Policies:
- Users can only view/edit/delete their own money boxes
- Public money boxes are viewable by anyone
- Private money boxes require direct link access

## 🧪 Testing

Run tests with:
```bash
php artisan test
```

## 📊 Database Schema

### Main Tables
- `countries` - Countries with currency information (seeded)
- `categories` - Money box categories (seeded)
- `users` - User accounts
- `money_boxes` - Money box records
- `contributions` - Contribution records

### Key Relationships
- User → MoneyBoxes (one-to-many)
- Category → MoneyBoxes (one-to-many)
- MoneyBox → Contributions (one-to-many)
- Country → Users (one-to-many)

## 🔄 Payment Flow

1. User creates money box
2. Contributor fills contribution form
3. System initializes payment with provider (Paystack)
4. User redirected to payment gateway
5. After payment, callback updates contribution status
6. Webhook verifies and confirms payment
7. Money box statistics updated automatically
8. Events fired for notifications

## 🎨 Customization

### Adding New Payment Provider

1. Create provider class implementing `PaymentProviderInterface`:
```php
class StripeProvider implements PaymentProviderInterface
{
    public function initializePayment(array $data): array { }
    public function verifyPayment(string $reference): array { }
    public function handleWebhook(array $payload): void { }
    public function getName(): string { }
}
```

2. Register in `AppServiceProvider`:
```php
$manager->extend('stripe', new StripeProvider());
```

### Adding New Categories

Edit the categories migration or seed new ones:
```bash
php artisan db:seed --class=CategorySeeder
```

## 📝 Environment Variables

Key environment variables:
```env
APP_NAME=MyMoneyBox
APP_URL=http://localhost:8000

PAYMENT_PROVIDER=paystack
PAYSTACK_PUBLIC_KEY=pk_test_xxx
PAYSTACK_SECRET_KEY=sk_test_xxx

MAIL_MAILER=smtp
MAIL_FROM_ADDRESS=noreply@mymoneybox.com
MAIL_FROM_NAME="${APP_NAME}"
```

## 🚀 Deployment

### Production Checklist

1. Set `APP_ENV=production` and `APP_DEBUG=false`
2. Run `php artisan config:cache`
3. Run `php artisan route:cache`
4. Run `php artisan view:cache`
5. Set up queue worker for background jobs
6. Configure proper session and cache drivers
7. Set up SSL certificate
8. Configure webhook URL with payment provider

### Queue Configuration

For background jobs (emails, notifications):
```bash
php artisan queue:work --tries=3
```

Or use Supervisor for production.

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Write tests for new features
5. Submit a pull request

## 📄 License

This project is open-sourced software licensed under the MIT license.

## 🆘 Support

For issues and questions:
- Check the documentation in `PROJECTSPECS.md`
- Open an issue on GitHub
- Review existing issues for solutions

## 🎉 Credits

Built with Laravel, Tailwind CSS, and modern PHP practices.

---

**Version**: 1.0.0
**Last Updated**: October 2025
