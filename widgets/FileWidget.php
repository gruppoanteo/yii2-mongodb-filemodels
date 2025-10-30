<?php
namespace anteo\mongodb\filemodels\widgets;

use anteo\mongodb\filemodels\FilemodelAsset;
use anteo\mongodb\filemodels\FilemodelBehavior;
use anteo\mongodb\filemodels\models\embedded\Metadata;
use anteo\mongodb\filemodels\models\Filemodel;
use anteo\mongodb\filemodels\models\FilemodelSearch;
use anteo\mongodb\filemodels\traits\ContainerAwareTrait;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\mongodb\file\ActiveRecord;
use yii\widgets\Pjax;

class FileWidget extends Widget
{
    use ContainerAwareTrait;
    
    const VIEW_GRID = 'grid';
    const VIEW_LIST = 'list';
    
    public $moduleId = 'filemodels';
    /**
     * @var ActiveRecord 
     */
    public $model;
    /**
     * @var string upload widget
     */
    public $uploadWidgetClass;
    /**
     * @var array upload field configuration params
     */
    public $uploadWidgetConfig = [];
     /**
     * @var array upload field configuration params
     */
    public $fieldConfig = [];
    /**
     * @var string. List type 
     */
    public $viewType = self::VIEW_GRID;
    /**
     * @var array additional params to view/upload
     */
    public $viewParams = [];
    /**
     * @var boolean. Show serial column
     */
    public $showSerialColumn = false;
    /**
     * @var boolean. Show serial column
     */
    public $enableDescription = false;
    /**
     * @var boolean. Show serial column
     */
    public $multiple = true;
    /**
     * @var boolean search files in list view
     */
    public $search = true;
    /**
     * @var array container options
     */
    public $options = [];
    /**
     * @var array plugin options
     */
    public $clientOptions = [];
    /**
     * @var string empty text
     */
    public $emptyText;
    /**
     * @var integer pjaxConfig
     */
    public $pjaxOptions = [];
    /**
     * @var boolean 
     */
    public $readOnly = false;
    
    protected $viewPath;
    
    private $_behavior;
    private $_module;
    
    public function init()
    {
        parent::init();
        if (($this->_module = Yii::$app->getModule($this->moduleId)) === null) {
            throw new InvalidConfigException('moduleId is invalid');
        }
        
        if ($this->model == '') {
            throw new InvalidConfigException('model is required');
        }
        foreach ($this->model->getBehaviors() as $behavior) {
            if ($behavior instanceof FilemodelBehavior) {
                $this->_behavior = $behavior;
            }
        }
        if ($this->_behavior == null) {
            throw new InvalidConfigException('model needs ' . FilemodelBehavior::class);
        }
        
        if (!empty($this->uploadViewParams) && !ArrayHelper::isAssociative($this->uploadViewParams)) {
            throw new InvalidConfigException('uploadViewParams must be an associative array');
        }
//        if (empty($this->relatedTo)) {
//            $this->relatedTo = get_class($this->model) . ':' . $this->model->primaryKey;
//        }
//        $model = $this->model;
//        foreach ($model->getBehaviors() as $behavior) {
//            if ($behavior instanceof FilemodelBehavior) {
//                $this->_behavior = $behavior;
//                break;
//            }
//        }
//        if ($this->_behavior === null) {
//            throw new InvalidConfigException($model::className() . ' must have ' . FilemodelBehavior::className());
//        }
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        } else {
            $this->id = $this->options['id'];
        }
        echo Html::beginTag('div', $this->options);
    }
    
    public function setViewPath($path)
    {
        $this->viewPath = $path;
    }
    
    public function getViewPath()
    {
        if ($this->viewPath !== null) {
            return $this->viewPath;
        }
        return parent::getViewPath();
    }

    public function run()
    {
        parent::run();
        $this->registerAssets();

//        $filemodel = Yii::createObject($this->_module->modelClass);
//        $filemodel->scenario = ($this->_behavior->emptyDescription) ? Filemodel::SCENARIO_UPLOAD : Filemodel::SCENARIO_UPLOAD_DESCR;
//        $filemodel->entity = $this->_behavior->entity;
//        $filemodel->entity_id = $this->model->{$this->_behavior->idAttribute};
//        $filemodel->tag = $this->tag;
        
//        $filemodelClass = $this->getModule()->modelClass;
//        $filemodel = $filemodelClass::create($this->model, '', $this->tag);
//        $filemodel->related_to = $this->relatedTo;
        
//        foreach ($this->_behavior->fileValidationRules as $rules) {
//            $filemodel->validators[] = Validator::createValidator($rules[0], $filemodel, 'file', array_slice($rules, 1));
//        }
        
        $metadata = $this->make(Metadata::class, [], [
            'entity' => $this->_behavior->getEntity(), 
            'entity_id' => $this->model->primaryKey,
//            'related_to' => $this->relatedTo
        ]);
        
        $filemodel = $this->make(Filemodel::class, [], [
            'metadataModel' => $metadata
        ]);

        $files = $this->model->getFiles();
        
        if ($files->count() == 0 && $this->emptyText !== null) {
            echo $this->emptyText;
        }
        
        $pjaxOptions = ArrayHelper::merge([
            'id' => $this->getPjaxId(), 
            'enablePushState' => false,
        ], $this->pjaxOptions);
        
        Pjax::begin($pjaxOptions);
        
        if ($this->multiple || $files->count() == 0 && !$this->multiple) {
            echo $this->render('upload.php', array_merge([
                'model' => $filemodel,
                'metadata' => $metadata,
                'formId' => $this->formId,
                'alertId' => $this->alertId,
            ], $this->viewParams));
        }
        
        $searchModel = $this->make(FilemodelSearch::class, [], [
            'entity' => $this->_behavior->getEntity(),
            'entity_id' => $this->model->primaryKey
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        echo $this->render($this->viewType == static::VIEW_GRID ? 'grid' : 'list', array_merge([
            'dataProvider' => $dataProvider,
            'searchModel' => $this->search ? $searchModel : null,
            'gridId' => $this->gridId,
        ], $this->viewParams));
        
        Pjax::end();
        
        echo Html::endTag('div');
    }
    
    public function getGridId()
    {
        return $this->id . '-grid';
    }
    
    public function getPjaxId()
    {
        return ArrayHelper::getValue($this->pjaxOptions, 'id', $this->id . '-pjax');
    }
    
    public function getFormId()
    {
        return $this->id . '-form';
    }
    
    public function getAlertId()
    {
        return $this->id . '-alert';
    }

    protected function registerAssets()
    {
        $view = $this->getView();
        FilemodelAsset::register($view);

        $this->clientOptions['pjaxId'] = '#' . $this->getPjaxId();
        $this->clientOptions['gridId'] = '#' . $this->getGridId();
        $this->clientOptions['formId'] = '#' . $this->getFormId();
        $this->clientOptions['alertId'] = '#' . $this->getAlertId();
        
        $options = Json::encode($this->clientOptions);
        $view->registerJs("$('#{$this->id}').filemodel($options)");
    }
}
