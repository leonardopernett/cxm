<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\Url;

/**
 * This is the model class for table "tbl_dashboardspeechcalls".
 *
 * @property integer $iddashboardspeechcalls
 * @property integer $callId
 * @property integer $idcategoria
 * @property string $nombreCategoria
 * @property string $extension
 * @property string $login_id
 * @property string $fechallamada
 * @property integer $callduracion
 * @property string $servicio
 * @property string $fechareal
 * @property string $idredbox
 * @property string $fechacreacion
 * @property integer $anulado
 */
class Dashboardspeechcalls extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_dashboardspeechcalls';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['callId', 'idcategoria', 'callduracion', 'anulado'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['nombreCategoria', 'extension', 'fechallamada', 'servicio', 'fechareal', 'idredbox'], 'string', 'max' => 80],
            [['login_id'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'iddashboardspeechcalls' => Yii::t('app', ''),
            'callId' => Yii::t('app', ''),
            'idcategoria' => Yii::t('app', ''),
            'nombreCategoria' => Yii::t('app', ''),
            'extension' => Yii::t('app', ''),
            'login_id' => Yii::t('app', ''),
            'fechallamada' => Yii::t('app', ''),
            'callduracion' => Yii::t('app', ''),
            'servicio' => Yii::t('app', ''),
            'fechareal' => Yii::t('app', ''),
            'idredbox' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }

    public function buscarsllamadas($params1,$params2,$params3,$params4,$varcategoriass,$varidloginid,$paramscalls,$varlider,$varasesor){
        $txtprograma = $params1;
        $txtextension = $params2;
        $txtfechainicio = $params3;
        $txtfechafin = $params4;
        $txtcategoria = $varcategoriass;        
        $txtcontieneno = $varidloginid;
        $txtllamadas = $paramscalls;
        $txtlideres = $varlider;
        $txtasesores = $varasesor;

        if ($txtasesores == "") {
            $txtresultadoasesor = Yii::$app->db->createCommand("select distinct e.dsusuario_red from tbl_evaluados e     inner join tbl_equipos_evaluados ee on e.id = ee.evaluado_id where ee.equipo_id in ('$txtlideres') and e.dsusuario_red not like '%usar%'")->queryAll();

            $arraylistasesores = array();
            foreach ($txtresultadoasesor as $key => $value) {
                array_push($arraylistasesores, $value['dsusuario_red']);
            }
            $txtarrayasesores = implode("', '", $arraylistasesores);
        }else{
            $txtarrayasesores = $txtasesores;
        }


        if ($txtcontieneno == "1") {
            if ($txtlideres == "" && $txtasesores == "") {

                $query = Dashboardspeechcalls::find()
                    ->where("anulado = 0")
                    ->andwhere("servicio in ('$txtprograma')")
                    ->andwhere("fechallamada between '$txtfechainicio' and '$txtfechafin'")
                    ->andwhere("extension in ('$txtextension')")
                    ->andwhere("idcategoria in ($txtcategoria)")
                    ->groupBy("callId")
                    ->orderBy([
                              'fechallamada' => SORT_DESC
                            ]);
            }else{
                $query = Dashboardspeechcalls::find()
                    ->where("anulado = 0")
                    ->andwhere("servicio in ('$txtprograma')")
                    ->andwhere("fechallamada between '$txtfechainicio' and '$txtfechafin'")
                    ->andwhere("extension in ('$txtextension')")
                    ->andwhere("idcategoria in ($txtcategoria)")
                    ->andwhere("login_id in ('$txtarrayasesores')")
                    ->groupBy("callId")
                    ->orderBy([
                              'fechallamada' => SORT_DESC
                            ]);
            }
        }else{
            $varlistcallid = Yii::$app->db->createCommand("select callId from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$txtprograma') and fechallamada between '$txtfechainicio' and '$txtfechafin' and extension in ('$txtextension') and idcategoria in ($txtcategoria) group by callId")->queryAll();
            $txtarraylistcallid = array();
            foreach ($varlistcallid as $key => $value) {
                array_push($txtarraylistcallid, $value['callId']);
            }
            $arraycallids = implode(", ", $txtarraylistcallid);

            if ($txtlideres == "" && $txtasesores == "") {
                $query = Dashboardspeechcalls::find()
                    ->where("anulado = 0")
                    ->andwhere("servicio in ('$txtprograma')")
                    ->andwhere("fechallamada between '$txtfechainicio' and '$txtfechafin'")
                    ->andwhere("extension in ('$txtextension')")
                    ->andwhere("idcategoria in ($txtllamadas)")
                    ->andwhere("callId not in ($arraycallids)")
                    ->groupBy("callId")
                    ->orderBy([
                              'fechallamada' => SORT_DESC
                            ]);
            }else{
                $query = Dashboardspeechcalls::find()
                    ->where("anulado = 0")
                    ->andwhere("servicio in ('$txtprograma')")
                    ->andwhere("fechallamada between '$txtfechainicio' and '$txtfechafin'")
                    ->andwhere("extension in ('$txtextension')")
                    ->andwhere("idcategoria in ($txtllamadas)")
                    ->andwhere("callId not in ($arraycallids)")
                    ->andwhere("login_id in ('$txtarrayasesores')")
                    ->groupBy("callId")
                    ->orderBy([
                              'fechallamada' => SORT_DESC
                            ]);
            }
            
        }

        
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $dataProvider;
    }

    public function buscarsllamadasmodel($params1,$params2,$params3,$params4,$paramscalls){
        $txtprograma = $params1;
        $txtextension = $params2;
        $txtfechainicio = $params3;
        $txtfechafin = $params4;
        $txtllamadas = $paramscalls;

        $query = Dashboardspeechcalls::find()
                    ->where("anulado = 0")
                    ->andwhere("servicio in ('$txtprograma')")
                    ->andwhere("fechallamada between '$txtfechainicio' and '$txtfechafin'")
                    ->andwhere("extension in ('$txtextension')")
                    ->andwhere("idcategoria in ('$txtllamadas')")
                    ->orderBy([
                              'fechallamada' => SORT_DESC
                            ]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $dataProvider;
    }

    public function getsencuestas($opcion){
        $varconnId = $opcion;
        $data = null;

        if ($varconnId != "") {
            $idbase = Yii::$app->db->createCommand("select b.id from tbl_base_satisfaccion b where b.connid in ('$varconnId')")->queryScalar();

            if ($idbase == "") {
                $data = "--";
            }else{
                $data = $idbase;
            }
        }else{
            $data = "--";
        }
        
        return $data;
    }

    public function getstipologia($opcion){
        $varconnId = $opcion;
        $data = null;

        if ($varconnId != "") {
            $idbase = Yii::$app->db->createCommand("select b.tipologia from tbl_base_satisfaccion b where b.connid in ('$varconnId')")->queryScalar();

            if ($idbase == "") {
                $data = "--";
            }else{
                $data = $idbase;
            }
        }else{
            $data = "--";
        }
        

        return $data;
    }

    public function getsbuzon($opcion){
        $varconnId = $opcion;
        $data = null;

        if ($varconnId != "") {
            $idbase = Yii::$app->db->createCommand("select b.buzon from tbl_base_satisfaccion b where b.connid in ('$varconnId')")->queryScalar();

            if ($idbase == "") {
                $data = "--";
            }else{
                $data = "Si";
            }
        }else{
            $data = "--";
        }
        

        return $data;
    }

    public function getsestado($opcion){
        $idspeech = $opcion;
        $data = null;


	$concatenarspeech = Yii::$app->db->createCommand("SELECT DISTINCT CONCAT(d.callId,'; ',d.fechareal) FROM  tbl_dashboardspeechcalls d WHERE d.iddashboardspeechcalls in ('$idspeech')")->queryScalar();

        $txttempejecucion = Yii::$app->db->createCommand("SELECT COUNT(te.id) FROM tbl_tmpejecucionformularios te WHERE te.dsfuente_encuesta like '%$concatenarspeech%'")->queryScalar();

        $txtejecucion = Yii::$app->db->createCommand("SELECT COUNT(te.id) FROM tbl_ejecucionformularios te WHERE te.dsfuente_encuesta like '%$concatenarspeech%'")->queryScalar();

        if ($txttempejecucion == 0 && $txtejecucion == 0) {
            $data = "Abierto";
        }else{
            if ($txttempejecucion == 1 && $txtejecucion == 0) {
                $data = "En Proceso";
            }else{
                if ($txttempejecucion == 0 && $txtejecucion == 1) {
                    $data = "Cerrado";
                }
            }
        }

        return $data;
    }

    public function getsresposanble($opcion){
        $idspeech = $opcion;
        $data = null;

        $concatenarspeech = Yii::$app->db->createCommand("SELECT DISTINCT CONCAT(d.callId,'; ',d.fechareal) FROM  tbl_dashboardspeechcalls d WHERE d.iddashboardspeechcalls in ('$idspeech')")->queryScalar();

        $txttempejecucion = Yii::$app->db->createCommand("SELECT COUNT(te.id) FROM tbl_tmpejecucionformularios te WHERE te.dsfuente_encuesta like '%$concatenarspeech%'")->queryScalar();

        $txtejecucion = Yii::$app->db->createCommand("SELECT COUNT(te.id) FROM tbl_ejecucionformularios te WHERE te.dsfuente_encuesta like '%$concatenarspeech%'")->queryScalar();

        if ($txttempejecucion == 0 && $txtejecucion == 0) {
            $data = "--";
        }else{
            if ($txttempejecucion == 1 && $txtejecucion == 0) {
                $data = Yii::$app->db->createCommand("SELECT DISTINCT u.usua_nombre FROM tbl_usuarios u INNER JOIN tbl_tmpejecucionformularios te ON u.usua_id = te.usua_id WHERE te.dsfuente_encuesta like '%$concatenarspeech%'")->queryScalar();
            }else{
                if ($txttempejecucion == 0 && $txtejecucion == 1) {
                    $data = Yii::$app->db->createCommand("SELECT DISTINCT u.usua_nombre FROM tbl_usuarios u INNER JOIN tbl_ejecucionformularios te ON u.usua_id = te.usua_id WHERE te.dsfuente_encuesta like '%$concatenarspeech%'")->queryScalar();
                }
            }
        }

        return $data;
    }

    public function getsmarca($opcion){
        $idspeech = $opcion;
        $data = null;

        $varcategorias = Yii::$app->db->createCommand("SELECT d.idcategoria FROM tbl_dashboardspeechcalls d WHERE d.anulado = 0 AND d.iddashboardspeechcalls = $idspeech")->queryScalar();
        
        if ($varcategorias == '1114' || $varcategorias == '1105') {
            $data = '--';
        }else{
            $varcallid = Yii::$app->db->createCommand("SELECT d.callId FROM tbl_dashboardspeechcalls d WHERE d.anulado = 0 AND d.iddashboardspeechcalls = $idspeech")->queryScalar();

            $varlistone = Yii::$app->db->createCommand("SELECT sg.idvariable FROM tbl_speech_general sg WHERE sg.anulado = 0 AND sg.callid = $varcallid")->queryAll();

            $varprograma = Yii::$app->db->createCommand("SELECT sg.programacliente FROM tbl_speech_general sg WHERE sg.anulado = 0 AND sg.callid = $varcallid")->queryScalar();

            $vararraycategoria = array();
            foreach ($varlistone as $key => $value) {
                array_push($vararraycategoria, $value['idvariable']);
            }
            $varscategorias = implode(", ", $vararraycategoria);

            if ($varscategorias != "") {
                $varcount = Yii::$app->db->createCommand("SELECT distinct sc.nombre FROM tbl_speech_categorias sc WHERE sc.anulado = 0 AND sc.programacategoria IN ('$varprograma')  AND sc.idcategoria IN ($varscategorias) AND sc.responsable = 3 AND sc.componentes = 1")->queryScalar();

                if ($varcount == "") {
                    $data = "--";
                }else{
                    $data = $varcount;
                }
            }else{
                $data = "--";
            }
            
            
        }

        return $data;
    }

    public function getscanal($opcion){
        $idspeech = $opcion;
        $data = null;

        $varcategorias = Yii::$app->db->createCommand("SELECT d.idcategoria FROM tbl_dashboardspeechcalls d WHERE d.anulado = 0 AND d.iddashboardspeechcalls = $idspeech")->queryScalar();
        
        if ($varcategorias == '1114' || $varcategorias == '1105') {
            $data = '--';
        }else{
            $varcallid = Yii::$app->db->createCommand("SELECT d.callId FROM tbl_dashboardspeechcalls d WHERE d.anulado = 0 AND d.iddashboardspeechcalls = $idspeech")->queryScalar();

            $varlistone = Yii::$app->db->createCommand("SELECT sg.idvariable FROM tbl_speech_general sg WHERE sg.anulado = 0 AND sg.callid = $varcallid")->queryAll();

            $varprograma = Yii::$app->db->createCommand("SELECT sg.programacliente FROM tbl_speech_general sg WHERE sg.anulado = 0 AND sg.callid = $varcallid")->queryScalar();

            $vararraycategoria = array();
            foreach ($varlistone as $key => $value) {
                array_push($vararraycategoria, $value['idvariable']);
            }
            $varscategorias = implode(", ", $vararraycategoria);

            if ($varscategorias != "") {
                $varcount = Yii::$app->db->createCommand("SELECT distinct sc.nombre FROM tbl_speech_categorias sc WHERE sc.anulado = 0 AND sc.programacategoria IN ('$varprograma')  AND sc.idcategoria IN ($varscategorias) AND sc.responsable = 2 AND sc.componentes = 1")->queryScalar();

                if ($varcount == "") {
                    $data = "--";
                }else{
                    $data = $varcount;
                }
            }else{
                $data = "--";
            }
            
        }

        return $data;
    }

    public function getsagente($opcion){
        $idspeech = $opcion;
        $data = null;

        $varcategorias = Yii::$app->db->createCommand("SELECT d.idcategoria FROM tbl_dashboardspeechcalls d WHERE d.anulado = 0 AND d.iddashboardspeechcalls = $idspeech")->queryScalar();
        
        if ($varcategorias == '1114' || $varcategorias == '1105') {
            $data = '--';
        }else{
            $varcallid = Yii::$app->db->createCommand("SELECT d.callId FROM tbl_dashboardspeechcalls d WHERE d.anulado = 0 AND d.iddashboardspeechcalls = $idspeech")->queryScalar();

            $varlistone = Yii::$app->db->createCommand("SELECT sg.idvariable FROM tbl_speech_general sg WHERE sg.anulado = 0 AND sg.callid = $varcallid")->queryAll();

            $varprograma = Yii::$app->db->createCommand("SELECT sg.programacliente FROM tbl_speech_general sg WHERE sg.anulado = 0 AND sg.callid = $varcallid")->queryScalar();

            $vararraycategoria = array();
            foreach ($varlistone as $key => $value) {
                array_push($vararraycategoria, $value['idvariable']);
            }
            $varscategorias = implode(", ", $vararraycategoria);

            if ($varscategorias != "") {
                $varcount = Yii::$app->db->createCommand("SELECT distinct sc.nombre FROM tbl_speech_categorias sc WHERE sc.anulado = 0 AND sc.programacategoria IN ('$varprograma')  AND sc.idcategoria IN ($varscategorias) AND sc.responsable = 1 AND sc.componentes = 1")->queryScalar();

                if ($varcount == "") {
                    $data = "--";
                }else{
                    $data = $varcount;
                }
            }else{
                $data = "--";
            }
            
        }

        return $data;
    }

}