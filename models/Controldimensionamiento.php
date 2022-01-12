<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_control_dimensionamiento".
 *
 * @property integer $iddimensionamiento
 * @property integer $usua_id
 * @property string $year
 * @property string $month
 * @property integer $cant_valor
 * @property string $tiempo_llamada
 * @property string $tiempoadicional
 * @property string $actuales
 * @property string $otras_actividad
 * @property string $turno_promedio
 * @property string $ausentismo
 * @property string $vaca_permi_licen
 * @property string $fechacreacion
 * @property integer $anulado
 */
class Controldimensionamiento extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_control_dimensionamiento';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['usua_id', 'cant_valor', 'anulado'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['year'], 'string', 'max' => 5],
            [['year','month', 'tiempo_llamada', 'tiempoadicional', 'actuales', 'otras_actividad', 'turno_promedio', 'ausentismo', 'vaca_permi_licen'], 'string', 'max' => 20],
            [['month', 'tiempo_llamada', 'tiempoadicional', 'actuales', 'otras_actividad', 'turno_promedio', 'ausentismo', 'vaca_permi_licen'], 'filter', 'filter' => function($value){
                return filter_var($value,FILTER_SANITIZE_STRING) ;
             }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'iddimensionamiento' => Yii::t('app', 'Iddimensionamiento'),
            'usua_id' => Yii::t('app', 'Usua ID'),
            'year' => Yii::t('app', 'Year'),
            'month' => Yii::t('app', 'Month'),
            'cant_valor' => Yii::t('app', 'Cant Valor'),
            'tiempo_llamada' => Yii::t('app', 'Tiempo Llamada'),
            'tiempoadicional' => Yii::t('app', 'Tiempoadicional'),
            'actuales' => Yii::t('app', 'Actuales'),
            'otras_actividad' => Yii::t('app', 'Otras Actividad'),
            'turno_promedio' => Yii::t('app', 'Turno Promedio'),
            'ausentismo' => Yii::t('app', 'Ausentismo'),
            'vaca_permi_licen' => Yii::t('app', 'Vaca Permi Licen'),
            'fechacreacion' => Yii::t('app', 'Fechacreacion'),
            'anulado' => Yii::t('app', 'Anulado'),
        ];
    }
}