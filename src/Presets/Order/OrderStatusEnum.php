<?php

namespace YourVendor\EnumOptions\Presets\Order;

use YourVendor\EnumOptions\Traits\EnumOptions;

/**
 * 订单状态枚举
 */
enum OrderStatusEnum: string
{
    use EnumOptions;

    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case EXPIRED = 'expired';
    case FAILED = 'failed';
    case ON_HOLD = 'on_hold';
    case REFUNDED = 'refunded';
    case PARTIALLY_REFUNDED = 'partially_refunded';

    public function label(): string
    {
        return $this->trans($this->value, match ($this) {
            self::PENDING => '待处理',
            self::CONFIRMED => '已确认',
            self::PROCESSING => '处理中',
            self::COMPLETED => '已完成',
            self::CANCELLED => '已取消',
            self::EXPIRED => '已过期',
            self::FAILED => '失败',
            self::ON_HOLD => '暂停',
            self::REFUNDED => '已退款',
            self::PARTIALLY_REFUNDED => '部分退款',
        });
    }

    public function color(): string
    {
        $configColor = config("enum-options.color_overrides.order_status.{$this->value}");
        if ($configColor) {
            return $configColor;
        }

        return match ($this) {
            self::PENDING => 'orange',
            self::CONFIRMED => 'blue',
            self::PROCESSING => 'cyan',
            self::COMPLETED => 'green',
            self::CANCELLED, self::EXPIRED => 'gray',
            self::FAILED => 'red',
            self::ON_HOLD => 'purple',
            self::REFUNDED, self::PARTIALLY_REFUNDED => 'red',
        };
    }
}
