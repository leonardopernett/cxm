<?php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "tbl_permisos_grupos_arbols".
 * 
 * (18-03-2016) Se realiza el ajuste en el cual la tabla de permisos ya no apunta a roles
 * si no que esta asociada a grupos de usuario (sebastian.orozco@ingeneo.com.co)
 * 
 * @property integer $id
 * @property integer $arbol_id
 * @property integer $grupousuario_id
 * @property integer $sncrear_formulario
 * @property integer $snver_grafica
 */
class PermisosGruposArbols extends \yii\db\ActiveRecord {

    public $rolName;
    public $rolDescripcion;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_permisos_grupos_arbols';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['arbol_id', 'grupousuario_id'], 'required'],
            [['arbol_id', 'grupousuario_id', 'rolName', 'rolDescripcion'], 'safe'],
            [['arbol_id', 'grupousuario_id', 'sncrear_formulario', 'snver_grafica'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'arbol_id' => Yii::t('app', 'Arbol ID'),
            'grupousuario_id' => Yii::t('app', 'Grupo Usuario ID'),
            'rolName' => Yii::t('app', 'Role Name'),
            'rolDescripcion' => Yii::t('app', 'Descripcion Role'),
            'sncrear_formulario' => Yii::t('app', 'Sncrear Formulario'),
            'snver_grafica' => Yii::t('app', 'Snver Grafica'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArbol() {
        return $this->hasOne(Arboles::className(), ['id' => 'arbol_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole() {
        return $this->hasOne(Gruposusuarios::className(), ['grupos_id' => 'grupousuario_id']);
    }

    /**
     * Metodo para guardar los permisos de un arbol
     * 
     * @param int    $arbolId  Id del arbol
     * @param int    $grupousuario_id   Id del grupo usuario
     * @param string $permiso  atributo
     * @param int    $valor    valor del atributo
     * 
     * @return boolean
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     * Modificado 28-03-2016 Por sebastian orozco <sebastian.orozco@ingeneo.com.co>
     */
    public function guardarPermiso($arbolId, $grupousuario_id, $permiso, $valor) {
        if (!empty($arbolId) && is_numeric($arbolId) && !empty($grupousuario_id) && is_numeric($grupousuario_id) && !empty($permiso) && !empty($valor)) {
            try {
                $model = new $this;
                $model->arbol_id = $arbolId;
                $model->grupousuario_id = $grupousuario_id;
                $model->$permiso = $valor;
                $model->save();
                return true;
            } catch (Exception $exc) {
                \Yii::error($exc->getMessage(), 'exception');
                return false;
            }
        }
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params) {
        $query = PermisosGruposArbols::find();
        $query->joinWith(['role']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);


        if (!($this->load($params))) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'tbl_permisos_grupos_arbols.id' => $this->id,
            'tbl_permisos_grupos_arbols.arbol_id' => $this->arbol_id,
            'tbl_permisos_grupos_arbols.grupousuario_id' => $this->grupousuario_id,
            'tbl_permisos_grupos_arbols.sncrear_formulario' => $this->sncrear_formulario,
            'tbl_permisos_grupos_arbols.snver_grafica' => $this->snver_grafica,
        ]);

        $query->andFilterWhere(['like', 'tbl_grupos_usuarios.nombre_grupo', $this->rolName])
                ->andFilterWhere(['like', 'tbl_grupos_usuarios.grupo_descripcion', $this->rolDescripcion]);

        return $dataProvider;
    }

}
