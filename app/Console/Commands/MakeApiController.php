<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class MakeApiController extends GeneratorCommand
{
    protected $name = 'make:api-controller';
    protected $description = 'Create a new API controller with optional model binding and auth protection.';
    protected $type = 'Controller';

    protected function getStub()
    {
        return __DIR__.'/stubs/api-controller.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\\Http\\Controllers\\Api';
    }

    public function handle()
    {
        $name = $this->getNameInput();
        if (!Str::endsWith($name, 'Controller')) {
            $name .= 'Controller';
        }
        $model = $this->option('model') ?: Str::studly(class_basename($name));
        $noAuth = $this->option('no-auth');
        $controllerClass = class_basename($name);
        $namespace = $this->getDefaultNamespace($this->laravel->getNamespace());

        $replace = [
            'DummyClass' => $controllerClass,
            'DummyModel' => $model,
            'DummyParent' => $noAuth ? 'BaseApiController' : 'ProtectedApiController',
        ];

        $relativePath = str_replace('\\', '/', $name) . '.php';
        $path = base_path('app/Http/Controllers/Api/' . $relativePath);
        $this->makeDirectory($path);
        $stub = $this->files->get($this->getStub());
        $stub = str_replace(array_keys($replace), array_values($replace), $stub);
        $this->files->put($path, $stub);
        $this->info($this->type.' created successfully.');
    }

    protected function getOptions()
    {
        return [
            ['model', null, InputOption::VALUE_OPTIONAL, 'Optional model name for resource binding'],
            ['no-auth', null, InputOption::VALUE_NONE, 'If set, controller will not be protected by auth'],
        ];
    }
}
