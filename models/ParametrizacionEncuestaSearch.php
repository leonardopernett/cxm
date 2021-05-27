<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ParametrizacionEncuesta;

/**
 * ParametrizacionEncuestaSearch represents the model behind the search form about `app\models\ParametrizacionEncuesta`.
 */
class ParametrizacionEncuestaSearch extends ParametrizacionEncuesta {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'cliente', 'programa'], 'integer'],
            [['clienteName', 'pcrcName'], 'safe'],
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
        $query = ParametrizacionEncuesta::find();
        //$query->joinWith(['cliente0']);
        $query->joinWith(['programa0']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['clienteName'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['tbl_arbols.name' => SORT_ASC],
            'desc' => ['tbl_arbols.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['pcrcName'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['tbl_arbols.name' => SORT_ASC],
            'desc' => ['tbl_arbols.name' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'cliente' => $this->cliente,
            'programa' => $this->programa,
        ]);

        $query->andFilterWhere([
            //'like', 'tbl_arbols.name', $this->clienteName,
            'like', 'tbl_arbols.name', $this->pcrcName,
        ]);

        return $dataProvider;
    }

}
