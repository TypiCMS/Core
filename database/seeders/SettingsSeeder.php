<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('settings')->truncate();

        $settings = [
            [
                'group_name' => 'fr',
                'key_name' => 'website_title',
                'value' => 'Site web sans titre',
                'created_at' => Date::now(),
                'updated_at' => Date::now(),
            ],
            [
                'group_name' => 'fr',
                'key_name' => 'status',
                'value' => '1',
                'created_at' => Date::now(),
                'updated_at' => Date::now(),
            ],
            [
                'group_name' => 'nl',
                'key_name' => 'website_title',
                'value' => 'Untitled website',
                'created_at' => Date::now(),
                'updated_at' => Date::now(),
            ],
            [
                'group_name' => 'nl',
                'key_name' => 'status',
                'value' => '1',
                'created_at' => Date::now(),
                'updated_at' => Date::now(),
            ],
            [
                'group_name' => 'en',
                'key_name' => 'website_title',
                'value' => 'Untitled website',
                'created_at' => Date::now(),
                'updated_at' => Date::now(),
            ],
            [
                'group_name' => 'en',
                'key_name' => 'status',
                'value' => '1',
                'created_at' => Date::now(),
                'updated_at' => Date::now(),
            ],
            [
                'group_name' => 'config',
                'key_name' => 'welcome_message',
                'value' => 'Welcome to the administration panel of TypiCMS.',
                'created_at' => Date::now(),
                'updated_at' => Date::now(),
            ],
            [
                'group_name' => 'config',
                'key_name' => 'auth_public',
                'value' => 0,
                'created_at' => Date::now(),
                'updated_at' => Date::now(),
            ],
            [
                'group_name' => 'config',
                'key_name' => 'register',
                'value' => 0,
                'created_at' => Date::now(),
                'updated_at' => Date::now(),
            ],
        ];

        DB::table('settings')->insert($settings);
    }
}
