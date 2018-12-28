<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{

    public $timestamps = false;
    # This property!
    protected $fillable = [
        'value',
        'reason',
        'reason_id',
        'reason_owner',
        'user_id',
    ];
    public function user()
    {
        return $this->BelongsTo('App\User');
    }
    public function userId()
    {
        return $this->BelongsTo('App\User');
    }
}
