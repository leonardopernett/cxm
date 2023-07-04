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
                                'tbl_distribucion_clientenuevo.id_clientenuevo',
                                'tbl_proceso_cliente_centrocosto.cliente', 
                                'tbl_hojavida_sociedad.sociedad'
                              ])
                              ->from(['tbl_distribucion_clientenuevo'])
                              ->join('LEFT OUTER JOIN', 'tbl_proceso_cliente_centrocosto',
                                'tbl_proceso_cliente_centrocosto.id_dp_clientes = tbl_distribucion_clientenuevo.id_dp_clientes')
                              ->join('LEFT OUTER JOIN', 'tbl_hojavida_sociedad',
                                'tbl_hojavida_sociedad.id_sociedad = tbl_distribucion_clientenuevo.id_sociedad')
                              ->where(['=','tbl_distribucion_clientenuevo.anulado',0])
                              ->groupby(['tbl_proceso_cliente_centrocosto.id_dp_clientes'])
                              ->all();  
    
    return $this->render('index',[
      'varListaGeneral' => $varListaGeneral,
    ]);

   }

   public function actionParametrizarclientes(){

    $model = new Valoracionclientenuevo();

    $form = Yii::$app->request->post();
    if ($model->load($form)) {
      $varIdCliente = $model->id_dp_clientes;
      $varIdSociedad = $model->id_sociedad;

      $varExiste = (new \yii\db\Query())
                                  ->select(['*'])
                                  ->from(['tbl_distribucion_clientenuevo'])
                                  ->where(['=','id_dp_clientes',$varIdCliente])
                                  ->andwhere(['=','id_sociedad',$varIdSociedad])
                                  ->andwhere(['=','anulado',0])
                                  ->count();

      if ($varExiste == 0) {
        Yii::$app->db->createCommand()->insert('tbl_distribucion_clientenuevo',[
                  'id_dp_clientes' => $varIdCliente,
                  'id_sociedad' => $varIdSociedad,
                  'anulado' => 0,
                  'usua_id' => Yii::$app->user->identity->id,
                  'fechacreacion' => date('Y-m-d'),
        ])->execute();

        $varIdGeneral = (new \yii\db\Query())
                                  ->select(['id_clientenuevo'])
                                  ->from(['tbl_distribucion_clientenuevo'])
                                  ->where(['=','id_dp_clientes',$varIdCliente])
                                  ->andwhere(['=','id_sociedad',$varIdSociedad])
                                  ->andwhere(['=','anulado',0])
                                  ->scalar();

        return $this->redirect(array('index'));
      }else{
        return $this->redirect(['index']);
      }
      
    }

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
    
   

    $codigoCliente =  (new \yii\db\Query())
                                  ->select(['cliente'])
                                  ->from(['tbl_proceso_cliente_centrocosto'])
                                  ->join('INNER JOIN', 'tbl_distribucion_clientenuevo',
                                      'tbl_distribucion_clientenuevo.id_dp_clientes = tbl_proceso_cliente_centrocosto.id_dp_clientes')
                                  ->where(['=','tbl_distribucion_clientenuevo.id_clientenuevo',$id_general])
                                  ->one();

          
          
    $datosTablaGlobal  = (new \yii\db\Query())
                                  ->select(['tbl_evaluados.id','tbl_evaluados.name', 'tbl_evaluados.dsusuario_red', 'tbl_equipos.name AS name_equipo'])
                                  ->from(['tbl_evaluados'])
                                  ->join('LEFT OUTER JOIN', 'tbl_equipos_evaluados',
                                  'tbl_equipos_evaluados.evaluado_id = tbl_evaluados.id')
                                  ->join('LEFT OUTER JOIN', 'tbl_equipos',
                                  'tbl_equipos.id = tbl_equipos_evaluados.equipo_id')
                                  ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                  'tbl_arbols.arbol_id = tbl_evaluados.idpcrc')   
                                  ->where(['LIKE', 'tbl_arbols.name', $codigoCliente])
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
      'id_general' => $varidcliente,
      'datosTablaGlobal' => $datosTablaGlobal,
      
      
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
      
      $identificacion = $sheet->getCell("C".$i)->getValue();

      if ($identificacion === null) {
        do{
          $identificacion = rand(10000000,99999999);
          $identificadores = (new \yii\db\Query())
              ->select(['*'])
              ->from(['tbl_evaluados'])   
              ->where(['=', 'identificacion', $identificacion])                               
              ->count();

        }while($identificadores > 0);  
        
      }


        $nombre_equipo = $sheet->getCell("D".$i)->getValue();                     

        $nombre_pcrc = $sheet->getCell("E".$i)->getValue();
        
        $varIdPcrc  = (new \yii\db\Query())
            ->select(['arbol_id'])
            ->from(['tbl_arbols'])
            ->where(['=','name',$nombre_pcrc])
            ->scalar();
  
            
            Yii::$app->db->createCommand()->insert('tbl_evaluados',[
              'dsusuario_red' => $username,
              'name' => $sheet->getCell("B".$i)->getValue(),
              'identificacion' => $identificacion,
              'email' =>  $username.'@cxm.com.co',
              'fechacreacion' => date("Y-m-d"),
              'usua_id' => Yii::$app->user->identity->id, 
              'idpcrc' => $varIdPcrc
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


