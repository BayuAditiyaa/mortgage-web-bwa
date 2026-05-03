<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\House;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'developer']);
        Role::firstOrCreate(['name' => 'lender']);
        Role::firstOrCreate(['name' => 'agent']);
        Role::firstOrCreate(['name' => 'customer']);

        $admin = User::firstOrCreate([
            'email' => 'admin@mortgage.test',
        ], [
            'name' => 'Portfolio Admin',
            'phone' => '0812345678',
            'photo' => null,
            'password' => bcrypt('password')
        ]);

        $developer = User::firstOrCreate([
            'email' => 'developer@mortgage.test',
        ], [
            'name' => 'Tedja Developer',
            'phone' => '0812345679',
            'photo' => null,
            'password' => bcrypt('password')
        ]);

        $customer = User::firstOrCreate([
            'email' => 'customer@mortgage.test',
        ], [
            'name' => 'Demo Customer',
            'phone' => '0812345680',
            'photo' => null,
            'password' => bcrypt('password')
        ]);

        $admin->syncRoles(['admin']);
        $developer->syncRoles(['developer']);
        $customer->syncRoles(['customer']);

        House::whereNull('developer_id')->update(['developer_id' => $developer->id]);
    }
}
