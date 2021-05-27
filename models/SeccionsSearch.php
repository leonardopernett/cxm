<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Seccions;

/**
 * SeccionsSearch represents the model behind the search form about `app\models\Seccions`.
 */
class SeccionsSearch extends Seccions {

    public $formularioName;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'tiposeccion_id', 'nmorden', 'sndesplegar_comentario'],
                'integer'],
            [['name', 'formularioName', 'formulario_id', 'i1_cdtipo_eval',
            'i2_cdtipo_eval', 'i3_cdtipo_eval', 'i4_cdtipo_eval',
            'i5_cdtipo_eval', 'i6_cdtipo_eval', 'i7_cdtipo_eval',
            'i8_cdtipo_eval', 'i9_cdtipo_eval', 'i10_cdtipo_eval'], 'safe'],
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
        $query = Seccions::find();
        $query->joinWith(['formulario']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['nmorden'=>SORT_ASC]]
        ]);

        $dataProvider->sort->attributes['formularioName'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['tbl_formularios.name' => SORT_ASC],
            'desc' => ['tbl_formularios.name' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {

            return $dataProvider;
        }

        $query->andFilterWhere([
            'tbl_seccions.id' => $this->id,
            'tbl_seccions.formulario_id' => $this->formulario_id,
            'tbl_seccions.tiposeccion_id' => $this->tiposeccion_id,
            'tbl_seccions.nmorden' => $this->nmorden,
            'tbl_seccions.i1_nmfactor' => $this->i1_nmfactor,
            'tbl_seccions.i2_nmfactor' => $this->i2_nmfactor,
            'tbl_seccions.i3_nmfactor' => $this->i3_nmfactor,
            'tbl_seccions.i4_nmfactor' => $this->i4_nmfactor,
            'tbl_seccions.i5_nmfactor' => $this->i5_nmfactor,
            'tbl_seccions.i6_nmfactor' => $this->i6_nmfactor,
            'tbl_seccions.i7_nmfactor' => $this->i7_nmfactor,
            'tbl_seccions.i8_nmfactor' => $this->i8_nmfactor,
            'tbl_seccions.i9_nmfactor' => $this->i9_nmfactor,
            'tbl_seccions.i10_nmfactor' => $this->i10_nmfactor,
            'tbl_seccions.sndesplegar_comentario' => $this->sndesplegar_comentario,
        ]);

        $query->andFilterWhere(['like', 'tbl_seccions.name', $this->name])
                ->andFilterWhere(['like', 'tbl_seccions.i1_cdtipo_eval', $this->i1_cdtipo_eval])
                ->andFilterWhere(['like', 'tbl_seccions.i2_cdtipo_eval', $this->i2_cdtipo_eval])
                ->andFilterWhere(['like', 'tbl_seccions.i3_cdtipo_eval', $this->i3_cdtipo_eval])
                ->andFilterWhere(['like', 'tbl_seccions.i4_cdtipo_eval', $this->i4_cdtipo_eval])
                ->andFilterWhere(['like', 'tbl_seccions.i5_cdtipo_eval', $this->i5_cdtipo_eval])
                ->andFilterWhere(['like', 'tbl_seccions.i6_cdtipo_eval', $this->i6_cdtipo_eval])
                ->andFilterWhere(['like', 'tbl_seccions.i7_cdtipo_eval', $this->i7_cdtipo_eval])
                ->andFilterWhere(['like', 'tbl_seccions.i8_cdtipo_eval', $this->i8_cdtipo_eval])
                ->andFilterWhere(['like', 'tbl_seccions.i9_cdtipo_eval', $this->i9_cdtipo_eval])
                ->andFilterWhere(['like', 'tbl_seccions.i10_cdtipo_eval', $this->i10_cdtipo_eval])
                ->andFilterWhere(['like', 'tbl_formularios.name', $this->formularioName]);

        return $dataProvider;
    }

}
