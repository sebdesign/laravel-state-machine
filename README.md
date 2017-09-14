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

Callbacks are used to guard transitions or execute some code before or after applying transitions. This package adds the ability to use Laravel's service container to resolve callbacks.

E.g.:

You want to call the `handle` method on the `MyService` class to determine if the state machine can apply the `submit_changes` transition.

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
            'args' => ['object'],
        ],
    ],
],
```

You can specify callbacks in array format, e.g. `['Class', 'method']`, or in *@* delimited string format, e.g. `Class@method`.

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

### Statable trait

The `Statable` trait provides drop-in functionality to manage state and state history of an existing entity. The entity can be either an Eloquent Model or any other object.

#### Perquisites
* Entity class with some property holding state (we use `last_state` in the example)
* State history Eloquent Model with migrations [*](#migration)

#### Setup
For this manual we will use a `Post` model as example.

First you configure the SM graph. Open `config/state-machine.php` and define a new graph:
```php
return [
    'post' => [
        'class' => App\Post::class,
        'graph' => 'post',

        'property_path': 'last_state',

        'states' => [
            'draft',
            'published',
            'archived'
        ],
        'transitions' => [
            'publish' => [
                'from' => ['draft'],
                'to' => 'published'
            ],
            'unpublish' => [
                'from' => ['published'],
                'to' => 'draft'
            ],
            'archive' => [
                'from' => ['published'],
                'to' => 'archived'
            ],
            'unarchive' => [
                'from' => ['archived'],
                'to' => 'published'
            ]
        ],
        'callbacks' => [
            'history' => [
                'do' => 'SM\Services\StateHistoryManager@storeHistory'
            ]
        ]
    ]
]

```

Now you have to edit the `Post` model:
```php
namespace App;

use \Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use Statable;

    const HISTORY_MODEL = [
        'name' => 'App\PostState' // the related model to store the history
    ];
    const SM_CONFIG = 'post'; // the SM graph to use
}
```

And that's it!

NOTE: If you want to use `Statable` on non-eloquent entity, the setup would look like this:
```php
namespace App;

use SM\Traits\Statable;

class SomeEntity
{
    use Statable;

    const HISTORY_MODEL = [
        'name' => 'App\SomeEntityState' // the related model to store the history
        'foreign_key' => 'entity_id' // field name identifying your entity in the history table
    ];
    const SM_CONFIG = 'entity'; // the SM graph to use

    const PRIMARY_KEY = 'id'; // unique ID property of your entity
}
```

#### Usage
You can now access the following methods on your entity:
```php
$post = \App\Post::first();

$post->stateIs(); // returns current state

try {
    $post->transition('publish'); // applies transition
} catch (\SM\SMException $e) {
    abort(500, $e->getMessage()); // if transition is not allowed, throws exception
}

$post->transitionAllowed('publish'); // return boolean

$post->history()->get(); // returns PostState collection for the given Post

$post->history()->where('user_id', \Auth::id())->get(); // you can query history as any Eloquent relation
```

NOTE: The history saves the currently autheticated user, when applying a transition. This makes sense in most cases, but you can define the `user_id` field `nullable` on the history table if you are not sure state transitions are always intiated by an authenticated user.

#### <a name="migration">*</a> If you have trouble with the history Model

You need to create an Eloquent Model to hold `Post` state history. Use the following command:
```bash
$ php artisan make:model PostState -m
```
This will create a Model class in `app/` and a migration in `database/migrations`. Open the migration and edit the schema:
```php
// database/migrations/yyyy_mm_dd_hhmmss_create_post_states_table.php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderStatesTable extends Migration
{
    public function up()
    {
        Schema::create('post_states', function (Blueprint $table) {
            $table->increments('id');
            $table->string('post_id');
            $table->string('transition');
            $table->string('to');
            $table->integer('user_id'); // optionally ->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('post_states');
    }
}
```
Then open the model and add the relations:
```php
// app/OrderState.php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostState extends Model
{
    protected $guarded = [];

    public function post() {
        return $this->belongsTo('App\Post');
    }
    public function user() {
        return $this->belongsTo('App\User');
    }
}
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
