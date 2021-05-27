<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;


/**
 * This is the model class for table "tbl_control_volumenxvalorador".
 *
 * @property integer $idcontrolvxv
 * @property integer $idservicio
 * @property integer $totalrealizadas
 * @property integer $usua_id
 * @property integer $identificacion
 * @property string $nombres
 * @property integer $idtc
 * @property string $mesyear
 * @property integer $anuladovxv
 * @property string $fechacreacion
 */
class Controlvolumenxvalorador extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_control_volumenxvalorador';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idservicio', 'totalrealizadas', 'usua_id', 'identificacion', 'idtc', 'anuladovxv'], 'integer'],
            [['mesyear', 'fechacreacion'], 'safe'],
            [['nombres'], 'string', 'max' => 80]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idcontrolvxv' => Yii::t('app', ''),
            'idservicio' => Yii::t('app', ''),
            'totalrealizadas' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'identificacion' => Yii::t('app', ''),
            'nombres' => Yii::t('app', ''),
            'idtc' => Yii::t('app', ''),
            'mesyear' => Yii::t('app', ''),
            'anuladovxv' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }

}