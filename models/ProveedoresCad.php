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
class ProveedoresCad extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_proveedores_cad';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['name'], 'string', 'max' => 500]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_proveedorescad' => Yii::t('app', ''),
            'name' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            
        ];
    }
}