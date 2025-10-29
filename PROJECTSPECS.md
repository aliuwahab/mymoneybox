# FundFlow - Technical Specification

## Product Name Suggestions
1. **FundFlow** - Primary recommendation
2. FlowBox
3. ContribHub
4. GiftPool
5. CollectBox

**Money Box → Flow Box / Contribution Box**

---

## Core Features Overview

### 1. Flow Box (Money Box) Management

#### Flow Box Attributes
- **Title & Description**
- **Visibility**: Private (unlisted) or Public (listed on homepage)
- **Category**: Customizable categories for public boxes
- **Contributor Identity Rules**:
    - Anonymous allowed
    - Must identify (known only)
    - User's choice per contribution

- **Contribution Amount Rules**:
    - Fixed amount
    - Variable (any amount)
    - Minimum amount only
    - Maximum amount only
    - Min-Max range

- **Time Settings**:
    - Start date (optional)
    - End date (optional)
    - Ongoing/Perpetual (no end date)
    - Timezone-aware

- **Currency**: Based on creator's country
- **Unique Public URL**: `/box/{slug}` or `/flow/{slug}`
- **QR Code**: Auto-generated for each box
- **Social Sharing**: Pre-formatted for WhatsApp, Facebook, Twitter

---

## Database Schema

### Tables

```sql
-- Countries & Currencies
countries
  - id
  - name
  - code (ISO 3166-1 alpha-2)
  - currency_name
  - currency_code (ISO 4217)
  - currency_symbol
  - is_active
  - timestamps

-- Users
users
  - id
  - name
  - email
  - password
  - country_id (FK)
  - email_verified_at
  - remember_token
  - timestamps

-- Categories
categories
  - id
  - name
  - slug
  - icon (optional)
  - sort_order
  - is_active
  - timestamps

-- Flow Boxes (Money Boxes)
flow_boxes
  - id
  - user_id (FK)
  - category_id (FK, nullable)
  - title
  - slug (unique)
  - description (text)
  - goal_amount (decimal, nullable)
  - currency_code
  - visibility (enum: 'public', 'private')
  - contributor_identity (enum: 'anonymous_allowed', 'must_identify', 'user_choice')
  - amount_type (enum: 'fixed', 'variable', 'minimum', 'maximum', 'range')
  - fixed_amount (decimal, nullable)
  - minimum_amount (decimal, nullable)
  - maximum_amount (decimal, nullable)
  - start_date (datetime, nullable)
  - end_date (datetime, nullable)
  - is_ongoing (boolean)
  - qr_code_path
  - total_contributions (decimal, default: 0)
  - contribution_count (integer, default: 0)
  - is_active (boolean)
  - timestamps
  - soft_deletes

-- Contributions
contributions
  - id
  - flow_box_id (FK)
  - contributor_name (nullable)
  - contributor_email (nullable)
  - contributor_phone (nullable)
  - amount (decimal)
  - currency_code
  - is_anonymous (boolean)
  - message (text, nullable)
  - payment_method
  - payment_reference
  - payment_status (enum: 'pending', 'completed', 'failed', 'refunded')
  - ip_address
  - user_agent
  - timestamps

-- QR Codes (if storing separately)
qr_codes
  - id
  - flow_box_id (FK)
  - file_path
  - format (png, svg)
  - timestamps
```

---

## Laravel Project Structure

```
app/
├── Models/
│   ├── User.php
│   ├── Country.php
│   ├── Category.php
│   ├── FlowBox.php
│   └── Contribution.php
├── Http/
│   ├── Controllers/
│   │   ├── FlowBoxController.php
│   │   ├── ContributionController.php
│   │   └── PublicBoxController.php
│   ├── Livewire/
│   │   ├── CreateFlowBox.php
│   │   ├── EditFlowBox.php
│   │   ├── FlowBoxList.php
│   │   ├── PublicBoxFeed.php
│   │   ├── ContributeToBox.php
│   │   └── BoxStatistics.php
│   └── Middleware/
│       └── CheckFlowBoxOwnership.php
├── Services/
│   ├── QRCodeService.php
│   ├── CurrencyService.php
│   ├── PaymentService.php
│   └── ShareService.php
└── Enums/
    ├── Visibility.php
    ├── ContributorIdentity.php
    ├── AmountType.php
    └── PaymentStatus.php

resources/
├── views/
│   ├── layouts/
│   │   └── app.blade.php
│   ├── livewire/
│   │   ├── create-flow-box.blade.php
│   │   ├── edit-flow-box.blade.php
│   │   ├── flow-box-list.blade.php
│   │   ├── public-box-feed.blade.php
│   │   ├── contribute-to-box.blade.php
│   │   └── box-statistics.blade.php
│   ├── flow-boxes/
│   │   ├── show.blade.php (public contribution page)
│   │   └── dashboard.blade.php
│   └── components/
│       ├── box-card.blade.php
│       ├── contribution-form.blade.php
│       └── qr-code-display.blade.php
└── css/
    └── app.css (Tailwind)
```

---

## Key Models & Relationships

### FlowBox Model
```php
class FlowBox extends Model
{
    protected $fillable = [
        'user_id', 'category_id', 'title', 'slug', 'description',
        'goal_amount', 'currency_code', 'visibility', 'contributor_identity',
        'amount_type', 'fixed_amount', 'minimum_amount', 'maximum_amount',
        'start_date', 'end_date', 'is_ongoing', 'qr_code_path',
        'total_contributions', 'contribution_count', 'is_active'
    ];

    protected $casts = [
        'visibility' => Visibility::class,
        'contributor_identity' => ContributorIdentity::class,
        'amount_type' => AmountType::class,
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_ongoing' => 'boolean',
        'is_active' => 'boolean',
        'goal_amount' => 'decimal:2',
        'fixed_amount' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'maximum_amount' => 'decimal:2',
        'total_contributions' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function contributions()
    {
        return $this->hasMany(Contribution::class);
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('visibility', Visibility::Public);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function($q) {
                $q->where('is_ongoing', true)
                  ->orWhere(function($q2) {
                      $q2->whereNull('end_date')
                         ->orWhere('end_date', '>=', now());
                  });
            });
    }

    public function scopeStarted($query)
    {
        return $query->where(function($q) {
            $q->whereNull('start_date')
              ->orWhere('start_date', '<=', now());
        });
    }

    // Helpers
    public function isActive(): bool
    {
        if (!$this->is_active) return false;
        
        $now = now();
        
        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }
        
        if (!$this->is_ongoing && $this->end_date && $now->gt($this->end_date)) {
            return false;
        }
        
        return true;
    }

    public function getPublicUrl(): string
    {
        return route('box.show', $this->slug);
    }

    public function getCurrencySymbol(): string
    {
        return Country::where('currency_code', $this->currency_code)
            ->value('currency_symbol') ?? $this->currency_code;
    }

    public function formatAmount($amount): string
    {
        return $this->getCurrencySymbol() . number_format($amount, 2);
    }

    public function canAcceptContributions(): bool
    {
        return $this->isActive();
    }

    public function validateContributionAmount($amount): bool
    {
        switch ($this->amount_type) {
            case AmountType::Fixed:
                return $amount == $this->fixed_amount;
            
            case AmountType::Minimum:
                return $amount >= $this->minimum_amount;
            
            case AmountType::Maximum:
                return $amount <= $this->maximum_amount;
            
            case AmountType::Range:
                return $amount >= $this->minimum_amount 
                    && $amount <= $this->maximum_amount;
            
            case AmountType::Variable:
            default:
                return $amount > 0;
        }
    }
}
```

### Contribution Model
```php
class Contribution extends Model
{
    protected $fillable = [
        'flow_box_id', 'contributor_name', 'contributor_email',
        'contributor_phone', 'amount', 'currency_code', 'is_anonymous',
        'message', 'payment_method', 'payment_reference', 'payment_status',
        'ip_address', 'user_agent'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_anonymous' => 'boolean',
        'payment_status' => PaymentStatus::class,
    ];

    public function flowBox()
    {
        return $this->belongsTo(FlowBox::class);
    }

    public function getDisplayName(): string
    {
        return $this->is_anonymous ? 'Anonymous' : ($this->contributor_name ?? 'Anonymous');
    }
}
```

---

## Enums (PHP 8.1+)

```php
// app/Enums/Visibility.php
enum Visibility: string
{
    case Public = 'public';
    case Private = 'private';
}

// app/Enums/ContributorIdentity.php
enum ContributorIdentity: string
{
    case AnonymousAllowed = 'anonymous_allowed';
    case MustIdentify = 'must_identify';
    case UserChoice = 'user_choice';
}

// app/Enums/AmountType.php
enum AmountType: string
{
    case Fixed = 'fixed';
    case Variable = 'variable';
    case Minimum = 'minimum';
    case Maximum = 'maximum';
    case Range = 'range';
}

// app/Enums/PaymentStatus.php
enum PaymentStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Failed = 'failed';
    case Refunded = 'refunded';
}
```

---

## Services

### QRCodeService
```php
namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\FlowBox;
use Illuminate\Support\Facades\Storage;

class QRCodeService
{
    public function generateForFlowBox(FlowBox $flowBox): string
    {
        $url = $flowBox->getPublicUrl();
        $filename = "qr-codes/{$flowBox->slug}.png";
        
        $qrCode = QrCode::format('png')
            ->size(300)
            ->margin(2)
            ->generate($url);
        
        Storage::disk('public')->put($filename, $qrCode);
        
        return $filename;
    }

    public function getQrCodeUrl(FlowBox $flowBox): string
    {
        return Storage::disk('public')->url($flowBox->qr_code_path);
    }
}
```

### ShareService
```php
namespace App\Services;

use App\Models\FlowBox;

class ShareService
{
    public function getWhatsAppShareUrl(FlowBox $flowBox): string
    {
        $text = urlencode("{$flowBox->title}\n\n{$flowBox->getPublicUrl()}");
        return "https://wa.me/?text={$text}";
    }

    public function getFacebookShareUrl(FlowBox $flowBox): string
    {
        $url = urlencode($flowBox->getPublicUrl());
        return "https://www.facebook.com/sharer/sharer.php?u={$url}";
    }

    public function getTwitterShareUrl(FlowBox $flowBox): string
    {
        $text = urlencode($flowBox->title);
        $url = urlencode($flowBox->getPublicUrl());
        return "https://twitter.com/intent/tweet?text={$text}&url={$url}";
    }

    public function getShareData(FlowBox $flowBox): array
    {
        return [
            'whatsapp' => $this->getWhatsAppShareUrl($flowBox),
            'facebook' => $this->getFacebookShareUrl($flowBox),
            'twitter' => $this->getTwitterShareUrl($flowBox),
            'qr_code' => app(QRCodeService::class)->getQrCodeUrl($flowBox),
        ];
    }
}
```

---

## Routes

```php
// routes/web.php

use App\Http\Controllers\FlowBoxController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\PublicBoxController;

Route::get('/', [PublicBoxController::class, 'index'])->name('home');
Route::get('/box/{slug}', [PublicBoxController::class, 'show'])->name('box.show');
Route::post('/box/{slug}/contribute', [ContributionController::class, 'store'])->name('box.contribute');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [FlowBoxController::class, 'dashboard'])->name('dashboard');
    Route::resource('flow-boxes', FlowBoxController::class);
    
    Route::get('/my-boxes', [FlowBoxController::class, 'index'])->name('my-boxes');
    Route::get('/flow-boxes/{flowBox}/statistics', [FlowBoxController::class, 'statistics'])->name('flow-boxes.statistics');
    Route::get('/flow-boxes/{flowBox}/share', [FlowBoxController::class, 'share'])->name('flow-boxes.share');
});
```

---

## Livewire Components

### CreateFlowBox Component
```php
namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\FlowBox;
use App\Models\Category;
use App\Services\QRCodeService;

class CreateFlowBox extends Component
{
    public $title;
    public $description;
    public $category_id;
    public $visibility = 'public';
    public $contributor_identity = 'user_choice';
    public $amount_type = 'variable';
    public $fixed_amount;
    public $minimum_amount;
    public $maximum_amount;
    public $goal_amount;
    public $start_date;
    public $end_date;
    public $is_ongoing = false;

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'category_id' => 'nullable|exists:categories,id',
        'visibility' => 'required|in:public,private',
        'contributor_identity' => 'required|in:anonymous_allowed,must_identify,user_choice',
        'amount_type' => 'required|in:fixed,variable,minimum,maximum,range',
        'fixed_amount' => 'nullable|numeric|min:0',
        'minimum_amount' => 'nullable|numeric|min:0',
        'maximum_amount' => 'nullable|numeric|min:0',
        'goal_amount' => 'nullable|numeric|min:0',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after:start_date',
        'is_ongoing' => 'boolean',
    ];

    public function save()
    {
        $this->validate();

        $flowBox = FlowBox::create([
            'user_id' => auth()->id(),
            'title' => $this->title,
            'slug' => \Str::slug($this->title) . '-' . \Str::random(6),
            'description' => $this->description,
            'category_id' => $this->category_id,
            'currency_code' => auth()->user()->country->currency_code,
            'visibility' => $this->visibility,
            'contributor_identity' => $this->contributor_identity,
            'amount_type' => $this->amount_type,
            'fixed_amount' => $this->fixed_amount,
            'minimum_amount' => $this->minimum_amount,
            'maximum_amount' => $this->maximum_amount,
            'goal_amount' => $this->goal_amount,
            'start_date' => $this->start_date,
            'end_date' => $this->is_ongoing ? null : $this->end_date,
            'is_ongoing' => $this->is_ongoing,
            'is_active' => true,
        ]);

        // Generate QR Code
        $qrCodePath = app(QRCodeService::class)->generateForFlowBox($flowBox);
        $flowBox->update(['qr_code_path' => $qrCodePath]);

        session()->flash('message', 'Flow Box created successfully!');
        
        return redirect()->route('flow-boxes.show', $flowBox);
    }

    public function render()
    {
        return view('livewire.create-flow-box', [
            'categories' => Category::where('is_active', true)->get(),
        ]);
    }
}
```

---

## Design System (Tailwind Config)

```js
// tailwind.config.js
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#f0f9ff',
          100: '#e0f2fe',
          200: '#bae6fd',
          300: '#7dd3fc',
          400: '#38bdf8',
          500: '#0ea5e9',
          600: '#0284c7',
          700: '#0369a1',
          800: '#075985',
          900: '#0c4a6e',
        },
        secondary: {
          50: '#faf5ff',
          100: '#f3e8ff',
          200: '#e9d5ff',
          300: '#d8b4fe',
          400: '#c084fc',
          500: '#a855f7',
          600: '#9333ea',
          700: '#7e22ce',
          800: '#6b21a8',
          900: '#581c87',
        },
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
```

---

## Next Steps - Implementation Order

1. **Setup Laravel Project**
    - Install Laravel, Livewire, Alpine.js, Tailwind
    - Configure database

2. **Database & Models**
    - Create migrations for all tables
    - Set up models with relationships
    - Create enums
    - Seed countries/currencies data

3. **Authentication**
    - Laravel Breeze/Jetstream with Livewire
    - Add country selection on registration

4. **Core Flow Box CRUD**
    - Create/Edit/Delete Flow Boxes
    - Dashboard for user's boxes
    - QR Code generation

5. **Public Pages**
    - Homepage with public boxes feed
    - Individual box contribution page
    - Category filtering

6. **Contribution System**
    - Payment gateway integration
    - Contribution form (Livewire)
    - Email notifications

7. **Sharing Features**
    - Social media sharing
    - QR code download
    - Embed options

8. **Analytics & Statistics**
    - Box owner dashboard
    - Contribution tracking
    - Goal progress

Would you like me to start building specific components or create the initial Laravel migrations?
