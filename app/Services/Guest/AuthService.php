<?php

namespace App\Services\Guest;

use App\Http\Requests\Core\LoginRequest;
use App\Http\Requests\Core\NewPasswordRequest;
use App\Http\Requests\Core\PasswordResetRequest;
use App\Mail\Core\ResetPassword;
use App\Repositories\User\Interfaces\NotificationInterface;
use App\Repositories\User\Interfaces\UserInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthService
{
    protected $user;

    /**
     * @param UserInterface $repository
     */
    public function __construct(UserInterface $repository)
    {
        $this->user = $repository;
    }

    /**
     * @param LoginRequest $request
     * @return array
     */
    public function login(LoginRequest $request): mixed
    {
        $field = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$field => $request->username, 'password' => $request->password], $request->has('remember_me'))) {
            $user = Auth::user();

            if (!is_null($user->created_by_admin) && !is_bool($user->created_by_admin)) {
                $this->user->update(['created_by_admin' => true], $user->id);
            }

            // check if user is deleted or not.
            if ($user->is_active < ACCOUNT_STATUS_INACTIVE) {
                Auth::logout();
                return [
                    SERVICE_RESPONSE_STATUS => false,
                    SERVICE_RESPONSE_MESSAGE => __('You account is permanently deleted.'),
                ];
            } elseif ($user->is_accessible_under_maintenance == UNDER_MAINTENANCE_ACCESS_INACTIVE && admin_settings('maintenance_mode') == UNDER_MAINTENANCE_MODE_ACTIVE) {
                Auth::logout();
                return [
                    SERVICE_RESPONSE_STATUS => false,
                    SERVICE_RESPONSE_MESSAGE => __('You are not allowed to log in under maintenance mode.'),
                ];
            }

            return [
                SERVICE_RESPONSE_STATUS => true,
                SERVICE_RESPONSE_MESSAGE => __('Login is successful.'),
            ];
        }

        return [
            SERVICE_RESPONSE_STATUS => false,
            SERVICE_RESPONSE_MESSAGE => __('Incorrect :field or password', ['field' => $field])
        ];
    }

    /**
     * @param PasswordResetRequest $request
     * @return array
     */
    public function sendPasswordResetMail(PasswordResetRequest $request): mixed
    {
        $conditions = [
            ['email', $request->email],
            ['is_active', '>', ACCOUNT_STATUS_DELETED]
        ];

        $user = $this->user->getFirstByConditions($conditions);

        if (!$user) {
            return [
                SERVICE_RESPONSE_STATUS => false,
                SERVICE_RESPONSE_MESSAGE => __("Failed! Your account is deleted by system."),
            ];
        }

        Mail::to($user->email)->send(new ResetPassword($user));

        return [
            SERVICE_RESPONSE_STATUS => true,
            SERVICE_RESPONSE_MESSAGE => __("Password reset link is sent to your email address."),
        ];
    }

    /**
     * @param Request $request
     * @param $id
     * @return array
     */
    public function resetPassword(Request $request, mixed $id): mixed
    {
        if (!$request->hasValidSignature()) {
            abort(401, 'Invalid request.');
        }

        $passwordResetLink = url()->signedRoute('reset-password.update', ['id' => $id]);

        return [
            'id' => $id,
            'passwordResetLink' => $passwordResetLink
        ];
    }

    /**
     * @param NewPasswordRequest $request
     * @param $id
     * @return array
     */
    public function updatePassword(NewPasswordRequest $request, mixed $id): mixed
    {
        if (!$request->hasValidSignature()) {
            return [
                SERVICE_RESPONSE_STATUS => false,
                SERVICE_RESPONSE_MESSAGE => __("Invalid request."),
            ];
        }

        $update = ['password' => Hash::make($request->new_password)];

        if ($this->user->update($update, $id)) {
            $notification = ['user_id' => $id, 'data' => __("You just reset your account's password successfully.")];
            app(NotificationInterface::class)->create($notification);

            return [
                SERVICE_RESPONSE_STATUS => true,
                SERVICE_RESPONSE_MESSAGE => __("New password is updated. Please login your account."),
            ];
        }

        return [
            SERVICE_RESPONSE_STATUS => false,
            SERVICE_RESPONSE_MESSAGE => __("Failed to set new password. Please try again."),
        ];
    }
}