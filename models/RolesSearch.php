<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Roles;

/**
 * RolesSearch represents the model behind the search form about `app\models\Roles`.
 */
class RolesSearch extends Roles {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['role_id', 'per_cuadrodemando', 'per_estadisticaspersonas', 'per_hacermonitoreo', 'per_reportes', 'per_modificarmonitoreo', 'per_adminsistema', 'per_adminprocesos', 'per_editarequiposvalorados'], 'integer'],
            [['role_nombre', 'role_descripcion'], 'safe'],
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
        $query = Roles::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'role_id' => $this->role_id,
            'per_cuadrodemando' => $this->per_cuadrodemando,
            'per_estadisticaspersonas' => $this->per_estadisticaspersonas,
            'per_hacermonitoreo' => $this->per_hacermonitoreo,
            'per_reportes' => $this->per_reportes,
            'per_modificarmonitoreo' => $this->per_modificarmonitoreo,
            'per_adminsistema' => $this->per_adminsistema,
            'per_adminprocesos' => $this->per_adminprocesos,
            'per_editarequiposvalorados' => $this->per_editarequiposvalorados,
        ]);

        $query->andFilterWhere(['like', 'role_nombre', $this->role_nombre])
                ->andFilterWhere(['like', 'role_descripcion', $this->role_descripcion]);

        return $dataProvider;
    }
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchExport($params) {
        $query = Roles::find();

        

        if (!($this->load($params) && $this->validate())) {
            return false;
        }

        $query->andFilterWhere([
            'role_id' => $this->role_id,
        ]);

        $query->andFilterWhere(['like', 'role_nombre', $this->role_nombre])
                ->andFilterWhere(['like', 'role_descripcion', $this->role_descripcion]);
        $model = $query->all();

        return $model;
    }
}
