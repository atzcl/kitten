<?php

declare(strict_types = 1);

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use App\Traits\Commands\ScaffoldCommand;
use App\Console\Commands\create\CreateHandler;

class CreateScaffoldCommand extends Command
{
    use ScaffoldCommand;

    /**
     * 控制台命令 signature 的名称。
     *
     * @var string
     */
    protected $signature = 'module:create {moduleName} {name}';

    /**
     * 控制台命令说明
     *
     * @var string
     */
    protected $description = '创建指定模块的相应文件';


    /**
     * Create constructor.
     *
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // 记录命令执行毫秒时间戳
        $startTime = Carbon::now()->micro;
        $this->line('----------- 开始创建 -------------------------');

         $this->createHandler();

         // 获取执行该命令的生命周期时长
        $depleteTime =  Carbon::now()->micro - $startTime;
        $this->line("----------- 创建完成，耗时：$depleteTime ms -----------");
    }

    /**
     * 执行创建业务处理器
     */
    private function createHandler()
    {
        new CreateHandler($this, $this->files);
    }
}
