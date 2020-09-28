<?php

namespace Hogen\Constant;

use Hogen\Constant\Console\ConstantCacheCommand;
use Hogen\Constant\Console\ConstantClearCommand;
use Hogen\Constant\Console\ConstantMakeCommand;
use Hogen\Constant\Console\ConstantMetaCommand;
use Illuminate\Support\ServiceProvider;

class ConstantServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application urland constant.
     *
     * @return void
     */
    public function boot()
    {
        // 配置发布
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/constant.php' => config_path('constant.php'),
                __DIR__ . '/../resources/constants/demo.php' => resource_path('constants/demo.php')
            ], 'laravel-constant');
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__ . '/../config/constant.php';
        $this->mergeConfigFrom($configPath, 'constant');

        $this->app->singleton('constant.compiler', function ($app) {
            $compiledPath   = $app->bootstrapPath('cache/constants.php');
            $locale         = $app['config']['app.locale'];
            $fallbackLocale = $app['config']['app.fallback_locale'];

            return new ConstantCompiler($app['files'], $compiledPath, $locale, $fallbackLocale);
        });

        $this->app->singleton('constant', function ($app) {
            $compiler = $app['constant.compiler'];

            return new Constant($compiler, $app['config']['constant.path']);
        });

        // 注册控制台命令
        $this->registerCommands();
    }

    /**
     * 注册控制台命令
     */
    protected function registerCommands()
    {
        // 生成常量文件命令
        $this->app->singleton('command.constant.cache', function ($app) {
            $compiler = $app['constant.compiler'];
            $path     = $app['config']['constant.path'];

            return new ConstantCacheCommand($compiler, $path);
        });

        $this->app->singleton('command.constant.clear', function ($app) {
            $compiler = $app['constant.compiler'];

            return new ConstantClearCommand($app['files'], $compiler->getCompiledPath());
        });

        $this->app->singleton('command.constant.meta', function ($app) {
            $compiler = $app['constant.compiler'];
            $files    = $app['files'];
            $filename = $app['config']['constant.meta_filename'];

            return new ConstantMetaCommand($compiler, $files, $filename);
        });

        $this->commands([
            'command.constant.cache',
            'command.constant.clear',
            'command.constant.meta',
        ]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'constant',
            'command.constant.cache',
            'command.constant.clear',
            'command.constant.meta',
        ];
    }

    /**
     * Get the config path
     *
     * @return string
     */
    protected function getConfigPath()
    {
        return config_path('constant.php');
    }
}