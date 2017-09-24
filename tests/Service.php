<?php

namespace Sebdesign\SM\Test;

use Illuminate\Contracts\Foundation\Application;
use SM\Event\TransitionEvent;

class Service
{
    public function guardOnSubmitting(Article $article)
    {
    }

    public function guardOnApproving(
        Article $article,
        TransitionEvent $event,
        Application $app,
        $default = true
    ) {
    }
}
