<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_evaluacion_novedadespares".
 *
 * @property integer $idnovedadesp
 * @property string $documento
 * @property string $asunto
 * @property string $comentarios
 * @property string $cambios
 * @property integer $aprobado
 * @property string $aprobadopor
 * @property string $fechacrecion
 * @property integer $anulado
 * @property integer $usua_id
 */
class EvaluacionNovedadespares extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_evaluacion_novedadespares';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['aprobado', 'anulado', 'usua_id'], 'integer'],
            [['fechacrecion'], 'safe'],
            [['documento'], 'string', 'max' => 20],
            [['asunto', 'comentarios', 'cambios'], 'string', 'max' => 200],
            [['aprobadopor'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idnovedadesp' => Yii::t('app', ''),
            'documento' => Yii::t('app', ''),
            'asunto' => Yii::t('app', ''),
            'comentarios' => Yii::t('app', ''),
            'cambios' => Yii::t('app', ''),
            'aprobado' => Yii::t('app', ''),
            'aprobadopor' => Yii::t('app', ''),
            'fechacrecion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}