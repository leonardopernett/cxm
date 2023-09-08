<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_alertas_permisoseliminar".
 *
 * @property integer $id_permisoseliminar
 * @property integer $id_usuario
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class AlertasPermisoseliminar extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_alertas_permisoseliminar';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_usuario', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_permisoseliminar' => Yii::t('app', ''),
            'id_usuario' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}