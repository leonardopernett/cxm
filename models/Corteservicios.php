<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_cortes_servicios".
 *
 * @property integer $id_corteservicios
 * @property integer $id_corte
 * @property integer $id_servicio
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class Corteservicios extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_cortes_servicios';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_corte', 'id_servicio', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_corteservicios' => Yii::t('app', ''),
            'id_corte' => Yii::t('app', ''),
            'id_servicio' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}