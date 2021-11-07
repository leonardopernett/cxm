<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_hojavida_asignareventos".
 *
 * @property integer $hv_idasignareventos
 * @property integer $hv_idpersonal
 * @property integer $hv_ideventos
 * @property integer $anulado
 * @property string $fechacreacion
 * @property integer $usua_id
 */
class HojavidaAsignareventos extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_hojavida_asignareventos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hv_idpersonal', 'hv_ideventos', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'hv_idasignareventos' => Yii::t('app', ''),
            'hv_idpersonal' => Yii::t('app', ''),
            'hv_ideventos' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}