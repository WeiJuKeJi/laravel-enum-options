<?php

use Illuminate\Support\Facades\Route;
use WeiJuKeJi\EnumOptions\Http\Controllers\EnumController;

/*
|--------------------------------------------------------------------------
| Enum Options API Routes
|--------------------------------------------------------------------------
|
| 这些路由提供枚举选项的 API 接口，用于前端下拉框等场景
| 默认不自动注册，需要在配置中启用 auto_register_routes
|
*/

// 获取所有可用的枚举项目列表（元数据）
Route::get('list', [EnumController::class, 'list'])->name('list');

// 获取所有枚举选项（推荐使用，一次性获取）
Route::get('all', [EnumController::class, 'all'])->name('all');

// 支付相关
Route::get('payment-methods', [EnumController::class, 'paymentMethods'])->name('payment_methods');
Route::get('payment-statuses', [EnumController::class, 'paymentStatuses'])->name('payment_statuses');
Route::get('refund-statuses', [EnumController::class, 'refundStatuses'])->name('refund_statuses');

// 订单相关
Route::get('order-statuses', [EnumController::class, 'orderStatuses'])->name('order_statuses');
Route::get('order-types', [EnumController::class, 'orderTypes'])->name('order_types');

// 用户相关
Route::get('user-statuses', [EnumController::class, 'userStatuses'])->name('user_statuses');
Route::get('genders', [EnumController::class, 'genders'])->name('genders');

// 业务相关
Route::get('approval-statuses', [EnumController::class, 'approvalStatuses'])->name('approval_statuses');
Route::get('publish-statuses', [EnumController::class, 'publishStatuses'])->name('publish_statuses');
