<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_alertas_eliminaralertas".
 *
 * @property integer $id_eliminaralertas
 * @property integer $id_alerta
 * @property string $comentarios
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class AlertasEliminaralertas extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_alertas_eliminaralertas';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_alerta', 'anulado', 'usua_id'], 'integer'],
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
            'id_eliminaralertas' => Yii::t('app', ''),
            'id_alerta' => Yii::t('app', ''),
            'comentarios' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}