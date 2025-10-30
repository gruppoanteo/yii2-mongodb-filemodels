<?php

use anteo\fontawesome\FA;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
?>

<?php $form = ActiveForm::begin([
//    'action' => Url::current([$model->formName() => null, 'tag' => $this->context->tag]),
    'layout' => 'inline',
    'method' => 'get',
    'options' => ['data-pjax' => '1']
]); ?>

    <?= $form->field($model, 'search', [
        'inputTemplate' => '<div class="input-group input-group-sm">{input}<span class="input-group-btn">' . Html::submitButton(FA::icon(FA::_SEARCH), ['class' => 'btn btn-default']) . '</span></div>',
    ])->textInput(['autocomplete' => 'off']) ?>

<?php ActiveForm::end(); ?>