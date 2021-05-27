<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_evaluacion_competencias".
 *
 * @property integer $idevaluacionfb_mensaje
 * @property string $mensaje
 * @property integer $idevaluacioncompetencia
 * @property integer $rol_competencia
 * @property integer $tipocompetencia
 * @property integer $idevaluacionnombre
 * @property string $fechacrecion
 * @property integer $anulado
 * @property integer $usua_id
 */
class EvaluacionMensajes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_evaluacion_feedback_mensaje';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idevaluacioncompetencia', 'rol_competencia', 'tipocompetencia', 'idevaluacionnombre', 'anulado', 'usua_id'], 'integer'],
            [['fechacrecion'], 'safe'],
            [['mensaje'], 'string', 'max' => 500]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idevaluacionfb_mensaje' => Yii::t('app', ''),
            'idevaluacioncompetencia' => Yii::t('app', ''),
            'mensaje' => Yii::t('app', ''),
            'rol_competencia' => Yii::t('app', ''),
            'tipocompetencia' => Yii::t('app', ''),
            'idevaluacionnombre' => Yii::t('app', ''),
            'fechacrecion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}