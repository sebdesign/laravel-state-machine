# Winzou State Machine service provider for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sebdesign/laravel-state-machine.svg?style=flat-square)](https://packagist.org/packages/sebdesign/laravel-state-machine)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/sebdesign/laravel-state-machine/master.svg?style=flat-square)](https://travis-ci.org/sebdesign/laravel-state-machine)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/sebdesign/laravel-state-machine/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/sebdesign/laravel-state-machine/?branch=master)
[![StyleCI](https://styleci.io/repos/78893356/shield?style=flat-square)](https://styleci.io/repos/78893356)

This is a Laravel service provider for [winzou/state-machine](https://github.com/winzou/state-machine). It provides dependency injection for the `StateMachineFactory`. You can also use Laravel's service container to resolve class methods for the callbacks. A facade is also available for convenience.

## Installation

You can install the package via composer. This package requires Laravel 5.1 or higher.

``` bash
composer require sebdesign/laravel-state-machine
```

Add the service provider and the facade in `config/app.php`.

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

Callbacks are used to guard transitions or execute some code before or after applying transitions. This package adds the ability to use Laravel's service container to resolve callbacks.

E.g.:

You want to call the `handle` method on the `MyService` class to determine if the state machine can apply the `submit_changes` transition.

```php
'callbacks' => [
    // will be called when testing a transition
    'guard' => [
        'guard_on_submitting' => [
            // call the callback on a specific transition
            'on' => 'submit_changes',

            // will call the method of this class
            'do' => ['MyService', 'handle'],

            // arguments for the callback
            'args' => ['object'],
        ],
    ],
],
```

You can specify callbacks in array format, e.g. `['Class', 'method']`, or in *@* delimited string format, e.g. `Class@method`.

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
