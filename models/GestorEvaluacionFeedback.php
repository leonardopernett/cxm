<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_gestor_evaluacion_feedback".
 *
 * @property int $id_gestor_evaluacion_feedback
 * @property int $id_calificaciontotal
 * @property int $id_jefe id usuario de la carga masiva que genera el acuerdo final
 * @property string|null $comentario acuerdo final
 * @property string|null $fechacreacion
 * @property int|null $usua_id
 * @property int|null $anulado
 */
class GestorEvaluacionFeedback extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_gestor_evaluacion_feedback';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_calificaciontotal', 'id_jefe'], 'required'],
            [['id_calificaciontotal', 'id_jefe', 'usua_id', 'anulado'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['comentario'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_gestor_evaluacion_feedback' => Yii::t('app', ''),
            'id_calificaciontotal' => Yii::t('app', ''),
            'id_jefe' => Yii::t('app', ''),
            'comentario' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }
}