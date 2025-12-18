<?php

namespace YourVendor\EnumOptions\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeEnumCommand extends Command
{
    protected $signature = 'make:enum
                            {name : The name of the enum class}
                            {--values= : Comma-separated list of enum values}
                            {--labels : Generate placeholder labels}
                            {--force : Overwrite existing file}';

    protected $description = 'Create a new enum class with EnumOptions trait';

    public function handle(): int
    {
        $name = $this->argument('name');
        $className = Str::studly($name);

        if (! Str::endsWith($className, 'Enum')) {
            $className .= 'Enum';
        }

        $path = app_path("Enums/{$className}.php");

        // 检查文件是否已存在
        if (File::exists($path) && ! $this->option('force')) {
            $this->error("Enum {$className} already exists!");

            return self::FAILURE;
        }

        // 确保目录存在
        File::ensureDirectoryExists(app_path('Enums'));

        // 获取枚举值
        $values = $this->getEnumValues();

        if (empty($values)) {
            $this->error('Please provide at least one enum value using --values option');
            $this->info('Example: php artisan make:enum OrderStatus --values=pending,completed,cancelled');

            return self::FAILURE;
        }

        // 生成枚举类内容
        $content = $this->generateEnumClass($className, $values);

        // 写入文件
        File::put($path, $content);

        $this->info("Enum created successfully: {$path}");

        return self::SUCCESS;
    }

    protected function getEnumValues(): array
    {
        $valuesOption = $this->option('values');

        if (! $valuesOption) {
            return [];
        }

        return array_map('trim', explode(',', $valuesOption));
    }

    protected function generateEnumClass(string $className, array $values): string
    {
        $enumName = Str::snake(str_replace('Enum', '', $className));
        $withLabels = $this->option('labels');

        // 生成 case 语句
        $cases = collect($values)->map(function ($value) {
            $constant = strtoupper(Str::snake($value));

            return "    case {$constant} = '{$value}';";
        })->implode("\n");

        // 生成 label 方法
        $labelMethod = $this->generateLabelMethod($values, $withLabels);

        return <<<PHP
<?php

namespace App\Enums;

use YourVendor\EnumOptions\Traits\EnumOptions;

/**
 * {$className}
 */
enum {$className}: string
{
    use EnumOptions;

{$cases}

{$labelMethod}

    public function color(): string
    {
        \$configColor = config("enum-options.color_overrides.{$enumName}.{\$this->value}");
        if (\$configColor) {
            return \$configColor;
        }

        return match (\$this) {
            // TODO: Define colors for each case
            default => 'default',
        };
    }
}

PHP;
    }

    protected function generateLabelMethod(array $values, bool $withLabels): string
    {
        if (! $withLabels) {
            return <<<'PHP'
    public function label(): string
    {
        return $this->trans($this->value);
    }
PHP;
        }

        $cases = collect($values)->map(function ($value) {
            $constant = 'self::'.strtoupper(Str::snake($value));
            $label = Str::title(str_replace('_', ' ', $value));

            return "            {$constant} => '{$label}',";
        })->implode("\n");

        return <<<PHP
    public function label(): string
    {
        return \$this->trans(\$this->value, match (\$this) {
{$cases}
        });
    }
PHP;
    }
}
