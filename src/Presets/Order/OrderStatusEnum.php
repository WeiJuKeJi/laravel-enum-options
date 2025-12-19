<?php

namespace WeiJuKeJi\EnumOptions\Presets\Order;

use WeiJuKeJi\EnumOptions\Traits\EnumOptions;

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
            self::COMPLETED => 'success',                       // 已完成 - 成功
            self::CONFIRMED, self::PROCESSING => 'primary',     // 已确认/处理中 - 进行中
            self::PENDING => 'warning',                         // 待处理 - 需要注意
            self::FAILED, self::REFUNDED, self::PARTIALLY_REFUNDED => 'danger', // 失败/退款 - 错误
            self::CANCELLED, self::EXPIRED, self::ON_HOLD => 'info', // 取消/过期/暂停 - 中性
        };
    }

    public function icon(): ?string
    {
        return match ($this) {
            self::PENDING => 'file-list-line',
            self::CONFIRMED => 'checkbox-circle-line',
            self::PROCESSING => 'loader-line',
            self::COMPLETED => 'checkbox-circle-fill',
            self::CANCELLED => 'close-circle-fill',
            self::EXPIRED => 'hourglass-fill',
            self::FAILED => 'error-warning-fill',
            self::ON_HOLD => 'pause-circle-fill',
            self::REFUNDED => 'refund-fill',
            self::PARTIALLY_REFUNDED => 'refund-2-fill',
        };
    }
}
