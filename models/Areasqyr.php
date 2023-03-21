<?php

namespace app\models;

use Yii;
use yii\db\Query;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "tbl_qr_areas".
 *
 * @property integer $id
 * @property string $nombre
 * @property string $fecha_creacion
 * @property integer $anulado
 * @property integer $usua_id
 */
class Areasqyr extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_qr_areas';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'anulado','usua_id'], 'integer'],
            [['fecha_creacion'], 'safe'],
            [['nombre'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', ''),
            'nombre' => Yii::t('app', ''),            
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacrecion' => Yii::t('app', ''),
        ];
    }
}
