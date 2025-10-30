<?php

namespace anteo\mongodb\filemodels;

use anteo\mongodb\filemodels\traits\ContainerAwareTrait;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class FilemodelBehavior extends Behavior
{
    use ContainerAwareTrait;
    
    /**
     * @var array file validator config
     * first element is the validator type. This can be a built-in validator name,
     * a method name of the model class, an anonymous function, or a validator class name.
     * for example:
     * ```php
     * [
     *     [
     *         'file',
     *         'extensions' => ['jpg', 'gif'],
     *         'maxSize' => 1000
     *     ]
     * ]
     * ```
     */
    public $fileValidationRules = [];
    
    public $crcEntity = false;
    
    private $_entity = null;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete'
        ];
    }

    public function setEntity($value)
    {
        if ($value instanceof \Closure) {
            $this->_entity = call_user_func($value, $this->owner);
        } else {
            $this->_entity = $value;
        }
    }

    public function getEntity()
    {
        if ($this->_entity === null) {
            $this->_entity = ($this->crcEntity) ? hash('crc32', get_class($this->owner)) : get_class($this->owner);
        }
        return $this->_entity;
    }
    
    public function getFiles()
    {
        $model = $this->make(models\Filemodel::class);
        $query = $this->owner
            ->hasMany(get_class($model), ['metadata.entity_id' => implode(',', $this->owner->primaryKey())])
            ->andWhere(['metadata.entity' => $this->entity]);
        return $query;
    }

    public function afterDelete($event)
    {
        foreach ($this->files->all() as $file) {
            $file->delete();
        }
    }
}