<?php

namespace Sebdesign\SM\Event;

use SM\Event\TransitionEvent as BaseTransitionEvent;

class TransitionEvent extends BaseTransitionEvent
{
    /**
     * @var array
     */
    protected $context = [];

    public function setContext(array $context): void
    {
        $this->context = $context;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
