<?php

namespace WeiJuKeJi\EnumOptions\Presets\Payment;

use WeiJuKeJi\EnumOptions\Traits\EnumOptions;

/**
 * 对账状态枚举
 */
enum ReconciliationStatusEnum: string
{
    use EnumOptions;

    case PENDING = 'pending';
    case MATCHED = 'matched';
    case DISCREPANCY = 'discrepancy';
    case MANUAL = 'manual';
    case NONE = 'none';

    /**
     * 获取枚举的中文显示名称
     */
    public static function displayName(): string
    {
        return '对账状态';
    }

    public function label(): string
    {
        return match ($this) {
            self::PENDING => '待对账',
            self::MATCHED => '已匹配',
            self::DISCREPANCY => '有差异',
            self::MANUAL => '人工处理',
            self::NONE => '无需对账',
        };
    }

    public function color(): string
    {
        $configColor = config("enum-options.color_overrides.reconciliation_status.{$this->value}");
        if ($configColor) {
            return $configColor;
        }

        return match ($this) {
            self::MATCHED => 'success',        // 已匹配 - 成功
            self::PENDING => 'warning',        // 待对账 - 警告
            self::DISCREPANCY => 'danger',     // 有差异 - 危险
            self::MANUAL => 'primary',         // 人工处理 - 主要色
            self::NONE => 'info',              // 无需对账 - 信息
        };
    }

    public function icon(): ?string
    {
        return match ($this) {
            self::PENDING => 'time-line',
            self::MATCHED => 'checkbox-circle-fill',
            self::DISCREPANCY => 'error-warning-fill',
            self::MANUAL => 'user-settings-line',
            self::NONE => 'checkbox-blank-circle-line',
        };
    }
}
