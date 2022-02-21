<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Gruposusuarios;

/**
 * GruposusuariosSearch represents the model behind the search form about `app\models\Gruposusuarios`.
 */
class GruposusuariosSearch extends Gruposusuarios
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['grupos_id', 'per_realizar_valoracion'], 'integer'],
            [['nombre_grupo','usuario','usuarioname','grupo_descripcion'], 'safe'],
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
    public function search($params,$paginacion = null)
    {
        $query = Gruposusuarios::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination'=>['page'=>(isset($paginacion['page']))?($paginacion['page']-1):0,'pageSize' =>50]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'grupos_id' => $this->grupos_id,
            'per_realizar_valoracion' => $this->per_realizar_valoracion,
        ]);

        $query->andFilterWhere(['like', 'nombre_grupo', $this->nombre_grupo]);
        $query->andFilterWhere(['like', 'grupo_descripcion', $this->grupo_descripcion]);

        return $dataProvider;
    }
    
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchAjax($params) {
        
        
       
        $query = Gruposusuarios::find();
        $query->select('gu.*,u.usua_id AS usuario,u.usua_nombre AS usuarioname');
        $query->from('tbl_usuarios u');
        $query->join('INNER JOIN', 'rel_grupos_usuarios rgu', 'rgu.usuario_id = u.usua_id');
        $query->join('INNER JOIN', 'tbl_grupos_usuarios gu', 'gu.grupos_id = rgu.grupo_id');
        $query->andWhere('u.usua_id = ' . $params);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
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
        $query = Usuarios::find();
        $query->from('tbl_usuarios u');
        $query->select('u.*, r.role_nombre, r.role_descripcion,gu.nombre_grupo,gu.grupo_descripcion,gu.per_realizar_valoracion');
        $query->join('LEFT JOIN', 'rel_usuarios_roles ru', 'ru.rel_usua_id = u.usua_id');
        $query->join('LEFT JOIN', 'tbl_roles r', 'r.role_id = ru.rel_role_id'); 
        $query->join('LEFT JOIN', 'rel_grupos_usuarios rgu', 'rgu.usuario_id = u.usua_id'); 
        $query->join('RIGHT JOIN', 'tbl_grupos_usuarios gu', 'gu.grupos_id = rgu.grupo_id'); 

        if (!($this->load($params) && $this->validate())) {
            return false;
        }

        $query->andFilterWhere([
            'gu.grupos_id' => $this->grupos_id,
        ]);

        $query->andFilterWhere(['like', 'gu.nombre_grupo', $this->nombre_grupo]);
        $query->andFilterWhere(['like', 'gu.grupo_descripcion', $this->grupo_descripcion]);

        $dataProvider = $query->asArray()->all();

        return $dataProvider;
    }
}
