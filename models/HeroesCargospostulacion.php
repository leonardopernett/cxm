<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_heroes_cargospostulacion".
 *
 * @property integer $id_cargospostulacion
 * @property string $cargospostulacion
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class HeroesCargospostulacion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_heroes_cargospostulacion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['cargospostulacion'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_cargospostulacion' => Yii::t('app', ''),
            'cargospostulacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}