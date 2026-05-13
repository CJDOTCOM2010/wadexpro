<?php

namespace Database\Seeders;

use App\Modules\Admin\Models\SystemSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;

class PaymentGatewaySettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Active Gateway Selection
            [
                'key' => 'active_payment_gateway',
                'value' => 'paystack',
                'type' => 'string',
                'group' => 'payments',
                'label' => 'Primary Payment Gateway',
                'tooltip' => 'Select which gateway the platform will use to process customer payments.',
                'is_public' => true,
                'is_encrypted' => false,
            ],

            // Paystack Configuration
            [
                'key' => 'paystack_public_key',
                'value' => 'pk_test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
                'type' => 'string',
                'group' => 'payments',
                'label' => 'Paystack Public Key',
                'tooltip' => 'Your Paystack integration public key.',
                'is_public' => true,
                'is_encrypted' => false,
            ],
            [
                'key' => 'paystack_secret_key',
                'value' => Crypt::encryptString('sk_test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'),
                'type' => 'string',
                'group' => 'payments',
                'label' => 'Paystack Secret Key',
                'tooltip' => 'Your Paystack integration secret key (Encrypted at rest).',
                'is_public' => false,
                'is_encrypted' => true,
            ],

            // Flutterwave Configuration
            [
                'key' => 'flutterwave_public_key',
                'value' => 'FLWPUBK_TEST-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-X',
                'type' => 'string',
                'group' => 'payments',
                'label' => 'Flutterwave Public Key',
                'tooltip' => 'Your Flutterwave integration public key.',
                'is_public' => true,
                'is_encrypted' => false,
            ],
            [
                'key' => 'flutterwave_secret_key',
                'value' => Crypt::encryptString('FLWSECK_TEST-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-X'),
                'type' => 'string',
                'group' => 'payments',
                'label' => 'Flutterwave Secret Key',
                'tooltip' => 'Your Flutterwave integration secret key (Encrypted at rest).',
                'is_public' => false,
                'is_encrypted' => true,
            ],

            // Stripe Configuration
            [
                'key' => 'stripe_public_key',
                'value' => 'pk_test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
                'type' => 'string',
                'group' => 'payments',
                'label' => 'Stripe Publishable Key',
                'tooltip' => 'Your Stripe integration publishable key.',
                'is_public' => true,
                'is_encrypted' => false,
            ],
            [
                'key' => 'stripe_secret_key',
                'value' => Crypt::encryptString('sk_test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'),
                'type' => 'string',
                'group' => 'payments',
                'label' => 'Stripe Secret Key',
                'tooltip' => 'Your Stripe integration secret key (Encrypted at rest).',
                'is_public' => false,
                'is_encrypted' => true,
            ],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
