<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Formularios;

/**
 * FormulariosSearch represents the model behind the search form about `app\models\Formularios`.
 */
class FormulariosSearch extends Formularios {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'nmorden'], 'integer'],
            [['name', 'i1_cdtipo_eval', 'i2_cdtipo_eval', 'i3_cdtipo_eval', 'i4_cdtipo_eval', 'i5_cdtipo_eval', 'i6_cdtipo_eval', 'i7_cdtipo_eval', 'i8_cdtipo_eval', 'i9_cdtipo_eval', 'i10_cdtipo_eval'], 'safe'],
            [['i1_nmfactor', 'i2_nmfactor', 'i3_nmfactor', 'i4_nmfactor', 'i5_nmfactor', 'i6_nmfactor', 'i7_nmfactor', 'i8_nmfactor', 'i9_nmfactor', 'i10_nmfactor'], 'number'],
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
        $query = Formularios::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'nmorden' => $this->nmorden,
            'i1_nmfactor' => $this->i1_nmfactor,
            'i2_nmfactor' => $this->i2_nmfactor,
            'i3_nmfactor' => $this->i3_nmfactor,
            'i4_nmfactor' => $this->i4_nmfactor,
            'i5_nmfactor' => $this->i5_nmfactor,
            'i6_nmfactor' => $this->i6_nmfactor,
            'i7_nmfactor' => $this->i7_nmfactor,
            'i8_nmfactor' => $this->i8_nmfactor,
            'i9_nmfactor' => $this->i9_nmfactor,
            'i10_nmfactor' => $this->i10_nmfactor,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
                ->andFilterWhere(['like', 'i1_cdtipo_eval', $this->i1_cdtipo_eval])
                ->andFilterWhere(['like', 'i2_cdtipo_eval', $this->i2_cdtipo_eval])
                ->andFilterWhere(['like', 'i3_cdtipo_eval', $this->i3_cdtipo_eval])
                ->andFilterWhere(['like', 'i4_cdtipo_eval', $this->i4_cdtipo_eval])
                ->andFilterWhere(['like', 'i5_cdtipo_eval', $this->i5_cdtipo_eval])
                ->andFilterWhere(['like', 'i6_cdtipo_eval', $this->i6_cdtipo_eval])
                ->andFilterWhere(['like', 'i7_cdtipo_eval', $this->i7_cdtipo_eval])
                ->andFilterWhere(['like', 'i8_cdtipo_eval', $this->i8_cdtipo_eval])
                ->andFilterWhere(['like', 'i9_cdtipo_eval', $this->i9_cdtipo_eval])
                ->andFilterWhere(['like', 'i10_cdtipo_eval', $this->i10_cdtipo_eval]);

        return $dataProvider;
    }

}
