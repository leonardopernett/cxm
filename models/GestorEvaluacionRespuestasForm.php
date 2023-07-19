<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_gestor_evaluacion_respuestasform".
 *
 * @property int $id_gestor_evaluacion_respuestasform
 * @property int $id_gestor_evaluacion_formulario
 * @property int $id_pregunta
 * @property int $id_respuesta
 * @property string $observacion
 * @property string $acuerdos
 * @property string|null $fechacreacion
 * @property int|null $usua_id
 * @property int|null $anulado
 */
class GestorEvaluacionRespuestasForm extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_gestor_evaluacion_respuestasform';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_gestor_evaluacion_formulario', 'id_pregunta', 'id_respuesta', 'observacion', 'acuerdos'], 'required'],
            [['id_gestor_evaluacion_formulario', 'id_pregunta', 'id_respuesta', 'usua_id', 'anulado'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['observacion', 'acuerdos'], 'string', 'max' => 300],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_gestor_evaluacion_respuestasform' => Yii::t('app', ''),
            'id_gestor_evaluacion_formulario' => Yii::t('app', ''),
            'id_pregunta' => Yii::t('app', ''),
            'id_respuesta' => Yii::t('app', ''),
            'observacion' => Yii::t('app', ''),
            'acuerdos' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }
}