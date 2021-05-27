<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_evaluacion_novedadeslog".
 *
 * @property integer $idnovedadeslog
 * @property string $evaluadorid
 * @property string $evaluado
 * @property string $tipo_eva
 * @property string $asunto
 * @property string $comentarios
 * @property integer $aprobado
 * @property string $fechacrecion
 * @property integer $anulado
 * @property integer $usua_id
 */
class EvaluacionNovedadeslog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_evaluacion_novedadeslog';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['aprobado', 'anulado', 'usua_id'], 'integer'],
            [['fechacrecion'], 'safe'],
            [['evaluadorid'], 'string', 'max' => 20],
            [['evaluado', 'tipo_eva', 'asunto', 'comentarios'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idnovedadeslog' => Yii::t('app', ''),
            'evaluadorid' => Yii::t('app', ''),
            'evaluado' => Yii::t('app', ''),
            'tipo_eva' => Yii::t('app', ''),
            'asunto' => Yii::t('app', ''),
            'comentarios' => Yii::t('app', ''),
            'aprobado' => Yii::t('app', ''),
            'fechacrecion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}