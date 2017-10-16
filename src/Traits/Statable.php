<?php

namespace Sebdesign\SM\Traits;

use Sebdesign\SM\Models\StateHistory;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachine;

/**
 * Trait Statable
 *
 * @package Sebdesign\SM\Traits
 */
trait Statable
{
    /**
     * @var StateMachine
     */
    protected $stateMachine;

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function history()
    {
        return $this->morphMany(StateHistory::class, 'statable');
    }

    /**
     * @param array $transitionData
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function addHistoryLine(array $transitionData)
    {
        $transitionData['actor_id'] = auth()->id();

        return $this->history()->create($transitionData);
    }

    /**
     * @return mixed|string
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    public function stateIs()
    {
        return $this->StateMachine()->getState();
    }

    /**
     * @param $transition
     * @return bool
     * @throws \SM\SMException|\Illuminate\Container\EntryNotFoundException
     */
    public function transition($transition)
    {
        return $this->stateMachine()->apply($transition);
    }

    /**
     * @param $transition
     * @return bool
     * @throws \SM\SMException|\Illuminate\Container\EntryNotFoundException
     */
    public function transitionAllowed($transition)
    {
        return $this->StateMachine()->can($transition);
    }

    /**
     * @return mixed|\SM\StateMachine\StateMachine
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    public function stateMachine()
    {
        if (! $this->stateMachine) {
            $this->stateMachine = app(FactoryInterface::class)->get($this, $this->SMConfig);
        }

        return $this->stateMachine;
    }
}
