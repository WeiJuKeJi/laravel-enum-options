<?php

namespace YourVendor\EnumOptions\Presets\Business;

use YourVendor\EnumOptions\Traits\EnumOptions;

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

    public function label(): string
    {
        return $this->trans($this->value, match ($this) {
            self::DRAFT => '草稿',
            self::SCHEDULED => '定时发布',
            self::PUBLISHED => '已发布',
            self::UNPUBLISHED => '未发布',
            self::ARCHIVED => '已归档',
        });
    }

    public function color(): string
    {
        $configColor = config("enum-options.color_overrides.publish_status.{$this->value}");
        if ($configColor) {
            return $configColor;
        }

        return match ($this) {
            self::DRAFT => 'gray',
            self::SCHEDULED => 'blue',
            self::PUBLISHED => 'green',
            self::UNPUBLISHED => 'orange',
            self::ARCHIVED => 'gray',
        };
    }
}
