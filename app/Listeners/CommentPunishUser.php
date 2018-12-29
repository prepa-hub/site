<?php

namespace App\Listeners;

use Laravelista\Comments\Events\CommentDeleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CommentPunishUser
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
     * @param  CommentDeleted  $event
     * @return void
     */
    public function handle(CommentDeleted $event)
    {
        $event->comment->commenter()->first()->rewardFor('File Upload', -config('rewards.files.commented'));
    }
}
