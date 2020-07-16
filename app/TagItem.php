<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TagItem extends Model
{
    protected $casts = [
        'tags' => 'array',
    ];

    protected $fillable = ['tags'];
}
