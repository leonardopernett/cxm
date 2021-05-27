<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_tmpejecucionbloques".
 *
 * @property integer $id
 * @property integer $tmpejecucionformulario_id
 * @property integer $tmpejecucionseccion_id
 * @property integer $seccion_id
 * @property integer $bloque_id
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
 * @property TblTmpejecucionsecciones $tmpejecucionseccion
 */
class Tmpejecucionbloques extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_tmpejecucionbloques';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['tmpejecucionformulario_id', 'tmpejecucionseccion_id', 'seccion_id', 'bloque_id'], 'required'],
            [['tmpejecucionformulario_id', 'tmpejecucionseccion_id', 'seccion_id', 'bloque_id'], 'integer'],
            [['i1_nmcalculo', 'i2_nmcalculo', 'i3_nmcalculo', 'i4_nmcalculo', 'i5_nmcalculo', 'i6_nmcalculo', 'i7_nmcalculo', 'i8_nmcalculo', 'i9_nmcalculo', 'i10_nmcalculo', 'i1_nmfactor', 'i2_nmfactor', 'i3_nmfactor', 'i4_nmfactor', 'i5_nmfactor', 'i6_nmfactor', 'i7_nmfactor', 'i8_nmfactor', 'i9_nmfactor', 'i10_nmfactor'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'tmpejecucionformulario_id' => Yii::t('app', 'Tmpejecucionformulario ID'),
            'tmpejecucionseccion_id' => Yii::t('app', 'Tmpejecucionseccion ID'),
            'seccion_id' => Yii::t('app', 'Seccion ID'),
            'bloque_id' => Yii::t('app', 'Bloque ID'),
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
    public function getTmpejecucionseccion() {
        return $this->hasOne(Tmpejecucionsecciones::className(), ['id' => 'tmpejecucionseccion_id']);
    }

}
