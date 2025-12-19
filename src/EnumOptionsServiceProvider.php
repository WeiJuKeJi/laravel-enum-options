<?php

namespace WeiJuKeJi\EnumOptions;

use Illuminate\Support\ServiceProvider;
use WeiJuKeJi\EnumOptions\Commands\ListPresetsCommand;
use WeiJuKeJi\EnumOptions\Commands\MakeEnumCommand;
use WeiJuKeJi\EnumOptions\Commands\PublishEnumCommand;

class EnumOptionsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // 合并配置
        $this->mergeConfigFrom(
            __DIR__.'/../config/enum-options.php',
            'enum-options'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // 发布配置文件
        $this->publishes([
            __DIR__.'/../config/enum-options.php' => config_path('enum-options.php'),
        ], 'enum-options-config');

        // 发布翻译文件
        $this->publishes([
            __DIR__.'/../lang' => $this->app->langPath('vendor/enum-options'),
        ], 'enum-options-lang');

        // 加载翻译文件
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'enum-options');

        // 注册命令
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeEnumCommand::class,
                PublishEnumCommand::class,
                ListPresetsCommand::class,
            ]);
        }

        // 自动注册路由（如果配置启用）
        if (config('enum-options.auto_register_routes', false)) {
            $this->registerRoutes();
        }
    }

    /**
     * 注册路由
     * 动态为所有已注册的枚举创建路由
     */
    protected function registerRoutes(): void
    {
        $prefix = config('enum-options.route_prefix', 'api/enums');
        $middleware = config('enum-options.route_middleware', []);
        $namePrefix = config('enum-options.route_name_prefix', 'enums');

        $this->app['router']
            ->prefix($prefix)
            ->middleware($middleware)
            ->name("{$namePrefix}.")
            ->group(function ($router) {
                // 枚举列表端点（元数据）
                $router->get('list', [\WeiJuKeJi\EnumOptions\Http\Controllers\EnumController::class, 'list'])
                    ->name('list');

                // 所有枚举一次性获取
                $router->get('all', [\WeiJuKeJi\EnumOptions\Http\Controllers\EnumController::class, 'all'])
                    ->name('all');

                // 动态注册每个枚举的单独路由
                $enums = \WeiJuKeJi\EnumOptions\Support\EnumRegistry::all();

                foreach ($enums as $key => $config) {
                    // 将 snake_case key 转换为 kebab-case URL
                    $route = \Illuminate\Support\Str::slug($key, '-');

                    $router->get($route, [\WeiJuKeJi\EnumOptions\Http\Controllers\EnumController::class, 'show'])
                        ->defaults('key', $key)
                        ->name($key);
                }
            });
    }
}
