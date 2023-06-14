<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_genesys_formularios".
 *
 * @property integer $id_genesysformularios
 * @property integer $arbol_id
 * @property string $cola_genesys
 * @property string $id_cola_genesys
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class GenesysFormularios extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_genesys_formularios';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['arbol_id', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['cola_genesys', 'id_cola_genesys'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_genesysformularios' => Yii::t('app', ''),
            'arbol_id' => Yii::t('app', ''),
            'cola_genesys' => Yii::t('app', ''),
            'id_cola_genesys' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}