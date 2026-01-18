<?php

namespace WeiJuKeJi\EnumOptions\Presets\Order;

use WeiJuKeJi\EnumOptions\Traits\EnumOptions;

/**
 * 订单状态枚举
 */
enum OrderStatusEnum: string
{
    use EnumOptions;

    case UNPAID = 'unpaid';
    case PENDING_USE = 'pending_use';
    case PARTIALLY_USED = 'partially_used';
    case PARTIALLY_REFUNDED = 'partially_refunded';
    case USED = 'used';
    case REFUNDED = 'refunded';
    case CANCELLED = 'cancelled';
    case CLOSED = 'closed';
    case COMPLETED = 'completed';

    /**
     * 获取枚举的中文显示名称
     */
    public static function displayName(): string
    {
        return '订单状态';
    }

    public function label(): string
    {
        return match ($this) {
            self::UNPAID => '未支付',
            self::PENDING_USE => '待使用',
            self::PARTIALLY_USED => '部分使用',
            self::PARTIALLY_REFUNDED => '部分退款',
            self::USED => '已使用',
            self::REFUNDED => '已退款',
            self::CANCELLED => '已取消',
            self::CLOSED => '已关闭',
            self::COMPLETED => '已完成',
        };
    }

    public function color(): string
    {
        $configColor = config("enum-options.color_overrides.order_status.{$this->value}");
        if ($configColor) {
            return $configColor;
        }

        return match ($this) {
            self::COMPLETED => 'success',                        // 已完成 - 成功
            self::USED => 'success',                             // 已使用 - 成功
            self::PENDING_USE => 'primary',                      // 待使用 - 进行中
            self::UNPAID => 'warning',                           // 未支付 - 需要注意
            self::PARTIALLY_USED => 'warning',                   // 部分使用 - 需要注意
            self::REFUNDED, self::PARTIALLY_REFUNDED => 'danger', // 退款 - 错误
            self::CANCELLED, self::CLOSED => 'info',             // 取消/关闭 - 中性
        };
    }

    public function icon(): ?string
    {
        return match ($this) {
            self::UNPAID => 'money-cny-circle-line',
            self::PENDING_USE => 'ticket-2-line',
            self::PARTIALLY_USED => 'file-list-2-line',
            self::PARTIALLY_REFUNDED => 'refund-2-fill',
            self::USED => 'checkbox-circle-fill',
            self::REFUNDED => 'refund-fill',
            self::CANCELLED => 'close-circle-fill',
            self::CLOSED => 'lock-fill',
            self::COMPLETED => 'check-double-fill',
        };
    }
}
