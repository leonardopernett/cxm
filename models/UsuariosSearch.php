<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Usuarios;

/**
 * UsuariosSearch represents the model behind the search form about `app\models\Usuarios`.
 */
class UsuariosSearch extends Usuarios {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['usua_id'], 'integer'],
            [['usua_usuario', 'usua_nombre', 'usua_email', 'usua_identificacion', 'usua_activo', 'usua_estado', 'usua_fechhoratimeout', 'rol', 'grupo','nombregrupo'], 'safe'],
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
    public function search($params,$paginacion=null) {

        $query = Usuarios::find()
                ->leftJoin('rel_usuarios_roles', 'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                ->leftJoin('tbl_roles', 'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                ->with('relUsuariosRoles');
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination'=>['page'=>(isset($paginacion['page']))?($paginacion['page']-1):0]
        ]);
        
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'usua_id' => $this->usua_id,
            'usua_fechhoratimeout' => $this->usua_fechhoratimeout,
        ]);

        $query->andFilterWhere(['like', 'usua_usuario', $this->usua_usuario])
                ->andFilterWhere(['like', 'usua_nombre', $this->usua_nombre])
                ->andFilterWhere(['like', 'usua_email', $this->usua_email])
                ->andFilterWhere(['like', 'usua_identificacion', $this->usua_identificacion])
                ->andFilterWhere(['like', 'usua_activo', $this->usua_activo])
                ->andFilterWhere(['like', 'usua_estado', $this->usua_estado])
                ->andFilterWhere(['like', 'tbl_roles.role_descripcion', $this->rol]);

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
        $query->select('u.*,r.role_id, r.role_nombre, r.role_descripcion');
        $query->join('LEFT JOIN', 'rel_usuarios_roles ru', 'ru.rel_usua_id = u.usua_id');
        $query->join('LEFT JOIN', 'tbl_roles r', 'r.role_id = ru.rel_role_id');        

        if (!($this->load($params) && $this->validate())) {
            return false;
        }

        $query->andFilterWhere([
            'u.usua_id' => $this->usua_id,
            'u.usua_fechhoratimeout' => $this->usua_fechhoratimeout,
        ]);

        $query->andFilterWhere(['like', 'u.usua_usuario', $this->usua_usuario])
                ->andFilterWhere(['like', 'u.usua_nombre', $this->usua_nombre])
                ->andFilterWhere(['like', 'u.usua_email', $this->usua_email])
                ->andFilterWhere(['like', 'u.usua_identificacion', $this->usua_identificacion])
                ->andFilterWhere(['like', 'u.usua_activo', $this->usua_activo])
                ->andFilterWhere(['like', 'u.usua_estado', $this->usua_estado])
                ->andFilterWhere(['like', 'r.role_descripcion', $this->rol]);

        $dataProvider = $query->asArray()->all();

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
        
        
       
        $query = Usuarios::find();
        $query->select('u.*,gu.grupos_id AS grupo,gu.nombre_grupo AS nombregrupo');
        $query->from('tbl_usuarios u');
        $query->join('INNER JOIN', 'rel_grupos_usuarios rgu', 'rgu.usuario_id = u.usua_id');
        $query->join('INNER JOIN', 'tbl_grupos_usuarios gu', 'gu.grupos_id = rgu.grupo_id');
        $query->andWhere('gu.grupos_id = ' . $params);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        return $dataProvider;
    }
}
