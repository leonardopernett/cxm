<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\base\Exception;

/**
 * This is the model class for table "tbl_arbols".
 *
 * @property integer $id
 * @property string $name
 * @property integer $arbol_id
 * @property integer $snhoja
 * @property integer $formulario_id
 * @property double $nmfactor_proceso
 * @property double $nmumbral_verde
 * @property double $nmumbral_amarillo
 * @property double $nmumbral_positivo
 * @property integer $usua_id_responsable
 * @property string $dsorden
 * @property integer $tableroproblema_id
 * @property integer $tiposllamada_id
 * @property string $dsname_full
 * @property integer $bloquedetalle_id
 * @property integer $snactivar_problemas
 * @property integer $snactivar_tipo_llamada
 *
 * @property Arboles $arbol
 * @property Arboles[] $arboles
 * @property TblFormularios $formulario
 * @property TblTableroproblemas $tableroproblema
 * @property TblTiposllamadas $tiposllamada
 * @property TblArbolsEquipos[] $tblArbolsEquipos
 * @property TblArbolsEvaluadores[] $tblArbolsEvaluadores
 * @property TblArbolsUsuarios[] $tblArbolsUsuarios
 * @property TblEjecucionformularios[] $tblEjecucionformularios
 * @property TblEstadisticasarbols[] $tblEstadisticasarbols
 * @property TblPlanmonitoreos[] $tblPlanmonitoreos
 */
class Arboles extends \yii\db\ActiveRecord {

    public $arbolPadreName;
    public $equipos;
    public $responsables;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_arbols';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            //[['name', 'arbol_id', 'formulario_id', 'usua_id_responsable', 'tableroproblema_id', 'tiposllamada_id', 'bloquedetalle_id', 'nmfactor_proceso', 'nmumbral_verde', 'nmumbral_amarillo', 'nmumbral_positivo', 'dsorden', 'dsname_full'], 'required'],
            [['name', 'arbol_id', 'responsables'], 'required', 'on' => 'create'],
            [['name', 'arbol_id', 'responsables'], 'required', 'on' => 'update'],
            [['arbol_id'], 'required', 'on' => 'monitoreo'],
            [['name', 'arbol_id', 'responsables', 'formulario_id', 'equipos'], 'required', 'on' => 'checkHoja'],
            [['arbol_id', 'snhoja', 'formulario_id', 'usua_id_responsable', 'tableroproblema_id', 'tiposllamada_id', 'bloquedetalle_id', 'snactivar_problemas', 'snactivar_tipo_llamada', 'activo'], 'integer'],
            [['nmfactor_proceso', 'nmumbral_verde', 'nmumbral_amarillo', 'nmumbral_positivo'], 'number'],
            [['name', 'dsorden'], 'string', 'max' => 100],
            [['dsname_full'], 'string', 'max' => 2000],
            //[['name'], 'match', 'not' => true, 'pattern' => '/[^a-zA-Z0-9\s()_-]/'],
            [['equipos'], 'safe'],
            [['name', 'dsorden','dsname_full'],'filter', 'filter' => function($value){
                return filter_var($value,FILTER_SANITIZE_STRING) ;
             }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'arbol_id' => Yii::t('app', 'Arbol ID'),
            'arbolPadreName' => Yii::t('app', 'Arbol ID'),
            'snhoja' => Yii::t('app', 'Snhoja'),
            'formulario_id' => Yii::t('app', 'Formulario ID'),
            'nmfactor_proceso' => Yii::t('app', 'Nmfactor Proceso'),
            'nmumbral_verde' => Yii::t('app', 'Nmumbral Verde'),
            'nmumbral_amarillo' => Yii::t('app', 'Nmumbral Amarillo'),
            'nmumbral_positivo' => Yii::t('app', 'Nmumbral Positivo'),
            'usua_id_responsable' => Yii::t('app', 'Usua Id Responsable'),
            'dsorden' => Yii::t('app', 'Dsorden'),
            'tableroproblema_id' => Yii::t('app', 'Tableroproblema ID'),
            'tiposllamada_id' => Yii::t('app', 'Tiposllamada ID'),
            'dsname_full' => Yii::t('app', 'Dsname Full'),
            'bloquedetalle_id' => Yii::t('app', 'Pregunta PEC RACK'),
            'snactivar_problemas' => Yii::t('app', 'Snactivar Problemas'),
            'snactivar_tipo_llamada' => Yii::t('app', 'Snactivar Tipo Llamada'),
            'activo' => Yii::t('app', 'Activo'),
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
    public function getArboles() {
        return $this->hasMany(Arboles::className(), ['arbol_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFormulario() {
        return $this->hasOne(Formularios::className(), ['id' => 'formulario_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTableroproblema() {
        return $this->hasOne(Tableroproblemas::className(), ['id' => 'tableroproblema_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTiposllamada() {
        return $this->hasOne(Tiposllamadas::className(), ['id' => 'tiposllamada_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArbolsEquipos() {
        return $this->hasMany(ArbolsEquipos::className(), ['arbol_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArbolsEvaluadores() {
        return $this->hasMany(ArbolsEvaluadores::className(), ['arbol_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArbolsUsuarios() {
        return $this->hasMany(ArbolsUsuarios::className(), ['arbol_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEjecucionformularios() {
        return $this->hasMany(Ejecucionformularios::className(), ['arbol_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEstadisticasarbols() {
        return $this->hasMany(Estadisticasarbols::className(), ['arbol_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlanmonitoreos() {
        return $this->hasMany(Planmonitoreos::className(), ['arbol_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermisosGruposArbols() {
        return $this->hasMany(PermisosGruposArbols::className(), ['arbol_id' => 'id']);
    }

    /**
     * Metodo que permite obtener el listado de usuarios  
     * 
     * @return array
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getAllUsuarios() {
        try {
            $sql = 'SELECT u.usua_nombre AS value, u.usua_nombre AS label, u.usua_id AS id FROM tbl_usuarios u';
            return \Yii::$app->db->createCommand($sql)->queryAll();
        } catch (Exception $exc) {
            \Yii::error($exc->getMessage(), 'exception');
        }
    }

    /**
     * Metodo que permite obtener el listado de arboles  
     * 
     * @return array
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getPreguntasByArbol($arbol_id) {
        try {
            $sql = 'SELECT  bd.`id` AS value, CONCAT( SUBSTRING(s.`name`,1,15) ,">",  SUBSTRING(b.`name`,1,15), ">", SUBSTRING(bd.`name`,1,50)) AS label  
            FROM tbl_arbols a INNER JOIN `tbl_seccions` s On s.`formulario_id` = a.`formulario_id` INNER JOIN `tbl_bloques` b ON b.`seccion_id` = s.`id`
            INNER JOIN `tbl_bloquedetalles` bd ON bd.`bloque_id` = b.`id` 
            WHERE a.id =' . $arbol_id;
            $preguntas1 = \Yii::$app->db->createCommand($sql)->queryAll();

            return ArrayHelper::map($preguntas1, 'value', 'label');
        } catch (Exception $exc) {
            \Yii::error($exc->getMessage(), 'exception');
        }
    }

    /**
     * 
     * @return array
     * @author Felipe echeverri <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getArbolesByRoles() {

        $grupo = Yii::$app->user->identity->grupousuarioid;
        return ArrayHelper::map(Arboles::find()
                                ->joinWith('permisosGruposArbols')
                                ->where([
                                    "sncrear_formulario" => 1,
                                    "snhoja" => 1,
                                    "grupousuario_id" => $grupo])
                                ->andWhere(['not', ['formulario_id' => null]])
                                ->orderBy("dsorden ASC")
                                ->all(), 'id', 'dsname_full');
    }

    /**
     * Retorna el listado de arboles padre
     * 
     * @return array
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getArbolPadreList() {
        return ArrayHelper::map(Arboles::find()
                                ->where(["snhoja" => 0])
                                ->orderBy("name ASC")
                                ->all(), 'id', 'name');
    }

    /**
     * Retorna el listado de formularios
     * 
     * @return array
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getFormularioList() {
        return ArrayHelper::map(Formularios::find()
                                ->orderBy("name ASC")
                                ->all(), 'id', 'name');
    }

    /**
     * Obtiene el listados de los responsables asociados
     * a un arbol
     * 
     * @param int $id Id Arbol
     * 
     * @return array
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getIdsResponsables($id) {
        return ArrayHelper::map(ArbolsUsuarios::find()
                                ->where(['arbol_id' => $id])
                                ->all(), 'usua_id', 'usua_id');
    }

    /**
     * Obtiene el listados de los equipos asociados
     * a un arbol
     * 
     * @param int $id Id Arbol
     * 
     * @return array
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getIdsEquipos($id) {
        return ArrayHelper::map(ArbolsEquipos::find()
                                ->where(['arbol_id' => $id])
                                ->all(), 'equipo_id', 'equipo_id');
    }

    /**
     * Obtiene el listado de problemas 
     * 
     * @return array
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getProblemasList() {
        return ArrayHelper::map(Tableroproblemas::find()->orderBy('name')
                                ->all(), 'id', 'name');
    }

    /**
     * Obtiene el listado de tipos llamadas
     * 
     * @return array
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getTiposLlamadasList() {
        return ArrayHelper::map(Tiposllamadas::find()->orderBy('name')
                                ->all(), 'id', 'name');
    }

    /**
     * Metodo que ejecuta el procedimiento almacenado de reorden
     * 
     * @return boolean
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function reordenar() {
        try {
            $sql = 'CALL sp_arbols_actualiza_orden()';
            $command = \Yii::$app->db->createCommand($sql);
            $command->execute();
            return true;
        } catch (Exception $exc) {
            \Yii::error($exc->getMessage(), 'exception');
            return false;
        }
    }

    /**
     * Metodo para asignar los grupos al arbol
     * 
     * @param int   $arbolId     Id del arbol
     * @param array $resposables Array de responsables
     * 
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function asignarGrupos($arbolId, $resposables) {
        if (!empty($arbolId) && is_numeric($arbolId) && is_array($resposables) && count($resposables) > 0) {
            $beforeGrupos = ArrayHelper::map(PermisosGruposArbols::find()->where(
                                    ['arbol_id' => $arbolId])->asArray()->all(), 'id', 'grupousuario_id');
            foreach ($resposables as $responsableId) {
                $grupos = RelGruposUsuarios::find()->where(
                                ['usuario_id' => $responsableId])->asArray()->one();
                $grupoId = $grupos['grupo_id'];
                if (!in_array($grupoId, $beforeGrupos)) {
                    $permisos = new PermisosGruposArbols;
                    $permisos->guardarPermiso($arbolId, $grupoId, 'sncrear_formulario', 1);
                }
            }
        }
    }

    /**
     * Metodo para las graficas del Dashboard
     * 
     * @param int $id Id del arbol
     * 
     * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public static function getRow_ByIDDashboard($id) {

        $return = new \stdClass();
        $return->arbol = Arboles::findOne($id);

        if (!empty($return->id) && isset($return->id) && $return->id > 0) {
            $return->equipos = Equipos::getAll_ByArbolID($return->id);
        }

        if (!empty($return->id) && isset($return->id) && $return->id > 0) {
            $return->usuarios = ArbolsUsuarios::getAll_ByArbolID($return->id);
        }
        return $return;
    }

    /**
     * Metodo para traer los permisos de graficar d eun usuarios
     * 
     * 
     * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public static function getArbolByPermisoGraficar() {
        return Arboles::find()
                        ->join("INNER JOIN", "tbl_permisos_grupos_arbols", "tbl_permisos_grupos_arbols.arbol_id = tbl_arbols.id")
                        ->where(["tbl_permisos_grupos_arbols.grupousuario_id" => "1", "snver_grafica" => "1"])
                        ->orderBy("dsorden")
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
                //NUEVO ATRIBUTOS
                $newattributes = $this->attributes;
                //VIEJOS ATRIBUTOS
                $oldattributes = $this->oldAttributes;
                //COMPARAR CAMPO A CAMPO
                foreach ($newattributes as $name => $value) {
                    if (!empty($oldattributes)) {
                        $old = $oldattributes[$name];
                    } else {
                        $old = '';
                    }
                    //SI HUBO UN CAMBIO EN UN CAMPO 
                    if ($value != $old) {
                        $log = new Logeventsadmin;
                        $log->datos_ant = $name . ': ' . $old;
                        $log->datos_nuevos = $name . ': ' . $value;
                        $log->tabla_modificada = $this->tableName();
                        $log->fecha_modificacion = date("Y-m-d H:i:s");
                        $log->usuario_modificacion = Yii::$app->user->identity->username;
                        $log->id_usuario_modificacion = Yii::$app->user->identity->id;
                        $log->save();
                    }
                }
            }
            return true;
        } else {
            return false;
        }
    }
    
    public static function getArbolByUser(){
        $sql = 'SELECT a.* FROM tbl_grupos_usuarios tgu '
                . 'INNER JOIN rel_grupos_usuarios rgu ON rgu.grupo_id = tgu.grupos_id '
                . 'INNER JOIN tbl_permisos_grupos_arbols pga ON tgu.grupos_id = pga.grupousuario_id '
                . ' INNER JOIN tbl_arbols a ON a.id = pga.arbol_id'
                . ' WHERE rgu.usuario_id =' . Yii::$app->user->identity->id . '  GROUP BY pga.arbol_id';
        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public function getActivos($opcion){
        $varIdPcrc = $opcion;

        $varActviado = Yii::$app->db->createCommand("select activo from tbl_arbols where id = '$varIdPcrc'")->queryScalar();

        $varActivo = "";
        if ($varActviado != 1) {
            $varActivo = 'Si';
        }else{
            $varActivo = 'No';
        }

        return $varActivo;
    }
}
