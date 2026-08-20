<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleAccountSeeder extends Seeder
{
    public function run(): void
    {
        $maternity = Unit::create(['name' => 'Maternity', 'code' => 'MAT']);
        $nicu = Unit::create(['name' => 'Neonatal ICU', 'code' => 'NICU', 'has_evening_shift' => true, 'has_night_shift' => true]);
        $general = Unit::create(['name' => 'General Ward', 'code' => 'GEN', 'has_evening_shift' => false, 'has_night_shift' => false]);

        User::create([
            'name' => 'System Admin',
            'email' => 'admin@mswnh.local',
            'password' => Hash::make('Admin123'),
            'role' => User::ROLE_ADMIN,
        ]);

        User::create([
            'name' => 'Head Nursing',
            'email' => 'head.nursing@mswnh.local',
            'password' => Hash::make('HNurse123'),
            'role' => User::ROLE_HEAD_NURSING,
        ]);

        User::create([
            'name' => 'Area Manager',
            'email' => 'area.manager@mswnh.local',
            'password' => Hash::make('AreaManager123'),
            'role' => User::ROLE_AREA_MANAGER,
        ]);

        User::create([
            'name' => 'Maternity In-Charge',
            'email' => 'incharge.maternity@mswnh.local',
            'password' => Hash::make('InCharge123'),
            'role' => User::ROLE_IN_CHARGE,
            'unit_id' => $maternity->id,
        ]);

        User::create([
            'name' => 'NICU In-Charge',
            'email' => 'incharge.nicu@mswnh.local',
            'password' => Hash::make('InCharge123'),
            'role' => User::ROLE_IN_CHARGE,
            'unit_id' => $nicu->id,
        ]);

        User::create([
            'name' => 'General Ward In-Charge',
            'email' => 'incharge.general@mswnh.local',
            'password' => Hash::make('InCharge123'),
            'role' => User::ROLE_IN_CHARGE,
            'unit_id' => $general->id,
        ]);
    }
}
