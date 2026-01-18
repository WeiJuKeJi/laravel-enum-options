<?php

namespace WeiJuKeJi\EnumOptions\Presets\Payment;

use WeiJuKeJi\EnumOptions\Traits\EnumOptions;

/**
 * 退款状态枚举
 */
enum RefundStatusEnum: string
{
    use EnumOptions;

    case NONE = 'none';
    case FULL = 'full';
    case PARTIAL = 'partial';

    /**
     * 获取枚举的中文显示名称
     */
    public static function displayName(): string
    {
        return '退款状态';
    }

    public function label(): string
    {
        return match ($this) {
            self::NONE => '无退款',
            self::FULL => '全额退款',
            self::PARTIAL => '部分退款',
        };
    }

    public function color(): string
    {
        $configColor = config("enum-options.color_overrides.refund_status.{$this->value}");
        if ($configColor) {
            return $configColor;
        }

        return match ($this) {
            self::NONE => 'info',                               // 无退款 - 灰色
            self::FULL => 'danger',                             // 全额退款 - 红色
            self::PARTIAL => 'warning',                         // 部分退款 - 橙色
        };
    }

    public function icon(): ?string
    {
        return match ($this) {
            self::NONE => 'checkbox-blank-circle-line',
            self::FULL => 'refund-fill',
            self::PARTIAL => 'refund-2-line',
        };
    }
}
