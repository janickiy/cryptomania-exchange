<?php

namespace App\Observers\Core;

use App\Models\Core\SystemNotice;
use Carbon\Carbon;

class SystemNoticeObserver
{
    /**
     * Purpose: performs the created operation in SystemNoticeObserver.
     *
     * Action: encapsulates one logic step so callers can use the result without duplicating details.
     *
     * @param SystemNotice $systemNotice
     * @throws \Exception
     */
    public function created(SystemNotice $systemNotice): void
    {
        $systemNotices = cache()->get('systemNotices');
        $date = Carbon::now();
        if (!empty($systemNotice->start_at) && !empty($systemNotice->end_at)) {
            if ($systemNotice->start_at <= $date && $systemNotice->end_at >= $date) {
                $systemNotices->push($systemNotice);
            }
        } else {
            $systemNotices->push($systemNotice);
        }

        $date = Carbon::now();
        $totalMinutes = $date->diffInMinutes($date->copy()->endOfDay());
        cache()->put('systemNotices', $systemNotices, $totalMinutes);
    }

    /**
     * Purpose: performs the updated operation in SystemNoticeObserver.
     *
     * Action: encapsulates one logic step so callers can use the result without duplicating details.
     *
     * @param SystemNotice $systemNoticeUpdate
     * @throws \Exception
     */
    public function updated(SystemNotice $systemNoticeUpdate): void
    {
        $systemNotices = cache()->get('systemNotices');

        $isFound = false;
        $systemNotices = $systemNotices->map(function ($systemNotice, $key) use ($systemNoticeUpdate, &$isFound) {
            if ($systemNotice->id == $systemNoticeUpdate->id) {
                $isFound = true;
                $date = Carbon::now();
                if (!empty($systemNotice->start_at) && !empty($systemNotice->end_at)) {
                    if ($systemNotice->start_at <= $date && $systemNotice->end_at >= $date) {
                        return $systemNoticeUpdate;
                    }
                } else {
                    return $systemNoticeUpdate;
                }
            }
            return $systemNotice;
        });

        if (!$isFound) {
            $systemNotices->push($systemNoticeUpdate);
        }

        $date = Carbon::now();
        $totalMinutes = $date->diffInMinutes($date->copy()->endOfDay());
        cache()->put('systemNotices', $systemNotices, $totalMinutes);
    }
}
