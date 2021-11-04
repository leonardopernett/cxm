<?php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "tbl_permisos_roles_arbols".
 *
 * @property integer $id
 * @property integer $arbol_id
 * @property integer $role_id
 * @property integer $sncrear_formulario
 * @property integer $snver_grafica
 */
class PermisosRolesArbols extends \yii\db\ActiveRecord {

    public $rolName;
    public $rolDescripcion;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_permisos_roles_arbols';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['arbol_id', 'role_id'], 'required'],
            [['arbol_id', 'role_id', 'rolName', 'rolDescripcion'], 'safe'],
            [['arbol_id', 'role_id', 'sncrear_formulario', 'snver_grafica'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'arbol_id' => Yii::t('app', 'Arbol ID'),
            'role_id' => Yii::t('app', 'Role ID'),
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
        return $this->hasOne(Roles::className(), ['role_id' => 'role_id']);
    }

    /**
     * Metodo para guardar los permisos de un arbol
     * 
     * @param int    $arbolId  Id del arbol
     * @param int    $roleId   Id del rol
     * @param string $permiso  atributo
     * @param int    $valor    valor del atributo
     * 
     * @return boolean
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function guardarPermiso($arbolId, $roleId, $permiso, $valor) {
        if (!empty($arbolId) && is_numeric($arbolId) && !empty($roleId) && is_numeric($roleId) && !empty($permiso) && !empty($valor)) {
            try {                
                $model = new $this;
                $model->arbol_id = $arbolId;
                $model->role_id = $roleId;
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
        $query = PermisosRolesArbols::find();
        $query->joinWith(['role']);

        $dataProvider->sort->attributes['rolName'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['tbl_roles.role_nombre' => SORT_ASC],
            'desc' => ['tbl_roles.role_nombre' => SORT_DESC],
        ];

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        
        if (!($this->load($params))) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'tbl_permisos_roles_arbols.id' => $this->id,
            'tbl_permisos_roles_arbols.arbol_id' => $this->arbol_id,
            'tbl_permisos_roles_arbols.role_id' => $this->role_id,
            'tbl_permisos_roles_arbols.sncrear_formulario' => $this->sncrear_formulario,
            'tbl_permisos_roles_arbols.snver_grafica' => $this->snver_grafica,
        ]);

        $query->andFilterWhere(['like', 'tbl_roles.role_nombre', $this->rolName])
                ->andFilterWhere(['like', 'tbl_roles.role_descripcion', $this->rolDescripcion]);

        return $dataProvider;
    }

}
