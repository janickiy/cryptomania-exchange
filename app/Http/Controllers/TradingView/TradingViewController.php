<?php

namespace App\Http\Controllers\TradingView;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Admin\CommentRequest;
use App\Services\TradingView\TradingViewService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class TradingViewController extends Controller
{
    /**
     * Purpose: initializes the TradingViewController instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     */
    public function __construct(
        private readonly TradingViewService $tradingViewService,
    ) {
    }

    /**
     * Purpose: displays the public trading view list page.
     *
     * Action: delegates published post listing and filter data preparation to the service.
     */
    public function index(): View
    {
        return view('frontend.trade_analysis.lists', $this->tradingViewService->indexData());
    }

    /**
     * Purpose: displays a public trading view detail page.
     *
     * Action: delegates published post lookup and relation loading to the service.
     */
    public function show(int|string $id): View
    {
        return view('frontend.trade_analysis.show', $this->tradingViewService->showData($id));
    }

    /**
     * Purpose: submits a comment for a trading view post.
     *
     * Action: delegates comment creation to the service and redirects with the operation result.
     */
    public function comment(CommentRequest $request, int|string $id): RedirectResponse
    {
        $response = $this->tradingViewService->comment($id, (string) $request->validated('content'));

        if ($response[SERVICE_RESPONSE_STATUS]) {
            return redirect()
                ->route('trading-views.show', $response['post_id'])
                ->with(SERVICE_RESPONSE_SUCCESS, $response[SERVICE_RESPONSE_MESSAGE]);
        }

        return redirect()
            ->back()
            ->withInput()
            ->with(SERVICE_RESPONSE_ERROR, $response[SERVICE_RESPONSE_MESSAGE]);
    }
}
