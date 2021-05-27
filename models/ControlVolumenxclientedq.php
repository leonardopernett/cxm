<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_control_volumenxclientedq".
 *
 * @property integer $idcontrolvxcdq
 * @property integer $idservicio
 * @property integer $idtc
 * @property string $fechavaloracion
 * @property string $mesyear
 * @property integer $cantidadvalor
 * @property string $fechacreacion
 * @property integer $anuladovxc
 */
class ControlVolumenxclientedq extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_control_volumenxclientedq';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idservicio', 'idtc', 'cantidadvalor', 'anuladovxc'], 'integer'],
            [['fechavaloracion', 'mesyear', 'fechacreacion'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idcontrolvxcdq' => Yii::t('app', ''),
            'idservicio' => Yii::t('app', ''),
            'idtc' => Yii::t('app', ''),
            'fechavaloracion' => Yii::t('app', ''),
            'mesyear' => Yii::t('app', ''),
            'cantidadvalor' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anuladovxc' => Yii::t('app', ''),
        ];
    }
}