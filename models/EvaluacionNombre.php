<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_evaluacion_nombre".
 *
 * @property integer $idevaluacionnombre
 * @property string $nombreeval
 * @property string $fechacrecion
 * @property integer $anulado
 * @property integer $usua_id
 */
class EvaluacionNombre extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_evaluacion_nombre';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fechacrecion'], 'safe'],
            [['anulado', 'usua_id'], 'integer'],
            [['nombreeval'], 'string', 'max' => 250]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idevaluacionnombre' => Yii::t('app', ''),
            'nombreeval' => Yii::t('app', ''),
            'fechacrecion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}