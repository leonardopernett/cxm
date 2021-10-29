<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_hv_pais".
 *
 * @property integer $hv_idpais
 * @property string $pais
 * @property integer $usua_id
 * @property integer $anulado
 * @property string $fechacreacion
 *
 * @property TblHvCiudad[] $tblHvCiudads
 * @property TblUsuarios $usua
 */
class HvPais extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_hv_pais';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['usua_id', 'anulado'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['pais'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'hv_idpais' => Yii::t('app', ''),
            'pais' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblHvCiudads()
    {
        return $this->hasMany(TblHvCiudad::className(), ['pais_id' => 'hv_idpais']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsua()
    {
        return $this->hasOne(TblUsuarios::className(), ['usua_id' => 'usua_id']);
    }
}