<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_tmpejecucionbloquedetalles_tipificaciones".
 *
 * @property integer $id
 * @property integer $tipificaciondetalle_id
 * @property integer $tmpejecucionbloquedetalle_id
 * @property integer $sncheck
 * @property integer $ejecucionbloquedetalles_tipificacion_id
 *
 * @property TblTmpejecucionbloquedetallesSubtipificaciones[] $tblTmpejecucionbloquedetallesSubtipificaciones
 * @property TblTmpejecucionbloquedetalles $tmpejecucionbloquedetalle
 * @property TblTipificaciondetalles $tipificaciondetalle
 */
class TmpejecucionbloquedetallesTipificaciones extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_tmpejecucionbloquedetalles_tipificaciones';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['tipificaciondetalle_id', 'tmpejecucionbloquedetalle_id'], 'required'],
            [['tipificaciondetalle_id', 'tmpejecucionbloquedetalle_id', 'sncheck', 'ejecucionbloquedetalles_tipificacion_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'tipificaciondetalle_id' => Yii::t('app', 'Tipificaciondetalle ID'),
            'tmpejecucionbloquedetalle_id' => Yii::t('app', 'Tmpejecucionbloquedetalle ID'),
            'sncheck' => Yii::t('app', 'Sncheck'),
            'ejecucionbloquedetalles_tipificacion_id' => Yii::t('app', 'Ejecucionbloquedetalles Tipificacion ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTmpejecucionbloquedetallesSubtipificaciones() {
        return $this->hasMany(TmpejecucionbloquedetallesSubtipificaciones::className(), ['tmpejecucionbloquedetalles_tipificacion_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTmpejecucionbloquedetalle() {
        return $this->hasOne(Tmpejecucionbloquedetalles::className(), ['id' => 'tmpejecucionbloquedetalle_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipificaciondetalle() {
        return $this->hasOne(Tipificaciondetalles::className(), ['id' => 'tipificaciondetalle_id']);
    }

    /**
     * Metodo que retorna las tipificaciones de un formulario
     * 
     * @return array
     * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public static function getTipificaciones($det_id) {
        return \app\models\TmpejecucionbloquedetallesTipificaciones::find()
                        ->joinWith('tipificaciondetalle')
                        ->select('tbl_tipificaciondetalles.*')
                        ->where([
                            'tbl_tmpejecucionbloquedetalles_tipificaciones.sncheck' => 1,
                            'tbl_tmpejecucionbloquedetalles_tipificaciones.tmpejecucionbloquedetalle_id' => $det_id,
                        ])
                        ->all();
    }

}
