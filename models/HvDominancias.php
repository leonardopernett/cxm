<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_hv_dominancias".
 *
 * @property integer $iddominancia
 * @property string $dominancia
 * @property integer $usua_id
 * @property integer $anulado
 * @property string $fechacreacion
 */
class HvDominancias extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_hv_dominancias';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['usua_id', 'anulado'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['dominancia'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'iddominancia' => Yii::t('app', ''),
            'dominancia' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}