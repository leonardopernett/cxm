<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_hojavida_roles".
 *
 * @property integer $id_hvroles
 * @property string $hvroles
 * @property integer $usua_id
 * @property integer $anulado
 * @property string $fechacreacion
 */
class Hojavidaroles extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_hojavida_roles';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['usua_id', 'anulado'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['hvroles'], 'string', 'max' => 50],
            [['hvroles'],'filter', 'filter' => function($value){
                return filter_var($value,FILTER_SANITIZE_STRING) ;
             }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_hvroles' => Yii::t('app', ''),
            'hvroles' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}