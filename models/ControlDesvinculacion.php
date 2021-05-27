<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * 
 */
class ControlDesvinculacion extends ControlDesvincular
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['iddesvincular','solicitante_id','evaluados_id','responsable'], 'integer'],
            [['motivo'], 'safe'],
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
    public function buscarpeticion($params)
    {
        $query = ControlDesvincular::find()        
                    ->joinWith('usuarios')
                    ->where(['anulado' => 'null']);                
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        return $dataProvider;
    }

}
