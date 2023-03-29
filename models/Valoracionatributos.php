<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_valoracion_atributos".
 *
 * @property integer $id_atributo
 * @property integer $id_datogeneral
 * @property integer $id_clientenuevo
 * @property string $atributos
 * @property string $resp_atributos
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class Valoracionatributos extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_valoracion_atributos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_datogeneral', 'id_clientenuevo', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['atributos'], 'string', 'max' => 300],
            [['resp_atributos'], 'string', 'max' => 3]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_atributo' => Yii::t('app', ''),
            'id_datogeneral' => Yii::t('app', ''),
            'id_clientenuevo' => Yii::t('app', ''),
            'atributos' => Yii::t('app', ''),
            'resp_atributos' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}