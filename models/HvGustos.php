<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_hv_gustos".
 *
 * @property integer $id
 * @property string $text
 *
 * @property TblHvInfoGustos[] $tblHvInfoGustos
 */
class HvGustos extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_hv_gustos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['text'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'text' => Yii::t('app', 'Text'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblHvInfoGustos()
    {
        return $this->hasMany(TblHvInfoGustos::className(), ['gustos_id' => 'id']);
    }
}