<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tbl_equipos".
 *
 * @property integer $id
 * @property string $name
 * @property double $nmumbral_verde
 * @property double $nmumbral_amarillo
 * @property integer $usua_id
 *
 * @property TblArbolsEquipos[] $tblArbolsEquipos
 * @property TblUsuarios $usua
 * @property TblEquiposEvaluados[] $tblEquiposEvaluados
 * @property TblEstadisticaequipos[] $tblEstadisticaequipos
 * @property TblEstadisticaevaluados[] $tblEstadisticaevaluados
 */
class Equipos extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_equipos';
    }

    public function init() {
        parent::init();
        $this->nmumbral_verde = 1;
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['nmumbral_verde', 'nmumbral_amarillo'], 'number'],
            [['usua_id', 'name', 'nmumbral_verde', 'nmumbral_amarillo'], 'required'],
            [['usua_id'], 'integer'],
            [['name'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'nmumbral_verde' => Yii::t('app', 'Nmumbral Verde'),
            'nmumbral_amarillo' => Yii::t('app', 'Nmumbral Amarillo'),
            'usua_id' => Yii::t('app', 'Usua_ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArbolsequipos() {
        return $this->hasMany(ArbolsEquipos::className(), ['equipo_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsua() {
        return $this->hasOne(Usuarios::className(), ['usua_id' => 'usua_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquiposevaluados() {
        return $this->hasMany(EquiposEvaluados::className(), ['equipo_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEstadisticaequipos() {
        return $this->hasMany(Estadisticaequipos::className(), ['equipo_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEstadisticaevaluados() {
        return $this->hasMany(Estadisticaevaluados::className(), ['equipo_id' => 'id']);
    }

    /**
     * Metodo que retorna el listado de los responsables
     * 
     * @return array
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getResponsableList() {
        return ArrayHelper::map(Usuarios::find()->select(['usua_id', 'nombre' => 'concat(usua_nombre, \' - \', usua_usuario)'])->orderBy('usua_nombre')->asArray()->all(), 'usua_id', 'nombre');
    }

    /**
     * Metodo que retorna el listado de todos los lideres de equipos
     * 
     * @return array
     * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public static function getLideresList($search) {
        return Equipos::find()
                        ->select(['id' => 'tbl_usuarios.usua_id', 'text' => 'UPPER(usua_nombre)'])
                        ->join('JOIN', 'tbl_usuarios', 'tbl_usuarios.usua_id = tbl_equipos.usua_id')
                        ->where('usua_nombre LIKE "%' . $search . '%"')
                        ->groupBy('id')
                        ->orderBy('usua_nombre')
                        ->asArray()
                        ->all();
    }

    /**
     * 
     * 
     * @return array
     * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public static function getEquipoLider($V_evaluado_id, $V_arbol_id) {
        $eq = \app\models\Equipos::find()
                ->joinWith('arbolsequipos')
                ->joinWith('equiposevaluados')
                ->select('tbl_equipos.id, tbl_equipos.usua_id')
                ->where('tbl_equipos_evaluados.evaluado_id = ' . $V_evaluado_id .
                        ' AND  tbl_arbols_equipos.arbol_id = ' . $V_arbol_id)
                ->all();
        if (empty($eq)) {
            return [];
        } else {
            return [
                "equipo_id" => $eq[0]->id,
                "lider" => $eq[0]->usua_id
            ];
        }
    }

    /**
     * Metodo para las graficas del Dashboard
     * 
     * @param int $arbol_id Id del arbol
     * @return array 
     * 
     * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public static function getAll_ByArbolID($arbol_id) {
        return Equipos::find()
                        ->select("tbl_equipos.id")
                        ->distinct()
                        ->join('INNER JOIN', 'tbl_arbols_equipos', 'tbl_arbols_equipos.equipo_id = tbl_equipos.id')
                        ->where(["tbl_arbols_equipos.arbol_id" => $arbol_id])
                        ->asArray()
                        ->all();
    }

    /**
     * 23/02/2016 -> Funcion que permite llevar un log o registro de los datos modificados
     * @param type $insert
     * @return boolean
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     */
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            if ($insert == false) {
                $modelLog = new Logeventsadmin();
                $modelLog->datos_nuevos = print_r($this->attributes, true);
                $modelLog->datos_ant = print_r($this->oldAttributes, true);
                $modelLog->fecha_modificacion = date("Y-m-d H:i:s");
                $modelLog->usuario_modificacion = Yii::$app->user->identity->username;
                $modelLog->id_usuario_modificacion = Yii::$app->user->identity->id;
                $modelLog->tabla_modificada = $this->tableName();
                $modelLog->save();
            }
            return true;
        } else {
            return false;
        }
    }

    //Automatizacion equipos Teo
    public function cleanEquipo($idequipo = 0, $nameEquipo = '', $iduser = 1 ){
        $model = \Yii::$app->db->createCommand("UPDATE tbl_equipos SET name = :nameEquipo, usua_id = :iduser WHERE id =:idequipo");
        $model->bindParam(':idequipo', $idequipo);
        $model->bindParam(':iduser', $iduser);
        $model->bindParam(':nameEquipo', $nameEquipo);
        $model->execute();
    }
    
    //Automatizacion equipos Teo
    public static function getEquiposidentificacionlider() 
    {
        return Equipos::find()
                        ->select("*")
                        ->from("tbl_equipos")
                        ->innerJoinWith('usua', 'usua_id = tbl_equipos.usua_id')
                        ->where("tbl_equipos.usua_id <> 1 and tbl_equipos.name <> 'vacío'")
                        ->asArray()
                        ->all();
    }
}
