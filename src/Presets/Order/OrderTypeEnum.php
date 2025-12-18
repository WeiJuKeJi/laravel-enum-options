<?php

namespace YourVendor\EnumOptions\Presets\Order;

use YourVendor\EnumOptions\Traits\EnumOptions;

/**
 * 订单类型枚举
 */
enum OrderTypeEnum: string
{
    use EnumOptions;

    case STANDARD = 'standard';
    case PRESALE = 'presale';
    case GROUP_BUY = 'group_buy';
    case FLASH_SALE = 'flash_sale';
    case SUBSCRIPTION = 'subscription';
    case GIFT = 'gift';
    case EXCHANGE = 'exchange';

    public function label(): string
    {
        return $this->trans($this->value, match ($this) {
            self::STANDARD => '普通订单',
            self::PRESALE => '预售订单',
            self::GROUP_BUY => '拼团订单',
            self::FLASH_SALE => '秒杀订单',
            self::SUBSCRIPTION => '订阅订单',
            self::GIFT => '礼品订单',
            self::EXCHANGE => '兑换订单',
        });
    }

    public function color(): string
    {
        $configColor = config("enum-options.color_overrides.order_type.{$this->value}");
        if ($configColor) {
            return $configColor;
        }

        return match ($this) {
            self::STANDARD => 'default',
            self::PRESALE => 'purple',
            self::GROUP_BUY => 'orange',
            self::FLASH_SALE => 'red',
            self::SUBSCRIPTION => 'blue',
            self::GIFT => 'pink',
            self::EXCHANGE => 'cyan',
        };
    }
}
