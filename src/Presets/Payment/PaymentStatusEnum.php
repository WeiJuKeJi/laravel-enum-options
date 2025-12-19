<?php

namespace WeiJuKeJi\EnumOptions\Presets\Payment;

use WeiJuKeJi\EnumOptions\Traits\EnumOptions;

/**
 * 支付状态枚举
 */
enum PaymentStatusEnum: string
{
    use EnumOptions;

    case UNPAID = 'unpaid';
    case PENDING = 'pending';
    case PAYING = 'paying';
    case PAID = 'paid';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
    case REFUNDING = 'refunding';
    case REFUNDED = 'refunded';
    case PARTIALLY_REFUNDED = 'partially_refunded';
    case TIMEOUT = 'timeout';

    public function label(): string
    {
        return $this->trans($this->value, match ($this) {
            self::UNPAID => '未支付',
            self::PENDING => '待支付',
            self::PAYING => '支付中',
            self::PAID => '已支付',
            self::FAILED => '支付失败',
            self::CANCELLED => '已取消',
            self::REFUNDING => '退款中',
            self::REFUNDED => '已退款',
            self::PARTIALLY_REFUNDED => '部分退款',
            self::TIMEOUT => '支付超时',
        });
    }

    public function color(): string
    {
        $configColor = config("enum-options.color_overrides.payment_status.{$this->value}");
        if ($configColor) {
            return $configColor;
        }

        return match ($this) {
            self::PAID => 'success',                           // 已支付 - 成功完成
            self::PAYING => 'primary',                         // 支付中 - 进行中
            self::UNPAID, self::PENDING => 'warning',          // 未支付/待支付 - 需要处理
            self::FAILED, self::TIMEOUT => 'danger',           // 失败/超时 - 错误状态
            self::CANCELLED => 'info',                         // 已取消 - 中性
            self::REFUNDING => 'primary',                      // 退款中 - 进行中
            self::REFUNDED, self::PARTIALLY_REFUNDED => 'danger', // 已退款 - 负向状态
        };
    }

    public function icon(): ?string
    {
        return match ($this) {
            self::UNPAID => 'wallet-line',
            self::PENDING => 'time-line',
            self::PAYING => 'loader-line',
            self::PAID => 'checkbox-circle-fill',
            self::FAILED => 'close-circle-fill',
            self::CANCELLED => 'forbid-fill',
            self::REFUNDING => 'refund-line',
            self::REFUNDED => 'refund-fill',
            self::PARTIALLY_REFUNDED => 'refund-2-fill',
            self::TIMEOUT => 'hourglass-fill',
        };
    }
}
