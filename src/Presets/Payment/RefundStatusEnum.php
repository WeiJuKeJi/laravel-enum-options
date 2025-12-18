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
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case PARTIAL = 'partial';
    case FULL = 'full';
    case FAILED = 'failed';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return $this->trans($this->value, match ($this) {
            self::NONE => '无退款',
            self::PENDING => '待退款',
            self::PROCESSING => '退款中',
            self::PARTIAL => '部分退款',
            self::FULL => '全额退款',
            self::FAILED => '退款失败',
            self::REJECTED => '退款拒绝',
        });
    }

    public function color(): string
    {
        $configColor = config("enum-options.color_overrides.refund_status.{$this->value}");
        if ($configColor) {
            return $configColor;
        }

        return match ($this) {
            self::NONE => 'default',
            self::PENDING => 'orange',
            self::PROCESSING => 'blue',
            self::PARTIAL => 'orange',
            self::FULL => 'red',
            self::FAILED, self::REJECTED => 'red',
        };
    }
}
