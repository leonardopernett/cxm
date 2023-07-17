<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_heroes_ciudadpostulacion".
 *
 * @property integer $id_ciudadpostulacion
 * @property string $ciudadpostulacion
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class HeroesCiudadpostulacion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_heroes_ciudadpostulacion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['ciudadpostulacion'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_ciudadpostulacion' => Yii::t('app', ''),
            'ciudadpostulacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}