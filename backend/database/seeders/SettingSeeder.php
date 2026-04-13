<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'site_name', 'value' => 'Kalapak Code Team', 'type' => 'string', 'group' => 'general'],
            ['key' => 'site_tagline', 'value' => 'Coding the Universe Since 2024', 'type' => 'string', 'group' => 'general'],
            ['key' => 'site_description', 'value' => 'A passionate collective of developers from Cambodia building impactful open-source projects.', 'type' => 'string', 'group' => 'general'],
            ['key' => 'contact_email', 'value' => 'kalapakteam@gmail.com', 'type' => 'string', 'group' => 'social'],
            ['key' => 'github_url', 'value' => 'https://github.com/Kalapak-Team', 'type' => 'string', 'group' => 'social'],
            ['key' => 'telegram_url', 'value' => 'https://t.me/kalapakteam', 'type' => 'string', 'group' => 'social'],
            ['key' => 'meta_title', 'value' => 'Kalapak Code Team | Coding the Universe', 'type' => 'string', 'group' => 'seo'],
            ['key' => 'meta_description', 'value' => 'Full-Stack · AI · Cloud · Mobile — Building the Future from Cambodia', 'type' => 'string', 'group' => 'seo'],
            ['key' => 'admin_direct_action', 'value' => '1', 'type' => 'boolean', 'group' => 'permissions'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
