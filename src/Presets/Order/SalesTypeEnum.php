<?php

namespace WeiJuKeJi\EnumOptions\Presets\Order;

use WeiJuKeJi\EnumOptions\Traits\EnumOptions;

/**
 * 销售类型枚举
 */
enum SalesTypeEnum: string
{
    use EnumOptions;

    case DIRECT = 'direct';
    case DISTRIBUTION = 'distribution';

    /**
     * 获取枚举的中文显示名称
     */
    public static function displayName(): string
    {
        return '销售类型';
    }

    public function label(): string
    {
        return match ($this) {
            self::DIRECT => '直销',
            self::DISTRIBUTION => '分销',
        };
    }

    public function color(): string
    {
        $configColor = config("enum-options.color_overrides.sales_type.{$this->value}");
        if ($configColor) {
            return $configColor;
        }

        return match ($this) {
            self::DIRECT => 'primary',        // 直销 - 主要色
            self::DISTRIBUTION => 'success',  // 分销 - 成功色
        };
    }

    public function icon(): ?string
    {
        return match ($this) {
            self::DIRECT => 'store-2-line',
            self::DISTRIBUTION => 'team-line',
        };
    }
}
