<?php

/*
|--------------------------------------------------------------------------
| Enum Options API Routes
|--------------------------------------------------------------------------
|
| 这些路由由 EnumOptionsServiceProvider 自动注册（当 auto_register_routes = true 时）
| 无需手动维护此文件，路由会根据 EnumRegistry 自动生成
|
| 所有路由都使用动态控制器方法，支持任何已注册的枚举类
|
*/

/*
|--------------------------------------------------------------------------
| 自动注册的路由示例
|--------------------------------------------------------------------------
|
| 以下路由会自动生成（假设配置 route_prefix = 'api/enums'）：
|
| GET /api/enums/list                   - 获取所有枚举的元数据（名称、描述、路由等）
| GET /api/enums/all                    - 一次性获取所有枚举的选项
|
| 动态生成的枚举路由（根据 EnumRegistry 自动发现）：
| GET /api/enums/payment-methods        - 支付方式
| GET /api/enums/payment-statuses       - 支付状态
| GET /api/enums/refund-statuses        - 退款状态
| GET /api/enums/order-statuses         - 订单状态
| GET /api/enums/order-types            - 订单类型
| GET /api/enums/user-statuses          - 用户状态
| GET /api/enums/genders                - 性别
| GET /api/enums/approval-statuses      - 审批状态
| GET /api/enums/publish-statuses       - 发布状态
| GET /api/enums/{your-custom-enum}     - 你的自定义枚举（自动生成）
|
| 所有路由都通过 EnumController::show() 方法处理
| 添加新枚举后，路由会自动生成，无需修改此文件
|
*/

/*
|--------------------------------------------------------------------------
| 手动注册路由（可选）
|--------------------------------------------------------------------------
|
| 如果你将 auto_register_routes 设置为 false，可以在这里手动注册路由：
|
| // 固定路由
| Route::get('list', [EnumController::class, 'list']);
| Route::get('all', [EnumController::class, 'all']);
|
| // 动态路由（推荐）
| foreach (\WeiJuKeJi\EnumOptions\Support\EnumRegistry::all() as $key => $config) {
|     Route::get(\Illuminate\Support\Str::kebab($key), [EnumController::class, 'show'])
|         ->defaults('key', $key);
| }
|
| // 或者为特定枚举手动注册
| Route::get('payment-methods', [EnumController::class, 'show'])
|     ->defaults('key', 'payment_methods');
|
*/
