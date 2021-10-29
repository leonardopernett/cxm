<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ErroresSatu;

/**
 * ErroresSatuSearch represents the model behind the search form about `app\models\ErroresSatu`.
 */
class ErroresSatuSearch extends ErroresSatu {

    public $startDateCreated;
    public $endDateCreated;
    public $startDateSatu;
    public $endDateSatu;
    public $rango_fecha;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id'], 'integer'],
            [['created', 'rango_fecha', 'fecha_satu', 'datos', 'error', 'startDateCreated', 'endDateCreated', 'startDateSatu', 'endDateSatu'], 'safe'],
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
        $query = ErroresSatu::find();

        $dataProvider = new ActiveDataProvider([
            'pagination' => array(
                'pageSize' => 1000,
            ),
            'query' => $query,
        ]);

        $query->orderBy("id DESC");

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);
        if (isset($this->created) && !empty($this->created)) {
            list($this->startDateCreated, $this->endDateCreated) = explode(' - ', $this->created);
            $query->andFilterWhere(['>=', 'created', $this->startDateCreated . ' 00:00:01']);
            $query->andFilterWhere(['<=', 'created', $this->endDateCreated . ' 23:59:59']);
        }
        if (isset($this->fecha_satu) && !empty($this->fecha_satu)) {
            list($this->startDateSatu, $this->endDateSatu) = explode(' - ', $this->fecha_satu);
            $query->andFilterWhere(['>=', 'created', $this->startDateSatu . ' 00:00:01']);
            $query->andFilterWhere(['<=', 'created', $this->endDateSatu . ' 23:59:59']);
        }
        $query->andFilterWhere(['like', 'datos', $this->datos])
                ->andFilterWhere(['like', 'error', $this->error]);

        return $dataProvider;
    }

    /**
     * Funcion que retornar el objeto ActiveDataProvider sin paginacion para poder exportar los datos
     * @param type $params
     * @return ActiveDataProvider
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     */
    public function searchExport($params) {
        $query = ErroresSatu::find();


        if (!($this->load($params) && $this->validate())) {
            return false;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);
        if (isset($this->created) && !empty($this->created)) {
            list($this->startDateCreated, $this->endDateCreated) = explode(' - ', $this->created);
            $query->andFilterWhere(['>=', 'created', $this->startDateCreated . ' 00:00:01']);
            $query->andFilterWhere(['<=', 'created', $this->endDateCreated . ' 23:59:59']);
        }
        if (isset($this->fecha_satu) && !empty($this->fecha_satu)) {
            list($this->startDateSatu, $this->endDateSatu) = explode(' - ', $this->fecha_satu);
            $query->andFilterWhere(['>=', 'created', $this->startDateSatu . ' 00:00:01']);
            $query->andFilterWhere(['<=', 'created', $this->endDateSatu . ' 23:59:59']);
        }
        $query->andFilterWhere(['like', 'datos', $this->datos])
                ->andFilterWhere(['like', 'error', $this->error]);


        $dataProvider = $query->all();

        return $dataProvider;
    }

}
