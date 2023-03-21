<?php

namespace app\models;

use Yii;
use yii\db\Query;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "tbl_qr_respuesta_automatica".
 *
 * @property integer $id_areaapoyo
 * @property string $asunto
 * @property string $comentario
 * @property string $parametro
 * @property string $fechacreacion
 * @property integer $anulado
 * @property integer $usua_id
 */
class RespuestaAutomatica extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_qr_respuesta_automatica';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_areaapoyo', 'anulado','usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['parametro','comentario','asunto'], 'string', 'max' => 500]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idusuarioevalua' => Yii::t('app', ''),
            'asunto' => Yii::t('app', ''),
            'comentario' => Yii::t('app', ''),
            'parametro' => Yii::t('app', ''),            
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacrecion' => Yii::t('app', ''),
        ];
    }
}
