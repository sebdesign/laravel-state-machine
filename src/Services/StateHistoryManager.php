<?php

namespace Sebdesign\SM\Services;

use SM\Event\TransitionEvent;

class StateHistoryManager
{
    /**
     * @param \SM\Event\TransitionEvent $event
     */
    public function storeHistory(TransitionEvent $event)
    {
        $sm = $event->getStateMachine();
        $model = $sm->getObject();

        $model->addHistoryLine([
            'transition' => $event->getTransition(),
            'from' => $event->getState(),
            'to' => $sm->getState(),
        ]);
    }
}
