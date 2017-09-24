<?php

namespace Sebdesign\SM\Callback;

use SM\SMException;
use SM\Callback\CallbackFactory;
use Illuminate\Contracts\Container\Container;

class ContainerAwareCallbackFactory extends CallbackFactory
{
    /**
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * @param string                                    $class     The Callback class
     * @param \Illuminate\Contracts\Container\Container $container The service container that will be used to resolve the callable
     *
     * @throws \SM\SMException if the Callback class does not exist
     */
    public function __construct($class, Container $container)
    {
        parent::__construct($class);

        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function get(array $specs)
    {
        if (! isset($specs['do'])) {
            throw new SMException(sprintf(
               'CallbackFactory::get needs the index "do" to be able to build a callback, array %s given.',
                json_encode($specs)
            ));
        }

        $class = $this->class;

        return new $class($specs, $specs['do'], $this->container);
    }
}
