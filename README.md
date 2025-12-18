# Laravel Enum Options

[![Latest Version on Packagist](https://img.shields.io/packagist/v/weijukeji/laravel-enum-options.svg?style=flat-square)](https://packagist.org/packages/weijukeji/laravel-enum-options)
[![Total Downloads](https://img.shields.io/packagist/dt/weijukeji/laravel-enum-options.svg?style=flat-square)](https://packagist.org/packages/weijukeji/laravel-enum-options)
[![License](https://img.shields.io/packagist/l/weijukeji/laravel-enum-options.svg?style=flat-square)](https://packagist.org/packages/weijukeji/laravel-enum-options)

English | [简体中文](README.zh-CN.md)

A Laravel package for handling enums with frontend-friendly options (labels, colors, icons) and multi-language support.

## Features

- 🎨 **Frontend-Friendly**: Built-in support for labels, colors, and icons
- 🌍 **Multi-Language**: Full i18n support with fallback mechanism
- 📦 **Preset Enums**: 10+ ready-to-use enum classes for common scenarios
- 🎯 **Flexible**: Use presets as-is, publish and customize, or create your own
- ⚙️ **Configurable**: Override labels and colors without modifying enum classes
- 🛠️ **Artisan Commands**: Generate, publish, and list enums with ease
- 🔄 **Resource Integration**: Works seamlessly with Laravel API Resources

## Requirements

- PHP 8.1+
- Laravel 10.x, 11.x, or 12.x

## Installation

Install the package via Composer:

```bash
composer require weijukeji/laravel-enum-options
```

Publish the configuration file (optional):

```bash
php artisan vendor:publish --tag=enum-options-config
```

## Quick Start

### Using Preset Enums

Use built-in preset enums directly:

```php
use WeiJuKeJi\EnumOptions\Presets\Payment\PaymentMethodEnum;

$method = PaymentMethodEnum::WECHAT;
echo $method->label();  // 微信支付
echo $method->color();  // green
echo $method->icon();   // wechat

// Get all options for dropdown
$options = PaymentMethodEnum::options();
// [
//     ['value' => 'wechat', 'label' => '微信支付', 'color' => 'green', 'icon' => 'wechat'],
//     ['value' => 'alipay', 'label' => '支付宝', 'color' => 'blue', 'icon' => 'alipay'],
//     ...
// ]
```

### Publishing Presets to Your App

Publish preset enums to customize them:

```bash
# Publish a specific preset
php artisan enum:publish PaymentMethod

# Publish all presets
php artisan enum:publish --all

# Publish with translation files
php artisan enum:publish PaymentMethod --with-translations
```

The enum will be published to `app/Enums/PaymentMethodEnum.php` and you can freely modify it.

### Creating Custom Enums

Create your own enum from scratch:

```bash
php artisan make:enum SubscriptionStatus --values=active,paused,cancelled --labels
```

This generates:

```php
<?php

namespace App\Enums;

use WeiJuKeJi\EnumOptions\Traits\EnumOptions;

enum SubscriptionStatusEnum: string
{
    use EnumOptions;

    case ACTIVE = 'active';
    case PAUSED = 'paused';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return $this->trans($this->value, match ($this) {
            self::ACTIVE => 'Active',
            self::PAUSED => 'Paused',
            self::CANCELLED => 'Cancelled',
        });
    }

    public function color(): string
    {
        $configColor = config("enum-options.color_overrides.subscription_status.{$this->value}");
        if ($configColor) {
            return $configColor;
        }

        return match ($this) {
            // TODO: Define colors for each case
            default => 'default',
        };
    }
}
```

## Available Preset Enums

### Payment
- **PaymentMethodEnum**: wechat, alipay, bank_transfer, cash, credit_card, debit_card, union_pay, paypal, apple_pay, google_pay, pos, wechat_pos, other
- **PaymentStatusEnum**: unpaid, pending, paying, paid, failed, cancelled, refunding, refunded, partially_refunded, timeout
- **RefundStatusEnum**: none, pending, processing, partial, full, failed, rejected

### Order
- **OrderStatusEnum**: pending, confirmed, processing, completed, cancelled, expired, failed, on_hold, refunded, partially_refunded
- **OrderTypeEnum**: standard, presale, group_buy, flash_sale, subscription, gift, exchange

### User
- **UserStatusEnum**: active, inactive, suspended, banned, deleted, pending_verification
- **GenderEnum**: male, female, other, prefer_not_to_say

### Business
- **ApprovalStatusEnum**: draft, pending, approved, rejected, cancelled, revoked
- **PublishStatusEnum**: draft, scheduled, published, unpublished, archived

## List Available Presets

```bash
# List all presets
php artisan enum:list-presets

# Show details of a specific preset
php artisan enum:list-presets PaymentMethod

# Output as JSON
php artisan enum:list-presets --json
```

## Usage in API Resources

Use enums in your API Resources to automatically format status fields:

```php
use App\Enums\OrderStatusEnum;
use App\Enums\PaymentMethodEnum;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'order_no' => $this->order_no,

            // Transform enum fields to objects with value, label, color
            'status' => OrderStatusEnum::fromValue($this->status)?->toArray(),
            'payment_method' => PaymentMethodEnum::fromValue($this->payment_method)?->toArray(),

            // Other fields...
        ];
    }
}
```

API Response:

```json
{
  "id": 1,
  "order_no": "ORD20250118001",
  "status": {
    "value": "paid",
    "label": "已支付",
    "color": "green"
  },
  "payment_method": {
    "value": "wechat",
    "label": "微信支付",
    "color": "green",
    "icon": "wechat"
  }
}
```

## Configuration

### Override Labels

Override labels without modifying enum classes:

```php
// config/enum-options.php
'label_overrides' => [
    'payment_method' => [
        'wechat' => '微信',  // Simplified label
        'pos' => 'POS刷卡',  // Custom terminology
    ],
],
```

### Override Colors

```php
// config/enum-options.php
'color_overrides' => [
    'payment_method' => [
        'wechat' => 'success',
        'alipay' => 'primary',
    ],
],
```

### Change Color Scheme

Support for different UI frameworks:

```php
// config/enum-options.php
'color_scheme' => 'element-plus',  // or 'ant-design', 'tailwind', 'bootstrap'
```

## Multi-Language Support

### Using Translation Files

Create translation files in your application:

```php
// lang/zh-CN/enums.php
return [
    'payment_method' => [
        'wechat' => '微信支付',
        'alipay' => '支付宝',
    ],
];
```

Enums will automatically use these translations with fallback to preset translations.

### Translation Priority

1. User-defined translations (`lang/{locale}/enums.php`)
2. Config overrides (`config/enum-options.php`)
3. Package preset translations (`lang/{locale}/presets.php`)
4. Default values in enum class

## Advanced Usage

### Get All Values

```php
$values = PaymentMethodEnum::values();
// ['wechat', 'alipay', 'bank_transfer', ...]
```

### Get All Labels

```php
$labels = PaymentMethodEnum::labels();
// ['wechat' => '微信支付', 'alipay' => '支付宝', ...]
```

### Validate Values

```php
if (PaymentMethodEnum::isValid($input)) {
    $enum = PaymentMethodEnum::from($input);
}
```

### Safe Conversion

```php
$enum = PaymentMethodEnum::fromValue($nullable);  // Returns null if value is null or invalid
```

### Safe Array Conversion with Fallback

When dealing with legacy data or external systems, you might have invalid enum values in your database. Use `toArraySafe()` to handle these gracefully without throwing exceptions:

```php
// Safe conversion - returns fallback object for invalid values
$result = PaymentStatusEnum::toArraySafe('old_invalid_status');
// Result: [
//     'value' => 'old_invalid_status',
//     'label' => 'old_invalid_status',  // or transformed based on config
//     'color' => 'default',
//     'icon' => null
// ]

// Use in API Resources to handle any value safely
class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            // Will never throw exception even if status value is invalid
            'status' => OrderStatusEnum::toArraySafe($this->status),
        ];
    }
}
```

Configure fallback behavior in `config/enum-options.php`:

```php
// Fallback color for invalid values
'fallback_color' => 'default',

// Label transformation strategy: none, upper, lower, ucfirst, ucwords
'fallback_label_transform' => 'ucwords',  // 'old_status' -> 'Old Status'

// Custom labels for specific invalid values
'fallback_labels' => [
    'legacy_paid' => 'Paid (Legacy)',
    'unknown' => 'Unknown Status',
],
```

## Automatic Enum Discovery

The package automatically discovers and registers enums without manual configuration.

### How It Works

1. **Preset Enums**: Automatically scanned from `src/Presets` directory
2. **App Enums**: Automatically scanned from `app/Enums` directory
3. **No Maintenance Required**: Just create enum files, they're discovered automatically

### Adding New Enums

Simply create a new enum file with the `EnumOptions` trait:

```php
// app/Enums/ShippingStatusEnum.php
namespace App\Enums;

use WeiJuKeJi\EnumOptions\Traits\EnumOptions;

enum ShippingStatusEnum: string
{
    use EnumOptions;

    case PENDING = 'pending';
    case SHIPPED = 'shipped';

    public function label(): string { return $this->value; }
}
```

The enum is **automatically registered** and available via API without any configuration.

### Configure Discovery

Control auto-discovery in `config/enum-options.php`:

```php
// Auto-discover preset enums from src/Presets
'auto_discover_presets' => true,

// Auto-discover app enums from app/Enums
'auto_discover_app_enums' => true,

// Customize scan paths
'app_enums_paths' => [
    app_path('Enums'),
    app_path('Domain/Shared/Enums'),  // Additional paths
],
```

## API Routes (Optional)

The package can automatically register API routes to provide enum options to your frontend.

### Enable Auto Routes

Publish and edit the config file:

```bash
php artisan vendor:publish --tag=enum-options-config
```

Enable auto routes in `config/enum-options.php`:

```php
'auto_register_routes' => true,
'route_prefix' => 'api/enums',
'route_middleware' => ['auth:sanctum'],
'route_name_prefix' => 'enums',
```

### Available Endpoints

Once enabled, the following endpoints will be available:

```bash
GET /api/enums/list                   # Get metadata of all available enums
GET /api/enums/all                    # Get all enum options (recommended)
GET /api/enums/payment-methods        # Payment methods
GET /api/enums/payment-statuses       # Payment statuses
GET /api/enums/refund-statuses        # Refund statuses
GET /api/enums/order-statuses         # Order statuses
GET /api/enums/order-types            # Order types
GET /api/enums/user-statuses          # User statuses
GET /api/enums/genders                # Genders
GET /api/enums/approval-statuses      # Approval statuses
GET /api/enums/publish-statuses       # Publish statuses
```

### Response Format

**Enum List Endpoint** (`GET /api/enums/list`):

Returns metadata about all available enums:

```json
{
  "code": 200,
  "msg": "success",
  "data": {
    "list": [
      {
        "key": "payment_methods",
        "name": "Payment Methods",
        "description": "All available payment method options",
        "route": "/enums/payment-methods",
        "count": 13,
        "category": "payment"
      }
    ],
    "total": 9
  }
}
```

**Single Enum Endpoints** (e.g., `GET /api/enums/payment-methods`):

Returns enum options in a standardized list format:

```json
{
  "code": 200,
  "msg": "success",
  "data": {
    "list": [
      {
        "value": "wechat",
        "label": "微信支付",
        "color": "green",
        "icon": "wechat"
      },
      {
        "value": "alipay",
        "label": "支付宝",
        "color": "blue",
        "icon": "alipay"
      }
    ],
    "total": 13
  }
}
```

**All Enums Endpoint** (`GET /api/enums/all`):

Returns all enum options grouped by key:

```json
{
  "code": 200,
  "msg": "success",
  "data": {
    "payment_methods": [...],
    "payment_statuses": [...],
    "order_statuses": [...]
  }
}
```

The response format can be customized in config:

```php
'response_format' => [
    'code_key' => 'code',
    'message_key' => 'msg',
    'data_key' => 'data',
],
```

### Manual Route Registration

If you prefer manual control, keep `auto_register_routes` as `false` and register routes yourself:

```php
// routes/api.php
use WeiJuKeJi\EnumOptions\Http\Controllers\EnumController;

Route::prefix('enums')->middleware('auth:sanctum')->group(function () {
    Route::get('all', [EnumController::class, 'all']);
    Route::get('payment-methods', [EnumController::class, 'paymentMethods']);
    // ... other routes
});
```

Or create your own controller:

```php
namespace App\Http\Controllers;

use WeiJuKeJi\EnumOptions\Presets\Payment\PaymentMethodEnum;

class MyEnumController extends Controller
{
    public function paymentMethods()
    {
        return response()->json([
            'code' => 200,
            'data' => PaymentMethodEnum::options(),
        ]);
    }
}
```

## Frontend Integration

### Vue 3 Example

```vue
<template>
  <!-- Display with color -->
  <el-tag :type="order.status.color">
    {{ order.status.label }}
  </el-tag>

  <!-- Dropdown selection -->
  <el-select v-model="filters.payment_method">
    <el-option
      v-for="method in paymentMethods"
      :key="method.value"
      :value="method.value"
      :label="method.label"
    >
      <i :class="`icon-${method.icon}`" />
      {{ method.label }}
    </el-option>
  </el-select>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const paymentMethods = ref([])

onMounted(async () => {
  // Fetch enum options from backend
  const { data } = await axios.get('/api/enums/payment-methods')
  paymentMethods.value = data.data.list  // Access the list from response
})
</script>
```

### React Example

```jsx
import { useEffect, useState } from 'react'

function OrderList() {
  const [enums, setEnums] = useState({})

  useEffect(() => {
    // Fetch all enums at once
    fetch('/api/enums/all')
      .then(res => res.json())
      .then(data => setEnums(data.data))
  }, [])

  return (
    <div>
      {/* Display status with color */}
      <Badge color={order.status.color}>
        {order.status.label}
      </Badge>

      {/* Dropdown */}
      <Select>
        {enums.payment_methods?.map(method => (
          <Option key={method.value} value={method.value}>
            {method.label}
          </Option>
        ))}
      </Select>
    </div>
  )
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email your.email@example.com instead of using the issue tracker.

## Credits

- [Your Name](https://github.com/your-username)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
