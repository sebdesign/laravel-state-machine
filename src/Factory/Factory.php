<?php

namespace Sebdesign\SM\Factory;

use Sebdesign\SM\Metadata\MetadataStoreInterface;
use Sebdesign\SM\StateMachine\StateMachine;
use SM\Callback\CallbackFactoryInterface;
use SM\Factory\Factory as BaseFactory;
use SM\SMException;
use SM\StateMachine\StateMachineInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Factory extends BaseFactory
{
    /**
     * @var \Sebdesign\SM\Metadata\MetadataStoreInterface|null
     */
    protected $metadataStore;

    public function __construct(
        array $configs,
        EventDispatcherInterface $dispatcher = null,
        CallbackFactoryInterface $callbackFactory = null,
        MetadataStoreInterface $metadataStore = null
    ) {
        parent::__construct($configs, $dispatcher, $callbackFactory);

        $this->metadataStore = $metadataStore;
    }

    /**
     * {@inheritdoc}
     */
    protected function createStateMachine($object, array $config): StateMachineInterface
    {
        if (! isset($config['state_machine_class'])) {
            $class = StateMachine::class;
        } elseif (class_exists($config['state_machine_class'])) {
            $class = $config['state_machine_class'];
        } else {
            throw new SMException(sprintf(
                'Class "%s" for creating a new state machine does not exist.',
                $config['state_machine_class']
            ));
        }

        return new $class($object, $config, $this->dispatcher, $this->callbackFactory, $this->metadataStore);
    }

    /**
     * {@inheritdoc}
     */
    public function addConfig(array $config, string $graph = 'default'): void
    {
        $config['states'] = $this->normalizeStates($config);

        parent::addConfig($config, $graph);
    }

    /**
     * Normalize the states as associative arrays.
     *
     * - The state is null.
     *   E.g. ['states' => [null]]
     *
     * - The state is defined as a string.
     *   E.g. ['states' => ['stateA']]
     *
     * - The state is defined as an associative array.
     *   E.g. ['states' => [['name' => 'stateA']]]
     */
    protected function normalizeStates(array $config): array
    {
        $states = [];

        foreach ($config['states'] as $state) {
            if (is_null($state) || is_scalar($state)) {
                $state = ['name' => $state];
            }

            $states[] = $state;
        }

        return $states;
    }
}
