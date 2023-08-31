<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_base_aleatorio".
 *
 * @property integer $id_aleatorio
 * @property integer $arbol_id
 * @property integer $form_id
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class BaseAleatorio extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_base_aleatorio';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['arbol_id', 'form_id', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_aleatorio' => Yii::t('app', ''),
            'arbol_id' => Yii::t('app', ''),
            'form_id' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}