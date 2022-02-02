<?php

namespace app\controllers;

ini_set('upload_max_filesize', '50M');

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\db\Query;
use yii\db\mssql\PDO;
use yii\web\UploadedFile;
use yii\web\Controller;
use yii\helpers\Url;
use PHPExcel;
use PHPExcel_IOFactory;
use app\models\UploadForm2;
use app\models\BuzonesKaliope;
use GuzzleHttp;
use app\models\BaseSatisfaccion; 
use app\models\BaseSatisfaccionSearch;
use app\models\Formularios;




  class BuzoneskaliopeController extends \yii\web\Controller {

    public function behaviors(){
      return[
        'access' => [
            'class' => AccessControl::classname(),
            'only' => ['index'],
            'rules' => [
              [
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema();
                        },
              ],
            ]
          ],
        'verbs' => [          
          'class' => VerbFilter::className(),
          'actions' => [
            'delete' => ['post'],
          ],
        ],
      ];
    }

    public function actions() {
      return [
          'error' => [
            'class' => 'yii\web\ErrorAction',
          ]
      ];
  }

  public function actionError() {

      //ERROR PRESENTADO
      $exception = Yii::$app->errorHandler->exception;

      if ($exception !== null) {
          //VARIABLES PARA LA VISTA ERROR
          $code = $exception->statusCode;
          $name = $exception->getName() . " (#$code)";
          $message = $exception->getMessage();
          //VALIDO QUE EL ERROR VENGA DEL CLIENTE DE IVR Y QUE SOLO APLIQUE
          // PARA LOS ERRORES 400
          $request = \Yii::$app->request->pathInfo;
          if ($request == "basesatisfaccion/clientebasesatisfaccion" && $code ==
                  400) {
              //GUARDO EN EL ERROR DE SATU
              $baseSat = new BasesatisfaccionController();
              $baseSat->setErrorSatu(\Yii::$app->request->url, $name . ": " . $message);
          }
          //RENDERIZO LA VISTA
          return $this->render('error', [
                      'name' => $name,
                      'message' => $message,
                      'exception' => $exception,
          ]);
      }
  }
    
    public function actionIndex(){ 
      $model = new BuzonesKaliope();
      $varbuzones = null;
      $varpcrc = null;
      $varnombrepcrc = null;
      $rest = null;

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
          $varpcrc = 18;
          $varfecha = explode(" ", $model->fechacreacion);
          $varFechaInicio = $varfecha[0];
          $varFechaFin = date('Y-m-d',strtotime($varfecha[2]));

          $paramsBusqueda = [':v.varpcrc'=>$varpcrc,':v.varFechaInicio'=>$varFechaInicio,':v.varFechaFin'=>$varFechaFin];
          $vardataList = Yii::$app->db->createCommand("select b.ano, b.mes, b.dia, b.pcrc, a.name, b.buzon, b.created, b.connid from tbl_arbols a inner join tbl_base_satisfaccion b on a.id = b.pcrc where b.pcrc = :v.varpcrc and b.created between ':v.varFechaInicio 00:00:00' and ':v.varFechaFin 23:59:59' and b.buzon like '%/srv/www/htdocs/qa_managementv2/web/buzones_qa/%'")->bindValues($paramsBusqueda)->queryAll();

          foreach ($vardataList as $key => $value) {
            $varbuzones = $value['buzon'];
            $varpcrc = $value['pcrc'];
            $varnombrepcrc = $value['name'];

            $resultado = intval(preg_replace('/[^0-9]+/', '', $varnombrepcrc), 10); 
            $varcountrta = strlen($resultado) + 1;
            $rest = substr($varnombrepcrc, 0, $varcountrta);
          }         

      }

      return $this->render('index',[
        'model' => $model,
        'varbuzones' => $varbuzones,
        'varpcrc' => $varpcrc,
        'varnombrepcrc' => $varnombrepcrc,
        'rest' => $rest,
        ]);
    }

    public function actionIngresarruta(){
      $txtvaridruta = Yii::$app->request->get("txtvaridruta");
      $txtvarpcrc = Yii::$app->request->get("txtvarpcrc");
      $txtvarnombrepcrc = Yii::$app->request->get("txtvarnombrepcrc");
      $txtvarrest = Yii::$app->request->get("txtvarrest");

      $paramsBusqueda = [':t.txtvarpcrc'=>$txtvarpcrc,':t.txtvaridruta'=>$txtvaridruta];
      $varrta = Yii::$app->db->createCommand("select count(1) from tbl_buzones_kaliope bk where bk.arbol_id = :t.txtvarpcrc and bk.ruta_inicio = :t.txtvaridruta and anulado = 0")->bindValues($paramsBusqueda)
      ->queryScalar();

      if ($varrta == 0) {
        Yii::$app->db->createCommand()->insert('tbl_buzones_kaliope',[
                                           'arbol_id' => $txtvarpcrc,
                                           'arbol_name' => $txtvarnombrepcrc,
                                           'cod_pcrc' => $txtvarrest,
                                           'ruta_inicio' => $txtvaridruta,
                                           'fecharuta' => null,
                                           'fechabuzon' => null,
                                           'fechacreacion' => date("Y-m-d"),
                                           'anulado' => 0,
                                           'usua_id' => Yii::$app->user->identity->id,
                                           'connid' => null,
                                       ])->execute();

      }

      $varrta = 1;
      
      die(json_encode($varrta));
    }

    public function actionTranscripcionkaliope(){
      $txtvaridruta = Yii::$app->request->GET("txtvaridruta");

      ob_start();
      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_SSL_VERIFYPEER=> false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_URL => 'https://api-kaliope.analiticagrupokonectacloud.com/status-by-connid',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{"connid": "'.$txtvaridruta.'"}',
        CURLOPT_HTTPHEADER => array(
          'Content-Type: application/json'
        ),
      ));

      $response = curl_exec($curl);

      curl_close($curl);
      ob_clean();

      if (!$response) {
        die(json_encode(array('status' => '0','data'=>'Error al buscar la transcripcion')));
      }

      $response = json_decode(iconv( "Windows-1252", "UTF-8", $response ),true);

      if (count($response) == 0) {
        die(json_encode(array('status' => '0','data'=>'Transcripcion no encontrada'))); 
      }
      

      $varrespuesta = 'Transcripcion: '.$response[0]['transcription'].'  *****  Valencia emocional: '.$response[0]['valencia'];
      
      die(json_encode($varrespuesta));
    }

    public function actionGenerarlogs(){
      ini_set("max_execution_time", "900");
      ini_set("memory_limit", "1024M");
      ini_set( 'post_max_size', '1024M' );

      ignore_user_abort(true);
      set_time_limit(900);

      $fecha_actual = date("Y-m-d");

      $varfechainicial = date("Y-m-d",strtotime($fecha_actual."- 1 days")); 
      

      $paramsBusqueda = [':v.varfechainicial'=>$varfechainicial];
      $varlista = Yii::$app->db->createCommand("SELECT b.connid, b.created FROM tbl_base_satisfaccion b WHERE b.fecha_satu BETWEEN ':v.varfechainicial 00:00:00' AND ':v.varfechainicial 23:59:59' AND b.connid IS NOT NULL AND b.tipo_inbox IN ('ALEATORIO','NORMAL') ")->bindValues($paramsBusqueda)->queryAll();

      foreach ($varlista as $key => $value) {
        $txtvaridruta = $value['connid'];
        $txtcreated = $value['created'];

        $vartexto = null;
        $varvalencia = null;

        ob_start();
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_SSL_VERIFYPEER=> false,
          CURLOPT_SSL_VERIFYHOST => false,
          CURLOPT_URL => 'https://api-kaliope.analiticagrupokonectacloud.com/status-by-connid',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{"connid": "'.$txtvaridruta.'"}',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
          ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        ob_clean();

        $response = json_decode(iconv( "Windows-1252", "UTF-8", $response ),true);

        if (count($response) == 0) {
          #
        }else{
          $vartexto = $response[0]['transcription'];
          $varvalencia = $response[0]['valencia'];

          if ($varvalencia == "NULL") {
            $varvalencia = "Buzón sin información";
          }

          Yii::$app->db->createCommand()->insert('tbl_kaliope_transcipcion',[
                                           'connid' => $txtvaridruta,
                                           'transcripcion' => $vartexto,
                                           'valencia' => $varvalencia,
                                           'fechagenerada' => $txtcreated,
                                           'fechacreacion' => date("Y-m-d"),
                                           'anulado' => 0,
                                           'usua_id' => Yii::$app->user->identity->id,
                                       ])->execute();
        }

      }

      die(json_encode(1));
    }


  }

?>
