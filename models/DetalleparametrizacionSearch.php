<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Detalleparametrizacion;

/**
 * DetalleparametrizacionSearch represents the model behind the search form about `app\models\Detalleparametrizacion`.
 */
class DetalleparametrizacionSearch extends Detalleparametrizacion
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_categoriagestion'], 'integer'],
            [['name_parametrizacion', 'categoria','configuracion'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
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
    public function search($params)
    {
        $query = Detalleparametrizacion::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'id_categoriagestion' => $this->id_categoriagestion,
            'categoria' => $this->categoria,
        ]);

        $query->andFilterWhere(['like', 'name_parametrizacion', $this->name_parametrizacion])
            ->andFilterWhere(['like', 'configuracion', $this->configuracion]);
            

        return $dataProvider;
    }
}
