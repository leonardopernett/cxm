<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_plan_eficacia".
 *
 * @property integer $id_eficacia
 * @property integer $id_generalsatu
 * @property string $eficacia
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class Planeficacia extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_plan_eficacia';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_generalsatu', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['eficacia'], 'string', 'max' => 250]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_eficacia' => Yii::t('app', ''),
            'id_generalsatu' => Yii::t('app', ''),
            'eficacia' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}