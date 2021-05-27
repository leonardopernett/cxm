<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Logeventsadmin;

/**
 * logeventsadminSearch represents the model behind the search form about `app\models\Logeventsadmin`.
 */
class LogeventsadminSearch extends Logeventsadmin {

    public $startDate;
    public $endDate;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id_log', 'id_usuario_modificacion'], 'integer'],
            [['tabla_modificada', 'datos_ant', 'datos_nuevos', 'fecha_modificacion', 'usuario_modificacion'], 'safe'],
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
        $query = Logeventsadmin::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 3,
            ],
        ]);
        
        $query->orderBy("id_log DESC");

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id_log' => $this->id_log,
            'id_usuario_modificacion' => $this->id_usuario_modificacion,
        ]);

        $query->andFilterWhere(['like', 'tabla_modificada', $this->tabla_modificada])
                ->andFilterWhere(['like', 'datos_ant', $this->datos_ant])
                ->andFilterWhere(['like', 'datos_nuevos', $this->datos_nuevos])
                ->andFilterWhere(['like', 'usuario_modificacion', $this->usuario_modificacion]);
        
        if ($this->fecha_modificacion != '') {
            $dates = explode(' - ', $this->fecha_modificacion);
            $this->startDate = $dates[0] . ' 00:00:01';
            $this->endDate = $dates[1] . ' 23:59:59';           
            $query->andWhere("fecha_modificacion BETWEEN '$this->startDate' AND '$this->endDate'");
        }
        return $dataProvider;
    }

    /**
     * Funcion que retornar el objeto ActiveDataProvider sin paginacion para poder exportar los datos
     * @param type $params
     * @return ActiveDataProvider
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     */
    public function searchExport($params) {
        $query = Logeventsadmin::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return false;
        }

        $query->andFilterWhere([
            'id_log' => $this->id_log,
            'id_usuario_modificacion' => $this->id_usuario_modificacion,
        ]);

        $query->andFilterWhere(['like', 'tabla_modificada', $this->tabla_modificada])
                ->andFilterWhere(['like', 'datos_ant', $this->datos_ant])
                ->andFilterWhere(['like', 'datos_nuevos', $this->datos_nuevos]);

        if ($this->fecha_modificacion != '') {
            $dates = explode(' - ', $this->fecha_modificacion);
            $this->startDate = $dates[0] . ' 00:00:01';
            $this->endDate = $dates[1] . ' 23:59:59';           
            $query->andWhere("fecha_modificacion BETWEEN '$this->startDate' AND '$this->endDate'");
        }
        $dataProvider = $query->all();
        return $dataProvider;
    }

}
