<?php

namespace app\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "tbl_detalle_momento_bit_uni".
 *
 * @property integer $id_detalle_momento
 * @property integer $id_momento
 * @property string $detalle_momento
 * @property integer $usua_id
 * @property string $fechacreacion
 * @property integer $anulado
 */
class Controldetallemomento extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_detalle_momento_bit_uni';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fechacreacion'], 'safe'],
            [['anulado'], 'integer'],
            [['id_momento'], 'integer'],
            [['usua_id'], 'integer'],
            [['detalle_momento'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_detalle_momento' => Yii::t('app', ''),
            'id_momento' => Yii::t('app', ''),
            'detalle_momento' => Yii::t('app', ''),            
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }
}