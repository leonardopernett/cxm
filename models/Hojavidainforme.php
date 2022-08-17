<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_hojavida_informe".
 *
 * @property integer $id_hvinforme
 * @property string $hvinforme
 * @property integer $usua_id
 * @property integer $anulado
 * @property string $fechacreacion
 */
class Hojavidainforme extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_hojavida_informe';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['usua_id', 'anulado'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['hvinforme'], 'string', 'max' => 50],
            [['hvinforme'],'filter', 'filter' => function($value){
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
            'id_hvinforme' => Yii::t('app', ''),
            'hvinforme' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}