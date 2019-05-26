<?php

namespace Hongyukeji\LaravelStorage;

use Hongyukeji\LaravelStorage\Adapters\AliyunOssAdapter;
use Hongyukeji\LaravelStorage\Adapters\QcloudCosAdapter;
use Hongyukeji\LaravelStorage\Adapters\QiniuAdapter;
use League\Flysystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class LaravelStorageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/storage.php' => config_path('filesystems.php'),
        ], 'storage_config');

        // 注册新的云存储文件驱动
        Storage::extend('aliyun-oss', function ($app, $config) {
            return new Filesystem(new AliyunOssAdapter('storage'));
        });
        Storage::extend('qiniu', function ($app, $config) {
            return new Filesystem(new QiniuAdapter('storage'));
        });
        Storage::extend('qcloud-cos', function ($app, $config) {
            return new Filesystem(new QcloudCosAdapter('storage'));
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/storage.php', 'filesystems'
        );
    }
}
