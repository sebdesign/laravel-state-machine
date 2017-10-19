<?php

namespace Sebdesign\SM\Test\Statable;

use Sebdesign\SM\Traits\Statable;
use Illuminate\Database\Eloquent\Model;

class StatableArticle extends Model
{
    use Statable;

    protected $table = 'articles';

    protected $guarded = [];

    /**
     * @return string
     */
    protected function getGraph()
    {
        return 'graphA';
    }
}
