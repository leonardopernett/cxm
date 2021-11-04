<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_hojavida_datapersonal".
 *
 * @property integer $hv_idpersonal
 * @property string $nombre_full
 * @property string $identificacion
 * @property string $email
 * @property integer $numero_movil
 * @property integer $numero_fijo
 * @property string $direccion_oficina
 * @property string $direccion_casa
 * @property integer $hv_idpais
 * @property integer $hv_idciudad
 * @property integer $hv_idmodalidad
 * @property integer $tratamiento_data
 * @property integer $suceptible
 * @property string $indicador_satu
 * @property integer $anulado
 * @property string $fechacreacion
 * @property integer $usua_id
 */
class HojavidaDatapersonal extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_hojavida_datapersonal';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hv_idpais', 'hv_idciudad', 'hv_idmodalidad', 'tratamiento_data', 'suceptible', 'anulado', 'usua_id'], 'integer'],
            [['indicador_satu'], 'number'],
            [['fechacreacion'], 'safe'],
            [['numero_movil', 'numero_fijo','nombre_full', 'email', 'direccion_oficina', 'direccion_casa'], 'string', 'max' => 250],
            [['identificacion'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'hv_idpersonal' => Yii::t('app', ''),
            'nombre_full' => Yii::t('app', ''),
            'identificacion' => Yii::t('app', ''),
            'email' => Yii::t('app', ''),
            'numero_movil' => Yii::t('app', ''),
            'numero_fijo' => Yii::t('app', ''),
            'direccion_oficina' => Yii::t('app', ''),
            'direccion_casa' => Yii::t('app', ''),
            'hv_idpais' => Yii::t('app', ''),
            'hv_idciudad' => Yii::t('app', ''),
            'hv_idmodalidad' => Yii::t('app', ''),
            'tratamiento_data' => Yii::t('app', ''),
            'suceptible' => Yii::t('app', ''),
            'indicador_satu' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}