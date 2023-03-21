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
use app\models\Areasqyr;
use app\models\Usuarios;
use app\models\Tipopqrs;
use app\models\Estadosqyr;
use app\models\HojavidaDatadirector;
use app\models\HojavidaDatagerente;
use app\models\Casosqyr;
use app\models\UploadForm2;
use app\models\UsuariosEvalua;





  class QrController extends \yii\web\Controller {

    public function behaviors(){
      return[
        'access' => [
            'class' => AccessControl::classname(),
            'only' => ['index','viewqyr', 'listartipologia','verqyr'],
            'rules' => [
              [
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isVerdirectivo();
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
    

    public function actionIndex(){ 

        
        $model = (new \yii\db\Query())
                  ->select(['tbl_qr_casos.id as idcaso','tbl_qr_casos.numero_caso','tbl_qr_tipos_de_solicitud.tipo_de_dato','tbl_qr_casos.comentario','tbl_qr_casos.cliente','tbl_qr_casos.nombre','tbl_qr_casos.documento','tbl_qr_casos.correo','tbl_qr_estados_casos.estado','tbl_qr_estados_casos.id as idestado','tbl_qr_casos.fecha_creacion', 'tbl_qr_areas.nombre area','tbl_qr_tipologias.tipologia','tbl_qr_casos.id_estado','tbl_qr_estados.nombre estado1'])
                  ->from(['tbl_qr_casos'])
                  ->join('LEFT OUTER JOIN', 'tbl_qr_tipos_de_solicitud',
                                  'tbl_qr_casos.id_solicitud = tbl_qr_tipos_de_solicitud.id') 
                  ->join('LEFT OUTER JOIN', 'tbl_qr_estados_casos',
                                  'tbl_qr_casos.id_estado_caso = tbl_qr_estados_casos.id')
                  ->join('LEFT JOIN', 'tbl_qr_areas', 'tbl_qr_casos.id_area = tbl_qr_areas.id' )
                  ->join('LEFT JOIN', 'tbl_qr_tipologias', 'tbl_qr_casos.id_tipologia = tbl_qr_tipologias.id')
                  ->join('LEFT JOIN', 'tbl_qr_estados', 'tbl_qr_casos.id_estado = tbl_qr_estados.id_estado')     
                  ->All();

        $varCantidadestados = (new \yii\db\Query())
                  ->select([
                    'tbl_qr_estados.id_estado',
                    'tbl_qr_estados.nombre',
                    'COUNT(tbl_qr_estados.id_estado) Cantidad'
                  ])
                  ->from(['tbl_qr_casos'])
                  ->join('LEFT OUTER JOIN', 'tbl_qr_estados',
                    'tbl_qr_casos.id_estado = tbl_qr_estados.id_estado')
                  ->where(['=','tbl_qr_casos.estatus',0])
                  ->groupBy(['tbl_qr_estados.id_estado'])
                  ->all();

        $varCantidadtranscurre = (new \yii\db\Query())
                  ->select([
                    'if(DATEDIFF( now(),tbl_qr_casos.fecha_creacion) <= 5,1,if(DATEDIFF( now(),tbl_qr_casos.fecha_creacion) >5 && DATEDIFF( now(),tbl_qr_casos.fecha_creacion) <=8,2,if(DATEDIFF( now(),tbl_qr_casos.fecha_creacion) > 8,3,""))) AS num',
                    'DATEDIFF( now(),tbl_qr_casos.fecha_creacion) as dias',
                    'count(if(DATEDIFF( now(),tbl_qr_casos.fecha_creacion) <= 5,1,if(DATEDIFF( now(),tbl_qr_casos.fecha_creacion) >5 && DATEDIFF( now(),tbl_qr_casos.fecha_creacion) <=8,2,if(DATEDIFF( now(),tbl_qr_casos.fecha_creacion) > 8,3,"")))) AS canti'
                  ])
                  ->from(['tbl_qr_casos'])
                  ->groupBy(['num'])
                  ->all();

      
        return $this->render('index',[
            'model' => $model,
            'varCantidadestados' => $varCantidadestados,
            'varCantidadtranscurre' => $varCantidadtranscurre
        ]);
    }

    public function actionViewqyr($idcaso){
      $id_caso = $idcaso;
      $model2 = new Areasqyr();   
      $model8 = new Areasqyr();   
      $model3 = new Areasqyr();
      $model4 = new Tipopqrs();
      $model5 = new Estadosqyr();
      $model6 = new HojavidaDatadirector();
      $model7 = new HojavidaDatagerente();
      
      $txtQuery2 =  new Query;
      $txtQuery2  ->select(['tbl_qr_casos.fecha_creacion','tbl_qr_casos.numero_caso','tbl_qr_clientes.clientes','tbl_qr_casos.nombre','tbl_qr_casos.documento','tbl_qr_casos.correo', 'tbl_qr_areas.nombre area','tbl_qr_tipologias.tipologia', 'tbl_qr_casos.comentario'])
                  ->from('tbl_qr_casos')            
                  ->join('LEFT JOIN', 'tbl_qr_areas', 'tbl_qr_casos.id_area = tbl_qr_areas.id' )
                  ->join('LEFT JOIN', 'tbl_qr_tipologias', 'tbl_qr_casos.id_tipologia = tbl_qr_tipologias.id')
                  ->join('LEFT JOIN', 'tbl_qr_tipos_de_solicitud', 'tbl_qr_casos.id_solicitud = tbl_qr_tipos_de_solicitud.id')
                  ->join('LEFT JOIN', 'tbl_qr_clientes', 'tbl_qr_casos.cliente = tbl_qr_clientes.id')                  
                  ->Where('tbl_qr_casos.id = :id_caso')
                  ->addParams([':id_caso'=>$id_caso]);
     
      $command = $txtQuery2->createCommand();
      $dataProvider = $command->queryAll();

      $txtQuery3 =  new Query;
      $txtQuery3  ->select(['tbl_qr_casos.correo'])
                  ->from('tbl_qr_casos')       
                  ->Where('tbl_qr_casos.id = :id_caso')
                  ->addParams([':id_caso'=>$id_caso]);
     
      $command = $txtQuery3->createCommand();
      $datacorreo = $command->queryScalar();

      $paramsinfo = [':varInfo' => $datacorreo];  
      $dataProviderInfo = Yii::$app->db->createCommand('
              SELECT tbl_hojavida_datapersonal.hv_idpersonal,
              tbl_hojavida_datapcrc.id_dp_cliente AS IdCliente, tbl_hojavida_datapersonal.clasificacion,
              tbl_hojavida_sociedad.sociedad
              FROM tbl_hojavida_datapersonal
              INNER JOIN tbl_hojavida_datapcrc  ON
              tbl_hojavida_datapersonal.hv_idpersonal = tbl_hojavida_datapcrc.hv_idpersonal
              LEFT JOIN tbl_hojavida_datacomplementos ON
              tbl_hojavida_datacomplementos.hv_idpersonal = tbl_hojavida_datapersonal.hv_idpersonal
              LEFT JOIN tbl_hv_estilosocial ON
              tbl_hv_estilosocial.idestilosocial = tbl_hojavida_datacomplementos.idestilosocial
              LEFT JOIN tbl_hojavida_sociedad ON
              tbl_hojavida_sociedad.id_sociedad = tbl_hojavida_datapersonal.id_sociedad
              WHERE
              tbl_hojavida_datapersonal.email = :varInfo
            GROUP BY tbl_hojavida_datapersonal.hv_idpersonal               
            ')->bindValues($paramsinfo)->queryAll();

      $form = Yii::$app->request->post();
      if ($model2->load($form)) {
          $valarea = $model2->id;
      }
      if ($model8->load($form)) {
        $valtipologia = $model8->id;
      }
      if ($model3->load($form)) {
        $valresponsable = $model3->id;
      }     
      if ($model4->load($form)) {
        $valtipoqr = $model4->id;
      }
      if ($model5->load($form)) {
        $valestado = $model5->id_estado;
      }
      if ($model6->load($form)) {
        $valtiporesp = $model6->ccdirector;

        Yii::$app->db->createCommand()->update('tbl_qr_casos',[
          'id_area' => $valarea,
          'id_tipologia' => $valtipologia,
          'id_responsable' => $valresponsable,
          'tipo_respuesta' => $valtiporesp,
          'id_estado' => $valestado,          
        ],"id = '$id_caso'")->execute();                
        return $this->redirect('index');

      }

      return $this->render('viewqyr', [
        'dataprovider' => $dataProvider, 
        'dataProviderInfo' => $dataProviderInfo,    
        'model2' => $model2,
        'model3' => $model3,
        'model4' => $model4,
        'model5' => $model5,
        'model6' => $model6,
        'model7' => $model7,
        'model8' => $model8,
      ]);
      
  }

  public function actionListartipologia(){
    $txtId = Yii::$app->request->post('id');
    if ($txtId) {
     $varListatipologia = (new \yii\db\Query())
        ->select(['tbl_qr_tipologias.id', 'tbl_qr_tipologias.tipologia'])
        ->from(['tbl_qr_tipologias'])
        ->where(['=','tbl_qr_tipologias.id_areas',$txtId])
        ->orderBY ('tbl_qr_tipologias.tipologia')
        ->All();
        echo "<option value='' disabled selected> Seleccionar...</option>";
        foreach ($varListatipologia as $key => $value) {
        echo "<option value='" . $value['id']. "'>" . $value['tipologia']."</option>";
        }
      }

  }
  public function actionCrearqyr(){
    $modelcaso = new Casosqyr(); 
    $model = new UploadForm2();
    $ruta = null;
    
     $form = Yii::$app->request->post();     

    if($model->load($form)){

      $model->file = UploadedFile::getInstance($model, 'file');
      if ($model->file && $model->validate()) {
        foreach ($model->file as $file) {
          $ruta = 'images/documentos/'."qyr_".time()."_".$model->file->baseName. ".".$model->file->extension;
          $model->file->saveAs( $ruta ); 
        }
      } 
      
      }else{
        $ruta = null;
      }

    if ($modelcaso->load($form)) { 
      
      $varnumcaso = (new \yii\db\Query())
      ->select(['MAX(tbl_qr_casos.numero_caso)'])
      ->from(['tbl_qr_casos'])
      ->Scalar();

    $posicion_espacio=strpos($varnumcaso, "-");
    $nombre1=substr($varnumcaso,$posicion_espacio + 1);
    $caso = 'C-'.strval($nombre1+1);
    $estado = 1;
    $estado_new = 9;
    $time = time();


      Yii::$app->db->createCommand()->insert('tbl_qr_casos',[
                  'id_solicitud' => $modelcaso->id_estado_caso,
                  'comentario' => $modelcaso->comentario,
                  'documento' => $modelcaso->documento,
                  'nombre' => $modelcaso->nombre,
                  'correo' => $modelcaso->correo,
                  'cliente' => $modelcaso->cliente,
                  'numero_caso' => $caso,
                  'archivo' => $ruta,
                  'id_estado_caso' => $estado,
                  'id_estado' => $estado_new,
                  'estatus' => 0,
                  'usua_id' => Yii::$app->user->identity->id,                                       
              ])->execute(); 

      return $this->redirect('crearqyr');
    }
    
    return $this->render('crearqyr',[
      'modelcaso' => $modelcaso,
      'model' => $model,
    ]);
  }

  public function actionVerqyr($idcaso){
    $id_caso = $idcaso;
    $model2 = new Areasqyr();   
    $model8 = new Areasqyr();   
    $model3 = new usuarios();
    $model4 = new Tipopqrs();
    $model5 = new Estadosqyr();
    $model6 = new HojavidaDatadirector();
    $model7 = new HojavidaDatagerente();
    
    $txtQuery2 =  new Query;
    $txtQuery2  ->select(['tbl_qr_casos.fecha_creacion','tbl_qr_casos.numero_caso','tbl_qr_clientes.clientes','tbl_qr_casos.nombre','tbl_qr_casos.documento','tbl_qr_casos.correo', 'tbl_qr_areas.nombre area','tbl_qr_tipologias.tipologia', 'tbl_qr_casos.comentario'])
                ->from('tbl_qr_casos')            
                ->join('LEFT JOIN', 'tbl_qr_areas', 'tbl_qr_casos.id_area = tbl_qr_areas.id' )
                ->join('LEFT JOIN', 'tbl_qr_tipologias', 'tbl_qr_casos.id_tipologia = tbl_qr_tipologias.id')
                ->join('LEFT JOIN', 'tbl_qr_tipos_de_solicitud', 'tbl_qr_casos.id_solicitud = tbl_qr_tipos_de_solicitud.id')
                ->join('LEFT JOIN', 'tbl_qr_clientes', 'tbl_qr_casos.cliente = tbl_qr_clientes.id')                  
                ->Where('tbl_qr_casos.id = :id_caso')
                ->addParams([':id_caso'=>$id_caso]);
   
    $command = $txtQuery2->createCommand();
    $dataProvider = $command->queryAll();

    $txtQuery3 =  new Query;
    $txtQuery3  ->select(['tbl_qr_casos.correo'])
                ->from('tbl_qr_casos')       
                ->Where('tbl_qr_casos.id = :id_caso')
                ->addParams([':id_caso'=>$id_caso]);
   
    $command = $txtQuery3->createCommand();
    $datacorreo = $command->queryScalar();

    $paramsinfo = [':varInfo' => $datacorreo];  
    $dataProviderInfo = Yii::$app->db->createCommand('
            SELECT tbl_hojavida_datapersonal.hv_idpersonal,
            tbl_hojavida_datapcrc.id_dp_cliente AS IdCliente, tbl_hojavida_datapersonal.clasificacion,
            tbl_hojavida_sociedad.sociedad
            FROM tbl_hojavida_datapersonal
            INNER JOIN tbl_hojavida_datapcrc  ON
            tbl_hojavida_datapersonal.hv_idpersonal = tbl_hojavida_datapcrc.hv_idpersonal
            LEFT JOIN tbl_hojavida_datacomplementos ON
            tbl_hojavida_datacomplementos.hv_idpersonal = tbl_hojavida_datapersonal.hv_idpersonal
            LEFT JOIN tbl_hv_estilosocial ON
            tbl_hv_estilosocial.idestilosocial = tbl_hojavida_datacomplementos.idestilosocial
            LEFT JOIN tbl_hojavida_sociedad ON
            tbl_hojavida_sociedad.id_sociedad = tbl_hojavida_datapersonal.id_sociedad
            WHERE
            tbl_hojavida_datapersonal.email = :varInfo
          GROUP BY tbl_hojavida_datapersonal.hv_idpersonal               
          ')->bindValues($paramsinfo)->queryAll();

    return $this->render('verqyr', [
      'dataprovider' => $dataProvider, 
      'dataProviderInfo' => $dataProviderInfo,    
      'model2' => $model2,
      'model3' => $model3,
      'model4' => $model4,
      'model5' => $model5,
      'model6' => $model6,
      'model7' => $model7,
      'model8' => $model8,
    ]);
    
}

public function actionGestionqyr($idcaso){
  $id_caso = $idcaso;  
  $model = new UploadForm2();
  $model2 = new Areasqyr();   
  $model8 = new Areasqyr();   
  $model3 = new Areasqyr();
  $model4 = new Tipopqrs();
  $model5 = new Estadosqyr();
  $model6 = new HojavidaDatadirector();
  $model7 = new HojavidaDatagerente();
  
  $txtQuery2 =  new Query;
  $txtQuery2  ->select(['tbl_qr_casos.fecha_creacion','tbl_qr_casos.numero_caso','tbl_qr_clientes.clientes','tbl_qr_casos.nombre','tbl_qr_casos.documento','tbl_qr_casos.correo', 'tbl_qr_areas.nombre area','tbl_qr_tipologias.tipologia', 'tbl_qr_casos.comentario', 'tbl_usuarios.usua_nombre','tbl_qr_casos.tipo_respuesta','tbl_qr_tipos_de_solicitud.tipo_de_dato','tbl_qr_estados.nombre estado'])
              ->from('tbl_qr_casos')            
              ->join('LEFT JOIN', 'tbl_qr_areas', 'tbl_qr_casos.id_area = tbl_qr_areas.id' )
              ->join('LEFT JOIN', 'tbl_qr_tipologias', 'tbl_qr_casos.id_tipologia = tbl_qr_tipologias.id')
              ->join('LEFT JOIN', 'tbl_qr_tipos_de_solicitud', 'tbl_qr_casos.id_solicitud = tbl_qr_tipos_de_solicitud.id')
              ->join('LEFT JOIN', 'tbl_qr_clientes', 'tbl_qr_casos.cliente = tbl_qr_clientes.id')
              ->join('LEFT JOIN', 'tbl_usuarios', 'tbl_qr_casos.id_responsable = tbl_usuarios.usua_id')
              ->join('LEFT JOIN', 'tbl_qr_estados', 'tbl_qr_casos.id_estado = tbl_qr_estados.id_estado')                  
              ->Where('tbl_qr_casos.id = :id_caso')
              ->addParams([':id_caso'=>$id_caso]);
 
  $command = $txtQuery2->createCommand();
  $dataProvider = $command->queryAll();

  $txtQuery3 =  new Query;
  $txtQuery3  ->select(['tbl_qr_casos.correo'])
              ->from('tbl_qr_casos')       
              ->Where('tbl_qr_casos.id = :id_caso')
              ->addParams([':id_caso'=>$id_caso]);
 
  $command = $txtQuery3->createCommand();
  $datacorreo = $command->queryScalar();

  $paramsinfo = [':varInfo' => $datacorreo];  
  $dataProviderInfo = Yii::$app->db->createCommand('
          SELECT tbl_hojavida_datapersonal.hv_idpersonal,
          tbl_hojavida_datapcrc.id_dp_cliente AS IdCliente, tbl_hojavida_datapersonal.clasificacion,
          tbl_hojavida_sociedad.sociedad
          FROM tbl_hojavida_datapersonal
          INNER JOIN tbl_hojavida_datapcrc  ON
          tbl_hojavida_datapersonal.hv_idpersonal = tbl_hojavida_datapcrc.hv_idpersonal
          LEFT JOIN tbl_hojavida_datacomplementos ON
          tbl_hojavida_datacomplementos.hv_idpersonal = tbl_hojavida_datapersonal.hv_idpersonal
          LEFT JOIN tbl_hv_estilosocial ON
          tbl_hv_estilosocial.idestilosocial = tbl_hojavida_datacomplementos.idestilosocial
          LEFT JOIN tbl_hojavida_sociedad ON
          tbl_hojavida_sociedad.id_sociedad = tbl_hojavida_datapersonal.id_sociedad
          WHERE
          tbl_hojavida_datapersonal.email = :varInfo
        GROUP BY tbl_hojavida_datapersonal.hv_idpersonal               
        ')->bindValues($paramsinfo)->queryAll();

  $form = Yii::$app->request->post();    
                  
  if($model->load($form)){
                  
     $model->file = UploadedFile::getInstance($model, 'file');
   if ($model->file && $model->validate()) {
                         
      foreach ($model->file as $file) {
          $ruta = 'images/uploads/'."qyr"."_".time()."_".$model->file->baseName. ".".$model->file->extension;
           $model->file->saveAs( $ruta ); 
       }
   } 
  }

  if ($model2->load($form)) {
      $valarea = $model2->id;
  }
  if ($model8->load($form)) {
    $valtipologia = $model8->id;
  }
  if ($model3->load($form)) {
    $valcomentario = $model3->nombre;  
    $valresponsable = $model3->id;
    $valestado = 8;
  

    Yii::$app->db->createCommand()->update('tbl_qr_casos',[
      'id_area' => $valarea,
      'id_tipologia' => $valtipologia,
      'id_responsable' => $valresponsable,
      'comentario2' => $valcomentario,
      'id_estado' => $valestado,
      'archivo2' => $ruta,
                
    ],"id = '$id_caso'")->execute();                
    return $this->redirect('index');

  }

  return $this->render('gestionqyr', [
    'dataprovider' => $dataProvider, 
    'dataProviderInfo' => $dataProviderInfo,   
    'model' => $model, 
    'model2' => $model2,
    'model3' => $model3,
    'model4' => $model4,
    'model5' => $model5,
    'model6' => $model6,
    'model7' => $model7,
    'model8' => $model8,
  ]);
  
}

public function actionRevisionqyr($idcaso){
  $id_caso = $idcaso;
  $model2 = new Areasqyr();   
  $model8 = new Areasqyr();   
  $model3 = new Areasqyr();
  $model4 = new Tipopqrs();
  $model5 = new Estadosqyr();
  $model6 = new HojavidaDatadirector();
  $model7 = new HojavidaDatagerente();
  
  $txtQuery2 =  new Query;
  $txtQuery2  ->select(['tbl_qr_casos.fecha_creacion','tbl_qr_casos.numero_caso','tbl_qr_clientes.clientes','tbl_qr_casos.nombre','tbl_qr_casos.documento','tbl_qr_casos.correo', 'tbl_qr_areas.nombre area','tbl_qr_tipologias.tipologia', 'tbl_qr_casos.comentario', 'tbl_usuarios.usua_nombre','tbl_qr_casos.tipo_respuesta','tbl_qr_tipos_de_solicitud.tipo_de_dato','tbl_qr_estados.nombre estado','tbl_qr_casos.comentario2','tbl_qr_casos.archivo2'])
              ->from('tbl_qr_casos')            
              ->join('LEFT JOIN', 'tbl_qr_areas', 'tbl_qr_casos.id_area = tbl_qr_areas.id' )
              ->join('LEFT JOIN', 'tbl_qr_tipologias', 'tbl_qr_casos.id_tipologia = tbl_qr_tipologias.id')
              ->join('LEFT JOIN', 'tbl_qr_tipos_de_solicitud', 'tbl_qr_casos.id_solicitud = tbl_qr_tipos_de_solicitud.id')
              ->join('LEFT JOIN', 'tbl_qr_clientes', 'tbl_qr_casos.cliente = tbl_qr_clientes.id')
              ->join('LEFT JOIN', 'tbl_usuarios', 'tbl_qr_casos.id_responsable = tbl_usuarios.usua_id')
              ->join('LEFT JOIN', 'tbl_qr_estados', 'tbl_qr_casos.id_estado = tbl_qr_estados.id_estado')                  
              ->Where('tbl_qr_casos.id = :id_caso')
              ->addParams([':id_caso'=>$id_caso]);
 
  $command = $txtQuery2->createCommand();
  $dataProvider = $command->queryAll();

  $txtQuery3 =  new Query;
  $txtQuery3  ->select(['tbl_qr_casos.correo'])
              ->from('tbl_qr_casos')       
              ->Where('tbl_qr_casos.id = :id_caso')
              ->addParams([':id_caso'=>$id_caso]);
 
  $command = $txtQuery3->createCommand();
  $datacorreo = $command->queryScalar();

  $txtQuery4 =  new Query;
  $txtQuery4  ->select(['tbl_qr_casos.archivo2'])
              ->from('tbl_qr_casos')       
              ->Where('tbl_qr_casos.id = :id_caso')
              ->addParams([':id_caso'=>$id_caso]);
 
  $command = $txtQuery4->createCommand();
  $dataanexo = $command->queryScalar();

  $paramsinfo = [':varInfo' => $datacorreo];  
  $dataProviderInfo = Yii::$app->db->createCommand('
          SELECT tbl_hojavida_datapersonal.hv_idpersonal,
          tbl_hojavida_datapcrc.id_dp_cliente AS IdCliente, tbl_hojavida_datapersonal.clasificacion,
          tbl_hojavida_sociedad.sociedad
          FROM tbl_hojavida_datapersonal
          INNER JOIN tbl_hojavida_datapcrc  ON
          tbl_hojavida_datapersonal.hv_idpersonal = tbl_hojavida_datapcrc.hv_idpersonal
          LEFT JOIN tbl_hojavida_datacomplementos ON
          tbl_hojavida_datacomplementos.hv_idpersonal = tbl_hojavida_datapersonal.hv_idpersonal
          LEFT JOIN tbl_hv_estilosocial ON
          tbl_hv_estilosocial.idestilosocial = tbl_hojavida_datacomplementos.idestilosocial
          LEFT JOIN tbl_hojavida_sociedad ON
          tbl_hojavida_sociedad.id_sociedad = tbl_hojavida_datapersonal.id_sociedad
          WHERE
          tbl_hojavida_datapersonal.email = :varInfo
        GROUP BY tbl_hojavida_datapersonal.hv_idpersonal               
        ')->bindValues($paramsinfo)->queryAll();

  $form = Yii::$app->request->post();
  if ($model6->load($form)) {
      $valrespuesta = $model6->ccdirector;  
      $valestado = 5;
      if($valrespuesta =="Aprobada"){

        $ruta = $dataanexo;
        //$tmpFile = "images/Alertas_Satu.jpg";
        $tmpFile = $ruta;
                
                $message = "<html><body>";
                $message .= "<h3>CX-MANAGEMENT</h3>";   
                $message .= "Hola, Te enviamos la respuesta de tu caso.";
                $message .= "<br> Gracias por tu espera, tenemos respuesta a tu caso. Esperamos tu revisión y aceptación de la misma.";             
                $message .= "</body></html>";

                Yii::$app->mailer->compose()
                    ->setTo('diego.montoya@grupokonecta.com')
                    ->setFrom(Yii::$app->params['email_satu_from'])
                    ->setSubject("Actualización de tu caso QyR - CX-MANAGEMENT")                    
                    ->attach($tmpFile)
                    ->setHtmlBody($message)
                    ->send();
//envio de correo al gerente con anexo
      } else{
//envio de correo al que envio respuesta con anexo
      }

    Yii::$app->db->createCommand()->update('tbl_qr_casos',[     
      'id_estado' => $valestado,          
    ],"id = '$id_caso'")->execute();                
    return $this->redirect('index');

  }

  return $this->render('revisionqyr', [
    'dataprovider' => $dataProvider, 
    'dataProviderInfo' => $dataProviderInfo,    
    'model2' => $model2,
    'model3' => $model3,
    'model4' => $model4,
    'model5' => $model5,
    'model6' => $model6,
    'model7' => $model7,
    'model8' => $model8,
  ]);
  
}

public function actionRevisiongerenteqyr($idcaso){
  $id_caso = $idcaso;
  $model2 = new Areasqyr();   
  $model8 = new Areasqyr();   
  $model3 = new Areasqyr();
  $model4 = new Tipopqrs();
  $model5 = new Estadosqyr();
  $model6 = new HojavidaDatadirector();
  $model7 = new HojavidaDatagerente();
  
  $txtQuery2 =  new Query;
  $txtQuery2  ->select(['tbl_qr_casos.fecha_creacion','tbl_qr_casos.numero_caso','tbl_qr_clientes.clientes','tbl_qr_casos.nombre','tbl_qr_casos.documento','tbl_qr_casos.correo', 'tbl_qr_areas.nombre area','tbl_qr_tipologias.tipologia', 'tbl_qr_casos.comentario', 'tbl_usuarios.usua_nombre','tbl_qr_casos.tipo_respuesta','tbl_qr_tipos_de_solicitud.tipo_de_dato','tbl_qr_estados.nombre estado','tbl_qr_casos.comentario2','tbl_qr_casos.archivo2'])
              ->from('tbl_qr_casos')            
              ->join('LEFT JOIN', 'tbl_qr_areas', 'tbl_qr_casos.id_area = tbl_qr_areas.id' )
              ->join('LEFT JOIN', 'tbl_qr_tipologias', 'tbl_qr_casos.id_tipologia = tbl_qr_tipologias.id')
              ->join('LEFT JOIN', 'tbl_qr_tipos_de_solicitud', 'tbl_qr_casos.id_solicitud = tbl_qr_tipos_de_solicitud.id')
              ->join('LEFT JOIN', 'tbl_qr_clientes', 'tbl_qr_casos.cliente = tbl_qr_clientes.id')
              ->join('LEFT JOIN', 'tbl_usuarios', 'tbl_qr_casos.id_responsable = tbl_usuarios.usua_id')
              ->join('LEFT JOIN', 'tbl_qr_estados', 'tbl_qr_casos.id_estado = tbl_qr_estados.id_estado')                  
              ->Where('tbl_qr_casos.id = :id_caso')
              ->addParams([':id_caso'=>$id_caso]);
 
  $command = $txtQuery2->createCommand();
  $dataProvider = $command->queryAll();

  $txtQuery3 =  new Query;
  $txtQuery3  ->select(['tbl_qr_casos.correo'])
              ->from('tbl_qr_casos')       
              ->Where('tbl_qr_casos.id = :id_caso')
              ->addParams([':id_caso'=>$id_caso]);
 
  $command = $txtQuery3->createCommand();
  $datacorreo = $command->queryScalar();

  $paramsinfo = [':varInfo' => $datacorreo];  
  $dataProviderInfo = Yii::$app->db->createCommand('
          SELECT tbl_hojavida_datapersonal.hv_idpersonal,
          tbl_hojavida_datapcrc.id_dp_cliente AS IdCliente, tbl_hojavida_datapersonal.clasificacion,
          tbl_hojavida_sociedad.sociedad
          FROM tbl_hojavida_datapersonal
          INNER JOIN tbl_hojavida_datapcrc  ON
          tbl_hojavida_datapersonal.hv_idpersonal = tbl_hojavida_datapcrc.hv_idpersonal
          LEFT JOIN tbl_hojavida_datacomplementos ON
          tbl_hojavida_datacomplementos.hv_idpersonal = tbl_hojavida_datapersonal.hv_idpersonal
          LEFT JOIN tbl_hv_estilosocial ON
          tbl_hv_estilosocial.idestilosocial = tbl_hojavida_datacomplementos.idestilosocial
          LEFT JOIN tbl_hojavida_sociedad ON
          tbl_hojavida_sociedad.id_sociedad = tbl_hojavida_datapersonal.id_sociedad
          WHERE
          tbl_hojavida_datapersonal.email = :varInfo
        GROUP BY tbl_hojavida_datapersonal.hv_idpersonal               
        ')->bindValues($paramsinfo)->queryAll();

  $form = Yii::$app->request->post();
  if ($model6->load($form)) {
      $valrespuesta = $model6->ccdirector;  
      $valestado = 2;
      if($valrespuesta =="Aprobada"){
//envio de correo al gerente con anexo
      } else{
//envio de correo al que envio respuesta con anexo
      }

    Yii::$app->db->createCommand()->update('tbl_qr_casos',[     
      'id_estado' => $valestado,          
    ],"id = '$id_caso'")->execute();                
    return $this->redirect('index');

  }

  return $this->render('revisiongerenteqyr', [
    'dataprovider' => $dataProvider, 
    'dataProviderInfo' => $dataProviderInfo,    
    'model2' => $model2,
    'model3' => $model3,
    'model4' => $model4,
    'model5' => $model5,
    'model6' => $model6,
    'model7' => $model7,
    'model8' => $model8,
  ]);
  
}

public function actionViewimage(){
  $varRuta = 'image/uploads/Carta respuesta Q&R.docx';
  
  return $this->render('viewimage', [
    'varRuta'=> $varRuta, 
   ]);

}

public function actionVeranexometri($id){
  $model = new UploadForm2();
  $ruta = null;
  $ruta = 'image/uploads/Carta respuesta Q&R.docx';
  return $this->renderAjax('veranexometri',[ 
    'model' => $model,       
    'ruta' => $ruta,
  ]);
}

    public function actionPruebasenvio(){

      $tmpFile = 'images/uploads/qyr_1679417953_Carta respuesta Q&R procesada.pdf';  

                $message = "<html><body>";
                $message .= "<h3>CX-MANAGEMENT</h3>";   
                $message .= "Hola, Te enviamos la respuesta de tu caso.";
                $message .= "<br> Gracias por tu espera, tenemos respuesta a tu caso. Esperamos tu revisión y aceptación de la misma.";             
                $message .= "</body></html>";

                Yii::$app->mailer->compose()
                    ->setTo('diego.montoya@grupokonecta.com','anmorenoa@grupokonecta.com')
                    ->setFrom(Yii::$app->params['email_satu_from'])
                    ->setSubject("Actualización de tu caso QyR - CX-MANAGEMENT")                    
                    ->attach($tmpFile)
                    ->setHtmlBody($message)
                    ->send();

      return $this->render('index');
    }

  }

?>
