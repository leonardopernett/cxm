<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Equiposvaloradores;

/**
 * EquiposSearch represents the model behind the search form about `app\models\Equipos`.
 */
class EquiposvaloradoresSearch extends Equiposvaloradores {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'usua_id'], 'integer'],
            [['name'], 'safe'],
            [['nmumbral_verde', 'nmumbral_amarillo'], 'number'],
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
        $query = Equiposvaloradores::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'nmumbral_verde' => $this->nmumbral_verde,
            'nmumbral_amarillo' => $this->nmumbral_amarillo,
            'usua_id' => $this->usua_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }

}
