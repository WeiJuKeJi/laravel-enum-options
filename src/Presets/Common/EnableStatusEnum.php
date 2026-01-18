<?php

namespace WeiJuKeJi\EnumOptions\Presets\Common;

use WeiJuKeJi\EnumOptions\Traits\EnumOptions;

/**
 * 启用状态枚举
 * 通用的启用/停用状态
 */
enum EnableStatusEnum: string
{
    use EnumOptions;

    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';

    /**
     * 获取枚举的中文显示名称
     */
    public static function displayName(): string
    {
        return '启用状态';
    }

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => '启用',
            self::INACTIVE => '停用',
            self::SUSPENDED => '暂停',
        };
    }

    public function color(): string
    {
        $configColor = config("enum-options.color_overrides.enable_status.{$this->value}");
        if ($configColor) {
            return $configColor;
        }

        return match ($this) {
            self::ACTIVE => 'success',      // 启用 - 绿色/成功色
            self::INACTIVE => 'danger',       // 停用 - 灰色/信息色
            self::SUSPENDED => 'warning',   // 暂停 - 橙色/警告色
        };
    }

    public function icon(): ?string
    {
        return match ($this) {
            self::ACTIVE => 'checkbox-circle-fill',
            self::INACTIVE => 'close-circle-fill',
            self::SUSPENDED => 'pause-circle-fill',
        };
    }
}
