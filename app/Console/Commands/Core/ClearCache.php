<?php

namespace App\Console\Commands\Core;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class ClearCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:all {--only=} {--except=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command clear cache from route, view, config and all cache data from application';


    protected $availableCommands;

    /**
     * Purpose: initializes the ClearCache instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->availableCommands = config('commonconfig.available_commands');
    }

    /**
     * Purpose: executes the console command.
     *
     * Action: runs an application action from the CLI and reports the result to the terminal.
     *
     * Execute the console command.
     *
     */
    public function handle(): void
    {
        if (!is_null($this->option('only'))) {
            $only = explode(',',$this->option('only'));
            $this->availableCommands = Arr::only($this->availableCommands, $only);
        } elseif (!is_null($this->option('except'))) {
            $except = explode(',',$this->option('except'));
            $this->availableCommands = Arr::except($this->availableCommands, $except);
        }
        foreach ($this->availableCommands as $key => $command) {
            $this->call($command);
        }
    }
}
