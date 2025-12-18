<?php

namespace YourVendor\EnumOptions\Presets\Business;

use YourVendor\EnumOptions\Traits\EnumOptions;

/**
 * 审批状态枚举
 */
enum ApprovalStatusEnum: string
{
    use EnumOptions;

    case DRAFT = 'draft';
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';
    case REVOKED = 'revoked';

    public function label(): string
    {
        return $this->trans($this->value, match ($this) {
            self::DRAFT => '草稿',
            self::PENDING => '待审批',
            self::APPROVED => '已通过',
            self::REJECTED => '已拒绝',
            self::CANCELLED => '已取消',
            self::REVOKED => '已撤销',
        });
    }

    public function color(): string
    {
        $configColor = config("enum-options.color_overrides.approval_status.{$this->value}");
        if ($configColor) {
            return $configColor;
        }

        return match ($this) {
            self::DRAFT => 'gray',
            self::PENDING => 'orange',
            self::APPROVED => 'green',
            self::REJECTED => 'red',
            self::CANCELLED, self::REVOKED => 'gray',
        };
    }
}
