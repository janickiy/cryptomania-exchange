<?php

namespace App\Mail\Core;

use App\Models\User\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPassword extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public User $user;

    /**
     * Purpose: initializes the ResetPassword instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     * Create a new message instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->queue = 'default';
        $this->user = $user;
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
        return $this->markdown('email.core.reset_password_link');
    }
}
