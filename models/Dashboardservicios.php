<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;


/**
 * This is the model class for table "tbl_dashboardservicios".
 *
 * @property integer $iddashboardservicios
 * @property string $nombreservicio
 * @property string $clientecategoria
 * @property integer arbol_id
 * @property integer idservicios
 * @property string $fechacreacion
 * @property integer $anulado
 */
class Dashboardservicios extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_dashboardservicios';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fechacreacion'], 'safe'],
            [['anulado','arbol_id','idservicios'], 'integer'],
            [['nombreservicio', 'clientecategoria'], 'string', 'max' => 80]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'iddashboardservicios' => Yii::t('app', ''),
            'nombreservicio' => Yii::t('app', ''),
            'clientecategoria' => Yii::t('app', ''),
            'arbol_id' => Yii::t('app', ''),
            'idservicios' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }

}