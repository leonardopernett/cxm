<?php

namespace app\models;

use Yii;
use yii\db\Query;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "tbl_indicadores_satisfaccion_cliente".
 *
 * @property integer $id_indicador
 * @property string $nombre
 * @property integer $anulado
 * @property string $fechacreacion
 * @property integer $usua_id
 */
class IndicadorSatisfaccion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_indicadores_satisfaccion_cliente';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_indicador', 'anulado','usua_id'], 'integer'],
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
            'id_indicador' => Yii::t('app', ''),
            'nombre' => Yii::t('app', ''),            
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}

