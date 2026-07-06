<?php

namespace App\Repositories;

use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

abstract class BaseRepository
{
    use RepositoryTrait;

    /**
     * Purpose: returns all records handled by the repository.
     *
     * Action: encapsulates model reads so callers do not depend on Eloquent query details.
     *
     */
    public function getAll(string|array|null $relations = null, ?array $orders = null): Collection
    {
        $query = $this->model->with($this->extractToArray($relations));

        if (!empty($orders) && is_array($orders)) {
            $query = $query->orderBy($orders);
        }

        return $query->get();
    }

    /**
     * Purpose: finds records by the provided conditions.
     *
     * Action: hides Eloquent query construction behind the repository interface.
     *
     */
    public function getByConditions(array $conditions, string|array|null $relations = null, ?array $orders = null): Collection
    {
        $query = $this->model->where($conditions)->with($this->extractToArray($relations));

        if (!empty($orders) && is_array($orders)) {
            $query = $query->orderBy($orders);
        }

        return $query->get();
    }

    /**
     * Purpose: finds a repository record by identifier.
     *
     * Action: gives services a centralized way to access model data.
     *
     */
    public function findOrFailById(int|string $id, string|array|null $relations = null): Model
    {
        return $this->model->with($this->extractToArray($relations))->findOrFail($id);
    }

    /**
     * Purpose: finds records by the provided conditions.
     *
     * Action: hides Eloquent query construction behind the repository interface.
     *
     */
    public function findOrFailByConditions(array $conditions, string|array|null $relations = null): Model
    {
        $instant = $this->model->where($conditions)->with($this->extractToArray($relations))->first();
        abort_if(empty($instant), 404);
        return $instant;
    }

    /**
     * Purpose: creates a new record in storage.
     *
     * Action: accepts prepared data or a DTO and persists only fields allowed by the model.
     *
     */
    public function create(array $attributes): Model|false
    {
        return $this->model->create(Arr::only($attributes, $this->model->getFillable()));
    }

    /**
     * Purpose: updates one or more records in storage.
     *
     * Action: centralizes data changes and returns the result to the service layer.
     *
     */
    public function update(array $attributes, int $id, string $attribute = "id"): Model|bool
    {
        $instance = $this->getFirstByConditions([$attribute => $id]);

        if (empty($instance)) {
            return false;
        }

        return $instance->update(Arr::only($attributes, $this->model->getFillable())) ? $instance : false;
    }

    /**
     * Purpose: finds records by the provided conditions.
     *
     * Action: hides Eloquent query construction behind the repository interface.
     *
     */
    public function getFirstByConditions(array $conditions, string|array|null $relations = null): ?Model
    {
        return $this->model->where($conditions)->with($this->extractToArray($relations))->first();
    }

    /**
     * Purpose: updates one or more records in storage.
     *
     * Action: centralizes data changes and returns the result to the service layer.
     *
     */
    public function updateByConditions(array $attributes, array $conditions): Model|bool
    {
        $instance = $this->getFirstByConditions($conditions);

        if (empty($instance)) {
            return false;
        }

        return $instance->update(Arr::only($attributes, $this->model->getFillable())) ? $instance : false;
    }

    /**
     * Purpose: removes records from storage.
     *
     * Action: encapsulates delete operations and their result in the repository layer.
     *
     */
    public function deleteById(int $id): bool
    {
        $instance = $this->getFirstById($id);

        if (empty($instance)) {
            return false;
        }

        try {
            $instance->delete();
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Purpose: finds a repository record by identifier.
     *
     * Action: gives services a centralized way to access model data.
     *
     */
    public function getFirstById(int|string $id, string|array|null $relations = null): ?Model
    {
        return $this->model->where('id', $id)->with($this->extractToArray($relations))->first();
    }

    /**
     * Purpose: removes records from storage.
     *
     * Action: encapsulates delete operations and their result in the repository layer.
     *
     */
    public function deleteByConditions(array $conditions): bool
    {
        $instance = $this->getFirstByConditions($conditions);

        if (empty($instance)) {
            return false;
        }

        try {
            $instance->delete();
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Purpose: performs the toggle status by id operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     */
    public function toggleStatusById(int $id, string $attribute = 'is_active'): Model|false
    {
        $instance = $this->getFirstById($id);

        if (empty($instance)) {
            return false;
        }

        $instance->{$attribute} = $instance->{$attribute} ? ACTIVE_STATUS_INACTIVE : ACTIVE_STATUS_ACTIVE;

        if ($instance->update()) {
            return $instance;
        }

        return $instance->update() ? $instance : false;
    }

    /**
     * Purpose: performs the toggle status by conditions operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     */
    public function toggleStatusByConditions(array $conditions, string $attribute = 'is_active'): Model|false
    {
        $instance = $this->getFirstByConditions($conditions);

        if (empty($instance)) {
            return false;
        }

        $instance->{$attribute} = $instance->{$attribute} ? ACTIVE_STATUS_INACTIVE : ACTIVE_STATUS_ACTIVE;

        return $instance->update() ? $instance : false;
    }

    /**
     * Purpose: performs the insert operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     */
    public function insert(array $attributes): bool
    {
        return $this->model->insert($attributes);
    }

    /**
     * Purpose: performs the bulk update operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     */
    public function bulkUpdate(array $values): int|false
    {
        if (!count($values)) {
            return false;
        }

        $table = $this->model->getTable();

        $sql = "UPDATE {$table} SET ";
        $updatableFieldSeparator = '';
        $rowCount = count($values);
        $updatableFields = $this->_getUpdatableFields($values);
        if (!$updatableFields) {
            return false;
        }
        foreach ($updatableFields as $updatableField) {
            //for each updatable field
            $sql .= $updatableFieldSeparator;
            $sql .= "{$updatableField} = (CASE";
            for ($i = 0; $i < $rowCount; $i++) {
                if (!isset($values[$i]['fields'][$updatableField])) {
                    continue;
                }
                $sql .= " WHEN ";
                $conditionalSyntax = '';

                // for each condition
                foreach ($values[$i]['conditions'] as $conditionalKey => $conditionalValue) {
                    if (is_array($conditionalValue)) {
                        $sql .= $conditionalSyntax . "{$conditionalKey} {$conditionalValue[0]} {$conditionalValue[1]}";
                    } else {
                        $sql .= $conditionalSyntax . "{$conditionalKey}='{$conditionalValue}'";
                    }
                    $conditionalSyntax = ' AND ';
                }

                $updatableFieldValue = $values[$i]['fields'][$updatableField];

                if (is_array($updatableFieldValue)) {
                    if ($updatableFieldValue[0] == 'increment') {
                        $sql .= " THEN {$updatableField} + {$updatableFieldValue[1]}";
                    } elseif ($updatableFieldValue[0] == 'decrement') {
                        $sql .= " THEN {$updatableField} - {$updatableFieldValue[1]}";
                    }else{
                        $sql .= " THEN {$updatableFieldValue[1]}";
                    }
                } else {
                    $sql .= " THEN '{$updatableFieldValue}'";
                }
            }
            $sql .= " ELSE {$updatableField} END) ";
            $updatableFieldSeparator = ', ';
        }

        $conditionalClause = "WHERE ";
        $conditionalFieldSeparator = '(';
        foreach ($values as $value) {
            $innerSeparator = '';
            $conditionalClause .= $conditionalFieldSeparator;
            foreach ($value['conditions'] as $conditionalKey => $conditionalValue) {
                if (is_array($conditionalValue)) {
                    $conditionalClause .= $innerSeparator . "{$conditionalKey} {$conditionalValue[0]} {$conditionalValue[1]}";
                } else {
                    $conditionalClause .= $innerSeparator . "{$conditionalKey}='{$conditionalValue}'";
                }
                $innerSeparator = ' AND ';
            }
            $conditionalClause .= ')';
            $conditionalFieldSeparator = ' OR (';
        }
        $sql .= $conditionalClause;
        return DB::update($sql);
    }

    /**
     * Purpose: performs the paginate operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     */
    public function paginate(array $columns = ['*'], int $perPage = ITEM_PER_PAGE, string $paginationKey = 'p'): LengthAwarePaginator
    {
        return $this->model->paginate($perPage, $columns, $paginationKey);
    }

    /**
     * Purpose: performs the simple paginate operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     */
    public function simplePaginate(array $columns = ['*'], int $perPage = ITEM_PER_PAGE, string $paginationKey = 'p', ?array $where = null, string $order = 'desc'): Paginator
    {
        if(!empty($where)){
            $this->model = $this->model->where($where);
        }
        if($order != 'asc'){
            $order = 'desc';
        }
        return $this->model->orderBy('id',$order)->simplePaginate($perPage, $columns, $paginationKey);
    }

    /**
     * Purpose: builds a record list with filters, search, and sorting.
     *
     * Action: provides shared table behavior for admin pages and reports.
     *
     */
    public function filters(array $searchFields, ?array $orderFields = null, ?array $whereArray = null, array|string|null $selectData = null, ?array $joinArray = null, array|string|null $groupBy = null, string $paginationKey = 'p', string $dateField = 'created_at'): Collection
    {
        $tableName = $this->model->getTable();
        if (!is_null($joinArray) && is_array($joinArray)) {
            $dateFieldChecker = explode('.', $dateField);
            if (count($dateFieldChecker) == 1) {
                $dateField = $tableName . '.' . $dateField;
            }
        }
        $itemPerPage = empty($itemPerPage) ? admin_settings('item_per_page') : $itemPerPage;
        $itemPerPage = filter_var($itemPerPage, FILTER_VALIDATE_INT) != false ? $itemPerPage : ITEM_PER_PAGE;
        $order = request()->query($paginationKey . '_ord');
        $col = request()->query($paginationKey . '_sort');
        $search = request()->query($paginationKey . '_srch');
        $frm = request()->query($paginationKey . '_frm');
        $to = request()->query($paginationKey . '_to');
        $comp = request()->query($paginationKey . '_comp');
        $ssf = request()->query($paginationKey . '_ssf');

        if ($order == 'a') {
            $order = 'asc';
        } else {
            $order = 'desc';
        }

        $comparison = ['e' => '=', 'lk' => 'like', 'l' => '<', 'le' => '<=', 'm' => '>', 'me' => '>=', 'ne' => '!='];
        $comparison = array_key_exists($comp, $comparison) ? $comparison[$comp] : $comparison = 'LIKE';
        if ($orderFields) {
            $allcol = $orderFields;
        }

        $whereFields = array_column($searchFields, 0);
        $whereFields = array_key_exists($ssf, $whereFields) ? $whereFields[$ssf] : array_values($whereFields);
        $getelements = [$paginationKey . '_srch' => $search, $paginationKey . '_ord' => $order, $paginationKey . '_sort' => $col, $paginationKey . '_frm' => $frm, $paginationKey . '_to' => $to, $paginationKey . '_ssf' => $ssf, $paginationKey . '_comp' => $comp];
        if (isset($allcol)) {
            $column = $this->_column_query($allcol, $paginationKey . '_sort', $tableName . '.id');
        }

        $srcharr = $comparison == 'like' ? explode(' ', $search) : $search;

        foreach ($getelements as $key => $val) {
            if ($val == '') {
                unset($getelements[$key]);
            } elseif ($key == $paginationKey . '_frm' || $key == $paginationKey . '_to') {
                if (validate_date($val) == false) {
                    unset($getelements[$key]);
                }
            }
        }


        if ($joinArray != null && is_array($joinArray[0])) {
            foreach ($joinArray as $arr) {
                if(isset($arr[4])){
                    $this->model = $this->model->leftJoin($arr[0], function($join) use($arr) {
                        $join->on($arr[1], $arr[2], $arr[3])
                            ->where($arr[4]);
                    });
                }
                else {
                    $this->model = $this->model->leftJoin($arr[0], $arr[1], $arr[2], $arr[3]);
                }
            }
        } elseif ($joinArray != null) {
            if(isset($joinArray[4])){
                $this->model = $this->model->leftJoin($joinArray[0], function($join) use($joinArray) {
                    $join->on($joinArray[1], $joinArray[2], $joinArray[3])
                        ->where($joinArray[4]);
                });
            }
            else{
                $this->model = $this->model->leftJoin($joinArray[0], $joinArray[1], $joinArray[2], $joinArray[3]);
            }
        }

        if (isset($getelements[$paginationKey . '_frm'])) {
            $this->model = $this->model->where($dateField, '>=', $frm);
        }
        if (isset($getelements[$paginationKey . '_to'])) {
            $this->model = $this->model->where($dateField, '<', Carbon::parse($to)->addDay());
        }

        if (!is_null($whereArray)) {
            $this->model = $this->_where_builder($whereArray);
        }

        if (!empty($search)) {
            $this->model = $this->model->where(function ($query) use ($srcharr, $whereFields, $comparison) {
                $firstQuery = 1;
                if (is_array($srcharr) && $comparison == 'like') {
                    foreach ($srcharr as $wh) {
                        if (is_array($whereFields)) {
                            foreach ($whereFields as $sf) {
                                if ($firstQuery == 1) {
                                    $query->where($sf, 'like', '%' . $wh . '%');
                                } else {
                                    $query->orWhere($sf, 'like', '%' . $wh . '%');
                                }
                                $firstQuery = 0;
                            }
                        } else {
                            if ($firstQuery == 1) {
                                $query->where($whereFields, 'like', '%' . $wh . '%');
                            } else {
                                $query->orWhere($whereFields, 'like', '%' . $wh . '%');
                            }
                            $firstQuery = 0;
                        }
                    }
                } else {
                    if (is_array($whereFields)) {
                        foreach ($whereFields as $sf) {
                            if ($firstQuery == 1) {
                                $query->where($sf, $comparison, $srcharr);
                            } else {
                                $query->orWhere($sf, $comparison, $srcharr);
                            }
                            $firstQuery = 0;
                        }
                    } else {
                        $query->where($whereFields, $comparison, $srcharr);
                    }
                }
            });
        }
        if ($selectData != null) {
            $this->model = $this->model->select($selectData);
        }
        if(!empty($groupBy)){
            $this->model = $this->model->groupBy($groupBy);
        }

        if (!empty($column)) {
            $this->model = $this->model->orderBy($column, $order);
        }

        $data = $this->model->get();

        return $data;
    }

    /**
     * Purpose: builds a record list with filters, search, and sorting.
     *
     * Action: provides shared table behavior for admin pages and reports.
     *
     */
    public function paginateWithFilters(array $searchFields, ?array $orderFields = null, ?array $whereArray = null, array|string|null $selectData = null, ?array $joinArray = null, array|string|null $groupBy = null, int|string|null $itemPerPage = null, string $paginationKey = 'p', string $dateField = 'created_at'): LengthAwarePaginator
    {
        $tableName = $this->model->getTable();
        if (!is_null($joinArray) && is_array($joinArray)) {
            $dateFieldChecker = explode('.', $dateField);
            if (count($dateFieldChecker) == 1) {
                $dateField = $tableName . '.' . $dateField;
            }
        }
        $itemPerPage = empty($itemPerPage) ? admin_settings('item_per_page') : $itemPerPage;
        $itemPerPage = filter_var($itemPerPage, FILTER_VALIDATE_INT) != false ? $itemPerPage : ITEM_PER_PAGE;
        $order = request()->query($paginationKey . '_ord');
        $col = request()->query($paginationKey . '_sort');
        $search = request()->query($paginationKey . '_srch');
        $frm = request()->query($paginationKey . '_frm');
        $to = request()->query($paginationKey . '_to');
        $comp = request()->query($paginationKey . '_comp');
        $ssf = request()->query($paginationKey . '_ssf');

        if ($order == 'a') {
            $order = 'asc';
        } else {
            $order = 'desc';
        }

        $comparison = ['e' => '=', 'lk' => 'like', 'l' => '<', 'le' => '<=', 'm' => '>', 'me' => '>=', 'ne' => '!='];
        $comparison = array_key_exists($comp, $comparison) ? $comparison[$comp] : $comparison = 'LIKE';
        if ($orderFields) {
            $allcol = $orderFields;
        }

        $whereFields = array_column($searchFields, 0);
        $whereFields = array_key_exists($ssf, $whereFields) ? $whereFields[$ssf] : array_values($whereFields);
        $getelements = [$paginationKey . '_srch' => $search, $paginationKey . '_ord' => $order, $paginationKey . '_sort' => $col, $paginationKey . '_frm' => $frm, $paginationKey . '_to' => $to, $paginationKey . '_ssf' => $ssf, $paginationKey . '_comp' => $comp];
        if (isset($allcol)) {
            $column = $this->_column_query($allcol, $paginationKey . '_sort', $tableName . '.id');
        }

        $srcharr = $comparison == 'like' ? explode(' ', $search) : $search;

        foreach ($getelements as $key => $val) {
            if ($val == '') {
                unset($getelements[$key]);
            } elseif ($key == $paginationKey . '_frm' || $key == $paginationKey . '_to') {
                if (validate_date($val) == false) {
                    unset($getelements[$key]);
                }
            }
        }


        if ($joinArray != null && is_array($joinArray[0])) {
            foreach ($joinArray as $arr) {
                if(isset($arr[4])){
                    $this->model = $this->model->leftJoin($arr[0], function($join) use($arr) {
                        $join->on($arr[1], $arr[2], $arr[3])
                            ->where($arr[4]);
                    });
                }
                else {
                    $this->model = $this->model->leftJoin($arr[0], $arr[1], $arr[2], $arr[3]);
                }
            }
        } elseif ($joinArray != null) {
            if(isset($joinArray[4])){
                $this->model = $this->model->leftJoin($joinArray[0], function($join) use($joinArray) {
                    $join->on($joinArray[1], $joinArray[2], $joinArray[3])
                        ->where($joinArray[4]);
                });
            }
            else{
                $this->model = $this->model->leftJoin($joinArray[0], $joinArray[1], $joinArray[2], $joinArray[3]);
            }
        }

        if (isset($getelements[$paginationKey . '_frm'])) {
            $this->model = $this->model->where($dateField, '>=', $frm);
        }
        if (isset($getelements[$paginationKey . '_to'])) {
            $this->model = $this->model->where($dateField, '<', Carbon::parse($to)->addDay());
        }

        if (!is_null($whereArray)) {
            $this->model = $this->_where_builder($whereArray);
        }

        if (!empty($search)) {
            $this->model = $this->model->where(function ($query) use ($srcharr, $whereFields, $comparison) {
                $firstQuery = 1;
                if (is_array($srcharr) && $comparison == 'like') {
                    foreach ($srcharr as $wh) {
                        if (is_array($whereFields)) {
                            foreach ($whereFields as $sf) {
                                if ($firstQuery == 1) {
                                    $query->where($sf, 'like', '%' . $wh . '%');
                                } else {
                                    $query->orWhere($sf, 'like', '%' . $wh . '%');
                                }
                                $firstQuery = 0;
                            }
                        } else {
                            if ($firstQuery == 1) {
                                $query->where($whereFields, 'like', '%' . $wh . '%');
                            } else {
                                $query->orWhere($whereFields, 'like', '%' . $wh . '%');
                            }
                            $firstQuery = 0;
                        }
                    }
                } else {
                    if (is_array($whereFields)) {
                        foreach ($whereFields as $sf) {
                            if ($firstQuery == 1) {
                                $query->where($sf, $comparison, $srcharr);
                            } else {
                                $query->orWhere($sf, $comparison, $srcharr);
                            }
                            $firstQuery = 0;
                        }
                    } else {
                        $query->where($whereFields, $comparison, $srcharr);
                    }
                }
            });
        }
        if ($selectData != null) {
            $this->model = $this->model->select($selectData);
        }
        if(!empty($groupBy)){
            $this->model = $this->model->groupBy($groupBy);
        }

        if (!empty($column)) {
            $this->model = $this->model->orderBy($column, $order);
        } else {
            $this->model = $this->model->orderBy($tableName . '.id', $order);
        }
        $data = $this->model->paginate($itemPerPage, ['*'], $paginationKey)->appends($getelements);

        return $data;
    }
}
