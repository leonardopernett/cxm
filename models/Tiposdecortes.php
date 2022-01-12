<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "tbl_tipos_cortes".
 *
 * @property integer $idtcs
 * @property string $cortetcs
 * @property string $fechainiciotcs
 * @property string $fechafintcs
 * @property string $diastcs
 * @property integer $cantdias
 * @property integer $idtc
 * @property string $fechacreacion
 */

class Tiposdecortes extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_tipos_cortes';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fechainiciotcs', 'fechafintcs', 'fechacreacion'], 'safe'],
            [['cantdiastcs', 'idtc'], 'integer'],
            [['cortetcs', 'diastcs'], 'string', 'max' => 150],
            [['cortetcs', 'diastcs'], 'filter', 'filter' => function($value){
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
            'idtcs' => Yii::t('app', ''),
            'cortetcs' => Yii::t('app', ''),
            'fechainiciotcs' => Yii::t('app', ''),
            'fechafintcs' => Yii::t('app', ''),
            'diastcs' => Yii::t('app', ''),
            'cantdiastcs' => Yii::t('app', ''),            
            'idtc' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }


    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
    */
    public function ObtenerCorte2($idtc)
    {
        
        $query = Tiposdecortes::find()
                    ->where('idtc ='.$idtc);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($idtc) && $this->validate())) {
             return $dataProvider;
        }

        return $dataProvider;      
    }    
}
