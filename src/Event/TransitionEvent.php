<?php

namespace Sebdesign\SM\Event;

use SM\Event\TransitionEvent as BaseTransitionEvent;

class TransitionEvent extends BaseTransitionEvent
{
    /**
     * @var array
     */
    protected $context = [];

    /**
     * @param array $context
     * @return void
     */
    public function setContext(array $context)
    {
        $this->context = $context;
    }

    /**
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }
}
