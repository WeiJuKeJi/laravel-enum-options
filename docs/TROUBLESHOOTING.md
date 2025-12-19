# 🔧 404 故障排查指南

如果你遇到枚举路由 404 错误（如 `api/enums/approval-statuses` 找不到），请按以下步骤排查：

## 🚀 快速解决（推荐）

在 Laravel 项目根目录执行以下命令：

```bash
# 1. 清除所有缓存
php artisan config:clear && php artisan route:clear && php artisan cache:clear

# 2. 查看枚举路由是否注册成功
php artisan route:list | grep enums

# 3. 如果看不到路由，发布配置文件
php artisan vendor:publish --tag=enum-options-config

# 4. 确认配置正确
cat config/enum-options.php | grep auto_register_routes
# 应该看到: 'auto_register_routes' => true

# 5. 再次清除缓存
php artisan config:clear && php artisan route:clear
```

## 📋 详细排查步骤

### 1️⃣ 检查配置文件是否存在

```bash
ls -la config/enum-options.php
```

**如果不存在，运行**：
```bash
php artisan vendor:publish --tag=enum-options-config
```

### 2️⃣ 确认配置正确

打开 `config/enum-options.php`，确认以下配置：

```php
'auto_register_routes' => true,  // ✅ 必须为 true
'route_prefix' => 'api/enums',   // ✅ 路由前缀
'route_middleware' => ['api'],   // ✅ 中间件
'auto_discover_presets' => true, // ✅ 自动发现预设枚举
```

### 3️⃣ 清除所有缓存

```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan optimize:clear  # Laravel 8+
```

### 4️⃣ 验证路由注册

```bash
# 查看所有枚举路由
php artisan route:list | grep enums

# 应该看到类似输出：
# GET|HEAD  api/enums/list ................... enums.list
# GET|HEAD  api/enums/all .................... enums.all
# GET|HEAD  api/enums/approval-statuses ..... enums.approval_statuses
# GET|HEAD  api/enums/payment-methods ....... enums.payment_methods
# ...
```

### 5️⃣ 检查 ServiceProvider 是否加载

**Laravel 11+**（检查 `bootstrap/providers.php`）：
```php
<?php
return [
    // ...
    WeiJuKeJi\EnumOptions\EnumOptionsServiceProvider::class,
];
```

**Laravel 10 及以下**（检查 `config/app.php`）：
```php
'providers' => [
    // ...
    WeiJuKeJi\EnumOptions\EnumOptionsServiceProvider::class,
],
```

**如果没有找到**，可能是自动发现失效，手动添加即可。

### 6️⃣ 测试 EnumRegistry

```bash
php artisan tinker
```

在 Tinker 中执行：
```php
\WeiJuKeJi\EnumOptions\Support\EnumRegistry::all()
```

应该看到所有已注册的枚举列表，包括 `approval_statuses`。

### 7️⃣ 重新加载自动发现

```bash
composer dump-autoload
```

## 🐛 使用诊断脚本

我们提供了一个诊断脚本，可以自动检查所有配置：

```bash
php vendor/weijukeji/laravel-enum-options/debug-routes.php
```

脚本会检查：
- ✅ 配置文件是否存在
- ✅ ServiceProvider 是否注册
- ✅ 预设枚举文件是否完整
- ✅ EnumRegistry 是否正常工作
- ✅ 提供具体的解决建议

## 🎯 常见原因

| 问题 | 原因 | 解决方案 |
|------|------|----------|
| 路由 404 | 配置缓存未清除 | `php artisan config:clear && php artisan route:clear` |
| 路由 404 | `auto_register_routes` 为 false | 在 `config/enum-options.php` 中改为 true |
| 路由 404 | 配置文件未发布 | `php artisan vendor:publish --tag=enum-options-config` |
| 路由 404 | ServiceProvider 未加载 | 检查 `bootstrap/providers.php` 或运行 `composer dump-autoload` |
| 枚举不存在 | EnumRegistry 未发现枚举 | 确认 `auto_discover_presets` 为 true |

## 📞 仍然无法解决？

1. 运行诊断脚本并提供输出：
   ```bash
   php vendor/weijukeji/laravel-enum-options/debug-routes.php > debug-output.txt
   ```

2. 检查 Laravel 版本兼容性：
   - Laravel 10.x+：✅ 完全支持
   - Laravel 8.x-9.x：✅ 支持
   - Laravel 7.x 及以下：⚠️ 需要手动注册

3. 提供以下信息以便排查：
   - Laravel 版本：`php artisan --version`
   - PHP 版本：`php -v`
   - 包版本：`composer show weijukeji/laravel-enum-options`
   - 诊断脚本输出
   - `php artisan route:list | grep enums` 的输出

## ✅ 验证修复

修复后，测试路由：

```bash
# 1. 测试枚举列表
curl -H "Accept: application/json" \
     -H "Authorization: Bearer YOUR_TOKEN" \
     https://your-api.com/api/enums/list

# 2. 测试单个枚举
curl -H "Accept: application/json" \
     -H "Authorization: Bearer YOUR_TOKEN" \
     https://your-api.com/api/enums/approval-statuses
```

应该返回正常的 JSON 响应，而不是 404 错误。
