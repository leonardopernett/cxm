<?php

namespace app\models;

use Yii;
use yii\db\Query;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "tbl_pilares_gptw".
 *
 * @property integer $id_pilares
 * @property string $nombre_pilar
 * @property integer $anulado
 * @property string $fechacreacion
 * @property integer $usua_id
 */
class Pilaresgptw extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_pilares_gptw';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_pilares', 'anulado','usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['nombre_pilar'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_pilares' => Yii::t('app', ''),
            'nombre_pilar' => Yii::t('app', ''),            
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}

