<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_gestor_evaluacion_preguntas".
 *
 * @property int $id_gestorevaluacionpreguntas
 * @property int|null $id_evaluacionnombre
 * @property string|null $nombrepregunta
 * @property string|null $descripcionpregunta
 * @property string|null $fechacreacion
 * @property int|null $usua_id
 * @property int|null $anulado
 */

class GestorEvaluacionPreguntas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_gestor_evaluacion_preguntas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_evaluacionnombre', 'usua_id', 'anulado'], 'integer'],            
            [['nombrepregunta'], 'string', 'max' => 255],
            [['descripcionpregunta'], 'string', 'max' => 500],
            [['fechacreacion'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_gestorevaluacionpreguntas' => Yii::t('app', ''), 
            'id_evaluacionnombre' => Yii::t('app', ''),
            'nombrepregunta' => Yii::t('app', ''),
            'descripcionpregunta' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }
}