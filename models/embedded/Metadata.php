<?php
namespace anteo\mongodb\filemodels\models\embedded;

use yii\base\Model;
use yii\mongodb\validators\MongoIdValidator;

class Metadata extends Model
{
    public $description;
    
    public $uploaded_by;
    
    public $entity;
    
    public $entity_id;
    
//    public function __construct($entity, $entity_id, $config = array())
//    {
//        $this->entity = $entity;
//        $this->entity_id = $entity_id;
//        parent::__construct($config);
//    }

    public function rules()
    {
        $rules = [
            'required' => [['entity', 'entity_id'], 'required'],
            'integer' => [['entity_id'], 'filter', 'filter' => 'intVal'],
//            'mongo' => [['entity_id'], MongoIdValidator::class, 'format' => 'object'],
            'safe' => [['description'], 'safe']
        ];
        
        return $rules;
    }
}