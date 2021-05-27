<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_basechat_motivos".
 *
 * @property integer $idbaselista
 * @property string $nombrelista
 * @property integer $idlista
 * @property string $fechacreacion
 * @property integer $anulado
 * @property integer $usua_id
 */
class BasechatMotivos extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_basechat_motivos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idlista', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['nombrelista'], 'string', 'max' => 250]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idbaselista' => Yii::t('app', ''),
            'nombrelista' => Yii::t('app', ''),
            'idlista' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}