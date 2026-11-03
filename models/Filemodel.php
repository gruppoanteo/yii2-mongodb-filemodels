<?php

namespace anteo\mongodb\filemodels\models;

use anteo\mongodb\filemodels\models\embedded\Metadata;
use anteo\mongodb\filemodels\Module;
use Yii;
use yii2tech\embedded\mongodb\ActiveRecordFile;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use \yii\web\Application as WebApplication;

/**
 * This is the model class for table "{{%filemodel}}".
 *
 * @property integer $id
 * @property string $entity
 * @property integer $entity_id
 * @property string $uuid
 * @property string $tag
 * @property string $description
 * @property string $basename
 * @property string $ext
 * @property resource $content
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 */
class Filemodel extends ActiveRecordFile
{

    protected $iconMap = [
        'default' => 'file-o',
        'image' => 'file-picture-o',
        'video' => 'file-video-o',
        'audio' => 'file-audio-o',
        'pdf' => 'file-pdf-o',
        'word' => 'file-word-o',
        'excel' => 'file-excel-o',
        'powerpoint' => 'file-powerpoint-o',
        'sql' => 'database',
        'x-ms-dos-executable' => 'terminal',
        'x-zip' => 'file-archive-o',
        'x-tar' => 'file-archive-o',
        'x-rar' => 'file-archive-o',
    ];
    
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'filemodels';
    }
    
    public static function find()
    {
        return new FilemodelQuery(static::class);
    }
    
    public function attributes()
    {
        return array_merge(parent::attributes(), [
            'metadata',
            'contentType'
        ]);
    }

    public function embedMetadataModel()
    {
        return $this->mapEmbedded('metadata', Metadata::class);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            ['file', 'required'],
            ['file', 'file'],
        ];
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'description' => Module::t('filemodels', 'Description'),
            'basename' => Module::t('filemodels', 'Basename'),
            'fullUploader' => Module::t('filemodels', 'Uploaded by'),
            'length' => Module::t('filemodels', 'Size'),
            'filename' => Module::t('filemodels', 'Filename'),
        ];
    }

    public function getUploader()
    {
//        if (isset(Yii::$app->user->identity))
        $modelClass = Yii::$app->user->identityClass;
        return $modelClass::findOne($this->metadataModel->uploaded_by);
    }

    public function getFullUploader()
    {
        return ArrayHelper::getValue($this->uploader, 'username') . ", " . Yii::$app->formatter->asDatetime($this->uploadDate->toDateTime(), 'short');
    }

    public function getIcon()
    {
        $arrMime = explode('/', (string)$this->contentType);
        if ($arrMime[0] == 'application') {
            $type = $arrMime[1];
        } else {
            $type = $arrMime[0];
        }
        if (!in_array($type, array_keys($this->iconMap))) {
            $type = 'default';
        }
        return $this->iconMap[$type];
    }

//    protected function getUpcountName($name)
//    {
//        return preg_replace_callback(
//        '/(?:(?: \(([\d]+)\))?(\.[^.]+))?$/', function($matches) {
//            $index = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
//            $ext = isset($matches[2]) ? $matches[2] : '';
//            return ' (' . $index . ')' . $ext;
//        }, $name, 1);
//    }

//    protected function getUniqueName($filename)
//    {
//        $module = $this->getModule();
//        while (is_dir($module->storagePath . DIRECTORY_SEPARATOR . $filename)) {
//            $filename = $this->upcountName($filename);
//        }
//        while (is_file($module->storagePath . DIRECTORY_SEPARATOR . $filename)) {
//            $filename = $this->upcountName($filename);
//        }
//        return $filename;
//    }
    
    public function beforeSave($insert)
    {
        if ($this->file instanceof UploadedFile) {
            $this->filename = $this->file->name;
            $this->contentType = $this->file->type;
        }
        $this->metadataModel->uploaded_by = (Yii::$app instanceof WebApplication && !Yii::$app->user->isGuest) ? Yii::$app->user->getId() : null;
        return parent::beforeSave($insert);
    }

}