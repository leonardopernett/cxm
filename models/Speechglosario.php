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
 * @property integer $cod_pcrc
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
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
            [['cod_pcrc', 'anulado', 'usua_id','idcategoria'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['tipocategoria'], 'string', 'max' => 100],
            [['nombrecategoria'], 'string', 'max' => 200],
            [['marca_canal_agente'], 'string', 'max' => 30],
            [['descripcioncategoria', 'variablesejemplos'], 'string', 'max' => 500]
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
            'cod_pcrc' => Yii::t('app', ' '),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ' '),
            'fechacreacion' => Yii::t('app', ''),
            'marca_canal_agente' => Yii::t('app', ' '),
            'idcategoria' => Yii::t('app', ''),
        ];
    }
}