<?php

namespace WeiJuKeJi\EnumOptions\Presets\Business;

use WeiJuKeJi\EnumOptions\Traits\EnumOptions;

/**
 * 发布状态枚举
 */
enum PublishStatusEnum: string
{
    use EnumOptions;

    case DRAFT = 'draft';
    case SCHEDULED = 'scheduled';
    case PUBLISHED = 'published';
    case UNPUBLISHED = 'unpublished';
    case ARCHIVED = 'archived';

    /**
     * 获取枚举的中文显示名称
     */
    public static function displayName(): string
    {
        return '发布状态';
    }

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => '草稿',
            self::SCHEDULED => '定时发布',
            self::PUBLISHED => '已发布',
            self::UNPUBLISHED => '未发布',
            self::ARCHIVED => '已归档',
        };
    }

    public function color(): string
    {
        $configColor = config("enum-options.color_overrides.publish_status.{$this->value}");
        if ($configColor) {
            return $configColor;
        }

        return match ($this) {
            self::PUBLISHED => 'success',                      // 已发布 - 成功
            self::SCHEDULED => 'primary',                       // 定时发布 - 进行中
            self::UNPUBLISHED => 'warning',                     // 未发布 - 需要注意
            self::DRAFT, self::ARCHIVED => 'info',             // 草稿/归档 - 中性
        };
    }

    public function icon(): ?string
    {
        return match ($this) {
            self::DRAFT => 'draft-fill',
            self::SCHEDULED => 'calendar-schedule-fill',
            self::PUBLISHED => 'send-plane-fill',
            self::UNPUBLISHED => 'eye-off-fill',
            self::ARCHIVED => 'archive-fill',
        };
    }
}
