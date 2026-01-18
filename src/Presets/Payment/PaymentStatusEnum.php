<?php

namespace WeiJuKeJi\EnumOptions\Presets\Payment;

use WeiJuKeJi\EnumOptions\Traits\EnumOptions;

/**
 * 支付状态枚举
 */
enum PaymentStatusEnum: string
{
    use EnumOptions;

    case PAID = 'paid';
    case UNPAID = 'unpaid';

    /**
     * 获取枚举的中文显示名称
     */
    public static function displayName(): string
    {
        return '支付状态';
    }

    public function label(): string
    {
        return match ($this) {
            self::PAID => '已支付',
            self::UNPAID => '未支付',
        };
    }

    public function color(): string
    {
        $configColor = config("enum-options.color_overrides.payment_status.{$this->value}");
        if ($configColor) {
            return $configColor;
        }

        return match ($this) {
            self::PAID => 'success',                           // 已支付 - 成功完成
            self::UNPAID => 'warning',                         // 未支付 - 需要处理
        };
    }

    public function icon(): ?string
    {
        return match ($this) {
            self::PAID => 'checkbox-circle-fill',
            self::UNPAID => 'wallet-line',
        };
    }
}
