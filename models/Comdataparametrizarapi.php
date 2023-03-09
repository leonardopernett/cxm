<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_comdata_parametrizarapi".
 *
 * @property integer $id_parametrizarapi
 * @property integer $id_dp_clientes
 * @property string $sociedadprovieniente
 * @property string $proyecto_id
 * @property string $dataset_id
 * @property string $table_id
 * @property string $limit
 * @property string $offset
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class Comdataparametrizarapi extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_comdata_parametrizarapi';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_dp_clientes', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['sociedadprovieniente', 'proyecto_id', 'dataset_id', 'table_id', 'limit', 'offset'], 'string', 'max' => 150],
            [['sociedadprovieniente', 'proyecto_id', 'dataset_id', 'table_id', 'limit', 'offset'],'filter', 'filter' => function($value){
                return filter_var($value,FILTER_SANITIZE_STRING) ;
             }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_parametrizarapi' => Yii::t('app', ''),
            'id_dp_clientes' => Yii::t('app', ''),
            'sociedadprovieniente' => Yii::t('app', ''),
            'proyecto_id' => Yii::t('app', ''),
            'dataset_id' => Yii::t('app', ''),
            'table_id' => Yii::t('app', ''),
            'limit' => Yii::t('app', ''),
            'offset' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}