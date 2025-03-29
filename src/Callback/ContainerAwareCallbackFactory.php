<?php

/*
 * This file is part of the StateMachine package.
 *
 * (c) Alexandre Bacco
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sebdesign\SM\Callback;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Container\Container as ContainerInterface;
use SM\Callback\CallbackFactory;
use SM\Callback\CallbackInterface;
use SM\SMException;

class ContainerAwareCallbackFactory extends CallbackFactory
{
    /**
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * @param  string  $class  The CallbackFactory
     * @param  \Illuminate\Contracts\Container\Container  $container  The service container that will be used to resolve the callable
     *
     * @throws \SM\SMException if the CallbackFactory class does not exist
     */
    public function __construct($class, ContainerInterface $container)
    {
        parent::__construct($class);

        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function get(array $specs): CallbackInterface
    {
        if (isset($specs['can'])) {
            return new GateCallback($specs, $this->container->make(Gate::class));
        }

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
