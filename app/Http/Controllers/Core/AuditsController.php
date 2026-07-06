<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Repositories\Core\Interfaces\AuditInterface;
use App\Services\Core\DataListService;
use Illuminate\Contracts\View\View;

class AuditsController extends Controller
{
    /**
     * Purpose: initializes the audits controller.
     *
     * Action: receives the repository and table list service used by the audit page.
     */
    public function __construct(
        private readonly AuditInterface $audit,
        private readonly DataListService $dataListService,
    ) {
    }

    /**
     * Purpose: shows the audit log list.
     *
     * Action: builds searchable, sortable audit data and renders the list page.
     */
    public function index(): View
    {
        $searchFields = $this->searchFields();
        $orderFields = $this->orderFields();

        return view('backend.audits.index', [
            'list' => $this->dataListService->dataList(
                $this->audit->paginateWithUserFilters($searchFields, $orderFields),
                $searchFields,
                $orderFields
            ),
            'title' => __('Audits'),
        ]);
    }

    /**
     * Purpose: returns fields available for audit search.
     *
     * Action: keeps filter field definitions out of the page action.
     *
     * @return array<int, array{0: string, 1: string}>
     */
    private function searchFields(): array
    {
        return [
            ['first_name', __('First Name')],
            ['last_name', __('Last Name')],
            ['email', __('Email')],
            ['event', __('Event')],
        ];
    }

    /**
     * Purpose: returns fields available for audit sorting.
     *
     * Action: keeps sort field definitions out of the page action.
     *
     * @return array<int, array{0: string, 1: string}>
     */
    private function orderFields(): array
    {
        return [
            ['id', __('Serial')],
            ['email', __('Email')],
            ['created_at', __('Date')],
        ];
    }
}
