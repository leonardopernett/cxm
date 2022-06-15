<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_responsabilidad_manual".
 *
 * @property integer $id_responsabilidad
 * @property integer $arbol_id
 * @property string $responsabilidad
 * @property string $tipo
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class ResponsabilidadManual extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_responsabilidad_manual';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['arbol_id', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['responsabilidad'], 'string', 'max' => 100],
            [['tipo'], 'string', 'max' => 50],
            [['responsabilidad', 'tipo'],'filter', 'filter' => function($value){
                return filter_var($value,FILTER_SANITIZE_STRING) ;
             }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_responsabilidad' => Yii::t('app', ''),
            'arbol_id' => Yii::t('app', ''),
            'responsabilidad' => Yii::t('app', ''),
            'tipo' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}