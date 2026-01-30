<?php

namespace WeiJuKeJi\EnumOptions\Traits;

/**
 * EnumOptions Trait
 *
 * 为 PHP 8.1+ 枚举提供前端友好的选项支持
 * 包含：中文标签、颜色、图标、下拉选项等
 */
trait EnumOptions
{
    /**
     * 获取中文标签
     * 子类必须实现此方法
     */
    abstract public function label(): string;

    /**
     * 获取枚举的中文显示名称
     * 子类可以覆盖此方法来提供自定义名称
     * 默认返回从类名生成的名称
     *
     * @return string
     */
    public static function displayName(): string
    {
        $className = class_basename(static::class);
        $name = preg_replace('/Enum$/', '', $className);
        // 转换为空格分隔的标题格式
        return \Illuminate\Support\Str::title(\Illuminate\Support\Str::snake($name, ' '));
    }

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
     * 安全地将枚举值转换为数组
     * 支持传入字符串值或枚举实例（兼容 Model $casts 场景）
     * 如果值无效，返回包含原始值的降级对象而不是 null
     *
     * @param string|self|null $value 枚举值或枚举实例
     * @return array|null 如果 value 为 null 返回 null，否则返回数组
     */
    public static function toArraySafe(string|self|null $value): ?array
    {
        if ($value === null) {
            return null;
        }

        // 如果已经是枚举实例，直接调用 toArray()
        if ($value instanceof self) {
            return $value->toArray();
        }

        // 尝试转换为枚举实例
        $enum = static::fromValue($value);

        // 成功匹配枚举，返回完整信息
        if ($enum !== null) {
            return $enum->toArray();
        }

        // 枚举值无效，返回降级对象
        return static::buildFallbackArray($value);
    }

    /**
     * 构建降级数组（当枚举值无效时使用）
     *
     * @param string $value 原始值
     * @return array{value: string, label: string, color: string, icon: null}
     */
    protected static function buildFallbackArray(string $value): array
    {
        return [
            'value' => $value,
            'label' => static::getFallbackLabel($value),
            'color' => config('enum-options.fallback_color', 'default'),
            'icon' => null,
        ];
    }

    /**
     * 获取降级标签
     * 优先查找配置，否则返回原始值
     *
     * @param string $value 原始值
     * @return string
     */
    protected static function getFallbackLabel(string $value): string
    {
        // 尝试从配置中获取自定义降级标签
        $customLabel = config("enum-options.fallback_labels.{$value}");
        if ($customLabel) {
            return $customLabel;
        }

        // 根据配置的转换策略处理标签
        $transform = config('enum-options.fallback_label_transform', 'none');

        return match ($transform) {
            'upper' => strtoupper($value),
            'lower' => strtolower($value),
            'ucfirst' => ucfirst($value),
            'ucwords' => ucwords(str_replace('_', ' ', $value)),
            default => $value,
        };
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
     * 获取标签（支持配置覆盖）
     * 这是一个辅助方法，供用户在 label() 方法中使用
     *
     * @param string $value 枚举值
     * @param string|null $default 默认标签
     * @return string
     */
    protected function trans(string $value, ?string $default = null): string
    {
        $enumName = $this->getEnumName();

        // 查找配置覆盖
        $configLabel = config("enum-options.label_overrides.{$enumName}.{$value}");
        if ($configLabel) {
            return $configLabel;
        }

        // 返回默认值或值本身
        return $default ?? $value;
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
