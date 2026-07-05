<?php

namespace App\Http\Controllers\Core;

use App\DTO\Admin\SystemNoticeData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SystemNoticeRequest;
use App\Repositories\Core\Interfaces\SystemNoticeInterface;
use App\Services\Core\DataListService;
use App\Services\User\Admin\SystemNoticeAdminService;

class SystemNoticeController extends Controller
{
    public $systemNotice;

    /**
     * @param SystemNoticeInterface $systemNotice
     */
    public function __construct(SystemNoticeInterface $systemNotice)
    {
        $this->systemNotice = $systemNotice;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $searchFields = [
            ['title', __('Title')],
        ];
        $orderFields = [
            ['id', __('Serial')],
            ['type', __('Type')],
            ['status', __('Status')],
            ['start_at', __('Start Time')],
            ['end_at', __('End Time')],
        ];

        $query = $this->systemNotice->paginateWithFilters($searchFields, $orderFields);
        $data['list'] = app(DataListService::class)->dataList($query, $searchFields, $orderFields);
        $data['title'] = __('System Notice');

        return view('backend.systemNotice.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['types'] = array_combine(config('commonconfig.system_notice_types'), array_map('ucfirst', config('commonconfig.system_notice_types')));
        $data['title'] = __('Create Notice');

        return view('backend.systemNotice.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SystemNoticeRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(SystemNoticeRequest $request)
    {
        if (app(SystemNoticeAdminService::class)->create(SystemNoticeData::fromArray($request->validated()))) {
            return redirect()->route('system-notices.index')->with(SERVICE_RESPONSE_SUCCESS, __('Notice has been created successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to create notice.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $data['systemNotice'] = $this->systemNotice->findOrFailById($id);
        $data['types'] = array_combine(config('commonconfig.system_notice_types'), array_map('ucfirst', config('commonconfig.system_notice_types')));
        $data['title'] = __('Edit Notices');

        return view('backend.systemNotice.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SystemNoticeRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SystemNoticeRequest $request, $id)
    {
        if (app(SystemNoticeAdminService::class)->update((int) $id, SystemNoticeData::fromArray($request->validated()))) {
            return redirect()->route('system-notices.index')->with(SERVICE_RESPONSE_SUCCESS, __('System notice has been updated successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to update system notice.'));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        if (app(SystemNoticeAdminService::class)->delete((int) $id)) {
            return redirect()->route('system-notices.index')->with(SERVICE_RESPONSE_SUCCESS, __('System notice has been deleted successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to delete system notice.'));
    }
}
