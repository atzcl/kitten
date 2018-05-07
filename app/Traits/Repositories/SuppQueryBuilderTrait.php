<?php

declare( strict_types = 1);

/*
+-----------------------------------------------------------------------------------------------------------------------
| Author: 植成樑 <atzcl0310@gmail.com>  Blog：https://www.atzcl.cn
+-----------------------------------------------------------------------------------------------------------------------
| 补充 BaseRepository 基类的方法，主要用于增加 Query Builder 的各类用法,并增加链式操作 [ 或许使用魔术方法来实现会更加好？ ]
|
*/

namespace App\Traits\Repositories;

use Prettus\Repository\Events\RepositoryEntityUpdated;

trait SuppQueryBuilderTrait
{
    /**
     * 关闭自动维护创建 UUID
     *
     * @return $this
     */
    public function closeAutoGenerateUUID()
    {
        $this->model::closeAutoGenerateUUID();

        return $this;
    }

    /**
     * 带条件的更新
     *
     * @param array $attributes
     * @param array $where
     * @return mixed
     */
    public function updateByFields(array $attributes, array $where = [])
    {
        $this->applyScope();

        $temporarySkipPresenter = $this->skipPresenter;

        $this->skipPresenter(true);

        $model = $this->model->where($where)->first();
        $model->fill($attributes);
        $model->save();

        $this->skipPresenter($temporarySkipPresenter);
        $this->resetModel();

        event(new RepositoryEntityUpdated($this, $model));

        return $this->parserResult($model);
    }

    /**
     * 还原软删除 [ 其实不应该放在这里的，后面再规划下 ]
     *
     * @return void
     */
    public function runRestore()
    {
        $this->model->restore();
    }

    /**
     * 批量添加 where 条件 [ 拓展为其他 where 条件 ]
     *
     * @param array $where
     * @param string $method
     * @return $this
     */
    public function addConditions(array $where, $method = 'where')
    {
        // 遍历条件
        foreach ($where as $field => $value) {
            if (is_array($value)) {
                // 如果是数组，那就代表是自带验证规则
                list($condition, $val) = $value;
                $this->model = $this->model->{$method}($field, $condition, $val);
            } else {
                // 如果不是，那么就默认是 【 = 】条件规则
                $this->model = $this->model->{$method}($field, '=', $value);
            }
        }

        return $this;
    }

    /**
     * orWhere 查询
     *
     * @param array $where
     * @return $this
     */
    public function addOrConditions(array $where)
    {
        return $this->addConditions($where, 'orWhere');
    }

    /**
     * whereBetween 查询
     *
     * @param array $where
     * @param string $method
     * @return $this
     */
    public function addBetweenConditions(array $where, $method = 'whereBetween')
    {
        // 遍历条件
        foreach ($where as $field => $value) {
            if (!is_array($value)) {
                abort(422, '查询条件必须要为数组');
            }

            $this->model = $this->model->{$method}($field, $value);
        }

        return $this;
    }

    /**
     * 自增
     *
     * @param string $column 自增字段名称
     * @param int $amount 自增个数
     * @param array $where 更新条件
     * @return int
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    protected function increment($column, $amount = 1, array $where = [])
    {
        return $this->incrementOrDecrement($column, $amount, $where, 'increment');
    }

    /**
     * 自减
     *
     * @param string $column 自减字段名称
     * @param int $amount 自减个数
     * @param array $where 更新条件
     * @return int
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    protected function decrement($column, $amount = 1, array $where = [])
    {
        return $this->incrementOrDecrement($column, $amount, $where, 'decrement');
    }

    /**
     * 判断执行 increment 或者 decrement
     *
     * @param $column
     * @param $amount
     * @param $where
     * @param $method
     * @return mixed
     */
    protected function incrementOrDecrement($column, $amount, $where, $method)
    {
        $this->applyScope();

        $temporarySkipPresenter = $this->skipPresenter;

        $this->skipPresenter(true);

        $model = $this->model->where($where)->{$method}($column, $amount);

        $this->skipPresenter($temporarySkipPresenter);
        $this->resetModel();

        event(new RepositoryEntityUpdated($this, $this->model));

        // 返回包装结果
        return $this->parserResult($model);
    }

    /**
     * 将软删除的数据也查询到数据集中
     *
     * @return $this
     */
    public function withTrashed(): self
    {
        $this->model = $this->model->withTrashed();

        return $this;
    }

    /**
     * 只查询软删除的数据
     *
     * @return self
     */
    public function onlyTrashed(): self
    {
        $this->model = $this->model->onlyTrashed();

        return $this;
    }

    /**
     * 默认情况下是对 created_at 字段进行排序。或者，你可以传递你想要排序的字段名称
     *
     * @return $this
     */
    public function latest($column = 'created_at'): self
    {
        return $this->orderBy($column, 'desc');
    }

    // 同上
    public function oldest($column = 'created_at'): self
    {
        return $this->orderBy($column, 'asc');
    }

    /**
     * 设置偏移量值
     *
     * @param  int  $value
     * @return $this
     */
    public function offset($value): self
    {
        $this->model = $this->model->offset($value);

        return $this;
    }

    /**
     * 查询条数
     *
     * @param int $limit 条数
     * @return $this
     */
    public function limit(int $limit): self
    {
        $this->model = $this->model->limit($limit);

        return $this;
    }

    /**
     * 统计
     *
     * @param array $where
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function countData(array $where = [])
    {
        $this->applyCriteria();
        $this->applyScope();

        $results = $this->model->where($where)->count();

        $this->resetModel();
        $this->resetScope();

        return $this->parserResult($results);
    }
}
