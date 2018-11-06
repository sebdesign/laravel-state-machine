<?php

return [
    'graphA' => [
        // class of your domain object
        'class' => App\User::class,

        // name of the graph (default is "default")
        'graph' => 'graphA',

        // property of your object holding the actual state (default is "state")
        'property_path' => 'state',

        'metadata' => [
            'title' => 'Graph A',
        ],

        // list of all possible states
        'states' => [
            // a state as associative array
            ['name' => 'new'],
            // a state as associative array with metadata
            [
                'name' => 'pending_review',
                'metadata' => ['title' => 'Pending Review'],
            ],
            // states as string
            'awaiting_changes',
            'accepted',
            'published',
            'rejected',
        ],

        // list of all possible transitions
        'transitions' => [
            'create' => [
                'from' => ['new'],
                'to' => 'pending_review',
            ],
            'ask_for_changes' => [
                'from' =>  ['pending_review', 'accepted'],
                'to' => 'awaiting_changes',
                'metadata' => ['title' => 'Ask for changes'],
            ],
            'cancel_changes' => [
                'from' => ['awaiting_changes'],
                'to' => 'pending_review',
            ],
            'submit_changes' => [
                'from' => ['awaiting_changes'],
                'to' =>  'pending_review',
            ],
            'approve' => [
                'from' => ['pending_review', 'rejected'],
                'to' =>  'accepted',
            ],
            'publish' => [
                'from' => ['accepted'],
                'to' =>  'published',
            ],
        ],

        // list of all callbacks
        'callbacks' => [
            // will be called when testing a transition
            'guard' => [
                'guard_on_submitting' => [
                    // call the callback on a specific transition
                    'on' => 'submit_changes',
                    // will call the method of this class
                    'do' => ['MyClass', 'handle'],
                    // arguments for the callback
                    'args' => ['object'],
                ],
                'guard_on_approving' => [
                    // call the callback on a specific transition
                    'on' => 'approve',
                    // will check the ability on the gate or the class policy
                    'can' => 'approve',
                ],
            ],

            // will be called before applying a transition
            'before' => [],

            // will be called after applying a transition
            'after' => [],
        ],
    ],
];
