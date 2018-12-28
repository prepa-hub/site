<?php

/*
 * Reward system reward values
 */

return [
    'referral' => [
        'register' => [
            'referrer' => 50,
            'referred' => 25
        ],
        'verified_account' => [
            'referrer' => 50,
            'referred' => 25
        ],
        'profit_percentage' => 0.2
    ],
    'files' => [
        'posted_file' => 100,
        'deleted_file' => 90,
        'voting' => [
            'upvoted' => 30,
            'downvoted' => 20,
            'owner' => 10,
        ],
        'commented' => 50,
    ],
];
