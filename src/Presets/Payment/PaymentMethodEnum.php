<?php

namespace WeiJuKeJi\EnumOptions\Presets\Payment;

use WeiJuKeJi\EnumOptions\Traits\EnumOptions;

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
            self::WECHAT => 'wechat-pay-fill',
            self::WECHAT_POS => 'wechat-2-fill',
            self::ALIPAY => 'alipay-fill',
            self::BANK_TRANSFER => 'bank-fill',
            self::CASH => 'cash-fill',
            self::CREDIT_CARD => 'bank-card-fill',
            self::DEBIT_CARD => 'bank-card-2-fill',
            self::UNION_PAY => 'secure-payment-fill',
            self::PAYPAL => 'paypal-fill',
            self::APPLE_PAY => 'apple-fill',
            self::GOOGLE_PAY => 'google-fill',
            self::POS => 'calculator-fill',
            self::OTHER => 'more-fill',
        };
    }
}
