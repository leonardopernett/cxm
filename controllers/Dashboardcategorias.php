<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\Url;

/**
 * This is the model class for table "tbl_dashboardcategorias".
 *
 * @property integer $iddashcategorias
 * @property integer $idcategoria
 * @property string $nombre
 * @property string $tipocategoria
 * @property string $tipoindicador
 * @property string $clientecategoria
 * @property string $fechacreacion
 * @property integer $anulado
 */
class Dashboardcategorias extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_dashboardcategorias';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idcategoria', 'anulado','usua_id', 'usabilidad','iddashservicio'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['nombre', 'tipocategoria', 'tipoindicador', 'clientecategoria','ciudadcategoria','orientacion'], 'string', 'max' => 80]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'iddashcategorias' => Yii::t('app', ''),
            'idcategoria' => Yii::t('app', ''),
            'nombre' => Yii::t('app', ''),
            'tipocategoria' => Yii::t('app', ''),
            'tipoindicador' => Yii::t('app', ''),
            'clientecategoria' => Yii::t('app', ''),
            'ciudadcategoria' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'orientacion' => Yii::t('app', ''),
            'usabilidad' => Yii::t('app', ''),
            'iddashservicio' => Yii::t('app', ''),
        ];
    }



}
