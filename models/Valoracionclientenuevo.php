<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_valoracion_clientenuevo".
 *
 * @property integer $id_clientenuevo
 * @property integer $id_dp_clientes
 * @property integer $id_sociedad
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class Valoracionclientenuevo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_valoracion_clientenuevo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_dp_clientes', 'id_sociedad', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_clientenuevo' => Yii::t('app', ''),
            'id_dp_clientes' => Yii::t('app', ''),
            'id_sociedad' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}