<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Tmptableroexperiencias;

/**
 * TmptableroexperienciasSearch represents the model behind the search form about `app\models\Tmptableroexperiencias`.
 */
class TmptableroexperienciasSearch extends Tmptableroexperiencias {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'tmpejecucionformulario_id', 'tableroenfoque_id', 'tableroproblemadetalle_id'], 'integer'],
            [['detalle'], 'safe'],
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
        $query = Tmptableroexperiencias::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'tmpejecucionformulario_id' => $this->tmpejecucionformulario_id,
            'tableroenfoque_id' => $this->tableroenfoque_id,
            'tableroproblemadetalle_id' => $this->tableroproblemadetalle_id,
        ]);

        $query->andFilterWhere(['like', 'detalle', $this->detalle]);

        return $dataProvider;
    }

}
