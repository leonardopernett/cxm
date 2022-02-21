<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\DeclinacionesUsuarios;

/**
 * DeclinacionesUsuariosSearch represents the model behind the search form about `app\models\DeclinacionesUsuarios`.
 */
class DeclinacionesUsuariosSearch extends DeclinacionesUsuarios {

    public $startDate;
    public $endDate;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id'/* , 'usua_id', 'declinacion_id', 'arbol_id', 'dimension_id', 'evaluado_id' */], 'integer'],
            [['fecha'], 'required'],
            [['usua_id', 'fecha', 'dimension_id', 'evaluado_id', 'arbol_id', 'rol', 'declinacion_id'], 'safe'],
            [['fecha'], 'required', 'on' => 'declinacion']
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
    public function search($params, $limit = true) {
        $query = DeclinacionesUsuarios::find();
        $query->from('tbl_declinaciones_usuarios du');
        $query->join('LEFT JOIN', 'tbl_usuarios u', 'u.usua_id = du.usua_id');
        $query->join('LEFT JOIN', 'rel_usuarios_roles ur', 'u.usua_id = ur.rel_usua_id');

        $query->andFilterWhere([
            'du.id' => $this->id,
            'du.declinacion_id' => $this->declinacion_id,
            'du.dimension_id' => $this->dimension_id,
        ]);
        $sql = 'SELECT tgu.*,pga.*,rgu.* FROM tbl_grupos_usuarios tgu '
                . 'INNER JOIN rel_grupos_usuarios rgu ON rgu.grupo_id = tgu.grupos_id '
                . 'INNER JOIN tbl_permisos_grupos_arbols pga ON tgu.grupos_id = pga.grupousuario_id '
                . ' INNER JOIN tbl_arbols a ON a.id = pga.arbol_id'
                . ' WHERE rgu.usuario_id =' . Yii::$app->user->identity->id . '  GROUP BY pga.arbol_id';
        $queryGrupos = \Yii::$app->db->createCommand($sql)->queryAll();
        foreach ($queryGrupos as $value) {
            $idArbolesPermiso[] = $value['arbol_id'];
        }
        $cadenaIdarboles = implode(',', $idArbolesPermiso);
        $query->andWhere('arbol_id IN (' . $cadenaIdarboles . ')');
        $query->andFilterWhere(['like', 'du.url', $this->url])
                ->andFilterWhere(['like', 'du.comentario', $this->comentario]);
        $query->andFilterWhere(['between', 'DATE(du.fecha)',
            $this->startDate, $this->endDate]);
        /*
         * 14/03/2016->Modificacion para busqueda de mutiples arboles y valorados
         */
        if ($this->usua_id != '') {
            $query->andWhere("du.usua_id IN(" . $this->usua_id . ")");
        }
        if ($this->arbol_id != '') {
            $query->andWhere("du.arbol_id IN(" . $this->arbol_id . ")");
        }
        if ($this->evaluado_id != '') {
            $query->andWhere("du.evaluado_id IN(" . $this->evaluado_id . ")");
        }
        if ($this->rol != '') {
            $query->andWhere("ur.rel_role_id IN(" . $this->rol . ")");
        }
        $count = $query->all();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'totalCount' => count($count),
            'pagination' => ['pageSize' => ($limit)?20:  count($count)],
        ]);
        /*
         * Fin de cambio
         */
        return $dataProvider;
    }

}
