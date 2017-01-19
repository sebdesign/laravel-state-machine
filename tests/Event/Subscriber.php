<?php

namespace Sebdesign\SM\Test\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Subscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'foo' => 'fooListener',
            'bar' => ['barListener', 1],
            'baz' => [['bazListener', 2]],
        ];
    }

    public function fooListener()
    {
    }

    public function barListener()
    {
    }

    public function bazListener()
    {
    }
}
