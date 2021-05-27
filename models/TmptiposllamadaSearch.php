<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Tmptiposllamada;

/**
 * TmptiposllamadaSearch represents the model behind the search form about `app\models\Tmptiposllamada`.
 */
class TmptiposllamadaSearch extends Tmptiposllamada {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'tiposllamadasdetalle_id', 'tmpejecucionformulario_id'], 'integer'],
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
        $query = Tmptiposllamada::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'tiposllamadasdetalle_id' => $this->tiposllamadasdetalle_id,
            'tmpejecucionformulario_id' => $this->tmpejecucionformulario_id,
        ]);

        return $dataProvider;
    }

}
