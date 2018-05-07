<?php

declare(strict_types = 1);

/*
+-----------------------------------------------------------------------------------------------------------------------
| Author: 植成樑 <atzcl0310@gmail.com>  Blog：https://www.atzcl.cn
+-----------------------------------------------------------------------------------------------------------------------
| 辅助自定义 artisan 命令
|
*/

namespace App\Traits\Commands;

trait ScaffoldCommand
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
     * @var array 创建文件所需的参数
     */
    protected $params = [];

    /**
     * 设置键入的值
     *
     * @param array $arguments
     * @return void
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * 开始执行创建
     *
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function startMake()
    {
        // 组装创建文件所需的数据
        $this->createParams();

        // 获取需要创建的文件所匹配的 app 应用目录路径
        $path = $this->getPath();

        // 判断是否已经存在该文件
        if ($this->files->exists($path)) {
            $this->error('该文件已存在: ' . $path);
            return false;
        }

        // 判断是否需要创建文件目录
        $this->makeDirectory($path);

        // 将渲染的内容写入文件
        $this->files->put($path, $this->compileFileStub());

        return $path;
    }

    /**
     * 获取需要创建的文件路径
     *
     * @return string
     */
    public function getPath(): string
    {
        return app_path('Modules' . '/' . $this->arguments['module'] . '/' .
            title_case(str_plural($this->defaultFileType)) . '/' . $this->arguments['name'] . '.php');
    }

    /**
     * 判断传入的路径是否为目录，如果不是，那么就创建该目录
     *
     * @param string $path
     */
    public function makeDirectory(string $path)
    {
        if (! $this->files->isDirectory(dirname($path))) {
            // 创建目录
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
    }

    /**
     * 处理 stub 文件
     *
     * @param string $stubsPath 文件前缀路径
     * @param string $ext 文件类型后缀
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function compileFileStub(
        string $stubsPath = 'Console/Commands/stubs/create',
        string $ext = 'stub'
    ): string {
        // 获取 stub 文件内容
        $stubs = $this->files->get(app_path($stubsPath . '/' . $this->defaultFileType . '.' . $ext));

        // 替换渲染
        $this->renderStub($this->params, $stubs);

        // 返回渲染完成的文件内容
        return $stubs;
    }


    /**
     * 组装创建文件所需的数据
     *
     * @return void
     */
    protected function createParams()
    {
        // 储存创建文件的命名空间
        $this->params['namespace'] = $this->getNamespace();
        // 文件名（类名）
        $this->params['class'] = $this->getClass();
    }

    /**
     * 获取创建文件的命名空间
     *
     * @return string
     */
    public function getNamespace()
    {
        // 获取文件名的前缀部分
        $extra = str_replace($this->getClass(), '', $this->arguments['name']);

        // 替换 /
        $extra = str_replace('/', '\\', $extra);

        // 拼接完整的命名空间
        $namespace = config('modules.namespace', 'Modules');
        $namespace .= '\\' . $this->arguments['module'];
        $namespace .= '\\' . title_case(str_plural($this->defaultFileType));
        $namespace .= '\\' . $extra;

        // 拼接返回
        return trim($namespace, '\\');
    }

    /**
     * 获取文件名
     *
     * @return string
     */
    public function getClass()
    {
        return class_basename($this->arguments['name']);
    }

    /**
     * 渲染替换 stub 文件内容
     *
     * @param array $params 替换的变量数组
     * @param string $template 通过 file_get_contents / fopen 打开的文件
     * @return mixed
     */
    public function renderStub(array $params, string &$template)
    {
        foreach ($params as $key => $param) {
            // 替换变量
            $template = str_replace('$' . strtoupper($key) . '$', $param, $template);
        }

        // 返回替换完毕的内容
        return $template;
    }
}
