<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_heroes_tipopostulacion".
 *
 * @property integer $id_tipopostulacion
 * @property string $tipopostulacion
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class HeroesTipopostulacion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_heroes_tipopostulacion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['tipopostulacion'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_tipopostulacion' => Yii::t('app', ''),
            'tipopostulacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}