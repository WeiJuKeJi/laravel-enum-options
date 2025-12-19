#!/usr/bin/env php
<?php

/**
 * 枚举路由诊断脚本
 *
 * 用于诊断枚举选项 API 路由的配置和注册情况
 *
 * 使用方法（在 Laravel 项目根目录运行）：
 * php vendor/weijukeji/laravel-enum-options/debug-routes.php
 */

echo "=================================\n";
echo "枚举选项路由诊断工具\n";
echo "=================================\n\n";

// 1. 检查配置文件
echo "【1】检查配置文件\n";
echo "----------------------------\n";

$configPath = __DIR__ . '/../../config/enum-options.php';
if (file_exists($configPath)) {
    echo "✅ 应用配置文件存在: {$configPath}\n";
    $config = require $configPath;
    echo "   - auto_register_routes: " . ($config['auto_register_routes'] ? '✅ true' : '❌ false') . "\n";
    echo "   - route_prefix: " . $config['route_prefix'] . "\n";
    echo "   - route_middleware: " . json_encode($config['route_middleware']) . "\n";
    echo "   - auto_discover_presets: " . ($config['auto_discover_presets'] ? '✅ true' : '❌ false') . "\n";
} else {
    echo "⚠️  应用配置文件不存在，使用包默认配置\n";
    echo "   建议运行: php artisan vendor:publish --tag=enum-options-config\n";
}
echo "\n";

// 2. 检查 ServiceProvider 注册
echo "【2】检查 ServiceProvider 注册\n";
echo "----------------------------\n";

$bootstrapPath = __DIR__ . '/../../bootstrap/providers.php';
if (file_exists($bootstrapPath)) {
    $content = file_get_contents($bootstrapPath);
    if (str_contains($content, 'EnumOptionsServiceProvider')) {
        echo "✅ ServiceProvider 已在 bootstrap/providers.php 中注册\n";
    } else {
        echo "⚠️  ServiceProvider 未在 bootstrap/providers.php 中找到\n";
    }
} else {
    echo "ℹ️  bootstrap/providers.php 不存在（Laravel 10 及以下版本正常）\n";
}

$appConfigPath = __DIR__ . '/../../config/app.php';
if (file_exists($appConfigPath)) {
    $content = file_get_contents($appConfigPath);
    if (str_contains($content, 'EnumOptionsServiceProvider')) {
        echo "✅ ServiceProvider 已在 config/app.php 中注册\n";
    } else {
        echo "ℹ️  ServiceProvider 未在 config/app.php 中找到（包自动发现时正常）\n";
    }
}
echo "\n";

// 3. 检查预设枚举文件
echo "【3】检查预设枚举文件\n";
echo "----------------------------\n";

$presetsDir = __DIR__ . '/src/Presets';
if (is_dir($presetsDir)) {
    echo "✅ Presets 目录存在: {$presetsDir}\n";

    $categories = glob($presetsDir . '/*', GLOB_ONLYDIR);
    echo "   发现 " . count($categories) . " 个分类目录:\n";

    foreach ($categories as $categoryDir) {
        $categoryName = basename($categoryDir);
        $files = glob($categoryDir . '/*.php');
        echo "   - {$categoryName}: " . count($files) . " 个枚举\n";

        // 列出具体的枚举
        foreach ($files as $file) {
            $enumName = basename($file, '.php');
            echo "     • {$enumName}\n";
        }
    }
} else {
    echo "❌ Presets 目录不存在\n";
}
echo "\n";

// 4. 检查 EnumRegistry
echo "【4】测试 EnumRegistry\n";
echo "----------------------------\n";

require_once __DIR__ . '/src/Support/EnumRegistry.php';
require_once __DIR__ . '/src/Traits/EnumOptions.php';

// 手动设置临时配置
$tempConfig = [
    'auto_discover_presets' => true,
    'auto_discover_app_enums' => false,
    'registered_enums' => [],
];

// 模拟 config() 函数
if (!function_exists('config')) {
    function config($key, $default = null) {
        global $tempConfig;
        $keys = explode('.', $key);
        $value = $tempConfig;
        foreach ($keys as $k) {
            if (isset($value[$k])) {
                $value = $value[$k];
            } else {
                return $default;
            }
        }
        return $value ?? $default;
    }
}

try {
    $enums = \WeiJuKeJi\EnumOptions\Support\EnumRegistry::all();
    echo "✅ EnumRegistry 加载成功\n";
    echo "   发现 " . count($enums) . " 个枚举:\n\n";

    foreach ($enums as $key => $config) {
        $route = \Illuminate\Support\Str::kebab($key);
        echo "   • {$key}\n";
        echo "     类: {$config['class']}\n";
        echo "     路由: /api/enums/{$route}\n";
        echo "     分类: {$config['category']}\n";
        echo "\n";
    }
} catch (\Exception $e) {
    echo "❌ EnumRegistry 加载失败: " . $e->getMessage() . "\n";
}
echo "\n";

// 5. 建议的解决步骤
echo "【5】问题排查步骤\n";
echo "----------------------------\n";
echo "如果路由 404，请按以下顺序检查:\n\n";
echo "1️⃣  发布配置文件（如果未发布）:\n";
echo "   php artisan vendor:publish --tag=enum-options-config\n\n";
echo "2️⃣  确认配置正确:\n";
echo "   cat config/enum-options.php | grep auto_register_routes\n";
echo "   确保 'auto_register_routes' => true\n\n";
echo "3️⃣  清除所有缓存:\n";
echo "   php artisan config:clear\n";
echo "   php artisan route:clear\n";
echo "   php artisan cache:clear\n\n";
echo "4️⃣  查看注册的路由:\n";
echo "   php artisan route:list | grep enums\n\n";
echo "5️⃣  检查 approval-statuses 路由:\n";
echo "   php artisan route:list | grep approval\n\n";
echo "6️⃣  手动测试 EnumRegistry:\n";
echo "   php artisan tinker\n";
echo "   >>> \\WeiJuKeJi\\EnumOptions\\Support\\EnumRegistry::all()\n\n";
echo "7️⃣  如果是新安装的包，需要重新加载自动发现:\n";
echo "   composer dump-autoload\n\n";

echo "=================================\n";
echo "诊断完成\n";
echo "=================================\n";
