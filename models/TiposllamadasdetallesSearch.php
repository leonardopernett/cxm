<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Tiposllamadasdetalles;

/**
 * TiposllamadasdetallesSearch represents the model behind the search form about `app\models\Tiposllamadasdetalles`.
 */
class TiposllamadasdetallesSearch extends Tiposllamadasdetalles {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'tiposllamada_id'], 'integer'],
            [['name'], 'safe'],
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
        $query = Tiposllamadasdetalles::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'tiposllamada_id' => $this->tiposllamada_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }

}
