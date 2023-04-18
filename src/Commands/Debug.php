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
    protected $description = 'Show states and transitions of state machine graphs';

    protected $config;

    /**
     * Create a new command instance.
     *
     * @param  array  $config
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
            $this->error('There are no state machines configured.');

            return 1;
        }

        if (! $this->argument('graph')) {
            $this->askForGraph();
        }

        $graph = $this->argument('graph');

        if (! array_key_exists($graph, $this->config)) {
            $this->error('The provided state machine graph is not configured.');

            return 1;
        }

        $config = $this->config[$graph];

        $this->printStates($config['states']);
        $this->printTransitions($config['transitions']);

        if (isset($config['callbacks'])) {
            $this->printCallbacks($config['callbacks']);
        }

        return 0;
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
     * @param  array  $states
     */
    protected function printStates(array $states)
    {
        $this->table(['Configured States:', 'Metadata:'], array_map(function ($state) {
            if (is_null($state)) {
                return [$state, null];
            }

            if (is_string($state)) {
                return [$state, null];
            }

            if (isset($state['metadata'])) {
                $metadata = json_encode($state['metadata'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            } else {
                $metadata = null;
            }

            return [$state['name'], $metadata];
        }, $states));
    }

    /**
     * Display the graph transitions on a table.
     *
     * @param  array  $transitions
     */
    protected function printTransitions(array $transitions)
    {
        end($transitions);

        $lastTransition = key($transitions);

        reset($transitions);

        $rows = [];

        foreach ($transitions as $name => $transition) {
            $rows[] = [$name, implode(PHP_EOL, $transition['from']), $transition['to']];

            if ($name !== $lastTransition) {
                $rows[] = new TableSeparator();
            }
        }

        $this->table(['Transition', 'From(s)', 'To'], $rows);
    }

    /**
     * Display the graph callbacks on a table.
     *
     * @param  array  $allCallbacks
     */
    protected function printCallbacks(array $allCallbacks)
    {
        foreach ($allCallbacks as $position => $callbacks) {
            $rows = [];
            foreach ($callbacks as $name => $specs) {
                $rows[] = [
                    $name,
                    $this->formatSatisfies($specs),
                    $this->formatCallable($specs),
                    $this->formatClause($specs, 'args'),
                ];
            }

            $this->table([ucfirst($position).' Callbacks', 'Satisfies', 'Do', 'Args'], $rows);
        }
    }

    /**
     * Format the clauses that satisfy the callback.
     *
     * @param  array  $specs
     * @return string
     */
    protected function formatSatisfies(array $specs)
    {
        $clauses = array_map(function ($clause) use ($specs) {
            if ($result = $this->formatClause($specs, $clause)) {
                return vsprintf('%s: %s', [
                    ucfirst(str_replace('_', ' ', $clause)),
                    $result,
                ]);
            }
        }, ['from', 'excluded_from', 'on', 'excluded_on', 'to', 'excluded_to']);

        return implode(PHP_EOL, array_filter($clauses));
    }

    /**
     * Format the callback clause.
     *
     * @param  array  $specs
     * @param  string  $clause
     * @return string
     */
    protected function formatClause(array $specs, $clause)
    {
        if (isset($specs[$clause])) {
            return implode(', ', (array) $specs[$clause]);
        }

        return '';
    }

    /**
     * Format the callable callable.
     *
     * @param  array  $specs
     * @return string
     */
    protected function formatCallable(array $specs)
    {
        if (isset($specs['can'])) {
            $callback = json_encode($specs['can']);

            return "Gate::check({$callback})";
        }

        if (! isset($specs['do'])) {
            return '';
        }

        $callback = $specs['do'];

        if ($callback instanceof \Closure) {
            return 'Closure';
        }

        if (is_string($callback)) {
            if (strpos($callback, '@') !== false) {
                $callback = explode('@', $callback);
            } else {
                return $callback.'()';
            }
        }

        if (is_array($callback)) {
            return implode('::', $callback).'()';
        }

        return $callback;
    }
}
