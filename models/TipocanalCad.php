<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_directorio_cad".
 *
 * @property integer $id_directorcad
 * @property string $vicepresidente
 * @property string $gerente
 * @property string $sociedad
 * 
 */
class TipocanalCad extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_tipocanal_cad';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['nombre'], 'string', 'max' => 500]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_tipocanalcad' => Yii::t('app', ''),
            'name' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            
        ];
    }
}