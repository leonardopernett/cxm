<?php

namespace app\models;

use Yii;
use yii\db\Query;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "tbl_areasapoyo_gptw".
 *
 * @property integer $id_areaapoyo
 * @property string $nombre
 * @property string $fechacreacion
 * @property integer $anulado
 * @property integer $usua_id
 */
class AreaGptw extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_areasapoyo_gptw';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_areaapoyo', 'anulado','usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['nombre'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idusuarioevalua' => Yii::t('app', ''),
            'nombre' => Yii::t('app', ''),            
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacrecion' => Yii::t('app', ''),
        ];
    }
}
