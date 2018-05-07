<?php

declare(strict_types = 1);

namespace Modules\User\Models;

use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    // 引入软删除、UUID 自动创建
    use SoftDeletes, GenerateUuid;

    // 项目默认使用 UUID 形式，所以需要关闭 Eloquent 的默认自增 id 行为
    public $incrementing = false;

    /**
     * @var string 设置表名
     */
    protected $table = 'users';

    /**
     * @var array 可批量赋值字段
     */
    protected $fillable = [
        'real_name',
        'nickname',
        'phone',
        'telephone',
        'email',
        'qq',
        'password',
        'username',
        'sex',
        'avatar',
        'contact',
        'address',
        'province',
        'city',
        'district',
        'zip',
    ];

    /**
     * @var array 输出隐藏字段
     */
    protected $hidden = [];

    /**
     * 加密密码
     *
     * @param string $value
     */
    public function setPasswordAttribute(string $value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
}
