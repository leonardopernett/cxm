<?php

namespace app\models;

use Yii;
use yii\db\Query;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "tbl_evaluacion_nivel".
 *
 * @property integer $idevaluacionnivel
 * @property integer $nivel
 * @property integer $cargo
 * @property string $nombrecargo
 * @property string $fechacrecion
 * @property integer $anulado
 * @property integer $usua_id
 */
class EvaluacionNivel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_evaluacion_nivel';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nivel', 'cargo', 'anulado', 'usua_id'], 'integer'],
            [['fechacrecion'], 'safe'],
            [['nombrecargo'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idevaluacionnivel' => Yii::t('app', ''),
            'nivel' => Yii::t('app', ''),
            'cargo' => Yii::t('app', ''),
            'nombrecargo' => Yii::t('app', ''),
            'fechacrecion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}
