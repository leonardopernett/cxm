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
use GuzzleHttp;


  class ApidistribuciondosController extends \yii\web\Controller {

    public function behaviors(){
      return[
        'verbs' => [          
          'class' => VerbFilter::className(),
          'actions' => [
            'delete' => ['post'],
          ],
        ],

        'access' => [
            'class' => AccessControl::classname(),
            'denyCallback' => function ($rule, $action) {
                    $msg = \Yii::t('app', 'The requested Item could not be found.');
                    Yii::$app->session->setFlash('danger', $msg);
                    $url = \yii\helpers\Url::to(['site/index']);
                    return $action->controller->redirect($url);
            },

            
            'rules' => [
              [
                'actions' => ['index','apiprocesadistribucionauto'],
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema();
                        },
              ],
              [
                'actions' => ['apiprocesadistribucionauto'],
                'allow' => true,

              ],
            ],

        ],

           


        
      ];
    }
  
    public function init(){
      $this->enableCsrfValidation = false;
    }

    public function actionIndex(){

      return $this->render('index');
    }

    public function actionApiprocesadistribucionauto(){

        $arraydatas = "Proceso realizado";

        ini_set("max_execution_time", "900");
        ini_set("memory_limit", "1024M");
        ini_set( 'post_max_size', '1024M' );

        ignore_user_abort(true);
        set_time_limit(900);

      
        $varListSecciones = Yii::$app->dbjarvis->createCommand("
        SELECT dp.documento AS CedulaAsesor, dp.documento_jefe AS CedulaLider, pc.id_dp_clientes AS id_dp_clientes, 
        dp.cod_pcrc AS CodPcrc, dp.fecha_actual AS FechaJarvis FROM dp_pcrc pc
          INNER JOIN dp_distribucion_personal dp ON 
            pc.cod_pcrc = dp.cod_pcrc
          INNER JOIN dp_cargos dc ON 
            dp.id_dp_cargos = dc.id_dp_cargos
          INNER JOIN dp_estados de ON 
            dp.id_dp_estados = de.id_dp_estados
          WHERE 
            dc.id_dp_posicion IN (39,18,40)
              AND dc.id_dp_funciones IN (322,783,190,909,915,323,324)
                AND dp.fecha_actual >= DATE_FORMAT(NOW() ,'%Y-%m-01')
                  AND de.tipo IN ('ACTIVO','GESTION')
                    AND pc.id_dp_clientes != 1
          GROUP BY dp.documento
        ")->queryAll();

        Yii::$app->db->createCommand()->truncateTable('tbl_distribucion_asesores')->execute();

        foreach ($varListSecciones as $key => $value) {
            Yii::$app->db->createCommand()->insert('tbl_distribucion_asesores',[
                      'cedulaasesor' => $value['CedulaAsesor'],
                      'cedulalider' => $value['CedulaLider'],
                      'fechaactualjarvis' => $value['FechaJarvis'],  
                      'id_dp_clientes' => $value['id_dp_clientes'],
                      'cod_pcrc' => $value['CodPcrc'],
                      'fechamodificacxm' => date('Y-m-d'),
                      'fechacreacion' => date('Y-m-d'),
                      'anulado' => 0,
                      'usua_id' => 7952,                                       
                  ])->execute();
        }

        // Es necesario generar esta funcion para permitir el uso de guardar cantidades despues de verificar el ingreso de los datos de Jarvis
        $this->Guardarcantidadesapi();
        
        die(json_encode(array("status"=>"1","data"=>$arraydatas)));
      
    }     

    public function Guardarcantidadesapi(){
        $paramsCeros = [':Anulado'=> 0];
        $varConteoAsesores = Yii::$app->db->createCommand('
          SELECT COUNT(ds.cedulaasesor) AS ConteoAsesos FROM tbl_distribucion_asesores ds
            WHERE 
              ds.anulado = :Anulado
          ')->bindValues($paramsCeros)->queryScalar();
  
        $varConteolideres = Yii::$app->db->createCommand('
          SELECT COUNT(1) AS ConteosLideres FROM tbl_distribucion_asesores ds
            WHERE 
              ds.anulado = :Anulado
            GROUP BY ds.cedulalider
              HAVING COUNT(1) > 1
          ')->bindValues($paramsCeros)->queryAll();
  
  
        Yii::$app->db->createCommand()->insert('tbl_distribucion_cantidades',[
                      'cantidadasesor' => $varConteoAsesores,
                      'cantidadlider' => count($varConteolideres),
                      'fecharegistro' => date('Y-m-d h:m:s'),
                      'fechacreacion' => date('Y-m-d'),
                      'anulado' => 0,
                      'usua_id' => 7952,                                       
                  ])->execute();
  
      }

      public function actionProcesaasesores(){
      
        $model = new DistribucionAsesores();
  
        $form = Yii::$app->request->post();
        if($model->load($form)){
          ini_set("max_execution_time", "900");
          ini_set("memory_limit", "1024M");
          ini_set( 'post_max_size', '1024M' );
  
          ignore_user_abort(true);
          set_time_limit(900);
  
          $varListaAsesores = (new \yii\db\Query())
                                      ->select(['cedulaasesor'])
                                      ->from(['tbl_distribucion_asesores'])
                                      ->where(['=','anulado',0])
                                      ->all(); 
  
          foreach ($varListaAsesores as $key => $value) {
            $varDocumentoAsesor = $value['cedulaasesor'];
  
            $varExisteAsesor = (new \yii\db\Query())
                                      ->select(['id'])
                                      ->from(['tbl_evaluados'])
                                      ->where(['=','identificacion',$varDocumentoAsesor])
                                      ->count(); 
  
            if ( $varExisteAsesor == '0') {
  
              $paramsBuscaAsesor = [':DocumentoAsesor'=>$varDocumentoAsesor];
              
              $varNombreAsesor = Yii::$app->dbjarvis->createCommand('
                SELECT CONCAT(dg.primer_apellido," ",dg.segundo_apellido," ",dg.primer_nombre," ",dg.segundo_nombre) AS NombreCompleto, du.usuario_red FROM dp_datos_generales dg
                  INNER JOIN dp_usuarios_red du ON
                    dg.documento = du.documento 
                WHERE 
                  du.documento = :DocumentoAsesor ')->bindValues($paramsBuscaAsesor)->queryScalar();
  
              $varUsuaredAsesor = Yii::$app->dbjarvis->createCommand('
                SELECT du.usuario_red FROM  dp_usuarios_red du 
                WHERE 
                  du.documento = :DocumentoAsesor ')->bindValues($paramsBuscaAsesor)->queryScalar();
  
              if ($varNombreAsesor != "" && $varUsuaredAsesor != "") {
  
                Yii::$app->db->createCommand()->insert('tbl_evaluados',[
                        'name' => $varNombreAsesor,
                        'telefono' => null,
                        'dsusuario_red' => $varUsuaredAsesor,
                        'cdestatus' => null,
                        'identificacion' => $varDocumentoAsesor,
                        'email' => $varUsuaredAsesor.'@grupokonecta.co',  
                        'idpcrc' => null,
                        'usua_id' => Yii::$app->user->identity->id,
                        'fechacreacion' => date('Y-m-d'),                                  
                    ])->execute();
  
              }            
            }
          }
  
          return $this->redirect('procesalideres');
        }      
  
        return $this->render('procesaasesores',[
          'model' => $model,
        ]);
      }
  
      public function actionProcesalideres(){
        
        $model = new DistribucionAsesores();
  
        $form = Yii::$app->request->post();
        if($model->load($form)){
          ini_set("max_execution_time", "900");
          ini_set("memory_limit", "1024M");
          ini_set( 'post_max_size', '1024M' );
  
          ignore_user_abort(true);
          set_time_limit(900);
  
          $varListaLideres = (new \yii\db\Query())
                                      ->select(['cedulalider','id_dp_clientes'])
                                      ->from(['tbl_distribucion_asesores'])
                                      ->where(['=','anulado',0])
                                      ->all(); 
  
          foreach ($varListaLideres as $key => $value) {
            $varDocumentoLider = $value['cedulalider'];
            $varIdCliente = $value['id_dp_clientes'];
  
            $varExisteLider = (new \yii\db\Query())
                                      ->select(['usua_id'])
                                      ->from(['tbl_usuarios'])
                                      ->where(['=','usua_identificacion',$varDocumentoLider])
                                      ->count(); 
  
            if ($varExisteLider == "0") {
  
              $paramsBuscaLider = [':DocumentoLider'=>$varDocumentoLider];
              
              $varNombreLider = Yii::$app->dbjarvis->createCommand('
                SELECT CONCAT(dg.primer_apellido," ",dg.segundo_apellido," ",dg.primer_nombre," ",dg.segundo_nombre) AS NombreCompleto, du.usuario_red FROM dp_datos_generales dg
                  INNER JOIN dp_usuarios_red du ON
                    dg.documento = du.documento 
                WHERE 
                  du.documento = :DocumentoLider ')->bindValues($paramsBuscaLider)->queryScalar();
  
              $varUsuaredLider = Yii::$app->dbjarvis->createCommand('
                SELECT du.usuario_red FROM  dp_usuarios_red du 
                WHERE 
                  du.documento = :DocumentoLider ')->bindValues($paramsBuscaLider)->queryScalar();
  
              if ($varNombreLider != "" && $varUsuaredLider != "") {
  
                Yii::$app->db->createCommand()->insert('tbl_usuarios',[
                        'usua_usuario' => $varUsuaredLider,
                        'usua_nombre' => $varNombreLider,
                        'usua_email' => $varUsuaredLider.'@grupokonecta.com',
                        'usua_identificacion' => $varDocumentoLider,
                        'usua_activo' => "S",
                        'usua_estado' => "D",  
                        'usua_fechhoratimeout' => null,
                        'fechacreacion' =>  date('Y-m-d'),                                  
                    ])->execute();
  
                $varidUsuarioLider = (new \yii\db\Query())
                                      ->select(['usua_id'])
                                      ->from(['tbl_usuarios'])
                                      ->where(['=','usua_identificacion',$varDocumentoLider])
                                      ->scalar(); 
  
                Yii::$app->db->createCommand()->insert('rel_usuarios_roles',[
                        'rel_usua_id' => $varidUsuarioLider,
                        'rel_role_id' => 273,                               
                    ])->execute();
  
                Yii::$app->db->createCommand()->insert('rel_grupos_usuarios',[
                        'usuario_id' => $varidUsuarioLider,
                        'grupo_id' => 1,                               
                    ])->execute();
  
              }
              
            }          
            
          }
  
          return $this->redirect('procesaequipos');
  
        }
  
        return $this->render('procesalideres',[
          'model' => $model,
        ]);
      }
      
      public function actionApiprocesaequiposauto(){
        $varHora = date('h:i:s');
        $varIdCorte = null;
  
          if ($varHora == "07:00:00") {
              $varIdCorte = (new \yii\db\Query())
                                      ->select(['idtc'])
                                      ->from(['tbl_tipocortes'])
                                      ->where(['=','anulado',0])
                                      ->andwhere(['=','idgrupocorte',1])
                                      ->scalar();
          }
          if ($varHora >= "07:03:00") {
              $varIdCorte = (new \yii\db\Query())
                                      ->select(['idtc'])
                                      ->from(['tbl_tipocortes'])
                                      ->where(['=','anulado',0])
                                      ->andwhere(['=','idgrupocorte',2])
                                      ->scalar();
          }	
          if ($varHora == "07:06:00") {
              $varIdCorte = (new \yii\db\Query())
                                      ->select(['idtc'])
                                      ->from(['tbl_tipocortes'])
                                      ->where(['=','anulado',0])
                                      ->andwhere(['=','idgrupocorte',3])
                                      ->scalar();
          }
          if ($varHora == "07:09:00") {
              $varIdCorte = (new \yii\db\Query())
                                      ->select(['idtc'])
                                      ->from(['tbl_tipocortes'])
                                      ->where(['=','anulado',0])
                                      ->andwhere(['=','idgrupocorte',4])
                                      ->scalar();
          }
          if ($varHora == "07:12:00") {
              $varIdCorte = (new \yii\db\Query())
                                      ->select(['idtc'])
                                      ->from(['tbl_tipocortes'])
                                      ->where(['=','anulado',0])
                                      ->andwhere(['=','idgrupocorte',5])
                                      ->scalar();
          }
  
          ini_set("max_execution_time", "900");
          ini_set("memory_limit", "1024M");
          ini_set( 'post_max_size', '1024M' );
  
          ignore_user_abort(true);
          set_time_limit(900);
  
          $varGrupoCorte = (new \yii\db\Query())
                                      ->select(['idgrupocorte'])
                                      ->from(['tbl_tipocortes'])
                                      ->where(['=','idtc',$varIdCorte])
                                      ->scalar();
  
          if ($varGrupoCorte == "1") {
            $varIdClientes = (new \yii\db\Query())
                                      ->select(['id_servicio'])
                                      ->from(['tbl_cortes_servicios'])
                                      ->where(['=','anulado',0])
                                      ->andwhere(['=','id_corte',1])
                                      ->all();         
          }
  
          if ($varGrupoCorte == "2") {
            $varIdClientes = (new \yii\db\Query())
                                      ->select(['id_servicio'])
                                      ->from(['tbl_cortes_servicios'])
                                      ->where(['=','anulado',0])
                                      ->andwhere(['=','id_corte',2])
                                      ->all();           
          }
  
          if ($varGrupoCorte == "3") {
            $varIdClientes = (new \yii\db\Query())
                                      ->select(['id_servicio'])
                                      ->from(['tbl_cortes_servicios'])
                                      ->where(['=','anulado',0])
                                      ->andwhere(['=','id_corte',3])
                                      ->all();
          }
  
          if ($varGrupoCorte == "4") {
            $varIdClientes = (new \yii\db\Query())
                                      ->select(['id_servicio'])
                                      ->from(['tbl_cortes_servicios'])
                                      ->where(['=','anulado',0])
                                      ->andwhere(['=','id_corte',4])
                                      ->all();           
          }
  
          if ($varGrupoCorte == "5") {
            $varIdClientes = (new \yii\db\Query())
                                      ->select(['id_servicio'])
                                      ->from(['tbl_cortes_servicios'])
                                      ->where(['=','anulado',0])
                                      ->andwhere(['=','id_corte',5])
                                      ->all();           
          }
  
          $varArrayClientes = array();
          foreach ($varIdClientes as $key => $value) {
            array_push($varArrayClientes,$value['id_servicio']);
  
          }
  
          $varClienteLista = implode(",",$varArrayClientes);
  
          $varListAsesoresEliminar = (new \yii\db\Query())
                                    ->select(['tbl_evaluados.id','tbl_distribucion_asesores.cedulaasesor'])
                                    ->from(['tbl_evaluados'])
                                    ->join('INNER JOIN', 'tbl_distribucion_asesores', 
                                        'tbl_evaluados.identificacion = tbl_distribucion_asesores.cedulaasesor')
                                    ->where(['IN','tbl_distribucion_asesores.id_dp_clientes',$varClienteLista])
                                    ->groupby(['tbl_evaluados.id'])
                                    ->all();
                                    foreach ($varListAsesoresEliminar as $key => $value) {
                                      $varAsesorId = $value['id'];          
                                      $paramsEliminarEquipos = [':varIdEvaluado'=>$varAsesorId]; 
                            
          $varValdiaParams = (new \yii\db\Query())
                                                        ->select(['tbl_equipo_parametros.idequipo_parametros'])
                                                        ->from(['tbl_equipo_parametros'])
                                                        ->join('INNER JOIN', 'tbl_equipos_evaluados', 
                                                                'tbl_equipo_parametros.id_equipo = tbl_equipos_evaluados.equipo_id')
                                                        ->where(['IN','tbl_equipos_evaluados.evaluado_id',$varAsesorId])
                                                        ->count();
                            
                                      if ($varValdiaParams == "0") {
                                        Yii::$app->db->createCommand('
                                                DELETE FROM tbl_equipos_evaluados 
                                                  WHERE 
                                                    evaluado_id = :varIdEvaluado')
                                              ->bindValues($paramsEliminarEquipos)
                                              ->execute();
                                      }
                            
                                    }
                            
                                    $varListUsuariosD = (new \yii\db\Query())
                                                      ->select(['tbl_usuarios.usua_id','tbl_usuarios.usua_identificacion','tbl_distribucion_asesores.id_dp_clientes'])
                                                      ->from(['tbl_usuarios'])
                                                      ->join('INNER JOIN', 'tbl_distribucion_asesores', 
                                                          'tbl_usuarios.usua_identificacion = tbl_distribucion_asesores.cedulalider')
                                                      ->where(['IN','tbl_distribucion_asesores.id_dp_clientes',$varIdClientes])
                                                      ->groupby(['tbl_usuarios.usua_id'])
                                                      ->all();         
                            
                            
                                    foreach ($varListUsuariosD as $key => $value) {
                                      $varUsuarioD = $value['usua_id'];
                                      $varClienteD = $value['id_dp_clientes'];
                            
                                      $varListaEquipoD = (new \yii\db\Query())
                                                                ->select(['*'])
                                                                ->from(['tbl_equipos'])
                                                                ->where(['=','usua_id',$varUsuarioD])
                                                                ->all();
                            
                                      if (count($varListaEquipoD) != 0) {
                                        foreach ($varListaEquipoD as $key => $value) {
                                          $varIdEquipoD = $value['id'];
                                          $varNombreEquipoD = $value['name'];
                            
                                          $varNoDistriEquipoD = (new \yii\db\Query())
                                                                ->select(['*'])
                                                                ->from(['tbl_equipo_parametros'])
                                                                ->where(['=','id_equipo',$varIdEquipoD])
                                                                ->count();
                            
                                          if ($varNoDistriEquipoD == "0") {
                                            $varValidaD = (new \yii\db\Query())
                                                                ->select(['*'])
                                                                ->from(['tbl_equipos_evaluados'])
                                                                ->where(['=','equipo_id',$varIdEquipoD])
                                                                ->count();
                            
                                            if ($varValidaD != "0") {
                            
                                              $paramsEliminar = [':IdControlEquipo'=>$varIdEquipoD]; 
                            
                                              Yii::$app->db->createCommand('
                                                DELETE FROM tbl_equipos_evaluados 
                                                  WHERE 
                                                    equipo_id = :IdControlEquipo')
                                              ->bindValues($paramsEliminar)
                                              ->execute();  
                            
                                            }else{
                                                
                                                $varNombreUsuaD = (new \yii\db\Query())
                                                                ->select(['usua_nombre'])
                                                                ->from(['tbl_usuarios'])
                                                                ->where(['=','usua_id',$varUsuarioD])
                                                                ->scalar();
                            
                                                $varNombreClienteD = (new \yii\db\Query())
                                                              ->select(['CONCAT("_",cliente)'])
                                                              ->from(['tbl_proceso_cliente_centrocosto'])
                                                              ->where(['=','id_dp_clientes',$varClienteD])
                                                              ->groupby(['id_dp_clientes'])
                                                              ->scalar();  
                            
                                                if ($varNombreEquipoD == $varNombreUsuaD.'_'.$varNombreClienteD.'(No usar)') {
                                                  Yii::$app->db->createCommand()->update('tbl_equipos',[
                                                                        'name' => $varNombreUsuaD.'_'.$varNombreClienteD.'(No usar)',
                                                                    ],'id ='.$varIdEquipoD.'')->execute();
                                                }                    
                                                               
                                            }
                                          }                
                            
                                        }
                            
                                      }else{
                                        
                                        $varNombreLiderD = (new \yii\db\Query())
                                                              ->select(['usua_nombre'])
                                                              ->from(['tbl_usuarios'])
                                                              ->where(['=','usua_id',$varUsuarioD])
                                                              ->scalar(); 
                            
                                        $varClienteNombreD = (new \yii\db\Query())
                                                              ->select(['CONCAT("_",cliente)'])
                                                              ->from(['tbl_proceso_cliente_centrocosto'])
                                                              ->where(['=','id_dp_clientes',$varClienteD])
                                                              ->groupby(['id_dp_clientes'])
                                                              ->scalar();  
                            
                                        Yii::$app->db->createCommand()->insert('tbl_equipos',[
                                                  'name' => $varNombreLiderD.$varClienteNombreD,
                                                  'nmumbral_verde' => 1, 
                                                  'nmumbral_amarillo' => 1,
                                                  'usua_id' =>  $varUsuarioD,                             
                                              ])->execute();
                                      }
                            
                                      $varListaEquipoDOk = (new \yii\db\Query())
                                                                ->select(['*'])
                                                                ->from(['tbl_equipos'])
                                                                ->where(['=','usua_id',$varUsuarioD])
                                                                ->andwhere(['not like','name','No usar'])
                                                                ->all();
                            
                                      foreach ($varListaEquipoDOk as $key => $value) {
                                        $varEquipoIdD = $value['id'];
                                        
                                        $varListAsesoresD = (new \yii\db\Query())
                                                            ->select(['tbl_evaluados.id'])
                                                            ->from(['tbl_evaluados'])
                                                            ->join('INNER JOIN', 'tbl_distribucion_asesores', 
                                                              'tbl_evaluados.identificacion = tbl_distribucion_asesores.cedulaasesor')
                                                            ->join('INNER JOIN', 'tbl_usuarios', 
                                                              'tbl_distribucion_asesores.cedulalider = tbl_usuarios.usua_identificacion')
                                                            ->where(['=','tbl_usuarios.usua_id',$varUsuarioD])
                                                            ->groupby(['tbl_evaluados.id'])
                                                            ->all();
                            
                                        foreach ($varListAsesoresD as $key => $value) {
                                          Yii::$app->db->createCommand()->insert('tbl_equipos_evaluados',[
                                                    'evaluado_id' => $value['id'],
                                                    'equipo_id' => $varEquipoIdD,                             
                                                ])->execute();
                                        }
                                        
                                      }
                                        
                                    }
                            
                            
                                    Yii::$app->db->createCommand()->insert('tbl_distribucion_cortes',[
                                                  'grupocorte' => $varGrupoCorte,
                                                  'idtc' => $varIdCorte, 
                                                  'ultimafecha' => date('Y-m-d h:i:s'),
                                                  'anulado' => 0,
                                                  'usua_id' =>  Yii::$app->user->identity->id,   
                                                  'fechacreacion' => date('Y-m-d'),                
                                              ])->execute();
  
  
        die(json_encode("Finaliza correctamente la accion"));
                                      
      }

    
       
    

  }

?>
