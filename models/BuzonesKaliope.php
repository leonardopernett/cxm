<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_buzones_kaliope".
 *
 * @property integer $idbuzonesk
 * @property integer $arbol_id
 * @property string $arbol_name
 * @property string $cod_pcrc
 * @property string $ruta_inicio
 * @property string $fechacreacion
 * @property integer $usua_id
 * @property integer $anulado
 */
class BuzonesKaliope extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_buzones_kaliope';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['arbol_id', 'usua_id', 'anulado'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['arbol_name', 'ruta_inicio'], 'string', 'max' => 250],
            [['cod_pcrc'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idbuzonesk' => Yii::t('app', ''),
            'arbol_id' => Yii::t('app', ''),
            'arbol_name' => Yii::t('app', ''),
            'cod_pcrc' => Yii::t('app', ''),
            'ruta_inicio' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }
}