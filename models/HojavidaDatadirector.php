<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_hojavida_datadirector".
 *
 * @property integer $hv_iddirector
 * @property integer $hv_idpersonal
 * @property integer $id_dp_cliente
 * @property string $ccdirector
 * @property integer $anulado
 * @property string $fechacreacion
 * @property integer $usua_id
 */
class HojavidaDatadirector extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_hojavida_datadirector';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hv_idpersonal', 'id_dp_cliente', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['ccdirector'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'hv_iddirector' => Yii::t('app', ''),
            'hv_idpersonal' => Yii::t('app', ''),
            'id_dp_cliente' => Yii::t('app', ''),
            'ccdirector' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}