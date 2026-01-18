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
    case BALANCE = 'balance';
    case POS = 'pos';
    case NO_PAYMENT = 'no_payment';
    case COUPON = 'coupon';
    case CREDIT_ACCOUNT = 'credit_account';
    case OTA = 'ota';

    /**
     * 获取枚举的中文显示名称
     */
    public static function displayName(): string
    {
        return '支付方式';
    }

    public function label(): string
    {
        return match ($this) {
            self::WECHAT => '微信支付',
            self::ALIPAY => '支付宝',
            self::BANK_TRANSFER => '银行转账',
            self::CASH => '现金',
            self::BALANCE => '余额',
            self::POS => 'POS机',
            self::NO_PAYMENT => '无需支付',
            self::COUPON => '兑换券',
            self::CREDIT_ACCOUNT => '挂账',
            self::OTA => 'OTA',
        };
    }

    public function color(): string
    {
        $configColor = config("enum-options.color_overrides.payment_method.{$this->value}");
        if ($configColor) {
            return $configColor;
        }

        return match ($this) {
            self::WECHAT, self::ALIPAY => 'success',           // 主流支付 - 绿色
            self::BANK_TRANSFER => 'warning',                   // 银行转账 - 需要确认
            self::CASH => 'info',                              // 现金 - 中性
            self::BALANCE => 'primary',                        // 余额 - 主要
            self::POS => 'primary',                            // POS机 - 主要
            self::NO_PAYMENT => 'info',                        // 无需支付 - 中性
            self::COUPON => 'warning',                         // 兑换券 - 橙色
            self::CREDIT_ACCOUNT => 'danger',                  // 挂账 - 需要注意
            self::OTA => 'primary',                            // OTA - 主要
        };
    }

    public function icon(): ?string
    {
        return match ($this) {
            self::WECHAT => 'wechat-pay-fill',
            self::ALIPAY => 'alipay-fill',
            self::BANK_TRANSFER => 'bank-fill',
            self::CASH => 'cash-fill',
            self::BALANCE => 'wallet-fill',
            self::POS => 'calculator-fill',
            self::NO_PAYMENT => 'checkbox-blank-circle-line',
            self::COUPON => 'coupon-fill',
            self::CREDIT_ACCOUNT => 'file-list-line',
            self::OTA => 'earth-fill',
        };
    }
}
