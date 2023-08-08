<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_alertas_tipoalerta".
 *
 * @property integer $id_tipoalerta
 * @property string $tipoalerta
 * @property string $comentarios
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class AlertasTipoalerta extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_alertas_tipoalerta';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['tipoalerta', 'comentarios'], 'string', 'max' => 300]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_tipoalerta' => Yii::t('app', ''),
            'tipoalerta' => Yii::t('app', ''),
            'comentarios' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}