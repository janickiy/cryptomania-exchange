<?php

namespace App\Http\Middleware;

use App\Models\Core\AdminSetting;
use Closure;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class Language
{
    /**
     * @Developer: zahid
     * @Date: 2018-07-29 2:58 PM
     * @Description:
     * @param $request
     * @param Closure $next
     * @return mixed
     * @throws \Exception
     */
    public function handle(mixed $request, Closure $next): Response
    {

        $locale = $request->segment(1);
        if(check_language($locale)==null){
            $locale= '';
        }
        set_language($locale, admin_settings('lang'));

        has_permission($request->route()->getName(), null, false);

        return $next($request);
    }
}
