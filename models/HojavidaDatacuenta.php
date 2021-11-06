<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_hojavida_datacuenta".
 *
 * @property integer $hv_idcuenta
 * @property integer $hv_idpersonal
 * @property integer $id_dp_cliente
 * @property string $cod_pcrc
 * @property string $ccdirector
 * @property string $ccgerente
 * @property integer $anulado
 * @property string $fechacreacion
 * @property integer $usua_id
 */
class HojavidaDatacuenta extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_hojavida_datacuenta';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hv_idpersonal', 'id_dp_cliente', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['cod_pcrc'], 'string', 'max' => 100],
            [['ccdirector', 'ccgerente'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'hv_idcuenta' => Yii::t('app', ''),
            'hv_idpersonal' => Yii::t('app', ''),
            'id_dp_cliente' => Yii::t('app', ''),
            'cod_pcrc' => Yii::t('app', ''),
            'ccdirector' => Yii::t('app', ''),
            'ccgerente' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}