<?php
/**
 * Created by PhpStorm.
 * User: rana
 * Date: 10/2/18
 * Time: 4:57 PM
 */

namespace App\Repositories\Core\Eloquent;

use App\Repositories\BaseRepository;
use App\Repositories\Core\Interfaces\AuditInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Models\Audit;

class AuditRepository extends BaseRepository implements AuditInterface
{
    /**
     * @var Audit
     */
    protected $model;

    /**
     * Purpose: initializes the AuditRepository instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     * @param Audit $audit
     */
    public function __construct(Audit $audit)
    {
        $this->model = $audit;
    }

    /**
     * Purpose: performs the paginate with user filters operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     */
    public function paginateWithUserFilters(array $searchFields, array $orderFields): LengthAwarePaginator
    {
        return $this->paginateWithFilters(
            $searchFields,
            $orderFields,
            null,
            ['audits.*', 'email', DB::raw("CONCAT(first_name,' ',last_name) as full_name")],
            [
                ['users', 'users.id', '=', 'audits.user_id'],
                ['user_infos', 'user_infos.user_id', '=', 'users.id'],
            ]
        );
    }
}
