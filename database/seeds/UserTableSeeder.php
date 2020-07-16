<?php

use App\Role;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        User::truncate();
        DB::table('role_user')->truncate();
        Schema::enableForeignKeyConstraints();

        $adminRole = Role::where('name', 'admin')->first();
//        $producerRole = Role::where('name', 'producer')->first();
//        $staffRole = Role::where('name', 'staff')->first();
//        $memberRole = Role::where('name', 'member')->first();

        $user1 = User::create([
            'name' => 'Admin',
            'avatar_id' => 1,
            'email' => 'admin@test.com',
            'password' => Hash::make('password')
        ]);
        $user1->roles()->attach($adminRole);

//        $user2 = User::create([
//            'name' => 'Producer',
//            'avatar_id' => 2,
//            'email' => 'pd@test.com',
//            'password' => Hash::make('password')
//        ]);
//        $user2->roles()->attach($producerRole);
//
//        $user3 = User::create([
//            'name' => 'Staff1',
//            'avatar_id' => 3,
//            'email' => 'staff1@test.com',
//            'password' => Hash::make('password')
//        ]);
//        $user3->roles()->attach($staffRole);
//
//        $user4 = User::create([
//            'name' => 'Staff2',
//            'avatar_id' => 4,
//            'email' => 'staff2@test.com',
//            'password' => Hash::make('password')
//        ]);
//        $user4->roles()->attach($staffRole);
//
//        $user5 = User::create([
//            'name' => 'Member1',
//            'avatar_id' => 5,
//            'email' => 'member1@test.com',
//            'password' => Hash::make('password')
//        ]);
//        $user5->roles()->attach($memberRole);
//
//        $user6 = User::create([
//            'name' => 'Member2',
//            'avatar_id' => 6,
//            'email' => 'member2@test.com',
//            'password' => Hash::make('password')
//        ]);
//        $user6->roles()->attach($memberRole);
    }
}
