<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Tmpejecucionfeedbacks;

/**
 * TmpejecucionfeedbacksSearch represents the model behind the search form about `app\models\Tmpejecucionfeedbacks`.
 */
class TmpejecucionfeedbacksSearch extends Tmpejecucionfeedbacks {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'tipofeedback_id', 'tmpejecucionformulario_id', 'usua_id', 'usua_id_lider', 'evaluado_id', 'snavisar', 'snaviso_revisado', 'nmescalamiento', 'basessatisfaccion_id'], 'integer'],
            [['created', 'dsaccion_correctiva', 'feaccion_correctiva', 'feescalamiento', 'dscausa_raiz', 'dscompromiso', 'dscomentario'], 'safe'],
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
        $query = Tmpejecucionfeedbacks::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'tipofeedback_id' => $this->tipofeedback_id,
            'tmpejecucionformulario_id' => $this->tmpejecucionformulario_id,
            'usua_id' => $this->usua_id,
            'created' => $this->created,
            'usua_id_lider' => $this->usua_id_lider,
            'evaluado_id' => $this->evaluado_id,
            'snavisar' => $this->snavisar,
            'snaviso_revisado' => $this->snaviso_revisado,
            'feaccion_correctiva' => $this->feaccion_correctiva,
            'nmescalamiento' => $this->nmescalamiento,
            'feescalamiento' => $this->feescalamiento,
            'basessatisfaccion_id'=> $this->basessatisfaccion_id,
        ]);

        $query->andFilterWhere(['like', 'dsaccion_correctiva', $this->dsaccion_correctiva])
                ->andFilterWhere(['like', 'dscausa_raiz', $this->dscausa_raiz])
                ->andFilterWhere(['like', 'dscompromiso', $this->dscompromiso])
                ->andFilterWhere(['like', 'dscomentario', $this->dscomentario]);

        return $dataProvider;
    }

}
