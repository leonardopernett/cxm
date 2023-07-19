<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_gestor_evaluacion_formulario".
 *
 * @property int $id_gestor_evaluacion_formulario
 * @property int $id_evaluacionnombre
 * @property int $id_tipo_evalua
 * @property int $id_evaluador Usuario que realiza la evaluacion
 * @property int $id_evaluado Usuario al que evaluan
 * @property int|null $id_estado_evaluacion
 * @property float|null $suma_respuestas suma de todas las respuestas asociadas al formulario
 * @property float|null $promedio_final promedio final de todas las respuestas asociadas al formulario
 * @property string $fechacreacion
 * @property int|null $usua_id
 * @property string|null $fechamodificacion
 * @property int|null $usua_id_modificacion
 * @property int|null $anulado 1: Eliminado logicamente, 0: Activo
 */
class GestorEvaluacionFormulario extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_gestor_evaluacion_formulario';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_evaluacionnombre', 'id_tipo_evalua', 'id_evaluador', 'id_evaluado'], 'required'],
            [['id_evaluacionnombre', 'id_tipo_evalua', 'id_evaluador', 'id_evaluado', 'id_estado_evaluacion', 'usua_id', 'anulado'], 'integer'],
            [['suma_respuestas', 'promedio_final'], 'number'],
            [['fechacreacion'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_gestor_evaluacion_formulario' => Yii::t('app', ''),
            'id_evaluacionnombre' => Yii::t('app', ''),
            'id_tipo_evalua' => Yii::t('app', ''),
            'id_evaluador' => Yii::t('app', ''),
            'id_evaluado' => Yii::t('app', ''),
            'id_estado_evaluacion' => Yii::t('app', ''),
            'suma_respuestas' => Yii::t('app', ''),
            'promedio_final' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }
}