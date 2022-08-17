<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_hojavida_metricas".
 *
 * @property integer $id_hvmetrica
 * @property string $hvmetrica
 * @property integer $usua_id
 * @property integer $anulado
 * @property string $fechacreacion
 */
class Hojavidametricas extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_hojavida_metricas';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['usua_id', 'anulado'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['hvmetrica'], 'string', 'max' => 50],
            [['hvhvmetricaroles'],'filter', 'filter' => function($value){
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
            'id_hvmetrica' => Yii::t('app', ''),
            'hvmetrica' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}