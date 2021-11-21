<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_hojavida_datalaboral".
 *
 * @property integer $hv_idlaboral
 * @property string $rol
 * @property integer $hv_idpersonal
 * @property integer $hv_id_antiguedad
 * @property string $fecha_inicio_contacto
 * @property integer $afinidad
 * @property integer $tipo_afinidad
 * @property integer $nivel_afinidad
 * @property string $nombre_jefe
 * @property string $cargo_jefe
 * @property string $trabajo_anterior
 * @property integer $anulado
 * @property string $fechacreacion
 * @property integer $usua_id
 */
class HojavidaDatalaboral extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_hojavida_datalaboral';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hv_idpersonal', 'hv_id_antiguedad', 'afinidad', 'tipo_afinidad', 'nivel_afinidad', 'anulado', 'usua_id'], 'integer'],
            [['fecha_inicio_contacto', 'fechacreacion'], 'safe'],
            [['rol', 'nombre_jefe', 'cargo_jefe', 'trabajo_anterior','areatrabajo'], 'string', 'max' => 250]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'hv_idlaboral' => Yii::t('app', ''),
            'rol' => Yii::t('app', ''),
            'hv_idpersonal' => Yii::t('app', ''),
            'hv_id_antiguedad' => Yii::t('app', ''),
            'fecha_inicio_contacto' => Yii::t('app', ''),
            'afinidad' => Yii::t('app', ''),
            'tipo_afinidad' => Yii::t('app', ''),
            'nivel_afinidad' => Yii::t('app', ''),
            'nombre_jefe' => Yii::t('app', ''),
            'cargo_jefe' => Yii::t('app', ''),
            'trabajo_anterior' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'areatrabajo' => Yii::t('app', ''),
        ];
    }
}