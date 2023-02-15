<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_areasapoyo_gptw".
 *
 * @property integer $id_areaapoyo
 * @property string $nombre
 * @property integer $anulado
 * @property string $fechacreacion
 * @property integer $usua_id
 */
class Areaapoyogptw extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_areasapoyo_gptw';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_areaapoyo'], 'required'],
            [['anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['nombre'], 'string', 'max' => 300],
            [['nombre'], 'filter', 'filter' => function($value){
                return filter_var($value,FILTER_SANITIZE_STRING) ;
             }]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_areaapoyo' => Yii::t('app', 'ID'),
            'nombre' => Yii::t('app', 'Nombre'),
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
