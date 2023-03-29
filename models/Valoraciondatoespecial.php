<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_valoracion_datoespecial".
 *
 * @property integer $id_datoespecial
 * @property integer $id_clientenuevo
 * @property string $campo_especial
 * @property string $item_especial
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class Valoraciondatoespecial extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_valoracion_datoespecial';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_clientenuevo', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['campo_especial', 'item_especial'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_datoespecial' => Yii::t('app', ''),
            'id_clientenuevo' => Yii::t('app', ''),
            'campo_especial' => Yii::t('app', ''),
            'item_especial' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}