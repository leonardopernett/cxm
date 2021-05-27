<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_evaluacion_tipoeval".
 *
 * @property integer $idevaluaciontipo
 * @property string $tipoevaluacion
 * @property string $fechacrecion
 * @property integer $anulado
 * @property integer $usua_id
 */
class EvaluacionTipoeval extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_evaluacion_tipoeval';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fechacrecion'], 'safe'],
            [['anulado', 'usua_id'], 'integer'],
            [['tipoevaluacion'], 'string', 'max' => 250]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idevaluaciontipo' => Yii::t('app', ''),
            'tipoevaluacion' => Yii::t('app', ''),
            'fechacrecion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}