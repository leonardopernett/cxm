<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_hojavida_sociedad".
 *
 * @property integer $id_sociedad
 * @property string $sociedad
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class Hojavidasociedad extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_hojavida_sociedad';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['sociedad'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_sociedad' => Yii::t('app', ''),
            'sociedad' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}