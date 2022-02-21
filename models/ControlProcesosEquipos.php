<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ControlProcesos;

/**
 * ControlProcesosEquipos represents the model behind the search form about `app\models\ControlProcesos`.
 */
class ControlProcesosEquipos extends ControlProcesos
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['evaluados_id'], 'integer'],
            [['salario', 'tipo_corte', 'cant_valor', 'Dedic_valora', 'responsable'], 'safe'],
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
    public function search1($params)
    {
        $query = ControlProcesos::find()               
                    ->joinWith('usuarios')
                    ->where(['anulado' => '0'])
                    ->andwhere(['responsable' => Yii::$app->user->identity->id])
		    ->orderBy([
                              'id' => SORT_DESC
                            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'evaluados_id' => $this->evaluados_id,
        ]);

        $query->andFilterWhere(['like', 'evaluados_id', $this->evaluados_id]);

        return $dataProvider;
    }



    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchplan2($params)
    {
        $txtUsuarios = $params;
        $query = ControlProcesos::find()         
                    ->joinWith('usuarios')
                    ->where(['anulado' => '0'])
                    ->andwhere(['evaluados_id' => $txtUsuarios])
                    ->orderBy([
                              'id' => SORT_DESC
                            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'evaluados_id' => $this->evaluados_id,
        ]);

        $query->andFilterWhere(['like', 'evaluados_id', $this->evaluados_id]);

        return $dataProvider;
    }    
}
