<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Calificaciondetalles;

/**
 * CalificaciondetallesSearch represents the model behind the search form about `app\models\Calificaciondetalles`.
 */
class CalificaciondetallesSearch extends Calificaciondetalles {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'sndespliega_tipificaciones', 'calificacion_id', 'nmorden', 'i1_snopcion_na', 'i2_snopcion_na', 'i3_snopcion_na', 'i4_snopcion_na', 'i5_snopcion_na', 'i6_snopcion_na', 'i7_snopcion_na', 'i8_snopcion_na', 'i9_snopcion_na', 'i10_snopcion_na'], 'integer'],
            [['name'], 'safe'],
            [['i1_povalor', 'i2_povalor', 'i3_povalor', 'i4_povalor', 'i5_povalor', 'i6_povalor', 'i7_povalor', 'i8_povalor', 'i9_povalor', 'i10_povalor'], 'number'],
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
        $query = Calificaciondetalles::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'sndespliega_tipificaciones' => $this->sndespliega_tipificaciones,
            'calificacion_id' => $this->calificacion_id,
            'nmorden' => $this->nmorden,
            'i1_povalor' => $this->i1_povalor,
            'i2_povalor' => $this->i2_povalor,
            'i3_povalor' => $this->i3_povalor,
            'i4_povalor' => $this->i4_povalor,
            'i5_povalor' => $this->i5_povalor,
            'i6_povalor' => $this->i6_povalor,
            'i7_povalor' => $this->i7_povalor,
            'i8_povalor' => $this->i8_povalor,
            'i9_povalor' => $this->i9_povalor,
            'i10_povalor' => $this->i10_povalor,
            'i1_snopcion_na' => $this->i1_snopcion_na,
            'i2_snopcion_na' => $this->i2_snopcion_na,
            'i3_snopcion_na' => $this->i3_snopcion_na,
            'i4_snopcion_na' => $this->i4_snopcion_na,
            'i5_snopcion_na' => $this->i5_snopcion_na,
            'i6_snopcion_na' => $this->i6_snopcion_na,
            'i7_snopcion_na' => $this->i7_snopcion_na,
            'i8_snopcion_na' => $this->i8_snopcion_na,
            'i9_snopcion_na' => $this->i9_snopcion_na,
            'i10_snopcion_na' => $this->i10_snopcion_na,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }

}
