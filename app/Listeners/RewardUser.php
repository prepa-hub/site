<?php

namespace App\Listeners;

use App\Events\UserReferred;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RewardUser
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserReferred  $event
     * @return void
     */
    public function handle(UserReferred $event)
    {
        $referrer = \App\User::where('affiliate_id', '=', $event->referralId)->first();
        if (!is_null($referrer)) {
            $newUser = $event->user;
            $newUser->increment('points', 50);
            $newUser->save();
            $referrer->increment('points', 10);
            $referrer->save();
        }
    }
}
