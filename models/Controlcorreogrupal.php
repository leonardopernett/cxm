<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Correogrupal;

/**
 * ControlProcesosEquipos represents the model behind the search form about `app\models\ControlProcesos`.
 */
class Controlcorreogrupal extends ControlProcesos
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idcg','usua_id'], 'integer'],
            [['fechacreacion','nombre'], 'safe'],
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
    public function obtenercorreogrupal($params)
    {
        $query = Correogrupal::find()
                ->select(['nombre','fechacreacion'])->distinct();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'usua_id' => $this->usua_id,
        ]);

        $query->andFilterWhere(['like', 'usua_id', $this->usua_id]);

        return $dataProvider;
    }
}