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
use GuzzleHttp;
use app\models\HojavidaEventos;
use app\models\HVCiudad;
use app\models\HvPais;


  class HojavidaController extends Controller {

    public function behaviors(){
        return[
          'access' => [
              'class' => AccessControl::classname(),
              'only' => ['index','resumen','eventos','paisciudad'],
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
              'delete' => ['get'],
            ],
          ],

          'corsFilter' => [
            'class' => \yii\filters\Cors::class,
        ],
        ];
    } 
   
    public function actionIndex(){
      
      return $this->render('index');
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
                  ->where('tbl_usuarios.usua_id = '.$sessiones.'');                    
      $command = $rol->createCommand();
      $roles = $command->queryScalar();
    

       $clients = Yii::$app->db->createCommand('SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i
       WHERE i.usua_id=:id')->bindParam(':id', $id)->queryAll();

      $decisor = Yii::$app->db->createCommand("SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i
      WHERE i.usua_id=:id  AND i.tipo='Decisor' ")->bindParam(':id', $id)->queryAll();

      $estrategico = Yii::$app->db->createCommand("SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i
      WHERE i.usua_id=:id  AND i.nivel='Estrategico' ")->bindParam(':id', $id)->queryAll();


      $operativo = Yii::$app->db->createCommand("SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i
      WHERE i.usua_id=:id  AND i.nivel='Operativo' ")->bindParam(':id', $id)->queryAll();



      $clientsAdmin = Yii::$app->db->createCommand('SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i')->queryAll();

      $clientsAdmins = Yii::$app->db->createCommand('SELECT * FROM tbl_hv_infopersonal')->queryAll();

      $decisorAdmin = Yii::$app->db->createCommand("SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i
      WHERE  i.tipo='Decisor' ")->bindParam(':id', $id)->queryAll();

      $estrategicoAdmin = Yii::$app->db->createCommand("SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i
      WHERE  i.nivel='Estrategico' ")->bindParam(':id', $id)->queryAll();


      $operativoAdmin = Yii::$app->db->createCommand("SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i
      WHERE  i.nivel='Operativo' ")->bindParam(':id', $id)->queryAll();



        $decisorEstrategico =Yii::$app->db->createCommand("SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i
        WHERE  i.tipo='Decisor' AND i.nivel='estrategico' ")->queryAll();

        $decisorOperativo=Yii::$app->db->createCommand("SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i
        WHERE  i.tipo='Decisor' AND i.nivel='operativo' ")->queryAll();


        $nodecisorEstrategico =Yii::$app->db->createCommand("SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i
        WHERE  i.tipo='No Decisor' AND i.nivel='estrategico' ")->queryAll();


        $nodecisorOperativo = Yii::$app->db->createCommand("SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i
        WHERE  i.tipo='No Decisor' AND i.nivel='operativo' ")->queryAll();


        $clienteInteresAdmin =  Yii::$app->db->createCommand("SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i
        WHERE  i.afinidad='de interes' ")->queryAll();


        $clienteInteres =  Yii::$app->db->createCommand("SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i
        WHERE  i.afinidad='de interes' AND i.usua_id=:id ")->bindParam(':id', $id)->queryAll();


        $decisorEstrategicoU =Yii::$app->db->createCommand("SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i
        WHERE  i.tipo='Decisor' AND i.nivel='estrategico' AND i.usua_id=:id  ")->bindParam(':id', $id)->queryAll();

        $decisorOperativoU=Yii::$app->db->createCommand("SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i
        WHERE  i.tipo='Decisor' AND i.nivel='operativo' AND i.usua_id=:id  ")->bindParam(':id', $id)->queryAll();


        $nodecisorEstrategicoU =Yii::$app->db->createCommand("SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i
        WHERE  i.tipo='No Decisor' AND i.nivel='estrategico' AND i.usua_id=:id ")->bindParam(':id', $id)->queryAll();


        $nodecisorOperativoU = Yii::$app->db->createCommand("SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i
        WHERE  i.tipo='No Decisor' AND i.nivel='operativo' AND i.usua_id=:id  ")->bindParam(':id', $id)->queryAll();



       return $this->render('resumen',[
          'clients'=>$clients,
          'decisor'=>$decisor,
          'estrategico'=>$estrategico,
          'operativo' =>$operativo,
          'clientsAdmin'=>$clientsAdmin,
          'decisorAdmin'=>$decisorAdmin,
          'estrategicoAdmin'=>$estrategicoAdmin,
          'operativoAdmin' =>$operativoAdmin,
          'id'=>$id,
          'roles' =>$roles,
          'clientsAdmins'=>$clientsAdmins,

          'decisorEstrategico'=>$decisorEstrategico,
          'decisorOperativo'=>$decisorOperativo,
          'nodecisorEstrategico'=>$nodecisorEstrategico,
          'nodecisorOperativo'=>$nodecisorOperativo,

          'decisorEstrategicoU'=>$decisorEstrategicoU,
          'decisorOperativoU'=>$decisorOperativoU,
          'nodecisorEstrategicoU'=>$nodecisorEstrategicoU,
          'nodecisorOperativoU'=>$nodecisorOperativoU,
          'clienteInteresAdmin'=>$clienteInteresAdmin,
          'clienteInteres'=>$clienteInteres
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
      }

      $dataProvider = Yii::$app->db->createCommand("
        SELECT e.nombre_evento, e.tipo_evento, e.fecha_evento_inicio, e.fecha_evento_fin, c.ciudad, e.asistencia 
          FROM tbl_hojavida_eventos e
              INNER JOIN tbl_hv_ciudad c ON c.hv_idciudad = e.hv_idciudad ")->queryAll();


      return $this->render('eventos',[
        'model' => $model,
        'dataProvider' => $dataProvider,
      ]);
    }

    public function actionPaisciudad(){
      $modelpais = new HvPais();
      $modelciudad = new HvCiudad();

      return $this->render('paisciudad',[
        'modelpais' => $modelpais,
        'modelciudad' => $modelciudad,
      ]);
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
       Yii::$app->session->setFlash('info','MODALIDAD CREADA EXITOSAMENTE');
       return $this->redirect(["crearmodalidad"]);
    }


    public function actionEliminarmodalidad($id){
      Yii::$app->db->createCommand('DELETE FROM tbl_hv_modalidad_trabajo WHERE hv_idmodalidad=:id')->bindParam(':id',$id)->execute();
      Yii::$app->session->setFlash('info','MODALIDAD ELIMINADA CORRECTAMENTE');
      return $this->redirect(["crearmodalidad"]);
    }

   

  }

?>


