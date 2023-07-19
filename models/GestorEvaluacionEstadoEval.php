<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_gestor_evaluacion_estadoeval".
 *
 * @property int $id_gestor_evaluacion_estadoeval
 * @property string|null $estado
 * @property string|null $fechacreacion
 * @property int|null $usua_id
 * @property int|null $anulado
 */
class GestorEvaluacionEstadoEval extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_gestor_evaluacion_estadoeval';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fechacreacion'], 'safe'],
            [['usua_id', 'anulado'], 'integer'],
            [['estado'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_gestor_evaluacion_estadoeval' => Yii::t('app', ''),
            'estado' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }
}