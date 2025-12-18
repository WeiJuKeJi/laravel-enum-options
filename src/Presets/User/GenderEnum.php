<?php

namespace YourVendor\EnumOptions\Presets\User;

use YourVendor\EnumOptions\Traits\EnumOptions;

/**
 * 性别枚举
 */
enum GenderEnum: string
{
    use EnumOptions;

    case MALE = 'male';
    case FEMALE = 'female';
    case OTHER = 'other';
    case PREFER_NOT_TO_SAY = 'prefer_not_to_say';

    public function label(): string
    {
        return $this->trans($this->value, match ($this) {
            self::MALE => '男',
            self::FEMALE => '女',
            self::OTHER => '其他',
            self::PREFER_NOT_TO_SAY => '保密',
        });
    }

    public function color(): string
    {
        $configColor = config("enum-options.color_overrides.gender.{$this->value}");
        if ($configColor) {
            return $configColor;
        }

        return match ($this) {
            self::MALE => 'blue',
            self::FEMALE => 'pink',
            self::OTHER => 'purple',
            self::PREFER_NOT_TO_SAY => 'gray',
        };
    }
}
