<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\User\Google2faRequest;
use App\Repositories\User\Interfaces\UserInterface;
use App\Services\User\ProfileService;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FAQRCode\Google2FA;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class Google2faController extends Controller
{
    /**
     * Purpose: initializes the Google2faController instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct(
        private readonly ProfileService $profileService,
        private readonly UserInterface $users,
    ) {
    }

    /**
     * Purpose: shows the form for creating a new record.
     *
     * Action: prepares form data and returns the create view.
     *
     */
    public function create(): View|Factory|Application
    {
        $data = $this->profileService->profile();
        $data['title'] = __('Google Two Factor Authentication');

        if (empty(Auth::user()->google2fa_secret)) {
            $google2fa = new Google2FA();
            $data['secretKey'] = $google2fa->generateSecretKey(16);
            $data['inlineUrl'] = $this->toQrCodeDataUri(
                $google2fa->getQRCodeInline(company_name(), Auth::user()->email, $data['secretKey'])
            );
        }

        return view('backend.google2fa.create', $data);
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
     * Purpose: creates a new record from request data.
     *
     * Action: passes validated data to the service layer and returns the operation result.
     *
     */
    public function store(Google2faRequest $request, string $googleCode): RedirectResponse
    {
        $google2fa = new Google2FA();

        try {
            if ($google2fa->verifyKey($googleCode, $request->google_app_code)) {
                if ($this->users->update(['google2fa_secret' => $googleCode], Auth::id())) {

                    $authenticator = app(Authenticator::class)->boot($request);
                    $authenticator->login();

                    return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('Google Authentication has been enabled successfully.'));
                }
            }

            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to enable google authentication.'));
        } catch (\Exception $exception) {
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to enable google authentication.'));
        }

    }

    /**
     * Purpose: handles user or account verification.
     *
     * Action: checks the provided parameters and redirects with the verification result.
     *
     */
    public function verify(Google2faRequest $request): RedirectResponse
    {
        $google2fa = new Google2FA();

        try {
            if ($google2fa->verifyKey(Auth::user()->google2fa_secret, $request->google_app_code)) {
                $authenticator = app(Authenticator::class)->boot($request);
                $authenticator->login();

                return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __("The One Time Password was correct."));
            }

            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to verify google authentication.'));
        } catch (\Exception $exception) {
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to verify google authentication.'));
        }
    }


    /**
     * Purpose: deletes the selected record.
     *
     * Action: performs deletion through a service or repository and redirects back with the result.
     *
     */
    public function destroy(Google2faRequest $request): RedirectResponse
    {
        $google2fa = new Google2FA();

        try {
            if ($google2fa->verifyKey(Auth::user()->google2fa_secret, $request->google_app_code)) {
                if ($this->users->update(['google2fa_secret' => null], Auth::id())) {
                    return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('Google Authentication has been disabled successfully.'));
                }
            }

            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to disabled google authentication.'));
        } catch (\Exception $exception) {
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to disabled google authentication.'));
        }
    }
}
