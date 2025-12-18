<?php

/**
 * Laravel Enum Options - 使用示例
 *
 * 这个文件展示了如何使用 laravel-enum-options 扩展包
 */

// ==================== 1. 使用预设枚举 ====================

use WeiJuKeJi\EnumOptions\Presets\Payment\PaymentMethodEnum;
use WeiJuKeJi\EnumOptions\Presets\Order\OrderStatusEnum;

// 获取单个枚举值
$wechat = PaymentMethodEnum::WECHAT;
echo $wechat->value;    // 'wechat'
echo $wechat->label();  // '微信支付'
echo $wechat->color();  // 'green'
echo $wechat->icon();   // 'wechat'

// 转换为数组（用于 API 响应）
$array = $wechat->toArray();
// [
//     'value' => 'wechat',
//     'label' => '微信支付',
//     'color' => 'green',
//     'icon' => 'wechat'
// ]

// 获取所有选项（用于下拉框）
$options = PaymentMethodEnum::options();
// [
//     ['value' => 'wechat', 'label' => '微信支付', 'color' => 'green', 'icon' => 'wechat'],
//     ['value' => 'alipay', 'label' => '支付宝', 'color' => 'blue', 'icon' => 'alipay'],
//     ...
// ]

// 从字符串值安全创建枚举
$method = PaymentMethodEnum::fromValue('wechat');  // PaymentMethodEnum::WECHAT
$invalid = PaymentMethodEnum::fromValue('invalid'); // null
$nullable = PaymentMethodEnum::fromValue(null);     // null

// 验证值是否有效
if (PaymentMethodEnum::isValid('wechat')) {
    // 有效的支付方式
}

// 获取所有值
$values = PaymentMethodEnum::values();
// ['wechat', 'alipay', 'bank_transfer', ...]

// 获取所有标签
$labels = PaymentMethodEnum::labels();
// ['wechat' => '微信支付', 'alipay' => '支付宝', ...]

// ==================== 2. 在 Model 中使用 ====================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\OrderStatusEnum;
use App\Enums\PaymentMethodEnum;

class Order extends Model
{
    protected $fillable = [
        'order_no',
        'status',
        'payment_method',
        // ...
    ];

    // 使用 Accessor 自动转换
    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => OrderStatusEnum::fromValue($value),
            set: fn ($value) => $value instanceof OrderStatusEnum ? $value->value : $value,
        );
    }

    protected function paymentMethod(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => PaymentMethodEnum::fromValue($value),
            set: fn ($value) => $value instanceof PaymentMethodEnum ? $value->value : $value,
        );
    }
}

// 使用
$order = Order::find(1);
echo $order->status->label();          // '已支付'
echo $order->payment_method->color();  // 'green'

// 设置枚举值
$order->status = OrderStatusEnum::COMPLETED;
$order->save();

// ==================== 3. 在 API Resource 中使用 ====================

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\OrderStatusEnum;
use App\Enums\PaymentMethodEnum;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'order_no' => $this->order_no,
            'amount' => $this->amount,

            // 自动转换为包含 value, label, color 的对象
            'status' => OrderStatusEnum::fromValue($this->status)?->toArray(),
            'payment_method' => PaymentMethodEnum::fromValue($this->payment_method)?->toArray(),

            'created_at' => $this->created_at,
        ];
    }
}

// API 响应示例:
// {
//     "id": 1,
//     "order_no": "ORD20250118001",
//     "amount": "99.00",
//     "status": {
//         "value": "paid",
//         "label": "已支付",
//         "color": "green"
//     },
//     "payment_method": {
//         "value": "wechat",
//         "label": "微信支付",
//         "color": "green",
//         "icon": "wechat"
//     },
//     "created_at": "2025-01-18 10:30:00"
// }

// ==================== 4. 在 Controller 中使用 ====================

// 方式 1: 启用自动路由（推荐）
// config/enum-options.php
/*
'auto_register_routes' => true,
'route_prefix' => 'api/v1/enums',
'route_middleware' => ['auth:sanctum'],
'route_name_prefix' => 'enums',
*/

// 自动注册以下接口:
// GET /api/v1/enums/all
// GET /api/v1/enums/payment-methods
// GET /api/v1/enums/payment-statuses
// ... 等等

// 方式 2: 使用内置控制器手动注册
// routes/api.php
use WeiJuKeJi\EnumOptions\Http\Controllers\EnumController;

Route::prefix('enums')->middleware('auth:sanctum')->name('enums.')->group(function () {
    Route::get('all', [EnumController::class, 'all'])->name('all');
    Route::get('payment-methods', [EnumController::class, 'paymentMethods'])->name('payment_methods');
    Route::get('payment-statuses', [EnumController::class, 'paymentStatuses'])->name('payment_statuses');
    // ...
});

// 方式 3: 创建自己的控制器
namespace App\Http\Controllers;

use App\Enums\PaymentMethodEnum;
use Illuminate\Http\JsonResponse;

class EnumController extends Controller
{
    /**
     * 获取支付方式选项（用于前端下拉框）
     */
    public function paymentMethods(): JsonResponse
    {
        return response()->json([
            'code' => 200,
            'msg' => 'success',
            'data' => PaymentMethodEnum::options(),
        ]);
    }

    /**
     * 获取所有枚举选项
     */
    public function all(): JsonResponse
    {
        return response()->json([
            'code' => 200,
            'msg' => 'success',
            'data' => [
                'payment_methods' => PaymentMethodEnum::options(),
                'order_statuses' => OrderStatusEnum::options(),
                // ...
            ],
        ]);
    }
}

// ==================== 5. 在 Validation 中使用 ====================

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\PaymentMethodEnum;

class CreateOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'payment_method' => [
                'required',
                Rule::in(PaymentMethodEnum::values()),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.in' => '请选择有效的支付方式',
        ];
    }
}

// ==================== 6. 配置覆盖 ====================

// config/enum-options.php

return [
    // 覆盖标签（无需修改枚举类）
    'label_overrides' => [
        'payment_method' => [
            'wechat' => '微信',      // 简化标签
            'pos' => 'POS刷卡',      // 自定义术语
        ],
    ],

    // 覆盖颜色
    'color_overrides' => [
        'payment_method' => [
            'wechat' => 'success',   // Element Plus 风格
            'alipay' => 'primary',
        ],
    ],

    // 选择颜色方案
    'color_scheme' => 'element-plus',  // 或 'ant-design', 'tailwind'
];

// ==================== 7. 多语言支持 ====================

// lang/zh-CN/enums.php
return [
    'payment_method' => [
        'wechat' => '微信支付',
        'alipay' => '支付宝',
    ],
];

// lang/en/enums.php
return [
    'payment_method' => [
        'wechat' => 'WeChat Pay',
        'alipay' => 'Alipay',
    ],
];

// 翻译优先级:
// 1. 用户自定义翻译 (lang/{locale}/enums.php)
// 2. 配置覆盖 (config/enum-options.php)
// 3. 扩展包预设翻译 (lang/{locale}/presets.php)
// 4. 枚举类默认值

// ==================== 8. Artisan 命令 ====================

// 列出所有预设枚举
// php artisan enum:list-presets

// 查看预设详情
// php artisan enum:list-presets PaymentMethod

// 发布预设到项目
// php artisan enum:publish PaymentMethod
// php artisan enum:publish --all
// php artisan enum:publish PaymentMethod --with-translations

// 创建自定义枚举
// php artisan make:enum SubscriptionStatus --values=active,paused,cancelled --labels

// ==================== 9. 前端集成示例 ====================

/**
 * Vue 3 示例
 */
/*
<template>
  <!-- 显示带颜色的标签 -->
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
    >
      <i :class="`icon-${method.icon}`" />
      {{ method.label }}
    </el-option>
  </el-select>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const paymentMethods = ref([])

onMounted(async () => {
  const { data } = await axios.get('/api/v1/enums/payment-methods')
  paymentMethods.value = data.data
})
</script>
*/

/**
 * React 示例
 */
/*
import { useEffect, useState } from 'react'
import { Select, Badge } from 'antd'

function OrderForm() {
  const [enums, setEnums] = useState({})

  useEffect(() => {
    fetch('/api/v1/enums/all')
      .then(res => res.json())
      .then(data => setEnums(data.data))
  }, [])

  return (
    <div>
      <Badge color={order.status.color}>
        {order.status.label}
      </Badge>

      <Select>
        {enums.payment_methods?.map(method => (
          <Select.Option key={method.value} value={method.value}>
            {method.label}
          </Select.Option>
        ))}
      </Select>
    </div>
  )
}
*/
