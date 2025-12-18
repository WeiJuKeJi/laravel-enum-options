<?php

namespace YourVendor\EnumOptions\Commands;

use Illuminate\Console\Command;
use YourVendor\EnumOptions\Presets\Business\ApprovalStatusEnum;
use YourVendor\EnumOptions\Presets\Business\PublishStatusEnum;
use YourVendor\EnumOptions\Presets\Order\OrderStatusEnum;
use YourVendor\EnumOptions\Presets\Order\OrderTypeEnum;
use YourVendor\EnumOptions\Presets\Payment\PaymentMethodEnum;
use YourVendor\EnumOptions\Presets\Payment\PaymentStatusEnum;
use YourVendor\EnumOptions\Presets\Payment\RefundStatusEnum;
use YourVendor\EnumOptions\Presets\User\GenderEnum;
use YourVendor\EnumOptions\Presets\User\UserStatusEnum;

class ListPresetsCommand extends Command
{
    protected $signature = 'enum:list-presets
                            {name? : Show details of a specific preset}
                            {--json : Output as JSON}';

    protected $description = 'List all available preset enums';

    protected array $presets = [
        'Payment' => [
            'PaymentMethod' => PaymentMethodEnum::class,
            'PaymentStatus' => PaymentStatusEnum::class,
            'RefundStatus' => RefundStatusEnum::class,
        ],
        'Order' => [
            'OrderStatus' => OrderStatusEnum::class,
            'OrderType' => OrderTypeEnum::class,
        ],
        'User' => [
            'UserStatus' => UserStatusEnum::class,
            'Gender' => GenderEnum::class,
        ],
        'Business' => [
            'ApprovalStatus' => ApprovalStatusEnum::class,
            'PublishStatus' => PublishStatusEnum::class,
        ],
    ];

    public function handle(): int
    {
        $name = $this->argument('name');

        if ($name) {
            return $this->showPresetDetails($name);
        }

        if ($this->option('json')) {
            return $this->outputJson();
        }

        return $this->listAll();
    }

    protected function listAll(): int
    {
        $this->info('Available Preset Enums:');
        $this->newLine();

        foreach ($this->presets as $category => $enums) {
            $this->line("<fg=yellow>{$category}</>");

            foreach ($enums as $name => $class) {
                $count = count($class::cases());
                $this->line("  - <fg=green>{$name}</> ({$count} values)");
            }

            $this->newLine();
        }

        $this->info('Use "php artisan enum:list-presets {name}" to see details');
        $this->info('Use "php artisan enum:publish {name}" to publish a preset to your app');

        return self::SUCCESS;
    }

    protected function showPresetDetails(string $name): int
    {
        $class = $this->findPresetClass($name);

        if (! $class) {
            $this->error("Preset enum '{$name}' not found!");

            return self::FAILURE;
        }

        $this->info("Details for {$name}Enum:");
        $this->newLine();

        $headers = ['Value', 'Label', 'Color', 'Icon'];
        $rows = [];

        foreach ($class::cases() as $case) {
            $rows[] = [
                $case->value,
                $case->label(),
                $case->color(),
                $case->icon() ?? '-',
            ];
        }

        $this->table($headers, $rows);

        $this->newLine();
        $this->info("Total: ".count($rows).' values');
        $this->info("Publish with: php artisan enum:publish {$name}");

        return self::SUCCESS;
    }

    protected function outputJson(): int
    {
        $output = [];

        foreach ($this->presets as $category => $enums) {
            $output[$category] = [];

            foreach ($enums as $name => $class) {
                $output[$category][$name] = [
                    'class' => $class,
                    'values' => $class::options(),
                ];
            }
        }

        $this->line(json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return self::SUCCESS;
    }

    protected function findPresetClass(string $name): ?string
    {
        foreach ($this->presets as $category => $enums) {
            if (isset($enums[$name])) {
                return $enums[$name];
            }
        }

        return null;
    }
}
