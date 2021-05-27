<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

class notificaciones extends \yii\db\ActiveRecord
{

	//public $usuario_red;

    public $Desempeno;
    public $namelider;
    public $Nombre;
    public $Identificacion;

	public static function tableName() {
        return 'tbl_notificaciones';
    }

    function validarExistencia($mes, $ano, $usuario_red)
    {
    	return $this->find()->where(['asesor'=>$usuario_red, "ano"=>$ano, "mes"=>$mes])->one();
    }

    function liderEquipo($user_id){
    	$sql = 'SELECT id_lider_equipo FROM tbl_base_satisfaccion WHERE agente = "' . $user_id . '" LIMIT 1';
            $categorias = \Yii::$app->db->createCommand($sql)->queryAll();
            return $categorias;
    }

    function all($fecha1 = NULL, $fecha2 = NULL, $asesor = NULL, $tipo = NULL, $lider = NULL, $identificacion = NULL){

        $query = notificaciones::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        

        $query->select = array('a.*', 'c.name AS Nombre', 'd.usua_nombre AS namelider', 'c.identificacion AS Identificacion', 'IF(b.desempeno = "1", CONCAT(DATE_FORMAT(CONCAT(a.ano,  "-", a.mes, "-", "00"), "%M-%Y"), ": Deficiente"),
IF(b.desempeno = "2", CONCAT(DATE_FORMAT(CONCAT(a.ano,  "-", a.mes, "-", "00"), "%M-%Y"), ": Fuera de Objetivo"),
IF(b.desempeno = "3", CONCAT(DATE_FORMAT(CONCAT(a.ano,  "-", a.mes, "-", "00"), "%M-%Y"), ": En Objetivo"),
IF(b.desempeno = "4", CONCAT(DATE_FORMAT(CONCAT(a.ano,  "-", a.mes, "-", "00"), "%M-%Y"), ": Sobresaliente"),"")))) AS Desempeno');



        $query->from("tbl_notificaciones a");

        $query->join("LEFT JOIN", "tbl_desempeno b", "a.asesor = b.usuario_red");

        $query->join("LEFT JOIN", "tbl_usuarios d", "a.lider = d.usua_usuario");

        $query->join("LEFT JOIN", "tbl_evaluados c", "a.asesor = c.dsusuario_red");



        if($tipo == "asesor"){

        }else{
            $query->andWhere("a.lider = '".Yii::$app->user->identity->username."'");
        }

        if ($fecha1 != "" AND $fecha2 !="") {
            $query->andWhere("a.fecha_ingreso between '".$fecha1."' and '".$fecha2."'");
        }else{

            $fecha = date('Y-m');
                
            $nuevafecha1 = strtotime ( '-1 month' , strtotime ( $fecha ) ) ;
            $nuevafecha1 = date ( 'm' , $nuevafecha1 );

            $nuevafecha2 = strtotime ( '-2 month' , strtotime ( $fecha ) ) ;
            $nuevafecha2 = date ( 'm' , $nuevafecha2 );

            $nuevafecha3 = strtotime ( '-3 month' , strtotime ( $fecha ) ) ;
            $nuevafecha3 = date ( 'm' , $nuevafecha3 );

            $nuevafecha4 = strtotime ( '-4 month' , strtotime ( $fecha ) ) ;
            $nuevafecha4 = date ( 'm' , $nuevafecha4 );
            
            //$separar = explode("-", $nuevafecha);

            //print_r($nuevafecha); die;
            //echo date("m"); die;

            $query->andFilterWhere(['or',
                ['b.mes'=> $nuevafecha1],
                ['b.mes'=> $nuevafecha2],
                ['b.mes'=> $nuevafecha3],
                ['b.mes'=> $nuevafecha4]]);
            }

        if ($asesor != "") {
            $query->andWhere("a.asesor = '".$asesor."'");
        }

        if ($lider != "") {
            $query->andWhere("a.lider = '".$lider."'");
        }

        if ($identificacion != "") {
            $query->andWhere("c.identificacion = '".$identificacion."'");
        }



        $query->groupBY('a.id');

        //$query->andWhere('tmpeje.usua_id = '.Yii::$app->user->identity->id);
        //$query->orderBy("tmpeje.created DESC");
        //echo "<pre>";
        //print_r($dataProvider); die;
        return $dataProvider;
    }

    function prueba($id){
        $query = Notificaciones::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

                    $query->select("*")->from("tbl_notificaciones a")
                        ->join("LEFT JOIN", "tbl_solicitudes_despidos b", "a.id = b.d_id_notificacion")
                        ->join("LEFT JOIN", "tbl_solicitudes_permanencia c", "a.id = c.p_id_notificacion")
                        ->where(['id' => $id])
                        ->asArray()
                        ->all();
        return $dataProvider;
    }

    function coordinador($lideres, $fecha1 = NULL, $fecha2 = NULL, $asesor = NULL){

        //print_r($asesor);
        // $sql = 'SELECT * FROM tbl_notificaciones WHERE lider IN (' . $lideres . ')';
        $sql = Notificaciones::find();
        // echo "<pre>";
        //          print_r($lideres); die;
        // $categorias = \Yii::$app->db->createCommand($sql)->queryAll();
        $categorias = new ActiveDataProvider([
            'query' => $sql,
            ]);

        $sql->select = array('a.*', 'c.name AS Nombre', 'd.usua_nombre AS namelider', 'c.identificacion AS Identificacion', 'IF(b.desempeno = "1", CONCAT(DATE_FORMAT(CONCAT(a.ano,  "-", a.mes, "-", "00"), "%M-%Y"), ": Deficiente"),
IF(b.desempeno = "2", CONCAT(DATE_FORMAT(CONCAT(a.ano,  "-", a.mes, "-", "00"), "%M-%Y"), ": Fuera de Objetivo"),
IF(b.desempeno = "3", CONCAT(DATE_FORMAT(CONCAT(a.ano,  "-", a.mes, "-", "00"), "%M-%Y"), ": En Objetivo"),
IF(b.desempeno = "4", CONCAT(DATE_FORMAT(CONCAT(a.ano,  "-", a.mes, "-", "00"), "%M-%Y"), ": Sobresaliente"),"")))) AS Desempeno');

        $sql->from("tbl_notificaciones a");

        $sql->join("LEFT JOIN", "tbl_desempeno b", "a.asesor = b.usuario_red");

        $sql->join("LEFT JOIN", "tbl_usuarios d", "a.lider = d.usua_usuario");

        $sql->join("LEFT JOIN", "tbl_evaluados c", "a.asesor = c.dsusuario_red");

        $sql->andWhere("notificacion = '3'");

        if ($fecha1 != "" AND $fecha2 !="") {
            $sql->andWhere("a.fecha_ingreso between '".$fecha1."' and '".$fecha2."'");
        }

        if ($asesor != "") {
            $sql->andWhere("a.asesor = '".$asesor."'");
        }

        $sql->andFilterWhere(['or',
                ['b.mes'=> '1'],
                ['b.mes'=> '12'],
                ['b.mes'=> '11'],
                ['b.mes'=> '10']]);
            

        $sql->andwhere(['IN', 'lider', $lideres])->all();

        $sql->groupBY('b.usuario_red');
        //print_r($categorias); die;
        return $categorias;
    }

}