<?php

namespace WeiJuKeJi\EnumOptions\Presets\User;

use WeiJuKeJi\EnumOptions\Traits\EnumOptions;

/**
 * 性别枚举
 */
enum GenderEnum: string
{
    use EnumOptions;

    case MALE = 'male';
    case FEMALE = 'female';
    case UNKNOWN = 'unknown';

    /**
     * 获取枚举的中文显示名称
     */
    public static function displayName(): string
    {
        return '性别';
    }

    public function label(): string
    {
        return match ($this) {
            self::MALE => '男',
            self::FEMALE => '女',
            self::UNKNOWN => '未知',
        };
    }

    public function color(): string
    {
        $configColor = config("enum-options.color_overrides.gender.{$this->value}");
        if ($configColor) {
            return $configColor;
        }

        return match ($this) {
            self::MALE => 'primary',                           // 男 - 蓝色
            self::FEMALE => 'danger',                          // 女 - 粉色（danger在某些主题是粉色）
            self::UNKNOWN => 'info',                           // 未知 - 灰色
        };
    }

    public function icon(): ?string
    {
        return match ($this) {
            self::MALE => 'men-line',
            self::FEMALE => 'women-line',
            self::UNKNOWN => 'question-line',
        };
    }
}
