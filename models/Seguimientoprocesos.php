<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "tbl_seguimiento_procesos".
 *
 * @property integer $idseguimiento
 * @property integer $evaluados_id
 * @property string $cant_valor
 * @property string $cant_realizadas
 * @property string $cumplimiento
 * @property string $tipo_corte
 * @property string $responsable
 * @property integer $totalcantvalor
 * @property integer $totalcantrealizadas
 * @property string $fechacreacion
 */
class Seguimientoprocesos extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_seguimiento_procesos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['evaluados_id', 'totalcantvalor', 'totalcantrealizadas'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['cant_valor', 'cant_realizadas', 'cumplimiento', 'tipo_corte', 'responsable'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idseguimiento' => Yii::t('app', ''),
            'evaluados_id' => Yii::t('app', ''),
            'cant_valor' => Yii::t('app', ''),
            'cant_realizadas' => Yii::t('app', ''),
            'cumplimiento' => Yii::t('app', ''),
            'tipo_corte' => Yii::t('app', ''),
            'responsable' => Yii::t('app', ''),
            'totalcantvalor' => Yii::t('app', ''),
            'totalcantrealizadas' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }

}
