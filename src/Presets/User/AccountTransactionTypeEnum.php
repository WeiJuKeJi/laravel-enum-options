<?php

namespace WeiJuKeJi\EnumOptions\Presets\User;

use WeiJuKeJi\EnumOptions\Traits\EnumOptions;

/**
 * 账户动账类型枚举
 */
enum AccountTransactionTypeEnum: string
{
    use EnumOptions;

    case RECHARGE = 'recharge';
    case DEDUCTION = 'deduction';
    case CONSUMPTION = 'consumption';
    case REFUND = 'refund';

    /**
     * 获取枚举的中文显示名称
     */
    public static function displayName(): string
    {
        return '动账类型';
    }

    public function label(): string
    {
        return match ($this) {
            self::RECHARGE => '充值',
            self::DEDUCTION => '扣款',
            self::CONSUMPTION => '消费',
            self::REFUND => '退款',
        };
    }

    public function color(): string
    {
        $configColor = config("enum-options.color_overrides.account_transaction_type.{$this->value}");
        if ($configColor) {
            return $configColor;
        }

        return match ($this) {
            self::RECHARGE => 'success',      // 充值 - 绿色/成功色
            self::DEDUCTION => 'danger',      // 扣款 - 红色/危险色
            self::CONSUMPTION => 'warning',   // 消费 - 橙色/警告色
            self::REFUND => 'primary',        // 退款 - 蓝色/主要色
        };
    }

    public function icon(): ?string
    {
        return match ($this) {
            self::RECHARGE => 'add-circle-fill',
            self::DEDUCTION => 'indeterminate-circle-fill',
            self::CONSUMPTION => 'shopping-cart-fill',
            self::REFUND => 'refund-fill',
        };
    }
}
