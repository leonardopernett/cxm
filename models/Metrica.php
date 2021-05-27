<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_metrica".
 *
 * @property integer $id
 * @property string $detexto
 */
class Metrica extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_metrica';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['detexto'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'detexto' => Yii::t('app', 'Detexto'),
        ];
    }
}
