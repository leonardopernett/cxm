<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_evaluacion_competencias".
 *
 * @property integer $idevaluacioncompetencia
 * @property string $namecompetencia
 * @property integer $idevaluacionnivel
 * @property integer $idevaluaciontipo
 * @property integer $idevaluacionbloques
 * @property string $fechacrecion
 * @property integer $anulado
 * @property integer $usua_id
 */
class EvaluacionCompetencias extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_evaluacion_competencias';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idevaluacionnivel', 'idevaluaciontipo', 'idevaluacionbloques', 'anulado', 'usua_id'], 'integer'],
            [['fechacrecion'], 'safe'],
            [['namecompetencia'], 'string', 'max' => 250]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idevaluacioncompetencia' => Yii::t('app', ''),
            'namecompetencia' => Yii::t('app', ''),
            'idevaluacionnivel' => Yii::t('app', ''),
            'idevaluaciontipo' => Yii::t('app', ''),
            'idevaluacionbloques' => Yii::t('app', ''),
            'fechacrecion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}