<?php

namespace Sebdesign\SM\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\TableSeparator;

class Debug extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'winzou:state-machine:debug {graph? : A state machine graph}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $config;

    /**
     * Create a new command instance.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct();

        $this->config = $config;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (empty($this->config)) {
            return $this->error('There are no state machines configured.');
        }

        if (! $this->argument('graph')) {
            $this->askForGraph();
        }

        $graph = $this->argument('graph');

        if (! array_key_exists($graph, $this->config)) {
            return $this->error('The provided state machine graph is not configured.');
        }

        $config = $this->config[$graph];

        $this->printStates($config['states']);
        $this->printTransitions($config['transitions']);
    }

    /**
     * Ask for a graph name if one was not provided as argument.
     */
    protected function askForGraph()
    {
        $choices = array_map(function ($name, $config) {
            return $name."\t(".$config['class'].' - '.$config['graph'].')';
        }, array_keys($this->config), $this->config);

        $choice = $this->choice('Which state machine would you like to know about?', $choices, 0);

        $choice = substr($choice, 0, strpos($choice, "\t"));

        $this->info('You have just selected: '.$choice);

        $this->input->setArgument('graph', $choice);
    }

    /**
     * Display the graph states on a table.
     *
     * @param array $states
     */
    protected function printStates(array $states)
    {
        $this->table(['Configured States:'], array_map(function ($state) {
            return [$state];
        }, $states));
    }

    /**
     * Display the graph transitions on a table.
     *
     * @param array $transitions
     */
    protected function printTransitions(array $transitions)
    {
        end($transitions);

        $lastTransition = key($transitions);

        reset($transitions);

        $rows = [];

        foreach ($transitions as $name => $transition) {
            $rows[] = [$name, implode("\n", $transition['from']), $transition['to']];

            if ($name !== $lastTransition) {
                $rows[] = new TableSeparator();
            }
        }

        $this->table(['Transition', 'From(s)', 'To'], $rows);
    }
}
