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
            // Reward user & file owner
            $newUser->rewardFor('Refferal Sign Up', config('rewards.referral.register.referred'), config('rewards.referral.register.referrer'), null, null, $referrer->id);
        }
    }
}
