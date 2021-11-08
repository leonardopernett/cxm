<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_hojavida_dataclasificacion".
 *
 * @property integer $hv_idclasificacion
 * @property integer $ciudadclasificacion
 * @property integer $anulado
 * @property string $fechacreacion
 * @property integer $usua_id
 */
class HojavidaDataclasificacion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_hojavida_dataclasificacion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ciudadclasificacion', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'hv_idclasificacion' => Yii::t('app', ''),
            'ciudadclasificacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}