<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_evaluacion_novedadesgeneral".
 *
 * @property integer $idnovedadesg
 * @property string $documento
 * @property integer $aprobado
 * @property string $fechacrecion
 * @property integer $anulado
 * @property integer $usua_id
 */
class EvaluacionNovedadesgeneral extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_evaluacion_novedadesgeneral';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['aprobado', 'anulado', 'usua_id'], 'integer'],
            [['fechacrecion'], 'safe'],
            [['documento'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idnovedadesg' => Yii::t('app', ''),
            'documento' => Yii::t('app', ''),
            'aprobado' => Yii::t('app', ''),
            'fechacrecion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}