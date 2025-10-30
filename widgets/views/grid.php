<?php

use anteo\mongodb\filemodels\Module;
use anteo\fontawesome\FA;
use yii\bootstrap\Html;
use yii\grid\GridView;
?>

<?php if ($searchModel !== null): ?>
    <?= $this->render('_search', ['model' => $searchModel]) ?>
<?php endif; ?>

<?= GridView::widget([
    'id' => $gridId,
    'dataProvider' => $dataProvider,
    'tableOptions' => ['class' => 'table table-condensed table-striped table-hover'],
    'showOnEmpty' => false,
    'emptyText' => Module::t('filemodels', 'No files uploaded.'),
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'visible' => $this->context->showSerialColumn
        ],
        [
            'attribute' => 'filename',
            'format' => 'raw',
            'value' => function($model, $key) {
                return FA::icon($model->icon)->fixedWidth() . " " . $model->filename;
            }
        ],
        [
            'attribute' => 'description',
            'visible' => $this->context->enableDescription,
            'value' => function($model) {
                return $model->metadataModel->description;
            }
        ],
        'length:shortsize',
        [
            'attribute' => 'fullUploader',
            'contentOptions' => ['class' => 'text-muted']
        ],
        [
            'class' => 'yii\grid\ActionColumn', 
            'controller' => "/{$this->context->moduleId}/default",
            'template' => '{view} {download} {delete} ',
            'visibleButtons' => ['delete' => !$this->context->readOnly],
            'buttons' => [
                'view' => function ($url, $model, $key) {
                    return Html::a(Html::icon('eye-open'), $url, ['data-pjax' => 0, 'title' => Yii::t('yii', 'View'), 'target' => '_blank']);
                },
                'download' => function ($url, $model, $key) {
                    return Html::a(Html::icon('download'), $url, ['data-pjax' => 0, 'title' => 'Download']);
                },
            ]
        ]
    ]
]) ?>