<?php

namespace anteo\mongodb\filemodels\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class FilemodelSearch extends Filemodel
{
    public $entity;
    
    public $entity_id;
    
    public $search;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['filename', 'search', 'entity', 'entity_id'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Filemodel::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'filename', 
                    'length',
                    'description' => [
                        'asc' => ['metadata.description' => SORT_ASC],
                        'desc' => ['metadata.description' => SORT_DESC]
                    ], 
                    'fullUploader' => [
                        'asc' => ['uploadDate' => SORT_ASC],
                        'desc' => ['uploadDate' => SORT_DESC]
                    ]
                ],
                'defaultOrder' => [
                    'filename' => SORT_ASC
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
//             $query->where('0=1');
            return $dataProvider;
        }
        
        $query->andWhere([
            'metadata.entity' => $this->entity,
            'metadata.entity_id' => $this->entity_id
        ]);

        $query->andFilterWhere([
            'or',
            ['like', 'metadata.description', $this->search],
            ['like', 'filename', $this->search]
        ]);

        return $dataProvider;
    }
}
