<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $admin = User::where('email', 'killian.lesaint@gmail.com')->first();
        if ($admin) {
            $admin->assignRole(['admin', 'musician']);
        }
    }
}
