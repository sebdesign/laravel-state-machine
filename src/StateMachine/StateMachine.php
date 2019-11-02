<?php

namespace Sebdesign\SM\StateMachine;

use SM\SMException;
use Sebdesign\SM\Metadata\MetadataStore;
use SM\Callback\CallbackFactoryInterface;
use Sebdesign\SM\Metadata\MetadataStoreInterface;
use SM\StateMachine\StateMachine as BaseStateMachine;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class StateMachine extends BaseStateMachine
{
    /**
     * @var \Sebdesign\SM\Metadata\MetadataStoreInterface
     */
    protected $metadataStore;

    /**
     * {@inheritdoc}
     *
     * @param \Sebdesign\SM\Metadata\MetadataStoreInterface|null $metadataStore
     */
    public function __construct(
        $object,
        array $config,
        EventDispatcherInterface $dispatcher = null,
        CallbackFactoryInterface $callbackFactory = null,
        MetadataStoreInterface   $metadataStore = null
    ) {
        parent::__construct($object, $config, $dispatcher, $callbackFactory);

        $this->metadataStore = $metadataStore ?: new MetadataStore($config);
    }

    /**
     * Set a new state to the underlying object.
     *
     * @param string $state
     *
     * @throws \SM\SMException
     */
    protected function setState($state)
    {
        if (! $this->hasState($state)) {
            throw new SMException(sprintf(
                'Cannot set the state to "%s" to object "%s" with graph %s because it is not pre-defined.',
                $state,
                get_class($this->object),
                $this->config['graph']
            ));
        }

        $accessor = new PropertyAccessor();
        $accessor->setValue($this->object, $this->config['property_path'], $state);
    }

    /**
     * Check if the graph has the given state.
     *
     * @param  string $state
     * @return bool
     */
    protected function hasState($state)
    {
        foreach ($this->config['states'] as $value) {
            if ($value['name'] === $state) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the metadata.
     *
     * @return \Sebdesign\SM\Metadata\MetadataStoreInterface
     */
    public function metadata($type = null, $subject = null, $key = null, $default = null)
    {
        if (is_null($type)) {
            return $this->metadataStore;
        }

        switch ($type) {
            case 'graph':
                return $this->getGraphMetadata($subject, $key);
            case 'state':
                return $this->getStateMetadata($subject, $key, $default);
            case 'transition':
                return $this->getTransitionMetadata($subject, $key, $default);
            default:
                return $this->getGraphMetadata($type, $subject);
        }
    }

    protected function getGraphMetadata($key, $default)
    {
        return $this->metadataStore->graph($key, $default);
    }

    protected function getStateMetadata($subject, $key, $default)
    {
        if ($this->hasState($subject)) {
            return $this->metadataStore->state($subject, $key, $default);
        }

        if (is_null($subject)) {
            return $this->metadataStore->state($this->getState(), $key, $default);
        }

        return $this->metadataStore->state($this->getState(), $subject, $key);
    }

    protected function getTransitionMetadata($subject, $key, $default)
    {
        return $this->metadataStore->transition($subject, $key, $default);
    }
}
