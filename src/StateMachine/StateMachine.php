<?php

namespace Sebdesign\SM\StateMachine;

use Sebdesign\SM\Event\TransitionEvent;
use Sebdesign\SM\Metadata\MetadataStore;
use Sebdesign\SM\Metadata\MetadataStoreInterface;
use SM\Callback\CallbackFactoryInterface;
use SM\Event\SMEvents;
use SM\SMException;
use SM\StateMachine\StateMachine as BaseStateMachine;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;

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
        MetadataStoreInterface $metadataStore = null
    ) {
        parent::__construct($object, $config, $dispatcher, $callbackFactory);

        $this->metadataStore = $metadataStore ?? new MetadataStore($config);
    }

    /**
     * {@inheritdoc}
     */
    public function can($transition, array $context = [])
    {
        if (! isset($this->config['transitions'][$transition])) {
            throw new SMException(sprintf(
                'Transition "%s" does not exist on object "%s" with graph "%s".',
                $transition,
                get_class($this->object),
                $this->config['graph']
            ));
        }

        if (! in_array($this->getState(), $this->config['transitions'][$transition]['from'])) {
            return false;
        }

        $event = new TransitionEvent($transition, $this->getState(), $this->config['transitions'][$transition], $this);

        $event->setContext($context);

        if (isset($this->dispatcher)) {
            $this->dispatcher->dispatch(SMEvents::TEST_TRANSITION, $event);

            if ($event->isRejected()) {
                return false;
            }
        }

        return $this->callCallbacks($event, 'guard');
    }

    /**
     * {@inheritdoc}
     */
    public function apply($transition, $soft = false, array $context = [])
    {
        if (! $this->can($transition, $context)) {
            if ($soft) {
                return false;
            }

            throw new SMException(sprintf(
                'Transition "%s" cannot be applied on state "%s" of object "%s" with graph "%s".',
                $transition,
                $this->getState(),
                get_class($this->object),
                $this->config['graph']
            ));
        }

        $event = new TransitionEvent($transition, $this->getState(), $this->config['transitions'][$transition], $this);

        $event->setContext($context);

        if (isset($this->dispatcher)) {
            $this->dispatcher->dispatch(SMEvents::PRE_TRANSITION, $event);

            if ($event->isRejected()) {
                return false;
            }
        }

        $this->callCallbacks($event, 'before');

        $this->setState($this->config['transitions'][$transition]['to']);

        $this->callCallbacks($event, 'after');

        if (isset($this->dispatcher)) {
            $this->dispatcher->dispatch(SMEvents::POST_TRANSITION, $event);
        }

        return true;
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
                'Cannot set the state to "%s" to object "%s" with graph "%s" because it is not pre-defined.',
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
            if ($value['name'] == $state) {
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
