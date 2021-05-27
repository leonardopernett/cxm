<?php

namespace app\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "tbl_controlvoc_listadopadre".
 *
 * @property integer $idlistapadrevoc
 * @property integer $idsessionvoc
 * @property string $nombrelistap
 * @property integer $arbol_id
 * @property string $fechacreacion
 * @property integer $anulado
 */
class ControlvocListadopadre extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_controlvoc_listadopadre';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idsessionvoc', 'arbol_id', 'anulado'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['nombrelistap'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idlistapadrevoc' => Yii::t('app', ''),
            'idsessionvoc' => Yii::t('app', ''),
            'nombrelistap' => Yii::t('app', ''),
            'arbol_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }
}