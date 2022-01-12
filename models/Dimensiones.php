<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tbl_dimensions".
 *
 * @property integer $id
 * @property string $name
 *
 * @property TblArbolsEvaluadores[] $tblArbolsEvaluadores
 * @property TblEjecucionformularios[] $tblEjecucionformularios
 * @property TblEstadisticasarbols[] $tblEstadisticasarbols
 * @property TblPlanmonitoreos[] $tblPlanmonitoreos
 * @property TblTmpejecucionformularios[] $tblTmpejecucionformularios
 */
class Dimensiones extends \yii\db\ActiveRecord {

    public $dimension_id;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_dimensions';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return array(
            array('name', 'required'),
            array('dimension_id', 'required', 'on' => 'monitoreo'),
            /*array(
                'name',
                'match', 'not' => true, 'pattern' => '/[^a-zA-Z\s()_-]/',
            ),*/
            array('name', 'string', 'min' => 2, 'max' => 30),
            array('name','filter', 'filter' => function($value){
                return filter_var($value,FILTER_SANITIZE_STRING);}),
        );
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'dimension_id' => Yii::t('app', 'Seleccione dimension'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblArbolsEvaluadores() {
        return $this->hasMany(TblArbolsEvaluadores::className(), ['dimension_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblEjecucionformularios() {
        return $this->hasMany(TblEjecucionformularios::className(), ['dimension_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblEstadisticasarbols() {
        return $this->hasMany(TblEstadisticasarbols::className(), ['dimension_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblPlanmonitoreos() {
        return $this->hasMany(TblPlanmonitoreos::className(), ['dimension_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblTmpejecucionformularios() {
        return $this->hasMany(TblTmpejecucionformularios::className(), ['dimension_id' => 'id']);
    }

    /**
     * Metodo que retorna el listado de dimensiones
     * 
     * @return array
     * @author Daniel Enciso <daniel.enciso@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getDimensionsList() {
        return ArrayHelper::map(Dimensiones::find()->orderBy('name')->asArray()->all(), 'id', 'name');
    }
    
    public static function getDimensionsListForm() {
        return ArrayHelper::map(Dimensiones::find()->orderBy('name')->asArray()->all(), 'id', 'name');
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
