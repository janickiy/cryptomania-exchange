<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Services\Guest\HomePageService;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    /**
     * Purpose: initializes the HomeController instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     */
    public function __construct(private readonly HomePageService $homePageService)
    {
    }

    /**
     * Purpose: handles calling the HomeController instance as a single action.
     *
     * Action: renders the public home page with data prepared by the service layer.
     */
    public function __invoke(): View
    {
        return view('home', $this->homePageService->viewData());
    }
}
