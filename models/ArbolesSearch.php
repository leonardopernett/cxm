<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Arboles;

/**
 * ArbolesSearch represents the model behind the search form about `app\models\Arboles`.
 */
class ArbolesSearch extends Arboles {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'arbol_id', 'snhoja', 'formulario_id', 'usua_id_responsable', 'tableroproblema_id', 'tiposllamada_id', 'bloquedetalle_id', 'snactivar_problemas', 'snactivar_tipo_llamada'], 'integer'],
            [['name', 'dsorden', 'dsname_full', 'equipos'], 'safe'],
            [['nmfactor_proceso', 'nmumbral_verde', 'nmumbral_amarillo', 'nmumbral_positivo'], 'number'],
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
        $query = Arboles::find()->orderBy('dsorden');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'arbol_id' => $this->arbol_id,
            'snhoja' => $this->snhoja,
            'formulario_id' => $this->formulario_id,
            'nmfactor_proceso' => $this->nmfactor_proceso,
            'nmumbral_verde' => $this->nmumbral_verde,
            'nmumbral_amarillo' => $this->nmumbral_amarillo,
            'nmumbral_positivo' => $this->nmumbral_positivo,
            'usua_id_responsable' => $this->usua_id_responsable,
            'tableroproblema_id' => $this->tableroproblema_id,
            'tiposllamada_id' => $this->tiposllamada_id,
            'bloquedetalle_id' => $this->bloquedetalle_id,
            'snactivar_problemas' => $this->snactivar_problemas,
            'snactivar_tipo_llamada' => $this->snactivar_tipo_llamada,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
                ->andFilterWhere(['like', 'dsorden', $this->dsorden])
                ->andFilterWhere(['like', 'dsname_full', $this->dsname_full]);

        return $dataProvider;
    }

}
