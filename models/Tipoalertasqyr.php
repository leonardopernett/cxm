<?php

namespace app\models;

use Yii;
use yii\db\Query;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "tbl_qr_tipo_alertas".
 *
 * @property integer $id_tipo_alerta
 * @property string $nombre
 * @property string $fechacreacion
 * @property integer $anulado
 * @property integer $usua_id
 */
class Tipoalertasqyr extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_qr_tipo_alertas';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_tipo_alerta', 'anulado','usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['nombre'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_tipo_alerta' => Yii::t('app', ''),
            'nombre' => Yii::t('app', ''),            
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacrecion' => Yii::t('app', ''),
        ];
    }
}
