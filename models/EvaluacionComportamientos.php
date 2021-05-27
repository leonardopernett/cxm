<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_evaluacion_comportamientos".
 *
 * @property integer $idevaluacionpregunta
 * @property string $namepregunta
 * @property integer $idevaluacioncompetencia
 * @property integer $idevaluacionnombre
 * @property string $fechacrecion
 * @property integer $anulado
 * @property integer $usua_id
 */
class EvaluacionComportamientos extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_evaluacion_comportamientos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idevaluacioncompetencia', 'idevaluacionnombre', 'anulado', 'usua_id'], 'integer'],
            [['fechacrecion'], 'safe'],
            [['namepregunta'], 'string', 'max' => 250]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idevaluacionpregunta' => Yii::t('app', ''),
            'namepregunta' => Yii::t('app', ''),
            'idevaluacioncompetencia' => Yii::t('app', ''),
            'idevaluacionnombre' => Yii::t('app', ''),            
            'fechacrecion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}
