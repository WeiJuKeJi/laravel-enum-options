<?php

namespace WeiJuKeJi\EnumOptions\Support;

use Illuminate\Support\Str;
use ReflectionEnum;
use WeiJuKeJi\EnumOptions\Traits\EnumOptions;

/**
 * 枚举注册表
 * 管理所有可用的枚举类及其元数据
 */
class EnumRegistry
{

    /**
     * 获取所有已注册的枚举
     *
     * @return array
     */
    public static function all(): array
    {
        $enums = [];

        // 1. 加载内置预设枚举（自动发现）
        if (config('enum-options.auto_discover_presets', true)) {
            $enums = static::discoverPresetEnums();
        }

        // 2. 自动发现应用枚举
        if (config('enum-options.auto_discover_app_enums', true)) {
            $appEnums = static::discoverAppEnums();
            $enums = array_merge($enums, $appEnums);
        }

        // 3. 合并用户手动注册的枚举（优先级最高）
        $customEnums = config('enum-options.registered_enums', []);
        $enums = array_merge($enums, $customEnums);

        return $enums;
    }

    /**
     * 自动发现内置预设枚举
     * 扫描 src/Presets 目录下的所有枚举类
     *
     * @return array
     */
    protected static function discoverPresetEnums(): array
    {
        $discovered = [];
        $baseDir = __DIR__ . '/../Presets';

        // 如果目录不存在，返回空数组
        if (!is_dir($baseDir)) {
            return $discovered;
        }

        // 扫描所有分类目录（Payment, Order, User, Business 等）
        $categoryDirs = glob($baseDir . '/*', GLOB_ONLYDIR);

        foreach ($categoryDirs as $categoryDir) {
            $categoryName = strtolower(basename($categoryDir)); // Payment -> payment

            // 扫描分类目录中的所有 PHP 文件
            $files = glob($categoryDir . '/*.php');

            foreach ($files as $file) {
                $fileName = basename($file);

                // 检查应用中是否已经发布了同名的枚举文件
                // 如果已发布，优先使用应用的枚举，跳过预设枚举
                if (static::isEnumPublishedToApp($fileName)) {
                    continue;
                }

                $className = 'WeiJuKeJi\\EnumOptions\\Presets\\'
                    . basename($categoryDir) . '\\'
                    . basename($file, '.php');

                // 检查类是否存在且是枚举
                if (!class_exists($className) || !enum_exists($className)) {
                    continue;
                }

                // 检查是否使用了 EnumOptions trait
                $reflection = new ReflectionEnum($className);
                $traits = $reflection->getTraitNames();

                if (!in_array(EnumOptions::class, $traits)) {
                    continue;
                }

                // 生成枚举配置
                $key = static::generateKey($className);
                $discovered[$key] = [
                    'class' => $className,
                    'name' => static::generateName($className),
                    'description' => static::generateDescription($className),
                    'category' => $categoryName,
                ];
            }
        }

        return $discovered;
    }

    /**
     * 自动发现应用中的枚举类
     *
     * @return array
     */
    protected static function discoverAppEnums(): array
    {
        $discovered = [];
        $paths = config('enum-options.app_enums_paths', [app_path('Enums')]);
        $namespace = config('enum-options.app_enums_namespace', 'App\\Enums');

        // 确保 paths 是数组
        if (!is_array($paths)) {
            $paths = [$paths];
        }

        foreach ($paths as $path) {
            if (!is_dir($path)) {
                continue;
            }

            // 1. 扫描根目录中的直接 PHP 文件
            $files = glob($path . '/*.php');

            foreach ($files as $file) {
                $className = $namespace . '\\' . basename($file, '.php');

                // 检查类是否存在且是枚举
                if (!class_exists($className) || !enum_exists($className)) {
                    continue;
                }

                // 检查是否使用了 EnumOptions trait
                $reflection = new ReflectionEnum($className);
                $traits = $reflection->getTraitNames();

                if (!in_array(EnumOptions::class, $traits)) {
                    continue;
                }

                // 生成枚举配置
                $key = static::generateKey($className);
                $discovered[$key] = [
                    'class' => $className,
                    'name' => static::generateName($className),
                    'description' => static::generateDescription($className),
                    'category' => static::guessCategory($className),
                ];
            }

            // 2. 扫描分类子目录（例如：Payment, Order, User, Business）
            $categoryDirs = glob($path . '/*', GLOB_ONLYDIR);

            foreach ($categoryDirs as $categoryDir) {
                $categoryName = basename($categoryDir);
                $categoryFiles = glob($categoryDir . '/*.php');

                foreach ($categoryFiles as $file) {
                    $className = $namespace . '\\' . $categoryName . '\\' . basename($file, '.php');

                    // 检查类是否存在且是枚举
                    if (!class_exists($className) || !enum_exists($className)) {
                        continue;
                    }

                    // 检查是否使用了 EnumOptions trait
                    $reflection = new ReflectionEnum($className);
                    $traits = $reflection->getTraitNames();

                    if (!in_array(EnumOptions::class, $traits)) {
                        continue;
                    }

                    // 生成枚举配置
                    $key = static::generateKey($className);
                    $discovered[$key] = [
                        'class' => $className,
                        'name' => static::generateName($className),
                        'description' => static::generateDescription($className),
                        'category' => strtolower($categoryName),
                    ];
                }
            }
        }

        return $discovered;
    }

    /**
     * 从类名生成 key
     *
     * @param string $className
     * @return string
     */
    protected static function generateKey(string $className): string
    {
        $shortName = class_basename($className);
        // 移除 Enum 后缀
        $shortName = preg_replace('/Enum$/', '', $shortName);
        // 转换为 snake_case 复数形式
        return Str::snake(Str::plural($shortName));
    }

    /**
     * 从类名生成显示名称
     *
     * @param string $className
     * @return string
     */
    protected static function generateName(string $className): string
    {
        $shortName = class_basename($className);
        $shortName = preg_replace('/Enum$/', '', $shortName);
        // 转换为空格分隔的标题格式
        return Str::title(Str::snake($shortName, ' '));
    }

    /**
     * 生成描述
     *
     * @param string $className
     * @return string
     */
    protected static function generateDescription(string $className): string
    {
        $name = static::generateName($className);
        return "所有可用的{$name}选项";
    }

    /**
     * 根据类名猜测分类
     *
     * @param string $className
     * @return string
     */
    protected static function guessCategory(string $className): string
    {
        $shortName = strtolower(class_basename($className));

        // 根据常见关键词猜测分类
        $categoryMap = [
            'payment' => ['payment', 'pay', 'refund'],
            'order' => ['order'],
            'user' => ['user', 'member', 'customer', 'gender'],
            'product' => ['product', 'goods', 'item', 'sku'],
            'business' => ['approval', 'publish', 'workflow', 'process'],
            'system' => ['status', 'type', 'level', 'priority'],
        ];

        foreach ($categoryMap as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($shortName, $keyword)) {
                    return $category;
                }
            }
        }

        return 'custom';
    }

    /**
     * 获取所有枚举的元数据列表
     * 用于 API 返回
     *
     * @return array
     */
    public static function getMetadata(): array
    {
        $enums = static::all();
        $metadata = [];

        foreach ($enums as $key => $config) {
            $enumClass = $config['class'] ?? null;

            if (!$enumClass || !enum_exists($enumClass)) {
                continue;
            }

            $metadata[] = [
                'key' => $key,
                'name' => $config['name'] ?? $key,
                'label' => static::getEnumDisplayName($enumClass),
                'description' => $config['description'] ?? '',
                'route' => $config['route'] ?? '/enums/' . Str::slug($key, '-'),
                'count' => count($enumClass::cases()),
                'category' => $config['category'] ?? 'other',
            ];
        }

        return $metadata;
    }

    /**
     * 获取枚举的中文显示名称
     * 从枚举类的 displayName() 方法读取
     *
     * @param string $enumClass
     * @return string
     */
    protected static function getEnumDisplayName(string $enumClass): string
    {
        // 检查枚举类是否有 displayName 方法
        if (method_exists($enumClass, 'displayName')) {
            return $enumClass::displayName();
        }

        // 回退：返回从类名生成的名称
        return static::generateName($enumClass);
    }

    /**
     * 根据 key 获取枚举类
     *
     * @param string $key
     * @return string|null
     */
    public static function getEnumClass(string $key): ?string
    {
        $enums = static::all();
        return $enums[$key]['class'] ?? null;
    }

    /**
     * 检查枚举是否已注册
     *
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        $enums = static::all();
        return isset($enums[$key]);
    }

    /**
     * 获取指定分类的枚举
     *
     * @param string $category
     * @return array
     */
    public static function getByCategory(string $category): array
    {
        $enums = static::all();

        return array_filter($enums, function ($config) use ($category) {
            return ($config['category'] ?? 'other') === $category;
        });
    }

    /**
     * 获取所有可用的分类
     *
     * @return array
     */
    public static function getCategories(): array
    {
        $enums = static::all();
        $categories = [];

        foreach ($enums as $config) {
            $category = $config['category'] ?? 'other';
            if (!in_array($category, $categories)) {
                $categories[] = $category;
            }
        }

        return $categories;
    }

    /**
     * 检查枚举文件是否已经发布到应用中
     * 用于避免预设枚举和已发布枚举的重复声明冲突
     *
     * @param string $fileName 枚举文件名（例如：ApprovalStatusEnum.php）
     * @return bool
     */
    protected static function isEnumPublishedToApp(string $fileName): bool
    {
        // 获取应用枚举的路径配置
        $paths = config('enum-options.app_enums_paths', [app_path('Enums')]);

        // 确保 paths 是数组
        if (!is_array($paths)) {
            $paths = [$paths];
        }

        // 检查所有配置的应用枚举路径
        foreach ($paths as $path) {
            // 1. 检查根目录中的直接文件
            $appEnumFile = $path . DIRECTORY_SEPARATOR . $fileName;

            if (file_exists($appEnumFile)) {
                return true;
            }

            // 2. 检查分类子目录（Payment, Order, User, Business 等）
            $categoryDirs = glob($path . '/*', GLOB_ONLYDIR);

            foreach ($categoryDirs as $categoryDir) {
                $categoryEnumFile = $categoryDir . DIRECTORY_SEPARATOR . $fileName;

                if (file_exists($categoryEnumFile)) {
                    return true;
                }
            }
        }

        return false;
    }
}

