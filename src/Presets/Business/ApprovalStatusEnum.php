<?php

namespace WeiJuKeJi\EnumOptions\Presets\Business;

use WeiJuKeJi\EnumOptions\Traits\EnumOptions;

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
            self::APPROVED => 'success',                       // 已通过 - 成功
            self::PENDING => 'warning',                         // 待审批 - 需要处理
            self::REJECTED => 'danger',                        // 已拒绝 - 错误
            self::DRAFT, self::CANCELLED, self::REVOKED => 'info', // 草稿/取消/撤销 - 中性
        };
    }

    public function icon(): ?string
    {
        return match ($this) {
            self::DRAFT => 'draft-fill',
            self::PENDING => 'time-line',
            self::APPROVED => 'checkbox-circle-fill',
            self::REJECTED => 'close-circle-fill',
            self::CANCELLED => 'forbid-fill',
            self::REVOKED => 'arrow-go-back-fill',
        };
    }
}
