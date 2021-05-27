<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Tipificaciondetalles;

/**
 * TipificaciondetallesSearch represents the model behind the search form about `app\models\Tipificaciondetalles`.
 */
class TipificaciondetallesSearch extends Tipificaciondetalles {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'tipificacion_id', 'subtipificacion_id', 'nmorden', 'snen_uso'],
                'integer'],
            [['name', 'subTipificacionName'], 'safe'],
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
        $query = Tipificaciondetalles::find();
        $query->joinWith(['subtipificacion']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);


        $dataProvider->sort->attributes['subTipificacionName'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['tbl_tipificacions.name' => SORT_ASC],
            'desc' => ['tbl_tipificacions.name' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'tbl_tipificaciondetalles.id' => $this->id,
            'tbl_tipificaciondetalles.tipificacion_id' => $this->tipificacion_id,
            'tbl_tipificaciondetalles.subtipificacion_id' => $this->subtipificacion_id,
            'tbl_tipificaciondetalles.nmorden' => $this->nmorden,
            'tbl_tipificaciondetalles.snen_uso' => $this->snen_uso,
        ]);

        $query->andFilterWhere(['like', 'tbl_tipificaciondetalles.name', $this->name])
                ->andFilterWhere(['like', 'tbl_tipificacions.name', $this->subTipificacionName]);

        return $dataProvider;
    }

}
