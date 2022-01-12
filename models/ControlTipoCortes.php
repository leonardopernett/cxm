<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Tiposdecortes;
use app\models\Tipocortes;

/**
 * ControlTipoCortes represents the model behind the search form about `app\models\Tiposdecortes`.
 */
class ControlTipoCortes extends Tiposdecortes
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idtc'], 'integer'],
            [['tipocortetc', 'fechainiciotc', 'fechafintc', 'cortetcs', 'diastc', 'cantdiastcs'], 'safe'],
            [['diastc'],'filter', 'filter' => function($value){
                return filter_var($value,FILTER_SANITIZE_STRING) ;
             }],

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
    public function searchcortes($params)
    {
        $query = Tipocortes::find()
                    ->joinWith('tiposdecortes')
		    ->orderBy([
                              'idtc' => SORT_DESC
                            ]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        return $dataProvider;
    }
}
