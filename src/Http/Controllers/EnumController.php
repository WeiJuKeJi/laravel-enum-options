<?php

namespace WeiJuKeJi\EnumOptions\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use WeiJuKeJi\EnumOptions\Presets\Business\ApprovalStatusEnum;
use WeiJuKeJi\EnumOptions\Presets\Business\PublishStatusEnum;
use WeiJuKeJi\EnumOptions\Presets\Order\OrderStatusEnum;
use WeiJuKeJi\EnumOptions\Presets\Order\OrderTypeEnum;
use WeiJuKeJi\EnumOptions\Presets\Payment\PaymentMethodEnum;
use WeiJuKeJi\EnumOptions\Presets\Payment\PaymentStatusEnum;
use WeiJuKeJi\EnumOptions\Presets\Payment\RefundStatusEnum;
use WeiJuKeJi\EnumOptions\Presets\User\GenderEnum;
use WeiJuKeJi\EnumOptions\Presets\User\UserStatusEnum;

/**
 * 枚举选项控制器
 * 为前端提供各种枚举类型的下拉选项
 */
class EnumController extends Controller
{
    /**
     * 获取支付方式选项
     */
    public function paymentMethods(): JsonResponse
    {
        return $this->respond(PaymentMethodEnum::options());
    }

    /**
     * 获取支付状态选项
     */
    public function paymentStatuses(): JsonResponse
    {
        return $this->respond(PaymentStatusEnum::options());
    }

    /**
     * 获取退款状态选项
     */
    public function refundStatuses(): JsonResponse
    {
        return $this->respond(RefundStatusEnum::options());
    }

    /**
     * 获取订单状态选项
     */
    public function orderStatuses(): JsonResponse
    {
        return $this->respond(OrderStatusEnum::options());
    }

    /**
     * 获取订单类型选项
     */
    public function orderTypes(): JsonResponse
    {
        return $this->respond(OrderTypeEnum::options());
    }

    /**
     * 获取用户状态选项
     */
    public function userStatuses(): JsonResponse
    {
        return $this->respond(UserStatusEnum::options());
    }

    /**
     * 获取性别选项
     */
    public function genders(): JsonResponse
    {
        return $this->respond(GenderEnum::options());
    }

    /**
     * 获取审批状态选项
     */
    public function approvalStatuses(): JsonResponse
    {
        return $this->respond(ApprovalStatusEnum::options());
    }

    /**
     * 获取发布状态选项
     */
    public function publishStatuses(): JsonResponse
    {
        return $this->respond(PublishStatusEnum::options());
    }

    /**
     * 获取所有枚举选项（一次性返回）
     */
    public function all(): JsonResponse
    {
        return $this->respond([
            'payment_methods' => PaymentMethodEnum::options(),
            'payment_statuses' => PaymentStatusEnum::options(),
            'refund_statuses' => RefundStatusEnum::options(),
            'order_statuses' => OrderStatusEnum::options(),
            'order_types' => OrderTypeEnum::options(),
            'user_statuses' => UserStatusEnum::options(),
            'genders' => GenderEnum::options(),
            'approval_statuses' => ApprovalStatusEnum::options(),
            'publish_statuses' => PublishStatusEnum::options(),
        ]);
    }

    /**
     * 统一响应格式
     */
    protected function respond($data): JsonResponse
    {
        $format = config('enum-options.response_format', [
            'code_key' => 'code',
            'message_key' => 'msg',
            'data_key' => 'data',
        ]);

        return response()->json([
            $format['code_key'] => 200,
            $format['message_key'] => 'success',
            $format['data_key'] => $data,
        ]);
    }
}
