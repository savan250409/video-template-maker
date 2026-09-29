<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'one_signal_app_id', 'value' => ''],
            ['key' => 'one_signal_rest_key', 'value' => ''],
            ['key' => 'ad_status', 'value' => 'on'],
            ['key' => 'ad_native_count', 'value' => '3'],
            ['key' => 'ad_inter_item', 'value' => '5'],
            ['key' => 'banner_ad_id', 'value' => 'ca-app-pub-3940256099942544/6300978111'],
            ['key' => 'interstital_ad_id', 'value' => 'ca-app-pub-3940256099942544/1033173712'],
            ['key' => 'native_ad_id', 'value' => 'ca-app-pub-3940256099942544/2247696110'],
            ['key' => 'reward_ad_id', 'value' => 'ca-app-pub-3940256099942544/5224354917'],
            ['key' => 'app_open_id', 'value' => 'ca-app-pub-9018175839730307/3810034255'],
            ['key' => 'api_authorization', 'value' => Str::random(32)],
            ['key' => 'app_update_popup', 'value' => 'off'],
            ['key' => 'app_update_version', 'value' => '1'],
            ['key' => 'app_update_description', 'value' => '<p>Initial Release. Install App Now.</p>'],
            ['key' => 'app_update_app_link', 'value' => 'https://play.google.com/store/apps/details?id='],
            ['key' => 'app_update_cancel_option', 'value' => 'off'],
            ['key' => 'banner_ad_id_status', 'value' => 'on'],
            ['key' => 'interstital_ad_id_status', 'value' => 'on'],
            ['key' => 'native_ad_id_status', 'value' => 'on'],
            ['key' => 'reward_ad_id_status', 'value' => 'on'],
            ['key' => 'app_open_id_status', 'value' => 'on'],
            ['key' => 'home', 'value' => '-1'],
            ['key' => 'privacy', 'value' => '<p><strong>Privacy Policy</strong></p><p>Please customize this policy for your application.</p>'],
            ['key' => 'terms', 'value' => '<p><strong>Terms and Conditions</strong></p><p>Please customize these terms for your application.</p>'],
            ['key' => 'refund', 'value' => '<p><strong>Refund Policy</strong></p><p>Please customize this policy for your application.</p>'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
