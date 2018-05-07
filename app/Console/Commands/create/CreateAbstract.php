<?php

declare(strict_types = 1);

/*
+-----------------------------------------------------------------------------------------------------------------------
|
+-----------------------------------------------------------------------------------------------------------------------
| 自定义创建文件命令接口类
|
*/

namespace App\Console\Commands\create;

use Illuminate\Filesystem\Filesystem;

abstract class CreateAbstract
{
    /**
     * @var \Illuminate\Filesystem\Filesystem; 操作文件实例
     */
    protected $files;

    /**
     * @var array 键入的值
     */
    protected $arguments = [];

    /**
     * @var string 创建的文件类型
     */
    protected $defaultFileType = 'service';

    /**
     * 设置键入的值
     *
     * @param array $arguments
     * @return void
     */
    abstract public function setArguments(array $arguments);

    // 创建相关变量
    abstract protected function createParams();

    // 处理具体业务
    abstract protected function start();

    // 获取创建文件真实物理路径
    abstract public function getPath(): string;

    // 处理 stubs 文件内容渲染
    abstract public function compileFileStub(
        string $stubsPath,
        string $ext
    ): string;
}
