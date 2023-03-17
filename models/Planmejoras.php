<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_plan_mejoras".
 *
 * @property integer $id_conceptos
 * @property integer $id_generalsatu
 * @property string $mejoras
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class Planmejoras extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_plan_mejoras';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_generalsatu', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['mejoras'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_conceptos' => Yii::t('app', ''),
            'id_generalsatu' => Yii::t('app', ''),
            'mejoras' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}