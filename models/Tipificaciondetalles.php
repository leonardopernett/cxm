<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tbl_tipificaciondetalles".
 *
 * @property integer $id
 * @property string $name
 * @property integer $tipificacion_id
 * @property integer $subtipificacion_id
 * @property integer $nmorden
 * @property integer $snen_uso
 *
 * @property TblEjecucionbloquedetallesSubtipificaciones[] $tblEjecucionbloquedetallesSubtipificaciones
 * @property TblEjecucionbloquedetallesTipificaciones[] $tblEjecucionbloquedetallesTipificaciones
 * @property TblTipificacions $subtipificacion
 * @property TblTipificacions $tipificacion
 * @property TblTmpejecucionbloquedetallesSubtipificaciones[] $tblTmpejecucionbloquedetallesSubtipificaciones
 * @property TblTmpejecucionbloquedetallesTipificaciones[] $tblTmpejecucionbloquedetallesTipificaciones
 */
class Tipificaciondetalles extends \yii\db\ActiveRecord {

    public $tipificacionName;
    public $subTipificacionName;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_tipificaciondetalles';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['tipificacion_id', 'name'], 'required'],
            [['tipificacion_id', 'subtipificacion_id', 'nmorden', 'snen_uso'], 'integer'],
            [['name'], 'string', 'max' => 250],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'tipificacion_id' => Yii::t('app', 'Tipificacion ID'),
            'tipificacionName' => Yii::t('app', 'Tipificacion ID'),
            'subtipificacion_id' => Yii::t('app', 'Subtipificacion ID'),
            'subTipificacionName' => Yii::t('app', 'Subtipificacion ID'),
            'nmorden' => Yii::t('app', 'Nmorden'),
            'snen_uso' => Yii::t('app', 'Snen Uso'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEjecucionbloquedetallesSubtipificaciones() {
        return $this->hasMany(EjecucionbloquedetallesSubtipificaciones::className(), ['tipificaciondetalle_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEjecucionbloquedetallesTipificaciones() {
        return $this->hasMany(EjecucionbloquedetallesTipificaciones::className(), ['tipificaciondetalle_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubtipificacion() {
        return $this->hasOne(Tipificaciones::className(), ['id' => 'subtipificacion_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipificacion() {
        return $this->hasOne(Tipificaciones::className(), ['id' => 'tipificacion_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTmpejecucionbloquedetallessubtipificaciones() {
        return $this->hasMany(TmpejecucionbloquedetallesSubtipificaciones::className(), ['tipificaciondetalle_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTmpejecucionbloquedetallestipificaciones() {
        return $this->hasMany(TmpejecucionbloquedetallesTipificaciones::className(), ['tipificaciondetalle_id' => 'id']);
    }

    /**
     * Metodo que retorna el listado de sub-tipificaciones
     * 
     * @return array
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getSubTipificacionList() {
        return ArrayHelper::map(Tipificaciones::find()->orderBy('name')->asArray()->all(), 'id', 'name');
    }

    /**
     * Retorna string SI o NO para el atributo (snen_uso)
     * 
     * @return string
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getStringUso() {
        return ($this->snen_uso == 1) ? 'SI' : 'NO';
    }

    /**
     * Metodo que retorna las tipificaciones
     * @return array
     * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public static function getDetallesFromIds($ids = array()) {
        $tip = \app\models\Tipificaciondetalles::find()
                ->where(['IN', "tipificacion_id", $ids])
                ->andWhere(["snen_uso" => 1])
                ->orderBy("nmorden asc,id asc")
                ->asArray()
                ->all();
        $return = array();
        foreach ($tip as $r) {
            $return[$r["tipificacion_id"]][$r["id"]] = $r["name"];
        }
        return $return;
    }

}
