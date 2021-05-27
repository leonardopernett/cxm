<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Evaluados;

/**
 * EvaluadosSearch represents the model behind the search form about `app\models\Evaluados`.
 */
class EvaluadosSearch extends Evaluados {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id'], 'integer'],
            [['name', 'telefono', 'dsusuario_red', 'cdestatus', 'identificacion', 'email','eqName','equId'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
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
    public function search($params) {
        $query = Evaluados::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
                ->andFilterWhere(['like', 'telefono', $this->telefono])
                ->andFilterWhere(['like', 'dsusuario_red', $this->dsusuario_red])
                ->andFilterWhere(['like', 'cdestatus', $this->cdestatus])
                ->andFilterWhere(['like', 'identificacion', $this->identificacion])
                ->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchExport($params) {
        $query = Evaluados::find();
        $query->from('tbl_evaluados e');
        $query->select('e.id,e.id AS idEvaluado, e.name AS evaluado,e.telefono,e.dsusuario_red,e.cdestatus,e.identificacion,e.email, eq.id AS equId,eq.name AS eqName, eq.*,e.*');
        $query->join('LEFT JOIN', 'tbl_equipos_evaluados ee', 'ee.evaluado_id = e.id');
        $query->join('LEFT JOIN', 'tbl_equipos eq', 'eq.id = ee.equipo_id');

        if (!($this->load($params) && $this->validate())) {
            return false;
        }

        $query->andFilterWhere([
            'e.id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'e.name', $this->name])
                ->andFilterWhere(['like', 'e.telefono', $this->telefono])
                ->andFilterWhere(['like', 'e.dsusuario_red', $this->dsusuario_red])
                ->andFilterWhere(['like', 'e.cdestatus', $this->cdestatus])
                ->andFilterWhere(['like', 'e.identificacion', $this->identificacion])
                ->andFilterWhere(['like', 'e.email', $this->email]);

        $dataProvider = $query->asArray()->all();

        return $dataProvider;
    }

}
