<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_hv_hobbies".
 *
 * @property integer $id
 * @property string $text
 */
class HvHobbies extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_hv_hobbies';
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
            'id' => Yii::t('app', ''),
            'text' => Yii::t('app', ''),
        ];
    }
}