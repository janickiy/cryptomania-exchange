<?php

namespace App\Http\Controllers\User\Trader;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Trader\DepositRequest;
use App\Http\Requests\User\Trader\WithdrawalRequest;
use App\Services\User\Trader\WalletService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    /**
     * Purpose: initializes the WalletController instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct(
        private readonly WalletService $walletService,
    ) {
    }

    /**
     * Purpose: displays the authenticated trader wallet list.
     *
     * Action: delegates missing-wallet creation and list preparation to the wallet service.
     *
     */
    public function index(): View|Factory|Application
    {
        return view('frontend.wallets.index', $this->walletService->indexData());
    }

    /**
     * Purpose: displays the deposit screen for a selected wallet.
     *
     * Action: delegates wallet lookup, wallet-address generation, and view selection to the wallet service.
     *
     */
    public function createDeposit(int|string $id): View|Factory|Application
    {
        $page = $this->walletService->depositPage($id);

        return view($page['view'], $page['data']);
    }

    /**
     * Purpose: starts a deposit request for a selected wallet.
     *
     * Action: delegates deposit processing to the wallet service and returns either a payment redirect or a flash redirect.
     *
     */
    public function storeDeposit(DepositRequest $request, int|string $id): RedirectResponse
    {
        $response = $this->walletService->storeDeposit($request, $id);

        if ($response instanceof RedirectResponse) {
            return $response;
        }

        return $response[SERVICE_RESPONSE_STATUS] === true
            ? redirect()->route('trader.wallets.index')->with(SERVICE_RESPONSE_SUCCESS, $response[SERVICE_RESPONSE_MESSAGE])
            : redirect()->back()->with(SERVICE_RESPONSE_ERROR, $response[SERVICE_RESPONSE_MESSAGE]);
    }

    /**
     * Purpose: handles the external payment provider success callback.
     *
     * Action: delegates payment completion to the wallet service and redirects to the wallet list with the result.
     *
     */
    public function completePayment(Request $request): RedirectResponse
    {
        $response = $this->walletService->completePayment($request);
        $flashKey = $response[SERVICE_RESPONSE_STATUS] === true ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->route('trader.wallets.index')->with($flashKey, $response[SERVICE_RESPONSE_MESSAGE]);
    }

    /**
     * Purpose: handles the external payment provider cancellation callback.
     *
     * Action: delegates cancellation cleanup to the wallet service and redirects to the wallet list with a warning.
     *
     */
    public function cancelPayment(): RedirectResponse
    {
        $response = $this->walletService->cancelPayment();

        return redirect()->route('trader.wallets.index')->with([SERVICE_RESPONSE_WARNING => $response[SERVICE_RESPONSE_MESSAGE]]);
    }

    /**
     * Purpose: displays the withdrawal form for a selected wallet.
     *
     * Action: delegates wallet lookup and form data preparation to the wallet service.
     *
     */
    public function createWithdrawal(int|string $id): View|Factory|Application
    {
        return view('frontend.wallets.withdrawal_form', $this->walletService->withdrawalPageData($id));
    }

    /**
     * Purpose: submits a withdrawal request for a selected wallet.
     *
     * Action: delegates DTO creation and withdrawal processing to the wallet service before redirecting with the result.
     *
     */
    public function storeWithdrawal(WithdrawalRequest $request, int|string $id): RedirectResponse
    {
        $response = $this->walletService->storeWithdrawalFromValidatedData($request->validated(), $id);

        if ($response[SERVICE_RESPONSE_STATUS] === true) {
            return redirect()
                ->route('reports.trader.withdrawals', ['id' => $response['wallet_id']])
                ->with(SERVICE_RESPONSE_SUCCESS, $response[SERVICE_RESPONSE_MESSAGE]);
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, $response[SERVICE_RESPONSE_MESSAGE]);
    }
}
