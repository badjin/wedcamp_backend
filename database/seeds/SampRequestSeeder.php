<?php

use Illuminate\Database\Seeder;

class SampRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\SongRequest::class,100)->create();
    }
}
