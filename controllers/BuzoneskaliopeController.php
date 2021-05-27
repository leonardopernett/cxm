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
use PHPExcel;
use PHPExcel_IOFactory;
use app\models\UploadForm2;
use app\models\BuzonesKaliope;
use GuzzleHttp;

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
    
    public function actionIndex(){ 
      $model = new BuzonesKaliope();
      $varbuzones = null;
      $varpcrc = null;
      $varnombrepcrc = null;
      $rest = null;

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
          $varpcrc = $model->arbol_id;
          $varfecha = explode(" ", $model->fechacreacion);
          $varFechaInicio = $varfecha[0];
          $varFechaFin = date('Y-m-d',strtotime($varfecha[2]));

          $vardataList = Yii::$app->db->createCommand("select b.ano, b.mes, b.dia, b.pcrc, a.name, b.buzon, b.created, b.connid from tbl_arbols a inner join tbl_base_satisfaccion b on a.id = b.pcrc where b.pcrc = $varpcrc and b.created between '$varFechaInicio 00:00:00' and '$varFechaFin 23:59:59' and b.buzon like '%/srv/www/htdocs/qa_managementv2/web/buzones_qa/%'")->queryAll();

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

      $varrta = Yii::$app->db->createCommand("select count(1) from tbl_buzones_kaliope bk where bk.arbol_id = $txtvarpcrc and bk.ruta_inicio = '$txtvaridruta' and anulado = 0")->queryScalar();

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
      $txtvaridruta = Yii::$app->request->POST("txtvaridruta");

      $client = new GuzzleHttp\Client([
        'verify' => false
      ]);

      $res = $client->request('POST', 'https://api-migi.analiticagrupokonectacloud.com/ ', [
        'headers' => [
            'Content-Type' => 'application/json'
          ],
        'json' => [
          'connid' => $txtvaridruta,
        ]
      ]);
      
      die(json_encode($res));
    }


  }

?>
