<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        DB::table('settings')->truncate();

        $settings = [
            ['group_name' => 'config', 'key_name' => 'webmaster_email', 'value' => 'info@example.com', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['group_name' => 'fr', 'key_name' => 'website_title', 'value' => 'Site web sans titre', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['group_name' => 'fr', 'key_name' => 'status', 'value' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['group_name' => 'nl', 'key_name' => 'website_title', 'value' => 'Untitled website', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['group_name' => 'nl', 'key_name' => 'status', 'value' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['group_name' => 'en', 'key_name' => 'website_title', 'value' => 'Untitled website', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['group_name' => 'en', 'key_name' => 'status', 'value' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['group_name' => 'config', 'key_name' => 'welcome_message', 'value' => 'Welcome to the administration panel of TypiCMS.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['group_name' => 'config', 'key_name' => 'auth_public', 'value' => 0, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['group_name' => 'config', 'key_name' => 'register', 'value' => 0, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ];

        DB::table('settings')->insert($settings);
    }
}
