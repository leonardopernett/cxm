<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_evaluacion_bloques".
 *
 * @property integer $idevaluacionbloques
 * @property string $namebloque
 * @property integer $idevaluacionnombre
 * @property string $fechacrecion
 * @property integer $anulado
 * @property integer $usua_id
 */
class EvaluacionBloque extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_evaluacion_bloques';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idevaluacionnombre', 'anulado', 'usua_id'], 'integer'],
            [['fechacrecion'], 'safe'],
            [['namebloque'], 'string', 'max' => 250]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [            
            'idevaluacionbloques' => Yii::t('app', ''),
            'namebloque' => Yii::t('app', ''),      
            'idevaluacionnombre' => Yii::t('app', ''),
            'fechacrecion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}
