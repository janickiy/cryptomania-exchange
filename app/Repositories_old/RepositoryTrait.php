<?php

namespace App\Repositories;

trait RepositoryTrait
{
    /**
     * Purpose: performs the column query operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     * @param $columns
     * @param string $key
     * @param null $default
     * @return false|mixed
     */
    protected function _column_query(array $columns, string $key = 'sort', ?string $default = null): string|false
    {
        $data = request()->query($key);
        if (is_numeric($data) && array_key_exists($data, array_column($columns, 0))) {
            return $columns[$data][0];
        }
        return false;
    }

    /**
     * Purpose: performs the where builder operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     * @param $where
     * @return mixed
     */
    protected function _where_builder(array $where): object
    {
        $model = $this->model;
        $output = $this->_subWhere($model, $where);
        return $output;
    }

    /**
     * Purpose: performs the sub where operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     * @param $model
     * @param $where
     * @return mixed
     */
    protected function _subWhere(object $model, array $where): object
    {
        if (count($where) <= 0) {
            return $model;
        }
        if (
            !isset($where['where_type']) &&
            !isset($where['group_query']) &&
            !isset($where[0])
        ) {
            $model = $model->where($where);
        } elseif (
            !isset($where['where_type']) &&
            !isset($where['group_query']) &&
            isset($where[0])
        ) {
            if (!is_array($where[0])) {
                $model = $model->where([$where]);
            } else {
                foreach ($where as $where_single) {
                    $model = $this->_subWhere($model, $where_single);
                }
            }
        } elseif (
            isset($where['where_type']) &&
            in_array($where['where_type'], ['whereBetween', 'whereNotBetween', 'whereIn', 'whereNotIn'])
        ) {
            $model = $model->{$where['where_type']}($where[0][0], $where[0][1]);
        } elseif (
            !isset($where['group_query']) ||
            (isset($where['group_query']) && $where['group_query'] !== true)
        ) {
            $wheretype = 'where';
            if ($where['where_type'] == 'orWhere') {
                $wheretype = 'orWhere';
            }
            if (isset($where[0][0])) {
                if (is_array($where[0][0])) {
                    $model = $model->{$wheretype}($where[0]);
                } else {
                    $model = $model->{$wheretype}([$where[0]]);
                }
            } else {
                $model = $model->{$wheretype}($where[0]);
            }
        } elseif (isset($where['group_query']) && $where['group_query'] === true) {
            $wheretype = 'where';
            if ($where['where_type'] == 'orWhere') {
                $wheretype = 'orWhere';
            }
            $model = $model->{$wheretype}(function ($query) use ($where) {
                foreach ($where[0] as $sub_key => $sub_val) {
                    $query = $this->_subWhere($query, $sub_val);
                }
            });
        }
        return $model;
    }

    /**
     * Purpose: performs the get updatable fields operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     * @param $values
     * @return array|false|int[]|string[]
     */
    private function _getUpdatableFields(array $values): array|false
    {
        $fields = [];
        $conditions = [];
        foreach ($values as $value) {
            if (count(array_intersect_key($value['fields'], $value['conditions'])) >= 2) {
                return false;
            }
            $fields = array_merge($fields, $value['fields']);
            $conditions = array_merge($conditions, $value['conditions']);
        }
        $fields = array_keys($fields);
        $conditions = array_keys($conditions);
        $commonFields = array_intersect($fields, $conditions);
        if (count($commonFields) >= 1) {
            $fields = array_merge(array_diff($fields, $commonFields), $commonFields);
        }
        return $fields;
    }

    /**
     * Purpose: performs the extract to array operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     * @param $relations
     * @return array|false|string[]
     */
    public function extractToArray(string|array|null $relations): array
    {

        if (is_string($relations)) {
            return explode(',', $relations);
        }

        return is_array($relations) ? $relations : [];

    }
}
