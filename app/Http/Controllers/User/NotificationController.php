<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Repositories\User\Interfaces\NotificationInterface;
use App\Services\Core\DataListService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Purpose: initializes the NotificationController instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct(
        private readonly NotificationInterface $notification,
        private readonly DataListService $dataListService,
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
        $user = Auth::user();
        $data['title'] = __('Notices');

        $searchFields = [
            ['data', __('Notice')],
        ];

        $orderFields = [
            ['id', __('Serial')],
            ['data', __('Notice')],
            ['created_at', __('Date')],
            ['read_at', __('Status')],
        ];

        $where = ['user_id' => $user->id];
        $query = $this->notification->paginateWithFilters($searchFields, $orderFields, $where);
        $data['list'] = $this->dataListService->dataList($query, $searchFields, $orderFields);

        return view('backend.notices.index', $data);
    }

    /**
     * Purpose: handles the mark as read action in NotificationController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     *
     */
    public function markAsRead(int|string $id): RedirectResponse
    {
        if ($this->notification->read($id)) {
            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The notice has been marked as read.'));
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to mark as read.'));
    }

    /**
     * Purpose: handles the mark as unread action in NotificationController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     *
     */
    public function markAsUnread(int|string $id): RedirectResponse
    {
        if ($this->notification->unread($id)) {
            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The notice has been marked as unread.'));
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to mark as unread.'));
    }
}
