<?php

namespace App\Http\Controllers\TradingView;

use App\Http\Controllers\Controller;
use App\Services\TradingView\FaqService;
use Illuminate\Contracts\View\View;

class FaqController extends Controller
{
    /**
     * Purpose: initializes the FaqController instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct(private readonly FaqService $faqService)
    {
    }

    /**
     * Purpose: displays the public FAQ list page.
     *
     * Action: delegates question listing and filter preparation to the service layer.
     *
     */
    public function index(): View
    {
        return view('frontend.faq.index', $this->faqService->indexData());
    }

    /**
     * Purpose: displays a public FAQ detail page.
     *
     * Action: delegates question lookup and relation loading to the service layer.
     *
     */
    public function show(int|string $id): View
    {
        return view('frontend.faq.show', $this->faqService->showData($id));
    }
}
