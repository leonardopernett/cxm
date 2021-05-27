<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_filtros_formularios".
 *
 * @property integer $id
 * @property integer $usua_id
 * @property string $vista
 * @property string $parametros
 *
 * @property TblUsuarios $usua
 */
class FiltrosFormularios extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_filtros_formularios';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['usua_id', 'vista', 'parametros'], 'required'],
            [['usua_id'], 'integer'],
            [['vista'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'usua_id' => 'Usua ID',
            'vista' => 'Vista',
            'parametros' => 'Parametros',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsua()
    {
        return $this->hasOne(TblUsuarios::className(), ['usua_id' => 'usua_id']);
    }
}
