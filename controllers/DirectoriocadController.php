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
use app\models\VicepresidenteCad;
use app\models\TipoCad;
use app\models\CiudadCad;
use app\models\SectorCad;
use app\models\ProveedoresCad;
use app\models\TipocanalCad;
use app\models\SociedadCad;
use app\models\EtapaCad;
use app\models\EtapamultipleCad;
use GuzzleHttp;
use Exception;

  class DirectorioCadController extends Controller {

    
    public function behaviors(){
        return[
          'access' => [
              'class' => AccessControl::classname(),
              'only' => ['index','subircarga'],
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

      $varAlerta = 0;

      $varId_Directorio = (new \yii\db\Query())
                            ->select(['tbl_directorio_cad.id_directorcad'])
                            ->from(['tbl_directorio_cad'])
                            ->where(['=','tbl_directorio_cad.anulado',0])
                            ->all();


      $varListaGeneral = (new \yii\db\Query()) 
                              ->select(['tbl_directorio_cad.id_directorcad',
                              'tbl_vicepresidente_cad.nombre AS vicepresidente',
                              'pg.director_programa',
                              'pd.gerente_cuenta',
                              'tbl_sociedad_cad.nombre AS sociedad',
                              'tbl_ciudad_cad.nombre AS ciudad',
                              'tbl_sector_cad.nombre AS sector',
                              'tbl_tipo_cad.nombre AS tipo','tbl_tipocanal_cad.nombre AS tipo_canal','tbl_directorio_cad.otro_canal',
                              'tbl_proveedores_cad.name AS proveedores','tbl_directorio_cad.nom_plataforma','tbl_directorio_cad.id_directorcad',
                              

                              ])
                              ->from(['tbl_directorio_cad'])  
                              ->join('INNER JOIN','tbl_vicepresidente_cad',
                              'tbl_vicepresidente_cad.id_vicepresidentecad = tbl_directorio_cad.vicepresidente')
                              ->join('INNER JOIN','tbl_ciudad_cad',
                              'tbl_ciudad_cad.id_ciudad_cad  = tbl_directorio_cad.ciudad')
                              ->join('INNER JOIN','tbl_proceso_cliente_centrocosto pd',
                              'pd.documento_gerente = tbl_directorio_cad.gerente') 
                              ->join('INNER JOIN','tbl_proceso_cliente_centrocosto pg',
                              'pg.documento_director = tbl_directorio_cad.directorprog') 
                              ->join('INNER JOIN','tbl_sector_cad',
                              'tbl_sector_cad.id_sectorcad = tbl_directorio_cad.sector')    
                              ->join('INNER JOIN','tbl_tipo_cad',
                              'tbl_tipo_cad.id_tipocad = tbl_directorio_cad.tipo') 
                              ->join('INNER JOIN','tbl_tipocanal_cad',
                              'tbl_tipocanal_cad.id_tipocanalcad = tbl_directorio_cad.tipo_canal') 
                              ->join('INNER JOIN','tbl_proveedores_cad',
                              'tbl_proveedores_cad.id_proveedorescad = tbl_directorio_cad.proveedores')
                              ->join('INNER JOIN','tbl_sociedad_cad',
                              'tbl_sociedad_cad.id_sociedadcad = tbl_directorio_cad.sociedad')
                              ->where(['=','tbl_directorio_cad.anulado',0])
                              ->groupBy(['tbl_directorio_cad.id_directorcad'])
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
                                  'directorprog' => $model->directorprog
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
      
            $varAlerta = 1;

            return $this->redirect(['index','varAlerta' => base64_encode($varAlerta)]);        
          }
               
        return $this->render('index',[
            'model' => $model,
            'varListaGeneral' => $varListaGeneral,
            'varAlerta' => $varAlerta, 
            'varId_Directorio' => $varId_Directorio, 
        ]);

    }

    public function actionSubircarga(){


      $varAlerta = 0;
      
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

                $varAlerta = 1;    
              
                return $this->redirect(['index','varAlerta' => base64_encode($varAlerta)]);
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
                ->where(['LIKE','tbl_vicepresidente_cad.nombre','%' . trim($sheet->getCell("A".$i)->getValue()) .'%',false])
                ->scalar();

          $varDirector = (new \yii\db\Query())
                ->select(['tbl_proceso_cliente_centrocosto.documento_director'])
                ->from(['tbl_proceso_cliente_centrocosto'])
                ->where(['LIKE','tbl_proceso_cliente_centrocosto.director_programa','%' . trim($sheet->getCell("B".$i)->getValue()) .'%',false])
                ->groupBy(['tbl_proceso_cliente_centrocosto.director_programa'])
                ->scalar();

          $varGerente = (new \yii\db\Query())
                ->select(['tbl_proceso_cliente_centrocosto.documento_gerente'])
                ->from(['tbl_proceso_cliente_centrocosto'])
                ->where(['LIKE','tbl_proceso_cliente_centrocosto.gerente_cuenta','%' . trim($sheet->getCell("C".$i)->getValue()) . '%',false])
                ->groupBY(['tbl_proceso_cliente_centrocosto.gerente_cuenta'])
                ->scalar();

          $varCiudad = (new \yii\db\Query())
                ->select(['tbl_ciudad_cad.id_ciudad_cad'])
                ->from(['tbl_ciudad_cad'])
                ->where(['LIKE','tbl_ciudad_cad.nombre','%' . trim($sheet->getCell("E".$i)->getValue()) . '%',false])
                ->scalar();

          $varSector = (new \yii\db\Query())
              ->select(['tbl_sector_cad.id_sectorcad'])
              ->from(['tbl_sector_cad'])
              ->where(['LIKE','tbl_sector_cad.nombre','%' . trim($sheet->getCell("F".$i)->getValue()) .'%',false])
              ->scalar();    
        
          $varCliente = (new \yii\db\Query())
              ->select(['tbl_proceso_cliente_centrocosto.id_dp_clientes'])
              ->from(['tbl_proceso_cliente_centrocosto'])
              ->where(['=','tbl_proceso_cliente_centrocosto.cliente',trim($sheet->getCell("G".$i)->getValue())])
              ->scalar();

          $varSociedad = (new \yii\db\Query())
              ->select(['tbl_sociedad_cad.id_sociedadcad'])
              ->from(['tbl_sociedad_cad'])
              ->where(['LIKE','tbl_sociedad_cad.nombre','%' . trim($sheet->getCell("D".$i)->getValue()) . '%',false])
              ->scalar();

          $varTipo = (new \yii\db\Query())
              ->select(['tbl_tipo_cad.id_tipocad'])
              ->from(['tbl_tipo_cad'])
              ->where(['LIKE','tbl_tipo_cad.nombre','%' . trim($sheet->getCell("H".$i)->getValue()) . '%',false])
              ->scalar();

          $varTipoCanal = (new \yii\db\Query())
              ->select(['tbl_tipocanal_cad.id_tipocanalcad'])
              ->from(['tbl_tipocanal_cad'])
              ->where(['LIKE','tbl_tipocanal_cad.nombre','%' . trim($sheet->getCell("I".$i)->getValue()) . '%',false])
              ->scalar();

          $varProveedores = (new \yii\db\Query())
              ->select(['tbl_proveedores_cad.id_proveedorescad'])
              ->from(['tbl_proveedores_cad'])
              ->where(['LIKE','tbl_proveedores_cad.name','%' . trim($sheet->getCell("K".$i)->getValue()) . '%',false])
              ->scalar();

            Yii::$app->db->createCommand()->insert('tbl_directorio_cad',[
              'vicepresidente' => $varVicepresidente,
              'directorprog' => $varDirector,
              'gerente'=>$varGerente,
              'sociedad'=> $varSociedad,
              'ciudad'=> $varCiudad,
              'sector' => $varSector,
              'cliente' => $varCliente,
              'tipo' => $varTipo,
              'tipo_canal' => $varTipoCanal,
              'otro_canal' => trim($sheet->getCell("J".$i)->getValue()),
              'proveedores'=>$varProveedores,
              'nom_plataforma' => trim($sheet->getCell("L".$i)->getValue()),
              'fechacreacion' => date("Y-m-d"),
              'usua_id' => Yii::$app->user->identity->id,
              'anulado'=> 0,
                ])->execute(); 

              $varDatos = (new \yii\db\Query())
                        ->select(['id_directorcad'])
                        ->from(['tbl_directorio_cad'])
                        ->where(['=','vicepresidente',$varVicepresidente])
                        ->andwhere(['=','gerente',$varGerente])
                        ->andwhere(['=','sociedad',$varSociedad])
                        ->andwhere(['=','ciudad',$varCiudad])
                        ->andwhere(['=','tipo',$varTipo])
                        ->andwhere(['=','cliente',$varCliente])
                        ->andwhere(['=','tipo_canal',$varTipoCanal])
                        ->andwhere(['=','usua_id',Yii::$app->user->identity->id])
                        ->andwhere(['=','proveedores',$varProveedores])
                        ->andwhere(['=','directorprog',$varDirector])
                        ->andwhere(['=','sector',$varSector])
                        ->andwhere(['=','anulado',0])
                        ->scalar();

              $varEtapa1 = (new \yii\db\Query())
                        ->select(['tbl_etapa_cad.id_etapacad'])
                        ->from(['tbl_etapa_cad'])
                        ->where(['LIKE','tbl_etapa_cad.nombre','%' . trim($sheet->getCell("M".$i)->getValue()) . '%',false])
                        ->groupBy(['tbl_etapa_cad.id_etapacad'])
                        ->scalar();
          
              $varEtapa2 = (new \yii\db\Query())
                        ->select(['tbl_etapa_cad.id_etapacad'])
                        ->from(['tbl_etapa_cad'])
                        ->where(['LIKE','tbl_etapa_cad.nombre','%' . trim($sheet->getCell("N".$i)->getValue()) . '%',false])
                        ->groupBy(['tbl_etapa_cad.id_etapacad'])
                        ->scalar();
          
              $varEtapa3 = (new \yii\db\Query())
                        ->select(['tbl_etapa_cad.id_etapacad'])
                        ->from(['tbl_etapa_cad'])
                        ->where(['LIKE','tbl_etapa_cad.nombre','%' . trim($sheet->getCell("O".$i)->getValue()) . '%',false])
                        ->groupBy(['tbl_etapa_cad.id_etapacad'])
                        ->scalar();
          
              $varEtapa4 = (new \yii\db\Query())
                        ->select(['tbl_etapa_cad.id_etapacad'])
                        ->from(['tbl_etapa_cad'])
                        ->where(['LIKE','tbl_etapa_cad.nombre','%' . trim($sheet->getCell("P".$i)->getValue()) . '%',false])
                        ->groupBy(['tbl_etapa_cad.id_etapacad'])
                        ->scalar();
          
              $varEtapa5 = (new \yii\db\Query())
                        ->select(['tbl_etapa_cad.id_etapacad'])
                        ->from(['tbl_etapa_cad'])
                        ->where(['LIKE','tbl_etapa_cad.nombre','%' . trim($sheet->getCell("Q".$i)->getValue()) . '%',false])
                        ->groupBy(['tbl_etapa_cad.id_etapacad'])
                        ->scalar();

              $varEtapa6 = (new \yii\db\Query())
                        ->select(['tbl_etapa_cad.id_etapacad'])
                        ->from(['tbl_etapa_cad'])
                        ->where(['LIKE','tbl_etapa_cad.nombre','%' . trim($sheet->getCell("R".$i)->getValue()) . '%',false])
                        ->groupBy(['tbl_etapa_cad.id_etapacad'])
                        ->scalar();

              $varExiste1 = (new \yii\db\Query())
                        ->select(['tbl_etapamultiple_cad.id_etapamultiplecad'])
                        ->from(['tbl_etapamultiple_cad'])
                        ->where(['=','tbl_etapamultiple_cad.anulado',0])
                        ->andwhere(['=','tbl_etapamultiple_cad.id_directorcad',$varDatos])
                        ->andwhere(['=','tbl_etapamultiple_cad.id_etapacad',$varEtapa1])
                        ->count();
               
              if ($varExiste1 == 0) {
                if (trim($sheet->getCell("M".$i)->getValue()) != "") {
                  Yii::$app->db->createCommand()->insert('tbl_etapamultiple_cad',[
                    'id_directorcad' => $varDatos,
                    'id_etapacad' => $varEtapa1,
                    'fechacreacion' => date("Y-m-d"),                    
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
                  ])->execute();

                }
              }

              $varExiste2 = (new \yii\db\Query())
                        ->select(['tbl_etapamultiple_cad.id_etapamultiplecad'])
                        ->from(['tbl_etapamultiple_cad'])
                        ->where(['=','tbl_etapamultiple_cad.anulado',0])
                        ->andwhere(['=','tbl_etapamultiple_cad.id_directorcad',$varDatos])
                        ->andwhere(['=','tbl_etapamultiple_cad.id_etapacad',$varEtapa2])
                        ->count();

              if ($varExiste2 == 0) {
                if (trim($sheet->getCell("N".$i)->getValue()) != "") {
                  Yii::$app->db->createCommand()->insert('tbl_etapamultiple_cad',[
                    'id_directorcad' => $varDatos,
                    'id_etapacad' => $varEtapa2,
                    'fechacreacion' => date("Y-m-d"),                    
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
                  ])->execute();
                }
              }

              $varExiste3 = (new \yii\db\Query())
                        ->select(['tbl_etapamultiple_cad.id_etapamultiplecad'])
                        ->from(['tbl_etapamultiple_cad'])
                        ->where(['=','tbl_etapamultiple_cad.anulado',0])
                        ->andwhere(['=','tbl_etapamultiple_cad.id_directorcad',$varDatos])
                        ->andwhere(['=','tbl_etapamultiple_cad.id_etapacad',$varEtapa3])
                        ->count();

              if ($varExiste3 == 0) {
                if (trim($sheet->getCell("O".$i)->getValue())!= "") {
                  Yii::$app->db->createCommand()->insert('tbl_etapamultiple_cad',[
                    'id_directorcad' => $varDatos,
                    'id_etapacad' => $varEtapa3,
                    'fechacreacion' => date("Y-m-d"),                    
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
                  ])->execute();
                }
              }

              $varExiste4 = (new \yii\db\Query())
                        ->select(['tbl_etapamultiple_cad.id_etapamultiplecad'])
                        ->from(['tbl_etapamultiple_cad'])
                        ->where(['=','tbl_etapamultiple_cad.anulado',0])
                        ->andwhere(['=','tbl_etapamultiple_cad.id_directorcad',$varDatos])
                        ->andwhere(['=','tbl_etapamultiple_cad.id_etapacad',$varEtapa4])
                        ->count();

              if ($varExiste4 == 0) {
                if (trim($sheet->getCell("P".$i)->getValue()) != "") {
                  Yii::$app->db->createCommand()->insert('tbl_etapamultiple_cad',[
                    'id_directorcad' => $varDatos,
                    'id_etapacad' => $varEtapa4,
                    'fechacreacion' => date("Y-m-d"),                    
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
                  ])->execute();
                }
              } 

              $varExiste5 = (new \yii\db\Query())
                        ->select(['tbl_etapamultiple_cad.id_etapamultiplecad'])
                        ->from(['tbl_etapamultiple_cad'])
                        ->where(['=','tbl_etapamultiple_cad.anulado',0])
                        ->andwhere(['=','tbl_etapamultiple_cad.id_directorcad',$varDatos])
                        ->andwhere(['=','tbl_etapamultiple_cad.id_etapacad',$varEtapa5])
                        ->count();

              if ($varExiste5 == 0) {
                if (trim($sheet->getCell("Q".$i)->getValue()) != "") {
                  Yii::$app->db->createCommand()->insert('tbl_etapamultiple_cad',[
                    'id_directorcad' => $varDatos,
                    'id_etapacad' => $varEtapa5,
                    'fechacreacion' => date("Y-m-d"),                    
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
                  ])->execute();
                }
              }  

              $varExiste6 = (new \yii\db\Query())
                        ->select(['tbl_etapamultiple_cad.id_etapamultiplecad'])
                        ->from(['tbl_etapamultiple_cad'])
                        ->where(['=','tbl_etapamultiple_cad.anulado',0])
                        ->andwhere(['=','tbl_etapamultiple_cad.id_directorcad',$varDatos])
                        ->andwhere(['=','tbl_etapamultiple_cad.id_etapacad',$varEtapa6])
                        ->count();

              if ($varExiste6 == 0) {
                if (trim($sheet->getCell("R".$i)->getValue()) != "") {
                  Yii::$app->db->createCommand()->insert('tbl_etapamultiple_cad',[
                    'id_directorcad' => $varDatos,
                    'id_etapacad' => $varEtapa6,
                    'fechacreacion' => date("Y-m-d"),                    
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
                  ])->execute();
                }
              }
               
               

                 

          }     

  }

    public function actionEditarusu($id_directorcad){
        
        $model = DirectorioCad::findOne($id_directorcad);

        $id_directorcad = $id_directorcad;
        $varAlerta = 0;


        $form = Yii::$app->request->post();
        if ($model->load($form) ) { 

          $varidcliente = (new \yii\db\Query())
                  ->select(['tbl_directorio_cad.cliente'])
                  ->from(['tbl_directorio_cad'])
                  ->where(['=','tbl_directorio_cad.id_directorcad',$id_directorcad])
                  ->scalar();

          if ($model->cliente == $varidcliente) {

            $varidgerente = null;          
            if ($model->gerente != '') {
              $varidgerente = $model->gerente;
            }else{
              $varidgerente = (new \yii\db\Query())
                  ->select(['tbl_directorio_cad.gerente'])
                  ->from(['tbl_directorio_cad'])
                  ->where(['=','tbl_directorio_cad.id_directorcad',$id_directorcad])
                  ->scalar();
            }

            $variddirector = null;          
            if ($model->directorprog != '') {
              $variddirector = $model->directorprog;
            }else{
              $variddirector = (new \yii\db\Query())
                  ->select(['tbl_directorio_cad.directorprog'])
                  ->from(['tbl_directorio_cad'])
                  ->where(['=','tbl_directorio_cad.id_directorcad',$id_directorcad])
                  ->scalar();
            }

            $varidetapa = null;          
            if ($model->etapa != '') {
                $varidetapa = (new \yii\db\Query())
                  ->select(['tbl_etapamultiple_cad.id_etapacad'])
                  ->from(['tbl_etapamultiple_cad'])
                  ->where(['=','tbl_etapamultiple_cad.id_directorcad',$id_directorcad])
                  ->scalar();
            }

              Yii::$app->db->createCommand()->update('tbl_directorio_cad',[
                'vicepresidente' => $model->vicepresidente,
                'gerente' => $varidgerente,
                'sociedad' => $model->sociedad,
                'ciudad' => $model->ciudad,
                'sector' => $model->sector, 
                'tipo' => $model->tipo,
                'tipo_canal' => $model->tipo_canal,
                'otro_canal' => $model->otro_canal,
                'fechacreacion' => date("Y-m-d"),                    
                'anulado' => 0,
                'usua_id' => Yii::$app->user->identity->id,
                'proveedores' => $model->proveedores,
                'nom_plataforma' => $model->nom_plataforma,
                'directorprog' => $variddirector,
              ],'id_directorcad ='.$id_directorcad.'')->execute();
             
              if ($model->etapa) {
                foreach ($model->etapa as $key => $value) {
              
                  $varExiste = (new \yii\db\Query())
                            ->select(['tbl_etapamultiple_cad.id_etapamultiplecad'])
                            ->from(['tbl_etapamultiple_cad'])
                            ->where(['=','tbl_etapamultiple_cad.anulado',0])
                            ->andwhere(['=','tbl_etapamultiple_cad.id_directorcad',$id_directorcad])
                            ->andwhere(['=','tbl_etapamultiple_cad.id_etapacad',$value])
                            ->count(); 
  
                
                       
                  if ($varExiste == 0) {
                      Yii::$app->db->createCommand()->insert('tbl_etapamultiple_cad',[
                        'id_directorcad' => $id_directorcad,
                        'id_etapacad' => $value,
                        'fechacreacion' => date("Y-m-d"),                    
                        'anulado' => 0,
                        'usua_id' => Yii::$app->user->identity->id,
                      ])->execute();
                    }   
   
                }
              }            
              $varAlerta = 1;

            return $this->redirect(['index','varAlerta' => base64_encode($varAlerta)]);         
          }else{
            $varAlerta = 2;
            return $this->redirect(['editarusu','varAlerta' => base64_encode($varAlerta),'id_directorcad' => $id_directorcad]);    
          }          
          
        }
        
        return $this->render('editarusu',[
            'model' => $model,
            'id_directorcad' => $id_directorcad,
        ]);  
    }

     public function actionDeletetapa($id_directorcad,$id_etapacad){
      $varparametros = [
        ':varid'=> $id_etapacad,
        ':vardirectorio'=> $id_directorcad
    ];
    Yii::$app->db->createCommand('
          UPDATE tbl_etapamultiple_cad SET id_etapacad = NULL
            WHERE 

            id_etapacad = :varid AND id_directorcad = :vardirectorio')
        ->bindValues($varparametros)
        ->execute();

    return $this->redirect(array('editarusu','id_directorcad'=>$id_directorcad));
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
        $modelSociedad = new SociedadCad();
        $modelVicepresidente = new VicepresidenteCad();
        $modelCiudad = new CiudadCad();
        $modelTipo = new TipoCad();
        $modelSector = new SectorCad();
        $modelTipo_Canal = new TipocanalCad();
        $modelProveedores = new ProveedoresCad();
        $modelEtapa = new EtapaCad();



        


        return $this->render('parametrizar',[
            'model' => $model,
            'modelSociedad' => $modelSociedad,
            'modelVicepresidente' => $modelVicepresidente,
            'modelCiudad' => $modelCiudad,
            'modelTipo' => $modelTipo,
            'modelSector' => $modelSector,
            'modelTipo_Canal' => $modelTipo_Canal,
            'modelProveedores' => $modelProveedores,
            'modelEtapa' => $modelEtapa,
        ]);  
      

  }

  public function actionEliminarcuenta($id_directorcad){

        $varparametros = [
          ':varid'=> $id_directorcad
      ];
      Yii::$app->db->createCommand('
        UPDATE tbl_directorio_cad SET anulado = 1
          WHERE 
          id_directorcad = :varid')
      ->bindValues($varparametros)
      ->execute(); 

    return $this->redirect(array('index','id_directorcad'=>$id_directorcad));//retornar  la vista 
  }

  public function actionIngresarsector(){

    $txtvaridsector = Yii::$app->request->get("txtvaridsector");

    Yii::$app->db->createCommand()->insert('tbl_sector_cad',[
                'nombre' => $txtvaridsector,
                'fechacreacion' => date('Y-m-d'),
                'anulado' => 0,
                'usua_id' => Yii::$app->user->identity->id,                                       
    ])->execute(); 

    die(json_encode($txtvaridsector));

  }

  public function actionEliminarsector($id_sectorcad){

    $varparametros = [
        ':varid'=> $id_sectorcad
    ];
    Yii::$app->db->createCommand('
      UPDATE tbl_sector_cad SET anulado = 1
        WHERE 
        id_sectorcad = :varid')
    ->bindValues($varparametros)
    ->execute(); 

    return $this->redirect(array('parametrizar','id_sectorcad'=>$id_sectorcad));//retornar  la vista 
  }

  public function actionIngresartipo(){

    $txtvaridtipo = Yii::$app->request->get("txtvaridtipo");

    Yii::$app->db->createCommand()->insert('tbl_tipo_cad',[
                'nombre' => $txtvaridtipo,
                'fechacreacion' => date('Y-m-d'),
                'anulado' => 0,
                'usua_id' => Yii::$app->user->identity->id,                                       
    ])->execute(); 

    die(json_encode($txtvaridtipo));

  }

  public function actionEliminartipo($id_tipocad){

      $varparametros = [
        ':varid'=> $id_tipocad
    ];
    Yii::$app->db->createCommand('
      UPDATE tbl_tipo_cad SET anulado = 1
        WHERE 
        id_tipocad = :varid')
    ->bindValues($varparametros)
    ->execute(); 

    return $this->redirect(array('parametrizar','id_tipocad'=>$id_tipocad));//retornar  la vista 
  }

  public function actionIngresarciudad(){

    $txtvaridciudad = Yii::$app->request->get("txtvaridciudad");

    Yii::$app->db->createCommand()->insert('tbl_ciudad_cad',[
                'nombre' => $txtvaridciudad,
                'fechacreacion' => date('Y-m-d'),
                'anulado' => 0,
                'usua_id' => Yii::$app->user->identity->id,                                       
    ])->execute(); 

    die(json_encode($txtvaridciudad));

  }

  public function actionEliminarciudad($id_ciudad_cad){

      $varparametros = [
        ':varid'=> $id_ciudad_cad
    ];
    Yii::$app->db->createCommand('
      UPDATE tbl_ciudad_cad SET anulado = 1
        WHERE 
        id_ciudad_cad = :varid')
    ->bindValues($varparametros)
    ->execute(); 

    return $this->redirect(array('parametrizar','id_ciudad_cad'=>$id_ciudad_cad));//retornar  la vista 
  }

  public function actionIngresartipocanal(){

    $txtvaridtipo_canal = Yii::$app->request->get("txtvaridtipo_canal");

    Yii::$app->db->createCommand()->insert('tbl_tipocanal_cad',[
                'nombre' => $txtvaridtipo_canal,
                'fechacreacion' => date('Y-m-d'),
                'anulado' => 0,
                'usua_id' => Yii::$app->user->identity->id,                                       
    ])->execute(); 

    die(json_encode($txtvaridtipo_canal));

  }

  public function actionEliminartipocanal($id_tipocanalcad){

    $varparametros = [
      ':varid'=> $id_tipocanalcad
    ];
    Yii::$app->db->createCommand('
      UPDATE tbl_tipocanal_cad SET anulado = 1
        WHERE 
        id_tipocanalcad = :varid')
    ->bindValues($varparametros)
    ->execute(); 

    return $this->redirect(array('parametrizar','id_tipocanalcad'=>$id_tipocanalcad));//retornar  la vista 
  }


  public function actionIngresaretapa(){

    $txtvaridetapa = Yii::$app->request->get("txtvaridetapa");

    Yii::$app->db->createCommand()->insert('tbl_etapa_cad',[
                'nombre' => $txtvaridetapa,
                'fechacreacion' => date('Y-m-d'),
                'anulado' => 0,
                'usua_id' => Yii::$app->user->identity->id,                                       
    ])->execute(); 

    die(json_encode($txtvaridetapa));

  }

  public function actionEliminaretapa($id_etapacad){

    $varparametros = [
      ':varid'=> $id_etapacad
    ];
    Yii::$app->db->createCommand('
      UPDATE tbl_etapa_cad SET anulado = 1
        WHERE 
        id_etapacad = :varid')
    ->bindValues($varparametros)
    ->execute(); 

    return $this->redirect(array('parametrizar','id_etapacad'=>$id_etapacad));//retornar  la vista 
  }

  public function actionIngresarvicepresidente(){

    $txtvaridvicepresidente = Yii::$app->request->get("txtvaridvicepresidente");

    Yii::$app->db->createCommand()->insert('tbl_vicepresidente_cad',[
                'nombre' => $txtvaridvicepresidente,
                'fechacreacion' => date('Y-m-d'),
                'anulado' => 0,
                'usua_id' => Yii::$app->user->identity->id,                                       
    ])->execute(); 

    die(json_encode($txtvaridvicepresidente));

  }
  public function actionEliminarvicepresidente($id_vicepresidentecad){

    $varparametros = [
      ':varid'=> $id_vicepresidentecad
    ];
    Yii::$app->db->createCommand('
      UPDATE tbl_vicepresidente_cad SET anulado = 1
        WHERE 
        id_vicepresidentecad = :varid')
    ->bindValues($varparametros)
    ->execute(); 

    return $this->redirect(array('parametrizar','id_vicepresidentecad'=>$id_vicepresidentecad));//retornar  la vista 
  }

  public function actionIngresarproveedores(){

    $txtvaridproveedores = Yii::$app->request->get("txtvaridproveedores");

    Yii::$app->db->createCommand()->insert('tbl_proveedores_cad',[
                'name' => $txtvaridproveedores,
                'fechacreacion' => date('Y-m-d'),
                'anulado' => 0,
                'usua_id' => Yii::$app->user->identity->id,                                       
    ])->execute(); 

    die(json_encode($txtvaridproveedores));

  }

  public function actionEliminarproveedores($id_proveedorescad){

    $varparametros = [
      ':varid'=> $id_proveedorescad
    ];
    Yii::$app->db->createCommand('
      UPDATE tbl_proveedores_cad SET anulado = 1
        WHERE 
        id_proveedorescad = :varid')
    ->bindValues($varparametros)
    ->execute(); 

    return $this->redirect(array('parametrizar','id_proveedorescad'=>$id_proveedorescad));//retornar  la vista 
  }

  public function actionIngresarsociedad(){

    $txtvaridsociedad = Yii::$app->request->get("txtvaridsociedad");

    Yii::$app->db->createCommand()->insert('tbl_sociedad_cad',[
                'nombre' => $txtvaridsociedad,
                'fechacreacion' => date('Y-m-d'),
                'anulado' => 0,
                'usua_id' => Yii::$app->user->identity->id,                                       
    ])->execute(); 

    die(json_encode($txtvaridsociedad));

  }

  public function actionEliminarsociedad($id_sociedadcad){

    $varparametros = [
      ':varid'=> $id_sociedadcad
    ];
    Yii::$app->db->createCommand('
      UPDATE tbl_sociedad_cad SET anulado = 1
        WHERE 
        id_sociedadcad = :varid')
    ->bindValues($varparametros)
    ->execute(); 

    return $this->redirect(array('parametrizar','id_sociedadcad'=>$id_sociedadcad));//retornar  la vista 
  }

 

}

?>


