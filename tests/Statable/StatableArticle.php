<?php

namespace Sebdesign\SM\Test\Statable;

use Illuminate\Database\Eloquent\Model;
use Sebdesign\SM\Traits\Statable;

class StatableArticle extends Model
{
    use Statable;

    protected $table = 'articles';

    protected $guarded = [];

    protected $SMConfig = 'graphA'; // the SM graph to use

}
