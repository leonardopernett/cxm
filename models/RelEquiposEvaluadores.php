<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tbl_equipos_evaluados".
 *
 * @property integer $id
 * @property integer $evaluadores_id
 * @property integer $equipo_id
 *
 * @property TblEquipos $equipo
 * @property TblEvaluados $evaluado
 */
class RelEquiposEvaluadores extends \yii\db\ActiveRecord {

    public $evaluadorName;
    public $equipoName;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_rel_equipos_evaluadores';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['evaluadores_id', 'equipo_id'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'evaluadores_id' => Yii::t('app', 'Evaluador ID'),
            'equipo_id' => Yii::t('app', 'Equipo'),
            'evaluadorName' => Yii::t('app', 'Evaluador'),
            'equipoName' => Yii::t('app', 'Equipo'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipo() {
        return $this->hasOne(Equiposvaloradores::className(), ['id' => 'equipo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvaluadores() {
        return $this->hasOne(Usuarios::className(), ['usua_id' => 'evaluadores_id']);
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
    public function getEvaluadoresList() {
        return ArrayHelper::map(Usuarios::find()->orderBy('usua_nombre')->asArray()->all(), 'usa_id', 'usua_nombre');
    }

}
