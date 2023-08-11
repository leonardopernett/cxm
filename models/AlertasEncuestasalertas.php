<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_alertas_encuestasalertas".
 *
 * @property integer $id_encuestasalertas
 * @property integer $id_alerta
 * @property integer $id_tipoencuestas
 * @property string $comentarios
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class AlertasEncuestasalertas extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_alertas_encuestasalertas';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_alerta', 'id_tipoencuestas', 'anulado', 'usua_id'], 'integer'],
            [['comentarios'], 'string'],
            [['fechacreacion'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_encuestasalertas' => Yii::t('app', ''),
            'id_alerta' => Yii::t('app', ''),
            'id_tipoencuestas' => Yii::t('app', ''),
            'comentarios' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}