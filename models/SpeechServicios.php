<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_speech_servicios".
 *
 * @property integer $idspeechservicios
 * @property integer $arbol_id
 * @property string $nameArbol
 * @property integer $id_dp_clientes
 * @property string $cliente
 * @property integer $cod_pcrc
 * @property string $pcrc
 * @property string $comentarios
 * @property string $fechacreacion
 * @property integer $anulado
 * @property integer $usua_id
 */
class SpeechServicios extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_speech_servicios';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['arbol_id', 'id_dp_clientes', 'cod_pcrc', 'idllamada', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['nameArbol', 'cliente', 'pcrc', 'comentarios'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idspeechservicios' => Yii::t('app', ''),
            'arbol_id' => Yii::t('app', ''),
            'nameArbol' => Yii::t('app', ''),
            'id_dp_clientes' => Yii::t('app', ''),
            'cliente' => Yii::t('app', ''),
            'cod_pcrc' => Yii::t('app', ''),
            'pcrc' => Yii::t('app', ''),
            'idllamada' => Yii::t('app', ''),
            'comentarios' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}