<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_hojavida_datacivil".
 *
 * @property integer $hv_idcivil
 * @property string $estadocivil
 * @property integer $anulado
 * @property string $fechacreacion
 * @property integer $usua_id
 */
class HojavidaDatacivil extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_hojavida_datacivil';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['estadocivil'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'hv_idcivil' => Yii::t('app', ''),
            'estadocivil' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}