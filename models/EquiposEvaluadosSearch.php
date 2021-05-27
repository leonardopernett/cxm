<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\EquiposEvaluados;

/**
 * EquiposEvaluadosSearch represents the model behind the search form about `app\models\EquiposEvaluados`.
 */
class EquiposEvaluadosSearch extends EquiposEvaluados {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'equipo_id', 'evaluado_id'], 'integer'],
            [['evaluadoName', 'equipoName'], 'safe']
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
        $query = EquiposEvaluados::find();
        $query->joinWith(['evaluado']);
        $query->orderBy('name ASC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['evaluadoName'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['tbl_evaluados.name' => SORT_ASC],
            'desc' => ['tbl_evaluados.name' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'tbl_equipos_evaluados.id' => $this->id,
            'tbl_equipos_evaluados.evaluado_id' => $this->evaluado_id,
            'tbl_equipos_evaluados.equipo_id' => $this->equipo_id,
        ]);

        $query->andFilterWhere(['like', 'tbl_evaluados.name', $this->evaluadoName]);

        return $dataProvider;
    }
    
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchEquipos($params) {
        $query = EquiposEvaluados::find();
        $query->joinWith(['equipo']);        
        $query->orderBy('id DESC');                

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['equipoName'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['tbl_equipos.name' => SORT_ASC],
            'desc' => ['tbl_equipos.name' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'tbl_equipos_evaluados.id' => $this->id,
            'tbl_equipos_evaluados.evaluado_id' => $this->evaluado_id,
            'tbl_equipos_evaluados.equipo_id' => $this->equipo_id,
        ]);

        $query->andFilterWhere(['like', 'tbl_equipos.name', $this->equipoName]);

        return $dataProvider;
    }

}
