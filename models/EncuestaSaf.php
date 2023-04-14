<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_encuesta_saf".
 *
 * @property integer $id_encuesta_saf
 * @property integer $id_alerta
 * @property integer $resp_encuesta_saf
 * @property string $comentario_saf
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class EncuestaSaf extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_encuesta_saf';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_alerta', 'resp_encuesta_saf', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['comentario_saf'], 'string', 'max' => 300]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_encuesta_saf' => Yii::t('app', ''),
            'id_alerta' => Yii::t('app', ''),
            'resp_encuesta_saf' => Yii::t('app', ''),
            'comentario_saf' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}