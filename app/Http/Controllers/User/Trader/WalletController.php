<?php

namespace App\Http\Controllers\User\Trader;

use App\DTO\Wallet\WithdrawalData;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Trader\DepositRequest;
use App\Http\Requests\User\Trader\WithdrawalRequest;
use App\Repositories\User\Trader\Interfaces\WalletInterface;
use App\Services\User\Trader\WalletService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    /**
     * Purpose: initializes the WalletController instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct(
        private readonly WalletInterface $walletRepository,
        private readonly WalletService $walletService,
    ) {
    }

    /**
     * Purpose: shows the main page or record list for the section.
     *
     * Action: collects data through services or repositories and returns the view.
     *
     */
    public function index(): View|Factory|Application
    {
        $this->walletRepository->createUnavailableWallet(Auth::id());

        return view('frontend.wallets.index', [
            'list' => $this->walletService->getWallets(Auth::id()),
            'title' => __('Wallets'),
        ]);
    }

    /**
     * Purpose: handles the create deposit action in WalletController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     *
     */
    public function createDeposit(int|string $id): View|Factory|Application
    {
        $data['wallet'] = $this->walletRepository->findOrFailByConditions(['id' => $id, 'user_id' => Auth::id()], 'stockItem');
        $data['title'] = __('Wallets');

        if ($data['wallet']->stockItem->item_type == CURRENCY_CRYPTO) {
            $data['walletAddress'] = __('Deposit is currently disabled.');

            if ($data['wallet']->stockItem->deposit_status == ACTIVE_STATUS_ACTIVE) {
                $data['walletAddress'] = !empty($data['wallet']->address)
                    ? $data['wallet']->address
                    : $this->walletService->generateWalletAddress($data['wallet']);
            }

            return view('frontend.wallets.wallet_address', $data);
        }

        if ($data['wallet']->stockItem->item_type == CURRENCY_REAL) {
            return view('frontend.wallets.deposit_form', $data);
        }

        return view('errors.404', $data);
    }

    /**
     * Purpose: handles the store deposit action in WalletController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     *
     */
    public function storeDeposit(DepositRequest $request, int|string $id): RedirectResponse
    {
        $response = $this->walletService->storeDeposit($request, $id);

        if ($response[SERVICE_RESPONSE_STATUS] == true) {
            return redirect()->route('trader.wallets.index')->with(SERVICE_RESPONSE_SUCCESS, $response[SERVICE_RESPONSE_MESSAGE]);
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, $response[SERVICE_RESPONSE_MESSAGE]);
    }

    /**
     * Purpose: handles the complete payment action in WalletController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     *
     */
    public function completePayment(Request $request): RedirectResponse
    {
        $response = $this->walletService->completePayment($request);
        $return = [SERVICE_RESPONSE_ERROR => $response[SERVICE_RESPONSE_MESSAGE]];

        if ($response[SERVICE_RESPONSE_STATUS] == true) {
            $return = [SERVICE_RESPONSE_SUCCESS => $response[SERVICE_RESPONSE_MESSAGE]];
        }

        return redirect()->route('trader.wallets.index')->with($return);
    }

    /**
     * Purpose: handles the cancel payment action in WalletController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     *
     */
    public function cancelPayment(): RedirectResponse
    {
        $response = $this->walletService->cancelPayment();

        return redirect()->route('trader.wallets.index')->with([SERVICE_RESPONSE_WARNING => $response[SERVICE_RESPONSE_MESSAGE]]);
    }

    /**
     * Purpose: handles the create withdrawal action in WalletController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     *
     */
    public function createWithdrawal(int|string $id): View|Factory|Application
    {
        return view('frontend.wallets.withdrawal_form', [
            'wallet' => $this->walletRepository->findOrFailByConditions(['id' => $id, 'user_id' => Auth::id()], 'stockItem'),
            'title' => __('Wallets'),
        ]);
    }

    /**
     * Purpose: handles the store withdrawal action in WalletController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     *
     */
    public function storeWithdrawal(WithdrawalRequest $request, int|string $id): RedirectResponse
    {
        $response = $this->walletService->storeWithdrawal(WithdrawalData::fromArray($request->validated()), $id);

        if ($response[SERVICE_RESPONSE_STATUS] === true) {
            return redirect()
                ->route('reports.trader.withdrawals', ['id' => $response['wallet_id']])
                ->with(SERVICE_RESPONSE_SUCCESS, $response[SERVICE_RESPONSE_MESSAGE]);
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, $response[SERVICE_RESPONSE_MESSAGE]);
    }
}
