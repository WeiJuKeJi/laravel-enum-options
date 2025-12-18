<?php

namespace YourVendor\EnumOptions\Presets\Payment;

use YourVendor\EnumOptions\Traits\EnumOptions;

/**
 * 支付方式枚举
 */
enum PaymentMethodEnum: string
{
    use EnumOptions;

    case WECHAT = 'wechat';
    case ALIPAY = 'alipay';
    case BANK_TRANSFER = 'bank_transfer';
    case CASH = 'cash';
    case CREDIT_CARD = 'credit_card';
    case DEBIT_CARD = 'debit_card';
    case UNION_PAY = 'union_pay';
    case PAYPAL = 'paypal';
    case APPLE_PAY = 'apple_pay';
    case GOOGLE_PAY = 'google_pay';
    case POS = 'pos';
    case WECHAT_POS = 'wechat_pos';
    case OTHER = 'other';

    public function label(): string
    {
        return $this->trans($this->value, match ($this) {
            self::WECHAT => '微信支付',
            self::ALIPAY => '支付宝',
            self::BANK_TRANSFER => '银行转账',
            self::CASH => '现金',
            self::CREDIT_CARD => '信用卡',
            self::DEBIT_CARD => '储蓄卡',
            self::UNION_PAY => '银联',
            self::PAYPAL => 'PayPal',
            self::APPLE_PAY => 'Apple Pay',
            self::GOOGLE_PAY => 'Google Pay',
            self::POS => 'POS机',
            self::WECHAT_POS => '微信POS',
            self::OTHER => '其他',
        });
    }

    public function color(): string
    {
        $configColor = config("enum-options.color_overrides.payment_method.{$this->value}");
        if ($configColor) {
            return $configColor;
        }

        return match ($this) {
            self::WECHAT => 'green',
            self::ALIPAY => 'blue',
            self::BANK_TRANSFER => 'orange',
            self::CASH => 'purple',
            self::CREDIT_CARD, self::DEBIT_CARD => 'cyan',
            self::UNION_PAY => 'red',
            self::PAYPAL => 'blue',
            self::APPLE_PAY => 'gray',
            self::GOOGLE_PAY => 'blue',
            self::POS, self::WECHAT_POS => 'lime',
            self::OTHER => 'default',
        };
    }

    public function icon(): ?string
    {
        return match ($this) {
            self::WECHAT, self::WECHAT_POS => 'wechat',
            self::ALIPAY => 'alipay',
            self::BANK_TRANSFER => 'bank',
            self::CASH => 'money',
            self::CREDIT_CARD, self::DEBIT_CARD => 'credit-card',
            self::UNION_PAY => 'union-pay',
            self::PAYPAL => 'paypal',
            self::APPLE_PAY => 'apple',
            self::GOOGLE_PAY => 'google',
            self::POS => 'pos',
            self::OTHER => 'question',
        };
    }
}
