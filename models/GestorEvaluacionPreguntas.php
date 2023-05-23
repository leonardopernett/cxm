<?php

namespace app\models;

use yii\base\Model;

class GestorEvaluacionPreguntas extends Model
{
    public $nombrepregunta;
    public $descripcionpregunta;

    public function rules()
    {
        return [
            [['nombrepregunta', 'descripcionpregunta'], 'required'],
            ['nombrepregunta', 'string', 'max' => 255],
            ['descripcionpregunta', 'string', 'max' => 1500],
        ];
    }

    public function attributeLabels()
    {
        return [
            'nombrepregunta' => 'Pregunta',
            'descripcionpregunta' => 'Descripción',
        ];
    }

}