<?php

namespace Sebdesign\SM\Traits;

use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachine;
use Illuminate\Database\Eloquent\Model;

trait Statable
{
    /**
     * @var StateMachine
     */
    protected $stateMachine;

    public function history()
    {
        if ($this->isEloquent()) {
            return $this->hasMany(self::HISTORY_MODEL_NAME);
        }

        /** @var Model $model */
        $model = app(self::HISTORY_MODEL_NAME);

        return $model->where(self::HISTORY_MODEL_FOREIGN_KEY, $this->{self::PRIMARY_KEY});
    }

    public function addHistoryLine(array $transitionData)
    {
        $transitionData['user_id'] = auth()->id();

        if ($this->isEloquent()) {
            $this->save();

            return $this->history()->create($transitionData);
        }

        $transitionData[self::HISTORY_MODEL_FOREIGN_KEY] = $this->{self::PRIMARY_KEY};
        /** @var Model $model */
        $model = app(self::HISTORY_MODEL_NAME);

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
        if (! $this->stateMachine) {
            $this->stateMachine = app(FactoryInterface::class)->get($this, self::SM_CONFIG);
        }

        return $this->stateMachine;
    }

    public function isEloquent()
    {
        return $this instanceof Model;
    }
}
