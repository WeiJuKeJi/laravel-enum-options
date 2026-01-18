<?php

namespace WeiJuKeJi\EnumOptions\Presets\Business;

use WeiJuKeJi\EnumOptions\Traits\EnumOptions;

/**
 * OTA平台枚举
 */
enum OtaPlatformEnum: string
{
    use EnumOptions;

    case DOUYIN = 'douyin';
    case MEITUAN = 'meituan';
    case CTRIP = 'ctrip';
    case PIAOFUTONG = 'piaofutong';
    case TIANSHI = 'tianshi';

    /**
     * 获取枚举的中文显示名称
     */
    public static function displayName(): string
    {
        return 'OTA平台';
    }

    public function label(): string
    {
        return match ($this) {
            self::DOUYIN => '抖音',
            self::MEITUAN => '美团',
            self::CTRIP => '携程',
            self::PIAOFUTONG => '票付通',
            self::TIANSHI => '天时同城',
        };
    }

    public function color(): string
    {
        $configColor = config("enum-options.color_overrides.ota_platform.{$this->value}");
        if ($configColor) {
            return $configColor;
        }

        return match ($this) {
            self::DOUYIN => 'danger',       // 抖音 - 红色
            self::MEITUAN => 'warning',     // 美团 - 黄色
            self::CTRIP => 'primary',       // 携程 - 蓝色
            self::PIAOFUTONG => 'success',  // 票付通 - 绿色
            self::TIANSHI => 'info',        // 天时同城 - 灰色
        };
    }

    public function icon(): ?string
    {
        return match ($this) {
            self::DOUYIN => 'music-2-fill',
            self::MEITUAN => 'restaurant-fill',
            self::CTRIP => 'plane-fill',
            self::PIAOFUTONG => 'ticket-fill',
            self::TIANSHI => 'building-fill',
        };
    }
}
