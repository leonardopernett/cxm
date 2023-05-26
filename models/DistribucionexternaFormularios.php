<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_distribucionexterna_formularios".
 *
 * @property integer $id_formularios
 * @property integer $arbol_id
 * @property integer $sociedad_id
 * @property string $Comentarios
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class DistribucionexternaFormularios extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_distribucionexterna_formularios';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['arbol_id', 'sociedad_id', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['Comentarios'], 'string', 'max' => 300],
            [['Comentarios'],'filter', 'filter' => function($value){
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
            'id_formularios' => Yii::t('app', ''),
            'arbol_id' => Yii::t('app', ''),
            'sociedad_id' => Yii::t('app', ''),
            'Comentarios' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}