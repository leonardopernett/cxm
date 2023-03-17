<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_plan_procesos".
 *
 * @property integer $id_procesos
 * @property string $proceso
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class Planprocesos extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_plan_procesos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['proceso'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_procesos' => Yii::t('app', ''),
            'proceso' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}