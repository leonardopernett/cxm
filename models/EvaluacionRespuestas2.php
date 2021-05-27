<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_evaluacion_respuestas2".
 *
 * @property integer $idevaluacionrespuesta
 * @property string $namerespuesta
 * @property integer $valor
 * @property integer $idevaluacionnombre
 * @property string $fechacrecion
 * @property integer $anulado
 * @property integer $usua_id
 */
class EvaluacionRespuestas2 extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_evaluacion_respuestas2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['valor', 'idevaluacionnombre', 'anulado', 'usua_id'], 'integer'],
            [['fechacrecion'], 'safe'],
            [['namerespuesta'], 'string', 'max' => 250]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idevaluacionrespuesta' => Yii::t('app', ''),
            'namerespuesta' => Yii::t('app', ''),
            'valor' => Yii::t('app', ''),
            'idevaluacionnombre' => Yii::t('app', ''),
            'fechacrecion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}