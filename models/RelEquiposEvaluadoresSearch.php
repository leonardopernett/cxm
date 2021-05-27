<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\RelEquiposEvaluadores;

/**
 * EquiposEvaluadosSearch represents the model behind the search form about `app\models\EquiposEvaluados`.
 */
class RelEquiposEvaluadoresSearch extends RelEquiposEvaluadores {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'equipo_id', 'evaluadores_id'], 'integer'],
            [['evaluadorName', 'equipoName'], 'safe']
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
        $query = RelEquiposEvaluadores::find();
        $query->joinWith(['evaluadores']);
        $query->orderBy('usua_nombre ASC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['evaluadorName'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['tbl_usuarios.usua_nombre' => SORT_ASC],
            'desc' => ['tbl_usuarios.usua_nombre' => SORT_DESC],
        ];
        //$this->load($params);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'tbl_rel_equipos_evaluadores.id' => $this->id,
            'tbl_rel_equipos_evaluadores.evaluadores_id' => $this->evaluadores_id,
            'tbl_rel_equipos_evaluadores.equipo_id' => $this->equipo_id,
        ]);

        $query->andFilterWhere(['like', 'tbl_usuarios.usua_nombre', $this->evaluadorName]);

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
            'asc' => ['tbl_equipos_evaluadores.name' => SORT_ASC],
            'desc' => ['tbl_equipos_evaluadores.name' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'tbl_rel_equipos_evaluadores.id' => $this->id,
            'tbl_rel_equipos_evaluadores.evaluadores_id' => $this->evaluado_id,
            'tbl_rel_equipos_evaluadores.equipo_id' => $this->equipo_id,
        ]);

        $query->andFilterWhere(['like', 'tbl_equipos_evaluadores.name', $this->equipoName]);

        return $dataProvider;
    }

}
