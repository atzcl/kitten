<?php

declare(strict_types = 1);

/*
+-----------------------------------------------------------------------------------------------------------------------
| Author: 植成樑 <atzcl0310@gmail.com>  Blog：https://www.atzcl.cn
+-----------------------------------------------------------------------------------------------------------------------
| 实现 l5-repository 的缓存处理 [ 后面会把 redis 缓存抽离出来，这里应该只单纯地处理重写 find 等方法 ]
|
*/

namespace App\Traits\Repositories;

use App\Foundation\CacheRepository;
use Closure;

trait CacheTrait
{
    /**
     * @var CacheRepository
     */
    protected $cacheRepository = null;

    /**
     * @var string 储存类别 [ id/where/list ]
     */
    public $cacheKeyType = 'id';

    /**
     * @var string 调用的方法
     */
    public $storeMethod;

    /**
     * @var array 用于组合 cache key 的 Query Builder 类属性
     */
    public $queryBuilderProperty = [
        'wheres', 'limit', 'offset', 'orders', 'columns', 'joins', 'groups', 'havings'
    ];

    /**
     * @var array 调用方法的所有参数，用于组合 cache key
     */
    public $args;

    /**
     * The columns that should be returned.
     *
     * @var array
     */
    public $columns;

    /**
     * The maximum number of records to return.
     *
     * @var int
     */
    public $limit;

    /**
     * 设置 cache key 储存类别 [ 目前决定只留 id/where 类别 ]
     *
     * @param string $type 储存类别
     * @return $this
     */
    public function setCacheKeyType(string $type)
    {
        $storeList = [
            'id' => 'id', 'where' => 'where', 'list' => 'list'
        ];

        $this->cacheKeyType = $storeList[$type] ?? 'id';

        return $this;
    }

    /**
     * 设置调用的方法
     *
     * @param string $method 设置调用的方法
     * @return $this
     */
    public function setStoreMethod(string $method)
    {
        $this->storeMethod = $method;

        return $this;
    }

    /**
     * 设置所有调用方法的所有参数
     *
     * @param null $args
     * @return $this
     */
    public function setMethodArgs($args = null)
    {
        $this->args = $args;

        return $this;
    }

    /**
     * 设置条数
     *
     * @param int $limit
     * @return $this
     */
    public function setLimit(int $limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * 设置 columns 字段集合
     *
     * @param array $columns
     * @return $this
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * 设置 Cache Repository 实例
     *
     * @param CacheRepository $cacheRepository
     *
     * @return $this
     */
    public function setCacheRepository(CacheRepository $cacheRepository)
    {
        $this->cacheRepository = $cacheRepository;

        return $this;
    }

    /**
     * 获取 Cache Repository 实例
     *
     * @return \App\Foundation\CacheRepository
     */
    public function getCacheRepository()
    {
        if (is_null($this->cacheRepository)) {
            $this->cacheRepository = app(
                config('cache_repository.repository', 'App\Foundation\CacheRepository')
            );
        }

        return $this->cacheRepository;
    }

    /**
     * 获取缓存 key
     *
     * @param string $value 需要加密的值
     * @param string $type 加密类型
     * @return string
     */
    public function getCacheKey(string $type = null, string $value = '')
    {
        $cacheKeyType = is_null($type) ? $this->cacheKeyType : $type;

        /**
         * 获取不同的缓存 key 后缀 [其实不需要加 `类型` 也是可以的，因为已经是分数据库存放了]
         *
         * id 类型：后缀后传入的 md5 加密过的 UUID, 例子：User:3ba946c5c6d8a68a3c780b9a4324401b
         * where 类型：类型 + md5 加密 where 条件,例子：User:ce4c38818790e221cc7a9fb7a5298c76
         */
        return $cacheKeyType === 'id' ? $this->generateIdKey($value) : $this->generateWhereAndListKey();
    }

    /**
     * 生成 id 储存类别的 key
     *
     * @param string $id
     * @return string
     */
    public function generateIdKey(string $id)
    {
        return class_basename($this->model()) . ':id:' . md5($id);
    }

    /**
     * 生成 where/list 储存类别的 key
     *
     * @return string
     */
    public function generateWhereAndListKey()
    {
        // Query Builder 类属性值
        $queryBuilderPropertyValue = [];

        // 获取 Query Builder 类属性值
        foreach ($this->queryBuilderProperty as $property) {
            $queryBuilderPropertyValue[] = $this->model->getQuery()->$property;
        }

        return class_basename($this->model()) . ':' . $this->storeMethod . ':' . md5(
            $this->getCacheRepository()->serialize($queryBuilderPropertyValue)
            .
            $this->getCacheRepository()->serialize($this->args)
        );
    }

    /**
     * 获取缓存过期时间
     *
     * @return int
     */
    public function getCacheMinutes()
    {
        return $this->cacheMinutes ?? config('cache_repository.minutes', 5);
    }

    /**
     * 判断是否需要跳过缓存
     *
     * @return bool
     */
    public function isSkippedCache()
    {
        $skipped = isset($this->cacheSkip) ? $this->cacheSkip : false;
        $skipCacheParam = config('cache_repository.params.skipCache', 'skipCache');

        // 在本地的情况下，如果 url 上带有指定参数，将可以跳过缓存
        if (config('app.env') === 'local' && app('request')->has($skipCacheParam)) {
            $skipped = true;
        }

        return $skipped;
    }

    /**
     * 根据 id 进行查找
     *
     * @param $id
     * @param array $columns
     * @return \Illuminate\Support\Collection
     */
    public function find($id, $columns = ['*'])
    {
        // 跳过缓存
        if ($this->isSkippedCache()) {
            return parent::find($id);
        }

        // 设置储存类别、获取过期时间
        $minutes = $this->getCacheMinutes();

        // 因为 find 除了可以传入单一 id,还可以传入 id 集合，所以并不能简单地用 remember 来做缓存处理
        if (! is_array($id)) {
            // 将传入单一 id 的情况传化为 id 集合形式
            $id = [ $id ];
        }

        // 获取 id 集合的缓存情况
        list($results, $notCaches) = $this->getCollectionEachCache($id);

        if (! empty($notCaches)) {
            // 如果有不存在缓存系统的数据，那么就应该到数据库查询
            $collects = parent::find($notCaches);

            if ($collects) {
                // 获取模型数据集合的所有的数据
                $collectAll = collect($collects)->all();
                // 批量写入缓存
                $this->batchSetupCache($collectAll, $minutes);
                // 合并 [ 缓存的数据 + 查询得到数据 ]
                $results = array_merge($results, $collectAll);
            }
        }

        $this->resetModel();
        $this->resetScope();

        // 为了保持跟 ORM 的一致性，所以这里需要做 [ 单个跟集合 ] 的输出
        return count($results) === 1 ? $results[0] : collect($results);
    }

    /**
     * 查询所有数据
     *
     * @param array $columns
     * @return array|string
     */
    public function all($columns = ['*'])
    {
        // 跳过缓存
        if ($this->isSkippedCache()) {
            return parent::all();
        }

        // 设置调用方法
        $this->setStoreMethod(__FUNCTION__);
        // 获取 key
        $key = $this->getCacheKey('where');
        // 获取过期时间
        $minutes = $this->getCacheMinutes();

        $results = $this->getCacheRepository()->simpleRemember($key, function ($res) {
            // 因为 where/list 的缓存实际是数据的 id 集合，所以这里可以直接使用 find 来查询
            return $this->find($res)->all();
        }, function () use ($key, $minutes) {
            // 如果缓存不存在，那么直接查询数据库
            $results = parent::all()->all();
            // 集合的指定属性列表
            $collectionAttributes = $results;

            // 如果查询结果不为空
            if (! empty($results)) {
                $collectionAttributes = $this->saveNotExistentCache($collectionAttributes, $minutes);
            }

            // 将本次查询结果设置到缓存
            $this->setWhereCache($key, $minutes, $collectionAttributes);

            return $results;
        });

        $this->resetModel();
        $this->resetScope();

        return $results;
    }

    /**
     * 查询分页
     *
     * @param null $limit
     * @param array $columns
     * @param string $method
     * @return mixed
     */
    public function paginate($limit = null, $columns = ['*'], $method = 'paginate')
    {
        // 跳过缓存
        if ($this->isSkippedCache()) {
            return $this->forPage($limit = null, $columns = ['*'])->basePaginate();
        }

        // 预设置相关值
        $this->setMethodArgs(func_get_args()) // 当前方法的所有参数
            ->setStoreMethod(__FUNCTION__) // 当次方法的类型
            ->forPage($limit = null, $columns = ['*']);

        // 获取缓存 key
        $key = $this->getCacheKey('where');
        // 获取过期时间
        $minutes = $this->getCacheMinutes();

        $result = $this->getCacheRepository()->simpleRemember($key, function ($res) use ($minutes) {
            // 如果缓存存在，那么就应该去查找对应的具体数据
            $results = $this->find($res['items'] ?? [])->all();

            return array_merge($res, [ 'items' => $results ]);
        }, function () use ($key, $minutes, $limit, $columns) {
            // 如果缓存不存在，直接查询数据库
            $results = $this->basePaginate();
            // 集合的指定属性列表
            $collectionAttributes = $results['items'];

            // 如果查询结果不为空
            if (! empty($collectionAttributes)) {
                $collectionAttributes = $this->saveNotExistentCache($collectionAttributes, $minutes);
            }

            // 切换缓存数据库，将本次查询结果设置到缓存
            $this->setWhereCache(
                $key,
                $minutes,
                array_merge($results, [ 'items' =>  $collectionAttributes])
            );

            return $results;
        });

        $this->resetModel();
        $this->resetScope();

        return $result;
    }

    /**
     * 设置分页的 limit、offset
     *
     * @param  int|null  $limit
     * @param  array  $columns
     * @return $this
     */
    public function forPage($limit = null, $columns = ['*'])
    {
        $request = app('request');

        // 每页查询条数
        $limit = is_null($limit) ? config('cache_repository.pagination.limit', 15) : $limit;
        // page 的属性名
        $pageName = config('cache_repository.pagination.page', 'page');
        // 获取查询第几页
        $page = $request->filled($pageName) ? (int) $request[$pageName] : 1;
        // 设置 limit， columns
        $this->setLimit((int) $limit)->setColumns($columns);
        // 为了防止 getQuery 获取不到响应
        $this->model = $this->model->forPage($page, $limit);

        return $this;
    }


    /**
     * 基础分页查询
     * 因为使用缓存跟当前项目是纯 api 的形式，所以没必要沿用自带的 paginate 方法，根据源码改造一下即可
     * @see /vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php 1732 行
     *
     * @return array
     */
    public function basePaginate()
    {
        // 获取总数
        $total = $this->model->getQuery()->getCountForPagination($this->columns);

        return [
            'total' => $total, // 总数
            'limit' => $this->limit, // 条数
            'items' => $total ? $this->model->get($this->columns)->all() : [] // 分页结果
            // ..... 更多，比如下一页、上一页、首页、尾页、url 等
        ];
    }

    /**
     * 设置 where 储存类别的缓存
     *
     * @param string $key
     * @param int $minutes
     * @param $value
     */
    public function setWhereCache(string $key, int $minutes, $value)
    {

        $this->getCacheRepository()->setDatabase('where')->set(
            $key,
            $minutes,
            $value
        );
    }

    /**
     * 判断数据集合的每一项缓存情况，把不存在缓存系统的数据批量保存到缓存系统中
     *
     * @param array $collections
     * @param int $minutes
     * @param string $column
     * @return mixed
     */
    public function saveNotExistentCache(array $collections, int $minutes, string $column = 'id')
    {
        // 切换到 id 储存类别所属的数据库
        $this->getCacheRepository()->setDatabase('id');

        // 获取该次查询结果集合的缓存情况
        list(
            $cacheList,
            $notCaches,
            $collectionAttributes
        ) = $this->getCollectionEachCache($collections, $column);

        // 将该次查询结果集合中，不存在缓存系统中的数据写入缓存中
        $this->setCacheKeyType('id')->batchSetupCache($notCaches, $minutes);

        // 集合指定属性列表
        return $collectionAttributes;
    }

    /**
     * 获取传入集合每一项的缓存情况
     *
     * @param array $collections 需要进行缓存判断的集合
     * @param string|null $column 指定属性名称
     * @return array
     */
    public function getCollectionEachCache(array $collections, string $column = null)
    {
        // 已经存在缓存系统中的数据
        $existCaches = [];
        // 还没写入缓存的原始数据集合
        $notCaches = [];
        // 原始数据的指定属性集合
        $collectionAttributes = [];

        // 循环判断传入的集合各自的缓存情况
        foreach ($collections as $collection) {
            // 用于生成密钥的值
            $keyValue = $collection;

            // 判断是否需要获取指定属性
            if (!is_null($column) && isset($collection[$column])) {
                $keyValue = $collection[$column];

                // 将原始数据的指定属性集合存放在一起
                $collectionAttributes[] = $collection[$column];
            }

            // 尝试获取缓存数据
            $value = $this->getCacheRepository()->get($this->getCacheKey(null, $keyValue));

            // 判断该缓存是否存在
            if (! is_null($value)) {
                // 将已经存在的缓存数据放置在一起
                $existCaches[] = $value;
            } else {
                // 将不存在缓存系统中的原始数据放置在一起
                $notCaches[] = $collection;
            }
        }

        unset($key, $value);

        /**
         * $cacheList 存在缓存系统数据集合
         * $notCaches 不存在缓存系统的数据集合
         * $collectionAttributes 集合的指定属性列表
         */
        return [ $existCaches, $notCaches, $collectionAttributes ];
    }

    /**
     * 批量写入缓存
     *
     * @param array $collections
     * @param int $minutes
     * @param string $column
     */
    public function batchSetupCache(array $collections, int $minutes, string $column = 'id')
    {
        // 通过管道来批量写入
        $this->getCacheRepository()->getCacheInstance()->pipeline(
            function ($pipe) use ($collections, $minutes, $column) {
                foreach ($collections as $collection) {
                    $pipe->setex(
                        $this->getCacheKey(null, $collection[$column]),
                        (int) max(1, $minutes * 60),
                        $this->getCacheRepository()->serialize($collection)
                    );
                }
            }
        );
    }
}
