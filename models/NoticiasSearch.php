<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Noticias;

/**
 * NoticiasSearch represents the model behind the search form about `app\models\Noticias`.
 */
class NoticiasSearch extends Noticias {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'activa'], 'integer'],
            [['titulo', 'descripcion', 'created', 'created_by', 'modified', 'modified_by'], 'safe'],
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
        $query = Noticias::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'activa' => $this->activa,
            'created' => $this->created,
            'modified' => $this->modified,
        ]);

        $query->andFilterWhere(['like', 'titulo', $this->titulo])
                ->andFilterWhere(['like', 'descripcion', $this->descripcion])
                ->andFilterWhere(['like', 'created_by', $this->created_by])
                ->andFilterWhere(['like', 'modified_by', $this->modified_by]);

        return $dataProvider;
    }

}
