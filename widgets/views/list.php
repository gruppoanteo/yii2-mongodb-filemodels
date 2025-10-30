<?php

use anteo\fontawesome\FA;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
?>

<?= ListView::widget([
    'id' => $gridId,
    'dataProvider' => $dataProvider,
    'itemOptions' => [
        'class' => 'col-md-3'
    ],
    'options' => [
        'class' => 'row'
    ],
    'itemView' => function ($model, $key, $index, $this) {
        $html[] = Html::beginTag('div', ['class' => 'text-center']);
        $html[] = Html::beginTag('a', ['href' => Url::to(["/{$this->context->moduleId}/default/view", 'hash' => $model->hash_key])]);
        $html[] = FA::icon($model->icon)->size(FA::SIZE_4X);
        $html[] = Html::tag('div', $model->filename);
        $html[] = Html::endTag('a');
        $html[] = Html::tag('small', Yii::$app->formatter->asShortSize($model->size));
        $html[] = Html::tag('small', $model->fullUploader, ['class' => 'text-muted']);
        if ($this->description) {
            $html[] = Html::tag('p', $model->description, ['class' => 'text-muted']);
        }
        $html[] = Html::endTag('div');
        $html[] = '<hr/>';
        return implode("\n", $html);
    }, 
   
]) ?>