# Laravel Enum Options API 文档索引

本目录包含 Laravel Enum Options 扩展包的所有 API 接口文档（OpenAPI 3.0.3 格式）。

## 如何使用

1. 使用 Apifox 导入对应的 JSON 文件
2. 根据下表快速定位控制器代码位置
3. 参考控制器方法表了解接口实现细节

---

## API 文档列表

| API 文档 | 对应控制器 | 说明 |
|---------|-----------|------|
| [枚举选项.json](./枚举选项.json) | EnumController | 枚举选项相关接口，提供各类业务枚举数据 |

---

## 控制器详情

### EnumController

**文件路径**: `src/Http/Controllers/EnumController.php`
**API 文档**: [枚举选项.json](./枚举选项.json)

| 控制器方法 | HTTP 方法 | 路由 | 接口说明 | operationId |
|-----------|----------|------|---------|-------------|
| list() | GET | /enums/list | 获取所有可用的枚举项目列表 | getEnumList |
| all() | GET | /enums/all | 获取所有枚举选项 | getAllEnums |
| paymentMethods() | GET | /enums/payment-methods | 获取支付方式选项 | getPaymentMethods |
| paymentStatuses() | GET | /enums/payment-statuses | 获取支付状态选项 | getPaymentStatuses |
| refundStatuses() | GET | /enums/refund-statuses | 获取退款状态选项 | getRefundStatuses |
| orderStatuses() | GET | /enums/order-statuses | 获取订单状态选项 | getOrderStatuses |
| orderTypes() | GET | /enums/order-types | 获取订单类型选项 | getOrderTypes |
| userStatuses() | GET | /enums/user-statuses | 获取用户状态选项 | getUserStatuses |
| genders() | GET | /enums/genders | 获取性别选项 | getGenders |
| approvalStatuses() | GET | /enums/approval-statuses | 获取审批状态选项 | getApprovalStatuses |
| publishStatuses() | GET | /enums/publish-statuses | 获取发布状态选项 | getPublishStatuses |

---

## 接口使用场景

### 1. GET /enums/list - 获取枚举项目列表

**使用场景：**
- 前端应用初始化时，动态获取系统中所有可用的枚举类型
- 管理后台展示系统枚举配置信息
- 开发工具或调试工具查看可用的枚举API
- 动态生成枚举选择器或配置界面

**典型用法：**
```javascript
// 1. 获取枚举列表
const { data } = await axios.get('/api/enums/list')

// 2. 按分类分组展示
const enumsByCategory = data.data.reduce((acc, item) => {
  if (!acc[item.category]) acc[item.category] = []
  acc[item.category].push(item)
  return acc
}, {})

// 3. 动态加载需要的枚举数据
for (const enumItem of data.data) {
  const enumData = await axios.get(`/api${enumItem.route}`)
  // 使用 enumData
}
```

### 2. GET /enums/all - 获取所有枚举选项

**使用场景：**
- 前端应用启动时一次性加载所有枚举数据（推荐）
- 需要多个枚举数据时，减少HTTP请求次数
- 适合中小型应用，一次加载全部枚举

### 3. 其他单个枚举接口

**使用场景：**
- 按需加载特定枚举数据
- 大型应用中避免一次加载过多数据
- 特定页面或功能需要特定枚举时使用

---

## 接口说明

### 路由前缀

所有接口的路由前缀为 `api/enums`，可在配置文件中自定义：

```php
// config/enum-options.php
'route_prefix' => 'api/enums',
```

### 认证方式

所有接口均需要 Bearer Token 认证，使用 `auth:sanctum` 中间件保护。

可在配置文件中自定义中间件：

```php
// config/enum-options.php
'route_middleware' => ['auth:sanctum'],
```

### 响应格式

#### 1. 枚举项目列表响应格式（/enums/list）

返回所有可用枚举的元数据，**符合接口返回规范**：

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

字段说明：
- `list`: 枚举元数据列表
- `total`: 枚举总数
- `key`: 枚举的唯一标识
- `name`: 枚举的显示名称
- `description`: 枚举的描述
- `route`: 获取该枚举选项数据的路由
- `count`: 该枚举包含的选项数量
- `category`: 枚举分类（payment/order/user/business）

#### 2. 枚举选项数据响应格式（单个枚举接口）

所有单个枚举接口返回统一的列表格式，**符合接口返回规范**：

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

字段说明：
- `list`: 枚举选项列表
- `total`: 选项总数
- `value`: 枚举值
- `label`: 显示标签
- `color`: 颜色标识
- `icon`: 图标名称（可选）

#### 3. 所有枚举选项响应格式（/enums/all）

一次性获取所有枚举的选项数据，返回对象格式：

```json
{
  "code": 200,
  "msg": "success",
  "data": [
    {
      "value": "wechat",
      "label": "微信支付",
      "color": "green",
      "icon": "wechat"
    }
  ]
}
```

响应格式可在配置文件中自定义：

```php
// config/enum-options.php
'response_format' => [
    'code_key' => 'code',
    'message_key' => 'msg',
    'data_key' => 'data',
],
```

### 启用自动路由

在配置文件中启用自动路由注册：

```php
// config/enum-options.php
'auto_register_routes' => true,
```

或在 `routes/api.php` 中手动注册路由：

```php
use WeiJuKeJi\EnumOptions\Http\Controllers\EnumController;

Route::prefix('enums')->middleware('auth:sanctum')->group(function () {
    Route::get('all', [EnumController::class, 'all']);
    Route::get('payment-methods', [EnumController::class, 'paymentMethods']);
    // ... 其他路由
});
```

---

## 预设枚举说明

### 支付相关
- **PaymentMethodEnum**: 13 种支付方式（微信、支付宝、银行转账、现金、信用卡、借记卡、银联、PayPal、Apple Pay、Google Pay、POS 机、微信 POS、其他）
- **PaymentStatusEnum**: 10 种支付状态（未支付、待支付、支付中、已支付、支付失败、已取消、退款中、已退款、部分退款、超时）
- **RefundStatusEnum**: 7 种退款状态（无退款、待处理、处理中、部分退款、全额退款、退款失败、已拒绝）

### 订单相关
- **OrderStatusEnum**: 10 种订单状态（待确认、已确认、处理中、已完成、已取消、已过期、失败、暂停、已退款、部分退款）
- **OrderTypeEnum**: 7 种订单类型（标准订单、预售订单、团购订单、秒杀订单、订阅订单、礼品订单、换货订单）

### 用户相关
- **UserStatusEnum**: 6 种用户状态（活跃、未激活、已暂停、已禁用、已删除、待验证）
- **GenderEnum**: 4 种性别选项（男、女、其他、不愿透露）

### 业务流程
- **ApprovalStatusEnum**: 6 种审批状态（草稿、待审批、已批准、已拒绝、已取消、已撤回）
- **PublishStatusEnum**: 5 种发布状态（草稿、定时发布、已发布、未发布、已归档）

---

## 版本历史

| 版本 | 日期 | 说明 |
|-----|------|------|
| 1.1.0 | 2025-12-19 | 新增 GET /enums/list 接口，用于获取所有可用的枚举项目列表 |
| 1.0.0 | 2025-12-19 | 初始版本，包含 10 个枚举选项接口 |
