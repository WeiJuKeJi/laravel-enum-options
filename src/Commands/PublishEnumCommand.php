<?php

namespace WeiJuKeJi\EnumOptions\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PublishEnumCommand extends Command
{
    protected $signature = 'enum:publish
                            {name? : The name of the preset enum to publish (e.g., PaymentMethod, OrderStatus)}
                            {--all : Publish all preset enums}
                            {--with-translations : Also publish translation files}';

    protected $description = 'Publish preset enum classes to your application';

    protected array $presets = [
        'Payment' => [
            'PaymentMethod' => 'Payment/PaymentMethodEnum.php',
            'PaymentStatus' => 'Payment/PaymentStatusEnum.php',
            'RefundStatus' => 'Payment/RefundStatusEnum.php',
        ],
        'Order' => [
            'OrderStatus' => 'Order/OrderStatusEnum.php',
            'OrderType' => 'Order/OrderTypeEnum.php',
        ],
        'User' => [
            'UserStatus' => 'User/UserStatusEnum.php',
            'Gender' => 'User/GenderEnum.php',
        ],
        'Business' => [
            'ApprovalStatus' => 'Business/ApprovalStatusEnum.php',
            'PublishStatus' => 'Business/PublishStatusEnum.php',
        ],
    ];

    public function handle(): int
    {
        if ($this->option('all')) {
            return $this->publishAll();
        }

        $name = $this->argument('name');

        if (! $name) {
            $this->error('Please specify an enum name or use --all option');
            $this->info('Available presets:');
            $this->listAvailablePresets();

            return self::FAILURE;
        }

        return $this->publishEnum($name);
    }

    protected function publishAll(): int
    {
        $this->info('Publishing all preset enums...');

        $published = 0;
        foreach ($this->presets as $category => $enums) {
            foreach ($enums as $name => $path) {
                if ($this->publishEnum($name, false)) {
                    $published++;
                }
            }
        }

        $this->info("Published {$published} enum(s) successfully!");

        if ($this->option('with-translations')) {
            $this->publishTranslations();
        }

        return self::SUCCESS;
    }

    protected function publishEnum(string $name, bool $showMessages = true): bool
    {
        $sourcePath = $this->findPresetPath($name);

        if (! $sourcePath) {
            if ($showMessages) {
                $this->error("Preset enum '{$name}' not found!");
                $this->info('Available presets:');
                $this->listAvailablePresets();
            }

            return false;
        }

        $destinationPath = app_path("Enums/{$name}Enum.php");

        // 检查目标文件是否已存在
        if (File::exists($destinationPath)) {
            if ($showMessages && ! $this->confirm("File {$destinationPath} already exists. Overwrite?", false)) {
                $this->info('Skipped.');

                return false;
            }
        }

        // 确保目标目录存在
        File::ensureDirectoryExists(app_path('Enums'));

        // 读取源文件内容
        $content = File::get($sourcePath);

        // 替换命名空间
        $content = str_replace(
            'namespace WeiJuKeJi\EnumOptions\Presets',
            'namespace App\Enums',
            $content
        );

        // 写入目标文件
        File::put($destinationPath, $content);

        if ($showMessages) {
            $this->info("Published: {$destinationPath}");

            if ($this->option('with-translations')) {
                $this->publishTranslations();
            }
        }

        return true;
    }

    protected function findPresetPath(string $name): ?string
    {
        foreach ($this->presets as $category => $enums) {
            if (isset($enums[$name])) {
                return __DIR__.'/../../src/Presets/'.$enums[$name];
            }
        }

        return null;
    }

    protected function publishTranslations(): void
    {
        $this->call('vendor:publish', [
            '--tag' => 'enum-options-lang',
            '--force' => true,
        ]);

        $this->info('Translation files published!');
    }

    protected function listAvailablePresets(): void
    {
        foreach ($this->presets as $category => $enums) {
            $this->line("<fg=yellow>{$category}</>");
            foreach (array_keys($enums) as $name) {
                $this->line("  - {$name}");
            }
        }
    }
}
