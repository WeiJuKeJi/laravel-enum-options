<?php

namespace WeiJuKeJi\EnumOptions\Presets\User;

use WeiJuKeJi\EnumOptions\Traits\EnumOptions;

/**
 * 用户状态枚举
 */
enum UserStatusEnum: string
{
    use EnumOptions;

    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';
    case BANNED = 'banned';
    case DELETED = 'deleted';
    case PENDING_VERIFICATION = 'pending_verification';

    public function label(): string
    {
        return $this->trans($this->value, match ($this) {
            self::ACTIVE => '正常',
            self::INACTIVE => '未激活',
            self::SUSPENDED => '已暂停',
            self::BANNED => '已封禁',
            self::DELETED => '已删除',
            self::PENDING_VERIFICATION => '待验证',
        });
    }

    public function color(): string
    {
        $configColor = config("enum-options.color_overrides.user_status.{$this->value}");
        if ($configColor) {
            return $configColor;
        }

        return match ($this) {
            self::ACTIVE => 'green',
            self::INACTIVE => 'gray',
            self::SUSPENDED => 'orange',
            self::BANNED => 'red',
            self::DELETED => 'gray',
            self::PENDING_VERIFICATION => 'blue',
        };
    }

    public function icon(): ?string
    {
        return match ($this) {
            self::ACTIVE => 'user-smile-fill',
            self::INACTIVE => 'user-line',
            self::SUSPENDED => 'pause-circle-fill',
            self::BANNED => 'forbid-fill',
            self::DELETED => 'delete-bin-fill',
            self::PENDING_VERIFICATION => 'user-search-fill',
        };
    }
}
