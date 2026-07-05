<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Repositories\Core\Interfaces\AuditInterface;
use App\Services\Core\DataListService;

class AuditsController extends Controller
{
    protected $audit;

    /**
     * @param AuditInterface $audit
     */
    public function __construct(AuditInterface $audit)
    {
        $this->audit = $audit;
    }

    public function index()
    {
        $searchFields = [
            ['first_name', __('First Name')],
            ['last_name', __('Last Name')],
            ['email', __('Email')],
            ['event', __('Event')],
        ];
        $orderFields = [
            ['id', __('Serial')],
            ['email', __('Email')],
            ['created_ar', __('Date')],
        ];

        $query = $this->audit->paginateWithUserFilters($searchFields, $orderFields);
        $data['list'] = app(DataListService::class)->dataList($query, $searchFields, $orderFields);
        $data['title'] = __('Audits');

        return view('backend.audits.index', $data);
    }
}
