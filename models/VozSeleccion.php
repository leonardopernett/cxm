<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_voz_seleccion".
 *
 * @property integer $idvozseleccion
 * @property string $ciudad
 * @property integer $iddirectores
 * @property string $gerentes
 * @property string $documentogerentes
 * @property integer $arbol_id
 * @property string $fechacreacion
 * @property integer $usua_id
 * @property integer $anulado
 */
class VozSeleccion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_voz_seleccion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['iddirectores', 'arbol_id', 'usua_id', 'anulado'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['ciudad'], 'string', 'max' => 50],
            [['gerentes'], 'string', 'max' => 80],
            [['documentogerentes'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idvozseleccion' => Yii::t('app', ''),
            'ciudad' => Yii::t('app', ''),
            'iddirectores' => Yii::t('app', ''),
            'gerentes' => Yii::t('app', ''),
            'documentogerentes' => Yii::t('app', ''),
            'arbol_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }
}