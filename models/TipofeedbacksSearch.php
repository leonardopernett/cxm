<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Tipofeedbacks;

/**
 * TipofeedbacksSearch represents the model behind the search form about `app\models\Tipofeedbacks`.
 */
class TipofeedbacksSearch extends Tipofeedbacks {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'categoriafeedback_id', 'snaccion_correctiva', 'sncausa_raiz', 'sncompromiso', 'cdtipo_automatico'], 'integer'],
            [['name', 'dsmensaje_auto'], 'safe'],
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
        $query = Tipofeedbacks::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'categoriafeedback_id' => $this->categoriafeedback_id,
            'snaccion_correctiva' => $this->snaccion_correctiva,
            'sncausa_raiz' => $this->sncausa_raiz,
            'sncompromiso' => $this->sncompromiso,
            'cdtipo_automatico' => $this->cdtipo_automatico,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
                ->andFilterWhere(['like', 'dsmensaje_auto', $this->dsmensaje_auto]);

        return $dataProvider;
    }

}
