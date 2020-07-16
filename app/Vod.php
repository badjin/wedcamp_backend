<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vod extends Model
{
    protected $casts = [
        'tags' => 'array',
    ];

    protected $fillable = ['tags', 'title', 'video_id', 'video_url'];
}
