<?php

namespace Sebdesign\SM\Models;

use Illuminate\Database\Eloquent\Model;

class StateHistory extends Model
{
    protected $table = 'state_history';

    protected $guarded = [];

    public function statable()
    {
        return $this->morphTo();
    }
}
