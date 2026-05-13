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
            // ─── Global Payment Configuration ─────────────────────────────────
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
            [
                'key' => 'payment_environment',
                'value' => 'sandbox',
                'type' => 'string',
                'group' => 'payments',
                'label' => 'Transaction Environment',
                'tooltip' => 'Set the environment mode for all gateways: sandbox (test) or production (live).',
                'is_public' => true,
                'is_encrypted' => false,
            ],
            [
                'key' => 'payment_currency',
                'value' => 'GHS',
                'type' => 'string',
                'group' => 'payments',
                'label' => 'Default Transaction Currency',
                'tooltip' => 'ISO 4217 currency code used for all payment transactions.',
                'is_public' => true,
                'is_encrypted' => false,
            ],
            [
                'key' => 'payment_webhook_secret',
                'value' => Crypt::encryptString('whsec_wadexpro_default_change_me'),
                'type' => 'string',
                'group' => 'payments',
                'label' => 'Webhook Signing Secret',
                'tooltip' => 'HMAC secret for verifying incoming payment webhook signatures.',
                'is_public' => false,
                'is_encrypted' => true,
            ],
            [
                'key' => 'cash_on_delivery_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'payments',
                'label' => 'Cash on Delivery',
                'tooltip' => 'Allow customers to pay with cash at the end of a ride or delivery.',
                'is_public' => true,
                'is_encrypted' => false,
            ],
            [
                'key' => 'wallet_payments_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'payments',
                'label' => 'Wallet Payments',
                'tooltip' => 'Allow customers to pay using their WADEXPRO wallet balance.',
                'is_public' => true,
                'is_encrypted' => false,
            ],

            // ─── Paystack Configuration ───────────────────────────────────────
            [
                'key' => 'paystack_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'payments',
                'label' => 'Paystack Enabled',
                'tooltip' => 'Enable or disable Paystack as an available payment provider.',
                'is_public' => true,
                'is_encrypted' => false,
            ],
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

            // ─── Flutterwave Configuration ────────────────────────────────────
            [
                'key' => 'flutterwave_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'payments',
                'label' => 'Flutterwave Enabled',
                'tooltip' => 'Enable or disable Flutterwave as an available payment provider.',
                'is_public' => true,
                'is_encrypted' => false,
            ],
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
            [
                'key' => 'flutterwave_encryption_key',
                'value' => Crypt::encryptString('FLWSECK_TESTxxxxxxxxxx'),
                'type' => 'string',
                'group' => 'payments',
                'label' => 'Flutterwave Encryption Key',
                'tooltip' => 'Required for card charge encryption on Flutterwave.',
                'is_public' => false,
                'is_encrypted' => true,
            ],

            // ─── Stripe Configuration ─────────────────────────────────────────
            [
                'key' => 'stripe_enabled',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'payments',
                'label' => 'Stripe Enabled',
                'tooltip' => 'Enable or disable Stripe as an available payment provider.',
                'is_public' => true,
                'is_encrypted' => false,
            ],
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

            // ─── Google Pay Configuration ─────────────────────────────────────
            [
                'key' => 'googlepay_enabled',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'payments',
                'label' => 'Google Pay Enabled',
                'tooltip' => 'Enable or disable Google Pay as a payment method on Android devices.',
                'is_public' => true,
                'is_encrypted' => false,
            ],
            [
                'key' => 'googlepay_merchant_id',
                'value' => '',
                'type' => 'string',
                'group' => 'payments',
                'label' => 'Google Pay Merchant ID',
                'tooltip' => 'Your registered Google Pay merchant identifier.',
                'is_public' => true,
                'is_encrypted' => false,
            ],
            [
                'key' => 'googlepay_merchant_name',
                'value' => 'WADEXPRO',
                'type' => 'string',
                'group' => 'payments',
                'label' => 'Google Pay Merchant Name',
                'tooltip' => 'Display name shown to customers during Google Pay checkout.',
                'is_public' => true,
                'is_encrypted' => false,
            ],
            [
                'key' => 'googlepay_gateway_tokenization',
                'value' => 'paystack',
                'type' => 'string',
                'group' => 'payments',
                'label' => 'Google Pay Tokenization Gateway',
                'tooltip' => 'The backend payment gateway used to tokenize Google Pay transactions.',
                'is_public' => true,
                'is_encrypted' => false,
            ],

            // ─── Mobile Money Configuration ───────────────────────────────────
            [
                'key' => 'momo_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'payments',
                'label' => 'Mobile Money Enabled',
                'tooltip' => 'Allow Mobile Money (MTN MoMo, Vodafone Cash, AirtelTigo) as a payment method.',
                'is_public' => true,
                'is_encrypted' => false,
            ],
            [
                'key' => 'momo_provider',
                'value' => 'paystack',
                'type' => 'string',
                'group' => 'payments',
                'label' => 'Mobile Money Processing Gateway',
                'tooltip' => 'Which payment gateway processes Mobile Money transactions.',
                'is_public' => true,
                'is_encrypted' => false,
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
