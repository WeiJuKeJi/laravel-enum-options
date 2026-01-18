<?php

namespace WeiJuKeJi\EnumOptions\Presets\Payment;

use WeiJuKeJi\EnumOptions\Traits\EnumOptions;

/**
 * 核销状态枚举
 */
enum VerificationStatusEnum: string
{
    use EnumOptions;

    case NONE = 'none';
    case PARTIAL = 'partial';
    case FULL = 'full';

    /**
     * 获取枚举的中文显示名称
     */
    public static function displayName(): string
    {
        return '核销状态';
    }

    public function label(): string
    {
        return match ($this) {
            self::NONE => '未核销',
            self::PARTIAL => '部分核销',
            self::FULL => '全部核销',
        };
    }

    public function color(): string
    {
        $configColor = config("enum-options.color_overrides.verification_status.{$this->value}");
        if ($configColor) {
            return $configColor;
        }

        return match ($this) {
            self::FULL => 'success',        // 全部核销 - 成功
            self::PARTIAL => 'warning',     // 部分核销 - 警告
            self::NONE => 'info',           // 未核销 - 信息
        };
    }

    public function icon(): ?string
    {
        return match ($this) {
            self::NONE => 'file-list-line',
            self::PARTIAL => 'file-list-2-line',
            self::FULL => 'file-list-3-fill',
        };
    }
}
