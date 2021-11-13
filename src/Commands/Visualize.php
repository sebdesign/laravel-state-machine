<?php

namespace Sebdesign\SM\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Visualize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'winzou:state-machine:visualize
        {graph? : A state machine graph}
        {--output=./graph.jpg}
        {--format=jpg}
        {--direction=TB}
        {--shape=circle}
        {--dot-path=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates an image of the states and transitions of state machine graphs';

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

        $this->stateMachineInDotFormat($config);

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

    protected function stateMachineInDotFormat(array $config)
    {
        // Output image mime types.
        $mimeTypes = [
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
        ];

        $format = $this->option('format');

        if (empty($mimeTypes[$format])) {
            throw new \Exception(sprintf("Format '%s' is not supported", $format));
        }

        $dotPath = $this->option('dot-path') ?? 'dot';
        $outputImage = $this->option('output');

        $process = new Process([$dotPath, '-T', $format, '-o', $outputImage]);
        $process->setInput($this->buildDotFile($config));
        $process->run();

        // executes after the command finishes
        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

    protected function buildDotFile(array $config): string
    {
        // Display settings
        $layout = $this->option('direction') === 'TB' ? 'TB' : 'LR';
        $nodeShape = $this->option('shape');

        // Build dot file content.
        $result = [];
        $result[] = 'digraph finite_state_machine {';
        $result[] = "rankdir={$layout};";
        $result[] = 'node [shape = point]; _start_'; // Input node

        // Use first value from 'states' as start.
        if (is_array($config['states'][0])) {
            $start = $config['states'][0]['name'] ?? 'null';
        } else {
            $start = $config['states'][0] ?? 'null';
        }
        $result[] = "node [shape = {$nodeShape}];"; // Default nodes
        $result[] = "_start_ -> \"{$start}\";"; // Input node -> starting node.

        foreach ($config['transitions'] as $name => $transition) {
            foreach ($transition['from'] as $from) {
                $result[] = "\"{$from}\" -> \"{$transition['to']}\" [label = \"{$name}\"];";
            }
        }

        $result[] = '}';

        return implode(PHP_EOL, $result);
    }
}
