<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_formvoc_acciones".
 *
 * @property integer $idformvocacciones
 * @property integer $idacciones
 * @property integer $iddetalle
 * @property string $acciones
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class FormvocAcciones extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_formvoc_acciones';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idacciones', 'iddetalle', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['acciones'], 'string', 'max' => 100]
            
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idformvocacciones' => Yii::t('app', ''),
            'idacciones' => Yii::t('app', ''),
            'iddetalle' => Yii::t('app', ''),
            'acciones' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}