<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_plan_gptw".
 *
 * @property integer $id_gptw
 * @property integer $id_operacion
 * @property integer $id_area_apoyo
 * @property string $id_pilares
 * @property integer $porcentaje_actual
 * @property integer $porcentaje_meta
 * @property string $acciones
 * @property string $fecha_registro
 * @property string $fecha_avance
 * @property string $fecha_cierre
 * @property string $observaciones
 * @property integer $responsable_area
 * @property string $estado
 * @property string $fechacreacion
 * @property integer $anulado
 * @property integer $usua_id
 */
class Plangptw extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_plan_gptw';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_operacion'], 'integer'],
            [['id_area_apoyo', 'usua_id','porcentaje_actual','porcentaje_meta','responsable_area','anulado','usua_id'], 'integer'],
            [['fecha_registro','fecha_avance','fecha_cierre','fechacreacion'], 'safe'],
            [['acciones','observaciones'], 'string', 'max' => 1000],
            [['estado','id_pilares'], 'string', 'max' => 1000]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_operacion' => Yii::t('app', 'Id_Opera'),            
            'id_area_apoyo' => Yii::t('app', 'Id_Area'),
            'porcentaje_actual' => Yii::t('app', 'Porcentaje_actual'),
            'porcentaje_meta' => Yii::t('app', 'Porcentaje_meta'),
            'responsable_area' => Yii::t('app', 'Responsable_area'),
            'acciones' => Yii::t('app', 'acciones'),
            'observaciones' => Yii::t('app', 'observaciones'),            
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
    
    /**
     * Array de estados 
     * 
     * @return array
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function arrayEstado(){
        return [
            1=>'SI', 0=>'NO'
        ];
    }
    
    /**
     * Retorna el estado
     * 
     * @param int $idEstado Estado
     * 
     * @return string
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getEstado($idEstado){
        $arrayEstado = $this->arrayEstado();
        return  $arrayEstado[$idEstado];
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
}
