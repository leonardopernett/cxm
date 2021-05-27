<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

class gestionpreguntas extends \yii\db\ActiveRecord
{

	public static function tableName() {
        return 'tbl_gest_preguntas';
    }

}