<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_gestor_evaluacion_calificaciontotal".
 *
 * @property int $id_gestor_evaluacion_calificaciontotal
 * @property int $id_evalua_nombre periodo al cual esta asignada esta calificacion
 * @property int $id_evaluado colaborador que fue evaluado
 * @property float|null $suma_total_evalua suma acumulada de los puntajes finales de cada tipo de evaluacion realizada al colaborador
 * @property float|null $promedio_total_evalua suma acumulada de los promedios de cada tipo de evaluacion realizada al colaborador
 * @property int|null $cant_evaluaciones
 * @property string|null $fechacreacion
 * @property int|null $usua_id
 * @property int|null $anulado
 */
class GestorEvaluacionCalificacionTotal extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_gestor_evaluacion_calificaciontotal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_evalua_nombre', 'id_evaluado'], 'required'],
            [['id_evalua_nombre', 'id_evaluado', 'usua_id', 'anulado','cant_evaluaciones'], 'integer'],
            [['suma_total_evalua', 'promedio_total_evalua'], 'number'],
            [['fechacreacion'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_gestor_evaluacion_calificaciontotal' => Yii::t('app', ''),
            'id_evalua_nombre' => Yii::t('app', ''),
            'id_evaluado' => Yii::t('app', ''),
            'suma_total_evalua' => Yii::t('app', ''),
            'promedio_total_evalua' => Yii::t('app', ''),
            'cant_evaluaciones' => Yii::t('app', ''),            
            'fechacreacion' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }
}