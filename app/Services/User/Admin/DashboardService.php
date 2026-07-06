<?php

namespace App\Services\User\Admin;

class DashboardService
{
    /**
     * Purpose: executes the get cpu usages service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     */
    public function getCpuUsages(): float
    {
        $free = shell_exec('free');
        $free = (string)trim($free);
        $free_arr = explode("\n", $free);
        $mem = explode(" ", $free_arr[1]);
        $mem = array_filter($mem);
        $mem = array_merge($mem);
        $memory_usage = $mem[2] / $mem[1] * 100;
        return round($memory_usage, 2);
    }
}
