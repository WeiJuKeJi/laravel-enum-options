<?php

namespace WeiJuKeJi\EnumOptions\Presets\Order;

use WeiJuKeJi\EnumOptions\Traits\EnumOptions;

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
            self::STANDARD => '',                              // 标准订单 - 默认
            self::SUBSCRIPTION => 'success',                   // 订阅订单 - 成功/稳定
            self::PRESALE, self::GROUP_BUY => 'warning',       // 预售/团购 - 需要关注
            self::FLASH_SALE => 'danger',                      // 限时特惠 - 紧急/热门
            self::GIFT => 'primary',                           // 礼品订单 - 主要
            self::EXCHANGE => 'info',                          // 兑换订单 - 中性
        };
    }

    public function icon(): ?string
    {
        return match ($this) {
            self::STANDARD => 'shopping-cart-fill',
            self::PRESALE => 'calendar-event-fill',
            self::GROUP_BUY => 'group-fill',
            self::FLASH_SALE => 'flashlight-fill',
            self::SUBSCRIPTION => 'loop-left-fill',
            self::GIFT => 'gift-fill',
            self::EXCHANGE => 'exchange-fill',
        };
    }
}
