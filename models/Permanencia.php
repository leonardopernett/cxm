<?php

namespace app\models;

class permanencia extends \yii\db\ActiveRecord
{

	//public $usuario_red;


	public static function tableName() {
        return 'tbl_solicitudes_permanencia';
    }

    public function validarExistencia($mes, $ano, $usuario_red)
    {
    	return $this->find()->where(['usuario_red'=>$usuario_red, "ano"=>$ano, "mes"=>$mes])->one();
    }
}