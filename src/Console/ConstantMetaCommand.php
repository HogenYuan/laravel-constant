<?php

namespace Hogen\Constant\Console;

use Illuminate\Console\Command;
use Illuminate\View\Engines\PhpEngine;

/**
 * A command to generate constant meta data
 *
 */
class ConstantMetaCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'constant:meta';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate constant metadata for PhpStorm';

    /**
     * @var \Urland\Constant\ConstantCompiler
     */
    protected $compiler;

    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $files;

    /**
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $view;

    /**
     * @var string
     */
    protected $compiledPath;

    /**
     * Metadata methods.
     *
     * @var array
     */
    protected $keyMethods = [
        '\Urland\Constant\Constant::has(0)',
        '\Urland\Constant\Constant::get(0)',
        '\Urland\Constant\Constant::lang(0)',
        '\cons(0)',
    ];

    protected $valueMethods = [
        '\Urland\Constant\Constant::hasValue(0)',
        '\Urland\Constant\Constant::key(0)',
        '\Urland\Constant\Constant::valueLang(0)',
    ];

    /**
     *
     * @param \Urland\Constant\ConstantCompiler           $compiler
     * @param \Illuminate\Contracts\Filesystem\Filesystem $files
     * @param string                                      $compiledPath
     */
    public function __construct($compiler, $files, $compiledPath)
    {
        $this->compiler     = $compiler;
        $this->files        = $files;
        $this->compiledPath = $compiledPath;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Make constant cache update to date.
        $this->call('constant:cache');

        // Format render data.
        ['key' => $keyConstants, 'value' => $valueIndexConstants] = require $this->compiler->getCompiledPath();

        $constantKeys      = array_keys($keyConstants);
        $constantValueKeys = collect($valueIndexConstants)->keys()->map(function ($key) {
            return substr($key, 0, strrpos($key, '.'));
        })->unique();

        $content = $this->renderMetaFile([
            'keyMethods'        => $this->keyMethods,
            'valueMethods'      => $this->valueMethods,
            'constantKeys'      => $constantKeys,
            'constantValueKeys' => $constantValueKeys,
        ]);

        $written = $this->files->put($this->compiledPath, $content);

        if ($written !== false) {
            $this->info("A new meta file was written to {$this->compiledPath}");
        } else {
            $this->error("The meta file could not be created at {$this->compiledPath}");
        }
    }

    /**
     * Render constant meta content.
     *
     * @param array $data
     *
     * @return string
     */
    protected function renderMetaFile($data = [])
    {
        return (new PhpEngine())->get(__DIR__ . '/../../resources/views/meta.php', $data);
    }
}
