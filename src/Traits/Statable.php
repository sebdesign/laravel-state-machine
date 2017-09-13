<?php

namespace SM\Traits;

use Illuminate\Database\Eloquent\Model;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachine;

trait Statable
{
    /**
     * @var StateMachine $stateMachine
     */
    protected $stateMachine;

    public function history()
    {
        if ($this->isEloquent()) {
            return $this->hasMany(self::HISTORY_MODEL['name']);
        }

        /** @var \Eloquent $model */
        $model = app(self::HISTORY_MODEL['name']);

        return $model->where(self::HISTORY_MODEL['foreign_key'], $this->{self::PRIMARY_KEY});
    }

    public function addHistoryLine(array $transitionData)
    {
        $transitionData['user_id'] = auth()->id();

        if ($this->isEloquent()) {
            $this->save();
            return $this->history()->create($transitionData);
        }

        $transitionData[self::HISTORY_MODEL['foreign_key']] = $this->{self::PRIMARY_KEY};
        /** @var \Eloquent $model */
        $model = app(self::HISTORY_MODEL['name']);

        return $model->create($transitionData);
    }

    public function stateIs()
    {
        return $this->StateMachine()->getState();
    }

    public function transition($transition)
    {
        return $this->stateMachine()->apply($transition);
    }

    public function transitionAllowed($transition)
    {
        return $this->StateMachine()->can($transition);
    }

    /**
     * @return StateMachine
     */
    public function stateMachine()
    {
        if (!$this->stateMachine) {
            $this->stateMachine = app(FactoryInterface::class)->get($this, self::SM_CONFIG);
        }
        return $this->stateMachine;
    }

    public function isEloquent()
    {
        return $this instanceof Model;
    }
}
