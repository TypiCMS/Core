<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['id' => 1, 'name' => 'administrator', 'guard_name' => 'web', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2, 'name' => 'visitor', 'guard_name' => 'web', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ];

        DB::table('roles')->insert($roles);
    }
}
