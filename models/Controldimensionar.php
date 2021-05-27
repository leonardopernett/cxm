<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_control_dimensionar".
 *
 * @property integer $iddimensionar
 * @property integer $iddimensionamiento
 * @property string $duracion_ponde
 * @property string $ocupacion
 * @property string $carga_trabajo
 * @property string $horasCNX
 * @property string $uti_gentes
 * @property string $horas_nomina_monit
 * @property string $horas_laboral_mes
 * @property string $FTE
 * @property string $p_monit
 * @property string $p_otras_actividad
 * @property string $personas
 * @property string $pnas_vacaciones
 * @property string $pnas_ausentismo
 * @property string $exceso_deficit
 * @property string $fechacreacion
 * @property integer $anulado
 */
class Controldimensionar extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_control_dimensionar';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['iddimensionamiento', 'anulado'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['duracion_ponde', 'ocupacion', 'carga_trabajo', 'horasCNX', 'uti_gentes', 'horas_nomina_monit', 'horas_laboral_mes', 'FTE', 'p_monit', 'p_otras_actividad', 'personas', 'pnas_vacaciones', 'pnas_ausentismo', 'exceso_deficit'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'iddimensionar' => Yii::t('app', ''),
            'iddimensionamiento' => Yii::t('app', ''),
            'duracion_ponde' => Yii::t('app', ''),
            'ocupacion' => Yii::t('app', ''),
            'carga_trabajo' => Yii::t('app', ''),
            'horasCNX' => Yii::t('app', ''),
            'uti_gentes' => Yii::t('app', ''),
            'horas_nomina_monit' => Yii::t('app', ''),
            'horas_laboral_mes' => Yii::t('app', ''),
            'FTE' => Yii::t('app', ''),
            'p_monit' => Yii::t('app', ''),
            'p_otras_actividad' => Yii::t('app', ''),
            'personas' => Yii::t('app', ''),
            'pnas_vacaciones' => Yii::t('app', ''),
            'pnas_ausentismo' => Yii::t('app', ''),
            'exceso_deficit' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }
}