@extends('admin.layout')
@section('title', 'Localization Settings')

@php
$locales = $settings ?? collect();
$defaultLang = $locales->get('default_language')?->castValue() ?? 'en';
$supportedLangs = $locales->get('supported_languages')?->castValue() ?? ['en'];
$defaultCurrency = $locales->get('default_currency')?->castValue() ?? 'GHS';
$supportedCurrencies = $locales->get('supported_currencies')?->castValue() ?? ['GHS'];
$defaultTimezone = $locales->get('default_timezone')?->castValue() ?? 'Africa/Accra';
$dateFormat = $locales->get('date_format')?->castValue() ?? 'Y-m-d';
$timeFormat = $locales->get('time_format')?->castValue() ?? 'H:i';
$measurementUnit = $locales->get('measurement_unit')?->castValue() ?? 'km';
$firstDayOfWeek = $locales->get('first_day_of_week')?->castValue() ?? 'monday';
$country = $locales->get('country')?->castValue() ?? 'GH';
$decimalSep = $locales->get('decimal_separator')?->castValue() ?? '.';
$thousandsSep = $locales->get('thousands_separator')?->castValue() ?? ',';
$currencyPosition = $locales->get('currency_position')?->castValue() ?? 'before';
@endphp

@section('content')
<div class="p-8 lg:p-12 max-w-5xl mx-auto">
    <form method="POST" action="{{ route('orchestrator.settings.update') }}">
        @csrf
        <input type="hidden" name="settings[localization_configured]" value="true">

        <div class="flex items-center justify-between mb-12">
            <div>
                <div class="flex items-center gap-2 text-[10px] font-black text-accent uppercase tracking-[0.2em] mb-2">
                    <a href="{{ route('orchestrator.settings') }}" class="hover:text-brand transition-colors">Settings Hub</a>
                    <span class="text-gray-300">/</span>
                    <span>Localization</span>
                </div>
                <h2 class="text-3xl font-black text-brand tracking-tight">Regional Localization</h2>
                <p class="text-brand-muted font-medium mt-1">Configure language, currency, timezone, and formatting for your regions.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('orchestrator.settings') }}" class="bg-surface text-brand-muted hover:bg-gray-100 px-6 py-3 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back
                </a>
                <button type="submit" class="bg-brand text-white hover:bg-brand-light px-8 py-3 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Save Changes
                </button>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-8 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
        </div>
        @endif

        <div class="space-y-8">
            <!-- Language & Region -->
            <div class="bg-white rounded-2xl border border-gray-100 p-8 shadow-sm">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-accent/10 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-brand">Language & Region</h3>
                        <p class="text-xs text-brand-muted">Default language, supported locales, and country settings.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-brand mb-2">Default Language</label>
                        <select name="settings[default_language]" class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-medium text-brand outline-none focus:ring-2 focus:ring-accent/20">
                            <option value="en" {{ $defaultLang === 'en' ? 'selected' : '' }}>English</option>
                            <option value="fr" {{ $defaultLang === 'fr' ? 'selected' : '' }}>Français</option>
                            <option value="es" {{ $defaultLang === 'es' ? 'selected' : '' }}>Español</option>
                            <option value="pt" {{ $defaultLang === 'pt' ? 'selected' : '' }}>Português</option>
                            <option value="ar" {{ $defaultLang === 'ar' ? 'selected' : '' }}>العربية</option>
                            <option value="sw" {{ $defaultLang === 'sw' ? 'selected' : '' }}>Kiswahili</option>
                            <option value="ha" {{ $defaultLang === 'ha' ? 'selected' : '' }}>Hausa</option>
                            <option value="yo" {{ $defaultLang === 'yo' ? 'selected' : '' }}>Yoruba</option>
                            <option value="ig" {{ $defaultLang === 'ig' ? 'selected' : '' }}>Igbo</option>
                            <option value="ak" {{ $defaultLang === 'ak' ? 'selected' : '' }}>Akan/Twi</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-brand mb-2">Country / Region</label>
                        <select name="settings[country]" class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-medium text-brand outline-none focus:ring-2 focus:ring-accent/20">
                            <option value="GH" {{ $country === 'GH' ? 'selected' : '' }}>Ghana</option>
                            <option value="NG" {{ $country === 'NG' ? 'selected' : '' }}>Nigeria</option>
                            <option value="KE" {{ $country === 'KE' ? 'selected' : '' }}>Kenya</option>
                            <option value="ZA" {{ $country === 'ZA' ? 'selected' : '' }}>South Africa</option>
                            <option value="TZ" {{ $country === 'TZ' ? 'selected' : '' }}>Tanzania</option>
                            <option value="UG" {{ $country === 'UG' ? 'selected' : '' }}>Uganda</option>
                            <option value="RW" {{ $country === 'RW' ? 'selected' : '' }}>Rwanda</option>
                            <option value="CI" {{ $country === 'CI' ? 'selected' : '' }}>Côte d'Ivoire</option>
                            <option value="SN" {{ $country === 'SN' ? 'selected' : '' }}>Senegal</option>
                            <option value="CM" {{ $country === 'CM' ? 'selected' : '' }}>Cameroon</option>
                            <option value="US" {{ $country === 'US' ? 'selected' : '' }}>United States</option>
                            <option value="GB" {{ $country === 'GB' ? 'selected' : '' }}>United Kingdom</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-brand mb-2">Supported Languages</label>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                            @php $langs = ['en' => 'English', 'fr' => 'Français', 'es' => 'Español', 'pt' => 'Português', 'ar' => 'العربية', 'sw' => 'Kiswahili', 'ha' => 'Hausa', 'yo' => 'Yoruba', 'ig' => 'Igbo', 'ak' => 'Akan/Twi']; @endphp
                            @foreach($langs as $code => $name)
                            <label class="flex items-center gap-3 p-3 bg-surface rounded-lg cursor-pointer hover:bg-accent/5 transition-colors {{ in_array($code, $supportedLangs ?? []) ? 'ring-2 ring-accent' : '' }}">
                                <input type="checkbox" name="settings[supported_languages][]" value="{{ $code }}" {{ in_array($code, $supportedLangs ?? []) ? 'checked' : '' }} class="w-4 h-4 text-accent rounded border-gray-300 focus:ring-accent">
                                <span class="text-xs font-bold text-brand">{{ $name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Currency & Pricing -->
            <div class="bg-white rounded-2xl border border-gray-100 p-8 shadow-sm">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-brand">Currency & Pricing</h3>
                        <p class="text-xs text-brand-muted">Default currency, supported currencies, and display formatting.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-brand mb-2">Default Currency</label>
                        <select name="settings[default_currency]" class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-medium text-brand outline-none focus:ring-2 focus:ring-accent/20">
                            <option value="GHS" {{ $defaultCurrency === 'GHS' ? 'selected' : '' }}>GHS — Ghana Cedi (₵)</option>
                            <option value="NGN" {{ $defaultCurrency === 'NGN' ? 'selected' : '' }}>NGN — Nigerian Naira (₦)</option>
                            <option value="KES" {{ $defaultCurrency === 'KES' ? 'selected' : '' }}>KES — Kenyan Shilling (KSh)</option>
                            <option value="ZAR" {{ $defaultCurrency === 'ZAR' ? 'selected' : '' }}>ZAR — South African Rand (R)</option>
                            <option value="TZS" {{ $defaultCurrency === 'TZS' ? 'selected' : '' }}>TZS — Tanzanian Shilling (TSh)</option>
                            <option value="UGX" {{ $defaultCurrency === 'UGX' ? 'selected' : '' }}>UGX — Ugandan Shilling (USh)</option>
                            <option value="XOF" {{ $defaultCurrency === 'XOF' ? 'selected' : '' }}>XOF — CFA Franc (CFA)</option>
                            <option value="USD" {{ $defaultCurrency === 'USD' ? 'selected' : '' }}>USD — US Dollar ($)</option>
                            <option value="EUR" {{ $defaultCurrency === 'EUR' ? 'selected' : '' }}>EUR — Euro (€)</option>
                            <option value="GBP" {{ $defaultCurrency === 'GBP' ? 'selected' : '' }}>GBP — British Pound (£)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-brand mb-2">Currency Position</label>
                        <select name="settings[currency_position]" class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-medium text-brand outline-none focus:ring-2 focus:ring-accent/20">
                            <option value="before" {{ $currencyPosition === 'before' ? 'selected' : '' }}>Before amount (₵100)</option>
                            <option value="after" {{ $currencyPosition === 'after' ? 'selected' : '' }}>After amount (100₵)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-brand mb-2">Decimal Separator</label>
                        <select name="settings[decimal_separator]" class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-medium text-brand outline-none focus:ring-2 focus:ring-accent/20">
                            <option value="." {{ $decimalSep === '.' ? 'selected' : '' }}>Period (.)</option>
                            <option value="," {{ $decimalSep === ',' ? 'selected' : '' }}>Comma (,)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-brand mb-2">Thousands Separator</label>
                        <select name="settings[thousands_separator]" class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-medium text-brand outline-none focus:ring-2 focus:ring-accent/20">
                            <option value="," {{ $thousandsSep === ',' ? 'selected' : '' }}>Comma (,)</option>
                            <option value="." {{ $thousandsSep === '.' ? 'selected' : '' }}>Period (.)</option>
                            <option value=" " {{ $thousandsSep === ' ' ? 'selected' : '' }}>Space ( )</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-brand mb-2">Supported Currencies</label>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                            @php $currencies = ['GHS' => 'Ghana Cedi', 'NGN' => 'Nigerian Naira', 'KES' => 'Kenyan Shilling', 'ZAR' => 'South African Rand', 'TZS' => 'Tanzanian Shilling', 'UGX' => 'Ugandan Shilling', 'XOF' => 'CFA Franc', 'USD' => 'US Dollar', 'EUR' => 'Euro', 'GBP' => 'British Pound']; @endphp
                            @foreach($currencies as $code => $name)
                            <label class="flex items-center gap-3 p-3 bg-surface rounded-lg cursor-pointer hover:bg-green-50 transition-colors {{ in_array($code, $supportedCurrencies ?? []) ? 'ring-2 ring-green-500' : '' }}">
                                <input type="checkbox" name="settings[supported_currencies][]" value="{{ $code }}" {{ in_array($code, $supportedCurrencies ?? []) ? 'checked' : '' }} class="w-4 h-4 text-green-600 rounded border-gray-300 focus:ring-green-500">
                                <span class="text-xs font-bold text-brand">{{ $code }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Time & Date -->
            <div class="bg-white rounded-2xl border border-gray-100 p-8 shadow-sm">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-brand">Time & Date</h3>
                        <p class="text-xs text-brand-muted">Timezone, date/time display format, and calendar settings.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-brand mb-2">Default Timezone</label>
                        <select name="settings[default_timezone]" class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-medium text-brand outline-none focus:ring-2 focus:ring-accent/20">
                            <optgroup label="Africa">
                                <option value="Africa/Accra" {{ $defaultTimezone === 'Africa/Accra' ? 'selected' : '' }}>Africa/Accra (GMT+0)</option>
                                <option value="Africa/Lagos" {{ $defaultTimezone === 'Africa/Lagos' ? 'selected' : '' }}>Africa/Lagos (GMT+1)</option>
                                <option value="Africa/Nairobi" {{ $defaultTimezone === 'Africa/Nairobi' ? 'selected' : '' }}>Africa/Nairobi (GMT+3)</option>
                                <option value="Africa/Johannesburg" {{ $defaultTimezone === 'Africa/Johannesburg' ? 'selected' : '' }}>Africa/Johannesburg (GMT+2)</option>
                                <option value="Africa/Dar_es_Salaam" {{ $defaultTimezone === 'Africa/Dar_es_Salaam' ? 'selected' : '' }}>Africa/Dar es Salaam (GMT+3)</option>
                                <option value="Africa/Kampala" {{ $defaultTimezone === 'Africa/Kampala' ? 'selected' : '' }}>Africa/Kampala (GMT+3)</option>
                                <option value="Africa/Kigali" {{ $defaultTimezone === 'Africa/Kigali' ? 'selected' : '' }}>Africa/Kigali (GMT+2)</option>
                                <option value="Africa/Abidjan" {{ $defaultTimezone === 'Africa/Abidjan' ? 'selected' : '' }}>Africa/Abidjan (GMT+0)</option>
                                <option value="Africa/Douala" {{ $defaultTimezone === 'Africa/Douala' ? 'selected' : '' }}>Africa/Douala (GMT+1)</option>
                                <option value="Africa/Dakar" {{ $defaultTimezone === 'Africa/Dakar' ? 'selected' : '' }}>Africa/Dakar (GMT+0)</option>
                            </optgroup>
                            <optgroup label="Global">
                                <option value="UTC" {{ $defaultTimezone === 'UTC' ? 'selected' : '' }}>UTC</option>
                                <option value="America/New_York" {{ $defaultTimezone === 'America/New_York' ? 'selected' : '' }}>America/New York (GMT-5)</option>
                                <option value="Europe/London" {{ $defaultTimezone === 'Europe/London' ? 'selected' : '' }}>Europe/London (GMT+0)</option>
                            </optgroup>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-brand mb-2">First Day of Week</label>
                        <select name="settings[first_day_of_week]" class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-medium text-brand outline-none focus:ring-2 focus:ring-accent/20">
                            <option value="monday" {{ $firstDayOfWeek === 'monday' ? 'selected' : '' }}>Monday</option>
                            <option value="sunday" {{ $firstDayOfWeek === 'sunday' ? 'selected' : '' }}>Sunday</option>
                            <option value="saturday" {{ $firstDayOfWeek === 'saturday' ? 'selected' : '' }}>Saturday</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-brand mb-2">Date Format</label>
                        <select name="settings[date_format]" class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-medium text-brand outline-none focus:ring-2 focus:ring-accent/20">
                            <option value="Y-m-d" {{ $dateFormat === 'Y-m-d' ? 'selected' : '' }}>2026-05-16 (Y-m-d)</option>
                            <option value="d/m/Y" {{ $dateFormat === 'd/m/Y' ? 'selected' : '' }}>16/05/2026 (d/m/Y)</option>
                            <option value="m/d/Y" {{ $dateFormat === 'm/d/Y' ? 'selected' : '' }}>05/16/2026 (m/d/Y)</option>
                            <option value="d M Y" {{ $dateFormat === 'd M Y' ? 'selected' : '' }}>16 May 2026 (d M Y)</option>
                            <option value="M d, Y" {{ $dateFormat === 'M d, Y' ? 'selected' : '' }}>May 16, 2026 (M d, Y)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-brand mb-2">Time Format</label>
                        <select name="settings[time_format]" class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-medium text-brand outline-none focus:ring-2 focus:ring-accent/20">
                            <option value="H:i" {{ $timeFormat === 'H:i' ? 'selected' : '' }}>14:30 (24-hour)</option>
                            <option value="h:i A" {{ $timeFormat === 'h:i A' ? 'selected' : '' }}>2:30 PM (12-hour)</option>
                            <option value="H:i:s" {{ $timeFormat === 'H:i:s' ? 'selected' : '' }}>14:30:00 (24-hour with seconds)</option>
                            <option value="h:i:s A" {{ $timeFormat === 'h:i:s A' ? 'selected' : '' }}>2:30:00 PM (12-hour with seconds)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Measurement & Formatting -->
            <div class="bg-white rounded-2xl border border-gray-100 p-8 shadow-sm">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-brand">Measurement & Formatting</h3>
                        <p class="text-xs text-brand-muted">Distance units and number formatting preferences.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-brand mb-2">Distance Measurement Unit</label>
                        <div class="flex gap-4">
                            <label class="flex-1 flex items-center justify-center gap-3 p-4 bg-surface rounded-lg cursor-pointer hover:bg-purple-50 transition-colors {{ $measurementUnit === 'km' ? 'ring-2 ring-purple-500 bg-purple-50' : '' }}">
                                <input type="radio" name="settings[measurement_unit]" value="km" {{ $measurementUnit === 'km' ? 'checked' : '' }} class="w-4 h-4 text-purple-600 focus:ring-purple-500">
                                <div class="text-center">
                                    <span class="text-sm font-black text-brand">Kilometers</span>
                                    <p class="text-[10px] text-brand-muted">Metric system</p>
                                </div>
                            </label>
                            <label class="flex-1 flex items-center justify-center gap-3 p-4 bg-surface rounded-lg cursor-pointer hover:bg-purple-50 transition-colors {{ $measurementUnit === 'miles' ? 'ring-2 ring-purple-500 bg-purple-50' : '' }}">
                                <input type="radio" name="settings[measurement_unit]" value="miles" {{ $measurementUnit === 'miles' ? 'checked' : '' }} class="w-4 h-4 text-purple-600 focus:ring-purple-500">
                                <div class="text-center">
                                    <span class="text-sm font-black text-brand">Miles</span>
                                    <p class="text-[10px] text-brand-muted">Imperial system</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-brand mb-2">Preview</label>
                        <div class="p-4 bg-surface rounded-lg">
                            <p class="text-sm font-bold text-brand">{{ number_format(1234567.89, 2, $decimalSep, $thousandsSep) }} {{ $defaultCurrency }}</p>
                            <p class="text-[10px] text-brand-muted mt-1">Live preview based on current settings</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-end gap-4">
            <a href="{{ route('orchestrator.settings') }}" class="px-8 py-3.5 bg-surface text-brand-muted hover:bg-gray-100 rounded-lg text-xs font-bold transition-all">Cancel</a>
            <button type="submit" class="px-10 py-3.5 bg-brand text-white hover:bg-brand-light rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Save Localization Settings
            </button>
        </div>
    </form>
</div>
@endsection