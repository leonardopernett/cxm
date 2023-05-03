<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

class Alertas extends \yii\db\ActiveRecord
{

	public static function tableName() {
        return 'tbl_alertascx';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['fecha', 'pcrc', 'valorador', 'tipo_alerta', 'archivo_adjunto', 'remitentes', 'asunto', 'comentario'], 'required'],
            [['remitentes'], 'string', 'max' => 500],
            [['asunto','remitentes','comentario'], 'filter', 'filter' => function($value){
                return filter_var($value,FILTER_SANITIZE_STRING) ;
             }],
            
        ];
    }

 
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', ''),
            'fecha' => Yii::t('app', 'Tipofeedback ID'),
            'pcrc' => Yii::t('app', 'Formulario ID'),
            'valorador' => Yii::t('app', 'Evaluador'),
            'tipo_alerta' => Yii::t('app', 'Fecha de Creacion del Feedback'),
            'archivo_adjunto' => Yii::t('app', 'Lider de Equipo'),
            'remitentes' => Yii::t('app', 'Evaluado ID'),
            'asunto' => Yii::t('app', 'Snavisar'),
            'comentario' => Yii::t('app', 'Gestionado'),
            
        ];
    }
}