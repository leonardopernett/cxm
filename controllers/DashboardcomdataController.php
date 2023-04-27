<?php

namespace app\controllers;

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
use GuzzleHttp;
use Exception;
use app\models\Comdatareportestudio;
use app\models\Comdataparametrizarapi;

  class DashboardcomdataController extends Controller {

    public function behaviors(){
        return[
          'access' => [
              'class' => AccessControl::classname(),
              'only' => ['index','permisoscomdata','permisosclientecomdata'],
              'rules' => [
                [
                  'allow' => true,
                  'roles' => ['@'],
                  'matchCallback' => function() {
                              return Yii::$app->user->identity->isAdminSistema() ||  Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isControlProcesoCX() || Yii::$app->user->identity->isVerdirectivo();
                          },
                ],
              ]
            ],
          'verbs' => [          
            'class' => VerbFilter::className(),
            'actions' => [
              'delete' => ['get'],
            ],
          ],

          'corsFilter' => [
            'class' => \yii\filters\Cors::class,
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
   
    public function actionIndex(){  
      $model = new Comdatareportestudio();
      $varComdataUrl = null;
      $varFullName = null;

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        $varIdDpClientes = $model->id_dp_clientes;
        $varCod_Pcrc = $model->cod_pcrc;

        $varconteoUrl = (new \yii\db\Query())
                                ->select(['tbl_comdata_reportestudio.url'])
                                ->from(['tbl_comdata_reportestudio'])            
                                ->where(['=','tbl_comdata_reportestudio.anulado',0])
                                ->andwhere(['=','tbl_comdata_reportestudio.id_dp_clientes',$varIdDpClientes])
                                ->andwhere(['=','tbl_comdata_reportestudio.cod_pcrc',$varCod_Pcrc])
                                ->count();

        if ($varconteoUrl != 0) {
          $varUrl = (new \yii\db\Query())
                                ->select(['tbl_comdata_reportestudio.url'])
                                ->from(['tbl_comdata_reportestudio'])            
                                ->where(['=','tbl_comdata_reportestudio.anulado',0])
                                ->andwhere(['=','tbl_comdata_reportestudio.id_dp_clientes',$varIdDpClientes])
                                ->andwhere(['=','tbl_comdata_reportestudio.cod_pcrc',$varCod_Pcrc])
                                ->scalar();

          $varComdataUrl = $varUrl;
          $varFullName = (new \yii\db\Query())
                                ->select(['CONCAT(tbl_speech_categorias.cod_pcrc," - ",tbl_speech_categorias.pcrc) AS fullname'])
                                ->from(['tbl_speech_categorias'])            
                                ->where(['=','tbl_speech_categorias.anulado',0])
                                ->andwhere(['=','tbl_speech_categorias.cod_pcrc',$varCod_Pcrc])
                                ->groupby(['tbl_speech_categorias.pcrc'])
                                ->scalar();
        }else{
          $varComdataUrl = 'SinProceso';
          $varFullName = null;
        }

      }

      return $this->render('index',[
        'model' => $model,
        'varComdataUrl' => $varComdataUrl,
        'varFullName' => $varFullName,
      ]);
    }

    public function actionListarpcrcs(){
        $txtanulado = 0;
        $txtidcliente = Yii::$app->request->get('id');


          if ($txtidcliente) {
            $txtControl = \app\models\SpeechCategorias::find()->distinct()
              ->select(['tbl_speech_categorias.cod_pcrc'])
              ->join('LEFT OUTER JOIN', 'tbl_speech_parametrizar',
                                  'tbl_speech_categorias.cod_pcrc = tbl_speech_parametrizar.cod_pcrc')
              ->where('tbl_speech_parametrizar.id_dp_clientes = :varCliente',[':varCliente'=>$txtidcliente])
              ->andwhere('tbl_speech_parametrizar.anulado = :varAnulado',[':varAnulado'=>$txtanulado])
              ->count();

            if ($txtControl > 0) {
              $varListaLideresx = \app\models\SpeechCategorias::find()->distinct()
                  ->select(['tbl_speech_categorias.cod_pcrc','tbl_speech_categorias.pcrc'])
                  ->join('LEFT OUTER JOIN', 'tbl_speech_parametrizar',
                                      'tbl_speech_categorias.cod_pcrc = tbl_speech_parametrizar.cod_pcrc')
                  ->where('tbl_speech_parametrizar.id_dp_clientes = :varCliente',[':varCliente'=>$txtidcliente])
                  ->andwhere('tbl_speech_parametrizar.anulado = :varAnulado',[':varAnulado'=>$txtanulado])
                  ->groupby(['tbl_speech_categorias.cod_pcrc'])                  
                  ->all(); 

              echo "<option value='' disabled selected>Seleccionar...</option>";
              foreach ($varListaLideresx as $key => $value) {
                echo "<option value='" . $value->cod_pcrc. "'>" . $value->cod_pcrc.' - '.$value->pcrc . "</option>";
              }
            }else{
              echo "<option>--</option>";
            }
          }else{
            echo "<option>Seleccionar...</option>";
          }          
    }

    public function actionConfigurarcomdata(){
      $modelconf = new Comdataparametrizarapi();

      $form = Yii::$app->request->post();
      if ($modelconf->load($form)) {
        $varIdDpClientesConfig = $modelconf->id_dp_clientes;
        $varCodPcrcConfig = $modelconf->cod_pcrc;
        $varTextoUrlConfig = $modelconf->table_id;

        Yii::$app->db->createCommand()->insert('tbl_comdata_reportestudio',[
                'id_dp_clientes' => $varIdDpClientesConfig,
                'cod_pcrc' => $varCodPcrcConfig,
                'extension' => null,
                'url' => $varTextoUrlConfig,
                'fechacreacion' => date('Y-m-d'),
                'anulado' => 0,
                'usua_id' => Yii::$app->user->identity->id,
        ])->execute();

        return $this->redirect('index');
      }

      return $this->renderAjax('configurarcomdata',[
        'modelconf' => $modelconf,
      ]);
    }

    public function actionPermisoscomdata(){
      $modelpermiso = new Comdatareportestudio();
      $varNombre = null;
      $varUsuario = null;
      $dataProviderClientes = null;

      $form = Yii::$app->request->post();
      if ($modelpermiso->load($form)) {
        $varUsuario = $modelpermiso->usua_id;

        $varNombre = (new \yii\db\Query())
                                ->select(['tbl_usuarios.usua_nombre'])
                                ->from(['tbl_usuarios'])            
                                ->where(['=','tbl_usuarios.usua_id',$varUsuario])
                                ->scalar();

        $dataProviderClientes = (new \yii\db\Query())
                                ->select([
                                  'tbl_proceso_cliente_centrocosto.id_dp_clientes',
                                  'tbl_proceso_cliente_centrocosto.cliente'
                                ])
                                ->from(['tbl_proceso_cliente_centrocosto'])
                                ->join('LEFT OUTER JOIN', 'tbl_comdata_permisosreportestudio',
                                            'tbl_proceso_cliente_centrocosto.id_dp_clientes = tbl_comdata_permisosreportestudio.id_dp_clientes')
                                ->where(['=','tbl_comdata_permisosreportestudio.anulado',0])
                                ->andwhere(['=','tbl_comdata_permisosreportestudio.usuario_permiso',$varUsuario])
                                ->groupby(['tbl_proceso_cliente_centrocosto.id_dp_clientes'])
                                ->orderby(['tbl_proceso_cliente_centrocosto.cliente' => SORT_DESC])
                                ->all();

      }


      return $this->render('permisoscomdata',[
        'modelpermiso' => $modelpermiso,
        'varNombre' => $varNombre,
        'varUsuario' => $varUsuario,
        'dataProviderClientes' => $dataProviderClientes,
      ]);
    }

    public function actionPermisosclientecomdata(){
      $txtvaridservicio = Yii::$app->request->get("txtvaridserviciocomdata");
      $txtvariduser = Yii::$app->request->get("txtvaridusercomdata");

      $array_idclientes = count($txtvaridservicio);
      for ($i=0; $i < $array_idclientes; $i++) { 
        $variddpcliente = $txtvaridservicio[$i];

        Yii::$app->db->createCommand()->insert('tbl_comdata_permisosreportestudio',[
                    'usuario_permiso' => $txtvariduser,  
                    'id_dp_clientes' => $variddpcliente,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                  
        ])->execute();
      }       

      die(json_encode($txtvariduser));
      
    }

}

?>


