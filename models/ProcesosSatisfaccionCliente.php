<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_procesos_satisfaccion_cliente".
 *
 * @property integer $id_proceso_satis
 * @property string $nombre
 * @property string $fechacreacion
 * @property integer $anulado
 * @property integer $usua_id
 */
class ProcesosSatisfaccionCliente extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_procesos_satisfaccion_cliente';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fechacreacion'], 'safe'],
            [['anulado', 'usua_id'], 'integer'],
            [['nombre'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_proceso_satis' => Yii::t('app', ''),
            'nombre' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}
