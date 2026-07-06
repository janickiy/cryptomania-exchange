<?php

namespace App\Observers\User;

use App\Mail\User\AccountCreated;
use App\Mail\User\Registered;
use App\Models\User\User;
use App\Models\User\UserInfo;
use Illuminate\Support\Facades\Mail;

class UserInfoObserver
{
    /**
     * Purpose: performs the created operation in UserInfoObserver.
     *
     * Action: encapsulates one logic step so callers can use the result without duplicating details.
     *
     * Handle to the user "created" event.
     *
     * @param UserInfo $userInfo
     * @return void
     */
    public function created(UserInfo $userInfo): void
    {
        if ($userInfo->user->created_by_admin) {
            Mail::to($userInfo->user->email)->send(new AccountCreated($userInfo));
        } else {
            Mail::to($userInfo->user->email)->send(new Registered($userInfo));
        }
    }

}
