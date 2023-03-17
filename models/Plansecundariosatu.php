<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_plan_secundariosatu".
 *
 * @property integer $id_secundariosatu
 * @property integer $id_generalsatu
 * @property string $fecha_implementacion
 * @property string $fecha_definicion
 * @property string $fecha_cierre
 * @property integer $indicador
 * @property string $acciones
 * @property string $puntaje_meta
 * @property string $puntaje_actual
 * @property string $puntaje_final
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class Plansecundariosatu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_plan_secundariosatu';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_generalsatu', 'indicador', 'anulado', 'usua_id'], 'integer'],
            [['fecha_implementacion', 'fecha_definicion', 'fecha_cierre', 'fechacreacion'], 'safe'],
            [['acciones'], 'string', 'max' => 100],
            [['puntaje_meta', 'puntaje_actual', 'puntaje_final'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_secundariosatu' => Yii::t('app', ''),
            'id_generalsatu' => Yii::t('app', ''),
            'fecha_implementacion' => Yii::t('app', ''),
            'fecha_definicion' => Yii::t('app', ''),
            'fecha_cierre' => Yii::t('app', ''),
            'indicador' => Yii::t('app', ''),
            'acciones' => Yii::t('app', ''),
            'puntaje_meta' => Yii::t('app', ''),
            'puntaje_actual' => Yii::t('app', ''),
            'puntaje_final' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}