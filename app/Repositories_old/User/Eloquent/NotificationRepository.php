<?php

namespace App\Repositories\User\Eloquent;

use App\Models\User\Notification;
use App\Repositories\BaseRepository;
use App\Repositories\User\Interfaces\NotificationInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class NotificationRepository extends BaseRepository implements NotificationInterface
{
    /**
     * @var Notification
     */
    protected $model;

    /**
     * Purpose: initializes the NotificationRepository instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct(Notification $model)
    {
        $this->model = $model;
    }

    /**
     * Purpose: performs the get last five operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     * @param $userId
     * @return mixed
     */
    public function getLastFive(int|string $userId): Collection
    {
       return $this->model->where('user_id',$userId)->whereNull('read_at')->orderBy('id','desc')->take(5)->get();
    }

    /**
     * Purpose: performs the count unread operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     * @param $userId
     * @return mixed
     */
    public function countUnread(int|string $userId): int
    {
        return $this->model->where('user_id',$userId)->whereNull('read_at')->count();
    }

    /**
     * Purpose: performs the read operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     * @param $id
     * @return false
     */
    public function read(int|string $id): bool
    {
        $notice = $this->model->where('id', $id)->firstOrFail();
        if (empty($notice->read_at)) {
            $notice->read_at = Carbon::now();
            return $notice->update();
        }
        return false;
    }

    /**
     * Purpose: performs the unread operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     * @param $id
     * @return false
     */
    public function unread(int|string $id): bool
    {
        $notice = $this->model->where('id', $id)->firstOrFail();
        if (!empty($notice->read_at)) {
            $notice->read_at = null;
            return $notice->update();
        }
        return false;
    }
}
