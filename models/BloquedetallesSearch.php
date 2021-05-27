<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Bloquedetalles;

/**
 * BloquedetallesSearch represents the model behind the search form about `app\models\Bloquedetalles`.
 */
class BloquedetallesSearch extends Bloquedetalles {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'bloque_id', 'calificacion_id', 'tipificacion_id', 'nmorden'],
                'integer'],
            [['name', 'bloqueName'], 'safe'],
            [['i1_nmfactor', 'i2_nmfactor', 'i3_nmfactor', 'i4_nmfactor', 'i5_nmfactor',
            'i6_nmfactor', 'i7_nmfactor', 'i8_nmfactor', 'i9_nmfactor', 'i10_nmfactor'],
                'number'],
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
        $query = Bloquedetalles::find();
        $query->joinWith(['bloque']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['nmorden'=>SORT_ASC]]
        ]);

        $dataProvider->sort->attributes['bloqueName'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['tbl_bloques.name' => SORT_ASC],
            'desc' => ['tbl_bloques.name' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'tbl_bloquedetalles.id' => $this->id,
            'tbl_bloquedetalles.bloque_id' => $this->bloque_id,
            'tbl_bloquedetalles.calificacion_id' => $this->calificacion_id,
            'tbl_bloquedetalles.tipificacion_id' => $this->tipificacion_id,
            'tbl_bloquedetalles.nmorden' => $this->nmorden,
            'tbl_bloquedetalles.i1_nmfactor' => $this->i1_nmfactor,
            'tbl_bloquedetalles.i2_nmfactor' => $this->i2_nmfactor,
            'tbl_bloquedetalles.i3_nmfactor' => $this->i3_nmfactor,
            'tbl_bloquedetalles.i4_nmfactor' => $this->i4_nmfactor,
            'tbl_bloquedetalles.i5_nmfactor' => $this->i5_nmfactor,
            'tbl_bloquedetalles.i6_nmfactor' => $this->i6_nmfactor,
            'tbl_bloquedetalles.i7_nmfactor' => $this->i7_nmfactor,
            'tbl_bloquedetalles.i8_nmfactor' => $this->i8_nmfactor,
            'tbl_bloquedetalles.i9_nmfactor' => $this->i9_nmfactor,
            'tbl_bloquedetalles.i10_nmfactor' => $this->i10_nmfactor,
        ]);

        $query->andFilterWhere(['like', 'tbl_bloquedetalles.name', $this->name])
                ->andFilterWhere(['like', 'tbl_bloques.name', $this->bloqueName]);

        return $dataProvider;
    }

}
