<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_control_volumenxencuestasdq".
 *
 * @property integer $idcontrolvxedq
 * @property integer $idservicio
 * @property integer $idtc
 * @property string $fechaencuesta
 * @property string $mesyear
 * @property integer $cantidadvalor
 * @property integer $anuladovxedq
 * @property string $fechacreacion
 */
class ControlVolumenxencuestasdq extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_control_volumenxencuestasdq';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idservicio', 'idtc', 'cantidadvalor', 'anuladovxedq'], 'integer'],
            [['fechaencuesta', 'mesyear', 'fechacreacion'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idcontrolvxedq' => Yii::t('app', ''),
            'idservicio' => Yii::t('app', ''),
            'idtc' => Yii::t('app', ''),
            'fechaencuesta' => Yii::t('app', ''),
            'mesyear' => Yii::t('app', ''),
            'cantidadvalor' => Yii::t('app', ''),
            'anuladovxedq' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}