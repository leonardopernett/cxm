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
use app\models\DirectorioCad;
use app\models\EtapamultipleCad;
use GuzzleHttp;
use Exception;

  class DirectorioCadController extends Controller {

    
    public function behaviors(){
        return[
          'access' => [
              'class' => AccessControl::classname(),
              'only' => ['index'],
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
      $model = new DirectorioCad();
      $modelo = new EtapamultipleCad();

      $varListaGeneral = (new \yii\db\Query())                                                                    
                              ->select(['tbl_vicepresidente_cad.nombre AS vicepresidente','tbl_proceso_cliente_centrocosto.director_programa',
                              'tbl_proceso_cliente_centrocosto.gerente_cuenta','tbl_directorio_cad.sociedad','tbl_ciudad_cad.nombre AS ciudad', 
                              'tbl_sector_cad.nombre AS sector','tbl_proceso_cliente_centrocosto.cliente',
                              'tbl_tipo_cad.nombre AS tipo','tbl_tipocanal_cad.nombre AS tipo_canal','tbl_directorio_cad.otro_canal',
                              'tbl_proveedores_cad.name AS proveedores','tbl_directorio_cad.nom_plataforma','tbl_etapa_cad.nombre AS etapa','tbl_directorio_cad.id_directorcad'])
                              ->from(['tbl_directorio_cad'])  
                              ->join('LEFT OUTER JOIN', 'tbl_vicepresidente_cad',
                              'tbl_vicepresidente_cad.id_vicepresidentecad = tbl_directorio_cad.vicepresidente')
                              ->join('LEFT OUTER JOIN', 'tbl_ciudad_cad',
                              'tbl_ciudad_cad.id_ciudad_cad  = tbl_directorio_cad.ciudad')
                              ->join('LEFT OUTER JOIN', 'tbl_proceso_cliente_centrocosto',
                              'tbl_proceso_cliente_centrocosto.documento_director = tbl_directorio_cad.directorprog and 
                              tbl_proceso_cliente_centrocosto.documento_gerente = tbl_directorio_cad.gerente and
                              tbl_proceso_cliente_centrocosto.id_dp_clientes = tbl_directorio_cad.cliente 
                              ')  
                              ->join('LEFT OUTER JOIN', 'tbl_sector_cad',
                              'tbl_sector_cad.id_sectorcad = tbl_directorio_cad.sector')    
                              ->join('LEFT OUTER JOIN', 'tbl_tipo_cad',
                              'tbl_tipo_cad.id_tipocad = tbl_directorio_cad.tipo') 
                              ->join('LEFT OUTER JOIN', 'tbl_tipocanal_cad',
                              'tbl_tipocanal_cad.id_tipocanalcad = tbl_directorio_cad.tipo_canal') 
                              ->join('LEFT OUTER JOIN', 'tbl_proveedores_cad',
                              'tbl_proveedores_cad.id_proveedorescad = tbl_directorio_cad.proveedores')
                              ->join('LEFT OUTER JOIN', 'tbl_etapa_cad',
                              'tbl_etapa_cad.id_etapacad = tbl_directorio_cad.etapa') 
                              ->where(['=','tbl_directorio_cad.anulado',0])
                              ->groupBy(['tbl_proceso_cliente_centrocosto.documento_director'])
                              ->all();

      $form = Yii::$app->request->post();
      if ($model->load($form) ) { 
        
        
        Yii::$app->db->createCommand()->insert('tbl_directorio_cad',[
                                  'vicepresidente' => $model->vicepresidente,
                                  'gerente' => $model->gerente,
                                  'sociedad' => $model->sociedad,
                                  'ciudad' => $model->ciudad,
                                  'sector' => $model->sector,
                                  'cliente' => $model->cliente, 
                                  'tipo' => $model->tipo,
                                  'tipo_canal' => $model->tipo_canal,
                                  'otro_canal' => $model->otro_canal,
                                  'fechacreacion' => date("Y-m-d"),                    
                                  'anulado' => 0,
                                  'usua_id' => Yii::$app->user->identity->id,
                                  'proveedores' => $model->proveedores,
                                  'nom_plataforma' => $model->nom_plataforma,
                                  'directorprog' => $model->directorprog,
                                  ])->execute();

        $varDatos = (new \yii\db\Query())
                            ->select(['id_directorcad'])
                            ->from(['tbl_directorio_cad'])
                            ->where(['=','vicepresidente',$model->vicepresidente])
                            ->andwhere(['=','gerente',$model->gerente])
                            ->andwhere(['=','sociedad',$model->sociedad])
                            ->andwhere(['=','ciudad',$model->ciudad])
                            ->andwhere(['=','tipo',$model->tipo])
                            ->andwhere(['=','tipo_canal',$model->tipo_canal])
                            ->andwhere(['=','otro_canal',$model->otro_canal])
                            ->andwhere(['=','usua_id',Yii::$app->user->identity->id])
                            ->andwhere(['=','proveedores',$model->proveedores])
                            ->andwhere(['=','nom_plataforma',$model->nom_plataforma,])
                            ->andwhere(['=','directorprog',$model->directorprog])
                            ->andwhere(['=','sector',$model->sector])
                            ->andwhere(['=','anulado',0])
                            ->scalar();
        
        foreach ($model->etapa as $key => $value) {

          Yii::$app->db->createCommand()->insert('tbl_etapamultiple_cad',[
            'id_directorcad' => $varDatos,
            'id_etapacad' => $value,
            'fechacreacion' => date("Y-m-d"),                    
            'anulado' => 0,
            'usua_id' => Yii::$app->user->identity->id,
            ])->execute();
        }
                    
        return $this->redirect(['index']);
      }  
        return $this->render('index',[
            'model' => $model,
            'varListaGeneral' => $varListaGeneral, 
        ]);

    }

    public function actionSubircarga(){

        $model = new FormUploadtigo();        

      if ($model->load(Yii::$app->request->post())) {
                
        $model->file = UploadedFile::getInstances($model, 'file');

        if ($model->file && $model->validate()) {
                
            foreach ($model->file as $file) {
                $fecha = date('Y-m-d-h-i-s');
                $user = Yii::$app->user->identity->username;
                $name = $fecha . '-' . $user;
                $file->saveAs('categorias/' . $name . '.' . $file->extension);
                $this->Importardirectorio($name);

                return $this->redirect(array('index'));
            }
        }
      }

        return $this->renderAjax('subircarga',[
            'model' => $model,
          ]);

    }


    public function Importardirectorio($name){

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


        for ($i=3; $i <= $highestRow; $i++) { 
                   
          $varVicepresidente = (new \yii\db\Query())
                ->select(['tbl_vicepresidente_cad.id_vicepresidentecad'])
                ->from(['tbl_vicepresidente_cad'])
                ->where(['LIKE','tbl_vicepresidente_cad.nombre','%' . $sheet->getCell("A".$i)->getValue() .'%',false])
                ->scalar();

          $varDirector = (new \yii\db\Query())
                ->select(['tbl_proceso_cliente_centrocosto.documento_director'])
                ->from(['tbl_proceso_cliente_centrocosto'])
                ->where(['LIKE','tbl_proceso_cliente_centrocosto.director_programa','%' . $sheet->getCell("B".$i)->getValue() .'%',false])
                ->groupBy(['tbl_proceso_cliente_centrocosto.director_programa'])
                ->scalar();

          $varGerente = (new \yii\db\Query())
                ->select(['tbl_proceso_cliente_centrocosto.documento_gerente'])
                ->from(['tbl_proceso_cliente_centrocosto'])
                ->where(['LIKE','tbl_proceso_cliente_centrocosto.gerente_cuenta','%' . $sheet->getCell("C".$i)->getValue() . '%',false])
                ->groupBY(['tbl_proceso_cliente_centrocosto.gerente_cuenta'])
                ->scalar();

          $varCiudad = (new \yii\db\Query())
                ->select(['tbl_ciudad_cad.id_ciudad_cad'])
                ->from(['tbl_ciudad_cad'])
                ->where(['LIKE','tbl_ciudad_cad.nombre','%' . $sheet->getCell("E".$i)->getValue() . '%',false])
                ->scalar();

          $varSector = (new \yii\db\Query())
              ->select(['tbl_sector_cad.id_sectorcad'])
              ->from(['tbl_sector_cad'])
              ->where(['LIKE','tbl_sector_cad.nombre','%' . $sheet->getCell("F".$i)->getValue() . '%',false])
              ->scalar();

          $varCliente = (new \yii\db\Query())
              ->select(['tbl_proceso_cliente_centrocosto.id_dp_clientes'])
              ->from(['tbl_proceso_cliente_centrocosto'])
              ->where(['LIKE','tbl_proceso_cliente_centrocosto.cliente','%' . $sheet->getCell("G".$i)->getValue() . '%',false])
              ->groupBY(['tbl_proceso_cliente_centrocosto.cliente'])
              ->scalar();

          $varTipo = (new \yii\db\Query())
              ->select(['tbl_tipo_cad.id_tipocad'])
              ->from(['tbl_tipo_cad'])
              ->where(['LIKE','tbl_tipo_cad.nombre','%' . $sheet->getCell("H".$i)->getValue() . '%',false])
              ->scalar();

          $varTipoCanal = (new \yii\db\Query())
              ->select(['tbl_tipocanal_cad.id_tipocanalcad'])
              ->from(['tbl_tipocanal_cad'])
              ->where(['LIKE','tbl_tipocanal_cad.nombre','%' . $sheet->getCell("I".$i)->getValue() . '%',false])
              ->scalar();

          $varProveedores = (new \yii\db\Query())
              ->select(['tbl_proveedores_cad.id_proveedorescad'])
              ->from(['tbl_proveedores_cad'])
              ->where(['LIKE','tbl_proveedores_cad.name','%' . $sheet->getCell("K".$i)->getValue() . '%',false])
              ->scalar();

          $varEtapa = (new \yii\db\Query())
              ->select(['tbl_etapa_cad.id_etapacad'])
              ->from(['tbl_etapa_cad'])
              ->where(['LIKE','tbl_etapa_cad.nombre','%' . $sheet->getCell("M".$i)->getValue() . '%',false])
              ->scalar();

            Yii::$app->db->createCommand()->insert('tbl_directorio_cad',[
              'vicepresidente' => $varVicepresidente,
              'directorprog' => $varDirector,
              'gerente'=>$varGerente,
              'sociedad'=> $sheet->getCell("D".$i)->getValue(),
              'ciudad'=> $varCiudad,
              'sector' => $varSector,
              'cliente' => $varCliente,
              'tipo' => $varTipo,
              'tipo_canal' => $varTipoCanal,
              'otro_canal' => $sheet->getCell("J".$i)->getValue(),
              'proveedores'=>$varProveedores,
              'nom_plataforma' => $sheet->getCell("L".$i)->getValue(),
              'etapa'=> $varEtapa,
              'fechacreacion' => date("Y-m-d"),
              'usua_id' => Yii::$app->user->identity->id,
              'anulado'=> 0,
          ])->execute(); 
          }     
  }

    public function actionEditarusu($id_directorcad){
        
        $model = DirectorioCad::findOne($id_directorcad);
        
        if($model->load(Yii::$app->request->post())){
          Yii::$app->db->createCommand()->update('tbl_directorio_cad',[
            'vicepresidente' => $model->vicepresidente,
            'gerente' => $model->gerente,
            'sociedad' => $model->sociedad,
            'ciudad' => $model->ciudad,
            'sector' => $model->sector,
            'cliente' => $model->cliente, 
            'tipo' => $model->tipo,
            'tipo_canal' => $model->tipo_canal,
            'otro_canal' => $model->otro_canal,
            'fechacreacion' => date("Y-m-d"),                    
            'anulado' => 0,
            'usua_id' => Yii::$app->user->identity->id,
            'proveedores' => $model->proveedores,
            'nom_plataforma' => $model->nom_plataforma,
            'directorprog' => $model->directorprog,
          ])->execute(); 
        }
        
        return $this->render('editarusu',[
            'model' => $model
        ]);  
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
              echo "<option value='" . $value->documento_director. "'>" . $value->director_programa. "</option>";
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
                            ->where(['=','tbl_proceso_cliente_centrocosto.id_dp_clientes',$txtId])
                            ->andwhere(['=','tbl_proceso_cliente_centrocosto.estado',1])
                            ->count();            
                        
          if ($txtControl > 0) {
            $varListaCiudad = \app\models\ProcesosClienteCentrocosto::find()
                            ->select(['tbl_proceso_cliente_centrocosto.documento_gerente','tbl_proceso_cliente_centrocosto.gerente_cuenta'])->distinct()
                              ->where(['=','tbl_proceso_cliente_centrocosto.id_dp_clientes', $txtId])
                              ->andwhere(['=','tbl_proceso_cliente_centrocosto.estado', 1]) 
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
  

      public function actionParametrizar(){
        $model = new DirectorioCad();

        $varListaGeneral = (new \yii\db\Query())                                                                    
                  ->select(['tbl_proceso_cliente_centrocosto.cliente'])
                  ->from(['tbl_clientesparametrizados_cad'])  
                  ->join('LEFT OUTER JOIN', 'tbl_proceso_cliente_centrocosto',
                              'tbl_proceso_cliente_centrocosto.id_dp_clientes = tbl_clientesparametrizados_cad.cliente')          
                  ->where(['=','tbl_clientesparametrizados_cad.anulado',0])
                  ->groupBy(['tbl_clientesparametrizados_cad.cliente']) 
                  ->all();
        
        $form = Yii::$app->request->post();
        if ($model->load($form) ) {
        Yii::$app->db->createCommand()->insert('tbl_clientesparametrizados_cad',[
                                  'cliente' => $model->cliente,
                                  'fechacreacion' => date("Y-m-d"),                    
                                  'anulado' => 0,
                                  'usua_id' => Yii::$app->user->identity->id,
                                  ])->execute();

                                  return $this->redirect(array('parametrizar'));

                                }


        return $this->render('parametrizar',[
            'model' => $model,
            'varListaGeneral' => $varListaGeneral,
        ]);  
      

  }

  public function actionEliminarclient(){
    
  }

}

?>


