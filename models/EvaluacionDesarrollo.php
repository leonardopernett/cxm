<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_evaluacion_desarrollo".
 *
 * @property integer $idevaluaciondesarrollo
 * @property string $idevaluador
 * @property string $idevalados
 * @property integer $idevaluaciontipo
 * @property integer $realizada
 * @property string $comentarios
 * @property string $fechacrecion
 * @property integer $anulado
 * @property integer $usua_id
 */
class EvaluacionDesarrollo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_evaluacion_desarrollo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idevaluaciontipo', 'realizada', 'anulado', 'usua_id'], 'integer'],
            [['fechacrecion'], 'safe'],
            [['idevaluador', 'idevalados'], 'string', 'max' => 20],
            [['comentarios'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idevaluaciondesarrollo' => Yii::t('app', ''),
            'idevaluador' => Yii::t('app', ''),
            'idevalados' => Yii::t('app', ''),
            'idevaluaciontipo' => Yii::t('app', ''),
            'realizada' => Yii::t('app', ''),
            'comentarios' => Yii::t('app', ''),
            'fechacrecion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}