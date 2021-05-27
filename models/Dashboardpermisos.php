<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\Url;

/**
 * This is the model class for table "tbl_dashboardpermisos".
 *
 * @property integer $iddashboardpermisos
 * @property integer $iddashservicio
 * @property integer $usuaid
 * @property string $nombreservicio
 * @property string $fechacreacion
 * @property integer $anulado
 */
class Dashboardpermisos extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_dashboardpermisos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['iddashservicio', 'usuaid', 'anulado'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['nombreservicio'], 'string', 'max' => 80]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'iddashboardpermisos' => Yii::t('app', ''),
            'iddashservicio' => Yii::t('app', ''),
            'usuaid' => Yii::t('app', ''),
            'nombreservicio' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }
}