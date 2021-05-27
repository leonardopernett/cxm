<?php

namespace app\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "tbl_controlvoc_listadohijo".
 *
 * @property integer $idlistahijovoc
 * @property integer $idsessionvoc
 * @property integer $idlistapadrevoc
 * @property string $nombrelistah
 * @property string $fechacreacion
 * @property integer $anulado
 */
class ControlvocListadohijo  extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_controlvoc_listadohijo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idsessionvoc', 'idlistapadrevoc', 'anulado'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['nombrelistah'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idlistahijovoc' => Yii::t('app', ''),
            'idsessionvoc' => Yii::t('app', ''),
            'idlistapadrevoc' => Yii::t('app', ''),
            'nombrelistah' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }
}