<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_alertas_tipoencuestas".
 *
 * @property integer $id_tipoencuestas
 * @property string $tipoencuestas
 * @property integer $peso
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class AlertasTipoencuestas extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_alertas_tipoencuestas';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['peso', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['tipoencuestas'], 'string', 'max' => 300]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_tipoencuestas' => Yii::t('app', ''),
            'tipoencuestas' => Yii::t('app', ''),
            'peso' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}