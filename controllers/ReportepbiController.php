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
use GuzzleHttp;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7;
use app\models\ReportesAdministracion;
use app\models\ControlProcesosEquipos;


    class ReportepbiController extends \yii\web\Controller {

        public function behaviors(){
            return[
                'access' => [
                        'class' => AccessControl::classname(),
                        'only' => ['reporte', 'get_reports_by_workspace', 'search_report', 'reporteframe', 'crearworkspace', 'delete_workspace', 'alter_report', 'duplicarreporte', 'permisoreporteusua', 'permisosreporte', 'eliminarpermi', 'crearpermiso', 'create_workspace', 'search_workspace_contributors', 'permisocolaborador', 'permisocolabora'],
                        'rules' => [
                            [
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isReportes()  || Yii::$app->user->identity->isVerexterno() || Yii::$app->user->identity->isVerdirectivo();
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
        
    public function actionReporte(){
             
    $model = new ReportesAdministracion();
    $sessiones = Yii::$app->user->identity->id;
    // Obtener un access token de azure AD para consumir API
    $accessToken = $model->getAzureAccessToken();

    if(!isset($accessToken) || !is_string($accessToken) || empty($accessToken)){
      die( json_encode( array("status"=>"0","data"=>"Error al intentar obtener la lista de espacios de trabajo") ) );
    }
    
    // CONSEGUIR LISTA DE TODOS LOS WORKSPACES
    $listWorkspaces = $model->get_list_workspaces($accessToken);
    
    if(!is_array($listWorkspaces)){
      if(is_string($listWorkspaces)){
        die( json_encode( array("status"=>"0","data"=>$listWorkspaces) ) );
      }
      die( json_encode( array("status"=>"0","data"=>"Error al intentar obtener la lista de espacios de trabajo") ) );
    }

    // FILTRAR REPORTES A LOS QUE EL USUARIO TIENE PERMISO
    $listWorkspaces = $model->filter_user_permits('workspace',$listWorkspaces, $sessiones);
   // $listaworkspaces = ['1' => 'workspaces1','2' => 'workspaces2','3' => 'workspaces3','4' => 'workspaces4'];


            return $this->render('reporte', [
               // 'model' => $model,                
                'listaworkspaces' => $listWorkspaces,               
                ]);
    

     }
     public function actionCrearworkspace(){

      return $this->renderAjax('crearworkspace',[

          ]);
      }

      public function actionDuplicarreporte($nombrearea){
      
        $varnombrearea = $nombrearea;
      
        return $this->renderAjax('duplicareportes',[
          'nombrearea' => $varnombrearea,
          ]);
      }
     
      public function actionPermisoreporteusua(){
        $varnombreareat = Yii::$app->request->post("workspace");
        $model = new ControlProcesosEquipos();
        $sessiones = Yii::$app->user->identity->id;
  
        die(json_encode($model));
      }
      public function actionPermisosreporte($model,$workspace, $reporte, $nombrerepor){
        $varnombreareat = $workspace;
        $varreporte = $reporte;
        $varnombrerep = $nombrerepor;
        $model = new ControlProcesosEquipos();
  
        return $this->render('permisosreporte',[
          'model'=>$model, 
          'areatrabajo'=>$varnombreareat, 
          'idreporte'=>$varreporte,
          'nombrerepor'=>$varnombrerep,]);
      }
      public function actionPermisocolabora($dataper,$workspace, $reporte, $nombrerepor){
        $varnombreareat = $workspace;
        $varreporte = $reporte;
        $varnombrerep = $nombrerepor;
        $vardataper = $dataper;
  
        return $this->render('permisocolabora',[
          'dataper'=>$vardataper,
          'areatrabajo'=>$varnombreareat, 
          'idreporte'=>$varreporte,
          'nombrerepor'=>$varnombrerep,
          ]);
      }

      public function actionPermisocolaborador($dataper,$workspace,$nombrerepor){
        $vardataper = (array)json_decode($dataper);
        $varnombreareat = $workspace;
        $varnombrerep = $nombrerepor;
        //$vardataper = $dataper;
  
        return $this->render('permisocolabordor',[
          'dataper'=>$vardataper, 
          'areatrabajo'=>$varnombreareat, 
          'nombrerepor'=>$varnombrerep,]);
      }

      public function actionCrearpermiso(){
        $model = new ControlProcesosEquipos();
        $varusua = Yii::$app->request->post("var_Idusuario");
        $varIdrepor = Yii::$app->request->post("var_Idrepor");
        $varAreatrab = Yii::$app->request->post("var_Areatrab");

        Yii::$app->db->createCommand()->insert('tbl_permisos_reportes_powerbi',[
                            'id_usuario' => $varusua,
                            'id_reporte' => $varIdrepor,
                            'id_workspace' => $varAreatrab,
                        ])->execute(); 

        die(json_encode($model));
    }

      public function actionEliminarpermi(){
        $model = new ControlProcesosEquipos();
        $varusua = Yii::$app->request->post("var_Idusuario");
        $varIdrepor = Yii::$app->request->post("var_Idrepor");
        $varAreatrab = Yii::$app->request->post("var_Areatrab");
        
        Yii::$app->db->createCommand("delete from tbl_permisos_reportes_powerbi where id_usuario = '$varusua' and id_reporte = '$varIdrepor' and id_workspace = '$varAreatrab'")->execute();
       
        $rta = 1;
        die(json_encode($model));
    } 

     public function actionReporteframe(){
      
      $sessiones = Yii::$app->user->identity->id; 
      #$rutaframe = $_POST["rutaframe"];
     // $rutaframe = "https://app.powerbi.com/view?r=eyJrIjoiNzAyMWFjY2QtYTBkMC00ODRlLWJjZjYtNDljODdkMzA3NmQ1IiwidCI6IjhkMGNjZmQzLWZhZDctNDhiNy04MzQ3LWE4NWExMzg2MzBmOSIsImMiOjh9";

      return $this->render('reporteframe',[
        'rutaframe' => $rutaframe,
        ]);
    }

     // FUNCTION GET LIST WORKSPACES
  public function actionGet_list_workspaces(){
    $model = new ReportesAdministracion();
    $sessiones = Yii::$app->user->identity->id;
    // Obtener un access token de azure AD para consumir API
    $accessToken = $model->getAzureAccessToken();

    if(!isset($accessToken) || !is_string($accessToken) || empty($accessToken)){
      die( json_encode( array("status"=>"0","data"=>"Error al intentar obtener la lista de espacios de trabajo") ) );
    }
    
    // CONSEGUIR LISTA DE TODOS LOS WORKSPACES
    $listWorkspaces = $model->get_list_workspaces($accessToken);
    
    if(!is_array($listWorkspaces)){
      if(is_string($listWorkspaces)){
        die( json_encode( array("status"=>"0","data"=>$listWorkspaces) ) );
      }
      die( json_encode( array("status"=>"0","data"=>"Error al intentar obtener la lista de espacios de trabajo") ) );
    }

    // FILTRAR REPORTES A LOS QUE EL USUARIO TIENE PERMISO
    $listWorkspaces = $model->filter_user_permits('workspace',$listWorkspaces,$sessiones);

    die( json_encode( array("status"=>"1","data"=>$listWorkspaces) ) );
  }
  // FUNCTION GET LIST WORKSPACES END


  // FUNCTION CREATE WORKSPACE
  public function actionCreate_workspace(){
 //   helper_check_permit( 88 );
    $model = new ReportesAdministracion();
    $sessiones = Yii::$app->user->identity->id;
    $workspace_name = $_POST["workspace_name"];

   // $workspace_name = $_POST["workspace_name"];
    
    // Obtener un access token de azure AD para consumir API
    $accessToken = $model->getAzureAccessToken();

    if(!isset($accessToken) || !is_string($accessToken) || empty($accessToken)){
      die( json_encode( array("status"=>"0","data"=>"No se ha logrado autenticar con Azure AD. Contacte un administrador") ) );
    }

    $result = $model->create_workspace($accessToken, $workspace_name);
    $res= 0;
    if($result !== TRUE){
      //die( json_encode( array("status"=>"0","data"=>"No se ha logrado autenticar con Azure AD. Contacte un administrador") ) );
      die( json_encode( $res));
    }
    
    //$log_data = array( "id_logs_grupos"=> 75 , "id_usuario" => $sessiones, "datos" => json_encode(array("Nombre del workspace"=>$workspace_name)),  "ip" => $_SERVER["REMOTE_ADDR"] );
    //@add_log($log_data);
    //die( json_encode( array("status"=>"1","data"=>"Area de trabajo creada correctamente") ) );
    $res=1;
    die( json_encode( $res));
  }
  // FUNCTION CREATE WORKSPACE END


  // FUNCTION GET REPORTS BY WORKSPACE
  public function actionGet_reports_by_workspace(){
    $model = new ReportesAdministracion();
    $sessiones = Yii::$app->user->identity->id;
    // Obtener el ID del workspace para consumir sus reportes
    $workspace_id = $_POST["workspace_id"];
    // Inicializar access token
    $accessToken = "";

    // Validar ID del workspace indicado por el cliente
    if(!isset($workspace_id) || empty($workspace_id)){
      json_encode( array("status"=>"0","data"=>"Workspace no especificado") );
    }

    // Obtener un access token de azure AD para consumir API
    $accessToken = $model->getAzureAccessToken();

    if(!isset($accessToken) || !is_string($accessToken) || empty($accessToken)){
      json_encode( array("status"=>"0","data"=>"No se ha logrado autenticar con Azure AD. Contacte un administrado") );
    }

    // Obtener reportes
    $reports = $model->get_reports_by_workspace($accessToken, $workspace_id);
    
    // FILTRAR REPORTES A LOS QUE EL USUARIO TIENE PERMISO
    $reports = $model->filter_user_permits('report',$reports,$sessiones); 
   /* $reporte1 = $workspace_id . "rep1";
   $reporte2 = $workspace_id . "rep2";
    
    $reports = [$reporte1, $reporte2];*/
    

    die(json_encode( array("status"=>"1","data"=>$reports) ));

  }
  // FUNCTION GET REPORTS BY WORKSPACE END


  // FUNCTION SEARCH AND SHOW A REPORT
  public function actionSearch_report(){
    $model = new ReportesAdministracion();
    // Obtener el ID del workspace para consumir sus reportes
    $report_id = $_POST["report_id"];
    $workspace_id = $_POST["workspace_id"];

    // Validar ID del workspace indicado por el cliente
    if(!isset($workspace_id) || empty($workspace_id)){
      die( json_encode( array("status"=>"0","data"=>"No se especifico un ID de workspace") ) );
    }
    // Validar ID del reporte indicado por el cliente
    if(!isset($report_id) || empty($report_id)){
      die( json_encode( array("status"=>"0","data"=>"No se especifico un ID de reporte") ) );
    }

    // Obtener un access token de azure AD para consumir API
    $accessToken = $model->getAzureAccessToken();

    if(!isset($accessToken) || !is_string($accessToken) || empty($accessToken)){
      die( json_encode( array("status"=>"0","No se ha logrado autenticar con Azure AD. Contacte un administrador") ) );
    }
    
    // Obtener embed token
    $result = $model->search_report($accessToken, $workspace_id, $report_id);
    die( json_encode( array("status"=>"1","data"=>$result) ) );
  /*  return $this->render('reporteframe',[
      'rutaframe' => $$result,
      ]);*/

  }
 // FUNCTION SEARCH AND SHOW A REPORTEND

  //  FUNCTION SEARCH USER PERMITS
  public function search_user_permits (){
    $model = new ReportesAdministracion();
    $reporte = $_POST["reporte"];

    $get_users = $model->search_user_permits($reporte);
    if(!$get_users){
      die( json_encode( array("status"=>"0","data"=>"Error al buscar los usuarios") ) );
    }
    die( json_encode( array("status"=>"1","data"=>$get_users->result_array()) ) );
  }
  //  FUNCTION SEARCH USER PERMITS END

  // FUNCTION SAVE REPORT PERMITS BY USER
  public function save_report_user_permits (){
    $sessiones = Yii::$app->user->identity->id;

    $list_users = json_decode(($_POST["list_users"]),true);
    $reporte = $_POST["reporte"];
    $workspace = $_POST["workspace"];
    $id_users = array_column($list_users,"id_usuario");

    $save_permits = $model->save_report_user_permits($list_users,$reporte,$workspace);
    if(!$save_permits){
      die( json_encode( array("status"=>"0","data"=>"Error al guardar los permisos") ) );
    }
    $log_data = array( 
      "id_logs_grupos"=> 74,"id_usuario" => $sessiones,"ip" => $_SERVER["REMOTE_ADDR"], 
      "datos" => json_encode( 
        array(
          "id_usuarios"=>$id_users,
          "reporte"=>array("name"=>$reporte["name"],"id"=>$reporte["id"]),
          "workspace"=>array("name"=>$workspace["name"],"id"=>$workspace["id"])  
        )  
      )
    );
    @add_log($log_data);
    die( json_encode( array("status"=>"1","data"=>"Permisos guardados correctamente") ) );

  }
  // FUNCTION SAVE REPORT PERMITS BY USER END


  // FUNCTION ALTER REPORT
  public function actionAlter_report (){
    $model = new ReportesAdministracion();
    $sessiones = Yii::$app->user->identity->id;
   // helper_check_permit( 88 );
    // TIPO 1: ELIMINAR REPORTE
    // TIPO 2: DUPLICAR REPORTE EN UN AREA DE TRABAJO
    $tipo = $_POST["tipo"];
    $reporte = $_POST["reporte"];
    $workspace = $_POST["workspace"];
    $new_name_report = $_POST["new_name_report"];
    $info_log["id_user"] = $sessiones;

    // Obtener un access token de azure AD para consumir API
    $accessToken = $model->getAzureAccessToken();

    if(!isset($accessToken) || !is_string($accessToken) || empty($accessToken)){
      die( json_encode( array("status"=>"0","No se ha logrado autenticar con Azure AD. Contacte un administrador") ) );
    }

    if($tipo == 1){ // ELIMINAR REPORTE
      //$info_log["id_logs_grupo"]  = 71; // LOG GRUPO
      //$info_log["data"] = array("workspace"=> array("nombre"=>$workspace["name"],"id"=>$workspace["id"]),"reporte"=>array("name"=>$reporte["name"],"id"=>$reporte["id"]));
      $alter_report = $model->delete_report($reporte,$workspace,$accessToken);
    }
    if($tipo == 2){ // DUPLICAR REPORTE EN UN AREA DE TRABAJO
      //$info_log["id_logs_grupo"]  = 73; // LOG GRUPO
      //$info_log["data"] = array("workspace"=>array("nombre"=>$workspace["name"],"id"=>$workspace["id"]),"reporte"=>array("name"=>$reporte["name"],"id"=>$reporte["id"]),"Nombre_del_duplicado"=>$new_name_report);
      $alter_report = $model->duplicate_report($reporte,$workspace,$new_name_report,$accessToken);
    }

    if(!$alter_report){
      die( json_encode( array("status"=>"0","data"=>"Error al ejecutar esta accion") ) );
    }
    else{
      $log_data = array( "id_logs_grupos"=> $info_log["id_logs_grupo"] , "id_usuario" => $info_log["id_user"], "datos" => json_encode( $info_log["data"] ),  "ip" => $_SERVER["REMOTE_ADDR"] );
      @add_log($log_data);
      die( json_encode( array("status"=>"1","data"=>"Accion ejecutada correctamente") ) );
    }

  }
  // FUNCTION ALTER REPORT END

  // FUNCTION DELETE WORKSPACE
  public function actionDelete_workspace (){
    $model = new ReportesAdministracion();
    $sessiones = Yii::$app->user->identity->id;
   // helper_check_permit( 88 );   
    $workspace = $_POST["workspace"];
    //$workspace = $_POST["workspace"];
    // Obtener un access token de azure AD para consumir API

    $accessToken = $model->getAzureAccessToken();
    $res=0;
    $delete_workspace = $model->delete_workspace($workspace,$accessToken);
    if(!$delete_workspace){
      //die(json_encode( array("status"=>"0","data"=>"Error al eliminar este workspace") ));
      die(json_encode($res));
    }

    ////$log_data = array( "id_logs_grupos"=> 72 , "id_usuario" => $sessiones, "datos" => json_encode( array("name"=>$workspace["name"],"id"=>$workspace["id"] ) ),  "ip" => $_SERVER["REMOTE_ADDR"] );
    ////@add_log($log_data);

    //die( json_encode( array("status"=>"1","data"=>"Area de trabajo eliminada correctamente") ) );
    $res=1;
    die( json_encode($res) );
  }
  // FUNCTION DELETE WORKSPACE END

  // FUNCTION SEARCH COLABORADORES WORKSPACE
  public function actionSearch_workspace_contributors (){
   // helper_check_permit( 88 );
   // $workspace = $this->input->post("workspace",true);
    $model = new ReportesAdministracion();
    $sessiones = Yii::$app->user->identity->id;
    $workspace = $_POST["workspace"];
    //$accessToken = $this->reportes_administracion_model->getAzureAccessToken();
    $accessToken = $model->getAzureAccessToken();

   $search_workspace_contributors = $model->search_workspace_contributors($workspace,$accessToken);
    if(!$search_workspace_contributors){
      die(json_encode( array("status"=>"0","data"=>"Error al buscar la lista de colaboradores en este workspace") ));
    }
    die( json_encode($search_workspace_contributors));
  }
  // FUNCTION SEARCH COLABORADORES WORKSPACE END

  // FUNCTION DELETE COLABORATOR WORKSPACE
  public function actionDelete_workspace_colaborator (){
    //helper_check_permit( 88 );
    $model = new ReportesAdministracion();
    $sessiones = Yii::$app->user->identity->id;

    //$workspace = $this->input->post("workspace",true);
    //$colaborator = $this->input->post("colaborator",true);    
    $workspace = $_POST["workspace"];    
    $colaborator = $_POST["colaborator"];

    $accessToken = $model->getAzureAccessToken();
    //$accessToken = $this->reportes_administracion_model->getAzureAccessToken();
    
    //$delete_workspace_colaborator = $this->reportes_administracion_model->delete_workspace_colaborator($workspace,$accessToken,$colaborator);
    $delete_workspace_colaborator = $model->delete_workspace_colaborator($workspace,$accessToken,$colaborator);
    $res=0;
    if(!$delete_workspace_colaborator){
      //die(json_encode( array("status"=>"0","data"=>"Error al intentar eliminar este colaborador") ));
      die(json_encode( $res));
    }

   // $log_data = array( "id_logs_grupos"=> 76 , "id_usuario" => $this->session->userdata("id"), "datos" => json_encode( array("workspace"=>$workspace,"colaborator"=>$colaborator) ),  "ip" => $_SERVER["REMOTE_ADDR"] );
    //@add_log($log_data);
    $res=1;    
    //die( json_encode(array( "status"=>"1","data"=>"Colaborador eliminado correctamente" )) );
    die(json_encode($delete_workspace_colaborator));

  }
  // FUNCTION DELETE COLABORATOR WORKSPACE END

  // FUNCTION ADD WORKSPACE COLABORATOR
  public function actionAdd_workspace_colaborator(){
    //helper_check_permit( 88 );

    /*$colaborator = $this->input->post("colaborator",true);
    $workspace = $this->input->post("workspace",true);
    $accessToken = $this->reportes_administracion_model->getAzureAccessToken();*/
    $model = new ReportesAdministracion();
    $sessiones = Yii::$app->user->identity->id;

    //$workspace = $this->input->post("workspace",true);
    //$colaborator = $this->input->post("colaborator",true);    
    $workspace = $_POST["workspace"];
    //$workspace = '0332fe05-1dc3-4b06-8060-5b93ac326dff';    
    $colaborator = $_POST["colaborator"];
    //$colaborator = 'rdfigueroao@grupokonecta.com.co';
    $accessToken = $model->getAzureAccessToken();

    //$add_workspace_colaborator = $this->reportes_administracion_model->add_workspace_colaborator($workspace,$accessToken,$colaborator);
    $add_workspace_colaborator = $model->add_workspace_colaborator($workspace,$accessToken,$colaborator);
    $res=0;
    if(!$add_workspace_colaborator){
      //die(json_encode( array("status"=>"0","data"=>"Error al intentar agregar este colaborador") ));
      die(json_encode( $res));
    }

    //$log_data = array( "id_logs_grupos"=> 77 , "id_usuario" => $this->session->userdata("id"), "datos" => json_encode( array("workspace"=>$workspace,"colaborator"=>$colaborator) ),  "ip" => $_SERVER["REMOTE_ADDR"] );
    //@add_log($log_data);
    $res=1;
    //die( json_encode(array( "status"=>"1","data"=>"Colaborador agregado correctamente" )) );
    die(json_encode($add_workspace_colaborator));

  }
  // FUNCTION ADD WORKSPACE COLABORATOR END
   
}

?>