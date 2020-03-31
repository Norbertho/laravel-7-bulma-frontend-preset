<?php
namespace LaravelFrontendPresets\BulmaPreset;

use Illuminate\Support\ServiceProvider;
use Laravel\Ui\UiCommand;
use Laravel\Ui\AuthCommand;

class BulmaPresetServiceProvider extends ServiceProvider
{
    public function boot()
    {
        UiCommand::macro('bulma', function ($command) {
            TailwindBulmaPresetCssPreset::install();

            $command->info('Bulma scaffolding installed successfully.');

            if ($command->option('auth')) {
                BulmaPreset::installAuth();

                $command->info('Bulma auth scaffolding installed successfully.');
            }

            $command->comment('Please run "npm install && npm run dev" to compile your fresh scaffolding.');
        });
    }
}
