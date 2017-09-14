<?php

namespace Sebdesign\SM\Test\Statable;

use \Sebdesign\SM\Traits\Statable;

class StatableArticle
{
    use Statable;
    
    const HISTORY_MODEL = [
        'name' => 'Sebdesign\SM\Test\Statable\ArticleState',
        'foreign_key' => 'article_id'
    ];
    const SM_CONFIG = 'graphA'; // the SM graph to use

    const PRIMARY_KEY = 'id'; // unique ID property of your entity


    public $state;

    public $id;

    public function __construct($state = 'new', $id = 1)
    {
        $this->state = $state;
        $this->id = $id;
    }
}
