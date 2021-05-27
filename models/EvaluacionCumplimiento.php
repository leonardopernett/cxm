<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_evaluacion_cumplimiento".
 *
 * @property integer $idevaluacioncumplimiento
 * @property string $cedulaevaluador
 * @property string $cedulaevaluado
 * @property integer $idtipoevalua
 * @property integer $idresultado
 * @property string $directorarea
 * @property string $clientearea
 * @property string $fechamodificacion
 * @property string $fechacreacion
 * @property integer $anulado
 * @property integer $usua_id
 */
class EvaluacionCumplimiento extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_evaluacion_cumplimiento';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idtipoevalua', 'idresultado', 'anulado', 'usua_id'], 'integer'],
            [['fechamodificacion', 'fechacreacion'], 'safe'],
            [['cedulaevaluador', 'cedulaevaluado'], 'string', 'max' => 20],
            [['directorarea', 'clientearea'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idevaluacioncumplimiento' => Yii::t('app', ''),
            'cedulaevaluador' => Yii::t('app', ''),
            'cedulaevaluado' => Yii::t('app', ''),
            'idtipoevalua' => Yii::t('app', ''),
            'idresultado' => Yii::t('app', ''),
            'directorarea' => Yii::t('app', ''),
            'clientearea' => Yii::t('app', ''),
            'fechamodificacion' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}