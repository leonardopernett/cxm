<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_hojavida_datagerente".
 *
 * @property integer $hv_idgerente
 * @property integer $hv_idpersonal
 * @property integer $id_dp_cliente
 * @property string $ccgerente
 * @property integer $anulado
 * @property string $fechacreacion
 * @property integer $usua_id
 */
class HojavidaDatagerente extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_hojavida_datagerente';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hv_idpersonal', 'id_dp_cliente', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['ccgerente'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'hv_idgerente' => Yii::t('app', ''),
            'hv_idpersonal' => Yii::t('app', ''),
            'id_dp_cliente' => Yii::t('app', ''),
            'ccgerente' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}