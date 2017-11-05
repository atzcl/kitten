<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model extends EloquentModel
{
    // 定义全局排序
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
