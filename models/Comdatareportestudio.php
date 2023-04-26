<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_comdata_reportestudio".
 *
 * @property integer $id_reportestudio
 * @property integer $id_dp_clientes
 * @property string $cod_pcrc
 * @property string $extension
 * @property string $url
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class Comdatareportestudio extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_comdata_reportestudio';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_dp_clientes', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['cod_pcrc', 'extension'], 'string', 'max' => 100],
            [['url'], 'string', 'max' => 300]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_reportestudio' => Yii::t('app', ''),
            'id_dp_clientes' => Yii::t('app', ''),
            'cod_pcrc' => Yii::t('app', ''),
            'extension' => Yii::t('app', ''),
            'url' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}