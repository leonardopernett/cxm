<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_speech_pecservicios".
 *
 * @property integer $id_pecservicios
 * @property integer $id_dp_cliente
 * @property string $cod_pcrc
 * @property string $bolsita
 * @property integer $id_indicador
 * @property integer $id_variable
 * @property string $comentarios
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class Speechpecservicios extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_speech_pecservicios';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_dp_cliente', 'id_indicador', 'id_variable', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['cod_pcrc', 'bolsita'], 'string', 'max' => 50],
            [['comentarios'], 'string', 'max' => 150],
            [['comentarios'],'filter', 'filter' => function($value){
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
            'id_pecservicios' => Yii::t('app', ''),
            'id_dp_cliente' => Yii::t('app', ''),
            'cod_pcrc' => Yii::t('app', ''),
            'bolsita' => Yii::t('app', ''),
            'id_indicador' => Yii::t('app', ''),
            'id_variable' => Yii::t('app', ''),
            'comentarios' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}