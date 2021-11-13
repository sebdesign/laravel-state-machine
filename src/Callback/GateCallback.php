<?php

namespace Sebdesign\SM\Callback;

use Illuminate\Contracts\Auth\Access\Gate;
use SM\Callback\Callback;

class GateCallback extends Callback
{
    /**
     * @var \Illuminate\Contracts\Auth\Access\Gate
     */
    protected $gate;

    /**
     * @param  array  $specs  Specification for the callback to be called
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate  The service container that will be used to resolve the callable
     */
    public function __construct(array $specs, Gate $gate)
    {
        if (! isset($specs['args'])) {
            $specs['args'] = ['object'];
        }

        parent::__construct($specs, [$this, 'check']);

        $this->gate = $gate;
    }

    /**
     * Check if the abilities are allowed against the gate.
     */
    public function check(): bool
    {
        return $this->gate->check($this->specs['can'], func_get_args());
    }
}
