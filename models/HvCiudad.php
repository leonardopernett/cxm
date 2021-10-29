<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_hv_ciudad".
 *
 * @property integer $hv_idciudad
 * @property integer $pais_id
 * @property string $ciudad
 * @property integer $usua_id
 * @property integer $anulado
 * @property string $fechacreacion
 *
 * @property TblHvPais $pais
 * @property TblUsuarios $usua
 */
class HvCiudad extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_hv_ciudad';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pais_id', 'usua_id', 'anulado'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['ciudad'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'hv_idciudad' => Yii::t('app', ''),
            'pais_id' => Yii::t('app', ''),
            'ciudad' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPais()
    {
        return $this->hasOne(TblHvPais::className(), ['hv_idpais' => 'pais_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsua()
    {
        return $this->hasOne(TblUsuarios::className(), ['usua_id' => 'usua_id']);
    }
}