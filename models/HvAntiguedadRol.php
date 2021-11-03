<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_hv_antiguedad_rol".
 *
 * @property integer $hv_id_antiguedad
 * @property string $antiguedad
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fecha_creacion
 *
 * @property TblUsuarios $usua
 */
class HvAntiguedadRol extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_hv_antiguedad_rol';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['anulado', 'usua_id'], 'integer'],
            [['fecha_creacion'], 'safe'],
            [['antiguedad'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'hv_id_antiguedad' => Yii::t('app', ''),
            'antiguedad' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fecha_creacion' => Yii::t('app', ''),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsua()
    {
        return $this->hasOne(TblUsuarios::className(), ['usua_id' => 'usua_id']);
    }
}