<?php

namespace App\Mail\User;

use App\Models\User\UserInfo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountCreated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public UserInfo $userInfo;

    /**
     * Purpose: initializes the AccountCreated instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     * Create a new message instance.
     *
     * @param UserInfo $userInfo
     */
    public function __construct(UserInfo $userInfo)
    {
        $this->queue = 'default';
        $this->userInfo = $userInfo;
    }

    /**
     * Purpose: builds the email before sending.
     *
     * Action: sets the template, subject, and data used by Laravel Mail.
     *
     * Build the message.
     *
     * @return $this
     */
    public function build(): static
    {
        return $this->markdown('email.user.account_created')->subject(__('Your account has been created on :company', ['company' => config('app.name')]));
    }
}
