<?php

namespace WeiJuKeJi\EnumOptions\Presets\Common;

use WeiJuKeJi\EnumOptions\Traits\EnumOptions;

/**
 * 布尔值枚举 (是/否)
 * 兼容多种常见格式: yes/no, y/n, 1/0
 */
enum BooleanEnum: string
{
    use EnumOptions;

    case YES = 'yes';
    case NO = 'no';
    case Y = 'y';
    case N = 'n';
    case ONE = '1';
    case ZERO = '0';

    public function label(): string
    {
        return $this->trans($this->value, match ($this) {
            self::YES, self::Y, self::ONE => '是',
            self::NO, self::N, self::ZERO => '否',
        });
    }

    public function color(): string
    {
        $configColor = config("enum-options.color_overrides.boolean.{$this->value}");
        if ($configColor) {
            return $configColor;
        }

        return match ($this) {
            self::YES, self::Y, self::ONE => 'success',     // 是 - 绿色/成功色
            self::NO, self::N, self::ZERO => 'info',        // 否 - 灰色/信息色
        };
    }

    public function icon(): ?string
    {
        return match ($this) {
            self::YES, self::Y, self::ONE => 'check-circle-fill',
            self::NO, self::N, self::ZERO => 'close-circle-fill',
        };
    }
}
