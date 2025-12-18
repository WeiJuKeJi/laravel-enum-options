<?php

namespace WeiJuKeJi\EnumOptions\Traits;

/**
 * EnumOptions Trait
 *
 * 为 PHP 8.1+ 枚举提供前端友好的选项支持
 * 包含：中文标签、颜色、图标、下拉选项等
 *
 * @method string label() 返回中文标签
 * @method string color() 返回颜色标识
 * @method string|null icon() 返回图标名称（可选）
 */
trait EnumOptions
{
    /**
     * 获取中文标签
     * 子类必须实现此方法
     */
    abstract public function label(): string;

    /**
     * 获取颜色标识（用于前端显示）
     * 默认返回 'default'，子类可以覆盖
     */
    public function color(): string
    {
        // 优先使用配置文件中的颜色覆盖
        $enumName = $this->getEnumName();
        $configColor = config("enum-options.color_overrides.{$enumName}.{$this->value}");

        if ($configColor) {
            return $configColor;
        }

        return 'default';
    }

    /**
     * 获取图标名称（可选）
     * 默认返回 null，子类可以覆盖
     */
    public function icon(): ?string
    {
        return null;
    }

    /**
     * 获取所有选项（用于下拉框）
     *
     * @return array<int, array{value: string, label: string, color: string, icon: string|null}>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $case) => $case->toArray(),
            self::cases()
        );
    }

    /**
     * 从字符串值安全地创建枚举实例
     *
     * @param string|null $value
     * @return static|null
     */
    public static function fromValue(?string $value): ?static
    {
        if ($value === null) {
            return null;
        }

        return self::tryFrom($value);
    }

    /**
     * 转换为数组（用于 API 响应）
     *
     * @return array{value: string, label: string, color: string, icon: string|null}
     */
    public function toArray(): array
    {
        $data = [
            'value' => $this->value,
            'label' => $this->label(),
            'color' => $this->color(),
        ];

        // 只有当 icon 有值时才添加
        $icon = $this->icon();
        if ($icon !== null) {
            $data['icon'] = $icon;
        }

        return $data;
    }

    /**
     * 获取枚举名称（用于配置查找）
     */
    private function getEnumName(): string
    {
        $className = class_basename($this);
        // 移除 'Enum' 后缀
        $name = str_replace('Enum', '', $className);
        // 转换为 snake_case
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
    }

    /**
     * 获取标签（支持翻译文件）
     *
     * @param string $key 翻译键名
     * @param string|null $default 默认值
     * @return string
     */
    protected function trans(string $key, ?string $default = null): string
    {
        $enumName = $this->getEnumName();

        // 优先查找用户自定义翻译
        $userTrans = __("enums.{$enumName}.{$key}");
        if ($userTrans !== "enums.{$enumName}.{$key}") {
            return $userTrans;
        }

        // 其次查找配置覆盖
        $configLabel = config("enum-options.label_overrides.{$enumName}.{$key}");
        if ($configLabel) {
            return $configLabel;
        }

        // 最后查找扩展包预设翻译
        $presetTrans = __("enum-options::presets.{$enumName}.{$key}");
        if ($presetTrans !== "enum-options::presets.{$enumName}.{$key}") {
            return $presetTrans;
        }

        // 如果都没有，返回默认值或键名
        return $default ?? $key;
    }

    /**
     * 获取所有值的数组
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }

    /**
     * 获取所有标签的数组
     *
     * @return array<string, string> [value => label]
     */
    public static function labels(): array
    {
        $labels = [];
        foreach (self::cases() as $case) {
            $labels[$case->value] = $case->label();
        }
        return $labels;
    }

    /**
     * 检查值是否有效
     */
    public static function isValid(?string $value): bool
    {
        return $value !== null && self::tryFrom($value) !== null;
    }
}
