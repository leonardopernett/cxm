<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_tmpejecucionbloquedetalles".
 *
 * @property integer $id
 * @property integer $tmpejecucionformulario_id
 * @property integer $seccion_id
 * @property integer $bloque_id
 * @property integer $bloquedetalle_id
 * @property integer $tipificacion_id
 * @property integer $calificacion_id
 * @property integer $calificaciondetalle_id
 * @property double $i1_nmcalculo
 * @property double $i2_nmcalculo
 * @property double $i3_nmcalculo
 * @property double $i4_nmcalculo
 * @property double $i5_nmcalculo
 * @property double $i6_nmcalculo
 * @property double $i7_nmcalculo
 * @property double $i8_nmcalculo
 * @property double $i9_nmcalculo
 * @property double $i10_nmcalculo
 * @property double $i1_nmfactor
 * @property double $i2_nmfactor
 * @property double $i3_nmfactor
 * @property double $i4_nmfactor
 * @property double $i5_nmfactor
 * @property double $i6_nmfactor
 * @property double $i7_nmfactor
 * @property double $i8_nmfactor
 * @property double $i9_nmfactor
 * @property double $i10_nmfactor
 *
 * @property TblTmpejecucionformularios $tmpejecucionformulario
 * @property TblTmpejecucionbloquedetallesTipificaciones[] $tblTmpejecucionbloquedetallesTipificaciones
 */
class Tmpejecucionbloquedetalles extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_tmpejecucionbloquedetalles';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['tmpejecucionformulario_id', 'seccion_id', 'bloque_id', 'bloquedetalle_id', 'calificacion_id'], 'required'],
            [['tmpejecucionformulario_id', 'seccion_id', 'bloque_id', 'bloquedetalle_id', 'tipificacion_id', 'calificacion_id', 'calificaciondetalle_id'], 'integer'],
            [['i1_nmcalculo', 'i2_nmcalculo', 'i3_nmcalculo', 'i4_nmcalculo', 'i5_nmcalculo', 'i6_nmcalculo', 'i7_nmcalculo', 'i8_nmcalculo', 'i9_nmcalculo', 'i10_nmcalculo', 'i1_nmfactor', 'i2_nmfactor', 'i3_nmfactor', 'i4_nmfactor', 'i5_nmfactor', 'i6_nmfactor', 'i7_nmfactor', 'i8_nmfactor', 'i9_nmfactor', 'i10_nmfactor','c_pits'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'tmpejecucionformulario_id' => Yii::t('app', 'Tmpejecucionformulario ID'),
            'seccion_id' => Yii::t('app', 'Seccion ID'),
            'bloque_id' => Yii::t('app', 'Bloque ID'),
            'bloquedetalle_id' => Yii::t('app', 'Bloquedetalle ID'),
            'tipificacion_id' => Yii::t('app', 'Tipificacion ID'),
            'calificacion_id' => Yii::t('app', 'Calificacion ID'),
            'calificaciondetalle_id' => Yii::t('app', 'Calificaciondetalle ID'),
            'i1_nmcalculo' => Yii::t('app', 'I1 Nmcalculo'),
            'i2_nmcalculo' => Yii::t('app', 'I2 Nmcalculo'),
            'i3_nmcalculo' => Yii::t('app', 'I3 Nmcalculo'),
            'i4_nmcalculo' => Yii::t('app', 'I4 Nmcalculo'),
            'i5_nmcalculo' => Yii::t('app', 'I5 Nmcalculo'),
            'i6_nmcalculo' => Yii::t('app', 'I6 Nmcalculo'),
            'i7_nmcalculo' => Yii::t('app', 'I7 Nmcalculo'),
            'i8_nmcalculo' => Yii::t('app', 'I8 Nmcalculo'),
            'i9_nmcalculo' => Yii::t('app', 'I9 Nmcalculo'),
            'i10_nmcalculo' => Yii::t('app', 'I10 Nmcalculo'),
            'i1_nmfactor' => Yii::t('app', 'I1 Nmfactor'),
            'i2_nmfactor' => Yii::t('app', 'I2 Nmfactor'),
            'i3_nmfactor' => Yii::t('app', 'I3 Nmfactor'),
            'i4_nmfactor' => Yii::t('app', 'I4 Nmfactor'),
            'i5_nmfactor' => Yii::t('app', 'I5 Nmfactor'),
            'i6_nmfactor' => Yii::t('app', 'I6 Nmfactor'),
            'i7_nmfactor' => Yii::t('app', 'I7 Nmfactor'),
            'i8_nmfactor' => Yii::t('app', 'I8 Nmfactor'),
            'i9_nmfactor' => Yii::t('app', 'I9 Nmfactor'),
            'i10_nmfactor' => Yii::t('app', 'I10 Nmfactor'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTmpejecucionformulario() {
        return $this->hasOne(Tmpejecucionformularios::className(), ['id' => 'tmpejecucionformulario_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblTmpejecucionbloquedetallesTipificaciones() {
        return $this->hasMany(TmpejecucionbloquedetallesTipificaciones::className(), ['tmpejecucionbloquedetalle_id' => 'id']);
    }
    
    /**
     * Metodo que retorna todas las preguntas
     * 
     * @return array
     * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public static function getAllByFormId($form_id) {

        $form = \app\models\Tmpejecucionbloquedetalles::find()
                ->join('INNER JOIN', 'tbl_seccions s'
                        , 's.id = tbl_tmpejecucionbloquedetalles.seccion_id')
                ->join('INNER JOIN', 'tbl_tmpejecucionsecciones ts'
                        , 'ts.seccion_id = s.id '
                        . 'AND ts.tmpejecucionformulario_id = ' . $form_id)
                ->join('INNER JOIN', 'tbl_bloques b'
                        , 'b.id = tbl_tmpejecucionbloquedetalles.bloque_id')
                ->join('INNER JOIN', 'tbl_bloquedetalles bd'
                        , 'bd.id = tbl_tmpejecucionbloquedetalles.bloquedetalle_id')
                ->select('tbl_tmpejecucionbloquedetalles.*'
                        . ', s.name seccion, s.sndesplegar_comentario'
                        . ', b.name bloque'
                        . ', b.dsdescripcion bloque_descripcion'
                        . ', bd.name pregunta, bd.calificacion_id'
                        . ', bd.tipificacion_id,ts.dscomentario'
                        . ', bd.c_pits  c_pitsBD'
                        . ', s.is_pits  isPits'
                        . ', bd.id_seccion_pits  id_seccion_pits'
                        . ', s.sdescripcion  sdescripcion'
                        . ', bd.descripcion  bddecripcion')
                ->where('tbl_tmpejecucionbloquedetalles.tmpejecucionformulario_id = ' . $form_id)
                ->orderBy("s.nmorden, b.nmorden, bd.nmorden ASC")
                ->asArray()
                ->all();

        $arr = array();
        $objeto = new \stdClass();
        foreach ($form as $value) {
            $objeto = new \stdClass();
            foreach ($value as $k => $v) {
                $objeto->$k = $v;
            }
            $arr[] = $objeto;
        }
        return $arr;
    }

}
