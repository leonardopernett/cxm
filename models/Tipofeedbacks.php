<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tbl_tipofeedbacks".
 *
 * @property integer $id
 * @property integer $categoriafeedback_id
 * @property string $name
 * @property integer $snaccion_correctiva
 * @property integer $sncausa_raiz
 * @property integer $sncompromiso
 * @property integer $cdtipo_automatico
 * @property string $dsmensaje_auto
 *
 * @property TblEjecucionfeedbacks[] $Ejecucionfeedbacks
 * @property TblCategoriafeedbacks $categoriafeedback
 * @property TblTmpejecucionfeedbacks[] $Tmpejecucionfeedbacks
 */
class Tipofeedbacks extends \yii\db\ActiveRecord {

    public $categoriaFeedName;
    
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_tipofeedbacks';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name', 'dsmensaje_auto'], 'required'],
            [['categoriafeedback_id', 'snaccion_correctiva', 'sncausa_raiz', 'sncompromiso', 'cdtipo_automatico'], 'integer'],
            [['name', 'dsmensaje_auto'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'categoriafeedback_id' => Yii::t('app', 'Categoriafeedback ID'),
            'categoriaFeedName' => Yii::t('app', 'Categoriafeedback ID'),
            'name' => Yii::t('app', 'Name'),
            'snaccion_correctiva' => Yii::t('app', 'Snaccion Correctiva'),
            'sncausa_raiz' => Yii::t('app', 'Sncausa Raiz'),
            'sncompromiso' => Yii::t('app', 'Sncompromiso'),
            'cdtipo_automatico' => Yii::t('app', 'Cdtipo Automatico'),
            'dsmensaje_auto' => Yii::t('app', 'Dsmensaje Auto'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEjecucionfeedbacks() {
        return $this->hasMany(Ejecucionfeedbacks::className(), ['tipofeedback_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoriafeedback() {
        return $this->hasOne(Categoriafeedbacks::className(), ['id' => 'categoriafeedback_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTmpejecucionfeedbacks() {
        return $this->hasMany(Tmpejecucionfeedbacks::className(), ['tipofeedback_id' => 'id']);
    }

    /**
     * Metodo que retorna el listado de tipos de feedback
     * 
     * @return array
     * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public static function getTipofeedbacksList() {
        return ArrayHelper::map(Tipofeedbacks::find()->orderBy('name')->asArray()->all(), 'id', 'name');
    }

    /**
     * Metodo que retorna el listado de tipos de feedback por id de categoria
     * 
     * @return array
     * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public static function getTipofeedbacksListByID($id) {
        return Tipofeedbacks::find()
                        ->select("id, name")
                        ->where("categoriafeedback_id = " . $id)
                        ->orderBy('name')
                        ->asArray()
                        ->all();
    }
    
    /**
     * Convierte el booleano en string
     * 
     * @param type $boolean
     * @return string
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getStringBoolean($boolean) {
        if ($boolean == 1) {
            return 'SI';
        } else {
            return 'NO';
        }
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
