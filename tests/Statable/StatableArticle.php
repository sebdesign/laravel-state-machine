<?php

namespace Sebdesign\SM\Test\Statable;

use Illuminate\Database\Eloquent\Model;
use Sebdesign\SM\Traits\Statable;

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
