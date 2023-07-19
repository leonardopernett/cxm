<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_gestor_evaluacion_feedbackentradas".
 *
 * @property int $id_gestor_evaluacion_feedbackentradas
 * @property int $id_feedback
 * @property int $id_remitente persona que envia el comentario
 * @property int $id_destinatario persona a quien va dirigido el comentario
 * @property string|null $comentario
 * @property string|null $fechacreacion
 * @property int|null $usua_id
 * @property int|null $anulado
 */
class GestorEvaluacionFeedbackentradas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_gestor_evaluacion_feedbackentradas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_feedback', 'id_remitente', 'id_destinatario'], 'required'],
            [['id_feedback', 'id_remitente', 'id_destinatario', 'usua_id', 'anulado'], 'integer'],
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
            'id_gestor_evaluacion_feedbackentradas' => Yii::t('app', ''),
            'id_feedback' => Yii::t('app', ''),
            'id_remitente' => Yii::t('app', ''),
            'id_destinatario' => Yii::t('app', ''),
            'comentario' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }
}