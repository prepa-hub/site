<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class League extends Model
{

    public $timestamps = false;
    # This property!
    protected $fillable = [
        'title',
        'parent',
        'level',
    ];
}
