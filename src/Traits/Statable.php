<?php

namespace Sebdesign\SM\Traits;

use SM\StateMachine\StateMachine;
use Sebdesign\SM\Models\StateHistory;

/**
 * Trait Statable.
 */
trait Statable
{
    /**
     * @var StateMachine
     */
    protected $SM;

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function stateHistory()
    {
        return $this->morphMany(StateHistory::class, 'statable');
    }

    /**
     * @param array $transitionData
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function addHistoryLine(array $transitionData)
    {
        $this->save();

        $transitionData['actor_id'] = $this->getActorId();

        return $this->stateHistory()->create($transitionData);
    }

    /**
     * @return int|null
     */
    public function getActorId()
    {
        return auth()->id();
    }

    /**
     * @return mixed|string
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    public function stateIs()
    {
        return $this->stateMachine()->getState();
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
        return $this->stateMachine()->can($transition);
    }

    /**
     * @return mixed|\SM\StateMachine\StateMachine
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    public function stateMachine()
    {
        if (! $this->SM) {
            $this->SM = app('sm.factory')->get($this, $this->getGraph());
        }

        return $this->SM;
    }

    /**
     * @return string
     */
    protected function getGraph()
    {
        return 'default';
    }
}
