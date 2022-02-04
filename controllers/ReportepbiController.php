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
        
    public function actionReporte(){
             
    $model = new ReportesAdministracion();
    $sessiones = Yii::$app->user->identity->id;
    // Obtener un access token de azure AD para consumir API
    $accessToken = $model->getAzureAccessToken();

    if(!isset($accessToken) || !is_string($accessToken) || empty($accessToken)){
      die( json_encode( array("status"=>"0","data"=>"Error al intentar obtener la lista de espacios de trabajo") ) );
    }else{
      #code
    }
    
    // CONSEGUIR LISTA DE TODOS LOS WORKSPACES
    $listWorkspaces = $model->get_list_workspaces($accessToken);
    
    if(!is_array($listWorkspaces)){
      if(is_string($listWorkspaces)){
        die( json_encode( array("status"=>"0","data"=>$listWorkspaces) ) );
      }
      die( json_encode( array("status"=>"0","data"=>"Error al intentar obtener la lista de espacios de trabajo") ) );
    }else{
      #code
    }

    // FILTRAR REPORTES A LOS QUE EL USUARIO TIENE PERMISO
    $listWorkspaces = $model->filter_user_permits('workspace',$listWorkspaces, $sessiones);


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
        $model = new ControlProcesosEquipos();
  
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
        
        Yii::$app->db->createCommand("delete from tbl_permisos_reportes_powerbi where id_usuario = ':varusua' and id_reporte = ':varIdrepor' and id_workspace = ':varAreatrab'")
        ->bindValue(':varusua', $varusua)
        ->bindValue(':varIdrepor', $varIdrepor)
        ->bindValue(':varAreatrab', $varAreatrab)
        ->execute();
       
        die(json_encode($model));
    } 

     public function actionReporteframe(){
      
      $rutaframe = Yii::$app->request->post("rutaframe");

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
    }else{
      #code
    }
    
    // CONSEGUIR LISTA DE TODOS LOS WORKSPACES
    $listWorkspaces = $model->get_list_workspaces($accessToken);
    
    if(!is_array($listWorkspaces)){
      if(is_string($listWorkspaces)){
        die( json_encode( array("status"=>"0","data"=>$listWorkspaces) ) );
      }else{
        #code
      }
      die( json_encode( array("status"=>"0","data"=>"Error al intentar obtener la lista de espacios de trabajo") ) );
    }else{
      #code
    }

    // FILTRAR REPORTES A LOS QUE EL USUARIO TIENE PERMISO
    $listWorkspaces = $model->filter_user_permits('workspace',$listWorkspaces,$sessiones);

    die( json_encode( array("status"=>"1","data"=>$listWorkspaces) ) );
  }
  // FUNCTION GET LIST WORKSPACES END


  // FUNCTION CREATE WORKSPACE
  public function actionCreate_workspace(){
    $model = new ReportesAdministracion();
    $workspace_name = Yii::$app->request->post("workspace_name");

    
    // Obtener un access token de azure AD para consumir API
    $accessToken = $model->getAzureAccessToken();

    if(!isset($accessToken) || !is_string($accessToken) || empty($accessToken)){
      die( json_encode( array("status"=>"0","data"=>"No se ha logrado autenticar con Azure AD. Contacte un administrador") ) );
    }else{
      #code
    }

    $result = $model->create_workspace($accessToken, $workspace_name);
    $res= 0;
    if($result !== TRUE){
      die( json_encode( $res));
    }else{
      #code
    }
    
    $res=1;
    die( json_encode( $res));
  }
  // FUNCTION CREATE WORKSPACE END


  // FUNCTION GET REPORTS BY WORKSPACE
  public function actionGet_reports_by_workspace(){
    $model = new ReportesAdministracion();
    $sessiones = Yii::$app->user->identity->id;
    // Obtener el ID del workspace para consumir sus reportes
    $workspace_id = Yii::$app->request->post("workspace_id");
    // Inicializar access token
    $accessToken = "";

    // Validar ID del workspace indicado por el cliente
    if(!isset($workspace_id) || empty($workspace_id)){
      json_encode( array("status"=>"0","data"=>"Workspace no especificado") );
    }else{
      #code
    }

    // Obtener un access token de azure AD para consumir API
    $accessToken = $model->getAzureAccessToken();

    if(!isset($accessToken) || !is_string($accessToken) || empty($accessToken)){
      json_encode( array("status"=>"0","data"=>"No se ha logrado autenticar con Azure AD. Contacte un administrado") );
    }else{
      #code
    }

    // Obtener reportes
    $reports = $model->get_reports_by_workspace($accessToken, $workspace_id);
    
    // FILTRAR REPORTES A LOS QUE EL USUARIO TIENE PERMISO
    $reports = $model->filter_user_permits('report',$reports,$sessiones); 
    

    die(json_encode( array("status"=>"1","data"=>$reports) ));

  }
  // FUNCTION GET REPORTS BY WORKSPACE END


  // FUNCTION SEARCH AND SHOW A REPORT
  public function actionSearch_report(){
    $model = new ReportesAdministracion();
    // Obtener el ID del workspace para consumir sus reportes
    $report_id = Yii::$app->request->post("report_id");
    $workspace_id = Yii::$app->request->post("workspace_id");

    // Validar ID del workspace indicado por el cliente
    if(!isset($workspace_id) || empty($workspace_id)){
      die( json_encode( array("status"=>"0","data"=>"No se especifico un ID de workspace") ) );
    }else{
      #code
    }
    // Validar ID del reporte indicado por el cliente
    if(!isset($report_id) || empty($report_id)){
      die( json_encode( array("status"=>"0","data"=>"No se especifico un ID de reporte") ) );
    }else{
      #code
    }

    // Obtener un access token de azure AD para consumir API
    $accessToken = $model->getAzureAccessToken();

    if(!isset($accessToken) || !is_string($accessToken) || empty($accessToken)){
      die( json_encode( array("status"=>"0","No se ha logrado autenticar con Azure AD. Contacte un administrador") ) );
    }else{
      #code
    }
    
    // Obtener embed token
    $result = $model->search_report($accessToken, $workspace_id, $report_id);
    die( json_encode( array("status"=>"1","data"=>$result) ) );

  }
 // FUNCTION SEARCH AND SHOW A REPORTEND

  //  FUNCTION SEARCH USER PERMITS
  public function search_user_permits (){
    $model = new ReportesAdministracion();
    $reporte = Yii::$app->request->post("reporte");

    $get_users = $model->search_user_permits($reporte);
    if(!$get_users){
      die( json_encode( array("status"=>"0","data"=>"Error al buscar los usuarios") ) );
    }else{
      #code
    }
    die( json_encode( array("status"=>"1","data"=>$get_users->result_array()) ) );
  }
  //  FUNCTION SEARCH USER PERMITS END

  // FUNCTION SAVE REPORT PERMITS BY USER
  public function save_report_user_permits (){
    $model = new ReportesAdministracion();
    $sessiones = Yii::$app->user->identity->id;
    foreach ($_SERVER as $key => $value)
    {
        $_SERVER[$key] = filter_input(INPUT_SERVER, $key, FILTER_SANITIZE_STRING);
    }

    $list_users = json_decode((Yii::$app->request->post("list_users")),true);
    $reporte = Yii::$app->request->post("reporte");
    $workspace = Yii::$app->request->post("workspace");
    $id_users = array_column($list_users,"id_usuario");

    $save_permits = $model->save_report_user_permits($list_users,$reporte,$workspace);
    if(!$save_permits){
      die( json_encode( array("status"=>"0","data"=>"Error al guardar los permisos") ) );
    }else{
      #code
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
    add_log($log_data);
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
    $tipo = Yii::$app->request->post("tipo");
    $reporte = Yii::$app->request->post("reporte");
    $workspace = Yii::$app->request->post("workspace");
    $new_name_report = Yii::$app->request->post("new_name_report");
    $info_log["id_user"] = $sessiones;

    // Obtener un access token de azure AD para consumir API
    $accessToken = $model->getAzureAccessToken();

    if(!isset($accessToken) || !is_string($accessToken) || empty($accessToken)){
      die( json_encode( array("status"=>"0","No se ha logrado autenticar con Azure AD. Contacte un administrador") ) );
    }else{
      #code
    }

    if($tipo == 1){ // ELIMINAR REPORTE
      //$info_log["id_logs_grupo"]  = 71; // LOG GRUPO
      //$info_log["data"] = array("workspace"=> array("nombre"=>$workspace["name"],"id"=>$workspace["id"]),"reporte"=>array("name"=>$reporte["name"],"id"=>$reporte["id"]));
      $alter_report = $model->delete_report($reporte,$workspace,$accessToken);
    }else{
      #code
    }
    if($tipo == 2){ // DUPLICAR REPORTE EN UN AREA DE TRABAJO
      //$info_log["id_logs_grupo"]  = 73; // LOG GRUPO
      //$info_log["data"] = array("workspace"=>array("nombre"=>$workspace["name"],"id"=>$workspace["id"]),"reporte"=>array("name"=>$reporte["name"],"id"=>$reporte["id"]),"Nombre_del_duplicado"=>$new_name_report);
      $alter_report = $model->duplicate_report($reporte,$workspace,$new_name_report,$accessToken);
    }else{
      #code
    }

    if(!$alter_report){
      die( json_encode( array("status"=>"0","data"=>"Error al ejecutar esta accion") ) );
    }
    else{
      foreach ($_SERVER as $key => $value)
      {
          $_SERVER[$key] = filter_input(INPUT_SERVER, $key, FILTER_SANITIZE_STRING);
      }
      $log_data = array( "id_logs_grupos"=> $info_log["id_logs_grupo"] , "id_usuario" => $info_log["id_user"], "datos" => json_encode( $info_log["data"] ),  "ip" => $_SERVER["REMOTE_ADDR"] );
      add_log($log_data);
      die( json_encode( array("status"=>"1","data"=>"Accion ejecutada correctamente") ) );
    }

  }
  // FUNCTION ALTER REPORT END

  // FUNCTION DELETE WORKSPACE
  public function actionDelete_workspace (){
    $model = new ReportesAdministracion();
    $workspace = Yii::$app->request->post("workspace");
    // Obtener un access token de azure AD para consumir API

    $accessToken = $model->getAzureAccessToken();
    $res=0;
    $delete_workspace = $model->delete_workspace($workspace,$accessToken);
    if(!$delete_workspace){
      die(json_encode($res));
    }
    else{
      #code
    }
    $res=1;
    die( json_encode($res) );
  }
  // FUNCTION DELETE WORKSPACE END

  // FUNCTION SEARCH COLABORADORES WORKSPACE
  public function actionSearch_workspace_contributors (){
    $model = new ReportesAdministracion();
    $workspace = Yii::$app->request->post("workspace");
    $accessToken = $model->getAzureAccessToken();

   $search_workspace_contributors = $model->search_workspace_contributors($workspace,$accessToken);
    if(!$search_workspace_contributors){
      die(json_encode( array("status"=>"0","data"=>"Error al buscar la lista de colaboradores en este workspace") ));
    }else{
      #code
    }
    die( json_encode($search_workspace_contributors));
  }
  // FUNCTION SEARCH COLABORADORES WORKSPACE END

  // FUNCTION DELETE COLABORATOR WORKSPACE
  public function actionDelete_workspace_colaborator (){
    $model = new ReportesAdministracion();

    $workspace = Yii::$app->request->post("workspace");
    $colaborator = Yii::$app->request->post("colaborator");

    $accessToken = $model->getAzureAccessToken();
    $delete_workspace_colaborator = $model->delete_workspace_colaborator($workspace,$accessToken,$colaborator);
    $res=0;
    if(!$delete_workspace_colaborator){
      die(json_encode( $res));
    }else{
      #code
    }

    $res=1;    
    die(json_encode($delete_workspace_colaborator));

  }
  // FUNCTION DELETE COLABORATOR WORKSPACE END

  // FUNCTION ADD WORKSPACE COLABORATOR
  public function actionAdd_workspace_colaborator(){
    $model = new ReportesAdministracion();

    $workspace = Yii::$app->request->post("workspace");
    $colaborator = Yii::$app->request->post("colaborator");
    $accessToken = $model->getAzureAccessToken();

    $add_workspace_colaborator = $model->add_workspace_colaborator($workspace,$accessToken,$colaborator);
    $res=0;
    if(!$add_workspace_colaborator){
      die(json_encode( $res));
    }else{
      #code
    }

    $res=1;
    die(json_encode($add_workspace_colaborator));

  }
  // FUNCTION ADD WORKSPACE COLABORATOR END
   
}

?>