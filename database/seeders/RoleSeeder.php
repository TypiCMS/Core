<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'id' => 1,
                'name' => 'administrator',
                'guard_name' => 'web',
                'created_at' => Date::now(),
                'updated_at' => Date::now(),
            ],
            [
                'id' => 2,
                'name' => 'visitor',
                'guard_name' => 'web',
                'created_at' => Date::now(),
                'updated_at' => Date::now(),
            ],
        ];

        DB::table('roles')->insert($roles);
    }
}
