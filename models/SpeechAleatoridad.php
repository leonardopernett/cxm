<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_speech_aleatoridad".
 *
 * @property integer $idaleatorio
 * @property integer $cantidad
 * @property integer $id_dp_clientes
 * @property string $cod_pcrc
 * @property string $comentarios
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class SpeechAleatoridad extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_speech_aleatoridad';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cantidad', 'id_dp_clientes', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['cod_pcrc'], 'string', 'max' => 100],
            [['comentarios'], 'string', 'max' => 300],
            [['cod_pcrc','comentarios'], 'filter', 'filter' => function($value){
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
            'idaleatorio' => Yii::t('app', ''),
            'cantidad' => Yii::t('app', ''),
            'id_dp_clientes' => Yii::t('app', ''),
            'cod_pcrc' => Yii::t('app', ''),
            'comentarios' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}