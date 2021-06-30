<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\Url;
use GuzzleHttp;

/**
 * ControlProcesosEquipos represents the model behind the search form about `app\models\ControlvocBloque1`.
 */
class ReportesAdministracion extends Model
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_permisos_reportes_powerbi';
    }

    public function rules()
    {
        return [
             [['id_usuario'], 'integer'],
              [['id_reporte', 'id_workspace'], 'string', 'max' => 80],
            // [['evaluados_id', 'salario', 'tipo_corte'], 'safe'],
           // [],
           // [['fechacreacion', 'valorador_id', 'arbol_id', 'dimensions', 'lider_id', 'tecnico_id', 'numidextsp', 'usuagente', 'duracion','extencion'], 'safe'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id_usuario' => Yii::t('app', ''),
            'id_reporte' => Yii::t('app', ''),
            'id_workspace' => Yii::t('app', ''),
        ];
    }


    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }


    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    // Obtener un access token de AD Azure
  public function getAzureAccessToken(){

    $txtRta = Yii::$app->db->createCommand("select azure_content from tbl_config_powerbi")->queryAll();

    $arrayazure = array();
    foreach ($txtRta as $key => $value) {
             array_push($arrayazure, $value['azure_content']);
    }    

    $tenant_id = $arrayazure[0];
    $client_id = $arrayazure[1];
    $client_secret = $arrayazure[2];
    $resource = $arrayazure[3];

    $client = new GuzzleHttp\Client([
        'verify' => false
    ]);

    try{

      $res = $client->request(
        'POST', 'https://login.windows.net/' . $tenant_id .'/oauth2/token', 
        [
          'form_params' => [
              'grant_type' => 'client_credentials',
              'resource' => $resource,
              'client_id' => $client_id,
              'client_secret' => $client_secret
            ]
        ]
      );
              
      // Parsear respuesta a JSON
      $response = json_decode((string)$res->getBody());

      // Validar si existe la propiedad access token en la respuesta
      if(!property_exists($response,'access_token')){
          return FALSE;
      }
      // Retornar el access token de la respuesta
      return $response->access_token;
    }
    catch(Exception $e){
        return FALSE;
    }

  }
  // Obtener un access token de AD Azure END

  // Obtener la lista de workspaces
  public function get_list_workspaces($accessToken = null){

    if(!isset($accessToken) || !is_string($accessToken) || empty($accessToken)){
      return 'No se ha logrado autenticar con Azure AD. Contacte un administrador';
    }

    $client = new GuzzleHttp\Client([
      'verify' => false
    ]);

    try{

      $res = $client->request('GET', 'https://api.powerbi.com/v1.0/myorg/groups', [
        'headers' => [
          'Authorization' => 'Bearer ' . $accessToken
          ]
        ]);
              
      // Parsear respuesta a JSON
      $response = json_decode((string)$res->getBody());

      // Validar si existe la propiedad access token en la respuesta
      if(!property_exists($response,'value')){
        return 'No se ha podido obtener la lista de espacios de workspaces. Contacte un administrador !';
      }
      // Retornar el access token de la respuesta
      return $response->value;
    }
    catch(Exception $e){
        return 'No se ha podido obtener la lista de espacios de workspaces. Contacte un administrador !';
    }

  }
  // Obtener la lista de workspaces end

  // Crear nueva  rea de trabajo
  public function create_workspace($accessToken = null, $workspace_name = null){

    if( !isset($accessToken) || empty($accessToken) || !isset($workspace_name) || empty($workspace_name)){
        return FALSE;
    }

    $client = new GuzzleHttp\Client([
      'verify' => false
    ]);

    try{

      $res = $client->request('POST', 'https://api.powerbi.com/v1.0/myorg/groups?workspaceV2=true', [
        'headers' => [
          'Authorization' => 'Bearer ' . $accessToken
        ],
        'form_params' => [
          'name' => $workspace_name
        ]
      ]);
      
      // Retornar el access token de la respuesta
      return TRUE;
    }
    catch(Exception $e){
      return FALSE;
    }

  }
  // Crear nueva area de trabajo end

  // Obtener la lista de reportes de un workspace
  public function get_reports_by_workspace($accessToken = null, $workspace_id = null){

    $client = new GuzzleHttp\Client([
      'verify' => false
    ]);

    try{

      $res = $client->request('GET', 'https://api.powerbi.com/v1.0/myorg/groups/'. $workspace_id .'/reports', [
        'headers' => [
          'Authorization' => 'Bearer ' . $accessToken
        ]
      ]);
              
      // Parsear respuesta a JSON
      $response = json_decode((string)$res->getBody());

      // Validar si existe la propiedad access token en la respuesta
      if(!property_exists($response,'value')){
          return 'No se ha podido obtener la lista de reportes asociados al grupo de trabajo !';
      }
      // Retornar el access token de la respuesta
      return $response->value;
    }
    catch(Exception $e){
      return 'No se ha podido obtener la lista de reportes asociados al grupo de trabajo !';
    }

  }
  // Obtener la lista de reportes de un workspace

  // BUSCAR UN EMBED TOKEN PARA CONSULTAR UN REPORTE
  public function search_report($accessToken = null, $workspace_id = null, $report_id = null){

    if(!isset($accessToken) || empty($accessToken)){
      return 'Access token no valido';
    }
        if(!isset($workspace_id) || empty($workspace_id)){
      return 'Espacio de trabajo no valido';
    }
    if(!isset($report_id) || empty($report_id)){
      return 'Reporte no valido';
    }

    $txtRta = Yii::$app->db->createCommand("select azure_content from tbl_config_powerbi")->queryAll();

    $arrayazure = array();
    foreach ($txtRta as $key => $value) {
             array_push($arrayazure, $value['azure_content']);
    }    

    $tenant_id = $arrayazure[0];
    $client_id = $arrayazure[1];
    $client_secret = $arrayazure[2];
    $resource = $arrayazure[3];
    $azure_powerbi_api_url =$arrayazure[4];

    $client = new GuzzleHttp\Client([
      'verify' => false
    ]);

    try{

      $res = $client->request('POST', $azure_powerbi_api_url . '/v1.0/myorg/groups/'. $workspace_id .'/reports/'. $report_id .'/GenerateToken', [
        'headers' => [
          'Authorization' => 'Bearer ' . $accessToken,
          'Content-Type' => 'application/json'
        ],
        'json' => [
          'accessLevel' => 'view'
        ]
      ]);
              
      // Parsear respuesta a JSON
      $response = json_decode((string)$res->getBody());

      // Validar si existe la propiedad access token en la respuesta
      if(!property_exists($response,'token')){
        return 'No se ha podido obtener acceso al reporte !';
      }
      // Retornar el access token de la respuesta
      return $response->token;
    }
    catch(Exception $e){
      return 'No se ha podido obtener acceso al reporte !';
    }

  }
  // BUSCAR UN EMBED TOKEN PARA CONSULTAR UN REPORT END

  //  FUNCTION SEARCH USER PERMITS
  public function search_user_permits ($reporte){
    
  $id_reporte = $reporte['id'];
   $get_users = Yii::$app->db->createCommand("select 
        a.usua_id,
        UPPER(a.usua_nombre) AS nombre,
        a.usua_identificacion,
        a.usua_usuario,
        b.id_reporte,
        SUM(IF(b.id_reporte = '$id_reporte',1,0)) AS tiene_permiso

        FROM tbl_usuarios a

        LEFT JOIN tbl_permisos_reportes_powerbi b
        ON a.usua_id = b.id_usuario

        WHERE a.usua_activo = 'S'

        GROUP BY a.usua_id")->queryAll();
   
    return $get_users;
  }
  //  FUNCTION SEARCH USER PERMITS END

  // FUNCTION SAVE REPORT PERMITS BY USER
  public function save_report_user_permits ($list_users,$reporte,$workspace){
    
    // BORRAR TODOS LOS PERMISOS DE UN ID DE REPORTE
    $this->db->where('id_reporte', $reporte["id"]);
    $this->db->where("id_workspace",$workspace["id"]);
    $delete_permits = $this->db->delete("permisos_reportes_powerbi");
    if(!$delete_permits){
      return false;
    }

    // INSERTAR LOS NUEVOS PERMISOS
    if(count($list_users) > 0 ){
      $insert_permits = $this->db->insert_batch("permisos_reportes_powerbi",$list_users);
      if(!$insert_permits){
        return false;
      }
    }
    return true;

  }
  // FUNCTION SAVE REPORT PERMITS BY USER END

  // FUNCTION FILTER USER PERMITS
  public function filter_user_permits ($type,$data, $sessiones){
    //TYPE permitidos:
    // WORKSPACE
    // REPORT

    //$id_user = $this->session->userdata("id");
    $id_user = $sessiones;
    /*$admin_permit = Yii::$app->db->createCommand("select azure_content from tbl_config_powerbi")->queryAll();
    $admin_permit = $this->db->get_where('permisos_usuarios', array('permiso' => 88,"id_usuario"=>$id_user));
    if(!$admin_permit){
      die( json_encode( array("status"=>"0","data"=>"Error al buscar los permisos del usuario") ) );
    }
    
    // SI TIENE PERMISO ADMINISTRADOR, SE MOSTRARAN TODOS LOS WORKSPACES / REPORTES
    if($admin_permit->num_rows() > 0 ){*/
      //if($id_user == 3205 || $id_user == 2953 || $id_user == 7 || $id_user == 69 || $id_user == 2915 || $id_user == 8 || $id_user == 3468){
      if($id_user == 3205 || $id_user == 2953 || $id_user == 3468 || $id_user == 69 || $id_user == 8 || $id_user == 57 || $id_user == 3229 || $id_user == 1515 || $id_user == 2991 || $id_user == 6636 || $id_user == 6639){ 
      return $data;
    }
    else{
      // SI NO ES ADMINISTRADOR, SE MOSTRARAN SOLAMENTE LOS WORKSPACES / REPORTES A LOS QUE TIENE PERMISO

      // ARRAY FINAL (WORKSPACES / REPORTES ) QUE EL USUARIO PODRA VISUALIZAR
      $final_data = array();

      // BUSCAR LOS WORKSPACE Y REPORTES QUE EL USUARIO TIENE PERMISO ACTUALMENTE
      /*$this->db->select("id_usuario,id_reporte,id_workspace");
      $this->db->where("id_usuario",$id_user);
      $reports_permits = $this->db->get("permisos_reportes_powerbi");*/
      $reports_permits = Yii::$app->db->createCommand("select id_usuario,id_reporte,id_workspace from tbl_permisos_reportes_powerbi where id_usuario = $id_user")->queryAll();
      if(!$reports_permits){
        die( json_encode( array("status"=>"0","data"=>"Error al buscar los permisos del usuario ".$id_user) ) );
      }
      /*
      $reports_permits = $reports_permits->result_array();

      if($type == "workspace"){
        $reports_permits = array_column($reports_permits,"id_workspace");
      }
      if($type == "report"){
        $reports_permits = array_column($reports_permits,"id_reporte");
      }
      $data = array_values($data);
      for($i = 0; $i < count($data); $i++ ){
        $data[$i] = (array)$data[$i];
        if( in_array($data[$i]["id"], $reports_permits ) ){
          array_push($final_data,$data[$i]);
        }
      }
      return $final_data;*/
      if($type == "workspace"){
        foreach ($reports_permits as $key => $value) {
          $txtworkspace = $value['id_workspace'];
          array_push($reports_permits, $txtworkspace);
        }
      }
      if($type == "report"){
        foreach ($reports_permits as $key => $value) {
          $txtreporte = $value['id_reporte'];
          array_push($reports_permits, $txtreporte);
        }
      }
      $data = array_values($data);
      for($i = 0; $i < count($data); $i++ ){
        $data[$i] = (array)$data[$i];
        if( in_array($data[$i]["id"], $reports_permits ) ){
          array_push($final_data,$data[$i]);
        }
      }

      return $final_data;
    }

  }
  // FUNCTION FILTER USER PERMITS END

  // FUNCTION DUPLICATE REPORT IN WORKSPACE
  public function duplicate_report ($reporte,$workspace,$new_name_report,$accessToken){

    $client = new GuzzleHttp\Client([
      'verify' => false
    ]);

    try{
      
      $res = $client->request(
        'POST', 
        'https://api.powerbi.com/v1.0/myorg/groups/' . $workspace .'/reports/'.$reporte.'/Clone', 
        [
          'headers' => [
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/x-www-form-urlencoded'
          ],
          'form_params' => [
            'name' => $new_name_report
          ]
        ]
      );
      
      return true;
    }
    catch(Exception $e){
      return false;
    }

  }
  // FUNCTION DUPLICATE REPORT IN WORKSPACE END

  // FUNCTION DELETE REPORT IN WORKSPACE
  public function delete_report ($reporte,$workspace,$accessToken){

    // ELIMINAR PERMISOS DE USUARIOS A ESTE REPORTE
    $id_reporte = $reporte;
    $id_wordspace = $workspace;
    
    $delete_old_permits = Yii::$app->db->createCommand("delete from tbl_permisos_reportes_powerbi where id_reporte = '$id_reporte' and id_workspace = '$id_wordspace' ")->execute();    

    if(!$delete_old_permits){
      return false;
    }

    $client = new GuzzleHttp\Client([
      'verify' => false
    ]);

    try{
      
      $res = $client->request(
        'DELETE', 
        'https://api.powerbi.com/v1.0/myorg/groups/' . $workspace. '/reports/'.$reporte,
        [
          'headers' => [
            'Authorization' => 'Bearer ' . $accessToken
          ]
        ]
      );
      
      return true;
    }
    catch(Exception $e){
      return false;
    }

  }
  // FUNCTION DELETE REPORT IN WORKSPACE END


  // FUNCTION DELETE WORKSPACE
  public function delete_workspace ($workspace,$accessToken){
    // ELIMINAR PERMISOS DE USUARIOS A ESTE REPORTE
   
    $id_wordspace = $workspace;
    
    $delete_old_permits = Yii::$app->db->createCommand("delete from tbl_permisos_reportes_powerbi where id_workspace = '$id_wordspace' ")->execute();    

    //if(!$delete_old_permits){
      //return false;
    //}

    $client = new GuzzleHttp\Client([
      'verify' => false
    ]);

    try{
      $res = $client->request(
        'DELETE', 
        'https://api.powerbi.com/v1.0/myorg/groups/' . $workspace,
        [
          'headers' => [
            'Authorization' => 'Bearer ' . $accessToken
          ]
        ]
      );
      return true;
    }
    catch(Exception $e){
      return false;
    }

  }
  // FUNCTION DELETE WORKSPACE END

  // FUNCTION SEARCH COLABORADORES WORKSPACE
  public function search_workspace_contributors ($workspace,$accessToken){

    $client = new GuzzleHttp\Client([
      'verify' => false
    ]);

    try{

      $res = $client->request('GET', 'https://api.powerbi.com/v1.0/myorg/groups/'. $workspace . '/users', [
        'headers' => [
          'Authorization' => 'Bearer ' . $accessToken
        ]
      ]);
        
      // Parsear respuesta a JSON
      $response = json_decode((string)$res->getBody());

      // Validar si existe la propiedad access token en la respuesta
      if(!property_exists($response,'value')){
          return FALSE;
      }
      // Retornar el access token de la respuesta
      return $response->value;
    }
    catch(Exception $e){
      return FALSE;
    }
  }
  // FUNCTION SEARCH COLABORADORES WORKSPACE END

  // FUNCTION DELETE COLABORATOR WORKSPACE
  public function delete_workspace_colaborator ($workspace,$accessToken,$colaborator){
    $client = new GuzzleHttp\Client([
      'verify' => false
    ]);

    try{
      $res = $client->request('DELETE', 'https://api.powerbi.com/v1.0/myorg/groups/'. $workspace .'/users/' . $colaborator, [
        'headers' => [
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json'
          ]
      ]);
      return TRUE;
    }
    catch (Exception $e) {
        return FALSE;
    }
  }
  // FUNCTION DELETE COLABORATOR WORKSPACE END

  // FUNCTION ADD WORKSPACE COLABORATOR
  public function add_workspace_colaborator($workspace,$accessToken,$colaborator){
    
    $client = new GuzzleHttp\Client([
      'verify' => false
    ]);

    try{

      $res = $client->request('POST', 'https://api.powerbi.com/v1.0/myorg/groups/'. $workspace .'/users', [
        'headers' => [
          'Authorization' => 'Bearer ' . $accessToken,
          'Content-Type' => 'application/json'
        ],
        'json' => [
          'emailAddress' => $colaborator,
          'groupUserAccessRight' => 'Admin'
        ]
      ]);
      
      return TRUE;
    }
    catch (Exception $e) {
      return FALSE;
    }

  }
  // FUNCTION ADD WORKSPACE COLABORATOR END


}
