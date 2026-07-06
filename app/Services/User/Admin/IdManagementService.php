<?php

namespace App\Services\User\Admin;

use App\Repositories\User\Interfaces\NotificationInterface;
use App\Repositories\User\Interfaces\UserInfoInterface;
use App\Services\Core\DataListService;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class IdManagementService
{
    /**
     * Purpose: initializes the ID management service.
     *
     * Action: receives repositories and helpers required to prepare ID verification screens and status changes.
     */
    public function __construct(
        private readonly UserInfoInterface $userInfo,
        private readonly NotificationInterface $notifications,
        private readonly DataListService $dataListService,
    ) {
    }

    /**
     * Purpose: prepares data for the ID management index page.
     *
     * Action: builds filter metadata, loads verification requests, and formats the shared admin list payload.
     *
     * @return array<string, mixed>
     */
    public function indexData(): array
    {
        $searchFields = $this->searchFields();
        $orderFields = $this->orderFields();
        $query = $this->userInfo->paginateWithFilters(
            $searchFields,
            $orderFields,
            ['is_id_verified', '!=', ID_STATUS_UNVERIFIED],
            ['users.id as id', 'email', 'id_type', 'is_id_verified'],
            ['users', 'users.id', '=', 'user_infos.user_id']
        );

        return [
            'list' => $this->dataListService->dataList($query, $searchFields, $orderFields),
            'title' => __('ID Management'),
        ];
    }

    /**
     * Purpose: prepares data for the ID verification detail page.
     *
     * Action: loads the selected verification request and adds the page title required by the view.
     *
     * @return array<string, mixed>
     */
    public function showData(int|string $id): array
    {
        return [
            'user' => $this->findVerificationRequest($id),
            'title' => __('View ID Verification Request'),
        ];
    }

    /**
     * Purpose: approves a pending ID verification request.
     *
     * Action: updates the verification status, notifies the user, and returns a normalized response.
     *
     * @return array<string, bool|string>
     */
    public function approve(int|string $id): array
    {
        try {
            if (!$this->updatePendingRequest($id, ['is_id_verified' => ID_STATUS_VERIFIED])) {
                return $this->response(false, __('Failed to approve.'));
            }

            $this->notify($id, __("Your ID verification request has been approved."));

            return $this->response(true, __('The ID has been approved successfully.'));
        } catch (Throwable $exception) {
            logs()->error('Failed to approve ID verification: ' . $exception->getMessage());

            return $this->response(false, __('Failed to approve.'));
        }
    }

    /**
     * Purpose: declines a pending ID verification request.
     *
     * Action: clears submitted document fields, notifies the user, and returns a normalized response.
     *
     * @return array<string, bool|string>
     */
    public function decline(int|string $id): array
    {
        try {
            $attributes = [
                'is_id_verified' => ID_STATUS_UNVERIFIED,
                'id_type' => null,
                'id_card_front' => null,
                'id_card_back' => null,
            ];

            if (!$this->updatePendingRequest($id, $attributes)) {
                return $this->response(false, __('Failed to decline.'));
            }

            $this->notify($id, __("Your ID verification request has been declined."));

            return $this->response(true, __('The ID has been declined successfully.'));
        } catch (Throwable $exception) {
            logs()->error('Failed to decline ID verification: ' . $exception->getMessage());

            return $this->response(false, __('Failed to decline.'));
        }
    }

    /**
     * Purpose: defines searchable ID management fields.
     *
     * Action: keeps filter field definitions in one place for the list query and UI builder.
     *
     * @return array<int, array<int, string>>
     */
    private function searchFields(): array
    {
        return [
            ['email', __('Email')],
        ];
    }

    /**
     * Purpose: defines sortable ID management fields.
     *
     * Action: keeps sort field definitions in one place for the list query and UI builder.
     *
     * @return array<int, array<int, string>>
     */
    private function orderFields(): array
    {
        return [
            ['email', __('Email')],
        ];
    }

    /**
     * Purpose: loads a submitted ID verification request.
     *
     * Action: ensures unsubmitted verification rows are not shown as reviewable requests.
     */
    private function findVerificationRequest(int|string $id): Model
    {
        return $this->userInfo->findOrFailByConditions(
            ['user_id' => $id, ['is_id_verified', '!=', ID_STATUS_UNVERIFIED]],
            ['user']
        );
    }

    /**
     * Purpose: updates a pending ID verification request.
     *
     * Action: only applies status changes while the request is still waiting for review.
     *
     * @param array<string, int|string|null> $attributes
     */
    private function updatePendingRequest(int|string $id, array $attributes): bool
    {
        return (bool) $this->userInfo->updateByConditions(
            $attributes,
            ['user_id' => $id, 'is_id_verified' => ID_STATUS_PENDING]
        );
    }

    /**
     * Purpose: creates a user notification for an ID verification decision.
     *
     * Action: stores the message so the reviewed user can see the admin decision.
     */
    private function notify(int|string $id, string $message): void
    {
        $this->notifications->create([
            'user_id' => $id,
            'data' => $message,
        ]);
    }

    /**
     * Purpose: formats ID management operation results.
     *
     * Action: gives controllers a consistent payload for flash messages and redirects.
     *
     * @return array<string, bool|string>
     */
    private function response(bool $status, string $message): array
    {
        return [
            SERVICE_RESPONSE_STATUS => $status,
            SERVICE_RESPONSE_MESSAGE => $message,
        ];
    }
}
