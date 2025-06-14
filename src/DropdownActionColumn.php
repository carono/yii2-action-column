<?php

namespace carono\yii2widgets;

use Closure;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

class DropdownActionColumn extends ActionColumn
{
    public $layout = <<<HTML
<div>
    <button type="button" class="btn btn-secondary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
            <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
        </svg>
    </button>
    <div class="dropdown-menu">
      {buttons}
    </div>
  </div>
HTML;

    public $buttonLayout = '<a class="dropdown-item" href="{url}"{button-options}>{icon} {label}</a>';
}