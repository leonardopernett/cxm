<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_tmpejecucionbloquedetalles_subtipificaciones".
 *
 * @property integer $id
 * @property integer $tipificaciondetalle_id
 * @property integer $tmpejecucionbloquedetalles_tipificacion_id
 * @property integer $sncheck
 *
 * @property TblTmpejecucionbloquedetallesTipificaciones $tmpejecucionbloquedetallesTipificacion
 * @property TblTipificaciondetalles $tipificaciondetalle
 */
class TmpejecucionbloquedetallesSubtipificaciones extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_tmpejecucionbloquedetalles_subtipificaciones';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['tipificaciondetalle_id', 'tmpejecucionbloquedetalles_tipificacion_id'], 'required'],
            [['tipificaciondetalle_id', 'tmpejecucionbloquedetalles_tipificacion_id', 'sncheck'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'tipificaciondetalle_id' => Yii::t('app', 'Tipificaciondetalle ID'),
            'tmpejecucionbloquedetalles_tipificacion_id' => Yii::t('app', 'Tmpejecucionbloquedetalles Tipificacion ID'),
            'sncheck' => Yii::t('app', 'Sncheck'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTmpejecucionbloquedetallesTipificacion() {
        return $this->hasOne(TmpejecucionbloquedetallesTipificaciones::className(), ['id' => 'tmpejecucionbloquedetalles_tipificacion_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipificaciondetalle() {
        return $this->hasOne(Tipificaciondetalles::className(), ['id' => 'tipificaciondetalle_id']);
    }
    
    /**
     * Metodo que retorna el listado de subtificaciones
     * 
     * @param int $det_id
     * @param int $tipif_id
     * @return array
     * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public static function getSubTipificaciones($det_id, $tipif_id) {
        return TmpejecucionbloquedetallesSubtipificaciones::find()
                ->select("tipifdet.*, tbl_tmpejecucionbloquedetalles_subtipificaciones.sncheck")
                ->join('INNER JOIN'
                        , 'tbl_tmpejecucionbloquedetalles_tipificaciones tmtip'
                        , 'tbl_tmpejecucionbloquedetalles_subtipificaciones.tmpejecucionbloquedetalles_tipificacion_id = tmtip.id')
                ->join('INNER JOIN'
                        , 'tbl_tipificaciondetalles tipifdet'
                        , 'tipifdet.id = tbl_tmpejecucionbloquedetalles_subtipificaciones.tipificaciondetalle_id')
                ->where('tmtip.tmpejecucionbloquedetalle_id = ' . $det_id . ' AND tmtip.tipificaciondetalle_id = ' . $tipif_id . ' AND `tipifdet`.`snen_uso` = 1')
                ->asArray()
                ->all();
    }

}
