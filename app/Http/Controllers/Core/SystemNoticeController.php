<?php

namespace App\Http\Controllers\Core;

use App\DTO\Admin\SystemNoticeData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SystemNoticeRequest;
use App\Repositories\Core\Interfaces\SystemNoticeInterface;
use App\Services\Core\DataListService;
use App\Services\User\Admin\SystemNoticeAdminService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SystemNoticeController extends Controller
{
    /**
     * Purpose: initializes the SystemNoticeController instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     */
    public function __construct(
        private readonly SystemNoticeInterface $systemNotice,
        private readonly SystemNoticeAdminService $systemNoticeService,
        private readonly DataListService $dataListService,
    ) {
    }

    /**
     * Purpose: shows the main page or record list for the section.
     *
     * Action: collects data through services or repositories and returns the view.
     */
    public function index(): View
    {
        $searchFields = $this->searchFields();
        $orderFields = $this->orderFields();

        return view('backend.systemNotice.index', [
            'list' => $this->dataListService->dataList(
                $this->systemNotice->paginateWithFilters($searchFields, $orderFields),
                $searchFields,
                $orderFields
            ),
            'title' => __('System Notice'),
        ]);
    }

    /**
     * Purpose: shows the form for creating a new record.
     *
     * Action: prepares form data and returns the create view.
     */
    public function create(): View
    {
        return view('backend.systemNotice.create', $this->formData(__('Create Notice')));
    }

    /**
     * Purpose: creates a new record from request data.
     *
     * Action: passes validated data to the service layer and returns the operation result.
     */
    public function store(SystemNoticeRequest $request): RedirectResponse
    {
        return $this->operationResponse(
            (bool) $this->systemNoticeService->create($this->noticeData($request)),
            __('Notice has been created successfully.'),
            __('Failed to create notice.')
        );
    }

    /**
     * Purpose: shows the edit form for the selected record.
     *
     * Action: loads current data and returns the edit view.
     */
    public function edit(int|string $id): View
    {
        return view('backend.systemNotice.edit', $this->formData(__('Edit Notices'), [
            'systemNotice' => $this->systemNotice->findOrFailById($id),
        ]));
    }

    /**
     * Purpose: updates the selected record from request data.
     *
     * Action: passes changes to the service layer and returns a result message.
     */
    public function update(SystemNoticeRequest $request, int|string $id): RedirectResponse
    {
        return $this->operationResponse(
            $this->systemNoticeService->update((int) $id, $this->noticeData($request)),
            __('System notice has been updated successfully.'),
            __('Failed to update system notice.')
        );
    }

    /**
     * Purpose: deletes the selected record.
     *
     * Action: performs deletion through a service or repository and redirects back with the result.
     */
    public function destroy(int|string $id): RedirectResponse
    {
        return $this->operationResponse(
            $this->systemNoticeService->delete((int) $id),
            __('System notice has been deleted successfully.'),
            __('Failed to delete system notice.')
        );
    }

    /**
     * Purpose: returns fields available for system notice search.
     *
     * Action: keeps filter field definitions out of the page action.
     *
     * @return array<int, array{0: string, 1: string}>
     */
    private function searchFields(): array
    {
        return [
            ['title', __('Title')],
        ];
    }

    /**
     * Purpose: returns fields available for system notice sorting.
     *
     * Action: keeps sort field definitions out of the page action.
     *
     * @return array<int, array{0: string, 1: string}>
     */
    private function orderFields(): array
    {
        return [
            ['id', __('Serial')],
            ['type', __('Type')],
            ['status', __('Status')],
            ['start_at', __('Start Time')],
            ['end_at', __('End Time')],
        ];
    }

    /**
     * Purpose: prepares shared data for system notice forms.
     *
     * Action: merges page title, notice types, and optional view data.
     *
     * @param array<string, object|string> $extra
     * @return array<string, array<string, string>|object|string>
     */
    private function formData(string $title, array $extra = []): array
    {
        return array_merge([
            'title' => $title,
            'types' => $this->noticeTypes(),
        ], $extra);
    }

    /**
     * Purpose: returns selectable system notice types.
     *
     * Action: builds the option list from application configuration.
     *
     * @return array<string, string>
     */
    private function noticeTypes(): array
    {
        $types = config('commonconfig.system_notice_types', []);

        if (!is_array($types)) {
            return [];
        }

        return array_combine($types, array_map('ucfirst', $types)) ?: [];
    }

    /**
     * Purpose: converts validated request data into a DTO.
     *
     * Action: keeps transport data creation in one controller helper.
     */
    private function noticeData(SystemNoticeRequest $request): SystemNoticeData
    {
        return SystemNoticeData::fromArray($request->validated());
    }

    /**
     * Purpose: redirects after a system notice write operation.
     *
     * Action: returns a success redirect to the list or an error redirect back to the form.
     */
    private function operationResponse(bool $success, string $successMessage, string $errorMessage): RedirectResponse
    {
        if ($success) {
            return redirect()
                ->route('system-notices.index')
                ->with(SERVICE_RESPONSE_SUCCESS, $successMessage);
        }

        return redirect()
            ->back()
            ->withInput()
            ->with(SERVICE_RESPONSE_ERROR, $errorMessage);
    }
}
