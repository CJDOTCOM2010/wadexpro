<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\Admin\Models\SystemSetting;

class PlatformBuildBranding extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'platform:build-branding {appType : The app type (customer or driver)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Outputs the branding configuration as JSON for the mobile build scripts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $appType = $this->argument('appType');

        if (!in_array($appType, ['customer', 'driver'])) {
            $this->error('Invalid app type. Must be customer or driver.');
            return 1;
        }

        $settings = SystemSetting::whereIn('key', [
            "{$appType}_app_display_name",
            "{$appType}_app_icon_url",
            'app_icon_url' // fallback
        ])->pluck('value', 'key');
        
        $appName = $settings["{$appType}_app_display_name"] ?? 'WADEXPRO';
        $appIconUrl = $settings["{$appType}_app_icon_url"] ?? ($settings['app_icon_url'] ?? '');

        // Output JSON for the script to consume
        echo json_encode([
            'appName' => $appName,
            'appIconUrl' => $appIconUrl,
        ]);

        return 0;
    }
}
