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
            [['login_id'], 'string', 'max' => 100],
            [['nombreCategoria', 'extension', 'fechallamada', 'servicio', 'fechareal', 'idredbox'],'filter', 'filter' => function($value){
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

    public function buscarsllamadas($params1,$params2,$params3,$params4,$varcategoriass,$varidloginid,$paramscalls,$varlider,$varasesor,$vartipologia){
        $txtprograma = $params1;
        $txtextension = $params2;
        $txtfechainicio = $params3;
        $txtfechafin = $params4;
        $txtcategoria = $varcategoriass;        
        $txtcontieneno = $varidloginid;
        $txtllamadas = $paramscalls;
        $txtlideres = $varlider;
        $txtasesores = $varasesor;
        $txttipologias = $vartipologia;

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

                if ($txttipologias != null) {

                    $queryCallid = Yii::$app->db->createCommand("
                        SELECT d.callId FROM  tbl_dashboardspeechcalls d
                            INNER JOIN tbl_base_satisfaccion b ON 
                                b.connid = d.connid
                            WHERE 
                                d.anulado = 0 AND d.servicio IN ('$txtprograma')
                                    AND d.fechallamada BETWEEN '$txtfechainicio' AND '$txtfechafin'
                                        AND d.extension IN ('$txtextension')
                                            AND d.idcategoria IN ($txtcategoria)
                                                AND b.tipologia IN ('$txttipologias')
                            GROUP BY d.callId
                                ORDER BY d.fechallamada DESC ")->queryAll();

                    if (count($queryCallid) != 0) {
                        $arrayListcallids = array();
                        foreach ($queryCallid as $key => $value) {
                            array_push($arrayListcallids, $value['callId']);
                        }
                        $textCallid = implode(", ", $arrayListcallids);

                        $query = Dashboardspeechcalls::find()
                            ->where("anulado = 0")
                            ->andwhere("servicio in ('$txtprograma')")
                            ->andwhere("fechallamada between '$txtfechainicio' and '$txtfechafin'")
                            ->andwhere("extension in ('$txtextension')")
                            ->andwhere("idcategoria in ($txtcategoria)")
                            ->andwhere("callId in ($textCallid)")
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
                            ->andwhere("callId in (0)")
                            ->groupBy("callId")
                            ->orderBy([
                                      'fechallamada' => SORT_DESC
                                    ]);
                    }
                    

                }else{
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
                }
                
            }else{

                if ($txttipologias != null) {

                    $queryCallid = Yii::$app->db->createCommand("
                        SELECT d.callId FROM  tbl_dashboardspeechcalls d
                            INNER JOIN tbl_base_satisfaccion b ON 
                                b.connid = d.connid
                            WHERE 
                                d.anulado = 0 AND d.servicio IN ('$txtprograma')
                                    AND d.fechallamada BETWEEN '$txtfechainicio' AND '$txtfechafin'
                                        AND d.extension IN ('$txtextension')
                                            AND d.idcategoria IN ($txtcategoria)
                                                AND b.tipologia IN ('$txttipologias')
                            GROUP BY d.callId
                                ORDER BY d.fechallamada DESC ")->queryAll();

                    if (count($queryCallid) != 0) {
                        $arrayListcallids = array();
                        foreach ($queryCallid as $key => $value) {
                            array_push($arrayListcallids, $value['callId']);
                        }
                        $textCallid = implode(", ", $arrayListcallids);

                        $query = Dashboardspeechcalls::find()
                            ->where("anulado = 0")
                            ->andwhere("servicio in ('$txtprograma')")
                            ->andwhere("fechallamada between '$txtfechainicio' and '$txtfechafin'")
                            ->andwhere("extension in ('$txtextension')")
                            ->andwhere("idcategoria in ($txtcategoria)")
                            ->andwhere("login_id in ('$txtarrayasesores')")
                            ->andwhere("callId in ($textCallid)")
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
                            ->andwhere("callId in (0)")
                            ->groupBy("callId")
                            ->orderBy([
                                      'fechallamada' => SORT_DESC
                                    ]);
                    }

                    
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
                
            }
        }else{
            $varlistcallid = Yii::$app->db->createCommand("select callId from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$txtprograma') and fechallamada between '$txtfechainicio' and '$txtfechafin' and extension in ('$txtextension') and idcategoria in ($txtcategoria) group by callId")->queryAll();
            $txtarraylistcallid = array();
            foreach ($varlistcallid as $key => $value) {
                array_push($txtarraylistcallid, $value['callId']);
            }
            $arraycallids = implode(", ", $txtarraylistcallid);

            if ($txtlideres == "" && $txtasesores == "") {

                if ($txttipologias != null) {

                    $queryCallid = Yii::$app->db->createCommand("
                        SELECT d.callId FROM  tbl_dashboardspeechcalls d
                            INNER JOIN tbl_base_satisfaccion b ON 
                                b.connid = d.connid
                            WHERE 
                                d.anulado = 0 AND d.servicio IN ('$txtprograma')
                                    AND d.fechallamada BETWEEN '$txtfechainicio' AND '$txtfechafin'
                                        AND d.extension IN ('$txtextension')
                                            AND d.idcategoria IN ($txtcategoria)
                                                AND b.tipologia IN ('$txttipologias')
                            GROUP BY d.callId
                                ORDER BY d.fechallamada DESC ")->queryAll();

                    if (count($queryCallid) != 0) {
                        $arrayListcallids = array();
                        foreach ($queryCallid as $key => $value) {
                            array_push($arrayListcallids, $value['callId']);
                        }
                        $textCallid = implode(", ", $arrayListcallids);

                        $query = Dashboardspeechcalls::find()
                            ->where("anulado = 0")
                            ->andwhere("servicio in ('$txtprograma')")
                            ->andwhere("fechallamada between '$txtfechainicio' and '$txtfechafin'")
                            ->andwhere("extension in ('$txtextension')")
                            ->andwhere("idcategoria in ($txtllamadas)")
                            ->andwhere("callId not in ($arraycallids)")
                            ->andwhere("callId in ($textCallid)")
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
                            ->andwhere("callId in (0)")
                            ->groupBy("callId")
                            ->orderBy([
                                      'fechallamada' => SORT_DESC
                                    ]);
                    }

                    

                }else{
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
                }
                
            }else{

                if ($txttipologias != null) {

                    $queryCallid = Yii::$app->db->createCommand("
                        SELECT d.callId FROM  tbl_dashboardspeechcalls d
                            INNER JOIN tbl_base_satisfaccion b ON 
                                b.connid = d.connid
                            WHERE 
                                d.anulado = 0 AND d.servicio IN ('$txtprograma')
                                    AND d.fechallamada BETWEEN '$txtfechainicio' AND '$txtfechafin'
                                        AND d.extension IN ('$txtextension')
                                            AND d.idcategoria IN ($txtcategoria)
                                                AND b.tipologia IN ('$txttipologias')
                            GROUP BY d.callId
                                ORDER BY d.fechallamada DESC ")->queryAll();

                    if (count($queryCallid) != 0) {
                        $arrayListcallids = array();
                        foreach ($queryCallid as $key => $value) {
                            array_push($arrayListcallids, $value['callId']);
                        }
                        $textCallid = implode(", ", $arrayListcallids);

                        $query = Dashboardspeechcalls::find()
                            ->where("anulado = 0")
                            ->andwhere("servicio in ('$txtprograma')")
                            ->andwhere("fechallamada between '$txtfechainicio' and '$txtfechafin'")
                            ->andwhere("extension in ('$txtextension')")
                            ->andwhere("idcategoria in ($txtllamadas)")
                            ->andwhere("callId not in ($arraycallids)")
                            ->andwhere("login_id in ('$txtarrayasesores')")
                            ->andwhere("callId in ($textCallid)")
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
                            ->andwhere("callId in (0)")
                            ->groupBy("callId")
                            ->orderBy([
                                      'fechallamada' => SORT_DESC
                                    ]);
                    }

                    

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
            
        }

        
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $dataProvider;
    }

    public function buscarsllamadasmodel($params1,$params2,$params3,$params4,$paramscalls,$params5,$params6){
        $txtprograma = $params1;
        $txtextension = $params2;
        $txtfechainicio = $params3;
        $txtfechafin = $params4;
        $txtllamadas = $paramscalls;

        $paramsBusqueda = [':varcodpcrcs' => $params5, ':anulado' => 0];

        $varcantidad = Yii::$app->db->createCommand('
          SELECT sa.cantidad FROM tbl_speech_aleatoridad sa
            WHERE sa.anulado = :anulado
              AND sa.cod_pcrc IN (:varcodpcrcs)')->bindValues($paramsBusqueda)->queryScalar();
        
        $Listadosid = Dashboardspeechcalls::find()
            ->select("iddashboardspeechcalls")
            ->where("anulado = 0")
            ->andwhere("servicio in ('$txtprograma')")
            ->andwhere("fechallamada between '$txtfechainicio' and '$txtfechafin'")
            ->andwhere("extension in ('$txtextension')")
            ->orderBy([
                    'rand()' => SORT_DESC
                ])
            ->limit($varcantidad)
            ->all();
        
        $arrayidspeech = array();
        foreach ($Listadosid as $key => $value) {
            array_push($arrayidspeech, $value['iddashboardspeechcalls']);
        }
        $arralistaidspeech = implode(", ", $arrayidspeech);

        if ($params6 == 1 && $varcantidad != "") {
            $query = Dashboardspeechcalls::find()
                    ->where("anulado = 0")
                    ->andwhere("servicio in ('$txtprograma')")
                    ->andwhere("fechallamada between '$txtfechainicio' and '$txtfechafin'")
                    ->andwhere("extension in ('$txtextension')")
                    ->andwhere("iddashboardspeechcalls IN ($arralistaidspeech)");
        }else{
            $query = Dashboardspeechcalls::find()
                    ->where("anulado = 0")
                    ->andwhere("servicio in ('$txtprograma')")
                    ->andwhere("fechallamada between '$txtfechainicio' and '$txtfechafin'")
                    ->andwhere("extension in ('$txtextension')")
                    ->andwhere("idcategoria in ('$txtllamadas')")
                    ->orderBy([
                              'fechallamada' => SORT_DESC
                        ]);
        }        
        
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

    public function getsestado($opcion,$opcion2,$opcion3){        
        $data = null;

        $concatenarspeech = $opcion.'; '.$opcion3;

        $txttempejecucion = Yii::$app->db->createCommand("SELECT COUNT(te.id) FROM tbl_tmpejecucionformularios te WHERE te.dsfuente_encuesta = '$concatenarspeech'")->queryScalar();

        $txtejecucion = Yii::$app->db->createCommand("SELECT COUNT(te.id) FROM tbl_ejecucionformularios te WHERE te.dsfuente_encuesta = '$concatenarspeech'")->queryScalar();

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

    public function getsresposanble($opcion,$opcion2,$opcion3){        
        $data = null;
        
        $concatenarspeech = $opcion.'; '.$opcion3;

        $txttempejecucion = Yii::$app->db->createCommand("SELECT COUNT(te.id) FROM tbl_tmpejecucionformularios te WHERE te.dsfuente_encuesta = '$concatenarspeech'")->queryScalar();

        $txtejecucion = Yii::$app->db->createCommand("SELECT COUNT(te.id) FROM tbl_ejecucionformularios te WHERE te.dsfuente_encuesta = '$concatenarspeech'")->queryScalar();

        if ($txttempejecucion == 0 && $txtejecucion == 0) {
            

            $data = "--";
        }else{
            if ($txttempejecucion == 1 && $txtejecucion == 0) {
                $data = Yii::$app->db->createCommand("SELECT DISTINCT u.usua_nombre FROM tbl_usuarios u INNER JOIN tbl_tmpejecucionformularios te ON u.usua_id = te.usua_id WHERE te.dsfuente_encuesta = '$concatenarspeech'")->queryScalar();
            }else{
                if ($txttempejecucion == 0 && $txtejecucion == 1) {
                    $data = Yii::$app->db->createCommand("SELECT DISTINCT u.usua_nombre FROM tbl_usuarios u INNER JOIN tbl_ejecucionformularios te ON u.usua_id = te.usua_id WHERE te.dsfuente_encuesta = '$concatenarspeech'")->queryScalar();
                }
            }
        }

        return $data;
    }

    public function getsmarca($opcion,$opcion2,$opcion3){
        $idcallid = $opcion;
        $servicio = $opcion2;
        $extension = $opcion3;        
        $data = null;

        $varStrinConteo = strlen($extension);
        if ($varStrinConteo <= 3) {
            $varExtension = " AND sp.rn IN ('$extension')";
        }
        if ($varStrinConteo == 5 || $varStrinConteo == 6) {
            $varExtension = " AND sp.ext IN ('$extension')";
        }
        if ($varStrinConteo > 6) {
            $varExtension = " AND sp.usuared IN ('$extension')";
        }

        $varCodPcrc = Yii::$app->db->createCommand("
            SELECT sp.cod_pcrc FROM tbl_speech_parametrizar sp
                INNER JOIN tbl_speech_categorias sc ON 
                    sp.cod_pcrc = sc.cod_pcrc
                WHERE 
                    sc.programacategoria IN ('$servicio')".$varExtension." GROUP BY sp.cod_pcrc")->queryScalar();

        $varlistone = Yii::$app->db->createCommand("
            SELECT d.idvariable FROM tbl_speech_general d 
                WHERE d.anulado = 0 AND d.callId in ($idcallid)
                        AND d.programacliente IN ('$servicio')")->queryAll();

        $vararraycategoria = array();
        foreach ($varlistone as $key => $value) {
            array_push($vararraycategoria, $value['idvariable']);
        }
        $varscategorias = implode(", ", $vararraycategoria);

        if ($varscategorias != "") {
            $varcount = Yii::$app->db->createCommand("
                SELECT GROUP_CONCAT(sc.nombre SEPARATOR', ') AS nombre  FROM tbl_speech_categorias sc WHERE sc.anulado = 0 
                    AND sc.programacategoria IN ('$servicio')  
                        AND sc.idcategoria IN ($varscategorias  ) AND sc.responsable = 3
                            AND sc.cod_pcrc IN ('$varCodPcrc')")->queryScalar();

            if ($varcount == "") {
                $data = "--";
            }else{
                $data = $varcount;
            }
        }else{
            $data = "--";
        }

        return $data;
    }

    public function getscanal($opcion,$opcion2,$opcion3){
        $idcallid = $opcion;
        $servicio = $opcion2;
        $extension = $opcion3;        
        $data = null;

        $varStrinConteo = strlen($extension);
        if ($varStrinConteo <= 3) {
            $varExtension = " AND sp.rn IN ('$extension')";
        }
        if ($varStrinConteo == 5 || $varStrinConteo == 6) {
            $varExtension = " AND sp.ext IN ('$extension')";
        }
        if ($varStrinConteo > 6) {
            $varExtension = " AND sp.usuared IN ('$extension')";
        }

        $varCodPcrc = Yii::$app->db->createCommand("
            SELECT sp.cod_pcrc FROM tbl_speech_parametrizar sp
                INNER JOIN tbl_speech_categorias sc ON 
                    sp.cod_pcrc = sc.cod_pcrc
                WHERE 
                    sc.programacategoria IN ('$servicio')".$varExtension." GROUP BY sp.cod_pcrc")->queryScalar();

        $varlistone = Yii::$app->db->createCommand("
            SELECT d.idvariable FROM tbl_speech_general d 
                WHERE d.anulado = 0 AND d.callId in ($idcallid)
                        AND d.programacliente IN ('$servicio')")->queryAll();

        $vararraycategoria = array();
        foreach ($varlistone as $key => $value) {
            array_push($vararraycategoria, $value['idvariable']);
        }
        $varscategorias = implode(", ", $vararraycategoria);

        if ($varscategorias != "") {
            $varcount = Yii::$app->db->createCommand("
                SELECT GROUP_CONCAT(sc.nombre SEPARATOR', ') AS nombre  FROM tbl_speech_categorias sc WHERE sc.anulado = 0 
                    AND sc.programacategoria IN ('$servicio')  
                        AND sc.idcategoria IN ($varscategorias  ) AND sc.responsable = 2
                            AND sc.cod_pcrc IN ('$varCodPcrc')")->queryScalar();

            if ($varcount == "") {
                $data = "--";
            }else{
                $data = $varcount;
            }
        }else{
            $data = "--";
        }

        return $data;
    }

    public function getsagente($opcion,$opcion2,$opcion3){
        $idcallid = $opcion;
        $servicio = $opcion2;
        $extension = $opcion3;        
        $data = null;

        $varStrinConteo = strlen($extension);
        if ($varStrinConteo <= 3) {
            $varExtension = " AND sp.rn IN ('$extension')";
        }
        if ($varStrinConteo == 5 || $varStrinConteo == 6) {
            $varExtension = " AND sp.ext IN ('$extension')";
        }
        if ($varStrinConteo > 6) {
            $varExtension = " AND sp.usuared IN ('$extension')";
        }

        $varCodPcrc = Yii::$app->db->createCommand("
            SELECT sp.cod_pcrc FROM tbl_speech_parametrizar sp
                INNER JOIN tbl_speech_categorias sc ON 
                    sp.cod_pcrc = sc.cod_pcrc
                WHERE 
                    sc.programacategoria IN ('$servicio')".$varExtension." GROUP BY sp.cod_pcrc")->queryScalar();

        $varlistone = Yii::$app->db->createCommand("
            SELECT d.idvariable FROM tbl_speech_general d 
                WHERE d.anulado = 0 AND d.callId in ($idcallid)
                        AND d.programacliente IN ('$servicio')")->queryAll();

        $vararraycategoria = array();
        foreach ($varlistone as $key => $value) {
            array_push($vararraycategoria, $value['idvariable']);
        }
        $varscategorias = implode(", ", $vararraycategoria);

        if ($varscategorias != "") {
            $varcount = Yii::$app->db->createCommand("
                SELECT GROUP_CONCAT(sc.nombre SEPARATOR', ') AS nombre  FROM tbl_speech_categorias sc WHERE sc.anulado = 0 
                    AND sc.programacategoria IN ('$servicio')  
                        AND sc.idcategoria IN ($varscategorias  ) AND sc.responsable = 1
                            AND sc.cod_pcrc IN ('$varCodPcrc')")->queryScalar();

            if ($varcount == "") {
                $data = "--";
            }else{
                $data = $varcount;
            }
        }else{
            $data = "--";
        }

        return $data;
    }

    /**
     * Retorna el listado de estados
     * @return array
     */
    public function tipologiasList() {
        return \yii\helpers\ArrayHelper::map(
                        BaseSatisfaccion::find()
                                ->groupBy(['tipologia'])
                                ->all(), 'tipologia', 'tipologia');
    }

}