<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\Url;

/**
 * This is the model class for table "tbl_detalle_pilaresgptw".
 *
 * @property integer $id_detalle_pilar
 * @property integer $id_pilares
 * @property string $nombre
 * @property integer $usua_id
 * @property string $fechacreacion
 * @property integer $anulado
 */
class DetallesPilaresGptw extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_detalle_pilaresgptw';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_pilares', 'usua_id', 'anulado'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['nombre'], 'string', 'max' => 80]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_detalle_pilar' => Yii::t('app', ''),
            'id_pilares' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'nombre' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }
}