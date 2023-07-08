<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_gestor_evaluacion_calificaporpregunta".
 *
 * @property int $id_gestor_evaluacion_calificaporpregunta
 * @property int $id_calificaciontotal
 * @property int $id_pregunta
 * @property float|null $suma_total_por_pregunta suma acumulada(autoevaluacion y a cargo) para cada pregunta (competencia)
 * @property int|null $cantidad_evaluaciones cantidad de tipo de evaluaciones
 * @property float|null $prom_total_por_pregunta promedio acumulado(autoevaluacion y a cargo) para cada pregunta (competencia)
 * @property string|null $fechacreacion
 * @property int|null $usua_id
 * @property int|null $anulado
 */
class GestorEvaluacionCalificaPorPregunta extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_gestor_evaluacion_calificaporpregunta';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_calificaciontotal', 'id_pregunta'], 'required'],
            [['id_calificaciontotal', 'id_pregunta', 'usua_id', 'anulado','cantidad_evaluaciones'], 'integer'],
            [['suma_total_por_pregunta', 'prom_total_por_pregunta'], 'number'],
            [['fechacreacion'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_gestor_evaluacion_calificaporpregunta' => Yii::t('app', ''),
            'id_calificaciontotal' => Yii::t('app', ''),
            'id_pregunta' => Yii::t('app', ''),
            'suma_total_por_pregunta' => Yii::t('app', ''),
            'cantidad_evaluaciones' => Yii::t('app', ''),
            'prom_total_por_pregunta' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }
}