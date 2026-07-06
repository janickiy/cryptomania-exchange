<?php

namespace App\Console\Commands\Core;

use Illuminate\Console\GeneratorCommand;

class MakeInterface extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:interface';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new interface';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Interface';

    /**
     * Purpose: describes the get stub contract for MakeInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__ . '/Stubs/interface.plain.stub';
    }

    /**
     * Purpose: describes the get default namespace contract for MakeInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace(mixed $rootNamespace): string
    {
        return $rootNamespace . '\Repositories';
    }



}
