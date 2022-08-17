<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_hojavida_periocidad".
 *
 * @property integer $id_hvperiocidad
 * @property string $hvperiocidad
 * @property integer $usua_id
 * @property integer $anulado
 * @property string $fechacreacion
 */
class Hojavidaperiocidad extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_hojavida_periocidad';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['usua_id', 'anulado'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['hvperiocidad'], 'string', 'max' => 50],
            [['hvperiocidad'],'filter', 'filter' => function($value){
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
            'id_hvperiocidad' => Yii::t('app', ''),
            'hvperiocidad' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}