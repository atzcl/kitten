<?php

declare(strict_types = 1);

/*
+-----------------------------------------------------------------------------------------------------------------------
| Author: 植成樑 <atzcl0310@gmail.com>  Blog：https://www.atzcl.cn
+-----------------------------------------------------------------------------------------------------------------------
| 监听 l5-repository 的 CUD 事件
|
*/

namespace App\Listeners;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Contracts\RepositoryInterface;
use Prettus\Repository\Events\RepositoryEventBase;
use Prettus\Repository\Helpers\CacheKeys;

class CleanCacheRepository
{
    /**
     * @var CacheRepository
     */
    protected $cache = null;

    /**
     * @var RepositoryInterface
     */
    protected $repository = null;

    /**
     * @var Model
     */
    protected $model = null;

    /**
     * @var string
     */
    protected $action = null;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * 处理 CUD 操作时候，清理相关缓存
     *
     * @param  RepositoryEventBase  $event
     * @return void
     */
    public function handle(RepositoryEventBase $event)
    {
        $this->repository = $event->getRepository();
        $this->model = $event->getModel();
        $this->action = $event->getAction();

        // 缓存实例
        $cacheRepository = $this->repository->getCacheRepository();

        /* if ($this->action === 'deleted') {
            // 如果执行的是删除，那么只需要清理该 id 在缓存系统的数据，而不需要清理 where 类别的的数据
            $keys = $this->repository->getCacheKey('id', $this->model->id);
        } else {
            // 获取 where 类别缓存数据库的中，所属当前模型的所有 key
            $keys = $cacheRepository->setDatabase('where')->getCacheInstance()->keys(
                class_basename(get_class($event->getModel())) . '*'
            );
        } */

        // 获取 where 类别缓存数据库的中，所属当前模型的所有 key [ CUD 操作都需要清除所有隶属 where 类别的缓存 ]
        $keys = $cacheRepository->setDatabase('where')->getCacheInstance()->keys(
            class_basename(get_class($event->getModel())) . '*'
        );

        if (! empty($keys)) {
            // 删除缓存
            $cacheRepository->forget($keys);
        }
    }
}
