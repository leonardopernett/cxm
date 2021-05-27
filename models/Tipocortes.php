<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_tipocortes".
 *
 * @property integer $idtc
 * @property string $tipocortetc
 * @property string $diastc
 * @property string $fechainiciotc
 * @property string $fechafintc
 * @property integer $cantdiastc
 * @property string $fechacreacion
 * @property integer $incluir
 * @property integer $anulado
 */

class Tipocortes extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_tipocortes';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fechainiciotc', 'fechafintc', 'fechacreacion', 'mesyear'], 'safe'],
            [['cantdiastc', 'incluir', 'anulado', 'idgrupocorte'], 'integer'],
            [['tipocortetc', 'diastc'], 'string', 'max' => 250]
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idtc' => Yii::t('app', ''),
            'tipocortetc' => Yii::t('app', ''),
            'diastc' => Yii::t('app', ''),
            'fechainiciotc' => Yii::t('app', ''),
            'fechafintc' => Yii::t('app', ''),
            'cantdiastc' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'incluir' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'mesyear' => Yii::t('app', ''),
            'idgrupocorte' => Yii::t('app', ''),
        ];
    }


    /**
    * @return \yii\db\ActiveQuery
    */
    public function getTiposdecortes() {
        return $this->hasOne(Tiposdecortes::className(), ['idtc' => 'idtc']);
    }

}
