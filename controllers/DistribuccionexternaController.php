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
use app\models\FormUploadtigo;
use app\models\UploadForm2;
use app\models\UploadForm3;
use app\models\Valoracionclientenuevo;
use app\models\Valoraciondatogeneral;
use app\models\Valoracionatributos;
use app\models\Valoraciondatoespecial;
use app\models\Valoracionformulariosexcel;
use app\models\EquiposEvaluados;
use GuzzleHttp;
use Exception;

  class DistribuccionexternaController extends Controller {

    
    public function behaviors(){
        return[
          'access' => [
              'class' => AccessControl::classname(),
              'only' => ['index','parametrizarclientes','subirclientesnuevos','agregarasesoresmas'],
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

    $varListaGeneral = (new \yii\db\Query())
                              ->select([
                                'tbl_valoracion_clientenuevo.id_clientenuevo',
                                'tbl_proceso_cliente_centrocosto.cliente', 
                                'tbl_hojavida_sociedad.sociedad'
                              ])
                              ->from(['tbl_valoracion_clientenuevo'])
                              ->join('LEFT OUTER JOIN', 'tbl_proceso_cliente_centrocosto',
                                'tbl_proceso_cliente_centrocosto.id_dp_clientes = tbl_valoracion_clientenuevo.id_dp_clientes')
                              ->join('LEFT OUTER JOIN', 'tbl_hojavida_sociedad',
                                'tbl_hojavida_sociedad.id_sociedad = tbl_valoracion_clientenuevo.id_sociedad')
                              ->where(['=','tbl_valoracion_clientenuevo.anulado',0])
                              ->groupby(['tbl_proceso_cliente_centrocosto.id_dp_clientes'])
                              ->all();  
    
    return $this->render('index',[
      'varListaGeneral' => $varListaGeneral,
    ]);

   }

   public function actionParametrizarclientes(){

    $model = new Valoracionclientenuevo();

      

      return $this->renderAjax('parametrizarclientes',[
        'model' => $model,
      ]);
   }


   public function actionSubirclientesnuevos($id_general){

    $varidcliente = (new \yii\db\Query())
                                  ->select(['id_dp_clientes'])
                                  ->from(['tbl_proceso_cliente_centrocosto'])
                                  ->where(['=','cod_pcrc',$id_general])
                                  ->andwhere(['=','anulado',0])
                                  ->scalar();

    $varidcliente = $id_general;

  

    $varNombreCliente = (new \yii\db\Query())
                                  ->select(['cliente'])
                                  ->from(['tbl_proceso_cliente_centrocosto'])
                                  ->where(['=','id_dp_clientes',$id_general])
                                  ->scalar();

    $datosTablaGlobal  = (new \yii\db\Query())
                                  ->select(['tbl_evaluados.id','tbl_evaluados.name', 'tbl_evaluados.dsusuario_red', 'tbl_equipos.name AS name_equipo'])
                                  ->from(['tbl_evaluados'])
                                  ->join('LEFT OUTER JOIN', 'tbl_equipos_evaluados',
                                  'tbl_equipos_evaluados.evaluado_id = tbl_evaluados.id')
                                  ->join('LEFT OUTER JOIN', 'tbl_equipos',
                                  'tbl_equipos.id = tbl_equipos_evaluados.equipo_id')
                                  ->where(['=','idpcrc',4041])
                                  ->all(); 


    $model = new FormUploadtigo();        

    if ($model->load(Yii::$app->request->post())) {
              
      $model->file = UploadedFile::getInstances($model, 'file');

      if ($model->file && $model->validate()) {
              
        foreach ($model->file as $file) {
          $fecha = date('Y-m-d-h-i-s');
          $user = Yii::$app->user->identity->username;
          $name = $fecha . '-' . $user;
          $file->saveAs('categorias/' . $name . '.' . $file->extension);
          $this->Importararchivo($name,$id_general);

          return $this->redirect(array('subirclientesnuevos','id_general'=>$varidcliente));
        }
      }
    }

    return $this->render('subirclientesnuevos',[
      'model' => $model,
      'varidcliente' => $varidcliente,
      'varNombreCliente' =>$varNombreCliente,
      'datosTablaGlobal' => $datosTablaGlobal,
      'id_general' => $varidcliente,
      
    ]);
  }

  public function Importararchivo($name,$id_general){
  
    $inputFile = 'categorias/' . $name . '.xlsx';

    try {
        $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
        $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($inputFile);

    } catch (Exception $e) {
        die('Error');
    }

    $sheet = $objPHPExcel->getSheet(0);
    $highestRow = $sheet->getHighestRow();


    for ($i=12; $i <= $highestRow; $i++) {  
      
     
      $username = $sheet->getCell("A".$i)->getValue();
             
        do{
          $identificacion = rand(10000000,99999999);
          $identificadores = (new \yii\db\Query())
              ->select(['*'])
              ->from(['tbl_evaluados'])   
              ->where(['=', 'identificacion', $identificacion])                               
              ->count();

        }while($identificadores > 0);            
      
        $nombre_equipo = $sheet->getCell("C".$i)->getValue();                     

        Yii::$app->db->createCommand()->insert('tbl_evaluados',[
          'dsusuario_red' => $username,
          'name' => $sheet->getCell("B".$i)->getValue(),
          'identificacion' => $identificacion,
          'email' =>  $username.'@cxm.com.co',
          'fechacreacion' => date("Y-m-d"),
          'usua_id' => Yii::$app->user->identity->id, 
          'idpcrc' => 4041
        ])->execute();
          

        $id_equipo = (new \yii\db\Query())
          ->select(['id'])
          ->from(['tbl_equipos'])
          ->where(['=','name',$nombre_equipo])
          ->all();

        
        $id_evaluado = (new \yii\db\Query())
          ->select(['id'])
          ->from(['tbl_evaluados'])    
          ->where(['=', 'dsusuario_red', $username])
          ->all();          
        
        

        Yii::$app->db->createCommand()->insert('tbl_equipos_evaluados',[
            'evaluado_id' => $id_evaluado[0]["id"],
            'equipo_id' => $id_equipo[0]["id"],
        ])->execute();

      }   
  }

  public function actionDeleteasesor($id,$id_general){ 

    Yii::$app->db->createCommand("UPDATE tbl_evaluados SET 
    name = CONCAT('(NO USAR)', name), 
    dsusuario_red = CONCAT('(NO USAR)', dsusuario_red) 
    WHERE id ='".$id."'")->execute();   

    return $this->redirect(array('subirclientesnuevos','id_general'=>$id_general));//retornar  la vista 

  }
 
         
  
}

?>


