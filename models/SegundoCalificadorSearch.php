<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SegundoCalificador;

/**
 * SegundoCalificadorSearch represents the model behind the search form about `app\models\SegundoCalificador`.
 */
class SegundoCalificadorSearch extends SegundoCalificador {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id_segundo_calificador', 'id_solicitante', 'id_evaluador', 'id_responsable', 'b_segundo_envio', 'id_ejecucion_formulario'], 'integer'],
            [['estado_sc', 'argumento', 'argumentoLider', 'argumentoAsesor', 'argumentoLiderEvaluadores', 'formulario', 's_fecha', 'tipo_notifi', 'id_caso', 'gestionado'], 'safe'],
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
    public function search() {
        $query = SegundoCalificador::find();
        $responsable = Yii::$app->user->identity->id;
        $estado = '"Abierto","Escalado"';
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false
        ]);
        $query->select('tbl_segundo_calificador.*, f.name AS formulario');
        $query->join('LEFT JOIN', 'tbl_ejecucionformularios e', 'e.id = tbl_segundo_calificador.id_ejecucion_formulario');
        $query->join('LEFT JOIN', 'tbl_formularios f', 'f.id = e.formulario_id');
        $query->andFilterWhere([
            'id_responsable' => $responsable,
            'gestionado' => "NO",
        ]);
        $query->andWhere('estado_sc IN (' . $estado . ')');
        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchFilter($params) {
        $query = SegundoCalificador::find();
        $responsable = Yii::$app->user->identity->id;
        $estado = '"Abierto","Escalado"';
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false
        ]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $query->select('tbl_segundo_calificador.*, f.name AS formulario');
        $query->join('LEFT JOIN', 'tbl_ejecucionformularios e', 'e.id = tbl_segundo_calificador.id_ejecucion_formulario');
        $query->join('LEFT JOIN', 'tbl_formularios f', 'f.id = e.formulario_id');
        $query->andFilterWhere([
            'id_segundo_calificador' => $this->id_segundo_calificador,
            'id_solicitante' => $this->id_solicitante,
            'id_evaluador' => $this->id_evaluador,
            'id_responsable' => $responsable,
            'b_segundo_envio' => $this->b_segundo_envio,
            'id_ejecucion_formulario' => $this->id_ejecucion_formulario,
            'gestionado' => "NO",
        ]);
        $query->andWhere('estado_sc IN (' . $estado . ')');
        $query->andFilterWhere(['like', 'argumento', $this->argumento]);

        return $dataProvider;
    }

}
