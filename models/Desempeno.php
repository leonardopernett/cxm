<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

class desempeno extends \yii\db\ActiveRecord
{

	public static function tableName() {
        return 'tbl_desempeno';
    }

    public function validarExistencia($mes, $ano, $usuario_red)
    {
    	return $this->find()->where(['usuario_red'=>$usuario_red, "ano"=>$ano, "mes"=>$mes])->one();
    }

    public function traerDatos($fecha, $usuario_red)
    {

    	  $uno = strtotime ( '-4 month' , strtotime ( $fecha ) ) ;
        $uno = date ( 'Y-m' , $uno );
                
       	$unosep = explode("-", $uno);

       	$mes1 = $unosep[1];

       	$dos = strtotime ( '-3 month' , strtotime ( $fecha ) ) ;
        $dos = date ( 'Y-m' , $dos );
                
       	$dossep = explode("-", $dos);

       	$mes2 = $dossep[1];

       	$tres = strtotime ( '-2 month' , strtotime ( $fecha ) ) ;
        $tres = date ( 'Y-m' , $tres );
                
       	$tressep = explode("-", $tres);

       	$mes3 = $tressep[1];

       	$cuatro = strtotime ( '-1 month' , strtotime ( $fecha ) ) ;
        $cuatro = date ( 'Y-m' , $cuatro );
                
       	$cuatrosep = explode("-", $cuatro);

       	$mes4 = $cuatrosep[1];


    	$sql = "SELECT usuario_red, GROUP_CONCAT(
IF(desempeno = '1', CONCAT(DATE_FORMAT(CONCAT(ano,  '-', mes, '-', '00'), '%M-%Y'), ': Deficiente'),''), 
IF(desempeno = '2', CONCAT(DATE_FORMAT(CONCAT(ano,  '-', mes, '-', '00'), '%M-%Y'), ': Fuera de Objetivo'),''), 
IF(desempeno = '3', CONCAT(DATE_FORMAT(CONCAT(ano,  '-', mes, '-', '00'), '%M-%Y'), ': En Objetivo'),''), 
IF(desempeno = '4', CONCAT(DATE_FORMAT(CONCAT(ano,  '-', mes, '-', '00'), '%M-%Y'), ': Sobresaliente'),'') 
SEPARATOR ' / ') desempeno FROM tbl_desempeno WHERE usuario_red = '" . $usuario_red . "' AND (mes = '" . $mes1 . "' OR mes = '" . $mes2 . "' OR mes = '" . $mes3 . "' OR mes = '" . $mes4 . "') GROUP BY usuario_red ORDER BY mes DESC;";
	

	return \Yii::$app->db->createCommand($sql)->queryAll();
    }
    
}