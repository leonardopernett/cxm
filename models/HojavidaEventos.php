<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_hojavida_eventos".
 *
 * @property integer $hv_ideventos
 * @property string $nombre_evento
 * @property string $tipo_evento
 * @property integer $hv_idciudad
 * @property string $fecha_evento
 * @property string $asistencia
 * @property integer $anulado
 * @property string $fechacreacion
 * @property integer $usua_id
 */
class HojavidaEventos extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_hojavida_eventos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hv_idciudad', 'anulado', 'usua_id'], 'integer'],
            [['fecha_evento_inicio', 'fecha_evento_fin', 'fechacreacion'], 'safe'],
            [['nombre_evento', 'tipo_evento', 'asistencia'], 'string', 'max' => 250]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'hv_ideventos' => Yii::t('app', ''),
            'nombre_evento' => Yii::t('app', ''),
            'tipo_evento' => Yii::t('app', ''),
            'hv_idciudad' => Yii::t('app', ''),
            'fecha_evento_inicio' => Yii::t('app', ''),
            'fecha_evento_fin' => Yii::t('app', ''),
            'asistencia' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}