# Laravel Enum Options - 快速上手指南

## 简介

这是一个为 Laravel 项目设计的枚举扩展包，专门解决前端需要枚举值的中文标签、颜色、图标等需求。

## 核心特性

- ✅ **开箱即用** - 内置 10+ 常用枚举类
- ✅ **中文友好** - 内置中英文翻译
- ✅ **前端友好** - 自动返回 `{value, label, color, icon}` 格式
- ✅ **高度灵活** - 直接使用、发布定制、完全自定义三种方式
- ✅ **配置覆盖** - 无需修改代码即可定制标签和颜色

## 安装

```bash
composer require weijukeji/laravel-enum-options
```

## 三种使用方式

### 方式一: 直接使用预设枚举（最快）

```php
use WeiJuKeJi\EnumOptions\Presets\Payment\PaymentMethodEnum;

$method = PaymentMethodEnum::WECHAT;
echo $method->label();  // 微信支付
echo $method->color();  // green
```

**适用场景**: 预设枚举完全符合需求，不需要修改

### 方式二: 发布后定制（推荐）

```bash
php artisan enum:publish PaymentMethod
```

生成到 `app/Enums/PaymentMethodEnum.php`，可以自由修改添加值。

**适用场景**: 需要在预设基础上添加自定义值

### 方式三: 完全自定义

```bash
php artisan make:enum SubscriptionStatus --values=active,paused,cancelled --labels
```

**适用场景**: 需要全新的枚举类型

## 快速开始

### 1. 查看可用的预设

```bash
php artisan enum:list-presets
```

输出:
```
Payment
  - PaymentMethod (13 values)
  - PaymentStatus (10 values)
  - RefundStatus (7 values)
Order
  - OrderStatus (10 values)
  - OrderType (7 values)
User
  - UserStatus (6 values)
  - Gender (4 values)
Business
  - ApprovalStatus (6 values)
  - PublishStatus (5 values)
```

### 2. 查看预设详情

```bash
php artisan enum:list-presets PaymentMethod
```

### 3. 在 Model 中使用

```php
namespace App\Models;

use App\Enums\OrderStatusEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Order extends Model
{
    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => OrderStatusEnum::fromValue($value),
        );
    }
}

// 使用
$order = Order::find(1);
echo $order->status->label();  // 已支付
```

### 4. 在 API Resource 中使用

```php
class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'status' => OrderStatusEnum::fromValue($this->status)?->toArray(),
        ];
    }
}
```

API 返回:
```json
{
  "id": 1,
  "status": {
    "value": "paid",
    "label": "已支付",
    "color": "green"
  }
}
```

### 5. 提供下拉选项接口

```php
class EnumController extends Controller
{
    public function paymentMethods()
    {
        return response()->json([
            'code' => 200,
            'msg' => 'success',
            'data' => PaymentMethodEnum::options(),
        ]);
    }
}
```

返回:
```json
{
  "code": 200,
  "msg": "success",
  "data": [
    {"value": "wechat", "label": "微信支付", "color": "green", "icon": "wechat"},
    {"value": "alipay", "label": "支付宝", "color": "blue", "icon": "alipay"}
  ]
}
```

## 配置定制

发布配置文件:
```bash
php artisan vendor:publish --tag=enum-options-config
```

### 启用自动路由（可选）

如果需要对外提供枚举选项 API 接口:

```php
// config/enum-options.php
'auto_register_routes' => true,
'route_prefix' => 'api/v1/enums',
'route_middleware' => ['auth:sanctum'],
'route_name_prefix' => 'enums',
```

启用后，自动注册以下接口:

```bash
GET /api/v1/enums/all                    # 获取所有枚举（推荐）
GET /api/v1/enums/payment-methods        # 支付方式
GET /api/v1/enums/payment-statuses       # 支付状态
GET /api/v1/enums/refund-statuses        # 退款状态
GET /api/v1/enums/order-statuses         # 订单状态
GET /api/v1/enums/order-types            # 订单类型
GET /api/v1/enums/user-statuses          # 用户状态
GET /api/v1/enums/genders                # 性别
GET /api/v1/enums/approval-statuses      # 审批状态
GET /api/v1/enums/publish-statuses       # 发布状态
```

响应格式:
```json
{
  "code": 200,
  "msg": "success",
  "data": [
    {"value": "wechat", "label": "微信支付", "color": "green", "icon": "wechat"}
  ]
}
```

### 手动注册路由

如果不想自动注册，可以手动控制:

```php
// routes/api.php
use WeiJuKeJi\EnumOptions\Http\Controllers\EnumController;

Route::prefix('enums')->middleware('auth:sanctum')->group(function () {
    Route::get('all', [EnumController::class, 'all']);
    Route::get('payment-methods', [EnumController::class, 'paymentMethods']);
});
```

或创建自己的控制器:

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

### 覆盖标签

```php
// config/enum-options.php
'label_overrides' => [
    'payment_method' => [
        'wechat' => '微信',     // 简化
        'pos' => 'POS刷卡',     // 自定义术语
    ],
],
```

### 覆盖颜色

```php
'color_overrides' => [
    'payment_method' => [
        'wechat' => 'success',
        'alipay' => 'primary',
    ],
],
```

## 前端集成

### Vue 3 + Element Plus

```vue
<template>
  <!-- 显示状态标签 -->
  <el-tag :type="order.status.color">
    {{ order.status.label }}
  </el-tag>

  <!-- 下拉选择 -->
  <el-select v-model="form.payment_method">
    <el-option
      v-for="method in paymentMethods"
      :key="method.value"
      :value="method.value"
      :label="method.label"
    />
  </el-select>
</template>

<script setup>
const { data } = await axios.get('/api/v1/enums/payment-methods')
const paymentMethods = data.data
</script>
```

### React + Ant Design

```jsx
import { Badge, Select } from 'antd'

function OrderList() {
  const [enums, setEnums] = useState({})

  useEffect(() => {
    fetch('/api/v1/enums/all')
      .then(res => res.json())
      .then(data => setEnums(data.data))
  }, [])

  return (
    <>
      <Badge color={order.status.color}>
        {order.status.label}
      </Badge>

      <Select>
        {enums.payment_methods?.map(m => (
          <Select.Option key={m.value} value={m.value}>
            {m.label}
          </Select.Option>
        ))}
      </Select>
    </>
  )
}
```

## 常见问题

### Q: 如何添加新的枚举值？

**方式1**: 发布到项目后直接修改
```bash
php artisan enum:publish PaymentMethod
# 编辑 app/Enums/PaymentMethodEnum.php
```

**方式2**: 使用配置覆盖（仅覆盖标签和颜色）

### Q: 如何修改枚举的翻译？

创建翻译文件:
```php
// lang/zh-CN/enums.php
return [
    'payment_method' => [
        'wechat' => '微信',  // 覆盖默认的"微信支付"
    ],
];
```

### Q: 如何在不同环境使用不同的标签？

使用环境变量 + 配置:
```php
// config/enum-options.php
'label_overrides' => [
    'payment_method' => [
        'wechat' => env('WECHAT_LABEL', '微信支付'),
    ],
],
```

### Q: 前端如何缓存枚举选项？

**推荐做法**: 应用启动时一次性获取所有枚举并存储到全局状态

```javascript
// Vue 3 示例
import { createApp } from 'vue'
import axios from 'axios'

const app = createApp(App)

// 启动时获取所有枚举
const { data } = await axios.get('/api/v1/enums/all')
app.config.globalProperties.$enums = data.data

app.mount('#app')
```

## 更多示例

查看 `EXAMPLES.php` 文件获取更多使用示例。

## 支持

- 文档: [README.md](README.md)
- 问题: [GitHub Issues](https://github.com/weijukeji/laravel-enum-options/issues)
