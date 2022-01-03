<?php

namespace app\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "tbl_control_procesos".
 *
 * @property string $id
 * @property string $identificacion
 * @property string $name
 * @property string $cant_valor
 * @property string $salario
 * @property string $tipo_corte
 * @property integer $responsable
 * @property string $dimensions
 * @property integer $arbol_id
 * @property string $Dedic_valora
 * @property string $fechacreacion
 * @property string $anulado
 */
class ControlProcesos extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_control_procesos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['evaluados_id', 'anulado','idtc'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['salario', 'tipo_corte', 'cant_valor', 'Dedic_valora', 'responsable'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'evaluados_id' => Yii::t('app', ''),
            'salario' => Yii::t('app', ''),
            'tipo_corte' => Yii::t('app', ''),
            'responsable' => Yii::t('app', ''),
            'cant_valor' => Yii::t('app', ''),
            'Dedic_valora' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'idtc' => Yii::t('app', ''),
        ];
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getUsuarios() {
        return $this->hasOne(Usuarios::className(), ['usua_id' => 'evaluados_id']);
    }

    public function getEjecucionformularios(){
        return $this->hasOne(Ejecucionformularios::className(), ['usua_id' => 'evaluados_id']);
    }

    public function getControlparams(){
        return $this->hasOne(ControlParams::className(), ['evaluados_id' => 'evaluados_id']);
    }

    public function getArbols(){
        return $this->hasOne(Arboles::className(), ['arbol_id' => 'id']);
    }	

    /**
     * Metodo que retorna el listado de todos los evaluados
     * 
     * @return array
     * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public static function getEvaluadosList($search) {
        return Usuarios::find()
                        ->select(['usua_id' => 'tbl_control_procesos.id', 'text' => 'UPPER(usua_nombre)'])
                        ->where('usua_nombre LIKE "%' . $search . '%"')
                        ->orderBy('usua_nombre')
                        ->asArray()
                        ->all();
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getMetas($opcion1, $opcion){
        $varId = $opcion1;
        $variableid = $opcion;

	    $txtcorte = Yii::$app->db->createCommand("select tipo_corte from tbl_control_procesos where evaluados_id ='$variableid' and id ='$varId' and anulado = 0")->queryScalar();
        $fechainiC = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where tipocortetc like '%$txtcorte%' and anulado = 0")->queryScalar();
        $fechafinC =  Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where tipocortetc like '%$txtcorte%' and anulado = 0")->queryScalar();

	    $data =  Yii::$app->db->createCommand("select sum(cant_valor) from tbl_control_params where anulado = 0 and evaluados_id = '$variableid' and fechacreacion between '$fechainiC' and '$fechafinC'")->queryScalar();

        return $data;
    }

    public function getMetas1($opcion1, $opcion){
        $varId = $opcion1;
        $variableid = $opcion;

	    $txtcorte = Yii::$app->db->createCommand("select idtc from tbl_control_procesos where evaluados_id ='$variableid' and id ='$varId' and anulado = 0")->queryScalar();
        $fechainiC = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where idtc = '$txtcorte'")->queryScalar();
        $fechafinC =  Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where idtc = '$txtcorte'")->queryScalar();

	    $data =  Yii::$app->db->createCommand("select sum(cant_valor) from tbl_control_params where anulado = 0 and evaluados_id = '$variableid' and fechacreacion between '$fechainiC' and '$fechafinC'")->queryScalar();

        return $data;
    }

    public function getRealizadas($opcion) {
        $variableid = $opcion; 
	$varCero = 0;

            $varMes = date("n");
            $txtMes = null;
            switch ($varMes) {
                case '1':
                    $txtMes = "Enero";
                    break;
                case '2':
                    $txtMes = "Febrero";
                    break;
                case '3':
                    $txtMes = "Marzo";
                    break;
                case '4':
                    $txtMes = "Abril";
                    break;
                case '5':
                    $txtMes = "Mayo";
                    break;
                case '6':
                    $txtMes = "Junio";
                    break;
                case '7':
                    $txtMes = "Julio";
                    break;
                case '8':
                    $txtMes = "Agosto";
                    break;
                case '9':
                    $txtMes = "Septiembre";
                    break;
                case '10':
                    $txtMes = "Octubre";
                    break;
                case '11':
                    $txtMes = "Noviembre";
                    break;
                case '12':
                    $txtMes = "Diciembre";
                    break;
                default:
                    # code...
                    break;
            }   
            $txtMes = "Diciembre";
        $txtcorte = Yii::$app->db->createCommand('select tipo_corte from tbl_control_procesos where evaluados_id ='.$variableid.' and tipo_corte like "%'.$txtMes.'%" and anulado ='.$varCero.'')->queryScalar();
        $fechainiC = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where tipocortetc like '$txtcorte' and anulado = 0")->queryScalar();
        $fechafinC =  Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where tipocortetc like '$txtcorte' and anulado = 0")->queryScalar();                         

        $querys =  new Query;
        $querys     ->select(['tbl_ejecucionformularios.created', 'tbl_usuarios.usua_nombre'])->distinct()
                    ->from('tbl_ejecucionformularios')
                    ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id')
                    //->where(['between','tbl_ejecucionformularios.created', $fechainiC, $fechafinC])
                    ->where("tbl_ejecucionformularios.created between '$fechainiC 00:00:00' and '$fechafinC 23:59:59'")
                    ->andwhere('tbl_usuarios.usua_id = '.$variableid.'');
                    
        $command = $querys->createCommand();
        $queryss = $command->queryAll();   

        $query = count($queryss);
        return $query; 
    }

    public function getCumplimiento($opcion){
        $variableid = $opcion;
	    $varCero = 0;
	    $varsumagestion = 0;
            $varMes = date("n");
            $txtMes = null;
            switch ($varMes) {
                case '1':
                    $txtMes = "Enero";
                    break;
                case '2':
                    $txtMes = "Febrero";
                    break;
                case '3':
                    $txtMes = "Marzo";
                    break;
                case '4':
                    $txtMes = "Abril";
                    break;
                case '5':
                    $txtMes = "Mayo";
                    break;
                case '6':
                    $txtMes = "Junio";
                    break;
                case '7':
                    $txtMes = "Julio";
                    break;
                case '8':
                    $txtMes = "Agosto";
                    break;
                case '9':
                    $txtMes = "Septiembre";
                    break;
                case '10':
                    $txtMes = "Octubre";
                    break;
                case '11':
                    $txtMes = "Noviembre";
                    break;
                case '12':
                    $txtMes = "Diciembre";
                    break;
                default:
                    # code...
                    break;
            }
            $txtMes = "Diciembre";   
        $txtcorte = Yii::$app->db->createCommand('select tipo_corte from tbl_control_procesos where evaluados_id ='.$variableid.' and tipo_corte like "%'.$txtMes.'%" and anulado ='.$varCero.'')->queryScalar();
        $fechainiC = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where tipocortetc like '$txtcorte'  and anulado = 0")->queryScalar();
        $fechafinC =  Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where tipocortetc like '$txtcorte'  and anulado = 0")->queryScalar();    

        $querys =  new Query;
        $querys     ->select(['tbl_ejecucionformularios.created', 'tbl_usuarios.usua_nombre'])->distinct()
                    ->from('tbl_ejecucionformularios')
                    ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id')
                    //->where(['between','tbl_ejecucionformularios.created', $fechainiC, $fechafinC])
                    ->where("tbl_ejecucionformularios.created between '$fechainiC 00:00:00' and '$fechafinC 23:59:59'")
                    ->andwhere('tbl_usuarios.usua_id = '.$variableid.'');
                    
        $command = $querys->createCommand();
        $queryss = $command->queryAll();
        $query2 = count($queryss);

        $txtidtc = Yii::$app->db->createCommand('select idtc from tbl_control_procesos where evaluados_id ='.$variableid.' and tipo_corte like "%'.$txtMes.'%" and anulado ='.$varCero.'')->queryScalar();
	$txtlistidtcs = Yii::$app->db->createCommand("SELECT idtcs FROM tbl_tipos_cortes WHERE idtc = $txtidtc")->queryAll();
	$vararrayidtcs = Array();
        foreach ($txtlistidtcs as $key => $value){
		 array_push($vararrayidtcs, $value['idtcs']);
	}
	
	
	$txtlistacortes = implode("', '", $vararrayidtcs);
	
	$varsumagestion = Yii::$app->db->createCommand("SELECT sum(cantidadjustificar) FROM tbl_plan_escalamientos WHERE tecnicolider = $variableid AND estado = 1 AND idtcs in ('$txtlistacortes')")->queryScalar();
	if ($varsumagestion == null){
		$varsumagestion = 0;
	}

        $query = new Query;
        $query  ->select(['round(('.$query2.' / (sum(tbl_control_params.cant_valor) - '.$varsumagestion.')) * 100) as cumplimiento'])
                ->from('tbl_control_params')
                ->where(['tbl_control_params.anulado' => 'null'])
		->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC])
                //->where("tbl_ejecucionformularios.created between '$fechainiC 00:00:00' and '$fechafinC 23:59:59'")
                ->andwhere('tbl_control_params.evaluados_id = '.$variableid.'');
        $command = $query->createCommand();
        $data = $command->queryAll();

        foreach ($data as $key => $value) {
            $data = $value['cumplimiento'];
            return $data;
        }     
    }

    public function getInicio($opcion){
        $variableid = $opcion;
	$varCero = 0;

        $querys = Yii::$app->db->createCommand('select tipo_corte from tbl_control_procesos where evaluados_id ='.$variableid.' and anulado ='.$varCero.'')->queryScalar();

        $data = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where tipocortetc like '$querys' and anulado = 0")->queryScalar();

        return $data;
    }

    public function getFin($opcion){
        $variableid = $opcion;
	$varCero = 0;

        $querys = Yii::$app->db->createCommand('select tipo_corte from tbl_control_procesos where evaluados_id ='.$variableid.' and anulado ='.$varCero.'')->queryScalar();

        $data = Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where tipocortetc like '$querys' and anulado = 0")->queryScalar();

        return $data;
    }

    public function getRol($opcion1){
        $varId = $opcion1;

        $data = Yii::$app->db->createCommand("select distinct tbl_roles.role_nombre from tbl_roles inner join rel_usuarios_roles on tbl_roles.role_id = rel_usuarios_roles.rel_role_id where         rel_usuarios_roles.rel_usua_id = $varId ")->queryScalar();

        return $data;
    }

    public function getEscaladas($opcion){
        $month = date('m');
        $year = date('Y');
        $day = date("d", mktime(0,0,0, $month+1, 0, $year));
         
       /*  $varfechainicio = date('Y-m-d', mktime(0,0,0, $month, 1, $year));
        $varfechafin = date('Y-m-d', mktime(0,0,0, $month, $day, $year)); */

        $varfechainicio = '2021-12-01';
        $varfechafin = '2022-01-05';

        $data = Yii::$app->db->createCommand("select sum(cantidadjustificar) from tbl_plan_escalamientos where anulado = 0 and tecnicolider = $opcion and Estado = 1 and fechacreacion between '$varfechainicio' and '$varfechafin'")->queryScalar();

        if ($data == null) {
            $data = 0;
        }

        return $data;
    }


}
