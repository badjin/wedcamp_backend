<?php

use App\Playlist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class SamplePlaylist extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (range(1, 100) as $index) {
            $date = Carbon::create(2018, 5, 9, 0, 0, 0);

            Playlist::create([
                'episode' => $index,
                'song_list' => '',
                'on_air'  => $date->addWeeks($index)->format('Y-m-d')
            ]);
        }
    }
}
