<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_hojavida_permisoscliente".
 *
 * @property integer $hv_idpermisocliente
 * @property integer $hv_idacciones
 * @property integer $id_dp_clientes
 * @property integer $anulado
 * @property string $fechacreacion
 * @property integer $usua_id
 */
class HojavidaPermisoscliente extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_hojavida_permisoscliente';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hv_idacciones', 'id_dp_clientes', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'hv_idpermisocliente' => Yii::t('app', ''),
            'hv_idacciones' => Yii::t('app', ''),
            'id_dp_clientes' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}