# Winzou State Machine service provider for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sebdesign/laravel-state-machine.svg?style=flat-square)](https://packagist.org/packages/sebdesign/laravel-state-machine)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/sebdesign/laravel-state-machine/master.svg?style=flat-square)](https://travis-ci.org/sebdesign/laravel-state-machine)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/sebdesign/laravel-state-machine/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/sebdesign/laravel-state-machine/?branch=master)
[![StyleCI](https://styleci.io/repos/78893356/shield?style=flat-square)](https://styleci.io/repos/78893356)

This is a Laravel service provider for [winzou/state-machine](https://github.com/winzou/state-machine).

## Features

- It provides dependency injection for the `StateMachineFactory`.

- You can use Laravel's service container to resolve class methods for the callbacks, along with their dependencies.

- It allows you to listen for transition events using Laravel's event dispatcher.

- A facade is also available for convenience.

## Installation

You can install the package via composer. This package requires Laravel 5.1 or higher.

``` bash
composer require sebdesign/laravel-state-machine
```

Laravel 5.5 uses package auto-discovery, so doesn't require you to manually add the ServiceProvider and the facade. If you don't use auto-discovery or you are using an older version, add the service provider and the facade in config/app.php.

``` php
<?php

'providers' => [
    Sebdesign\SM\ServiceProvider::class,
],

'aliases' => [
    'StateMachine' => Sebdesign\SM\Facade::class,
],
```

## Configuration

Publish the config file in `config/state-machine.php`.

``` bash
php artisan vendor:publish --provider="Sebdesign\SM\ServiceProvider"
```

Please see the documentation of the [StateMachineBundle](https://github.com/winzou/StateMachineBundle) for all the available options.

## Usage

``` php
<?php

// Get the article
$article = App\Article::find($id);

// Get the state machine for this article, and graph called "simple"

// Using the facade
$stateMachine = StateMachine::get($article, 'simple');

// Or using the service container with dependency injection
public function method(SM\Factory\FactoryInterface $factory)
{
    $stateMachine = $factory->get($article, 'simple');
}
```

Now you can use the `$stateMachine` to interact with the state of the `$article`.

``` php
<?php

// Get the actual state of the object
$stateMachine->getState();

// Get all available transitions
$stateMachine->getPossibleTransitions();

// Check if a transition can be applied: returns true or false
$stateMachine->can('approve');

// Apply a transition
$stateMachine->apply('publish');
```

### Callbacks

Callbacks are used to guard transitions or execute some code before or after applying transitions.

This package adds the ability to resolve callbacks and inject their dependencies from Laravel's Service Container.

#### Defining a callback

Under the `callbacks.guard` array of your configuration, add an associative array to define the callback. The key of the array can be anything you want (e.g. `guard_on_submitting`). This array must have a clause, a callback, and may have some arguments.

##### Clause

First, you need to specify a clause that will determine when the callback will be invoked. A clause has a key (`from`, `to`, `on`, `excluded_from`, `excluded_to`, `excluded_on`) and its value is the state or the transition that should satisfy the clause. 

E.g. `'on' => 'submit_changes'` will be triggered when the transition `submit changes` is being checked or applied.

##### Callback

Second, you need to specify the callback that will be invoked under the `do` key. The callback must be a `callable`, and can be one of the following:

###### A closure:
```php
'do' => function () {
    // 
},
```
###### A built-in or user-defined function as a string:
```php
`do` => 'abort',
```
###### A class method as array:
Classes are resolved through the service container with their dependencies.
```php
'do' => ['MyService', 'handle'],
```
###### A class method as string:
```php
`do` => 'MyService@handle',
```

##### Arguments

###### Using dependency injection

By default, if you **don't** specify the `args` key in your array, all the callback method parameters will be injected automatically using Laravel's service container, like Route/Controller methods.

**Type-hints**

All type-hinted parameters are resolved from the container, e.g. `App $app`.

**Object in the state machine**

If a parameter has the same type-hint as the object that is associated to the state machine, e.g. `App\Article $article`, the instance will be injected.

**State machine event**

If a parameter has the state machine event type-hint, e.g. `SM\Event\TransitionEvent $e`, the event that was triggered in the state machine will be injected. Alternatively you can define a parameter named `$event` without a type-hint.

###### Using ExpressionLanguage notation

Otherwise, you can define one or multiple arguments that will be passed explicitly to your callbacks in the given order, by using the `args` key. This package is using Symfony's [ExpressionLanguage](https://symfony.com/doc/current/components/expression_language.html) notation to evaluate expressions.

Here are some examples:
```php
<?php

// The callback won't be passed any argument
'args' => [],

// You can pass strings and arrays as JSON-like strings.
'args' => ['"approved"', '["foo", "bar"]', '{"foo": "bar"}'],

// The callback will receive the object that is associated with the state machine,
// e.g. the `$article`.
'args' => ['object'],

// The callback will receive the `SM\Event\TransitionEvent` instance.
'args' => ['event'],
```

#### Example

You want to call the `handle` method on the `MyService` class to determine if the state machine can apply the `submit_changes` transition. The handle method will receive the object of the state machine as the first argument, and the transition event as the second argument.

```php
<?php

'callbacks' => [
    // will be called when testing a transition
    'guard' => [
        'guard_on_submitting' => [
            // call the callback on a specific transition
            'on' => 'submit_changes',

            // will call the method of this class
            'do' => ['MyService', 'handle'],

            // arguments for the callback
            'args' => ['object', 'event'],
        ],
    ],
],
```

### Events

When checking if a transition can be applied, the `SM\Event\SMEvents::TEST_TRANSITION` event is fired.

Before and after a transition is being applied, the `SM\Event\SMEvents::PRE_TRANSITION` and `SM\Event\SMEvents::POST_TRANSITION` events are fired respectively.

All the events receive an `SM\Event\TransitionEvent` instance.

If you wish to listen to all the events with the same listener, you can use the `winzou.state_machine.*` wildcard parameter.

You can define your own listeners in your app's `EventServiceProvider`. E.g.:

```php
<?php

use SM\Event\SMEvents;

/**
 * The event listener mappings for the application.
 *
 * @var array
 */
protected $listen = [
    SMEvents::TEST_TRANSITION => [
        \App\Listeners\CheckTransition::class,
    ],
    SMEvents::PRE_TRANSITION => [
        \App\Listeners\BeforeTransition::class,
    ],
    SMEvents::POST_TRANSITION => [
        \App\Listeners\AfterTransition::class,
    ],
    'winzou.state_machine.*' => [
        \App\Listeners\Transition::class,
    ],
];
```

### Debug command

An artisan command for debugging graphs is included. It accepts the name of the graph as an argument. If no arguments are passed, the graph name will be asked interactively.

```bash
$ php artisan winzou:state-machine:debug simple

+--------------------+
| Configured States: |
+--------------------+
| new                |
| pending_review     |
| awaiting_changes   |
| accepted           |
| published          |
| rejected           |
+--------------------+
+-----------------+------------------+------------------+
| Transition      | From(s)          | To               |
+-----------------+------------------+------------------+
| create          | new              | pending_review   |
+-----------------+------------------+------------------+
| ask_for_changes | pending_review   | awaiting_changes |
|                 | accepted         |                  |
+-----------------+------------------+------------------+
| cancel_changes  | awaiting_changes | pending_review   |
+-----------------+------------------+------------------+
| submit_changes  | awaiting_changes | pending_review   |
+-----------------+------------------+------------------+
| approve         | pending_review   | accepted         |
|                 | rejected         |                  |
+-----------------+------------------+------------------+
| publish         | accepted         | published        |
+-----------------+------------------+------------------+
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email info@sebdesign.eu instead of using the issue tracker.

## Credits

- [Alexandre Bacco](https://github.com/winzou)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
