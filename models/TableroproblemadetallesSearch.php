<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Tableroproblemadetalles;

/**
 * TableroproblemadetallesSearch represents the model behind the search form about `app\models\Tableroproblemadetalles`.
 */
class TableroproblemadetallesSearch extends Tableroproblemadetalles {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'tableroproblema_id', 'tableroenfoque_id'], 'integer'],
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
        $query = Tableroproblemadetalles::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'tableroproblema_id' => $this->tableroproblema_id,
            'tableroenfoque_id' => $this->tableroenfoque_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }

}
