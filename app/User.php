<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\UserActivate;
use Questocat\Referral\Traits\UserReferral;
use App\File;
use Illuminate\Support\Facades\Crypt;
use Cookie;
use App\Reward;

class User extends \TCG\Voyager\Models\User implements MustVerifyEmail
{
    use Notifiable;
    use UserReferral;

    const ACTIVE = 1;
    const INACTIVE = 0;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'first_name', 'last_name', 'matiere_id', 'avatar', 'status', 'affiliate_id', 'referred_by', 'points'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    public function subject()
    {
        return $this->BelongsTo('App\Subject');
    }


    public function files()
    {
        return $this->hasMany('File', 'user_id');
    }
    public function verified()
    {
        return $this->hasVerifiedEmail();
    }
    public function fileVote($fileId)
    {
        // TODO: Log the user actions & use that to verify votes.
        $file = File::find($fileId);
        if ($this->id == $file->used_id) {
            return 0;
        } else {
            $voteType = 'up_voted';
            $otherVoteType = 'down_voted';
            $votedFiles = json_decode(Crypt::decryptString(Cookie::get($voteType)), true);
            $otherVotedFiles = json_decode(Crypt::decryptString(Cookie::get($otherVoteType)), true);
            if (in_array($fileId, $votedFiles['files'])) {
                return 1;
            } else {
                if (in_array($fileId, $otherVotedFiles['files'])) {
                    return -1;
                }
            }
            return 0;
        }
    }

    public function rewardFor(string $reasonStr, int $exp, int $expOwner = 0, $reason = null, int $reasonId = null, int $ownerID = null)
    {
        if ($reason && $reasonId) {
            $data = $reason::findOrfail($reasonId);
            $ownerID = $data->user_id;
        }
        if ($ownerID) {
            $owner = self::find($ownerID);
            $owner->increment('points', $exp);
            $owner->save(); // TODO check if this is necessary
        }
        Reward::create([
            'value' => $exp,
            'reason' => $reasonStr,
            'reason_id' => $reasonId,
            'reason_owner' => isset($ownerID) ? $ownerID : null,
            'user_id' => $this->id,
        ]);
        $this->increment('points', $exp);
        $this->save(); // TODO check if this is necessary
    }
}
