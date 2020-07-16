<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    protected $casts = [
        'song_list' => 'array',
    ];

    protected $fillable = [
        'episode',
        'song_list',
        'on_air'
    ];
}
