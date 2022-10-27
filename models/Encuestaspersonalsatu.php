<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_encuestas_personalsatu".
 *
 * @property integer $id_personalsatu
 * @property integer $id_dp_posicion
 * @property string $posicion
 * @property string $personalsatu
 * @property string $documentopersonalsatu
 * @property string $correosatu
 * @property string $movilsatu
 * @property integer $usua_id_satu
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class Encuestaspersonalsatu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_encuestas_personalsatu';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_personalsatu'], 'required'],
            [['id_personalsatu', 'id_dp_posicion', 'usua_id_satu', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['posicion'], 'string', 'max' => 50],
            [['personalsatu', 'correosatu', 'movilsatu'], 'string', 'max' => 100],
            [['documentopersonalsatu'], 'string', 'max' => 20],
            [['posicion','personalsatu', 'correosatu', 'movilsatu','documentopersonalsatu'],'filter', 'filter' => function($value){
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
            'id_personalsatu' => Yii::t('app', ''),
            'id_dp_posicion' => Yii::t('app', ''),
            'posicion' => Yii::t('app', ''),
            'personalsatu' => Yii::t('app', ''),
            'documentopersonalsatu' => Yii::t('app', ''),
            'correosatu' => Yii::t('app', ''),
            'movilsatu' => Yii::t('app', ''),
            'usua_id_satu' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}