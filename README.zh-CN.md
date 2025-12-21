# Laravel Enum Options

[![Latest Version on Packagist](https://img.shields.io/packagist/v/weijukeji/laravel-enum-options.svg?style=flat-square)](https://packagist.org/packages/weijukeji/laravel-enum-options)
[![Total Downloads](https://img.shields.io/packagist/dt/weijukeji/laravel-enum-options.svg?style=flat-square)](https://packagist.org/packages/weijukeji/laravel-enum-options)
[![License](https://img.shields.io/packagist/l/weijukeji/laravel-enum-options.svg?style=flat-square)](https://packagist.org/packages/weijukeji/laravel-enum-options)

[English](README.md) | 简体中文

一个为 Laravel 设计的枚举扩展包，提供前端友好的选项（标签、颜色、图标）和多语言支持。

## 特性

- 🎨 **前端友好**: 内置标签、颜色和图标支持
- 🌍 **多语言**: 完整的 i18n 支持，带回退机制
- 📦 **预设枚举**: 10+ 开箱即用的枚举类，覆盖常见业务场景
- 🎯 **灵活使用**: 直接使用预设、发布定制或完全自定义
- ⚙️ **配置覆盖**: 无需修改枚举类即可覆盖标签和颜色
- 🛠️ **Artisan 命令**: 轻松生成、发布和列出枚举
- 🔄 **Resource 集成**: 与 Laravel API Resources 无缝协作

## 环境要求

- PHP 8.1+
- Laravel 10.x, 11.x 或 12.x

## 安装

通过 Composer 安装扩展包:

```bash
composer require weijukeji/laravel-enum-options
```

发布配置文件（可选）:

```bash
php artisan vendor:publish --tag=enum-options-config
```

## 快速开始

### 使用预设枚举

直接使用内置的预设枚举:

```php
use WeiJuKeJi\EnumOptions\Presets\Payment\PaymentMethodEnum;

$method = PaymentMethodEnum::WECHAT;
echo $method->label();  // 微信支付
echo $method->color();  // green
echo $method->icon();   // wechat

// 获取所有选项用于下拉框
$options = PaymentMethodEnum::options();
// [
//     ['value' => 'wechat', 'label' => '微信支付', 'color' => 'green', 'icon' => 'wechat'],
//     ['value' => 'alipay', 'label' => '支付宝', 'color' => 'blue', 'icon' => 'alipay'],
//     ...
// ]
```

### 发布预设到项目

发布预设枚举以便自定义:

```bash
# 发布指定预设
php artisan enum:publish PaymentMethod

# 发布所有预设
php artisan enum:publish --all

# 发布时包含翻译文件
php artisan enum:publish PaymentMethod --with-translations
```

枚举将发布到 `app/Enums/PaymentMethodEnum.php`，你可以自由修改。

### 创建自定义枚举

从头创建自己的枚举:

```bash
php artisan make:enum SubscriptionStatus --values=active,paused,cancelled --labels
```

这将生成:

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
            self::ACTIVE => '活跃',
            self::PAUSED => '暂停',
            self::CANCELLED => '已取消',
        });
    }

    public function color(): string
    {
        $configColor = config("enum-options.color_overrides.subscription_status.{$this->value}");
        if ($configColor) {
            return $configColor;
        }

        return match ($this) {
            // TODO: 为每个状态定义颜色
            default => 'default',
        };
    }
}
```

## 可用的预设枚举

### 通用 (Common)
- **BooleanEnum**: yes, no, y, n, 1, 0 - 兼容多种布尔值格式

### 支付相关 (Payment)
- **PaymentMethodEnum**: 微信、支付宝、银行转账、现金、信用卡、借记卡、云闪付、PayPal、Apple Pay、Google Pay、POS机、微信POS、其他
- **PaymentStatusEnum**: 未支付、待支付、支付中、已支付、支付失败、已取消、退款中、已退款、部分退款、超时
- **RefundStatusEnum**: 无退款、待退款、退款处理中、部分退款、全额退款、退款失败、退款拒绝

### 订单相关 (Order)
- **OrderStatusEnum**: 待处理、已确认、处理中、已完成、已取消、已过期、失败、挂起、已退款、部分退款
- **OrderTypeEnum**: 标准订单、预售、团购、限时抢购、订阅、赠品、换货

### 用户相关 (User)
- **UserStatusEnum**: 活跃、未激活、已暂停、已封禁、已删除、待验证
- **GenderEnum**: 男、女、其他、不愿透露

### 业务相关 (Business)
- **ApprovalStatusEnum**: 草稿、待审批、已通过、已拒绝、已取消、已撤销
- **PublishStatusEnum**: 草稿、定时发布、已发布、已下架、已归档

## 列出可用预设

```bash
# 列出所有预设
php artisan enum:list-presets

# 显示指定预设的详情
php artisan enum:list-presets PaymentMethod

# 以 JSON 格式输出
php artisan enum:list-presets --json
```

## 在 API Resource 中使用

在 API Resources 中使用枚举自动格式化状态字段:

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

            // 将枚举字段转换为包含 value, label, color 的对象
            'status' => OrderStatusEnum::fromValue($this->status)?->toArray(),
            'payment_method' => PaymentMethodEnum::fromValue($this->payment_method)?->toArray(),

            // 其他字段...
        ];
    }
}
```

API 响应:

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

## 配置

### 覆盖标签

无需修改枚举类即可覆盖标签:

```php
// config/enum-options.php
'label_overrides' => [
    'payment_method' => [
        'wechat' => '微信',     // 简化标签
        'pos' => 'POS刷卡',     // 自定义术语
    ],
],
```

### 覆盖颜色

```php
// config/enum-options.php
'color_overrides' => [
    'payment_method' => [
        'wechat' => 'success',
        'alipay' => 'primary',
    ],
],
```

### 更改颜色方案

支持不同的 UI 框架:

```php
// config/enum-options.php
'color_scheme' => 'element-plus',  // 或 'ant-design', 'tailwind', 'bootstrap'
```

## 多语言支持

### 使用翻译文件

在你的应用中创建翻译文件:

```php
// lang/zh-CN/enums.php
return [
    'payment_method' => [
        'wechat' => '微信支付',
        'alipay' => '支付宝',
    ],
];
```

枚举将自动使用这些翻译，并回退到预设翻译。

### 翻译优先级

1. 用户自定义翻译 (`lang/{locale}/enums.php`)
2. 配置覆盖 (`config/enum-options.php`)
3. 扩展包预设翻译 (`lang/{locale}/presets.php`)
4. 枚举类中的默认值

## 高级用法

### 获取所有值

```php
$values = PaymentMethodEnum::values();
// ['wechat', 'alipay', 'bank_transfer', ...]
```

### 获取所有标签

```php
$labels = PaymentMethodEnum::labels();
// ['wechat' => '微信支付', 'alipay' => '支付宝', ...]
```

### 验证值

```php
if (PaymentMethodEnum::isValid($input)) {
    $enum = PaymentMethodEnum::from($input);
}
```

### 安全转换

```php
$enum = PaymentMethodEnum::fromValue($nullable);  // 如果值为 null 或无效则返回 null
```

### 安全数组转换（带降级处理）

处理遗留数据或外部系统时，数据库中可能存在无效的枚举值。使用 `toArraySafe()` 优雅地处理这些值而不抛出异常：

```php
// 安全转换 - 对无效值返回降级对象
$result = PaymentStatusEnum::toArraySafe('old_invalid_status');
// 结果: [
//     'value' => 'old_invalid_status',
//     'label' => 'old_invalid_status',  // 或根据配置转换
//     'color' => 'default',
//     'icon' => null
// ]

// 在 API Resource 中使用，安全处理任何值
class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            // 即使 status 值无效也不会抛出异常
            'status' => OrderStatusEnum::toArraySafe($this->status),
        ];
    }
}
```

在 `config/enum-options.php` 中配置降级行为：

```php
// 无效值使用的颜色
'fallback_color' => 'default',

// 标签转换策略: none, upper, lower, ucfirst, ucwords
'fallback_label_transform' => 'ucwords',  // 'old_status' -> 'Old Status'

// 为特定无效值自定义标签
'fallback_labels' => [
    'legacy_paid' => '已支付（历史数据）',
    'unknown' => '未知状态',
],
```

## 自动枚举发现

扩展包自动发现和注册枚举，无需手动配置。

### 工作原理

1. **预设枚举**: 自动从 `src/Presets` 目录扫描
2. **应用枚举**: 自动从 `app/Enums` 目录扫描
3. **无需维护**: 只需创建枚举文件，自动发现

### 添加新枚举

只需创建带有 `EnumOptions` trait 的枚举文件：

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

枚举会**自动注册**并通过 API 提供，无需任何配置。

### 配置发现行为

在 `config/enum-options.php` 中控制自动发现：

```php
// 自动发现 src/Presets 中的预设枚举
'auto_discover_presets' => true,

// 自动发现 app/Enums 中的应用枚举
'auto_discover_app_enums' => true,

// 自定义扫描路径
'app_enums_paths' => [
    app_path('Enums'),
    app_path('Domain/Shared/Enums'),  // 额外路径
],
```

## API 路由（可选）

扩展包可以自动注册 API 路由，为前端提供枚举选项。

### 启用自动路由

发布并编辑配置文件:

```bash
php artisan vendor:publish --tag=enum-options-config
```

在 `config/enum-options.php` 中启用自动路由:

```php
'auto_register_routes' => true,
'route_prefix' => 'api/enums',
'route_middleware' => ['auth:sanctum'],
'route_name_prefix' => 'enums',
```

### 可用端点

**路由会为所有已注册的枚举自动生成！** 无需手动维护。

启用后，将自动注册以下端点:

```bash
GET /api/enums/list                   # 获取所有可用枚举的元数据
GET /api/enums/all                    # 获取所有枚举选项（推荐）

# 为所有预设枚举动态生成的路由:
GET /api/enums/payment-methods        # 支付方式
GET /api/enums/payment-statuses       # 支付状态
GET /api/enums/refund-statuses        # 退款状态
GET /api/enums/order-statuses         # 订单状态
GET /api/enums/order-types            # 订单类型
GET /api/enums/user-statuses          # 用户状态
GET /api/enums/genders                # 性别
GET /api/enums/approval-statuses      # 审批状态
GET /api/enums/publish-statuses       # 发布状态

# 你的自定义枚举也会自动注册:
GET /api/enums/shipping-statuses      # 如果你有 ShippingStatusEnum
GET /api/enums/{你的自定义枚举}        # 你创建的任何枚举！
```

**核心特性:**
- ✅ **零配置**: 添加新枚举，路由自动创建
- ✅ **动态发现**: 使用 EnumRegistry 查找所有枚举
- ✅ **一致的 URL**: 枚举 key 自动转换为 kebab-case（例如：`payment_methods` → `payment-methods`）
- ✅ **完全可扩展**: 支持无限数量的枚举，无需修改代码

### 响应格式

**枚举列表端点** (`GET /api/enums/list`):

返回所有可用枚举的元数据：

```json
{
  "code": 200,
  "msg": "success",
  "data": {
    "list": [
      {
        "key": "payment_methods",
        "name": "支付方式",
        "description": "所有可用的支付方式选项",
        "route": "/enums/payment-methods",
        "count": 13,
        "category": "payment"
      }
    ],
    "total": 9
  }
}
```

**单个枚举端点** (例如 `GET /api/enums/payment-methods`):

返回标准列表格式的枚举选项：

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

**所有枚举端点** (`GET /api/enums/all`):

返回按 key 分组的所有枚举选项：

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

响应格式可在配置中自定义:

```php
'response_format' => [
    'code_key' => 'code',
    'message_key' => 'msg',
    'data_key' => 'data',
],
```

### 手动注册路由

如果你希望手动控制，保持 `auto_register_routes` 为 `false` 并自己注册路由。

**方式 1: 动态注册（推荐）**

使用 EnumRegistry 动态注册路由:

```php
// routes/api.php
use WeiJuKeJi\EnumOptions\Http\Controllers\EnumController;
use WeiJuKeJi\EnumOptions\Support\EnumRegistry;
use Illuminate\Support\Str;

Route::prefix('enums')->middleware('auth:sanctum')->group(function () {
    // 固定路由
    Route::get('list', [EnumController::class, 'list']);
    Route::get('all', [EnumController::class, 'all']);

    // 动态注册所有枚举
    foreach (EnumRegistry::all() as $key => $config) {
        Route::get(Str::kebab($key), [EnumController::class, 'show'])
            ->defaults('key', $key);
    }
});
```

**方式 2: 特定路由**

手动注册特定枚举的路由:

```php
// routes/api.php
use WeiJuKeJi\EnumOptions\Http\Controllers\EnumController;

Route::prefix('enums')->middleware('auth:sanctum')->group(function () {
    Route::get('all', [EnumController::class, 'all']);

    // 使用动态 show() 方法注册特定枚举
    Route::get('payment-methods', [EnumController::class, 'show'])
        ->defaults('key', 'payment_methods');
    Route::get('order-statuses', [EnumController::class, 'show'])
        ->defaults('key', 'order_statuses');
});
```

**方式 3: 自定义控制器**

创建自己的控制器实现自定义响应格式:

```php
namespace App\Http\Controllers;

use WeiJuKeJi\EnumOptions\Support\EnumRegistry;

class MyEnumController extends Controller
{
    public function show(string $key)
    {
        $enumClass = EnumRegistry::getEnumClass($key);

        if (!$enumClass) {
            return response()->json(['error' => '未找到'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $enumClass::options(),
        ]);
    }
}
```

## 前端集成

### Vue 3 示例

```vue
<template>
  <!-- 显示带颜色的状态 -->
  <el-tag :type="order.status.color">
    {{ order.status.label }}
  </el-tag>

  <!-- 下拉选择 -->
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
  // 从后端获取枚举选项
  const { data } = await axios.get('/api/enums/payment-methods')
  paymentMethods.value = data.data.list  // 从响应中访问 list
})
</script>
```

### React 示例

```jsx
import { useEffect, useState } from 'react'

function OrderList() {
  const [enums, setEnums] = useState({})

  useEffect(() => {
    // 一次性获取所有枚举
    fetch('/api/enums/all')
      .then(res => res.json())
      .then(data => setEnums(data.data))
  }, [])

  return (
    <div>
      {/* 显示带颜色的状态 */}
      <Badge color={order.status.color}>
        {order.status.label}
      </Badge>

      {/* 下拉选择 */}
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

## 更多文档

- [前端集成完整指南](docs/FRONTEND_INTEGRATION_GUIDE.md)
- [前端 5 分钟快速上手](docs/zh-CN/frontend-quick-start.md)
- [后端快速上手指南](docs/zh-CN/quick-start.md)
- [完整使用示例](EXAMPLES.php)

## 测试

```bash
composer test
```

## 更新日志

请查看 [CHANGELOG](CHANGELOG.md) 了解最近的变更。

## 贡献

欢迎贡献代码！请查看 [CONTRIBUTING](CONTRIBUTING.md) 了解详情。

## 安全

如果你发现任何安全相关的问题，请发送邮件至 ruihuachen@qq.com，而不是使用 issue 追踪器。

## 致谢

- [Ruihua](https://github.com/WeiJuKeJi)
- [所有贡献者](../../contributors)

## 许可证

MIT 许可证。详情请查看 [License File](LICENSE.md)。
