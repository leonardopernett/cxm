<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_speech_glosario".
 *
 * @property integer $id_glosario
 * @property string $tipocategoria
 * @property string $nombrecategoria
 * @property string $descripcioncategoria
 * @property string $variablesejemplos
 * @property string $cod_pcrc
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 * @property string $marca_canal_agente
 * @property string $categoria
 * @property string $indicador
 */
class Speechglosario extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_speech_glosario';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['tipocategoria', 'categoria', 'indicador'], 'string', 'max' => 100],
            [['nombrecategoria'], 'string', 'max' => 200],
            [['descripcioncategoria', 'variablesejemplos'], 'string', 'max' => 500],
            [['cod_pcrc'], 'string', 'max' => 50],
            [['marca_canal_agente'], 'string', 'max' => 30]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_glosario' => Yii::t('app', ''),
            'tipocategoria' => Yii::t('app', ''),
            'nombrecategoria' => Yii::t('app', ''),
            'descripcioncategoria' => Yii::t('app', ''),
            'variablesejemplos' => Yii::t('app', ''),
            'cod_pcrc' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'marca_canal_agente' => Yii::t('app', ''),
            'categoria' => Yii::t('app', ''),
            'indicador' => Yii::t('app', ''),
        ];
    }
}