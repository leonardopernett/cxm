<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tbl_correogrupal".
 *
 * @property integer $idcg
 * @property string $nombre
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class Correogrupal extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_correogrupal';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['usua_id'], 'integer'],
            [['fechacreacion', 'usua_id'], 'safe'],
            [['nombre'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idcg' => Yii::t('app', ''),
            'nombre' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
    */
    public function getUsuario() {
        return $this->hasMany(Usuarios::className(), ['usua_id' => 'usua_id']);
    }


}