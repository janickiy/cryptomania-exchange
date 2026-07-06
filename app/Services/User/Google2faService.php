<?php

namespace App\Services\User;

use App\Http\Requests\User\Google2faRequest;
use App\Repositories\User\Interfaces\UserInterface;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FAQRCode\Google2FA;
use PragmaRX\Google2FALaravel\Support\Authenticator;
use Throwable;

class Google2faService
{
    /**
     * Purpose: initializes the Google2faService instance.
     *
     * Action: receives dependencies required to prepare 2FA setup data and persist user 2FA state.
     */
    public function __construct(
        private readonly ProfileService $profileService,
        private readonly UserInterface $users,
        private readonly Google2FA $google2fa,
    ) {
    }

    /**
     * Purpose: prepares the Google 2FA management page data.
     *
     * Action: returns profile data and generates a new secret plus QR data URI when the user has not enabled 2FA yet.
     *
     * @return array<string, mixed>
     */
    public function createData(): array
    {
        $data = $this->profileService->profile();
        $data['title'] = __('Google Two Factor Authentication');
        $user = Auth::user();

        if (empty($user->google2fa_secret)) {
            $data['secretKey'] = $this->google2fa->generateSecretKey(16);
            $data['inlineUrl'] = $this->toQrCodeDataUri(
                $this->google2fa->getQRCodeInline(company_name(), $user->email, $data['secretKey'])
            );
        }

        return $data;
    }

    /**
     * Purpose: enables Google 2FA for the authenticated user.
     *
     * Action: verifies the submitted OTP against the pending secret, stores the secret, and marks the 2FA challenge as passed.
     *
     * @return array<string, mixed>
     */
    public function enable(Google2faRequest $request, string $googleCode): array
    {
        try {
            if ($this->verifyCode($googleCode, (string) $request->google_app_code) && $this->users->update(['google2fa_secret' => $googleCode], $this->currentUserId())) {
                $this->loginAuthenticator($request);

                return $this->response(true, __('Google Authentication has been enabled successfully.'));
            }
        } catch (Throwable) {
            return $this->response(false, __('Failed to enable google authentication.'));
        }

        return $this->response(false, __('Failed to enable google authentication.'));
    }

    /**
     * Purpose: verifies the authenticated user's Google 2FA OTP.
     *
     * Action: validates the submitted OTP and marks the current 2FA challenge as passed when it is correct.
     *
     * @return array<string, mixed>
     */
    public function verify(Google2faRequest $request): array
    {
        try {
            if ($this->verifyCode(Auth::user()->google2fa_secret, (string) $request->google_app_code)) {
                $this->loginAuthenticator($request);

                return $this->response(true, __('The One Time Password was correct.'));
            }
        } catch (Throwable) {
            return $this->response(false, __('Failed to verify google authentication.'));
        }

        return $this->response(false, __('Failed to verify google authentication.'));
    }

    /**
     * Purpose: disables Google 2FA for the authenticated user.
     *
     * Action: verifies the submitted OTP before removing the stored Google 2FA secret.
     *
     * @return array<string, mixed>
     */
    public function disable(Google2faRequest $request): array
    {
        try {
            if ($this->verifyCode(Auth::user()->google2fa_secret, (string) $request->google_app_code) && $this->users->update(['google2fa_secret' => null], $this->currentUserId())) {
                return $this->response(true, __('Google Authentication has been disabled successfully.'));
            }
        } catch (Throwable) {
            return $this->response(false, __('Failed to disabled google authentication.'));
        }

        return $this->response(false, __('Failed to disabled google authentication.'));
    }

    /**
     * Purpose: normalizes a generated QR code into a browser-safe image source.
     *
     * Action: keeps existing data URIs unchanged and wraps raw SVG markup returned by the QR backend into a data URI.
     */
    private function toQrCodeDataUri(string $qrCode): string
    {
        if (str_starts_with(trim($qrCode), 'data:')) {
            return $qrCode;
        }

        return 'data:image/svg+xml;base64,' . base64_encode($qrCode);
    }

    /**
     * Purpose: verifies an OTP against a Google 2FA secret.
     *
     * Action: guards against empty secrets and delegates verification to the Google 2FA package.
     */
    private function verifyCode(?string $secret, string $code): bool
    {
        return !empty($secret) && $this->google2fa->verifyKey($secret, $code);
    }

    /**
     * Purpose: marks the current request as having passed the 2FA challenge.
     *
     * Action: boots the package authenticator with the request and stores the successful 2FA login state.
     */
    private function loginAuthenticator(Google2faRequest $request): void
    {
        app(Authenticator::class)->boot($request)->login();
    }

    /**
     * Purpose: returns the authenticated user id for user updates.
     *
     * Action: keeps Auth id access centralized for 2FA persistence.
     */
    private function currentUserId(): int
    {
        return (int) Auth::id();
    }

    /**
     * Purpose: builds a normalized response for a Google 2FA operation.
     *
     * Action: lets controllers map service results to flash redirects consistently.
     *
     * @return array<string, mixed>
     */
    private function response(bool $status, string $message): array
    {
        return [
            SERVICE_RESPONSE_STATUS => $status,
            SERVICE_RESPONSE_MESSAGE => $message,
        ];
    }
}
