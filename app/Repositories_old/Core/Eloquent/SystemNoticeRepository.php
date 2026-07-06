<?php

namespace App\Repositories\Core\Eloquent;

use App\Models\Core\SystemNotice;
use App\Repositories\BaseRepository;
use App\Repositories\Core\Interfaces\SystemNoticeInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SystemNoticeRepository extends BaseRepository implements SystemNoticeInterface
{
    /**
     * @var SystemNotice
     */
    protected $model;

    /**
     * Purpose: initializes the SystemNoticeRepository instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     * @param SystemNotice $model
     */
    public function __construct(SystemNotice $model)
    {
        $this->model = $model;
    }

    /**
     * Purpose: performs the todays notifications operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     */
    public function todaysNotifications(): Collection
    {
        $startDate = Carbon::now();
        return $this->model->where('status', 1)->where(function ($q) use ($startDate) {
            $q->where('start_at', '<=', $startDate)
                ->where('end_at', '>=', $startDate);
        })->orWhere(function ($q) {
            $q->whereNull('start_at')
                ->whereNull('end_at');
        })->get();

    }
}
