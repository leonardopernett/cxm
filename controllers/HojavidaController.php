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
use app\models\HojavidaEventos;
use app\models\HvCiudad;
use app\models\HvPais;
use app\models\HojavidaDatapersonal;
use app\models\HojavidaDatalaboral;
use app\models\HojavidaDataacademica;
use app\models\HojavidaDatacuenta;
use app\models\HojavidaDatapcrc;
use app\models\HojavidaDatadirector;
use app\models\HojavidaDatagerente;
use app\models\HojavidaDatacivil;
use app\models\HvDominancias;
use app\models\HvEstilosocial;
use app\models\HvHobbies;
use app\models\HvGustos;
use app\models\HojavidaPermisosacciones;
use app\models\HojavidaPermisoscliente;
use app\models\HojavidaDatacomplementos;
use app\models\ProcesosClienteCentrocosto;
use app\models\HojavidaDataclasificacion;
use app\models\HojavidaTipoeventos;
use app\models\HvInfopersonal;
use app\models\Hojavidaroles;
use app\models\Hojavidainforme;
use app\models\Hojavidaperiocidad;
use app\models\Hojavidametricas;
use Exception;

  class HojavidaController extends Controller {

    public function behaviors(){
        return[
          'access' => [
              'class' => AccessControl::classname(),
              'only' => ['index','resumen','eventos','paisciudad','eliminarevento','creapais','creaciudad','eliminarpais','eliminarciudad','informacionpersonal','listarciudades','viewinfo','permisoshv','complementoshv','asignarpermisos','eliminarpermisos','editarpermisos','createdservicio','deleteinfo','editinfo','complementosaccion','tiposeventos','informacioncontrato','contratorol','contratoinforme','contratopriocidad','contratometrica','importarpersona'],
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
      // Procesos para calcular el resumen
      $arrayIdCiudades = [1,2];
      $varListaClientesResumen = (new \yii\db\Query())
                                ->select(['if(clasificacion=1,"Bogota","Medellín") AS Ciudad',' COUNT(clasificacion) AS Conteo'])
                                ->from(['tbl_hojavida_datapersonal'])            
                                ->where(['IN','clasificacion',$arrayIdCiudades])
                                ->andwhere(['=','anulado',0])
                                ->groupby(['clasificacion'])
                                ->all();

      $varListaDecisores = (new \yii\db\Query())
                        ->select(['if(tbl_hojavida_datapersonal.clasificacion=1,"Bogota","Medellín") AS Ciudad',' COUNT(tbl_hojavida_datapersonal.clasificacion) AS Conteo'])
                        ->from(['tbl_hojavida_datapersonal']) 
                        ->join('INNER JOIN', 'tbl_hojavida_datalaboral', 'tbl_hojavida_datapersonal.hv_idpersonal = tbl_hojavida_datalaboral.hv_idpersonal')  
                        ->join('INNER JOIN', 'tbl_hojavida_datatipoafinidad', 'tbl_hojavida_datalaboral.tipo_afinidad = tbl_hojavida_datatipoafinidad.hv_idtipoafinidad')          
                        ->where(['IN','tbl_hojavida_datapersonal.clasificacion',$arrayIdCiudades])
                        ->andwhere(['=','tbl_hojavida_datatipoafinidad.hv_idtipoafinidad',1])
                        ->andwhere(['=','tbl_hojavida_datapersonal.anulado',0])
                        ->groupby(['tbl_hojavida_datapersonal.clasificacion'])
                        ->all();

      $varListaDecisorEstrategico = (new \yii\db\Query())
                        ->select(['if(tbl_hojavida_datapersonal.clasificacion=1,"Bogota","Medellín") AS Ciudad',' COUNT(tbl_hojavida_datapersonal.clasificacion) AS Conteo'])
                        ->from(['tbl_hojavida_datapersonal']) 

                        ->join('INNER JOIN', 'tbl_hojavida_datalaboral', 'tbl_hojavida_datapersonal.hv_idpersonal = tbl_hojavida_datalaboral.hv_idpersonal')  

                        ->join('INNER JOIN', 'tbl_hojavida_datanivelafinidad', 'tbl_hojavida_datalaboral.nivel_afinidad = tbl_hojavida_datanivelafinidad.hv_idinvelafinidad')   

                        ->join('INNER JOIN', 'tbl_hojavida_datatipoafinidad', 'tbl_hojavida_datalaboral.tipo_afinidad = tbl_hojavida_datatipoafinidad.hv_idtipoafinidad')  

                        ->where(['IN','tbl_hojavida_datapersonal.clasificacion',$arrayIdCiudades])
                        ->andwhere(['=','tbl_hojavida_datatipoafinidad.hv_idtipoafinidad',1])
                        ->andwhere(['=','tbl_hojavida_datanivelafinidad.hv_idinvelafinidad',1])
                        ->andwhere(['=','tbl_hojavida_datapersonal.anulado',0])
                        ->groupby(['tbl_hojavida_datapersonal.clasificacion'])
                        ->all();

      $varListaDecisorOperativo = (new \yii\db\Query())
                        ->select(['if(tbl_hojavida_datapersonal.clasificacion=1,"Bogota","Medellín") AS Ciudad',' COUNT(tbl_hojavida_datapersonal.clasificacion) AS Conteo'])
                        ->from(['tbl_hojavida_datapersonal']) 

                        ->join('INNER JOIN', 'tbl_hojavida_datalaboral', 'tbl_hojavida_datapersonal.hv_idpersonal = tbl_hojavida_datalaboral.hv_idpersonal')  

                        ->join('INNER JOIN', 'tbl_hojavida_datanivelafinidad', 'tbl_hojavida_datalaboral.nivel_afinidad = tbl_hojavida_datanivelafinidad.hv_idinvelafinidad')   

                        ->join('INNER JOIN', 'tbl_hojavida_datatipoafinidad', 'tbl_hojavida_datalaboral.tipo_afinidad = tbl_hojavida_datatipoafinidad.hv_idtipoafinidad')  

                        ->where(['IN','tbl_hojavida_datapersonal.clasificacion',$arrayIdCiudades])
                        ->andwhere(['=','tbl_hojavida_datatipoafinidad.hv_idtipoafinidad',1])
                        ->andwhere(['=','tbl_hojavida_datanivelafinidad.hv_idinvelafinidad',2])
                        ->andwhere(['=','tbl_hojavida_datapersonal.anulado',0])
                        ->groupby(['tbl_hojavida_datapersonal.clasificacion'])
                        ->all();

      $varListaDirectores = (new \yii\db\Query())
                        ->select(['if(tbl_hojavida_datadirector.ccdirector="","Sin Definir",tbl_hojavida_datadirector.ccdirector) AS CcDirector','(SELECT p.director_programa FROM tbl_proceso_cliente_centrocosto p WHERE p.documento_director = tbl_hojavida_datadirector.ccdirector LIMIT 1) AS NombreDirector','COUNT(tbl_hojavida_datadirector.ccdirector) AS ConteoDirector'])
                        ->from(['tbl_hojavida_datadirector'])
                        ->where(['!=','tbl_hojavida_datadirector.ccdirector',''])
                        ->andwhere(['!=','tbl_hojavida_datadirector.ccdirector','-'])
                        ->groupby(['tbl_hojavida_datadirector.ccdirector'])
                        ->all();

      $arrayListaDirector = array();
      $arrayListaDirectorCantidad = array();
      foreach ($varListaDirectores as $key => $value) {
        array_push($arrayListaDirector, $value['NombreDirector']);
        array_push($arrayListaDirectorCantidad, $value['ConteoDirector']);
      }

      $varListaClientes = (new \yii\db\Query())
                        ->select(['if(tbl_hojavida_datapcrc.id_dp_cliente="","Sin Definir",tbl_hojavida_datapcrc.id_dp_cliente) AS IdClientes','(SELECT p.cliente FROM tbl_proceso_cliente_centrocosto p WHERE p.id_dp_clientes = tbl_hojavida_datapcrc.id_dp_cliente LIMIT 1) AS NombreCliente','COUNT(tbl_hojavida_datapcrc.id_dp_cliente) AS ConteoClientes'])
                        ->from(['tbl_hojavida_datapcrc'])
                        ->where(['!=','tbl_hojavida_datapcrc.id_dp_cliente',''])
                        ->andwhere(['!=','tbl_hojavida_datapcrc.id_dp_cliente',1])
                        ->groupby(['tbl_hojavida_datapcrc.id_dp_cliente'])
                        ->all();   

      $arrayListaCliente = array();
      $arrayListaClienteCantidad = array();
      foreach ($varListaClientes as $key => $value) {
        array_push($arrayListaCliente, $value['NombreCliente']);
        array_push($arrayListaClienteCantidad, $value['ConteoClientes']);
      }

      // Procesos para generar el listado de los calculos
      $sesiones = Yii::$app->user->identity->id;
      $modelos = new HojavidaDatapersonal();
      $rol =  new Query;
      $rol     ->select(['tbl_roles.role_id'])
                  ->from('tbl_roles')
                  ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                              'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                  ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                              'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                  ->where('tbl_usuarios.usua_id = :sesiones')
                  ->addParams([':sesiones'=>$sesiones]);                    
      $command = $rol->createCommand();
      $roles = $command->queryScalar();

      if ($roles == "270") {

        $paramsuser = [':idsesion' => $sesiones ];
        $varidclientes = Yii::$app->db->createCommand('
          SELECT GROUP_CONCAT(id_dp_clientes SEPARATOR", ") servicios 
            FROM tbl_hojavida_permisoscliente hp
              WHERE   
                hp.usuario_registro = :idsesion')->bindValues($paramsuser)->queryScalar(); 

        if ($varidclientes != null) {
          $dataProviderhv = Yii::$app->db->createCommand("
          SELECT dp.hv_idpersonal 'idHojaVida', pc.cliente, if(dl.tipo_afinidad = 1, 'Decisor','No Decisor') 'tipo', if(dl.nivel_afinidad = 1, 'Estrategico','Operativo') 'nivel', dp.nombre_full, dl.rol, hp.pais, if(da.activo = 1, 'Activo','No Activo') 'estado' FROM tbl_hojavida_datapersonal dp
          INNER JOIN tbl_hojavida_datalaboral dl ON 
            dl.hv_idpersonal = dp.hv_idpersonal
          LEFT JOIN tbl_hv_pais hp ON 
            hp.hv_idpais = dp.hv_idpais
          LEFT JOIN tbl_hojavida_dataacademica da ON 
            da.hv_idpersonal = dp.hv_idpersonal
          LEFT JOIN tbl_hojavida_datapcrc dc ON 
            dc.hv_idpersonal = dp.hv_idpersonal
          LEFT JOIN tbl_proceso_cliente_centrocosto pc ON 
            pc.id_dp_clientes = dc.id_dp_cliente
          WHERE
            dc.id_dp_cliente IN (:varidclientes)
              AND dp.anulado = 0
            GROUP BY dp.hv_idpersonal
          ")
          ->bindValue(':varidclientes',$varidclientes)
          ->queryAll();
        }else{
          $dataProviderhv = Yii::$app->db->createCommand("
          SELECT dp.hv_idpersonal 'idHojaVida', pc.cliente, if(dl.tipo_afinidad = 1, 'Decisor','No Decisor') 'tipo', if(dl.nivel_afinidad = 1, 'Estrategico','Operativo') 'nivel', dp.nombre_full, dl.rol, hp.pais, if(da.activo = 1, 'Activo','No Activo') 'estado' FROM tbl_hojavida_datapersonal dp
          INNER JOIN tbl_hojavida_datalaboral dl ON 
            dl.hv_idpersonal = dp.hv_idpersonal
          LEFT JOIN tbl_hv_pais hp ON 
            hp.hv_idpais = dp.hv_idpais
          LEFT JOIN tbl_hojavida_dataacademica da ON 
            da.hv_idpersonal = dp.hv_idpersonal
          LEFT JOIN tbl_hojavida_datapcrc dc ON 
            dc.hv_idpersonal = dp.hv_idpersonal
          LEFT JOIN tbl_proceso_cliente_centrocosto pc ON 
            pc.id_dp_clientes = dc.id_dp_cliente
          WHERE
            dp.anulado = 0
            GROUP BY dp.hv_idpersonal
          ")->queryAll();
        }

      }else{
        $varidclientes = (new \yii\db\Query())
                                ->select(['id_dp_clientes'])
                                ->from(['tbl_hojavida_permisoscliente'])
                                ->where(['=','anulado',0])
                                ->andwhere(['=','usuario_registro',$sesiones])
                                ->groupby(['id_dp_clientes'])
                                ->all();
                                

        $varArrayClientes = array();
        foreach ($varidclientes as $key => $value) {
          array_push($varArrayClientes, intval($value['id_dp_clientes']));
        }
        $varClienteListV = implode(", ", $varArrayClientes);
        $arrayCliente_downV = str_replace(array("#", "'", ";", " "), '', $varClienteListV);
        $varDataClienteJ = explode(",", $arrayCliente_downV);

        $dataProviderhv = (new \yii\db\Query())
                                ->select(['tbl_hojavida_datapersonal.hv_idpersonal as idHojaVida','tbl_proceso_cliente_centrocosto.cliente','if(tbl_hojavida_datalaboral.tipo_afinidad = 1, "Decisor","No Decisor") as tipo','if(tbl_hojavida_datalaboral.nivel_afinidad = 1, "Estrategico","Operativo") as nivel','tbl_hojavida_datapersonal.nombre_full','tbl_hojavida_datalaboral.rol','tbl_hv_pais.pais','if(tbl_hojavida_dataacademica.activo = 1, "Activo","No Activo") as estado'])

                                ->from(['tbl_hojavida_datapersonal'])  

                                ->join('LEFT OUTER JOIN', 'tbl_hojavida_datalaboral',
                                  'tbl_hojavida_datalaboral.hv_idpersonal = tbl_hojavida_datapersonal.hv_idpersonal') 

                                ->join('LEFT OUTER JOIN', 'tbl_hv_pais',
                                  ' tbl_hv_pais.hv_idpais = tbl_hojavida_datapersonal.hv_idpais') 

                                ->join('LEFT OUTER JOIN', 'tbl_hojavida_dataacademica',
                                  ' tbl_hojavida_dataacademica.hv_idpersonal = tbl_hojavida_datapersonal.hv_idpersonal') 

                                ->join('LEFT OUTER JOIN', 'tbl_hojavida_datapcrc',
                                  ' tbl_hojavida_datapcrc.hv_idpersonal = tbl_hojavida_datapersonal.hv_idpersonal') 

                                ->join('LEFT OUTER JOIN', 'tbl_proceso_cliente_centrocosto',
                                  ' tbl_proceso_cliente_centrocosto.id_dp_clientes = tbl_hojavida_datapcrc.id_dp_cliente') 

                                ->where(['in','tbl_hojavida_datapcrc.id_dp_cliente',$varDataClienteJ])
                                ->groupby(['tbl_hojavida_datapersonal.hv_idpersonal'])
                                ->All();
      }

      if ($roles == "270") {
        $varListaContratos = (new \yii\db\Query())
                                ->select(['*'])
                                ->from(['tbl_hojavida_contratogeneral'])
                                ->where(['=','anulado',0])
                                ->all();
      }else{
        $varListaContratos = (new \yii\db\Query())
                                ->select(['*'])
                                ->from(['tbl_hojavida_contratogeneral'])
                                ->where(['=','usua_id',$sesiones])
                                ->andwhere(['=','anulado',0])
                                ->all();
      }     

      $varServiciosRegistrados = (new \yii\db\Query())
                                ->select(['id_dp_clientes'])
                                ->from(['tbl_hojavida_contratogeneral'])
                                ->where(['=','anulado',0])
                                ->groupby(['id_dp_clientes'])
                                ->count();

      $varServiciosCompletos = (new \yii\db\Query())
                                ->select(['id_dp_clientes'])
                                ->from(['tbl_proceso_cliente_centrocosto'])
                                ->where(['=','anulado',0])
                                ->andwhere(['=','estado',1])
                                ->groupby(['id_dp_clientes'])
                                ->count();

      $varServiciosNoRegistrados = $varServiciosCompletos - $varServiciosRegistrados;

      $varPcrcRegistrados = (new \yii\db\Query())
                                ->select(['cod_pcrc'])
                                ->from(['tbl_hojavida_contratopcrc'])
                                ->where(['=','anulado',0])
                                ->groupby(['cod_pcrc'])
                                ->count();

      $varPcrcCompletos = (new \yii\db\Query())
                                ->select(['cod_pcrc'])
                                ->from(['tbl_proceso_cliente_centrocosto'])
                                ->where(['=','anulado',0])
                                ->andwhere(['=','estado',1])
                                ->groupby(['cod_pcrc'])
                                ->count();

      $varPcrcNoRegistrados = $varPcrcCompletos - $varPcrcRegistrados;

      $varPorcentajeServicios = round(($varServiciosRegistrados / 100) * $varServiciosCompletos, 2);
      $varRestantesPorcentaje = round(100 - $varPorcentajeServicios, 2); 
      
      return $this->render('index',[
        'dataProviderhv' => $dataProviderhv,
        'modelos' =>  $modelos,
        'varListaClientesResumen' => $varListaClientesResumen,
        'varListaDecisores' => $varListaDecisores,
        'varListaDecisorEstrategico' => $varListaDecisorEstrategico,
        'varListaDecisorOperativo' => $varListaDecisorOperativo,
        'arrayListaDirector' => $arrayListaDirector,
        'arrayListaDirectorCantidad' => $arrayListaDirectorCantidad,
        'arrayListaCliente' => $arrayListaCliente,
        'arrayListaClienteCantidad' => $arrayListaClienteCantidad,
        'varListaContratos' => $varListaContratos,
        'varServiciosRegistrados' => $varServiciosRegistrados,
        'varServiciosNoRegistrados' => $varServiciosNoRegistrados,
        'varPcrcRegistrados' => $varPcrcRegistrados,
        'varPcrcNoRegistrados' => $varPcrcNoRegistrados,
        'varPorcentajeServicios' => $varPorcentajeServicios,
        'varRestantesPorcentaje' => $varRestantesPorcentaje,
      ]);
    }

    public function actionSeleccioncontrato(){
      $model = new HvInfopersonal();

      $form = Yii::$app->request->post();
      if($model->load($form)){
        $varIdClientes = $model->cliente;
        $varLitaPcrc = $model->pcrc;
        $varListaDirectorCc = $model->director;

        $arrayDataDirector = array();
        for ($i=0; $i < count($varListaDirectorCc); $i++) { 
          array_push($arrayDataDirector, substr($varListaDirectorCc[$i],0,-4));
        }

        Yii::$app->db->createCommand()->insert('tbl_hojavida_contratogeneral',[
                    'id_dp_clientes' => $varIdClientes,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute();

        $varIdContratoGeneral = (new \yii\db\Query())
                                ->select(['max(id_contratogeneral)'])
                                ->from(['tbl_hojavida_contratogeneral'])
                                ->scalar();

        $varListasPcrc = (new \yii\db\Query())
                          ->select(['cod_pcrc'])
                          ->from(['tbl_proceso_cliente_centrocosto'])
                          ->where(['IN','cod_pcrc',$varLitaPcrc])
                          ->andwhere(['=','estado',1])
                          ->andwhere(['=','anulado',0])
                          ->groupby(['cod_pcrc'])
                          ->all();

        foreach ($varListasPcrc as $key => $value) {
          Yii::$app->db->createCommand()->insert('tbl_hojavida_contratopcrc',[
                    'id_contratogeneral' => $varIdContratoGeneral,
                    'cod_pcrc' => $value['cod_pcrc'],
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute();
        }

        $varListasDirectores = (new \yii\db\Query())
                          ->select(['documento_director'])
                          ->from(['tbl_proceso_cliente_centrocosto'])
                          ->where(['IN','documento_director',$arrayDataDirector])
                          ->andwhere(['=','estado',1])
                          ->andwhere(['=','anulado',0])
                          ->groupby(['documento_director'])
                          ->all();

        foreach ($varListasDirectores as $key => $value) {
          Yii::$app->db->createCommand()->insert('tbl_hojavida_contratodirector',[
                    'id_contratogeneral' => $varIdContratoGeneral,
                    'director_cedula' => $value['documento_director'],
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute();
        }        

        return $this->redirect(array('informacioncontrato','id_contrato'=>$varIdContratoGeneral));
        
      }

      return $this->renderAjax('seleccioncontrato',[
        'model' => $model,
      ]);
    }

    public function actionInformacioncontrato($id_contrato){
      $model = new HvInfopersonal();

      $varNombreCliente = (new \yii\db\Query())
                          ->select(['tbl_proceso_cliente_centrocosto.cliente'])
                          ->from(['tbl_proceso_cliente_centrocosto'])
                          ->join('LEFT OUTER JOIN', 'tbl_hojavida_contratogeneral',
                              'tbl_proceso_cliente_centrocosto.id_dp_clientes = tbl_hojavida_contratogeneral.id_dp_clientes')
                          ->where(['=','tbl_hojavida_contratogeneral.id_contratogeneral',$id_contrato])
                          ->andwhere(['=','tbl_hojavida_contratogeneral.anulado',0])
                          ->groupby(['tbl_proceso_cliente_centrocosto.id_dp_clientes'])
                          ->Scalar();

      $varNombrePcrcs = (new \yii\db\Query())
                          ->select(['tbl_proceso_cliente_centrocosto.pcrc','tbl_proceso_cliente_centrocosto.cod_pcrc'])
                          ->from(['tbl_proceso_cliente_centrocosto'])
                          ->join('LEFT OUTER JOIN', 'tbl_hojavida_contratopcrc',
                              'tbl_proceso_cliente_centrocosto.cod_pcrc = tbl_hojavida_contratopcrc.cod_pcrc')
                          ->join('LEFT OUTER JOIN', 'tbl_hojavida_contratogeneral',
                              'tbl_hojavida_contratopcrc.id_contratogeneral = tbl_hojavida_contratogeneral.id_contratogeneral')
                          ->where(['=','tbl_hojavida_contratogeneral.id_contratogeneral',$id_contrato])
                          ->andwhere(['=','tbl_hojavida_contratogeneral.anulado',0])
                          ->groupby(['tbl_proceso_cliente_centrocosto.pcrc'])
                          ->All();

      $varNombreDirectores = (new \yii\db\Query())
                          ->select(['tbl_proceso_cliente_centrocosto.director_programa'])
                          ->from(['tbl_proceso_cliente_centrocosto'])

                          ->join('LEFT OUTER JOIN', 'tbl_hojavida_contratodirector',
                              'tbl_proceso_cliente_centrocosto.documento_director = tbl_hojavida_contratodirector.director_cedula')

                          ->join('LEFT OUTER JOIN', 'tbl_hojavida_contratogeneral',
                              'tbl_hojavida_contratodirector.id_contratogeneral = tbl_hojavida_contratogeneral.id_contratogeneral')

                          ->where(['=','tbl_hojavida_contratogeneral.id_contratogeneral',$id_contrato])
                          ->andwhere(['=','tbl_hojavida_contratogeneral.anulado',0])
                          ->groupby(['tbl_proceso_cliente_centrocosto.director_programa'])
                          ->All();

      $vardataProviderPersona = (new \yii\db\Query())
                          ->select(['*'])
                          ->from(['tbl_hojavida_bloquepersona'])
                          ->where(['=','tbl_hojavida_bloquepersona.id_contratogeneral',$id_contrato])
                          ->andwhere(['=','tbl_hojavida_bloquepersona.anulado',0])
                          ->All();

      $vardataProviderentregable = (new \yii\db\Query())
                          ->select(['*'])
                          ->from(['tbl_hojavida_bloqueinformes'])
                          ->where(['=','tbl_hojavida_bloqueinformes.id_contratogeneral',$id_contrato])
                          ->andwhere(['=','tbl_hojavida_bloqueinformes.anulado',0])
                          ->All();

      $vardataProviderherramientas = (new \yii\db\Query())
                          ->select(['*'])
                          ->from(['tbl_hojavida_bloqueherramienta'])
                          ->where(['=','tbl_hojavida_bloqueherramienta.id_contratogeneral',$id_contrato])
                          ->andwhere(['=','tbl_hojavida_bloqueherramienta.anulado',0])
                          ->All();

      $vardataProvidermetricas = (new \yii\db\Query())
                          ->select(['*'])
                          ->from(['tbl_hojavida_bloquekpis'])
                          ->where(['=','tbl_hojavida_bloquekpis.id_contratogeneral',$id_contrato])
                          ->andwhere(['=','tbl_hojavida_bloquekpis.anulado',0])
                          ->All();

      $vardataExclusivas = (new \yii\db\Query())
                          ->select(['*'])
                          ->from(['tbl_hojavida_bloquesalas'])
                          ->where(['=','tbl_hojavida_bloquesalas.id_contratogeneral',$id_contrato])
                          ->andwhere(['=','tbl_hojavida_bloquesalas.anulado',0])
                          ->All();

      $form = Yii::$app->request->post();     
      if($model->load($form)){
        Yii::$app->db->createCommand()->insert('tbl_hojavida_bloquesalas',[
                      'exclusivas' => $model->cliente,
                      'comentarios' => $model->director,
                      'id_contratogeneral' => $id_contrato,
                      'fechacreacion' => date('Y-m-d'),
                      'anulado' => 0,
                      'usua_id' => Yii::$app->user->identity->id,                                       
                  ])->execute(); 

        return $this->redirect(['index']);
      }



      return $this->render('informacioncontrato',[
        'model' => $model,
        'varNombreCliente' => $varNombreCliente,
        'varNombrePcrcs' => $varNombrePcrcs,
        'varNombreDirectores' => $varNombreDirectores,
        'vardataProviderPersona' => $vardataProviderPersona,
        'id_contrato' => $id_contrato,
        'vardataProviderentregable' => $vardataProviderentregable,
        'vardataProviderherramientas' => $vardataProviderherramientas,
        'vardataProvidermetricas' => $vardataProvidermetricas,
        'vardataExclusivas' => $vardataExclusivas,
      ]);
    }

    public function actionImportarpersona($id){
      $modelpersona = new HojavidaDatapersonal(); 
      $model = new UploadForm2();
      $ruta = null;

      $form = Yii::$app->request->post();     

      if($model->load($form)){

        $model->file = UploadedFile::getInstance($model, 'file');
        var_dump("Ingresa");
        if ($model->file && $model->validate()) {
          var_dump("Ingresa");
          foreach ($model->file as $file) {
            $ruta = 'images/contratos/'."bloque1_".$id."_".time()."_".$model->file->baseName. ".".$model->file->extension;
            $model->file->saveAs( $ruta ); 
          }
        } 
         
        if ($ruta != null) {
          $varRutaAnexoBone = (new \yii\db\Query())
                          ->select(['*'])
                          ->from(['tbl_hojavida_archivos'])
                          ->where(['=','anulado',0])
                          ->andwhere(['=','bloques',1])
                          ->andwhere(['=','anexo',$ruta])
                          ->count();

          if ($varRutaAnexoBone == 0) {
            Yii::$app->db->createCommand()->insert('tbl_hojavida_archivos',[
                      'bloques' => 1,
                      'anexo' => $ruta,
                      'fechacreacion' => date('Y-m-d'),
                      'anulado' => 0,
                      'usua_id' => Yii::$app->user->identity->id,                                       
                  ])->execute(); 
          }
        }else{
          $ruta = null;
        }

      }

      if ($modelpersona->load($form)) {

        Yii::$app->db->createCommand()->insert('tbl_hojavida_bloquepersona',[
                    'id_contratogeneral' => $id,
                    'id_hvroles' => $modelpersona->clasificacion,
                    'perfil' => $modelpersona->direccion_oficina,
                    'funciones' => $modelpersona->email,
                    'salario' => $modelpersona->numero_movil,
                    'variable' => $modelpersona->direccion_casa,
                    'totalsalario' => $modelpersona->identificacion,
                    'tramocontrol' => $modelpersona->numero_fijo,
                    'ratiopricing' => $modelpersona->nombre_full,
                    'rutaanexo' => $ruta,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute(); 

        return $this->redirect(array('informacioncontrato','id_contrato'=>$id));
      }
      
      return $this->renderAjax('importarpersona',[
        'modelpersona' => $modelpersona,
        'model' => $model,
        'id' => $id,
      ]);
    }

    public function actionImportarentregables($id){
      $modelpersona = new HojavidaDatapersonal(); 
      $model = new UploadForm2();
      $ruta = null;

      $form = Yii::$app->request->post();     

      if($model->load($form)){

        $model->file = UploadedFile::getInstance($model, 'file');
        var_dump("Ingresa");
        if ($model->file && $model->validate()) {
          var_dump("Ingresa");
          foreach ($model->file as $file) {
            $ruta = 'images/contratos/'."bloque1_".$id."_".time()."_".$model->file->baseName. ".".$model->file->extension;
            $model->file->saveAs( $ruta ); 
          }
        }            

         
        if ($ruta != null) {
          $varRutaAnexoBone = (new \yii\db\Query())
                          ->select(['*'])
                          ->from(['tbl_hojavida_archivos'])
                          ->where(['=','anulado',0])
                          ->andwhere(['=','bloques',2])
                          ->andwhere(['=','anexo',$ruta])
                          ->count();

          if ($varRutaAnexoBone == 0) {
            Yii::$app->db->createCommand()->insert('tbl_hojavida_archivos',[
                      'bloques' => 2,
                      'anexo' => $ruta,
                      'fechacreacion' => date('Y-m-d'),
                      'anulado' => 0,
                      'usua_id' => Yii::$app->user->identity->id,                                       
                  ])->execute(); 
          }
        }else{
          $ruta = null;
        }

      }

      if ($modelpersona->load($form)) {

        Yii::$app->db->createCommand()->insert('tbl_hojavida_bloqueinformes',[
                    'id_contratogeneral' => $id,
                    'id_hvinforme' => $modelpersona->clasificacion,
                    'alcance' => $modelpersona->numero_fijo,
                    'id_hvperiocidad' => $modelpersona->nombre_full,
                    'detalle' => $modelpersona->email,
                    'rutaanexoinforme' => $ruta,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute(); 

        return $this->redirect(array('informacioncontrato','id_contrato'=>$id));
      }
      
      return $this->renderAjax('importarentregables',[
        'modelpersona' => $modelpersona,
        'model' => $model,
        'id' => $id,
      ]);
    }

    public function actionImportarherramientas($id){
      $modelpersona = new HojavidaDatapersonal(); 
      $model = new UploadForm2();
      $ruta = null;

      $form = Yii::$app->request->post();     

      if($model->load($form)){

        $model->file = UploadedFile::getInstance($model, 'file');
        var_dump("Ingresa");
        if ($model->file && $model->validate()) {
          var_dump("Ingresa");
          foreach ($model->file as $file) {
            $ruta = 'images/contratos/'."bloque1_".$id."_".time()."_".$model->file->baseName. ".".$model->file->extension;
            $model->file->saveAs( $ruta ); 
          }
        }            

         
        if ($ruta != null) {
          $varRutaAnexoBone = (new \yii\db\Query())
                          ->select(['*'])
                          ->from(['tbl_hojavida_archivos'])
                          ->where(['=','anulado',0])
                          ->andwhere(['=','bloques',3])
                          ->andwhere(['=','anexo',$ruta])
                          ->count();

          if ($varRutaAnexoBone == 0) {
            Yii::$app->db->createCommand()->insert('tbl_hojavida_archivos',[
                      'bloques' => 3,
                      'anexo' => $ruta,
                      'fechacreacion' => date('Y-m-d'),
                      'anulado' => 0,
                      'usua_id' => Yii::$app->user->identity->id,                                       
                  ])->execute(); 
          }
        }else{
          $ruta = null;
        }

      }

      if ($modelpersona->load($form)) {

        Yii::$app->db->createCommand()->insert('tbl_hojavida_bloqueherramienta',[
                    'id_contratogeneral' => $id,
                    'alcance' => $modelpersona->clasificacion,
                    'funcionalidades' => $modelpersona->numero_fijo,
                    'detalle' => $modelpersona->email,
                    'rutaanexoherramienta' => $ruta,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute(); 

        return $this->redirect(array('informacioncontrato','id_contrato'=>$id));
      }
      
      return $this->renderAjax('importarherramientas',[
        'modelpersona' => $modelpersona,
        'model' => $model,
        'id' => $id,
      ]);
    }

    public function actionImportarmetricas($id){
      $modelpersona = new HojavidaDatapersonal(); 
      $model = new UploadForm2();
      $ruta = null;

      $form = Yii::$app->request->post();     

      if($model->load($form)){

        $model->file = UploadedFile::getInstance($model, 'file');
        var_dump("Ingresa");
        if ($model->file && $model->validate()) {
          var_dump("Ingresa");
          foreach ($model->file as $file) {
            $ruta = 'images/contratos/'."bloque1_".$id."_".time()."_".$model->file->baseName. ".".$model->file->extension;
            $model->file->saveAs( $ruta ); 
          }
        }            

         
        if ($ruta != null) {
          $varRutaAnexoBone = (new \yii\db\Query())
                          ->select(['*'])
                          ->from(['tbl_hojavida_archivos'])
                          ->where(['=','anulado',0])
                          ->andwhere(['=','bloques',4])
                          ->andwhere(['=','anexo',$ruta])
                          ->count();

          if ($varRutaAnexoBone == 0) {
            Yii::$app->db->createCommand()->insert('tbl_hojavida_archivos',[
                      'bloques' => 4,
                      'anexo' => $ruta,
                      'fechacreacion' => date('Y-m-d'),
                      'anulado' => 0,
                      'usua_id' => Yii::$app->user->identity->id,                                       
                  ])->execute(); 
          }
        }else{
          $ruta = null;
        }

      }

      if ($modelpersona->load($form)) {

        Yii::$app->db->createCommand()->insert('tbl_hojavida_bloquekpis',[
                    'id_contratogeneral' => $id,
                    'id_hvmetrica' => $modelpersona->clasificacion,
                    'obtjetivo' => $modelpersona->numero_fijo,
                    'penalizacion' => $modelpersona->email,
                    'rango' => $modelpersona->direccion_oficina,
                    'rutaanexokpis' => $ruta,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute(); 

        return $this->redirect(array('informacioncontrato','id_contrato'=>$id));
      }
      
      return $this->renderAjax('importarmetricas',[
        'modelpersona' => $modelpersona,
        'model' => $model,
        'id' => $id,
      ]);
    }

    public function actionContratorol(){
      $model = new Hojavidaroles();

      $vardataProviderRol = (new \yii\db\Query())
                          ->select(['*'])
                          ->from(['tbl_hojavida_roles'])
                          ->where(['=','anulado',0])
                          ->All();

      $form = Yii::$app->request->post();
      if($model->load($form)){
        Yii::$app->db->createCommand()->insert('tbl_hojavida_roles',[
                    'hvroles' => $model->hvroles,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute();

        return $this->redirect('contratorol');
      }

      return $this->render('contratorol',[
        'model' => $model,
        'vardataProviderRol' => $vardataProviderRol,
      ]);
    }

    public function actionEliminarbloquepersona($id,$id_contrato){
      Yii::$app->db->createCommand('DELETE FROM tbl_hojavida_bloquepersona WHERE id_bloquepersona=:id')->bindParam(':id',$id)->execute();

      return $this->redirect(array('informacioncontrato','id_contrato'=>$id_contrato));
    }

    public function actionEliminarbloqueentregable($id,$id_contrato){
      Yii::$app->db->createCommand('DELETE FROM tbl_hojavida_bloqueinformes WHERE id_bloqueinformes=:id')->bindParam(':id',$id)->execute();

      return $this->redirect(array('informacioncontrato','id_contrato'=>$id_contrato));
    }

    public function actionEliminarbloqueherramienta($id,$id_contrato){
      Yii::$app->db->createCommand('DELETE FROM tbl_hojavida_bloqueherramienta WHERE id_bloqueherramienta=:id')->bindParam(':id',$id)->execute();

      return $this->redirect(array('informacioncontrato','id_contrato'=>$id_contrato));
    }

    public function actionEliminarbloquekpis($id,$id_contrato){
      Yii::$app->db->createCommand('DELETE FROM tbl_hojavida_bloquekpis WHERE id_bloquekpis=:id')->bindParam(':id',$id)->execute();

      return $this->redirect(array('informacioncontrato','id_contrato'=>$id_contrato));
    }

    public function actionEliminarbloqueexclusiva($id,$id_contrato){
      Yii::$app->db->createCommand('DELETE FROM tbl_hojavida_bloquesalas WHERE id_bloquesalas=:id')->bindParam(':id',$id)->execute();

      return $this->redirect(array('informacioncontrato','id_contrato'=>$id_contrato));
    }

    public function actionEliminarrol($id){
      Hojavidaroles::findOne($id)->delete();

      return $this->redirect(['contratorol']);
    }

    public function actionContratoinforme(){
      $model = new Hojavidainforme();

      $vardataProviderInforme = (new \yii\db\Query())
                          ->select(['*'])
                          ->from(['tbl_hojavida_informe'])
                          ->where(['=','anulado',0])
                          ->All();

      $form = Yii::$app->request->post();
      if($model->load($form)){
        Yii::$app->db->createCommand()->insert('tbl_hojavida_informe',[
                    'hvinforme' => $model->hvinforme,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute();

        return $this->redirect('contratoinforme');
      }

      return $this->render('contratoinforme',[
        'model' => $model,
        'vardataProviderInforme' => $vardataProviderInforme,
      ]);
    }

    public function actionEliminarinforme($id){
      Hojavidainforme::findOne($id)->delete();

      return $this->redirect(['contratoinforme']);
    }

    public function actionContratopriocidad(){
      $model = new Hojavidaperiocidad();

      $vardataProviderperiocidad = (new \yii\db\Query())
                          ->select(['*'])
                          ->from(['tbl_hojavida_periocidad'])
                          ->where(['=','anulado',0])
                          ->All();

      $form = Yii::$app->request->post();
      if($model->load($form)){
        Yii::$app->db->createCommand()->insert('tbl_hojavida_periocidad',[
                    'hvperiocidad' => $model->hvperiocidad,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute();

        return $this->redirect('contratopriocidad');
      }

      return $this->render('contratopriocidad',[
        'model' => $model,
        'vardataProviderperiocidad' => $vardataProviderperiocidad,
      ]);
    }

    public function actionEliminarperiocidad($id){
      Hojavidaperiocidad::findOne($id)->delete();

      return $this->redirect(['contratopriocidad']);
    }

    public function actionContratometrica(){
      $model = new Hojavidametricas();

      $vardataProvidermetrica = (new \yii\db\Query())
                          ->select(['*'])
                          ->from(['tbl_hojavida_metricas'])
                          ->where(['=','anulado',0])
                          ->All();

      $form = Yii::$app->request->post();
      if($model->load($form)){
        Yii::$app->db->createCommand()->insert('tbl_hojavida_metricas',[
                    'hvmetrica' => $model->hvmetrica,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute();

        return $this->redirect('contratometrica');
      }

      return $this->render('contratometrica',[
        'model' => $model,
        'vardataProvidermetrica' => $vardataProvidermetrica,
      ]);
    }

    public function actionEliminarmetrica($id){
      Hojavidametricas::findOne($id)->delete();

      return $this->redirect(['contratometrica']);
    }

    public function actionDescargaservicio($id_contrato){
      $model = new HojavidaDatapersonal();

      $varNombreClienteServicio = (new \yii\db\Query())
                          ->select(['tbl_proceso_cliente_centrocosto.cliente'])
                          ->from(['tbl_proceso_cliente_centrocosto'])
                          ->join('LEFT OUTER JOIN', 'tbl_hojavida_contratogeneral',
                              'tbl_proceso_cliente_centrocosto.id_dp_clientes = tbl_hojavida_contratogeneral.id_dp_clientes')
                          ->where(['=','tbl_hojavida_contratogeneral.id_contratogeneral',$id_contrato])
                          ->andwhere(['=','tbl_hojavida_contratogeneral.anulado',0])
                          ->groupby(['tbl_proceso_cliente_centrocosto.id_dp_clientes'])
                          ->Scalar();

      $varNombrePcrcsServicio = (new \yii\db\Query())
                          ->select(['tbl_proceso_cliente_centrocosto.pcrc','tbl_proceso_cliente_centrocosto.cod_pcrc'])
                          ->from(['tbl_proceso_cliente_centrocosto'])
                          ->join('LEFT OUTER JOIN', 'tbl_hojavida_contratopcrc',
                              'tbl_proceso_cliente_centrocosto.cod_pcrc = tbl_hojavida_contratopcrc.cod_pcrc')
                          ->join('LEFT OUTER JOIN', 'tbl_hojavida_contratogeneral',
                              'tbl_hojavida_contratopcrc.id_contratogeneral = tbl_hojavida_contratogeneral.id_contratogeneral')
                          ->where(['=','tbl_hojavida_contratogeneral.id_contratogeneral',$id_contrato])
                          ->andwhere(['=','tbl_hojavida_contratogeneral.anulado',0])
                          ->groupby(['tbl_proceso_cliente_centrocosto.pcrc'])
                          ->All();

      $varNombreDirectoresServicio = (new \yii\db\Query())
                          ->select(['tbl_proceso_cliente_centrocosto.director_programa'])
                          ->from(['tbl_proceso_cliente_centrocosto'])

                          ->join('LEFT OUTER JOIN', 'tbl_hojavida_contratodirector',
                              'tbl_proceso_cliente_centrocosto.documento_director = tbl_hojavida_contratodirector.director_cedula')

                          ->join('LEFT OUTER JOIN', 'tbl_hojavida_contratogeneral',
                              'tbl_hojavida_contratodirector.id_contratogeneral = tbl_hojavida_contratogeneral.id_contratogeneral')

                          ->where(['=','tbl_hojavida_contratogeneral.id_contratogeneral',$id_contrato])
                          ->andwhere(['=','tbl_hojavida_contratogeneral.anulado',0])
                          ->groupby(['tbl_proceso_cliente_centrocosto.director_programa'])
                          ->All();

      $vardataProviderPersonaServicio = (new \yii\db\Query())
                          ->select(['*'])
                          ->from(['tbl_hojavida_bloquepersona'])
                          ->where(['=','tbl_hojavida_bloquepersona.id_contratogeneral',$id_contrato])
                          ->andwhere(['=','tbl_hojavida_bloquepersona.anulado',0])
                          ->All();

      $vardataProviderentregableServicio = (new \yii\db\Query())
                          ->select(['*'])
                          ->from(['tbl_hojavida_bloqueinformes'])
                          ->where(['=','tbl_hojavida_bloqueinformes.id_contratogeneral',$id_contrato])
                          ->andwhere(['=','tbl_hojavida_bloqueinformes.anulado',0])
                          ->All();

      $vardataProviderherramientasServicio = (new \yii\db\Query())
                          ->select(['*'])
                          ->from(['tbl_hojavida_bloqueherramienta'])
                          ->where(['=','tbl_hojavida_bloqueherramienta.id_contratogeneral',$id_contrato])
                          ->andwhere(['=','tbl_hojavida_bloqueherramienta.anulado',0])
                          ->All();

      $vardataProvidermetricasServicio = (new \yii\db\Query())
                          ->select(['*'])
                          ->from(['tbl_hojavida_bloquekpis'])
                          ->where(['=','tbl_hojavida_bloquekpis.id_contratogeneral',$id_contrato])
                          ->andwhere(['=','tbl_hojavida_bloquekpis.anulado',0])
                          ->All();

      $vardataExclusivasServicio = (new \yii\db\Query())
                          ->select(['*'])
                          ->from(['tbl_hojavida_bloquesalas'])
                          ->where(['=','tbl_hojavida_bloquesalas.id_contratogeneral',$id_contrato])
                          ->andwhere(['=','tbl_hojavida_bloquesalas.anulado',0])
                          ->All();
      

      return $this->renderAjax('descargaservicio',[
        'model' => $model,
        'varNombreClienteServicio' => $varNombreClienteServicio,
        'varNombrePcrcsServicio' => $varNombrePcrcsServicio,
        'varNombreDirectoresServicio' => $varNombreDirectoresServicio,
        'vardataProviderPersonaServicio' => $vardataProviderPersonaServicio,
        'vardataProviderentregableServicio' => $vardataProviderentregableServicio,
        'vardataProviderherramientasServicio' => $vardataProviderherramientasServicio,
        'vardataProvidermetricasServicio' => $vardataProvidermetricasServicio,
        'vardataExclusivasServicio' => $vardataExclusivasServicio,
        'id_contrato' => $id_contrato,
      ]);
    }

    public function actionDescargageneral(){
      $model = new HojavidaDatapersonal();

      $form = Yii::$app->request->post();
      if($model->load($form)){
        $varCorreo = $model->file;

        $varSessionesGeneral = Yii::$app->user->identity->id;
        
        $varRolGeneral =  new Query;
        $varRolGeneral     ->select(['tbl_roles.role_id'])
                    ->from('tbl_roles')
                    ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                                'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                    ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                    ->where('tbl_usuarios.usua_id = :sesiones')
                    ->addParams([':sesiones'=>$varSessionesGeneral]);                    
        $command = $varRolGeneral->createCommand();
        $varRolesGeneral = $command->queryScalar();

        if ($varRolesGeneral == "270") {
          $varListaContratosGeneral = (new \yii\db\Query())
                                ->select(['*'])
                                ->from(['tbl_hojavida_contratogeneral'])
                                ->where(['=','anulado',0])
                                ->all();
        }else{
          $varListaContratosGeneral = (new \yii\db\Query())
                                ->select(['*'])
                                ->from(['tbl_hojavida_contratogeneral'])
                                ->where(['=','usua_id',$varSessionesGeneral])
                                ->andwhere(['=','anulado',0])
                                ->all();
        }

        $phpExc = new \PHPExcel();
        $phpExc->getProperties()
            ->setCreator("Konecta")
            ->setLastModifiedBy("Konecta")
            ->setTitle("Archivo - Lista General de Servicios con Contrato")
            ->setSubject("Procesos de Contrato para servicio Registrados")
            ->setDescription("El actual archivo permite verificar de forma general los servicios que tiene registrado contratos.")
            ->setKeywords("Listado de Servicios con Contrato");
        $phpExc->setActiveSheetIndex(0);

        $phpExc->getActiveSheet()->setShowGridlines(False);

        $styleArray = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $styleColor = array( 
            'fill' => array( 
                'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                'color' => array('rgb' => '28559B'),
            )
        );

        $styleArrayTitle = array(
            'font' => array(
              'bold' => false,
              'color' => array('rgb' => 'FFFFFF')
            )
        );

        $styleArraySubTitle2 = array(              
            'fill' => array( 
                'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                'color' => array('rgb' => 'C6C6C6'),
            )
        );  

        // ARRAY STYLE FONT COLOR AND TEXT ALIGN CENTER
        $styleArrayBody = array(
            'font' => array(
                'bold' => false,
                'color' => array('rgb' => '2F4F4F')
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => 'DDDDDD')
                )
            )
        );

        $phpExc->getDefaultStyle()->applyFromArray($styleArrayBody);

        $phpExc->getActiveSheet()->SetCellValue('A1','KONECTA - CX MANAGEMENT');
        $phpExc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArrayTitle);
        $phpExc->setActiveSheetIndex(0)->mergeCells('A1:h1');

        $phpExc->getActiveSheet()->SetCellValue('A2','SERVICIO DEL CONTRATO');
        $phpExc->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArraySubTitle2);

        $phpExc->getActiveSheet()->SetCellValue('B2','USUARIO REGISTRADO');
        $phpExc->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleArraySubTitle2);

        $phpExc->getActiveSheet()->SetCellValue('C2','FECHA INGRESO CONTRATO');
        $phpExc->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleArraySubTitle2);

        $phpExc->getActiveSheet()->SetCellValue('D2','REQUERIMIENTOS SOBRE ROLES');
        $phpExc->getActiveSheet()->getStyle('D2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleArraySubTitle2);

        $phpExc->getActiveSheet()->SetCellValue('E2','REQUERIMIENTOS SOBRE ENTREGABLES');
        $phpExc->getActiveSheet()->getStyle('E2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleArraySubTitle2);

        $phpExc->getActiveSheet()->SetCellValue('F2','REQUERIMIENTOS SOBRE HERRAMIENTAS');
        $phpExc->getActiveSheet()->getStyle('F2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleArraySubTitle2);

        $phpExc->getActiveSheet()->SetCellValue('G2','REQUERIMIENTOS SOBRE METRICAS');
        $phpExc->getActiveSheet()->getStyle('G2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleArraySubTitle2);

        $phpExc->getActiveSheet()->SetCellValue('H2','REQUERIMIENTOS SOBRE RECURSOS FISICOS');
        $phpExc->getActiveSheet()->getStyle('H2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleArraySubTitle2);

        $numCellGeneral = 3;
        foreach ($varListaContratosGeneral as $key => $value) {
          $numCellGeneral++;

          $varServicioNombre = (new \yii\db\Query())
                              ->select(['cliente'])
                              ->from(['tbl_proceso_cliente_centrocosto'])
                              ->where(['=','id_dp_clientes',$value['id_dp_clientes']])
                              ->andwhere(['=','estado',1])
                              ->andwhere(['=','anulado',0])
                              ->groupby(['cliente'])
                              ->Scalar();

          $varUsuanombre = (new \yii\db\Query())
                          ->select(['usua_nombre'])
                          ->from(['tbl_usuarios'])
                          ->where(['=','usua_id',$value['usua_id']])
                          ->groupby(['usua_nombre'])
                          ->Scalar();

          $varBloquePersona = (new \yii\db\Query())
                              ->select(['*'])
                              ->from(['tbl_hojavida_bloquepersona'])
                              ->where(['=','id_contratogeneral',$value['id_contratogeneral']])
                              ->andwhere(['=','anulado',0])
                              ->count();

          $varBloqueEntregable = (new \yii\db\Query())
                                ->select(['*'])
                                ->from(['tbl_hojavida_bloqueinformes'])
                                ->where(['=','id_contratogeneral',$value['id_contratogeneral']])
                                ->andwhere(['=','anulado',0])
                                ->count();

          $varBloqueHerramienta = (new \yii\db\Query())
                                  ->select(['*'])
                                  ->from(['tbl_hojavida_bloqueherramienta'])
                                  ->where(['=','id_contratogeneral',$value['id_contratogeneral']])
                                  ->andwhere(['=','anulado',0])
                                  ->count();

          $varBloqueMetricas = (new \yii\db\Query())
                              ->select(['*'])
                              ->from(['tbl_hojavida_bloquekpis'])
                              ->where(['=','id_contratogeneral',$value['id_contratogeneral']])
                              ->andwhere(['=','anulado',0])
                              ->count();

          $varSalasExclusivas = (new \yii\db\Query())
                                ->select(['if(exclusivas=1,"Si","No") as Exclusiva'])
                                ->from(['tbl_hojavida_bloquesalas'])
                                ->where(['=','id_contratogeneral',$value['id_contratogeneral']])
                                ->andwhere(['=','anulado',0])
                                ->Scalar();

          $phpExc->getActiveSheet()->setCellValue('A'.$numCellGeneral, $varServicioNombre); 
          $phpExc->getActiveSheet()->setCellValue('B'.$numCellGeneral, $varUsuanombre); 
          $phpExc->getActiveSheet()->setCellValue('C'.$numCellGeneral, $value['fechacreacion']); 
          if ($varBloquePersona != 0) {
            $phpExc->getActiveSheet()->setCellValue('D'.$numCellGeneral, 'X'); 
          }else{
            $phpExc->getActiveSheet()->setCellValue('D'.$numCellGeneral, '-'); 
          }
          if ($varBloqueEntregable != 0) {
            $phpExc->getActiveSheet()->setCellValue('E'.$numCellGeneral, 'X'); 
          }else{
            $phpExc->getActiveSheet()->setCellValue('E'.$numCellGeneral, '-'); 
          }
          if ($varBloqueHerramienta != 0) {
            $phpExc->getActiveSheet()->setCellValue('F'.$numCellGeneral, 'X'); 
          }else{
            $phpExc->getActiveSheet()->setCellValue('F'.$numCellGeneral, '-'); 
          }
          if ($varBloqueMetricas != 0) {
            $phpExc->getActiveSheet()->setCellValue('G'.$numCellGeneral, 'X'); 
          }else{
            $phpExc->getActiveSheet()->setCellValue('G'.$numCellGeneral, '-'); 
          }
          if ($varSalasExclusivas != 0) {
            $phpExc->getActiveSheet()->setCellValue('H'.$numCellGeneral, 'X'); 
          }else{
            $phpExc->getActiveSheet()->setCellValue('H'.$numCellGeneral, '-'); 
          }
        }

        $hoy = getdate();
        $hoy = $hoy['year']."_".$hoy['month']."_".$hoy['mday']."ListadoGeneral_ServiciosContratos";

        $objWriter = \PHPExcel_IOFactory::createWriter($phpExc, 'Excel5');

        $tmpFile = tempnam(sys_get_temp_dir(), $hoy);
        $tmpFile.= ".xls";

        $objWriter->save($tmpFile);

        $message = "<html><body>";
        $message .= "<h3>Listado General de Servicio Con Contrato Registrados en CXM.</h3>";
        $message .= "</body></html>";

        Yii::$app->mailer->compose()
                    ->setTo($varCorreo)
                    ->setFrom(Yii::$app->params['email_satu_from'])
                    ->setSubject("Archivo Listado General - Servicios Con Contrato CXM")
                    ->attach($tmpFile)
                    ->setHtmlBody($message)
                    ->send();

        return $this->redirect(['index']);
      }

      return $this->renderAjax('descargageneral',[
        'model' => $model,
      ]);
    }

    public function actionEventos(){
      $model = new HojavidaEventos();

      $form = Yii::$app->request->post();
      if($model->load($form)){

        $txtFecha = explode(" ", $model->fechacreacion);
        $varFechaInicio = $txtFecha[0];
        $varFechaFin = $txtFecha[2];

        Yii::$app->db->createCommand()->insert('tbl_hojavida_eventos',[
                    'nombre_evento' => $model->nombre_evento,
                    'tipo_evento' => $model->tipo_evento,
                    'hv_idciudad' => $model->hv_idciudad,  
                    'fecha_evento_inicio' => $varFechaInicio,
                    'fecha_evento_fin' => $varFechaFin,
                    'asistencia' => $model->asistencia,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute();

        return $this->redirect('eventos',['model'=>$model]);
      }else{
        #code
    }

      $dataProvider = Yii::$app->db->createCommand("
        SELECT e.hv_ideventos, e.nombre_evento, e.tipo_evento, e.fecha_evento_inicio, e.fecha_evento_fin, c.ciudad, e.asistencia 
          FROM tbl_hojavida_eventos e
              INNER JOIN tbl_hv_ciudad c ON c.hv_idciudad = e.hv_idciudad ")->queryAll();


      return $this->render('eventos',[
        'model' => $model,
        'dataProvider' => $dataProvider,
      ]);
    }

    public function actionEliminarevento($ideventos){
      $model = new HojavidaEventos();
      $varideventos = $ideventos;

      if ($varideventos != null) {
        $eventos = HojavidaEventos::findOne($varideventos);
        $eventos->delete();
      }      

      return $this->redirect('eventos',['model'=>$model]);
    }

    public function actionPaisciudad(){

      $dataProviderPais = HvPais::find()
                            ->asArray()
                            ->all();

      $dataProviderCiudad = HvCiudad::find()
                            ->asArray()
                            ->all();

      return $this->render('paisciudad',[
        'dataProviderPais' => $dataProviderPais,
        'dataProviderCiudad' => $dataProviderCiudad,
      ]);
    }

    public function actionCreapais(){
      $modelpais = new HvPais();

      $form = Yii::$app->request->post();
      if($modelpais->load($form)){

        Yii::$app->db->createCommand()->insert('tbl_hv_pais',[
              'pais' => $modelpais->pais,
              'fechacreacion' => date('Y-m-d'),
              'anulado' => 0,
              'usua_id' => Yii::$app->user->identity->id,                                       
          ])->execute();

        Yii::$app->db->createCommand()->insert('tbl_logs', [
          'usua_id' => Yii::$app->user->identity->id,
          'usuario' => Yii::$app->user->identity->username,
          'fechahora' => date('Y-m-d h:i:s'),
          'ip' => Yii::$app->getRequest()->getUserIP(),
          'accion' => 'Create',
          'tabla' => 'tbl_hv_pais'
        ])->execute(); 

        return $this->redirect('paisciudad');
      }else{
        #code
    }

      return $this->renderAjax('creapais',[
        'modelpais' => $modelpais,
      ]);
    }

    public function actionEliminarpais($idpais){
      $varidpais = $idpais;

      if ($varidpais != null) {
        $pais = HvPais::findOne($varidpais);
        $pais->delete();
      }      

      return $this->redirect('paisciudad');
    }

    public function actionCreaciudad(){      
      $modelciudad = new HvCiudad();

      $form = Yii::$app->request->post();
      if($modelciudad->load($form)){

        Yii::$app->db->createCommand()->insert('tbl_hv_ciudad',[
            'pais_id' => $modelciudad->pais_id,
            'ciudad' => $modelciudad->ciudad,
            'fechacreacion' => date('Y-m-d'),
            'anulado' => 0,
            'usua_id' => Yii::$app->user->identity->id,                                       
        ])->execute();

        Yii::$app->db->createCommand()->insert('tbl_logs', [
          'usua_id' => Yii::$app->user->identity->id,
          'usuario' => Yii::$app->user->identity->username,
          'fechahora' => date('Y-m-d h:i:s'),
          'ip' => Yii::$app->getRequest()->getUserIP(),
          'accion' => 'Create',
          'tabla' => 'tbl_hv_ciudad'
        ])->execute();

        return $this->redirect('paisciudad');
      }else{
        #code
    }

      return $this->renderAjax('creaciudad',[
        'modelciudad' => $modelciudad,
      ]);
    }

    public function actionEliminarciudad($idciudad){
      $varidciudad = $idciudad;

      if ($varidciudad != null) {
        $ciudad = HvCiudad::findOne($varidciudad);
        $ciudad->delete();
      }      

      return $this->redirect('paisciudad');
    }

    public function actionInformacionpersonal(){
      $model = new HojavidaDatapersonal();
      $model2 = new HojavidaDatalaboral();
      $model3 = new HojavidaDataacademica();
      $model4 = new HojavidaDatapcrc();
      $model5 = new HojavidaDatadirector();
      $model6 = new HojavidaDatagerente();
      $model7 = new HojavidaEventos();


      return $this->render('informacionpersonal',[
        'model' => $model,
        'model2' => $model2,
        'model3' => $model3,
        'model4' => $model4,
        'model5' => $model5,
        'model6' => $model6,
        'model7' => $model7,
      ]);
    }

    public function actionListarciudades(){
      $txtId = Yii::$app->request->get('id');

      if ($txtId) {
        $txtControl = \app\models\HvCiudad::find()->distinct()
                          ->where(['tbl_hv_ciudad.pais_id' => $txtId])
                          ->andwhere("tbl_hv_ciudad.anulado = 0")
                          ->count();            

        if ($txtControl > 0) {
          $varListaCiudad = \app\models\HvCiudad::find()
                          ->select(['tbl_hv_ciudad.hv_idciudad','tbl_hv_ciudad.ciudad'])->distinct()
                            ->where(['tbl_hv_ciudad.pais_id' => $txtId])
                            ->andwhere("tbl_hv_ciudad.anulado = 0")                 
                            ->orderBy(['tbl_hv_ciudad.ciudad' => SORT_DESC])
                            ->all();            
                    
          foreach ($varListaCiudad as $key => $value) {
            echo "<option value='" . $value->hv_idciudad. "'>" . $value->ciudad. "</option>";
          }
        }else{
          echo "<option>-</option>";
        }
      }else{
        echo "<option>No hay datos</option>";
      }

    }

    public function actionListarpcrcindex(){
      $txtId = Yii::$app->request->get('id');

      $varClienteID = null;
      $varDirectorCC = null;

      $varStingData = implode(";", $txtId);
      $varListData = explode(";", $varStingData);
      for ($i=0; $i < count($varListData); $i++) { 
        $varDirectorCC = $varListData[0];
        $varClienteID = $varListData[1];
      }

      if ($txtId) {
        $txtControl = \app\models\ProcesosClienteCentrocosto::find()->distinct()
                    ->where(['=','tbl_proceso_cliente_centrocosto.id_dp_clientes',$varClienteID])
                    ->andwhere(['=','tbl_proceso_cliente_centrocosto.documento_director',$varDirectorCC ])
                    ->andwhere("tbl_proceso_cliente_centrocosto.estado = 1")
                    ->count();          

        if ($txtControl > 0) {
          $varListaCiudad = \app\models\ProcesosClienteCentrocosto::find()
                            ->select(['tbl_proceso_cliente_centrocosto.cod_pcrc','tbl_proceso_cliente_centrocosto.pcrc'])->distinct()
                            ->where(['=','tbl_proceso_cliente_centrocosto.id_dp_clientes',$varClienteID])
                            ->andwhere(['=','tbl_proceso_cliente_centrocosto.documento_director',$varDirectorCC ])
                            ->andwhere("tbl_proceso_cliente_centrocosto.estado = 1") 
                            ->groupby(['tbl_proceso_cliente_centrocosto.cod_pcrc'])
                            ->all();            
      
          foreach ($varListaCiudad as $key => $value) {
            echo "<option value='" . $value->cod_pcrc. "'>" . $value->cod_pcrc." - ".$value->pcrc. "</option>";
          }
        }else{
          echo "<option>-</option>";
        }
      }else{
        echo "<option>No hay datos</option>";
      }

    }


    public function actionListardirectores(){
      $txtId = Yii::$app->request->get('id');

      if ($txtId) {
        $txtControl = \app\models\ProcesosClienteCentrocosto::find()->distinct()
                          ->where(['tbl_proceso_cliente_centrocosto.id_dp_clientes' => $txtId])
                          ->andwhere("tbl_proceso_cliente_centrocosto.estado = 1")
                          ->count();            

        if ($txtControl > 0) {
          $varListaCiudad = \app\models\ProcesosClienteCentrocosto::find()
                          ->select(['tbl_proceso_cliente_centrocosto.documento_director','tbl_proceso_cliente_centrocosto.director_programa'])->distinct()
                            ->where(['tbl_proceso_cliente_centrocosto.id_dp_clientes' => $txtId])
                            ->andwhere("tbl_proceso_cliente_centrocosto.estado = 1") 
                            ->all();            
          
                    
          foreach ($varListaCiudad as $key => $value) {
            echo "<option value='" . $value->documento_director.";".$txtId. "'>" . $value->director_programa. "</option>";
          }
        }else{
          echo "<option>-</option>";
        }
      }else{
        echo "<option>No hay datos</option>";
      }
    }

    public function actionListargerentes(){
      $txtId = Yii::$app->request->get('id');

      if ($txtId) {
        $txtControl = \app\models\ProcesosClienteCentrocosto::find()->distinct()
                          ->where(['tbl_proceso_cliente_centrocosto.id_dp_clientes' => $txtId])
                          ->andwhere("tbl_proceso_cliente_centrocosto.estado = 1")
                          ->count();            

        if ($txtControl > 0) {
          $varListaCiudad = \app\models\ProcesosClienteCentrocosto::find()
                          ->select(['tbl_proceso_cliente_centrocosto.documento_gerente','tbl_proceso_cliente_centrocosto.gerente_cuenta'])->distinct()
                            ->where(['tbl_proceso_cliente_centrocosto.id_dp_clientes' => $txtId])
                            ->andwhere("tbl_proceso_cliente_centrocosto.estado = 1") 
                            ->all();           
         
                    
          foreach ($varListaCiudad as $key => $value) {
            echo "<option value='" . $value->documento_gerente. "'>" . $value->gerente_cuenta. "</option>";
          }
        }else{
          echo "<option>-</option>";
        }
      }else{
        echo "<option>No hay datos</option>";
      }
    }

    public function actionVerificacedula(){
      $paramsdocumento = [':documento' => Yii::$app->request->get("txtvarididentificacion")];
      $varVerifica = Yii::$app->db->createCommand('
          SELECT COUNT(dp.hv_idpersonal) FROM tbl_hojavida_datapersonal dp 
            WHERE dp.anulado = 0 AND dp.identificacion = :documento')->bindValues($paramsdocumento)->queryScalar();

      die(json_encode($varVerifica));      
    }

    public function actionGuardarpersonal(){
      $txtvaridnombrefull = Yii::$app->request->get("txtvaridnombrefull");
      $txtvarididentificacion = Yii::$app->request->get("txtvarididentificacion");
      $txtvaridemail = Yii::$app->request->get("txtvaridemail");
      $txtvaridnumeromovil = Yii::$app->request->get("txtvaridnumeromovil");
      $txtvaridnumerooficina = Yii::$app->request->get("txtvaridnumerooficina");
      $txtvaridmdoalidad = Yii::$app->request->get("txtvaridmdoalidad");
      $txtvariddireccionoficiona = Yii::$app->request->get("txtvariddireccionoficiona");
      $txtvariddireccioncasa = Yii::$app->request->get("txtvariddireccioncasa");
      $txtvaridautoriza = Yii::$app->request->get("txtvaridautoriza");
      $txtvaridpais = Yii::$app->request->get("txtvaridpais");
      $txtvarididciudad = Yii::$app->request->get("txtvarididciudad");
      $txtvaridsusceptible = Yii::$app->request->get("txtvaridsusceptible");      
      $txtvarclasificacion = Yii::$app->request->get("txtvarclasificacion");
      $txtvaridsatu = Yii::$app->request->get("txtvaridsatu");
      $txtvarfechacumple = Yii::$app->request->get("txtvarfechacumple");

      $txtrta = 0;
      Yii::$app->db->createCommand()->insert('tbl_hojavida_datapersonal',[
                    'nombre_full' => $txtvaridnombrefull,
                    'identificacion' => $txtvarididentificacion,
                    'email' => $txtvaridemail,
                    'numero_movil' => $txtvaridnumeromovil,
                    'numero_fijo' => $txtvaridnumerooficina,
                    'direccion_oficina' => $txtvariddireccionoficiona,
                    'direccion_casa' => $txtvariddireccioncasa,
                    'hv_idpais' => $txtvaridpais,
                    'hv_idciudad' => $txtvarididciudad,
                    'hv_idmodalidad' => $txtvaridmdoalidad,
                    'tratamiento_data' => $txtvaridautoriza,
                    'suceptible' => $txtvaridsusceptible,
                    'indicador_satu' => $txtvaridsatu,
                    'clasificacion' => $txtvarclasificacion,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,  
                    'fechacumple' => $txtvarfechacumple,                                             
                ])->execute();


      $message  = "<html><body>";
      $message .= "<h3>Se ha realizado Correctamente la creacion de un nuevo contacto</h3>";
      $message .= "<p><b>Contacto: </b>".$txtvaridnombrefull."</p>";
      $message .= "<p><b>Identificacion: </b>".$txtvarididentificacion."</p>";
      $message .= "<p><b>Email Corporativo: </b>".$txtvaridemail."</p>";
      $message .= "</body></html>";
        
         
      Yii::$app->mailer->compose()
                ->setTo('engie.guerrero@grupokonecta.com')
                ->setFrom(Yii::$app->params['email_satu_from'])
                ->setSubject("Contacto creado en la Hoja de Vida CXM")
                ->setHtmlBody($message) 
                ->send(); 
      
      die(json_encode($txtrta));
    }
    
    public function actionGuardarlaboral(){
      $txtvarautoincrement = Yii::$app->request->get("txtvarautoincrement");
      $txtvaridrol = Yii::$app->request->get("txtvaridrol");
      $txtvaridantiguedad = Yii::$app->request->get("txtvaridantiguedad");
      $txtvaridfechainicio = Yii::$app->request->get("txtvaridfechainicio");
      $txtvaridnombrejefe = Yii::$app->request->get("txtvaridnombrejefe");
      $txtvaridcargojefe = Yii::$app->request->get("txtvaridcargojefe");
      $txtvaridtrabajoanterior = Yii::$app->request->get("txtvaridtrabajoanterior");
      $txtvaridafinidad = Yii::$app->request->get("txtvaridafinidad");
      $txtvaridtipoafinidad = Yii::$app->request->get("txtvaridtipoafinidad");
      $txtvaridnivelafinidad = Yii::$app->request->get("txtvaridnivelafinidad");
      $txtvaridareatrabajo = Yii::$app->request->get("txtvaridareatrabajo");
      
      $txtrta = 0;      

      Yii::$app->db->createCommand()->insert('tbl_hojavida_datalaboral',[
                    'rol' => $txtvaridrol,
                    'hv_idpersonal' => $txtvarautoincrement,
                    'hv_id_antiguedad' => $txtvaridantiguedad,
                    'fecha_inicio_contacto' => $txtvaridfechainicio,
                    'nombre_jefe' => $txtvaridnombrejefe,
                    'cargo_jefe' => $txtvaridcargojefe,
                    'trabajo_anterior' => $txtvaridtrabajoanterior,
                    'afinidad' => $txtvaridafinidad,
                    'tipo_afinidad' => $txtvaridtipoafinidad,
                    'nivel_afinidad' => $txtvaridnivelafinidad,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,  
                    'areatrabajo' => $txtvaridareatrabajo,                                               
                ])->execute();      

      die(json_encode($txtrta));
    }

    public function actionActualizaacademicos(){
      $txtvarautoincrement = Yii::$app->request->get("txtvarautoincrement");
      $txtvaridestado = Yii::$app->request->get("txtvaridestado");

      $txtrta = 0;

      Yii::$app->db->createCommand()->update('tbl_hojavida_dataacademica',[
                    'activo' => $txtvaridestado,                                                
                ],'hv_idpersonal ='.$txtvarautoincrement.'')->execute();

      die(json_encode($txtrta));

    }

    public function actionGuardaracademicos(){
      $txtvarautoincrement = Yii::$app->request->get("txtvarautoincrement");
      $txtvaridprofesion = Yii::$app->request->get("txtvaridprofesion");
      $txtvaridespecializacion = Yii::$app->request->get("txtvaridespecializacion");
      $txtvaridmaestria = Yii::$app->request->get("txtvaridmaestria");
      $txtvariddoctorado = Yii::$app->request->get("txtvariddoctorado");
      $txtvaridestado = Yii::$app->request->get("txtvaridestado");

      $txtrta = 0;      

      Yii::$app->db->createCommand()->insert('tbl_hojavida_dataacademica',[
                    'hv_idpersonal' => $txtvarautoincrement,
                    'idhvcursosacademico' => $txtvaridprofesion,
                    'activo' => $txtvaridestado,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute(); 

      Yii::$app->db->createCommand()->insert('tbl_hojavida_dataacademica',[
                    'hv_idpersonal' => $txtvarautoincrement,
                    'idhvcursosacademico' => $txtvaridespecializacion,
                    'activo' => $txtvaridestado,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute(); 

      Yii::$app->db->createCommand()->insert('tbl_hojavida_dataacademica',[
                    'hv_idpersonal' => $txtvarautoincrement,
                    'idhvcursosacademico' => $txtvaridmaestria,
                    'activo' => $txtvaridestado,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute(); 

      Yii::$app->db->createCommand()->insert('tbl_hojavida_dataacademica',[
                    'hv_idpersonal' => $txtvarautoincrement,
                    'idhvcursosacademico' => $txtvariddoctorado,
                    'activo' => $txtvaridestado,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute(); 

      die(json_encode($txtrta));
    }

    public function actionAplicareventos(){
      $txtvarautoincrement = Yii::$app->request->get("txtvarautoincrement");
      $txtvarlisteventos = Yii::$app->request->get("txtvarlisteventos");
      $txtrtaEventos = 0;

      $array_eventos = count($txtvarlisteventos);
      for ($i=0; $i < $array_eventos; $i++) { 
        $vareventos = $txtvarlisteventos[$i];

        Yii::$app->db->createCommand()->insert('tbl_hojavida_asignareventos',[
                    'hv_ideventos' => $vareventos,
                    'hv_idpersonal' => $txtvarautoincrement,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute();          
      }       
      
      die(json_encode($txtrtaEventos));
    }

    public function actionGuardarcuentas(){
      $txtvarautoincrement = Yii::$app->request->get("txtvarautoincrement");
      $txtvarid_dp_cliente = Yii::$app->request->get("txtvarid_dp_cliente");
      $txtvaridrequester = Yii::$app->request->get("txtvaridrequester");
      $txtvaridrequester2 = Yii::$app->request->get("txtvaridrequester2");
      $txtvaridrequester3 = Yii::$app->request->get("txtvaridrequester3");
      
      $txtrta = 0;      

      $array_codpcrc = count($txtvaridrequester);
      for ($i=0; $i < $array_codpcrc; $i++) { 
        $varcodpcrc = $txtvaridrequester[$i];

        Yii::$app->db->createCommand()->insert('tbl_hojavida_datapcrc',[
                    'hv_idpersonal' => $txtvarautoincrement,
                    'id_dp_cliente' => $txtvarid_dp_cliente,
                    'cod_pcrc' => $varcodpcrc,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute();  
      }

      $array_director = count($txtvaridrequester2);
      for ($i=0; $i < $array_director; $i++) { 
        $vardirector = $txtvaridrequester2[$i];

        Yii::$app->db->createCommand()->insert('tbl_hojavida_datadirector',[
                    'hv_idpersonal' => $txtvarautoincrement,
                    'ccdirector' => $vardirector,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute(); 
      }

      $array_gerente = count($txtvaridrequester3);
      for ($i=0; $i < $array_gerente; $i++) { 
        $vargerente = $txtvaridrequester3[$i];

        Yii::$app->db->createCommand()->insert('tbl_hojavida_datagerente',[
                    'hv_idpersonal' => $txtvarautoincrement,
                    'ccgerente' => $vargerente,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute();  
      }          

      die(json_encode($txtrta));
    }

    public function actionViewinfo($idinfo){
      $paramsinfo = [':varInfo' => $idinfo];
      $dataProviderInfo = Yii::$app->db->createCommand('
        SELECT 
          dp.hv_idpersonal, dp.nombre_full AS NombreFull, dp.identificacion AS Identificacion,
          dp.email AS Email, dp.numero_movil AS Movil, dp.numero_fijo AS Fijo, dp.direccion_oficina AS DireccioOficina,
          dp.direccion_casa AS DireccionCasa, p.pais AS Pais, c.ciudad AS Ciudad, m.modalidad AS Modalidad,
          if(dp.tratamiento_data = 1,"No","Si") AS TratamientoDatos, if(dp.suceptible = 1,"No","Si") AS Susceptible,
          dp.indicador_satu AS IndicadorSatu, l.rol AS Rol, a.antiguedad AS Antiguedad, l.fecha_inicio_contacto AS FechaContacto,
          l.nombre_jefe AS NombreJefe, l.cargo_jefe AS CargoJefe, l.trabajo_anterior AS TrabajoAnterior,
          if(l.afinidad = 1,"RelaciÃƒÂ³n Directa","RelaciÃƒÂ³n de Interes") AS Afinidad,  dp.clasificacion,
          if(l.tipo_afinidad = 1,"Decisor","No Decisor") AS TipoAfinidad, if(l.nivel_afinidad = 1,"EstratÃƒÂ©gio","Operativo") AS NivelAfinidad,
          pc.id_dp_cliente AS IdCliente, dp.fechacumple, l.areatrabajo
           FROM tbl_hojavida_datapersonal dp
            INNER JOIN tbl_hv_pais p ON 
              dp.hv_idpais = p.hv_idpais
            INNER JOIN tbl_hv_ciudad c ON 
              p.hv_idpais = c.pais_id
            INNER JOIN tbl_hv_modalidad_trabajo m ON 
              dp.hv_idmodalidad = m.hv_idmodalidad
            INNER JOIN tbl_hojavida_datalaboral l ON 
              dp.hv_idpersonal = l.hv_idpersonal
            INNER JOIN tbl_hv_antiguedad_rol a ON 
              l.hv_id_antiguedad = a.hv_id_antiguedad
            INNER JOIN tbl_hojavida_datapcrc pc ON 
              dp.hv_idpersonal = pc.hv_idpersonal
            WHERE 
              dp.hv_idpersonal = :varInfo
            GROUP BY dp.hv_idpersonal               
            ')->bindValues($paramsinfo)->queryAll();

      return $this->render('viewinfo',[
        'dataProviderInfo' => $dataProviderInfo,
      ]);
    }

    public function actionComplementoshv(){
      $model = new HojavidaDatacivil();
      $model1 = new HvDominancias();
      $model2 = new HvEstilosocial();
      $model3 = new HvHobbies();
      $model4 = new HvGustos();
      $model5 = new HojavidaDataclasificacion();

      $dataProviderCivil = HojavidaDatacivil::find()
                            ->asArray()
                            ->all();

      $dataProviderDominancias = HvDominancias::find()
                                  ->asArray()
                                  ->all();

      $dataProviderEstilo = HvEstilosocial::find()
                              ->asArray()
                              ->all();

      $dataProviderHobbies = HvHobbies::find()
                              ->asArray()
                              ->all();

      $dataProvidergustos = HvGustos::find()
                              ->asArray()
                              ->all();

      $dataProviderClasificacion = HojavidaDataclasificacion::find()
                                    ->asArray()
                                    ->all();

      return $this->render('complementoshv',[
        'model' => $model,
        'dataProviderCivil' => $dataProviderCivil,
        'model1' => $model1,
        'dataProviderDominancias' => $dataProviderDominancias,
        'model2' => $model2,
        'dataProviderEstilo' => $dataProviderEstilo,
        'model3' => $model3,
        'dataProviderHobbies' => $dataProviderHobbies,
        'model4' => $model4,
        'dataProvidergustos' => $dataProvidergustos,
        'model5' => $model5,
        'dataProviderClasificacion' => $dataProviderClasificacion,

      ]);
    }

    public function actionIngresarcivil(){
      $txtvaridcivil = Yii::$app->request->get("txtvaridcivil");

      Yii::$app->db->createCommand()->insert('tbl_hojavida_datacivil',[
                    'estadocivil' => $txtvaridcivil,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute(); 

      die(json_encode($txtvaridcivil));

    }

    public function actionIngresardominancia(){
      $txtiddominancia = Yii::$app->request->get("txtiddominancia");

      Yii::$app->db->createCommand()->insert('tbl_hv_dominancias',[
                    'dominancia' => $txtiddominancia,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute(); 

      die(json_encode($txtiddominancia));

    }

    public function actionIngresarestilo(){
      $txtvaridestio = Yii::$app->request->get("txtvaridestio");

      Yii::$app->db->createCommand()->insert('tbl_hv_estilosocial',[
                    'estilosocial' => $txtvaridestio,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute(); 

      die(json_encode($txtvaridestio));

    }

    public function actionIngresarhobbie(){
      $txtvaridhobbies = Yii::$app->request->get("txtvaridhobbies");

      Yii::$app->db->createCommand()->insert('tbl_hv_hobbies',[
                    'text' => $txtvaridhobbies,                                     
                ])->execute(); 

      die(json_encode($txtvaridhobbies));

    }

    public function actionIngresargustos(){
      $txtvaridgustos = Yii::$app->request->get("txtvaridgustos");

      Yii::$app->db->createCommand()->insert('tbl_hv_gustos',[
                    'text' => $txtvaridgustos,                                     
                ])->execute(); 

      die(json_encode($txtvaridgustos));

    }

    public function actionAsignarpermisos(){

      $dataProviderPermisos = HojavidaPermisosacciones::find()
                              ->asArray()
                              ->all();


      return $this->renderAjax('asignarpermisos',[
        'dataProviderPermisos' => $dataProviderPermisos,
      ]);
    }

    public function actionPermisoshv(){
      $model = new HojavidaPermisosacciones(); 
      $model2 = new HojavidaPermisoscliente();
      $varUsuario = null;
      $varNombre = null;

      $form = Yii::$app->request->post();
      if($model->load($form)){

        $varUsuario = $model->usuario_registro;
        $paramsdocumento = [':documento' => $varUsuario];
        $varNombre = Yii::$app->db->createCommand('
          SELECT usua_nombre FROM tbl_usuarios
            WHERE usua_id = :documento
            ')->bindValues($paramsdocumento)->queryScalar();
          
      }else{
        #code
    }

      return $this->render('permisoshv',[
        'model' => $model,
        'model2' => $model2,
        'varUsuario' => $varUsuario,
        'varNombre' => $varNombre,
      ]);
    }

    public function actionEliminarpermisos($id) {      

      $paramsdocumento = [':documento' => $id];
      $idDoslist = Yii::$app->db->createCommand('
          SELECT p.hv_idpermisocliente FROM tbl_hojavida_permisosacciones h 
            INNER JOIN tbl_hojavida_permisoscliente p ON 
              h.usuario_registro = p.usuario_registro
            WHERE h.hv_idacciones = :documento
            ')->bindValues($paramsdocumento)->queryAll();

      if (count($idDoslist) != 0) {
        foreach ($idDoslist as $key => $value) {
          $idDos = $value['hv_idpermisocliente'];
          $this->findModelDos($idDos)->delete(); 
        }        
      }

      $this->findModel($id)->delete(); 
           
      return $this->redirect(['index']);
    }

    public function actionEliminarservicio($idDos,$id){
      $this->findModelDos($idDos)->delete();

      return $this->redirect(['editarpermisos','id'=>$id]);
    }

    public function actionEditarpermisos($id) {
      $idacciones = $id;
      $model = $this->findModel($id);

      $paramsdocumento = [':idaccion' => $id];
      $varNombre = Yii::$app->db->createCommand('
          SELECT u.usua_nombre FROM tbl_usuarios u
            INNER JOIN tbl_hojavida_permisosacciones hp ON 
              u.usua_id = hp.usuario_registro
            WHERE hp.hv_idacciones = :idaccion
            ')->bindValues($paramsdocumento)->queryScalar();

      $varUsuario = Yii::$app->db->createCommand('
          SELECT hp.usuario_registro FROM tbl_hojavida_permisosacciones hp 
            WHERE hp.hv_idacciones = :idaccion
            ')->bindValues($paramsdocumento)->queryScalar();

      $model2 = new HojavidaPermisoscliente();

      $dataProviderClientes = Yii::$app->db->createCommand('
          SELECT hp.hv_idpermisocliente, pc.cliente FROM tbl_proceso_cliente_centrocosto pc
            INNER JOIN tbl_hojavida_permisoscliente hp ON 
              pc.id_dp_clientes = hp.id_dp_clientes
            INNER JOIN tbl_hojavida_permisosacciones pa ON 
              hp.usuario_registro = pa.usuario_registro
                WHERE pa.hv_idacciones = :idaccion
             GROUP BY pc.id_dp_clientes
            ')->bindValues($paramsdocumento)->queryAll();

      if ($model->load(Yii::$app->request->post()) && $model->save()) {
        return $this->render('editarpermisos',[
          'model' => $model,
          'varNombre' => $varNombre,
          'varUsuario' => $varUsuario,
          'model2' => $model2,
          'dataProviderClientes' => $dataProviderClientes,
          'idacciones' => $idacciones,
        ]);
      }


      return $this->render('editarpermisos',[
        'model' => $model,
        'varNombre' => $varNombre,
        'varUsuario' => $varUsuario,
        'model2' => $model2,
        'dataProviderClientes' => $dataProviderClientes,
        'idacciones' => $idacciones,
      ]);
    }

    protected function findModel($id) {
      if (($model = HojavidaPermisosacciones::findOne($id)) !== null) {
        return $model;
      } else {
        throw new NotFoundHttpException('El resultado de la bus no existe.');
      }
    }

    protected function findModelDos($idDos) {
      if (($model = HojavidaPermisoscliente::findOne($idDos)) !== null) {
        return $model;
      } else {
        throw new NotFoundHttpException('El resultado de la bus no existe.');
      }
    }

    public function actionPermisosaccion(){
      $txtvarideliminar = Yii::$app->request->get("txtvarideliminar");
      $txtvarideditar = Yii::$app->request->get("txtvarideditar");
      $txtvaridmasiva = Yii::$app->request->get("txtvaridmasiva");
      $txtvariddata = Yii::$app->request->get("txtvariddata");
      $txtvaridver = Yii::$app->request->get("txtvaridver");
      $txtvariduser = Yii::$app->request->get("txtvariduser");
      $txtvarverdata = Yii::$app->request->get("txtvarverdata");

      
        Yii::$app->db->createCommand()->insert('tbl_hojavida_permisosacciones',[
                    'usuario_registro' => $txtvariduser,   
                    'hveliminar' => $txtvarideliminar,
                    'hveditar' => $txtvarideditar,
                    'hvcasrgamasiva' => $txtvaridmasiva,
                    'hvdatapersonal' => $txtvariddata,
                    'hvverresumen' => $txtvaridver,
                    'hvverdata' => $txtvarverdata,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                  
                ])->execute();      

      die(json_encode($txtvarguardarpermisos));
    }

    public function actionPermisosaccioncliente(){
      $txtvaridservicio = Yii::$app->request->get("txtvaridservicio");
      $txtvariduser = Yii::$app->request->get("txtvariduser");

      $array_idclientes = count($txtvaridservicio);
      for ($i=0; $i < $array_idclientes; $i++) { 
        $variddpcliente = $txtvaridservicio[$i];

        Yii::$app->db->createCommand()->insert('tbl_hojavida_permisoscliente',[
                    'usuario_registro' => $txtvariduser,  
                    'id_dp_clientes' => $variddpcliente,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                  
        ])->execute();
      }       

      die(json_encode($txtvariduser));
    }

    public function actionCreatedservicio($idusuario,$idaccion){
      $model2 = new HojavidaPermisoscliente();
      $varidusua = $idusuario;

      $form = Yii::$app->request->post();
      if($model2->load($form)){
        
        $array_idclientes = count($model2->id_dp_clientes);
        for ($i=0; $i < $array_idclientes; $i++) { 
          $variddpcliente = $model2->id_dp_clientes[$i];

          Yii::$app->db->createCommand()->insert('tbl_hojavida_permisoscliente',[
                      'usuario_registro' => $varidusua,  
                      'id_dp_clientes' => $variddpcliente,
                      'fechacreacion' => date('Y-m-d'),
                      'anulado' => 0,
                      'usua_id' => Yii::$app->user->identity->id,                                  
          ])->execute();

          Yii::$app->db->createCommand()->insert('tbl_logs', [
            'usua_id' => Yii::$app->user->identity->id,
            'usuario' => Yii::$app->user->identity->username,
            'fechahora' => date('Y-m-d h:i:s'),
            'ip' => Yii::$app->getRequest()->getUserIP(),
            'accion' => 'Create',
            'tabla' => 'tbl_hojavida_permisoscliente'
          ])->execute();
        }    

        return $this->redirect(array('editarpermisos','id'=>$idaccion));
      }else{
        #code
    }

      return $this->renderAjax('createdservicio',[
        'model2' => $model2,
        'varidusua' => $varidusua,
      ]);
    }

    public function actionDeleteinfo($idinfo){
      Yii::$app->db->createCommand()->update('tbl_hojavida_asignareventos',[
          'anulado' => 1,
      ],'hv_idpersonal ='.$idinfo.'')->execute();  

      Yii::$app->db->createCommand()->insert('tbl_logs', [
        'usua_id' => Yii::$app->user->identity->id,
        'usuario' => Yii::$app->user->identity->username,
        'fechahora' => date('Y-m-d h:i:s'),
        'ip' => Yii::$app->getRequest()->getUserIP(),
        'accion' => 'Update',
        'tabla' => 'tbl_hojavida_asignareventos'
      ])->execute();

      Yii::$app->db->createCommand()->update('tbl_hojavida_dataacademica',[
          'anulado' => 1,
      ],'hv_idpersonal ='.$idinfo.'')->execute();  

      Yii::$app->db->createCommand()->insert('tbl_logs', [
        'usua_id' => Yii::$app->user->identity->id,
        'usuario' => Yii::$app->user->identity->username,
        'fechahora' => date('Y-m-d h:i:s'),
        'ip' => Yii::$app->getRequest()->getUserIP(),
        'accion' => 'Update',
        'tabla' => 'tbl_hojavida_dataacademica'
      ])->execute();

      Yii::$app->db->createCommand()->update('tbl_hojavida_datadirector',[
          'anulado' => 1,
      ],'hv_idpersonal ='.$idinfo.'')->execute(); 

      Yii::$app->db->createCommand()->insert('tbl_logs', [
        'usua_id' => Yii::$app->user->identity->id,
        'usuario' => Yii::$app->user->identity->username,
        'fechahora' => date('Y-m-d h:i:s'),
        'ip' => Yii::$app->getRequest()->getUserIP(),
        'accion' => 'Update',
        'tabla' => 'tbl_hojavida_datadirector'
      ])->execute();

      Yii::$app->db->createCommand()->update('tbl_hojavida_datagerente',[
          'anulado' => 1,
      ],'hv_idpersonal ='.$idinfo.'')->execute(); 
      
      Yii::$app->db->createCommand()->insert('tbl_logs', [
        'usua_id' => Yii::$app->user->identity->id,
        'usuario' => Yii::$app->user->identity->username,
        'fechahora' => date('Y-m-d h:i:s'),
        'ip' => Yii::$app->getRequest()->getUserIP(),
        'accion' => 'Update',
        'tabla' => 'tbl_hojavida_datagerente'
      ])->execute();

      Yii::$app->db->createCommand()->update('tbl_hojavida_datalaboral',[
          'anulado' => 1,
      ],'hv_idpersonal ='.$idinfo.'')->execute();
      
      Yii::$app->db->createCommand()->insert('tbl_logs', [
        'usua_id' => Yii::$app->user->identity->id,
        'usuario' => Yii::$app->user->identity->username,
        'fechahora' => date('Y-m-d h:i:s'),
        'ip' => Yii::$app->getRequest()->getUserIP(),
        'accion' => 'Update',
        'tabla' => 'tbl_hojavida_datalaboral'
      ])->execute();

      Yii::$app->db->createCommand()->update('tbl_hojavida_datapcrc',[
          'anulado' => 1,
      ],'hv_idpersonal ='.$idinfo.'')->execute();
      
      Yii::$app->db->createCommand()->insert('tbl_logs', [
        'usua_id' => Yii::$app->user->identity->id,
        'usuario' => Yii::$app->user->identity->username,
        'fechahora' => date('Y-m-d h:i:s'),
        'ip' => Yii::$app->getRequest()->getUserIP(),
        'accion' => 'Update',
        'tabla' => 'tbl_hojavida_datapcrc'
      ])->execute();

      Yii::$app->db->createCommand()->update('tbl_hojavida_datapersonal',[
          'anulado' => 1,
      ],'hv_idpersonal ='.$idinfo.'')->execute();

      Yii::$app->db->createCommand()->insert('tbl_logs', [
        'usua_id' => Yii::$app->user->identity->id,
        'usuario' => Yii::$app->user->identity->username,
        'fechahora' => date('Y-m-d h:i:s'),
        'ip' => Yii::$app->getRequest()->getUserIP(),
        'accion' => 'Update',
        'tabla' => 'tbl_hojavida_datapersonal'
      ])->execute();

      return $this->redirect('index');
    }

    public function actionEditinfo($idinfo){
      $model = HojavidaDatapersonal::findOne($idinfo);      
      $paramsmodel2 = [':idinfos'=>$idinfo];
      $varinfolaboral = Yii::$app->db->createCommand('
        SELECT dl.hv_idlaboral FROM tbl_hojavida_datalaboral dl
        WHERE 
          dl.hv_idpersonal = :idinfos
            GROUP BY dl.hv_idlaboral
            ')->bindValues($paramsmodel2)->queryScalar();
            
      $varActivo = (new \yii\db\Query())
            ->select(['activo'])
            ->from(['tbl_hojavida_dataacademica'])
            ->where(['=','anulado',0])
            ->andwhere(['=','hv_idpersonal',$idinfo])
            ->groupby(['activo'])
            ->Scalar();

      $model2 = HojavidaDatalaboral::findOne($varinfolaboral);
      $model3 = new HojavidaDataacademica();
      $model4 = new HojavidaDatapcrc();
      $model5 = new HojavidaDatadirector();
      $model6 = new HojavidaDatagerente();
      $model7 = new HojavidaEventos();


      return $this->render('editinfo',[
        'model' => $model,
        'model2' => $model2,
        'model3' => $model3,
        'model4' => $model4,
        'model5' => $model5,
        'model6' => $model6,
        'model7' => $model7,
        'idinfo' => $idinfo,
        'varActivo' => $varActivo,
      ]);
    }

    public function actionDeletepcrc($id,$idsinfo){
      HojavidaDatapcrc::findOne($id)->delete();

      Yii::$app->db->createCommand()->insert('tbl_logs', [
        'usua_id' => Yii::$app->user->identity->id,
        'usuario' => Yii::$app->user->identity->username,
        'fechahora' => date('Y-m-d h:i:s'),
        'ip' => Yii::$app->getRequest()->getUserIP(),
        'accion' => 'Delete',
        'tabla' => 'tbl_hojavida_datapcrc'
      ])->execute();

      return $this->redirect(['editinfo','idinfo'=>$idsinfo]);
    }

    public function actionDeletedirector($id,$idsinfo){
      HojavidaDatadirector::findOne($id)->delete();

      Yii::$app->db->createCommand()->insert('tbl_logs', [
        'usua_id' => Yii::$app->user->identity->id,
        'usuario' => Yii::$app->user->identity->username,
        'fechahora' => date('Y-m-d h:i:s'),
        'ip' => Yii::$app->getRequest()->getUserIP(),
        'accion' => 'Delete',
        'tabla' => 'tbl_hojavida_datadirector'
      ])->execute();

      return $this->redirect(['editinfo','idinfo'=>$idsinfo]);
    }

    public function actionDeletegerente($id,$idsinfo){
      HojavidaDatagerente::findOne($id)->delete();

      Yii::$app->db->createCommand()->insert('tbl_logs', [
        'usua_id' => Yii::$app->user->identity->id,
        'usuario' => Yii::$app->user->identity->username,
        'fechahora' => date('Y-m-d h:i:s'),
        'ip' => Yii::$app->getRequest()->getUserIP(),
        'accion' => 'Delete',
        'tabla' => 'tbl_hojavida_datagerente'
      ])->execute();

      return $this->redirect(['editinfo','idinfo'=>$idsinfo]);
    }

    public function actionDeleteacademico($id,$idsinfo){
      HojavidaDataacademica::findOne($id)->delete();

      Yii::$app->db->createCommand()->insert('tbl_logs', [
        'usua_id' => Yii::$app->user->identity->id,
        'usuario' => Yii::$app->user->identity->username,
        'fechahora' => date('Y-m-d h:i:s'),
        'ip' => Yii::$app->getRequest()->getUserIP(),
        'accion' => 'Delete',
        'tabla' => 'tbl_hojavida_dataacademica'
      ])->execute();

      return $this->redirect(['editinfo','idinfo'=>$idsinfo]);
    }

    public function actionDeleteeventos($id,$idsinfo){
      HojavidaEventos::findOne($id)->delete();
      Yii::$app->db->createCommand()->insert('tbl_logs', [
        'usua_id' => Yii::$app->user->identity->id,
        'usuario' => Yii::$app->user->identity->username,
        'fechahora' => date('Y-m-d h:i:s'),
        'ip' => Yii::$app->getRequest()->getUserIP(),
        'accion' => 'Delete',
        'tabla' => 'tbl_hojavida_eventos'
      ])->execute();

      return $this->redirect(['editinfo','idinfo'=>$idsinfo]);
    }

    public function actionDeletecomplementos($id,$idsinfo){
      HojavidaDatacomplementos::findOne($id)->delete();
      Yii::$app->db->createCommand()->insert('tbl_logs', [
        'usua_id' => Yii::$app->user->identity->id,
        'usuario' => Yii::$app->user->identity->username,
        'fechahora' => date('Y-m-d h:i:s'),
        'ip' => Yii::$app->getRequest()->getUserIP(),
        'accion' => 'Delete',
        'tabla' => 'tbl_hojavida_datacomplementos'
      ])->execute();

      return $this->redirect(['editinfo','idinfo'=>$idsinfo]);
    }

    public function actionComplementosaccion($idsinfo){
      $model = new HojavidaDatacomplementos();

      if ($model->load(Yii::$app->request->post()) && $model->save()) {
        return $this->redirect(['editinfo','idinfo'=>$idsinfo]);
      }

      return $this->renderAjax('complementosaccion',[
        'model' => $model,
        'idsinfo' => $idsinfo,
      ]);
    }

    public function actionActualizapersonal(){
      $txtvarautoincrement = Yii::$app->request->get("txtvarautoincrement");
      $txtvaridnombrefull = Yii::$app->request->get("txtvaridnombrefull");
      $txtvarididentificacion = Yii::$app->request->get("txtvarididentificacion");
      $txtvaridemail = Yii::$app->request->get("txtvaridemail");
      $txtvaridnumeromovil = Yii::$app->request->get("txtvaridnumeromovil");
      $txtvaridnumerooficina = Yii::$app->request->get("txtvaridnumerooficina");
      $txtvaridmdoalidad = Yii::$app->request->get("txtvaridmdoalidad");
      $txtvariddireccionoficiona = Yii::$app->request->get("txtvariddireccionoficiona");
      $txtvariddireccioncasa = Yii::$app->request->get("txtvariddireccioncasa");
      $txtvaridautoriza = Yii::$app->request->get("txtvaridautoriza");
      $txtvaridpais = Yii::$app->request->get("txtvaridpais");
      $txtvarididciudad = Yii::$app->request->get("txtvarididciudad");
      $txtvaridsusceptible = Yii::$app->request->get("txtvaridsusceptible");
      $txtvaridsatu = Yii::$app->request->get("txtvaridsatu");
      $txtvarclasificacion = Yii::$app->request->get("txtvarclasificacion");
      $txtvarfechacumple = Yii::$app->request->get("txtvarfechacumple");

      $txtrta = 0;
      

      Yii::$app->db->createCommand()->update('tbl_hojavida_datapersonal',[
                    'nombre_full' => $txtvaridnombrefull,
                    'identificacion' => $txtvarididentificacion,
                    'email' => $txtvaridemail,
                    'numero_movil' => $txtvaridnumeromovil,
                    'numero_fijo' => $txtvaridnumerooficina,
                    'direccion_oficina' => $txtvariddireccionoficiona,
                    'direccion_casa' => $txtvariddireccioncasa,
                    'hv_idpais' => $txtvaridpais,
                    'hv_idciudad' => $txtvarididciudad,
                    'hv_idmodalidad' => $txtvaridmdoalidad,
                    'tratamiento_data' => $txtvaridautoriza,
                    'suceptible' => $txtvaridsusceptible,
                    'indicador_satu' => $txtvaridsatu,
                    'clasificacion' => $txtvarclasificacion,
                    'fechacumple' => $txtvarfechacumple,                                                 
                ],'hv_idpersonal ='.$txtvarautoincrement.'')->execute();

      
      die(json_encode($txtrta));
    }
    
    public function actionActualizalaboral(){
      $txtvarautoincrement = Yii::$app->request->get("txtvarautoincrement");
      $txtvaridrol = Yii::$app->request->get("txtvaridrol");
      $txtvaridantiguedad = Yii::$app->request->get("txtvaridantiguedad");
      $txtvaridfechainicio = Yii::$app->request->get("txtvaridfechainicio");
      $txtvaridnombrejefe = Yii::$app->request->get("txtvaridnombrejefe");
      $txtvaridcargojefe = Yii::$app->request->get("txtvaridcargojefe");
      $txtvaridtrabajoanterior = Yii::$app->request->get("txtvaridtrabajoanterior");
      $txtvaridafinidad = Yii::$app->request->get("txtvaridafinidad");
      $txtvaridtipoafinidad = Yii::$app->request->get("txtvaridtipoafinidad");
      $txtvaridnivelafinidad = Yii::$app->request->get("txtvaridnivelafinidad");
      $txtvaridareatrabajo = Yii::$app->request->get("txtvaridareatrabajo");
      
      $txtrta = 0;      

      Yii::$app->db->createCommand()->update('tbl_hojavida_datalaboral',[
                    'rol' => $txtvaridrol,
                    'hv_id_antiguedad' => $txtvaridantiguedad,
                    'fecha_inicio_contacto' => $txtvaridfechainicio,
                    'nombre_jefe' => $txtvaridnombrejefe,
                    'cargo_jefe' => $txtvaridcargojefe,
                    'trabajo_anterior' => $txtvaridtrabajoanterior,
                    'afinidad' => $txtvaridafinidad,
                    'tipo_afinidad' => $txtvaridtipoafinidad,
                    'nivel_afinidad' => $txtvaridnivelafinidad,  
                    'areatrabajo' => $txtvaridareatrabajo,                                            
                ],'hv_idpersonal ='.$txtvarautoincrement.'')->execute();      

      die(json_encode($txtrta));
    }


    public function actionActualizacuentas(){
      $txtvarautoincrement = Yii::$app->request->get("txtvarautoincrement");
      $txtvarid_dp_cliente = Yii::$app->request->get("txtvarid_dp_cliente");
      $txtvaridrequester = Yii::$app->request->get("txtvaridrequester");
      $txtvaridrequester2 = Yii::$app->request->get("txtvaridrequester2");
      $txtvaridrequester3 = Yii::$app->request->get("txtvaridrequester3");
      
      $txtrta = 0;      

      $array_codpcrc = count($txtvaridrequester);
      for ($i=0; $i < $array_codpcrc; $i++) { 
        $varcodpcrc = $txtvaridrequester[$i];

        $paramsAutopcrc = [':VarAuto'=>$txtvarautoincrement];
        $varVerificarAutopcrc = Yii::$app->db->createCommand('
          SELECT COUNT(hd.hv_idpcrc) FROM tbl_hojavida_datapcrc hd 
            WHERE hd.hv_idpersonal = :VarAuto
            ')->bindValues($paramsAutopcrc)->queryScalar();
        
        if ($varVerificarAutopcrc != "0") {
          Yii::$app->db->createCommand()->update('tbl_hojavida_datapcrc',[
            'id_dp_cliente' => $txtvarid_dp_cliente,
            'cod_pcrc' => $varcodpcrc,                                       
          ],'hv_idpersonal ='.$txtvarautoincrement.'')->execute();
        }else{
          Yii::$app->db->createCommand()->insert('tbl_hojavida_datapcrc',[
            'id_dp_cliente' => $txtvarid_dp_cliente,
            'cod_pcrc' => $varcodpcrc,                                       
          ],'hv_idpersonal ='.$txtvarautoincrement.'')->execute();
        }

          
      }

      $array_director = count($txtvaridrequester2);
      for ($i=0; $i < $array_director; $i++) { 
        $vardirector = $txtvaridrequester2[$i];

        $paramsAutoDirector = [':VarAuto'=>$txtvarautoincrement];
        $varVerificarAutoDirector = Yii::$app->db->createCommand('
          SELECT COUNT(hd.hv_iddirector) FROM tbl_hojavida_datadirector hd 
            WHERE hd.hv_idpersonal = :VarAuto
            ')->bindValues($paramsAutoDirector)->queryScalar();

        if ($varVerificarAutoDirector != "0") {
          Yii::$app->db->createCommand()->update('tbl_hojavida_datadirector',[
            'ccdirector' => $vardirector,                                      
          ],'hv_idpersonal ='.$txtvarautoincrement.'')->execute(); 
        }else{
          Yii::$app->db->createCommand()->insert('tbl_hojavida_datadirector',[
            'hv_idpersonal' => $txtvarautoincrement,
            'ccdirector' => $vardirector,       
            'anulado' => 0,
            'fechacreacion' => date('Y-m-d'), 
            'usua_id' => Yii::$app->user->identity->id,                     
          ],'hv_idpersonal ='.$txtvarautoincrement.'')->execute(); 
        }

      }

      $array_gerente = count($txtvaridrequester3);
      for ($i=0; $i < $array_gerente; $i++) { 
        $vargerente = $txtvaridrequester3[$i];

        $paramsAutoGerente = [':VarAuto'=>$txtvarautoincrement];
        $varVerificarAutoGerente = Yii::$app->db->createCommand('
          SELECT COUNT(hd.hv_idgerente) FROM tbl_hojavida_datagerente hd 
            WHERE hd.hv_idpersonal = :VarAuto
            ')->bindValues($paramsAutoGerente)->queryScalar();

        if ($varVerificarAutoGerente != "0") {
          Yii::$app->db->createCommand()->update('tbl_hojavida_datagerente',[
            'ccgerente' => $vargerente,                                       
          ],'hv_idpersonal ='.$txtvarautoincrement.'')->execute();
        }else{
          Yii::$app->db->createCommand()->insert('tbl_hojavida_datagerente',[
            'hv_idpersonal' => $txtvarautoincrement,
            'ccgerente' => $vargerente,       
            'anulado' => 0,
            'fechacreacion' => date('Y-m-d'), 
            'usua_id' => Yii::$app->user->identity->id,                                
          ],'hv_idpersonal ='.$txtvarautoincrement.'')->execute(); 
        }

          
      }          

      die(json_encode($txtrta));
    }

    public function actionEditcomplementos($id,$idsinfo){
      $model = HojavidaDatacomplementos::findOne($id);

      if ($model->load(Yii::$app->request->post()) && $model->save()) {
        
        $varcivil = $model->hv_idcivil;
        Yii::$app->db->createCommand()->update('tbl_hojavida_datacomplementos',[
                    'hv_idcivil' => $varcivil,                                      
                ],'hv_idpersonal ='.$idsinfo.'')->execute(); 

        return $this->redirect(['editinfo','idinfo'=>$idsinfo]);
      }

      return $this->render('editcomplementos',[
        'model' => $model,
        'idsinfo' => $idsinfo,
      ]);
    }
 
    public function actionComplementosadd($idsinfo){
      $model = new HojavidaDatacomplementos();      

      if ($model->load(Yii::$app->request->post()) && $model->save()) {
        return $this->redirect(['editinfo','idinfo'=>$idsinfo]);
      }

      $paramscomplement = [':idhvaccion' => $idsinfo];
      $varCivil = Yii::$app->db->createCommand('
        SELECT dc.hv_idcivil from tbl_hojavida_datacomplementos dc
          WHERE 
            dc.hv_idpersonal = :idhvaccion
          GROUP BY dc.hv_idcivil')->bindValues($paramscomplement)->queryScalar();

      return $this->render('complementosadd',[
        'model' => $model,
        'idsinfo' => $idsinfo,
        'varCivil' => $varCivil,
      ]);
    }

    

    public function actionEliminarcivil($id){
      HojavidaDatacivil::findOne($id)->delete();

      return $this->redirect(['complementoshv']);
    }

    public function actionEliminardominancia($id){
      HvDominancias::findOne($id)->delete();

      return $this->redirect(['complementoshv']);
    }

    public function actionEliminarsocial($id){
      HvEstilosocial::findOne($id)->delete();

      return $this->redirect(['complementoshv']);
    }

    public function actionEliminarhobbie($id){
      HvHobbies::findOne($id)->delete();

      return $this->redirect(['complementoshv']);
    }

    public function actionEliminargustos($id){
      HvGustos::findOne($id)->delete();

      return $this->redirect(['complementoshv']);
    }

    public function actionEliminarclasificacion($id){
      HojavidaDataclasificacion::findOne($id)->delete();

      return $this->redirect(['complementoshv']);
    }

    public function actionTiposeventos(){
     $model = new HojavidaTipoeventos();
 
     $form = Yii::$app->request->post();
     if($model->load($form)){
       Yii::$app->db->createCommand()->insert('tbl_hojavida_tipoeventos',[
                     'tipoeventos' => $model->tipoeventos,  
                     'fechacreacion' => date('Y-m-d'),
                     'anulado' => 0,
                     'usua_id' => Yii::$app->user->identity->id,                                  
         ])->execute();
 
       return $this->redirect(['eventos']);
     }else{
      #code
  }
 
     return $this->renderAjax('tiposeventos',[
       'model' => $model,
     ]);
    }

    public function actionVerficarConnid(){
      $varConnid = Yii::$app->db->createCommand("
        SELECT * FROM tbl_base_satisfaccion b WHERE b.fecha_satu BETWEEN '2021-10-01 00:00:00' AND '2021-10-31 23:59:59'
        AND b.pcrc = 2727
          AND b.id = 14930572 ")->queryAll();

      foreach ($varConnid as $key => $value) {

        if (is_null($value['buzon']) || empty($value['buzon']) || $value['buzon'] == "") {
          var_dump("No tiene buzon en BD");
          $varBuzon = $this->_buscarArchivoBuzon(
                                  sprintf("%02s", $value['dia']) . "_" . sprintf("%02s", $value['mes']) . "_" . $value['ano'], $value['connid']);

          var_dump('Resultado: '.$varBuzon);
          
        }else{
          $varBuzon = $this->_buscarArchivoBuzon(
                                  sprintf("%02s", $value['dia']) . "_" . sprintf("%02s", $value['mes']) . "_" . $value['ano'], $value['connid']);
          var_dump('Resultado: '.$varBuzon);
          var_dump("No Consulto");
        }

      }
   
    }

    private function _buscarArchivoBuzon($fechaEncuesta, $connId) {
                $output = NULL;
                try {
                    $rutaPrincipalBuzonesLlamadas = \Yii::$app->params["ruta_buzon"];                    
                    $command = "find {$rutaPrincipalBuzonesLlamadas}/Buzones_{$fechaEncuesta} -iname *{$connId}*.wav";
                    \Yii::error("COMANDO BUZON: " . $command, 'basesatisfaccion');
                    
                    file_put_contents("A.TXT", $command);

                    $output = exec($command);
                    var_dump('Resultado funcion: '.$output);
                } catch (\yii\base\Exception $exc) {
                    \Yii::error($exc->getTraceAsString(), 'basesatisfaccion');
                    return $output;
                }
                return $output;
    }

    public function actionCrearmodalidad(){
      $modalidad = Yii::$app->db->createCommand('select * from tbl_hv_modalidad_trabajo')->queryAll();
      return $this->render('modalidad',[ "modalidad"=> $modalidad ]);
    }

    public function actionGuardarmodalidad(){
       Yii::$app->db->createCommand()->insert('tbl_hv_modalidad_trabajo',[
           "modalidad"=>Yii::$app->request->post('modalidad'),
           "usua_id"  =>Yii::$app->user->identity->id
       ])->execute();

       Yii::$app->db->createCommand()->insert('tbl_logs', [
        'usua_id' => Yii::$app->user->identity->id,
        'usuario' => Yii::$app->user->identity->username,
        'fechahora' => date('Y-m-d h:i:s'),
        'ip' => Yii::$app->getRequest()->getUserIP(),
        'accion' => 'Create',
        'tabla' => 'tbl_hv_modalidad_trabajo'
      ])->execute();

       Yii::$app->session->setFlash('info','MODALIDAD CREADA EXITOSAMENTE');
       return $this->redirect(["crearmodalidad"]);
    }

    public function actionEliminarmodalidad($id){
      Yii::$app->db->createCommand('DELETE FROM tbl_hv_modalidad_trabajo WHERE hv_idmodalidad=:id')->bindParam(':id',$id)->execute();

      Yii::$app->db->createCommand()->insert('tbl_logs', [
        'usua_id' => Yii::$app->user->identity->id,
        'usuario' => Yii::$app->user->identity->username,
        'fechahora' => date('Y-m-d h:i:s'),
        'ip' => Yii::$app->getRequest()->getUserIP(),
        'accion' => 'Delete',
        'tabla' => 'tbl_hv_modalidad_trabajo'
      ])->execute();

      Yii::$app->session->setFlash('info','MODALIDAD ELIMINADA CORRECTAMENTE');
      return $this->redirect(["crearmodalidad"]);
    }

    public function actionResumen($id){

        $sessiones = Yii::$app->user->identity->id;
        $rol =  new Query;
        $rol     ->select(['tbl_roles.role_id'])
                  ->from('tbl_roles')
                  ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                              'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                  ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                              'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                  ->where('tbl_usuarios.usua_id = :sessiones')
                  ->addParams([':sessiones'=>$sessiones]);                    
        $command = $rol->createCommand();
        $roles = $command->queryScalar();


        $clientsTotalBogota   = Yii::$app->db->createCommand("SELECT COUNT( i.hv_idpersonal) AS total FROM tbl_hojavida_datapersonal i where clasificacion= 1 ")->queryAll();
        $clientsTotalMedellin = Yii::$app->db->createCommand("SELECT COUNT( i.hv_idpersonal) AS total FROM tbl_hojavida_datapersonal i where clasificacion= 2 ")->queryAll();
        $clientsTotalAdmin    = Yii::$app->db->createCommand('SELECT COUNT(i.hv_idpersonal) AS total FROM tbl_hojavida_datapersonal i')->queryAll();
        
        $clientDecisorBogota   = Yii::$app->db->createCommand("SELECT COUNT(*) AS total FROM tbl_hojavida_datapersonal p 
        INNER JOIN tbl_hojavida_datalaboral l
        ON p.hv_idpersonal = l.hv_idpersonal
        INNER JOIN tbl_hojavida_datatipoafinidad t
        ON l.tipo_afinidad = t.hv_idtipoafinidad
        WHERE t.tipoafinidad='Decisor' AND p.clasificacion = 1")->queryAll();

        $clientDecisorMedellin = Yii::$app->db->createCommand("SELECT COUNT(*) AS total FROM tbl_hojavida_datapersonal p 
        INNER JOIN tbl_hojavida_datalaboral l
        ON p.hv_idpersonal = l.hv_idpersonal
        INNER JOIN tbl_hojavida_datatipoafinidad t
        ON l.tipo_afinidad = t.hv_idtipoafinidad
        WHERE t.tipoafinidad='Decisor' AND p.clasificacion = 2")->queryAll();

        $clientDecisor    = Yii::$app->db->createCommand("SELECT COUNT(*) AS total FROM tbl_hojavida_datapersonal p 
        INNER JOIN tbl_hojavida_datalaboral l
        ON p.hv_idpersonal = l.hv_idpersonal
        INNER JOIN tbl_hojavida_datatipoafinidad t
        ON l.tipo_afinidad = t.hv_idtipoafinidad
        WHERE t.tipoafinidad='Decisor' ")->queryAll();



        $clientEstrategicoBogota   = Yii::$app->db->createCommand("SELECT COUNT(*) AS total FROM tbl_hojavida_datapersonal p 
        INNER JOIN tbl_hojavida_datalaboral l
        ON p.hv_idpersonal = l.hv_idpersonal
        INNER JOIN tbl_hojavida_datanivelafinidad n
        ON n.hv_idinvelafinidad = l.nivel_afinidad
        WHERE n.nivelafinidad='EstratÃƒÂ©gico' AND p.clasificacion = 1")->queryAll();

        $clientEstrategicoMedellin = Yii::$app->db->createCommand("SELECT COUNT(*) AS total FROM tbl_hojavida_datapersonal p 
        INNER JOIN tbl_hojavida_datalaboral l
        ON p.hv_idpersonal = l.hv_idpersonal
        INNER JOIN tbl_hojavida_datanivelafinidad n
        ON n.hv_idinvelafinidad = l.nivel_afinidad
        WHERE n.nivelafinidad='EstratÃƒÂ©gico' AND p.clasificacion = 2")->queryAll();

        $clientEstrategico   = Yii::$app->db->createCommand("SELECT COUNT(*) AS total FROM tbl_hojavida_datapersonal p 
        INNER JOIN tbl_hojavida_datalaboral l
        ON p.hv_idpersonal = l.hv_idpersonal
        INNER JOIN tbl_hojavida_datanivelafinidad n
        ON n.hv_idinvelafinidad = l.nivel_afinidad
        WHERE n.nivelafinidad='EstratÃƒÂ©gico' ")->queryAll();



        $clientOperativoBogota   = Yii::$app->db->createCommand("SELECT COUNT(*) AS total FROM tbl_hojavida_datapersonal p 
        INNER JOIN tbl_hojavida_datalaboral l
        ON p.hv_idpersonal = l.hv_idpersonal
        INNER JOIN tbl_hojavida_datanivelafinidad n
        ON n.hv_idinvelafinidad = l.nivel_afinidad
        WHERE n.nivelafinidad='Operativo' AND p.clasificacion = 1")->queryAll();

        $clientOperativoMedellin = Yii::$app->db->createCommand("SELECT COUNT(*) AS total FROM tbl_hojavida_datapersonal p 
        INNER JOIN tbl_hojavida_datalaboral l
        ON p.hv_idpersonal = l.hv_idpersonal
        INNER JOIN tbl_hojavida_datanivelafinidad n
        ON n.hv_idinvelafinidad = l.nivel_afinidad
        WHERE n.nivelafinidad='Operativo' AND p.clasificacion = 2")->queryAll();

        $clienOperativo   = Yii::$app->db->createCommand("SELECT COUNT(*) AS total FROM tbl_hojavida_datapersonal p 
        INNER JOIN tbl_hojavida_datalaboral l
        ON p.hv_idpersonal = l.hv_idpersonal
        INNER JOIN tbl_hojavida_datanivelafinidad n
        ON n.hv_idinvelafinidad = l.nivel_afinidad
        WHERE n.nivelafinidad='Operativo' ")->queryAll();






        $totalDecisorEstrategico   = Yii::$app->db->createCommand("SELECT COUNT(*) AS total FROM tbl_hojavida_datapersonal p
        INNER JOIN tbl_hojavida_datalaboral l
        ON p.hv_idpersonal = l.hv_idpersonal
        INNER JOIN tbl_hojavida_datanivelafinidad n
        ON n.hv_idinvelafinidad = l.nivel_afinidad
        INNER JOIN tbl_hojavida_datatipoafinidad t
        ON t.hv_idtipoafinidad = l.tipo_afinidad
        where t.tipoafinidad='Decisor' AND n.nivelafinidad='EstratÃƒÂ©gico' ")->queryAll();


        $totalDecisorOperativo   = Yii::$app->db->createCommand("SELECT COUNT(*) AS total FROM tbl_hojavida_datapersonal p
        INNER JOIN tbl_hojavida_datalaboral l
        ON p.hv_idpersonal = l.hv_idpersonal
        INNER JOIN tbl_hojavida_datanivelafinidad n
        ON n.hv_idinvelafinidad = l.nivel_afinidad
        INNER JOIN tbl_hojavida_datatipoafinidad t
        ON t.hv_idtipoafinidad = l.tipo_afinidad
        where t.tipoafinidad='Decisor' AND n.nivelafinidad='Operativo' ")->queryAll();



        $totalNoDecisorEstrategico   = Yii::$app->db->createCommand("SELECT COUNT(*) AS total FROM tbl_hojavida_datapersonal p
        INNER JOIN tbl_hojavida_datalaboral l
        ON p.hv_idpersonal = l.hv_idpersonal
        INNER JOIN tbl_hojavida_datanivelafinidad n
        ON n.hv_idinvelafinidad = l.nivel_afinidad
        INNER JOIN tbl_hojavida_datatipoafinidad t
        ON t.hv_idtipoafinidad = l.tipo_afinidad
        where t.tipoafinidad='No Decisor' AND n.nivelafinidad='EstratÃƒÂ©gico' ")->queryAll();


        $totalNoDecisorOperativo   = Yii::$app->db->createCommand("SELECT COUNT(*) AS total FROM tbl_hojavida_datapersonal p
        INNER JOIN tbl_hojavida_datalaboral l
        ON p.hv_idpersonal = l.hv_idpersonal
        INNER JOIN tbl_hojavida_datanivelafinidad n
        ON n.hv_idinvelafinidad = l.nivel_afinidad
        INNER JOIN tbl_hojavida_datatipoafinidad t
        ON t.hv_idtipoafinidad = l.tipo_afinidad
        where t.tipoafinidad='No Decisor' AND n.nivelafinidad='Operativo' ")->queryAll();
        

       $directores = Yii::$app->db->createCommand('SELECT director_programa 
       FROM tbl_proceso_cliente_centrocosto 
       GROUP BY documento_director 
       ORDER BY director_programa
       ')->queryAll();



       return $this->render('resumen',[
           'roles' => $roles,
           'clientsTotalBogota'   => $clientsTotalBogota,
           'clientsTotalMedellin' => $clientsTotalMedellin,
           'clientsTotalAdmin'    => $clientsTotalAdmin,

           'clientDecisorBogota'  => $clientDecisorBogota,
           'clientDecisorMedellin'=> $clientDecisorMedellin,
           'clientDecisor'        => $clientDecisor ,

           'clientEstrategicoBogota'  => $clientEstrategicoBogota,
           'clientEstrategicoMedellin'=> $clientEstrategicoMedellin,
           'clientEstrategico'        => $clientEstrategico,


           'clientOperativoBogota'   =>  $clientOperativoBogota,
           'clientOperativoMedellin' =>  $clientOperativoMedellin,
           'clienOperativo'          =>  $clienOperativo,

           'totalDecisorEstrategico'  => $totalDecisorEstrategico,
           'totalDecisorOperativo'    => $totalDecisorOperativo,
           'totalNoDecisorEstrategico'  => $totalDecisorEstrategico,
           'totalNoDecisorOperativo'    => $totalDecisorOperativo,
            'directores' =>$directores
        ]);




    }

    public function actionResumenapi(){
      $directores = Yii::$app->db->createCommand('SELECT  d.ccdirector AS cedula , COUNT(*) AS total,
      (SELECT c.director_programa FROM tbl_proceso_cliente_centrocosto c WHERE c.documento_director = d.ccdirector LIMIT 1) AS nombre
      FROM tbl_hojavida_datadirector d
      GROUP BY d.ccdirector
      ')->queryAll();
       return  json_encode($directores);
    }

    public function actionResumenapicliente(){

       $clientes =  Yii::$app->db->createCommand('SELECT COUNT(*) AS total,
        (SELECT c.cliente FROM tbl_proceso_cliente_centrocosto c  WHERE c.id_dp_clientes = pcrc.id_dp_cliente LIMIT 1 ) AS cliente
        FROM tbl_hojavida_datapcrc pcrc
        WHERE pcrc.id_dp_cliente != 0
        GROUP BY pcrc.id_dp_cliente')->queryAll();

        return json_encode($clientes);
    }
 
   
    public function actionAcademico(){
       $profesion =  Yii::$app->db->createCommand('select * from tbl_hv_cursosacademico where idhvacademico = 4')->queryAll();
       $especializacion =  Yii::$app->db->createCommand('select * from tbl_hv_cursosacademico where idhvacademico = 2')->queryAll();
       $maestria =  Yii::$app->db->createCommand('select * from tbl_hv_cursosacademico where idhvacademico = 3')->queryAll();
       $doctorado =  Yii::$app->db->createCommand('select * from tbl_hv_cursosacademico where idhvacademico = 1')->queryAll();
       return $this->render('academico',[
          'profesion' => $profesion,
          'especializacion' => $especializacion,
          'maestria' =>$maestria,
          'doctorado' =>$doctorado
       ]);
    }

    
    public function actionProfesion(){
      Yii::$app->db->createCommand()->insert('tbl_hv_cursosacademico',[
        'idhvacademico'=> 4,
        'hv_cursos'=> Yii::$app->request->post('profesion')
      ])
      ->execute();
      Yii::$app->session->setFlash('list','Lista Agregada Exitosamente');
      return $this->redirect(['academico']) ;
   }

   public function actionEspecializacion(){
     Yii::$app->db->createCommand()->insert('tbl_hv_cursosacademico',[
       'idhvacademico'=> 2,
       'hv_cursos'=> Yii::$app->request->post('especializacion')
     ])
     ->execute();
     Yii::$app->session->setFlash('list','Lista Agregada Exitosamente');
     return $this->redirect(['academico']) ;
   }

   public function actionMaestria(){
     Yii::$app->db->createCommand()->insert('tbl_hv_cursosacademico',[
       'idhvacademico'=> 3,
       'hv_cursos'=> Yii::$app->request->post('maestria')
     ])
     ->execute();
     Yii::$app->session->setFlash('list','Lista Agregada Exitosamente');
     return $this->redirect(['academico']) ;
   }

   public function actionDoctorado(){
     Yii::$app->db->createCommand()->insert('tbl_hv_cursosacademico',[
       'idhvacademico'=> 1,
       'hv_cursos'=> Yii::$app->request->post('doctorado')
    ])
     ->execute();
     Yii::$app->session->setFlash('list','Lista Agregada Exitosamente');
     return $this->redirect(['academico']) ;
   }
    
   public function actionEliminarprofesion($id){
      
      Yii::$app->db->createCommand('DELETE FROM tbl_hv_cursosacademico WHERE idhvcursosacademico=:id')
      ->bindParam(':id',$id )
      ->execute();
      Yii::$app->session->setFlash('list','Lista Eliminada Exitosamente');
      return $this->redirect(['academico']);
   }
   
   public function actionEliminarespecializacion($id){
      Yii::$app->db->createCommand('DELETE FROM tbl_hv_cursosacademico WHERE idhvcursosacademico=:id')
      ->bindParam(':id',$id )
      ->execute();
      Yii::$app->session->setFlash('list','Lista Eliminada Exitosamente');
      return $this->redirect(['academico']);
   }

   public function actionEliminarmaestria($id){
    Yii::$app->db->createCommand('DELETE FROM tbl_hv_cursosacademico WHERE idhvcursosacademico=:id')
    ->bindParam(':id',$id )
    ->execute();
    Yii::$app->session->setFlash('list','Lista Eliminada Exitosamente');
    return $this->redirect(['academico']);
   }

   public function actionEliminardoctorado($id){
    Yii::$app->db->createCommand('DELETE FROM tbl_hv_cursosacademico WHERE idhvcursosacademico=:id')
    ->bindParam(':id',$id )
    ->execute();
    Yii::$app->session->setFlash('list','Lista Eliminada Exitosamente');
    return $this->redirect(['academico']);
   }


   public function actionExcelexportadmin(){
  
    $varCorreo = Yii::$app->request->post("email");


    $varlistusuarios = Yii::$app->db->createCommand("SELECT p.nombre_full AS nombre, p.identificacion AS identificacion,
    p.direccion_oficina AS hvdireccionoficina , p.direccion_casa AS hvdireccioncasa,
    p.email AS hvemailcorporativo , p.numero_movil AS hvmovil, p.numero_fijo AS hvcontactooficina,
    pa.pais AS hvpais , ci.ciudad AS hvciudad , m.modalidad AS hvmodalidadtrabajo, p.indicador_satu,
    p.fechacreacion, a.afinidad AS afinidad , t.tipoafinidad AS tipo , n.nivelafinidad , c.cantidadhijos ,
    civ.estadocivil, c.NombreHijos , h.text AS hobbie , g.text AS gustos, cla.ciudadclasificacion, ant.antiguedad,
    l.nombre_jefe, l.rol, l.trabajo_anterior, l.fecha_inicio_contacto, social.estilosocial, 
    if(p.tratamiento_data=1,'NO',if(p.tratamiento_data=2,'Si','NA')) AS tratamiento,
    if(da.activo=1,'Activo','No Activo') AS estados,    
    if(p.suceptible=1,'No','Si') AS suceptible,
    pcc.director_programa AS director, pcc.documento_director AS documentodirector,
    pcc.gerente_cuenta AS gerente, pcc.documento_gerente AS documentogerente, pcc.cliente AS cliente
    
    FROM tbl_hojavida_datapersonal p
    
    LEFT JOIN   tbl_hojavida_dataclasificacion cla
    ON cla.hv_idclasificacion = p.clasificacion
    
    LEFT JOIN   tbl_hojavida_datalaboral l
    ON p.hv_idpersonal = l.hv_idpersonal
    
    LEFT JOIN tbl_hv_antiguedad_rol ant
    ON ant.hv_id_antiguedad = l.hv_id_antiguedad
    
    LEFT JOIN  tbl_hv_pais pa
    ON pa.hv_idpais = p.hv_idpais
    
    LEFT JOIN  tbl_hv_ciudad ci
    ON ci.hv_idciudad = p.hv_idciudad
    
    LEFT JOIN tbl_hojavida_dataafinidad a
    ON a.hv_idafinidad = l.afinidad
    
    LEFT JOIN tbl_hojavida_datatipoafinidad t
    on t.hv_idtipoafinidad = l.tipo_afinidad
    
    LEFT JOIN tbl_hojavida_datanivelafinidad n
    ON n.hv_idinvelafinidad = l.tipo_afinidad
    
    LEFT JOIN tbl_hv_modalidad_trabajo m
    ON m.hv_idmodalidad = p.hv_idmodalidad
    
    LEFT JOIN tbl_hojavida_datacomplementos c
    ON c.hv_idpersonal = p.hv_idpersonal
    
    LEFT JOIN tbl_hv_hobbies h
    ON h.id = c.idhobbies
    
    LEFT JOIN tbl_hv_gustos g
    on g.id = c.idgustos
    
    LEFT JOIN  tbl_hv_estilosocial social
    on social.idestilosocial = c.idestilosocial
    
    LEFT JOIN tbl_hojavida_datacivil civ
    ON civ.hv_idcivil = c.hv_idcivil

    LEFT JOIN tbl_hojavida_dataacademica da
    ON da.hv_idpersonal = p.hv_idpersonal
    
    LEFT JOIN tbl_hojavida_datapcrc hp
    ON hp.hv_idpersonal = p.hv_idpersonal
    
    LEFT JOIN tbl_hojavida_datadirector dt
    ON dt.hv_idpersonal = p.hv_idpersonal
	 
	  LEFT JOIN tbl_hojavida_datagerente dg
	  ON dg.hv_idpersonal = p.hv_idpersonal
    
    LEFT JOIN tbl_proceso_cliente_centrocosto pcc
    ON pcc.documento_director = dt.ccdirector
    	AND pcc.documento_gerente = dg.ccgerente
        AND pcc.id_dp_clientes = hp.id_dp_cliente
        
    GROUP BY p.identificacion")->queryAll();

    $phpExc = new \PHPExcel();
    $phpExc->getProperties()
            ->setCreator("Konecta")
            ->setLastModifiedBy("Konecta")
            ->setTitle("Lista de procesos - Gestor de Clientes")
            ->setSubject("Gestor de Clientes")
            ->setDescription("Este archivo contiene el listado de los usuarios registrados para maestro cliente")
            ->setKeywords("Lista de Procesos");
    $phpExc->setActiveSheetIndex(0);
   
    $phpExc->getActiveSheet()->setShowGridlines(False);

    $styleArray = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

    $styleColor = array( 
            'fill' => array( 
                'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                'color' => array('rgb' => '28559B'),
            )
        );

    $styleArrayTitle = array(
            'font' => array(
              'bold' => false,
              'color' => array('rgb' => 'FFFFFF')
            )
        );

    $styleArraySubTitle2 = array(              
            'fill' => array( 
                'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                'color' => array('rgb' => 'C6C6C6'),
            )
        );  

    // ARRAY STYLE FONT COLOR AND TEXT ALIGN CENTER
    $styleArrayBody = array(
            'font' => array(
                'bold' => false,
                'color' => array('rgb' => '2F4F4F')
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => 'DDDDDD')
                )
            )
        );

      $phpExc->getDefaultStyle()->applyFromArray($styleArrayBody);

      $phpExc->getActiveSheet()->SetCellValue('A1','KONECTA - CX MANAGEMENT');
      $phpExc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
      $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArrayTitle);
      $phpExc->setActiveSheetIndex(0)->mergeCells('A1:AJ1');
  
      $phpExc->getActiveSheet()->SetCellValue('A2','NOMBRE');
      $phpExc->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArraySubTitle2);
  
      $phpExc->getActiveSheet()->SetCellValue('B2','IDENTIFICACION');
      $phpExc->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleArraySubTitle2);
  
      $phpExc->getActiveSheet()->SetCellValue('C2','DIRECCION OFICINA');
      $phpExc->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleArraySubTitle2);
  
      $phpExc->getActiveSheet()->SetCellValue('D2','DIRECCION CASA');
      $phpExc->getActiveSheet()->getStyle('D2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleArraySubTitle2);
  
      $phpExc->getActiveSheet()->SetCellValue('E2','CORREO');
      $phpExc->getActiveSheet()->getStyle('E2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleArraySubTitle2);
  
  
      $phpExc->getActiveSheet()->SetCellValue('F2','CELULAR');
      $phpExc->getActiveSheet()->getStyle('F2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleArraySubTitle2);
  
  
  
      $phpExc->getActiveSheet()->SetCellValue('G2','CONTACTO OFICINA');
      $phpExc->getActiveSheet()->getStyle('G2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleArraySubTitle2);
  
    
      $phpExc->getActiveSheet()->SetCellValue('H2','PAIS');
      $phpExc->getActiveSheet()->getStyle('H2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleArraySubTitle2);
  
  
  
      $phpExc->getActiveSheet()->SetCellValue('I2','CIUDAD');
      $phpExc->getActiveSheet()->getStyle('I2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('I2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('I2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('I2')->applyFromArray($styleArraySubTitle2);
  
  
  
      $phpExc->getActiveSheet()->SetCellValue('J2','MODALIDAD DE TRABAJO');
      $phpExc->getActiveSheet()->getStyle('J2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('J2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('J2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('J2')->applyFromArray($styleArraySubTitle2);
  
  
      $phpExc->getActiveSheet()->SetCellValue('K2','SATU');
      $phpExc->getActiveSheet()->getStyle('K2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('K2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('K2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('K2')->applyFromArray($styleArraySubTitle2);
  
      $phpExc->getActiveSheet()->SetCellValue('L2','FECHA DE CREACION');
      $phpExc->getActiveSheet()->getStyle('L2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('L2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('L2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('L2')->applyFromArray($styleArraySubTitle2);
  
      $phpExc->getActiveSheet()->SetCellValue('M2','AFINIDAD');
      $phpExc->getActiveSheet()->getStyle('M2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('M2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('M2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('M2')->applyFromArray($styleArraySubTitle2);
  
  
      $phpExc->getActiveSheet()->SetCellValue('N2','TIPO');
      $phpExc->getActiveSheet()->getStyle('N2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('N2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('N2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('N2')->applyFromArray($styleArraySubTitle2);
  
  
      $phpExc->getActiveSheet()->SetCellValue('O2','NIVEL DE AFINIDAD');
      $phpExc->getActiveSheet()->getStyle('O2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('O2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('O2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('O2')->applyFromArray($styleArraySubTitle2);
  
  
      $phpExc->getActiveSheet()->SetCellValue('P2','CANTIDAD DE HIJOS');
      $phpExc->getActiveSheet()->getStyle('P2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('P2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('P2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('P2')->applyFromArray($styleArraySubTitle2);
  
  
      $phpExc->getActiveSheet()->SetCellValue('Q2','ESTADO CIVIL');
      $phpExc->getActiveSheet()->getStyle('Q2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('Q2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('Q2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('Q2')->applyFromArray($styleArraySubTitle2);
  
  
      $phpExc->getActiveSheet()->SetCellValue('R2','NOMBRE DE HIJOS');
      $phpExc->getActiveSheet()->getStyle('R2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('R2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('R2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('R2')->applyFromArray($styleArraySubTitle2);
  
  
      $phpExc->getActiveSheet()->SetCellValue('S2','HOBBIES');
      $phpExc->getActiveSheet()->getStyle('S2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('S2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('S2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('S2')->applyFromArray($styleArraySubTitle2);
  
  
      $phpExc->getActiveSheet()->SetCellValue('T2','GUSTOS');
      $phpExc->getActiveSheet()->getStyle('T2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('T2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('T2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('T2')->applyFromArray($styleArraySubTitle2);
  
  
      $phpExc->getActiveSheet()->SetCellValue('U2','CIUDAD DE CLASIFICACION');
      $phpExc->getActiveSheet()->getStyle('U2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('U2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('U2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('U2')->applyFromArray($styleArraySubTitle2);
  
  
      $phpExc->getActiveSheet()->SetCellValue('V2','ANTIGUEDAD');
      $phpExc->getActiveSheet()->getStyle('V2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('V2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('V2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('V2')->applyFromArray($styleArraySubTitle2);
  
  
      $phpExc->getActiveSheet()->SetCellValue('W2','NOMBRE JEFE');
      $phpExc->getActiveSheet()->getStyle('W2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('W2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('W2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('W2')->applyFromArray($styleArraySubTitle2);
  
      $phpExc->getActiveSheet()->SetCellValue('X2','CARGO CONTACTO');
      $phpExc->getActiveSheet()->getStyle('X2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('X2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('X2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('X2')->applyFromArray($styleArraySubTitle2);
  
      $phpExc->getActiveSheet()->SetCellValue('Y2','TRABAJO ANTERIOR');
      $phpExc->getActiveSheet()->getStyle('Y2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('Y2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('Y2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('Y2')->applyFromArray($styleArraySubTitle2);
  
  
      $phpExc->getActiveSheet()->SetCellValue('Z2','FECHA INICIO CONTACTO');
      $phpExc->getActiveSheet()->getStyle('Z2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('Z2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('Z2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('Z2')->applyFromArray($styleArraySubTitle2);
  
  
      $phpExc->getActiveSheet()->SetCellValue('AA2','ESTILO SOCIAL');
      $phpExc->getActiveSheet()->getStyle('AA2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('AA2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('AA2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('AA2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('AB2','AUTORIZA TRATAMIENTO DE DATOS');
      $phpExc->getActiveSheet()->getStyle('AB2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('AB2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('AB2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('AB2')->applyFromArray($styleArraySubTitle2);
  
      $phpExc->getActiveSheet()->SetCellValue('AC2','DIRECTOR');
      $phpExc->getActiveSheet()->getStyle('AC2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('AC2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('AC2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('AC2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('AD2','DOCUMENTO DIRECTOR');
      $phpExc->getActiveSheet()->getStyle('AD2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('AD2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('AD2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('AD2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('AE2','GERENTE');
      $phpExc->getActiveSheet()->getStyle('AE2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('AE2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('AE2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('AE2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('AF2','DOCUMENTO GERENTE');
      $phpExc->getActiveSheet()->getStyle('AF2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('AF2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('AF2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('AF2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('AG2','CLIENTE');
      $phpExc->getActiveSheet()->getStyle('AG2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('AG2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('AG2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('AG2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('AH2','ESTADO CONTACTO');
      $phpExc->getActiveSheet()->getStyle('AH2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('AH2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('AH2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('AH2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('AI2','SUCEPTBILE ENCUESTAR');
      $phpExc->getActiveSheet()->getStyle('AI2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('AI2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('AI2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('AI2')->applyFromArray($styleArraySubTitle2);
  
    
   
    $numCell = 3;
    foreach ($varlistusuarios as $key => $value) {
      $numCell++;

      $phpExc->getActiveSheet()->setCellValue('A'.$numCell, $value['nombre']); 

      $phpExc->getActiveSheet()->setCellValue('B'.$numCell, $value['identificacion']); 

      $phpExc->getActiveSheet()->setCellValue('C'.$numCell, $value['hvdireccionoficina']);

      $phpExc->getActiveSheet()->setCellValue('D'.$numCell, $value['hvdireccioncasa']);

      $phpExc->getActiveSheet()->setCellValue('E'.$numCell, $value['hvemailcorporativo']); 

      $phpExc->getActiveSheet()->setCellValue('F'.$numCell, $value['hvmovil']); 

      $phpExc->getActiveSheet()->setCellValue('G'.$numCell, $value['hvcontactooficina']);

      $phpExc->getActiveSheet()->setCellValue('H'.$numCell, $value['hvpais']); 
      $phpExc->getActiveSheet()->setCellValue('I'.$numCell, $value['hvciudad']); 
      $phpExc->getActiveSheet()->setCellValue('J'.$numCell, $value['hvmodalidadtrabajo']); 
      $phpExc->getActiveSheet()->setCellValue('K'.$numCell, $value['indicador_satu']);
      $phpExc->getActiveSheet()->setCellValue('L'.$numCell, $value['fechacreacion']);

      $phpExc->getActiveSheet()->setCellValue('M'.$numCell, $value['afinidad']); 
      $phpExc->getActiveSheet()->setCellValue('N'.$numCell, $value['tipo']); 
      $phpExc->getActiveSheet()->setCellValue('O'.$numCell, $value['nivelafinidad']); 
      $phpExc->getActiveSheet()->setCellValue('P'.$numCell, $value['cantidadhijos']); 
      $phpExc->getActiveSheet()->setCellValue('Q'.$numCell, $value['estadocivil']); 
      $phpExc->getActiveSheet()->setCellValue('R'.$numCell, $value['NombreHijos']); 
      $phpExc->getActiveSheet()->setCellValue('S'.$numCell, $value['hobbie']);
      $phpExc->getActiveSheet()->setCellValue('T'.$numCell, $value['gustos']);

      $phpExc->getActiveSheet()->setCellValue('U'.$numCell, $value['ciudadclasificacion']); 
      $phpExc->getActiveSheet()->setCellValue('V'.$numCell, $value['antiguedad']); 
      $phpExc->getActiveSheet()->setCellValue('W'.$numCell, $value['nombre_jefe']); 
      $phpExc->getActiveSheet()->setCellValue('X'.$numCell, $value['rol']); 
      $phpExc->getActiveSheet()->setCellValue('Y'.$numCell, $value['trabajo_anterior']); 
      $phpExc->getActiveSheet()->setCellValue('Z'.$numCell, $value['fecha_inicio_contacto']); 
      $phpExc->getActiveSheet()->setCellValue('AA'.$numCell, $value['estilosocial']);
      $phpExc->getActiveSheet()->setCellValue('AB'.$numCell, $value['tratamiento']);
      
      $phpExc->getActiveSheet()->setCellValue('AC'.$numCell, $value['director']);
      $phpExc->getActiveSheet()->setCellValue('AD'.$numCell, $value['documentodirector']);
      $phpExc->getActiveSheet()->setCellValue('AE'.$numCell, $value['gerente']);
      $phpExc->getActiveSheet()->setCellValue('AF'.$numCell, $value['documentogerente']);
      $phpExc->getActiveSheet()->setCellValue('AG'.$numCell, $value['cliente']);

      $phpExc->getActiveSheet()->setCellValue('AH'.$numCell, $value['estados']);

      $phpExc->getActiveSheet()->setCellValue('AI'.$numCell, $value['suceptible']);

    }
    $numCell = $numCell;

    $hoy = getdate();
    $hoy = $hoy['year']."_".$hoy['month']."_".$hoy['mday']."ListadoProcesos_GestorClientes_Contactos";
          
    $objWriter = \PHPExcel_IOFactory::createWriter($phpExc, 'Excel5');
            
    $tmpFile = tempnam(sys_get_temp_dir(), $hoy);
    $tmpFile.= ".xls";

    $objWriter->save($tmpFile);

    $message = "<html><body>";
    $message .= "<h3>Adjunto del archivo sobre los contactos del gestor de clientes - CXM</h3>";
    $message .= "</body></html>";

    Yii::$app->mailer->compose()
                    ->setTo($varCorreo)
                    ->setFrom(Yii::$app->params['email_satu_from'])
                    ->setSubject("Envio Listado de Contactos - Gestor Clientes")
                    ->attach($tmpFile)
                    ->setHtmlBody($message)
                    ->send();

     Yii::$app->session->setFlash('file','Correo Enviado Exitosamente');
    return $this->redirect(['index']);

   }

   public function actionExcelexport(){

    $varCorreo = Yii::$app->request->post("email");

    $sessiones = Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol->select(['tbl_roles.role_id'])
        ->from('tbl_roles')
        ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                    'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
        ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                    'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
        ->where(['=','tbl_usuarios.usua_id',$sessiones]);                    
    $command = $rol->createCommand();
    $roles = $command->queryScalar();

    if ($roles != '270') {
      $varListaClientesPermisos = (new \yii\db\Query())
                                              ->select(['tbl_hojavida_permisoscliente.id_dp_clientes'])
                                              ->from(['tbl_hojavida_permisoscliente'])            
                                              ->where(['=','tbl_hojavida_permisoscliente.usuario_registro',$sessiones])
                                              ->andwhere(['!=','tbl_hojavida_permisoscliente.id_dp_clientes',1])
                                              ->groupby(['tbl_hojavida_permisoscliente.id_dp_clientes'])
                                              ->All();
    }else{
      $varListaClientesPermisos = (new \yii\db\Query())
                                              ->select(['tbl_proceso_cliente_centrocosto.id_dp_clientes'])
                                              ->from(['tbl_proceso_cliente_centrocosto'])            
                                              ->where(['!=','tbl_proceso_cliente_centrocosto.id_dp_clientes',1])
                                              ->groupby(['tbl_proceso_cliente_centrocosto.id_dp_clientes'])
                                              ->All();
    }


    $varArrayPermisos = array();
    foreach ($varListaClientesPermisos as $key => $value) {
      array_push($varArrayPermisos, intval($value['id_dp_clientes']));
    }
    $varListarPermisos = implode(", ", $varArrayPermisos);
    $varDataPermisos = explode(",", str_replace(array("#", "'", ";", " "), '', $varListarPermisos));

    $varlistusuarios = (new \yii\db\Query())
                      ->select([
                        'tbl_hojavida_datapersonal.nombre_full AS nombre', 
                        'tbl_hojavida_datapersonal.identificacion AS identificacion',
                        'tbl_hojavida_datapersonal.direccion_oficina AS hvdireccionoficina', 
                        'tbl_hojavida_datapersonal.direccion_casa AS hvdireccioncasa',
                        'tbl_hojavida_datapersonal.email AS hvemailcorporativo', 
                        'tbl_hojavida_datapersonal.numero_movil AS hvmovil', 
                        'tbl_hojavida_datapersonal.numero_fijo AS hvcontactooficina',
                        'tbl_hv_pais.pais AS hvpais', 
                        'tbl_hv_ciudad.ciudad AS hvciudad', 
                        'tbl_hv_modalidad_trabajo.modalidad AS hvmodalidadtrabajo', 
                        'tbl_hojavida_datapersonal.indicador_satu',
                        'tbl_hojavida_datapersonal.fechacreacion', 
                        'tbl_hojavida_dataafinidad.afinidad AS afinidad', 
                        'tbl_hojavida_datatipoafinidad.tipoafinidad AS tipo', 
                        'tbl_hojavida_datanivelafinidad.nivelafinidad', 
                        'tbl_hojavida_datacomplementos.cantidadhijos',
                        'tbl_hojavida_datacivil.estadocivil', 
                        'tbl_hojavida_datacomplementos.NombreHijos', 
                        'tbl_hv_hobbies.text AS hobbie', 
                        'tbl_hv_gustos.text AS gustos', 
                        'tbl_hojavida_dataclasificacion.ciudadclasificacion', 
                        'tbl_hv_antiguedad_rol.antiguedad',
                        'tbl_hojavida_datalaboral.nombre_jefe', 
                        'tbl_hojavida_datalaboral.rol  as cargo_jefe', 
                        'tbl_hojavida_datalaboral.trabajo_anterior', 
                        'tbl_hojavida_datalaboral.fecha_inicio_contacto', 
                        'tbl_hv_estilosocial.estilosocial', 
                        'if(tbl_hojavida_datapersonal.tratamiento_data=1,"NO",if(tbl_hojavida_datapersonal.tratamiento_data=2,"Si","NA")) AS tratamiento',
                        'if(tbl_hojavida_dataacademica.activo=1,"Activo","No Activo") AS estados',
                        'if(tbl_hojavida_datapersonal.suceptible=1,"No","Si") AS suceptible',
                        'tbl_proceso_cliente_centrocosto.director_programa AS director', 
                        'tbl_proceso_cliente_centrocosto.documento_director AS documentodirector',
                        'tbl_proceso_cliente_centrocosto.gerente_cuenta AS gerente', 
                        'tbl_proceso_cliente_centrocosto.documento_gerente AS documentogerente', 
                        'tbl_proceso_cliente_centrocosto.cliente AS cliente'
                      ])

                      ->from(['tbl_hojavida_datapersonal']) 

                      ->join('LEFT OUTER JOIN', 'tbl_hojavida_dataclasificacion',
                          'tbl_hojavida_dataclasificacion.hv_idclasificacion = tbl_hojavida_datapersonal.clasificacion') 

                      ->join('LEFT OUTER JOIN', 'tbl_hojavida_datalaboral',
                          'tbl_hojavida_datapersonal.hv_idpersonal = tbl_hojavida_datalaboral.hv_idpersonal')

                      ->join('LEFT OUTER JOIN', 'tbl_hv_antiguedad_rol',
                          'tbl_hv_antiguedad_rol.hv_id_antiguedad = tbl_hojavida_datalaboral.hv_id_antiguedad') 

                      ->join('LEFT OUTER JOIN', 'tbl_hv_pais',
                          'tbl_hv_pais.hv_idpais = tbl_hojavida_datapersonal.hv_idpais')

                      ->join('LEFT OUTER JOIN', 'tbl_hv_ciudad',
                          'tbl_hv_ciudad.hv_idciudad = tbl_hojavida_datapersonal.hv_idciudad') 

                      ->join('LEFT OUTER JOIN', 'tbl_hojavida_dataafinidad',
                          'tbl_hojavida_dataafinidad.hv_idafinidad = tbl_hojavida_datalaboral.afinidad') 

                      ->join('LEFT OUTER JOIN', 'tbl_hojavida_datatipoafinidad',
                          'tbl_hojavida_datatipoafinidad.hv_idtipoafinidad = tbl_hojavida_datalaboral.tipo_afinidad') 

                      ->join('LEFT OUTER JOIN', 'tbl_hojavida_datanivelafinidad',
                          'tbl_hojavida_datanivelafinidad.hv_idinvelafinidad = tbl_hojavida_datalaboral.tipo_afinidad') 

                      ->join('LEFT OUTER JOIN', 'tbl_hv_modalidad_trabajo',
                          'tbl_hv_modalidad_trabajo.hv_idmodalidad = tbl_hojavida_datapersonal.hv_idmodalidad') 

                      ->join('LEFT OUTER JOIN', 'tbl_hojavida_datacomplementos',
                          'tbl_hojavida_datacomplementos.hv_idpersonal = tbl_hojavida_datapersonal.hv_idpersonal') 

                      ->join('LEFT OUTER JOIN', 'tbl_hv_hobbies',
                          'tbl_hv_hobbies.id = tbl_hojavida_datacomplementos.idhobbies') 

                      ->join('LEFT OUTER JOIN', 'tbl_hv_gustos',
                          'tbl_hv_gustos.id = tbl_hojavida_datacomplementos.idgustos') 

                      ->join('LEFT OUTER JOIN', 'tbl_hv_estilosocial',
                          'tbl_hv_estilosocial.idestilosocial = tbl_hojavida_datacomplementos.idestilosocial') 

                      ->join('LEFT OUTER JOIN', 'tbl_hojavida_datacivil',
                          'tbl_hojavida_datacivil.hv_idcivil = tbl_hojavida_datacomplementos.hv_idcivil') 

                      ->join('LEFT OUTER JOIN', 'tbl_hojavida_dataacademica',
                          'tbl_hojavida_dataacademica.hv_idpersonal = tbl_hojavida_datapersonal.hv_idpersonal') 

                      ->join('LEFT OUTER JOIN', 'tbl_hojavida_datapcrc',
                          'tbl_hojavida_datapcrc.hv_idpersonal = tbl_hojavida_datapersonal.hv_idpersonal')

                      ->join('LEFT OUTER JOIN', 'tbl_proceso_cliente_centrocosto',
                          'tbl_proceso_cliente_centrocosto.id_dp_clientes = tbl_hojavida_datapcrc.id_dp_cliente')

                      ->where(['IN','tbl_proceso_cliente_centrocosto.id_dp_clientes',$varDataPermisos])
                      ->groupby(['tbl_hojavida_datapersonal.identificacion'])
                      ->All();

    $phpExc = new \PHPExcel();
    $phpExc->getProperties()
            ->setCreator("Konecta")
            ->setLastModifiedBy("Konecta")
            ->setTitle("Lista de usuarios - Evaluacion Desarrollo")
            ->setSubject("Evaluacion de Desarrollo")
            ->setDescription("Este archivo contiene el listado de los usuarios registrados para maestro cliente")
            ->setKeywords("Lista de usuarios");
    $phpExc->setActiveSheetIndex(0);

    $phpExc->getActiveSheet()->setShowGridlines(False);

    $styleArray = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

    $styleColor = array( 
            'fill' => array( 
                'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                'color' => array('rgb' => '28559B'),
            )
        );

    $styleArrayTitle = array(
            'font' => array(
              'bold' => false,
              'color' => array('rgb' => 'FFFFFF')
            )
        );

    $styleArraySubTitle2 = array(              
            'fill' => array( 
                'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                'color' => array('rgb' => 'C6C6C6'),
            )
        );  

    // ARRAY STYLE FONT COLOR AND TEXT ALIGN CENTER
    $styleArrayBody = array(
            'font' => array(
                'bold' => false,
                'color' => array('rgb' => '2F4F4F')
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => 'DDDDDD')
                )
            )
        );

        $phpExc->getDefaultStyle()->applyFromArray($styleArrayBody);

        $phpExc->getActiveSheet()->SetCellValue('A1','KONECTA - CX MANAGEMENT');
        $phpExc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArrayTitle);
        $phpExc->setActiveSheetIndex(0)->mergeCells('A1:AJ1');
    
        $phpExc->getActiveSheet()->SetCellValue('A2','NOMBRE');
        $phpExc->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArraySubTitle2);
    
        $phpExc->getActiveSheet()->SetCellValue('B2','IDENTIFICACION');
        $phpExc->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleArraySubTitle2);
    
        $phpExc->getActiveSheet()->SetCellValue('C2','DIRECCION OFICINA');
        $phpExc->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleArraySubTitle2);
    
        $phpExc->getActiveSheet()->SetCellValue('D2','DIRECCION CASA');
        $phpExc->getActiveSheet()->getStyle('D2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleArraySubTitle2);
    
        $phpExc->getActiveSheet()->SetCellValue('E2','CORREO');
        $phpExc->getActiveSheet()->getStyle('E2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleArraySubTitle2);
    
    
        $phpExc->getActiveSheet()->SetCellValue('F2','CELULAR');
        $phpExc->getActiveSheet()->getStyle('F2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleArraySubTitle2);
    
    
    
        $phpExc->getActiveSheet()->SetCellValue('G2','CONTACTO OFICINA');
        $phpExc->getActiveSheet()->getStyle('G2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleArraySubTitle2);
    
      
        $phpExc->getActiveSheet()->SetCellValue('H2','PAIS');
        $phpExc->getActiveSheet()->getStyle('H2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleArraySubTitle2);
    
    
    
        $phpExc->getActiveSheet()->SetCellValue('I2','CIUDAD');
        $phpExc->getActiveSheet()->getStyle('I2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('I2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('I2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('I2')->applyFromArray($styleArraySubTitle2);
    
    
    
        $phpExc->getActiveSheet()->SetCellValue('J2','MODALIDAD DE TRABAJO');
        $phpExc->getActiveSheet()->getStyle('J2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('J2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('J2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('J2')->applyFromArray($styleArraySubTitle2);
    
    
        $phpExc->getActiveSheet()->SetCellValue('K2','SATU');
        $phpExc->getActiveSheet()->getStyle('K2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('K2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('K2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('K2')->applyFromArray($styleArraySubTitle2);
    
        $phpExc->getActiveSheet()->SetCellValue('L2','FECHA DE CREACION');
        $phpExc->getActiveSheet()->getStyle('L2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('L2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('L2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('L2')->applyFromArray($styleArraySubTitle2);
    
        $phpExc->getActiveSheet()->SetCellValue('M2','AFINIDAD');
        $phpExc->getActiveSheet()->getStyle('M2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('M2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('M2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('M2')->applyFromArray($styleArraySubTitle2);
    
    
        $phpExc->getActiveSheet()->SetCellValue('N2','TIPO');
        $phpExc->getActiveSheet()->getStyle('N2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('N2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('N2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('N2')->applyFromArray($styleArraySubTitle2);
    
    
        $phpExc->getActiveSheet()->SetCellValue('O2','NIVEL DE AFINIDAD');
        $phpExc->getActiveSheet()->getStyle('O2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('O2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('O2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('O2')->applyFromArray($styleArraySubTitle2);
    
    
        $phpExc->getActiveSheet()->SetCellValue('P2','CANTIDAD DE HIJOS');
        $phpExc->getActiveSheet()->getStyle('P2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('P2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('P2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('P2')->applyFromArray($styleArraySubTitle2);
    
    
        $phpExc->getActiveSheet()->SetCellValue('Q2','ESTADO CIVIL');
        $phpExc->getActiveSheet()->getStyle('Q2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('Q2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('Q2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('Q2')->applyFromArray($styleArraySubTitle2);
    
    
        $phpExc->getActiveSheet()->SetCellValue('R2','NOMBRE DE HIJOS');
        $phpExc->getActiveSheet()->getStyle('R2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('R2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('R2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('R2')->applyFromArray($styleArraySubTitle2);
    
    
        $phpExc->getActiveSheet()->SetCellValue('S2','HOBBIES');
        $phpExc->getActiveSheet()->getStyle('S2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('S2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('S2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('S2')->applyFromArray($styleArraySubTitle2);
    
    
        $phpExc->getActiveSheet()->SetCellValue('T2','GUSTOS');
        $phpExc->getActiveSheet()->getStyle('T2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('T2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('T2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('T2')->applyFromArray($styleArraySubTitle2);
    
    
        $phpExc->getActiveSheet()->SetCellValue('U2','CIUDAD DE CLASIFICACION');
        $phpExc->getActiveSheet()->getStyle('U2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('U2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('U2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('U2')->applyFromArray($styleArraySubTitle2);
    
    
        $phpExc->getActiveSheet()->SetCellValue('V2','ANTIGUEDAD');
        $phpExc->getActiveSheet()->getStyle('V2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('V2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('V2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('V2')->applyFromArray($styleArraySubTitle2);
    
    
        $phpExc->getActiveSheet()->SetCellValue('W2','NOMBRE JEFE');
        $phpExc->getActiveSheet()->getStyle('W2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('W2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('W2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('W2')->applyFromArray($styleArraySubTitle2);
    
        $phpExc->getActiveSheet()->SetCellValue('X2','CARGO JEFE');
        $phpExc->getActiveSheet()->getStyle('X2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('X2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('X2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('X2')->applyFromArray($styleArraySubTitle2);
    
        $phpExc->getActiveSheet()->SetCellValue('Y2','TRABAJO ANTERIOR');
        $phpExc->getActiveSheet()->getStyle('Y2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('Y2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('Y2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('Y2')->applyFromArray($styleArraySubTitle2);
    
    
        $phpExc->getActiveSheet()->SetCellValue('Z2','FECHA INICIO CONTACTO');
        $phpExc->getActiveSheet()->getStyle('Z2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('Z2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('Z2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('Z2')->applyFromArray($styleArraySubTitle2);
    
    
        $phpExc->getActiveSheet()->SetCellValue('AA2','ESTILO SOCIAL');
        $phpExc->getActiveSheet()->getStyle('AA2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('AA2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('AA2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('AA2')->applyFromArray($styleArraySubTitle2);



    $numCell = 3;
    foreach ($varlistusuarios as $key => $value) {
      $numCell++;

      $phpExc->getActiveSheet()->setCellValue('A'.$numCell, $value['nombre']); 

      $phpExc->getActiveSheet()->setCellValue('B'.$numCell, $value['identificacion']); 

      $phpExc->getActiveSheet()->setCellValue('C'.$numCell, $value['hvdireccionoficina']);

      $phpExc->getActiveSheet()->setCellValue('D'.$numCell, $value['hvdireccioncasa']);

      $phpExc->getActiveSheet()->setCellValue('E'.$numCell, $value['hvemailcorporativo']); 

      $phpExc->getActiveSheet()->setCellValue('F'.$numCell, $value['hvmovil']); 

      $phpExc->getActiveSheet()->setCellValue('G'.$numCell, $value['hvcontactooficina']);

      $phpExc->getActiveSheet()->setCellValue('H'.$numCell, $value['hvpais']); 
      $phpExc->getActiveSheet()->setCellValue('I'.$numCell, $value['hvciudad']); 
      $phpExc->getActiveSheet()->setCellValue('J'.$numCell, $value['hvmodalidadtrabajo']); 
      $phpExc->getActiveSheet()->setCellValue('K'.$numCell, $value['indicador_satu']);
      $phpExc->getActiveSheet()->setCellValue('L'.$numCell, $value['fechacreacion']);

      $phpExc->getActiveSheet()->setCellValue('M'.$numCell, $value['afinidad']); 
      $phpExc->getActiveSheet()->setCellValue('N'.$numCell, $value['tipo']); 
      $phpExc->getActiveSheet()->setCellValue('O'.$numCell, $value['nivelafinidad']); 
      $phpExc->getActiveSheet()->setCellValue('P'.$numCell, $value['cantidadhijos']); 
      $phpExc->getActiveSheet()->setCellValue('Q'.$numCell, $value['estadocivil']); 
      $phpExc->getActiveSheet()->setCellValue('R'.$numCell, $value['NombreHijos']); 
      $phpExc->getActiveSheet()->setCellValue('S'.$numCell, $value['hobbie']);
      $phpExc->getActiveSheet()->setCellValue('T'.$numCell, $value['gustos']);

      $phpExc->getActiveSheet()->setCellValue('U'.$numCell, $value['ciudadclasificacion']); 
      $phpExc->getActiveSheet()->setCellValue('V'.$numCell, $value['antiguedad']); 
      $phpExc->getActiveSheet()->setCellValue('W'.$numCell, $value['nombre_jefe']); 
      $phpExc->getActiveSheet()->setCellValue('X'.$numCell, $value['cargo_jefe']); 
      $phpExc->getActiveSheet()->setCellValue('Y'.$numCell, $value['trabajo_anterior']); 
      $phpExc->getActiveSheet()->setCellValue('Z'.$numCell, $value['fecha_inicio_contacto']); 
      $phpExc->getActiveSheet()->setCellValue('AA'.$numCell, $value['estilosocial']);


    }
    $numCell = $numCell;

    $hoy = getdate();
    $hoy = $hoy['year']."_".$hoy['month']."_".$hoy['mday']."ListadoUsuarios_Evaluacion_Desarrollo";
          
    $objWriter = \PHPExcel_IOFactory::createWriter($phpExc, 'Excel5');
            
    $tmpFile = tempnam(sys_get_temp_dir(), $hoy);
    $tmpFile.= ".xls";

    $objWriter->save($tmpFile);

    $message = "<html><body>";
    $message .= "<h3>Adjunto del archivo listado usuario maestro cliente</h3>";
    $message .= "</body></html>";

    Yii::$app->mailer->compose()
                    ->setTo($varCorreo)
                    ->setFrom(Yii::$app->params['email_satu_from'])
                    ->setSubject("Envio Listado de usuarios registrado - Hoja de Vida")
                    ->attach($tmpFile)
                    ->setHtmlBody($message)
                    ->send();

     Yii::$app->session->setFlash('file','Correo Enviado Exitosamente');
    return $this->redirect(['index']);

   }


   public function actionExcelexporteventosadmin(){
    $varCorreo = Yii::$app->request->post("email");


    $evento =Yii::$app->db->createCommand("SELECT p.nombre_full AS nombre, p.identificacion , p.email,
    e.nombre_evento, e.tipo_evento,c.ciudad, e.fecha_evento_inicio , e.fecha_evento_fin,
    e.asistencia
    
    FROM tbl_hojavida_asignareventos a
    
    LEFT JOIN tbl_hojavida_eventos e
    ON e.hv_ideventos = a.hv_ideventos
    
    LEFT JOIN tbl_hojavida_datapersonal p
    ON p.hv_idpersonal = a.hv_idpersonal
    
    LEFT JOIN tbl_hv_ciudad c
    ON c.hv_idciudad = p.hv_idciudad
    ")->queryAll();

    $phpExc = new \PHPExcel();
    $phpExc->getProperties()
            ->setCreator("Konecta")
            ->setLastModifiedBy("Konecta")
            ->setTitle("Lista de usuarios - Evaluacion Desarrollo")
            ->setSubject("eventos - Evaluacion de Desarrollo")
            ->setDescription("Este archivo contiene el listado de los eventos registrados ")
            ->setKeywords("Lista de usuarios");
    $phpExc->setActiveSheetIndex(0);
   
    $phpExc->getActiveSheet()->setShowGridlines(False);

    $styleArray = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

    $styleColor = array( 
            'fill' => array( 
                'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                'color' => array('rgb' => '28559B'),
            )
        );

    $styleArrayTitle = array(
            'font' => array(
              'bold' => false,
              'color' => array('rgb' => 'FFFFFF')
            )
        );

    $styleArraySubTitle2 = array(              
            'fill' => array( 
                'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                'color' => array('rgb' => 'C6C6C6'),
            )
        );  

    // ARRAY STYLE FONT COLOR AND TEXT ALIGN CENTER
    $styleArrayBody = array(
            'font' => array(
                'bold' => false,
                'color' => array('rgb' => '2F4F4F')
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => 'DDDDDD')
                )
            )
        );

    $phpExc->getDefaultStyle()->applyFromArray($styleArrayBody);


    $phpExc->getActiveSheet()->SetCellValue('A1','KONECTA - CX MANAGEMENT');
    $phpExc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
    $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
    $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleColor);
    $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArrayTitle);
    $phpExc->setActiveSheetIndex(0)->mergeCells('A1:I1');

    $phpExc->getActiveSheet()->SetCellValue('A2','NOMBRE');
    $phpExc->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
    $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray);            
    $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleColor);
    $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArraySubTitle2);

    $phpExc->getActiveSheet()->SetCellValue('B2','IDENTIFICACION');
    $phpExc->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
    $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleArray);            
    $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleColor);
    $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleArraySubTitle2);

    $phpExc->getActiveSheet()->SetCellValue('C2','EMAIL');
    $phpExc->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
    $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleArray);            
    $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleColor);
    $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleArraySubTitle2);

    $phpExc->getActiveSheet()->SetCellValue('D2','NOMBRE EVENTO');
    $phpExc->getActiveSheet()->getStyle('D2')->getFont()->setBold(true);
    $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleArray);            
    $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleColor);
    $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleArraySubTitle2);

    $phpExc->getActiveSheet()->SetCellValue('E2','TIPO EVENTO');
    $phpExc->getActiveSheet()->getStyle('E2')->getFont()->setBold(true);
    $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleArray);            
    $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleColor);
    $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleArraySubTitle2);

    $phpExc->getActiveSheet()->SetCellValue('F2','CIUDAD EVENTO');
    $phpExc->getActiveSheet()->getStyle('F2')->getFont()->setBold(true);
    $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleArray);            
    $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleColor);
    $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleArraySubTitle2);

    $phpExc->getActiveSheet()->SetCellValue('G2','FECHA EVENTO INICIO');
    $phpExc->getActiveSheet()->getStyle('G2')->getFont()->setBold(true);
    $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleArray);            
    $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleColor);
    $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleArraySubTitle2);


    $phpExc->getActiveSheet()->SetCellValue('H2','FECHA EVENTO FIN');
    $phpExc->getActiveSheet()->getStyle('H2')->getFont()->setBold(true);
    $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleArray);            
    $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleColor);
    $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleArraySubTitle2);

    $phpExc->getActiveSheet()->SetCellValue('I2','ASISTENCIA');
    $phpExc->getActiveSheet()->getStyle('I2')->getFont()->setBold(true);
    $phpExc->getActiveSheet()->getStyle('I2')->applyFromArray($styleArray);            
    $phpExc->getActiveSheet()->getStyle('I2')->applyFromArray($styleColor);
    $phpExc->getActiveSheet()->getStyle('I2')->applyFromArray($styleArraySubTitle2);


  
      $numCell = 3;

      foreach ($evento as $key => $value) {
        $numCell++;

        $phpExc->getActiveSheet()->setCellValue('A'.$numCell, $value['nombre']); 
        $phpExc->getActiveSheet()->setCellValue('B'.$numCell, $value['identificacion']); 
        $phpExc->getActiveSheet()->setCellValue('C'.$numCell, $value['email']);
        $phpExc->getActiveSheet()->setCellValue('D'.$numCell, $value['nombre_evento']);

        $phpExc->getActiveSheet()->setCellValue('E'.$numCell, $value['tipo_evento']); 
        $phpExc->getActiveSheet()->setCellValue('F'.$numCell, $value['ciudad']); 
        $phpExc->getActiveSheet()->setCellValue('G'.$numCell, $value['fecha_evento_inicio']);     
        $phpExc->getActiveSheet()->setCellValue('H'.$numCell, $value['fecha_evento_fin']); 
        $phpExc->getActiveSheet()->setCellValue('I'.$numCell, $value['asistencia']); 

      }

    $numCell = $numCell;

    $hoy = getdate();
    $hoy = $hoy['year']."_".$hoy['month']."_".$hoy['mday']."ListadoUsuarios_Evaluacion_Desarrollo";
          
    $objWriter = \PHPExcel_IOFactory::createWriter($phpExc, 'Excel5');
            
    $tmpFile = tempnam(sys_get_temp_dir(), $hoy);
    $tmpFile.= ".xls";

    $objWriter->save($tmpFile);

    $message = "<html><body>";
    $message .= "<h3>Adjunto del archivo listado de eventos en maestro cliente</h3>";
    $message .= "</body></html>";

    Yii::$app->mailer->compose()
                    ->setTo($varCorreo)
                    ->setFrom(Yii::$app->params['email_satu_from'])
                    ->setSubject("Envio Listado de eventos registrado - Hoja de Vida")
                    ->attach($tmpFile)
                    ->setHtmlBody($message)
                    ->send();

     Yii::$app->session->setFlash('file','Correo Enviado Exitosamente');
    return $this->redirect(['index']);
   }

   public function actionExcelexporteventos(){
    $varCorreo = Yii::$app->request->post("email");
    $sessiones = Yii::$app->user->identity->id;


    $evento =Yii::$app->db->createCommand("SELECT p.nombre_full AS nombre, p.identificacion , p.email,
    e.nombre_evento, e.tipo_evento,c.ciudad, e.fecha_evento_inicio , e.fecha_evento_fin,
    e.asistencia
    
    FROM tbl_hojavida_asignareventos a
    
    LEFT JOIN tbl_hojavida_eventos e
    ON e.hv_ideventos = a.hv_ideventos
    
    LEFT JOIN tbl_hojavida_datapersonal p
    ON p.hv_idpersonal = a.hv_idpersonal
    
    LEFT JOIN tbl_hv_ciudad c
    ON c.hv_idciudad = p.hv_idciudad
    where p.usua_id = :id")->bindParam(':id',$sessiones)->queryAll();

    $phpExc = new \PHPExcel();
    $phpExc->getProperties()
            ->setCreator("Konecta")
            ->setLastModifiedBy("Konecta")
            ->setTitle("Lista de usuarios - Evaluaci n Desarrollo")
            ->setSubject("Evaluaci n de Desarrollo")
            ->setDescription("Este archivo contiene el listado de los usuarios registrados para maetsro cliente")
            ->setKeywords("Lista de usuarios");
    $phpExc->setActiveSheetIndex(0);
   
    $phpExc->getActiveSheet()->setShowGridlines(False);

    $styleArray = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

    $styleColor = array( 
            'fill' => array( 
                'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                'color' => array('rgb' => '28559B'),
            )
        );

    $styleArrayTitle = array(
            'font' => array(
              'bold' => false,
              'color' => array('rgb' => 'FFFFFF')
            )
        );

    $styleArraySubTitle2 = array(              
            'fill' => array( 
                'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                'color' => array('rgb' => 'C6C6C6'),
            )
        );  

    // ARRAY STYLE FONT COLOR AND TEXT ALIGN CENTER
    $styleArrayBody = array(
            'font' => array(
                'bold' => false,
                'color' => array('rgb' => '2F4F4F')
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => 'DDDDDD')
                )
            )
        );

    $phpExc->getDefaultStyle()->applyFromArray($styleArrayBody);


    $phpExc->getActiveSheet()->SetCellValue('A1','KONECTA - CX MANAGEMENT');
    $phpExc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
    $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
    $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleColor);
    $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArrayTitle);
    $phpExc->setActiveSheetIndex(0)->mergeCells('A1:I1');

    $phpExc->getActiveSheet()->SetCellValue('A2','NOMBRE');
    $phpExc->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
    $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray);            
    $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleColor);
    $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArraySubTitle2);

    $phpExc->getActiveSheet()->SetCellValue('B2','IDENTIFICACION');
    $phpExc->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
    $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleArray);            
    $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleColor);
    $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleArraySubTitle2);

    $phpExc->getActiveSheet()->SetCellValue('C2','EMAIL');
    $phpExc->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
    $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleArray);            
    $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleColor);
    $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleArraySubTitle2);

    $phpExc->getActiveSheet()->SetCellValue('D2','NOMBRE EVENTO');
    $phpExc->getActiveSheet()->getStyle('D2')->getFont()->setBold(true);
    $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleArray);            
    $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleColor);
    $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleArraySubTitle2);

    $phpExc->getActiveSheet()->SetCellValue('E2','TIPO EVENTO');
    $phpExc->getActiveSheet()->getStyle('E2')->getFont()->setBold(true);
    $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleArray);            
    $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleColor);
    $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleArraySubTitle2);

    $phpExc->getActiveSheet()->SetCellValue('F2','CIUDAD EVENTO');
    $phpExc->getActiveSheet()->getStyle('F2')->getFont()->setBold(true);
    $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleArray);            
    $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleColor);
    $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleArraySubTitle2);

    $phpExc->getActiveSheet()->SetCellValue('G2','FECHA EVENTO INICIO');
    $phpExc->getActiveSheet()->getStyle('G2')->getFont()->setBold(true);
    $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleArray);            
    $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleColor);
    $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleArraySubTitle2);


    $phpExc->getActiveSheet()->SetCellValue('H2','FECHA EVENTO FIN');
    $phpExc->getActiveSheet()->getStyle('H2')->getFont()->setBold(true);
    $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleArray);            
    $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleColor);
    $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleArraySubTitle2);

    $phpExc->getActiveSheet()->SetCellValue('I2','ASISTENCIA');
    $phpExc->getActiveSheet()->getStyle('I2')->getFont()->setBold(true);
    $phpExc->getActiveSheet()->getStyle('I2')->applyFromArray($styleArray);            
    $phpExc->getActiveSheet()->getStyle('I2')->applyFromArray($styleColor);
    $phpExc->getActiveSheet()->getStyle('I2')->applyFromArray($styleArraySubTitle2);


   
      $numCell = 3;

      foreach ($evento as $key => $value) {
        $numCell++;

        $phpExc->getActiveSheet()->setCellValue('A'.$numCell, $value['nombre']); 
        $phpExc->getActiveSheet()->setCellValue('B'.$numCell, $value['identificacion']); 
        $phpExc->getActiveSheet()->setCellValue('C'.$numCell, $value['email']);
        $phpExc->getActiveSheet()->setCellValue('D'.$numCell, $value['nombre_evento']);

        $phpExc->getActiveSheet()->setCellValue('E'.$numCell, $value['tipo_evento']); 
        $phpExc->getActiveSheet()->setCellValue('F'.$numCell, $value['ciudad']); 
        $phpExc->getActiveSheet()->setCellValue('G'.$numCell, $value['fecha_evento_inicio']);     
        $phpExc->getActiveSheet()->setCellValue('H'.$numCell, $value['fecha_evento_fin']); 
        $phpExc->getActiveSheet()->setCellValue('I'.$numCell, $value['asistencia']); 

      }

    $numCell = $numCell;

    $hoy = getdate();
    $hoy = $hoy['year']."_".$hoy['month']."_".$hoy['mday']."ListadoUsuarios_Evaluacion_Desarrollo";
          
    $objWriter = \PHPExcel_IOFactory::createWriter($phpExc, 'Excel5');
            
    $tmpFile = tempnam(sys_get_temp_dir(), $hoy);
    $tmpFile.= ".xls";

    $objWriter->save($tmpFile);

    $message = "<html><body>";
    $message .= "<h3>Adjunto del archivo listado de eventos maestro cliente</h3>";
    $message .= "</body></html>";

    Yii::$app->mailer->compose()
                    ->setTo($varCorreo)
                    ->setFrom(Yii::$app->params['email_satu_from'])
                    ->setSubject("Envio Listado de eventos registrado - Hoja de Vida")
                    ->attach($tmpFile)
                    ->setHtmlBody($message)
                    ->send();

     Yii::$app->session->setFlash('file','Correo Enviado Exitosamente');
    return $this->redirect(['index']);
   }


   public function actionExport(){
$modelos = new HojavidaDatapersonal();
    if($modelos->load(Yii::$app->request->post())){
        $modelos->file = UploadedFile::getInstance($modelos, 'file');
        $ruta = 'archivos/'.time()."_".$modelos->file->baseName. ".".$modelos->file->extension;
        $modelos->file->saveAs( $ruta ); 
         $this->Importexcel($ruta);  
    }else{
      #code
  }

    Yii::$app->session->setFlash('file','archivo cargado exitosamente');
    unlink($ruta);
    return $this->redirect(['index']);


  }

  public function Importexcel($name){

    $inputFile  = $name;

    try{
      $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
      $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
      $objPHPExcel = $objReader->load($inputFile);

    }catch(Exception $e) {
      die('Error');
    }


    $sheet = $objPHPExcel->getSheet(0);
    $highestRow = $sheet->getHighestRow();

    for ($row=3; $row < $highestRow; $row++) { 
      $varDocumento = $sheet->getCell("A".$row)->getValue();
      
      $paramsPais = [':TextPais' => $sheet->getCell("G".$row)->getValue()];
      $varIdPais = Yii::$app->db->createCommand('
      SELECT if(hp.hv_idpais = "",4,hp.hv_idpais) AS Pais_ids FROM tbl_hv_pais hp 
        WHERE 
          hp.anulado = 0 
            AND hp.pais IN (:TextPais)')->bindValues($paramsPais)->queryScalar();

      $paramsCiudad = [':TextCiudad' => $sheet->getCell("H".$row)->getValue()];
      $varIdCiudad = Yii::$app->db->createCommand('
      SELECT if(hc.hv_idciudad="",9,hc.hv_idciudad) AS Ciudad_ids FROM tbl_hv_ciudad hc
        WHERE 
          hc.anulado = 0
            AND hc.ciudad IN (:TextCiudad)')->bindValues($paramsCiudad)->queryScalar();

      $paramsModalidad = [':TextModalidad' => $sheet->getCell("J".$row)->getValue()];
      $varIdModalidad = Yii::$app->db->createCommand('
      SELECT if(mt.modalidad = "",2,mt.hv_idmodalidad) AS resultadoModalidad FROM tbl_hv_modalidad_trabajo mt
        WHERE 
          mt.anulado = 0
            AND mt.modalidad IN (:TextModalidad)')->bindValues($paramsModalidad)->queryScalar();

      $varAutoriza = null;
      $varTextAutoriza = $sheet->getCell("K".$row)->getValue();
      if ($varTextAutoriza == "Si" || $varTextAutoriza == "SI" || $varTextAutoriza == "si") {
        $varAutoriza = 2;
      }else{
        $varAutoriza = 1;
      }

      $varSusceptible = null;
      $varTextsusceptible = $sheet->getCell("L".$row)->getValue();
      if ($varTextsusceptible == "Si" || $varTextsusceptible == "SI" || $varTextsusceptible == "si") {
        $varSusceptible = 2;
      }else{
        $varSusceptible = 1;
      }

      $paramsClasificacion = [':TextClasificacion' => $sheet->getCell("N".$row)->getValue()];
      $varIdClasificacion = Yii::$app->db->createCommand('
      SELECT if(dc.hv_idclasificacion="",1,dc.hv_idclasificacion) FROM tbl_hojavida_dataclasificacion dc 
        WHERE 
          dc.anulado = 0
            AND dc.ciudadclasificacion IN  (:TextClasificacion)')->bindValues($paramsClasificacion)->queryScalar();

      $varTextAfinidad = null;
      $varAfinidad = $sheet->getCell("O".$row)->getValue();
      if ($varAfinidad == "Relaci0n Directa" ||$varAfinidad == "relacion directa" || $varAfinidad == "Relacion directa") {
        $varTextAfinidad = 1;
      }else{
        $varTextAfinidad = 2;
      }

      $varTextTipoAfinidad = null;
      $varTipoAfinidad = $sheet->getCell("P".$row)->getValue();
      if ($varTipoAfinidad == "Decisor" || $varTipoAfinidad == "decisor") {
        $varTextTipoAfinidad = 1;
      }else{
        $varTextTipoAfinidad = 2;
      }

      $varTextNivelAfinidad = null;
      $varNivelAfinidad = $sheet->getCell("Q".$row)->getValue();
      if ($varNivelAfinidad == "Operativo" || $varNivelAfinidad == "operativo") {
        $varTextNivelAfinidad = 2;
      }else{
        $varTextNivelAfinidad = 1;
      }

      $varDirectores = $sheet->getCell("R".$row)->getValue();
      $varListDirector = explode(", ", $varDirectores);

      $varGerentes = $sheet->getCell("S".$row)->getValue();
      $varListGerentes = explode(", ", $varGerentes);

      $varnombrecliente = $sheet->getCell("T".$row)->getValue();
      if ($varnombrecliente == "Directv") {
        $varIdCliente = "255";
      }else{
        if ($varnombrecliente == "Metro") {
          $varIdCliente = "308";
        }else{
          $paramsCliente = [':TextCliente' => $varnombrecliente];
          $varIdCliente = Yii::$app->db->createCommand('
          SELECT DISTINCT cc.id_dp_clientes, cc.cliente FROM tbl_proceso_cliente_centrocosto cc
            WHERE 
              cc.cliente IN (:TextCliente)')->bindValues($paramsCliente)->queryScalar();
        }        
      }     

      $varPcrcs = $sheet->getCell("U".$row)->getValue();
      $varListPcrcs = explode(", ", $varPcrcs);

      Yii::$app->db->createCommand()->insert('tbl_hojavida_datapersonal',[
                  'nombre_full' => $sheet->getCell("B".$row)->getValue(),
                  'identificacion' => $varDocumento,
                  'email' => $sheet->getCell("C".$row)->getValue(),
                  'numero_movil' => $sheet->getCell("D".$row)->getValue(),
                  'numero_fijo' => $sheet->getCell("E".$row)->getValue(),
                  'direccion_oficina' => $sheet->getCell("I".$row)->getValue(),
                  'direccion_casa' => $sheet->getCell("F".$row)->getValue(),
                  'hv_idpais' => $varIdPais,
                  'hv_idciudad' => $varIdCiudad,
                  'hv_idmodalidad' => $varIdModalidad,
                  'tratamiento_data' => $varAutoriza,
                  'suceptible' => $varSusceptible,
                  'indicador_satu' => null,
                  'clasificacion' => $varIdClasificacion,
                  'fechacreacion' => date('Y-m-d'),
                  'anulado' => 0,
                  'usua_id' => Yii::$app->user->identity->id, 
                  'fechacumple' => null,                                   
              ])->execute();


      $paramsInfoPersonal = [':DocumentoInfo' => $varDocumento];
      $varIdInfoPersonal = Yii::$app->db->createCommand('
      SELECT dp.hv_idpersonal FROM tbl_hojavida_datapersonal dp 
        WHERE 
          dp.identificacion = :DocumentoInfo ')->bindValues($paramsInfoPersonal)->queryScalar();

      $varNA = "Sin datos";

      Yii::$app->db->createCommand()->insert('tbl_hojavida_datalaboral',[
                  'rol' => $varNA,
                  'hv_idpersonal' => $varIdInfoPersonal,
                  'hv_id_antiguedad' => 1,
                  'fecha_inicio_contacto' => null,
                  'nombre_jefe' => $varNA,
                  'cargo_jefe' => $varNA,
                  'trabajo_anterior' => $varNA,
                  'afinidad' => $varTextAfinidad,
                  'tipo_afinidad' => $varTextTipoAfinidad,
                  'nivel_afinidad' => $varTextNivelAfinidad,
                  'fechacreacion' => date('Y-m-d'),
                  'anulado' => 0,
                  'usua_id' => Yii::$app->user->identity->id, 
                  'areatrabajo' => $varNA,                                  
              ])->execute(); 


      $arrayDirectores = count($varListDirector);
      for ($i=0; $i < $arrayDirectores; $i++) { 
        $varDocDirector = $varListDirector[$i];

        Yii::$app->db->createCommand()->insert('tbl_hojavida_datadirector',[
                  'hv_idpersonal' => $varIdInfoPersonal,
                  'ccdirector' => $varDocDirector,
                  'fechacreacion' => date('Y-m-d'),
                  'anulado' => 0,
                  'usua_id' => Yii::$app->user->identity->id,                                       
              ])->execute(); 
      }

      $arrayGerentes = count($varListGerentes);
      for ($i=0; $i < $arrayGerentes; $i++) { 
        $varDocGerente = $varListGerentes[$i];
        
        Yii::$app->db->createCommand()->insert('tbl_hojavida_datagerente',[
                  'hv_idpersonal' => $varIdInfoPersonal,
                  'ccgerente' => $varDocGerente,
                  'fechacreacion' => date('Y-m-d'),
                  'anulado' => 0,
                  'usua_id' => Yii::$app->user->identity->id,                                       
              ])->execute();  
      }

      if(count($varIdCliente) != 0) {
        Yii::$app->db->createCommand()->insert('tbl_hojavida_datapcrc',[
                  'hv_idpersonal' => $varIdInfoPersonal,
                  'id_dp_cliente' => $varIdCliente,
                  'cod_pcrc' => null,
                  'fechacreacion' => date('Y-m-d'),
                  'anulado' => 0,
                  'usua_id' => Yii::$app->user->identity->id,                                       
              ])->execute(); 
      } else{
        #code
    }


      $arrayPcrc = count($varListPcrcs);
      for ($i=0; $i < $arrayPcrc; $i++) { 

        $paramsPcrc = [':varCodPcrcs' => $varListPcrcs[$i]];

        $varCentroCostos = Yii::$app->db->createCommand('
        SELECT DISTINCT if(pc.cod_pcrc = "",NULL,pc.cod_pcrc)  FROM tbl_proceso_cliente_centrocosto pc
          WHERE 
            pc.pcrc IN (:varCodPcrcs)')->bindValues($paramsPcrc)->queryScalar();

        Yii::$app->db->createCommand()->insert('tbl_hojavida_datapcrc',[
                  'hv_idpersonal' => $varIdInfoPersonal,
                  'id_dp_cliente' => $varIdCliente,
                  'cod_pcrc' => $varCentroCostos,
                  'fechacreacion' => date('Y-m-d'),
                  'anulado' => 0,
                  'usua_id' => Yii::$app->user->identity->id,                                       
              ])->execute(); 
      }

    }

  }

}

?>


