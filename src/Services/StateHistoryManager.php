<?php

namespace SM\Services;

use SM\Event\TransitionEvent;

class StateHistroyManager
{
    public function postTransition(TransitionEvent $event)
    {
        $sm = $event->getStateMachine();
        $model = $sm->getObject();

        $model->history()->create([
            "transition" => $event->getTransition(),
            "to" => $sm->getState(),
            "user_id" => auth()->id()
        ]);

        $model->save();
    }
}
