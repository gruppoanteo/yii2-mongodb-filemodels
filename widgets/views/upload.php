<?php
use anteo\mongodb\filemodels\Module;
use yii\bootstrap\Progress;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
?>
<div class="text-right">
    <?= Html::a(Html::icon('upload') . " " . Module::t('filemodels', 'Upload'), '#', [
        'class' => 'btn btn-default btn-sm' . (($this->context->readOnly) ? ' disabled' : ''), 
        'onclick' => "$('#{$formId}').toggleClass('hidden'); return false;"
    ]) ?>
</div>

<?= Html::tag('div', '', ['id' => $alertId, 'class' => 'alert hidden']) ?>

<?php $form = ActiveForm::begin([
    'id' => $formId,
    'action' => ["/{$this->context->moduleId}/default/upload"],
    'fieldConfig' => $this->context->fieldConfig,
    'enableAjaxValidation' => false,
    'options' => [
        'enctype' => 'multipart/form-data',
        'class' => 'hidden',
        'data-pjax' => '0'
    ]
]); ?>

    <?= Html::activeHiddenInput($metadata, 'entity') ?>
    <?= Html::activeHiddenInput($metadata, 'entity_id') ?>

    <?php if ($this->context->uploadWidgetClass != ""): ?>
        <?= $form->field($model, 'file')->widget($this->context->uploadWidgetClass, $this->context->uploadWidgetConfig) ?>
    <?php else: ?>
        <?= $form->field($model, 'file')->fileInput() ?>
    <?php endif; ?>

    <?php if ($this->context->enableDescription): ?>
        <?= $form->field($metadata, 'description'); ?>

        <div class="form-group">
            <div class="row">
                <div class="col-md-4">
                    <?= Html::submitButton(Module::t('filemodels', 'Upload'), ['class' => 'btn btn-success']) ?>
                </div>
                <div class="col-md-8">
                    <?= Progress::widget([
                        'barOptions' => ['class' => 'progress-bar-success']
                    ]) ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?= Progress::widget([
            'barOptions' => ['class' => 'progress-bar-success']
        ]) ?>
        <?php 
        $this->registerJs("
        $('#$formId :file').on('change', function() {
            $('#$formId').submit();
        });
        "); ?>
    <?php endif; ?>
<?php ActiveForm::end(); ?>