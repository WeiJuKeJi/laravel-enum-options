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
            self::MALE => 'primary',                           // 男 - 蓝色
            self::FEMALE => 'danger',                          // 女 - 粉色（danger在某些主题是粉色）
            self::OTHER => 'success',                          // 其他 - 绿色
            self::PREFER_NOT_TO_SAY => 'info',                 // 保密 - 灰色
        };
    }

    public function icon(): ?string
    {
        return match ($this) {
            self::MALE => 'men-fill',
            self::FEMALE => 'women-fill',
            self::OTHER => 'genderless-fill',
            self::PREFER_NOT_TO_SAY => 'question-fill',
        };
    }
}
