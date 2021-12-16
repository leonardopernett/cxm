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
use yii\base\Exception;
use app\models\DistribucionAsesores;


  class DistribuciondosController extends \yii\web\Controller {

    public function behaviors(){
        return[
          'access' => [
              'class' => AccessControl::classname(),
              'only' => ['index','procesadistribucion','procesaasesores','procesalideres','procesaequipos'],
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
              'delete' => ['get','post'],
            ],
          ],

          'corsFilter' => [
            'class' => \yii\filters\Cors::class,
        ],
        ];
    } 

    public function actionIndex(){
      $varUltimaFecha = null;
      $varCantAsesores = null;
      $varCantLideres = null;

      $paramsCeros = [':Anulado'=> 0];
      $varConteoDistribucion = Yii::$app->db->createCommand('
        SELECT COUNT(ds.cedulaasesor) AS ConteoAsesores FROM tbl_distribucion_asesores ds
          WHERE 
            ds.anulado = :Anulado
        ')->bindValues($paramsCeros)->queryScalar();


      if ($varConteoDistribucion != 0) {
        $varDatos = Yii::$app->db->createCommand('
          SELECT dc.cantidadasesor, dc.cantidadlider, dc.fecharegistro FROM tbl_distribucion_cantidades dc
            WHERE dc.anulado = :Anulado
              AND dc.fecharegistro = (SELECT MAX(fecharegistro) FROM tbl_distribucion_cantidades)
          ')->bindValues($paramsCeros)->queryAll();

        foreach ($varDatos as $key => $value) {
          $varUltimaFecha = $value['fecharegistro'];
          $varCantAsesores = $value['cantidadasesor'];
          $varCantLideres = $value['cantidadlider'];
        }

      }

      return $this->render('index',[
        'varUltimaFecha' => $varUltimaFecha,
        'varCantAsesores' => $varCantAsesores,
        'varCantLideres' => $varCantLideres,
      ]);
    }

    public function actionProcesadistribucion(){
      $model = new DistribucionAsesores();

      $form = Yii::$app->request->post();
      if($model->load($form)){

        $varListSecciones = Yii::$app->dbjarvis2->createCommand("
        SELECT dp.documento AS CedulaAsesor, dp.documento_jefe AS CedulaLider, pc.id_dp_clientes AS id_dp_clientes, 
        dp.cod_pcrc AS CodPcrc, dp.fecha_actual AS FechaJarvis FROM dp_pcrc pc
          INNER JOIN dp_distribucion_personal dp ON 
            pc.cod_pcrc = dp.cod_pcrc
          INNER JOIN dp_cargos dc ON 
            dp.id_dp_cargos = dc.id_dp_cargos
          INNER JOIN dp_estados de ON 
            dp.id_dp_estados = de.id_dp_estados
          WHERE 
            dc.id_dp_posicion IN (39,18)
              AND dc.id_dp_funciones IN (322,783,190,909,915)
                AND dp.fecha_actual >= DATE_FORMAT(NOW() ,'%Y-%m-01')
                  AND de.tipo = 'ACTIVO'
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
                      'usua_id' => Yii::$app->user->identity->id,                                       
                  ])->execute();
        }

        $this->Guardarcantidades();

        return $this->redirect('procesaasesores');
      }else{
        #code
      }
      

      return $this->render('procesadistribucion',[
        'model' => $model,
      ]);
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

        $varListaAsesores = Yii::$app->db->createCommand("
          SELECT ds.cedulaasesor AS CCAsesores FROM tbl_distribucion_asesores ds
            WHERE 
              ds.anulado = 0")->queryAll();

        foreach ($varListaAsesores as $key => $value) {
          $varDocumentoAsesor = $value['CCAsesores'];

          $VarExisteAsesor = Yii::$app->db->createCommand("
          SELECT COUNT(*) FROM tbl_evaluados e 
            WHERE 
              e.identificacion IN ($varDocumentoAsesor)")->queryScalar();

          if ($VarExisteAsesor == 0) {
            $varListDatosAsesor = Yii::$app->dbjarvis2->createCommand("
              SELECT dg.nombre_completo AS Name, ur.usuario_red AS UsuarioRed  FROM dp_actualizacion_datos ad
                INNER JOIN dp_datos_generales dg ON 
                  ad.documento = dg.documento
                INNER JOIN dp_usuarios_red ur ON 
                  dg.documento = ur.documento
                WHERE 
                  ur.documento IN ($varDocumentoAsesor) AND ur.dominio = 'multienlace.com.co'")->queryAll();


            foreach ($varListDatosAsesor as $key => $value) {

              Yii::$app->db->createCommand()->insert('tbl_evaluados',[
                      'name' => $value['Name'],
                      'telefono' => null,
                      'dsusuario_red' => $value['UsuarioRed'],
                      'cdestatus' => null,
                      'identificacion' => $varDocumentoAsesor,
                      'email' => $value['UsuarioRed'].'@grupokonecta.co',  
                      'idpcrc' => null,
                      'usua_id' => Yii::$app->user->identity->id,
                      'fechacreacion' => date('Y-m-d'),                                  
                  ])->execute();

            }
            
          }

        }

        return $this->redirect('procesalideres');
      }else{
        #code
      }      

      return $this->render('procesaasesores',[
        'model' => $model,
      ]);
    }

    public function actionProcesalideres(){
      $model = new DistribucionAsesores();

      $form = Yii::$app->request->post();
      if($model->load($form)){

        $varListLideres = Yii::$app->db->createCommand("
        SELECT da.cedulalider, da.id_dp_clientes FROM tbl_distribucion_asesores da 
          WHERE 
            da.anulado = 0
          GROUP BY da.cedulalider")->queryAll();

        foreach ($varListLideres as $key => $value) {
          $varCCLider = $value['cedulalider'];
          $varCliente = $value['id_dp_clientes'];

          $varNombreCliente = Yii::$app->db->createCommand("
          SELECT pc.cliente FROM tbl_proceso_cliente_centrocosto pc 
          WHERE 
            pc.id_dp_clientes IN ($varCliente)
          GROUP BY pc.id_dp_clientes")->queryScalar();

          $varExisteLider = Yii::$app->db->createCommand("
          SELECT COUNT(u.usua_id) FROM tbl_usuarios u
            WHERE 
              u.usua_identificacion IN ($varCCLider)")->queryScalar();

          if ($varExisteLider == 0) {
            
            $varListDatosLideres = Yii::$app->dbjarvis2->createCommand("
              SELECT dg.nombre_completo AS Name, ur.usuario_red AS UsuarioRed  FROM dp_actualizacion_datos ad
                INNER JOIN dp_datos_generales dg ON 
                  ad.documento = dg.documento
                INNER JOIN dp_usuarios_red ur ON 
                  dg.documento = ur.documento
                WHERE 
                  ur.documento IN ($varCCLider) AND ur.dominio = 'multienlace.com.co'")->queryAll();

            foreach ($varListDatosLideres as $key => $value) {
              
              Yii::$app->db->createCommand()->insert('tbl_usuarios',[
                      'usua_usuario' => $value['UsuarioRed'],
                      'usua_nombre' => $value['Name'],
                      'usua_email' => $value['UsuarioRed'].'@grupokonecta.com',
                      'usua_identificacion' => $varCCLider,
                      'usua_activo' => "S",
                      'usua_estado' => "D",  
                      'usua_fechhoratimeout' => null,
                      'fechacreacion' =>  date('Y-m-d'),                                  
                  ])->execute();

            }

            $varUsuarioLider = Yii::$app->db->createCommand("
              SELECT u.usua_id FROM tbl_usuarios u
                WHERE 
                  u.usua_identificacion IN ('$varCCLider')")->queryScalar();

            if (COUNT($varUsuarioLider) != 0) {

                $varValidadosrol = Yii::$app->db->createCommand("
                SELECT COUNT(u.rel_usua_id) FROM rel_usuarios_roles u
                  WHERE 
                    u.rel_usua_id IN ('$varUsuarioLider')")->queryScalar();

                if ($varValidadosrol == 0) {
                    Yii::$app->db->createCommand()->insert('rel_usuarios_roles',[
                        'rel_usua_id' => $varUsuarioLider,
                        'rel_role_id' => 273,                               
                    ])->execute();
                }
                
                $varValidadosgrupo = Yii::$app->db->createCommand("
                        SELECT COUNT(u.usuario_id) FROM rel_grupos_usuarios u
                        WHERE 
                            u.usuario_id IN ('$varUsuarioLider')")->queryScalar();

                if ($varValidadosgrupo == 0) {
                    Yii::$app->db->createCommand()->insert('rel_grupos_usuarios',[
                        'usuario_id' => $varUsuarioLider,
                        'grupo_id' => 1,                               
                    ])->execute();
                }     
            }
                   

          }

          $varUsuaIdLider = Yii::$app->db->createCommand("
          SELECT u.usua_id FROM tbl_usuarios u
            WHERE 
              u.usua_identificacion IN ($varCCLider)")->queryScalar();

          $varUsuaNombreLider = Yii::$app->db->createCommand("
          SELECT u.usua_nombre FROM tbl_usuarios u
            WHERE 
              u.usua_identificacion IN ($varCCLider)")->queryScalar();          

          $varContarEquipos = Yii::$app->db->createCommand("
              SELECT COUNT(eq.id) FROM tbl_equipos eq
                INNER JOIN tbl_usuarios u ON 
                  eq.usua_id = u.usua_id 
                          WHERE 
                            u.usua_identificacion IN ($varCCLider)")->queryScalar();

          if ($varContarEquipos == 0 && $varUsuaIdLider != 0) {
            Yii::$app->db->createCommand()->insert('tbl_equipos',[
                      'name' => $varUsuaNombreLider.'_'.$varNombreCliente,
                      'nmumbral_verde' => 1, 
                      'nmumbral_amarillo' => 1,
                      'usua_id' =>  $varUsuaIdLider,                             
                  ])->execute();
          }

        }      

        return $this->redirect('procesaequipos');

      }else{
        #code
      }

      return $this->render('procesalideres',[
        'model' => $model,
      ]);
    }

    public function actionProcesaequipos(){
      $model = new DistribucionAsesores();

      $form = Yii::$app->request->post();
      if($model->load($form)){        
        $modelos = Yii::$app->db->createCommand('DELETE FROM tbl_equipos_evaluados');
        $modelos->execute();

        $varListLider = Yii::$app->db->createCommand("
        SELECT e.id, u.usua_id, da.cedulalider FROM tbl_equipos e
          INNER JOIN tbl_usuarios u ON 
            e.usua_id = u.usua_id
          INNER JOIN tbl_distribucion_asesores da ON 
            u.usua_identificacion = da.cedulalider
          GROUP BY 
            da.cedulalider")->queryAll();

        foreach ($varListLider as $key => $value) {
          $varIdEquipos = $value['id'];
          $varCcLideres = $value['cedulalider'];

          $varListAsesores = Yii::$app->db->createCommand("
          SELECT e.id FROM tbl_evaluados e
            INNER JOIN tbl_distribucion_asesores da ON 
              e.identificacion = da.cedulaasesor
            WHERE 
              da.cedulalider = $varCcLideres")->queryAll();

          foreach ($varListAsesores as $key => $value) {
            Yii::$app->db->createCommand()->insert('tbl_equipos_evaluados',[
                      'evaluado_id' => $value['id'],
                      'equipo_id' => $varIdEquipos,                             
                  ])->execute();
          }
        }

        return $this->redirect('index');
      }else{
        #code
      }

      return $this->render('procesaequipos',[
        'model' => $model,
      ]);
    }
    
    public function Guardarcantidades(){
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
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute();

    }

  }

?>
