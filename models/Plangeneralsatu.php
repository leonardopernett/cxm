<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_plan_generalsatu".
 *
 * @property integer $id_generalsatu
 * @property integer $id_proceso
 * @property integer $id_actividad
 * @property integer $id_dp_clientes
 * @property integer $id_dp_area
 * @property string $cc_responsable
 * @property string $comentarios
 * @property integer $estado
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class Plangeneralsatu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_plan_generalsatu';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_proceso', 'id_actividad', 'id_dp_clientes', 'anulado', 'usua_id', 'estado', 'id_dp_area'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['cc_responsable', 'comentarios'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_generalsatu' => Yii::t('app', ''),
            'id_proceso' => Yii::t('app', ''),
            'id_actividad' => Yii::t('app', ''),
            'id_dp_clientes' => Yii::t('app', ''),
            'id_dp_area' => Yii::t('app', ''),
            'cc_responsable' => Yii::t('app', ''),
            'comentarios' => Yii::t('app', ''),
            'estado' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}