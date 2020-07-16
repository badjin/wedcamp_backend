<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SongRequest extends Model
{
    protected $fillable = ['title', 'real_name', 'mobile', 'is_mfgc', 'description', 'user_id'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
