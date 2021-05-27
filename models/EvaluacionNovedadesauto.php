<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_evaluacion_novedadesauto".
 *
 * @property integer $idnovedades
 * @property string $documento
 * @property string $asunto
 * @property string $comentarios
 * @property string $fechacrecion
 * @property integer $anulado
 * @property integer $usua_id
 */
class EvaluacionNovedadesauto extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_evaluacion_novedadesauto';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fechacrecion'], 'safe'],
            [['anulado', 'usua_id', 'aprobado'], 'integer'],
            [['documento'], 'string', 'max' => 20],
            [['asunto', 'cambios', 'comentarios'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idnovedades' => Yii::t('app', ''),
            'documento' => Yii::t('app', ''),
            'asunto' => Yii::t('app', ''),
            'comentarios' => Yii::t('app', ''),
            'cambios' => Yii::t('app', ''),
            'aprobado' => Yii::t('app', ''),
            'fechacrecion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}