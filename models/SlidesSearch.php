<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Slides;

/**
 * SlidesSearch represents the model behind the search form about `app\models\Slides`.
 */
class SlidesSearch extends Slides {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'activo'], 'integer'],
            [['titulo', 'descripcion', 'imagen', 'created', 'created_by', 'modified', 'modified_by'], 'safe'],
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
        $query = Slides::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'activo' => $this->activo,
            'created' => $this->created,
            'modified' => $this->modified,
        ]);

        $query->andFilterWhere(['like', 'titulo', $this->titulo])
                ->andFilterWhere(['like', 'descripcion', $this->descripcion])
                ->andFilterWhere(['like', 'imagen', $this->imagen])
                ->andFilterWhere(['like', 'created_by', $this->created_by])
                ->andFilterWhere(['like', 'modified_by', $this->modified_by]);

        return $dataProvider;
    }

}
