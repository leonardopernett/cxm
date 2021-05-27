<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Reglanegocio;

/**
 * ReglaNegocioSearch represents the model behind the search form about `app\models\Reglanegocio`.
 */
class ReglaNegocioSearch extends Reglanegocio {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'pcrc', 'cliente', 'cod_industria', 'cod_institucion'], 'integer'],
            [['rn', 'tipo_regla', 'pcrcName', 'clienteName'], 'safe'],
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
        $query = Reglanegocio::find();
        $query->join("INNER JOIN", "tbl_arbols a", "a.id =  tbl_reglanegocio.pcrc");
        $query->join("INNER JOIN", "tbl_arbols b", "b.id =  tbl_reglanegocio.cliente");
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'pcrc' => $this->pcrc,
            'cliente' => $this->cliente,
            'cod_industria' => $this->cod_industria,
            'cod_institucion' => $this->cod_institucion,
            'id_formulario' => $this->id_formulario,
        ]);

        $query->andFilterWhere(['like', 'rn', $this->rn])
                ->andFilterWhere(['like', 'tipo_regla', $this->tipo_regla])
                ->andFilterWhere(['like', 'promotores', $this->promotores])
                ->andFilterWhere(['like', 'detractores', $this->detractores])
                ->andFilterWhere(['like', 'neutros', $this->neutros]);
        $query->andWhere("a.name LIKE '%" . $this->pcrcName . "%'");
        $query->andWhere("b.name LIKE '%" . $this->clienteName . "%'");

        return $dataProvider;
    }

}
