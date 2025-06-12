<?php

namespace carono\yii2widgets\src;

use Closure;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

class ActionColumn extends \yii\grid\ActionColumn
{
    public $layout = '{buttons}';
    public $buttonLayout = '<a href="{url}"{button-options}>{icon}</a>';

    public $buttons = [
        'update' => [
            'partials' => [
                'icon' => '<svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:1em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M498 142l-46 46c-5 5-13 5-17 0L324 77c-5-5-5-12 0-17l46-46c19-19 49-19 68 0l60 60c19 19 19 49 0 68zm-214-42L22 362 0 484c-3 16 12 30 28 28l122-22 262-262c5-5 5-13 0-17L301 100c-4-5-12-5-17 0zM124 340c-5-6-5-14 0-20l154-154c6-5 14-5 20 0s5 14 0 20L144 340c-6 5-14 5-20 0zm-36 84h48v36l-64 12-32-31 12-65h36v48z"/></svg>'
            ]
        ],
        'view' => [
            'partials' => [
                'icon' => '<svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:1.125em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M573 241C518 136 411 64 288 64S58 136 3 241a32 32 0 000 30c55 105 162 177 285 177s230-72 285-177a32 32 0 000-30zM288 400a144 144 0 11144-144 144 144 0 01-144 144zm0-240a95 95 0 00-25 4 48 48 0 01-67 67 96 96 0 1092-71z"/></svg>'
            ]
        ],
        'delete' => [
            'partials' => [
                'icon' => '<svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:.875em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M32 464a48 48 0 0048 48h288a48 48 0 0048-48V128H32zm272-256a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zM432 32H312l-9-19a24 24 0 00-22-13H167a24 24 0 00-22 13l-9 19H16A16 16 0 000 48v32a16 16 0 0016 16h416a16 16 0 0016-16V48a16 16 0 00-16-16z"/></svg>'
            ],
            'options' => [
                'data-method' => 'post',
            ]
        ],
    ];

    public $urls = [];

    protected function initDefaultButtons()
    {
        $this->buttons['delete']['options']['data-confirm'] = Yii::t('yii', 'Are you sure you want to delete this item?');
    }

    public function buttonLabels()
    {
        return [
            'update' => Yii::t('yii', 'Update'),
            'view' => Yii::t('yii', 'View'),
            'delete' => Yii::t('yii', 'Delete'),
        ];
    }

    public function createUrl($action, $model, $key, $index)
    {
        if (isset($this->urls[$action])) {
            return Url::toRoute($this->urls[$action] instanceof Closure ? call_user_func($this->urls[$action], $model, $key, $index) : $this->urls[$action]);
        }
        return parent::createUrl($action, $model, $key, $index);
    }

    public function getButtonLabel($name)
    {
        return ArrayHelper::getValue($this->buttonLabels(), $name, $name);
    }

    public function renderButton($name, $model, $key, $index)
    {
        $url = $this->createUrl($name, $model, $key, $index);
        $buttonPartials = ArrayHelper::getValue($this->buttons, $name . '.partials', []);
        $label = $this->getButtonLabel($name);

        $defaultButtonOptions = ['title' => $label, 'aria-label' => $label, 'data-pjax' => '0',];
        $buttonOptions = array_merge($defaultButtonOptions, ArrayHelper::getValue($this->buttons, $name . '.options'), $this->buttonOptions);

        $replacePatterns = array_merge([
            'url' => $url,
            'label' => $this->getButtonLabel($name),
            'button-options' => Html::renderTagAttributes($buttonOptions)
        ], $buttonPartials);

        return preg_replace_callback('/\\{([\w\-\/]+)\\}/', function ($matches) use ($model, $key, $index, $replacePatterns) {
            $name = $matches[1];
            return ArrayHelper::getValue($replacePatterns, $name, '');
        }, $this->buttonLayout);
    }

    protected function isVisible($name, $model, $key, $index)
    {
        if (isset($this->visibleButtons[$name])) {
            return $this->visibleButtons[$name] instanceof \Closure
                ? call_user_func($this->visibleButtons[$name], $model, $key, $index)
                : $this->visibleButtons[$name];
        }
        return true;
    }

    protected function renderDataCellContent($model, $key, $index)
    {
        $buttons = preg_replace_callback('/\\{([\w\-\/]+)\\}/', function ($matches) use ($model, $key, $index) {
            $name = $matches[1];
            if ($this->isVisible($name, $model, $key, $index)) {
                if (isset($this->buttons[$name]) && $this->buttons[$name] instanceof Closure) {
                    $url = $this->createUrl($name, $model, $key, $index);
                    return call_user_func($this->buttons[$name], $url, $model, $key);
                } else {
                    return $this->renderButton($name, $model, $key, $index);
                }
            }
            return '';
        }, $this->template);

        return strtr($this->layout, ['{buttons}' => $buttons]);
    }

}