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
            [['cod_pcrc', 'anulado', 'usua_id'], 'integer'],
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
            'id_glosario' => Yii::t('app', 'Id Glosario'),
            'tipocategoria' => Yii::t('app', 'Tipocategoria'),
            'nombrecategoria' => Yii::t('app', 'Nombrecategoria'),
            'descripcioncategoria' => Yii::t('app', 'Descripcioncategoria'),
            'variablesejemplos' => Yii::t('app', 'Variablesejemplos'),
            'cod_pcrc' => Yii::t('app', 'Cod Pcrc'),
            'anulado' => Yii::t('app', 'Anulado'),
            'usua_id' => Yii::t('app', 'Usua ID'),
            'fechacreacion' => Yii::t('app', 'Fechacreacion'),
            'marca_canal_agente' => Yii::t('app', ' MarcaCanalAgente'),
        ];
    }
}