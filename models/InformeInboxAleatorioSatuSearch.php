<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\InformeInboxAleatorio;

/**
 * InformeInboxAleatorioSatuSearch represents the model behind the search form about `app\models\InformeInboxAleatorio`.
 */
class InformeInboxAleatorioSatuSearch extends InformeInboxAleatorio {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id'], 'integer'],
            [['pcrc', 'encu_diarias_pcrc', 'encu_diarias_totales', 'encu_mes_pcrc', 'encu_mes_totales', 'faltaron', 'disponibles', 'estado', 'fecha_creacion'], 'safe'],
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
        $query = InformeInboxAleatorio::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
        ]);
        
        $query->orderBy("id DESC");

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'fecha_creacion' => $this->fecha_creacion,
        ]);

        $query->andFilterWhere(['like', 'pcrc', $this->pcrc])
                ->andFilterWhere(['like', 'encu_diarias_pcrc', $this->encu_diarias_pcrc])
                ->andFilterWhere(['like', 'encu_diarias_totales', $this->encu_diarias_totales])
                ->andFilterWhere(['like', 'encu_mes_pcrc', $this->encu_mes_pcrc])
                ->andFilterWhere(['like', 'encu_mes_totales', $this->encu_mes_totales])
                ->andFilterWhere(['like', 'faltaron', $this->faltaron])
                ->andFilterWhere(['like', 'disponibles', $this->disponibles])
                ->andFilterWhere(['like', 'estado', $this->estado]);

        return $dataProvider;
    }
    
     /**
     * Metodo para filtrar el Inbox
     * @return ActiveDataProvider
     */
    public function searchInforme() {
        
        
        $query = InformeInboxAleatorio::find();
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
        ]);
        
        $query->andFilterWhere([
            'estado' => $this->estado,            
        ]);
        
        $query->andFilterWhere(['like', 'pcrc', $this->pcrc]);
        
        if (!empty($this->fecha_creacion)) {
            $dates = explode(' - ', $this->fecha_creacion);
            $startDate = $dates[0] . " 00:00:00";
            $endDate = $dates[1] . " 23:59:59";            
            $query->andFilterWhere(['between', 'fecha_creacion',
            $startDate, $endDate]);
        }

        $query->orderBy("id DESC");

        return $dataProvider;
    }

}
