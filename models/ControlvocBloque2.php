<?php

namespace app\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "tbl_controlvoc_bloque2".
 *
 * @property integer $idbloque2
 * @property integer $idbloque1
 * @property integer $indicadorglobal
 * @property integer $variable
 * @property integer $moticocontacto
 * @property integer $motivollamadas
 * @property integer $puntodolor
 * @property string $categoria
 * @property string $ajustecategoia
 * @property string $indicadorvar
 * @property integer $agente
 * @property integer $marca
 * @property integer $canal
 * @property string $detalle
 * @property integer $mapa1
 * @property integer $mapa2
 * @property string $fechacreacion
 * @property integer $anulado
 */
class ControlvocBloque2 extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_controlvoc_bloque2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idbloque1', 'indicadorglobal', 'variable', 'moticocontacto', 'motivollamadas', 'puntodolor', 'agente', 'marca', 'canal', 'mapa1', 'mapa2', 'responsabilidad', 'anulado'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['categoria', 'ajustecategoia', 'indicadorvar', 'detalle'], 'string', 'max' => 50]
        ];   
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idbloque2' => Yii::t('app', ''),
            'idbloque1' => Yii::t('app', ''),
            'indicadorglobal' => Yii::t('app', ''),
            'variable' => Yii::t('app', ''),
            'moticocontacto' => Yii::t('app', ''),
            'motivollamadas' => Yii::t('app', ''),
            'puntodolor' => Yii::t('app', ''),
            'categoria' => Yii::t('app', ''),
            'ajustecategoia' => Yii::t('app', ''),
            'indicadorvar' => Yii::t('app', ''),
            'agente' => Yii::t('app', ''),
            'marca' => Yii::t('app', ''),
            'canal' => Yii::t('app', ''),
            'detalle' => Yii::t('app', ''),
            'mapa1' => Yii::t('app', ''),
            'mapa2' => Yii::t('app', ''),,
            'interesados' => Yii::t('app', ''),
            'responsabilidad' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }
}