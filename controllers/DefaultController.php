<?php

namespace anteo\mongodb\filemodels\controllers;

use anteo\mongodb\filemodels\models\embedded\Metadata;
use anteo\mongodb\filemodels\models\Filemodel;
use anteo\mongodb\filemodels\models\FilemodelQuery;
use anteo\mongodb\filemodels\Module;
use anteo\mongodb\filemodels\traits\ContainerAwareTrait;
use Yii;
use yii\base\Model;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class DefaultController extends Controller
{
    use ContainerAwareTrait;
    
    protected $filemodelQuery;
    
    public function __construct($id, Module $module, FilemodelQuery $filemodelQuery, array $config = [])
    {
        $this->filemodelQuery = $filemodelQuery;
        parent::__construct($id, $module, $config);
    }
    
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post']
                ]
            ]
        ];
    }

//    public function actionIndex()
//    {
//        $searchModel = $this->make(FilemodelSearch::class);
//        $dataProvider = $searchModel->search(Yii::$app->request->get());
////        return $this->render(
////            'index',
////            [
////                'dataProvider' => $dataProvider,
////                'searchModel' => $searchModel,
////            ]
////        );
//        return $this->renderContent('test');
//    }

    
    
    public function actionUpload()
    {
        if (!Yii::$app->getRequest()->isAjax && !Yii::$app->getRequest()->isPost) {
            throw new BadRequestHttpException;
        }
        $model = $this->make(Filemodel::class);
        $metadata = $this->make(Metadata::class);
        $postData = Yii::$app->request->post();
        
        $out = [];
        if ($model->load($postData) && $metadata->load($postData)) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if (Model::validateMultiple([$model, $metadata])) {
                $model->metadataModel = $metadata;
                if ($model->save(false)) {
                    $out['success'] = Module::t('filemodels', "File {filename} uploaded successfully.", ['filename' => $model->file->name]);
                } else {
                    $out['error'] = Module::t('filemodels', 'Unable to upload file');
                }
            } else {
                $out['error'] = $model->getErrors();
            }
        }
        return $this->asJson($out);
    }

    public function actionDownload($id)
    {
        return $this->runAction('view', [
            'id' => $id,
            'inline' => false
        ]);
    }

    public function actionView($id, $inline = true)
    {
        $model = $this->findModel($id);
        
        return Yii::$app->response->sendContentAsFile($model->file->getBytes(), $model->filename, [
            'inline' => $inline,
            'mimeType' => $model->contentType
        ]);
    }

    /**
     * Deletes an existing Documento model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id, $redirectUrl = null)
    {
        $delete = $this->findModel($id)->delete();
        if (Yii::$app->request->isAjax) {
            return $this->asJson($delete);
        } else {
//            return $this->redirect(\yii\helpers\Url::previous());
            return $this->redirect(($redirectUrl) ?: Yii::$app->request->referrer);
        }
    }
//
//    protected function findModelByHashKey($hash)
//    {
//        $modelClass = $this->module->modelClass;
//        if (($model = $modelClass::findByHashKey($hash)) !== null) {
//            return $model;
//        } else {
//            throw new NotFoundHttpException('The requested page does not exist.');
//        }
//    }

    protected function findModel($id)
    {
//        $modelClass = $this->module->modelClass;
        if (($model = $this->filemodelQuery->where(['_id' => $id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}