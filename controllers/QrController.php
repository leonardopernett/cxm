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

      $sessiones = Yii::$app->user->identity->id;
     
      if($sessiones == "3205" || $sessiones == "2953" || $sessiones == "57" || $sessiones == "1475" || $sessiones == "69" || $sessiones == "1699" || $sessiones == "5658" || $sessiones == "3468" || $sessiones == "7952"){ 
        $model = (new \yii\db\Query())
                  ->select(['tbl_qr_casos.id as idcaso','tbl_qr_casos.numero_caso','tbl_qr_tipos_de_solicitud.tipo_de_dato','tbl_qr_casos.comentario','tbl_qr_casos.cliente','tbl_usuarios_evalua.clientearea','tbl_qr_casos.nombre','tbl_qr_casos.documento','tbl_qr_casos.correo','tbl_qr_estados_casos.estado','tbl_qr_estados_casos.id as idestado','tbl_qr_casos.fecha_creacion', 'tbl_qr_areas.nombre area','tbl_qr_tipologias.tipologia','tbl_qr_casos.id_estado','tbl_qr_estados.nombre estado1'])
                  ->from(['tbl_qr_casos'])
                  ->join('LEFT OUTER JOIN', 'tbl_qr_tipos_de_solicitud',
                                  'tbl_qr_casos.id_solicitud = tbl_qr_tipos_de_solicitud.id') 
                  ->join('LEFT OUTER JOIN', 'tbl_qr_estados_casos',
                                  'tbl_qr_casos.id_estado_caso = tbl_qr_estados_casos.id')
                  ->join('LEFT JOIN', 'tbl_qr_areas', 'tbl_qr_casos.id_area = tbl_qr_areas.id' )
                  ->join('LEFT JOIN', 'tbl_qr_tipologias', 'tbl_qr_casos.id_tipologia = tbl_qr_tipologias.id')
                  ->join('LEFT JOIN', 'tbl_qr_estados', 'tbl_qr_casos.id_estado = tbl_qr_estados.id_estado')
                  ->join('LEFT JOIN', 'tbl_usuarios_evalua', 'tbl_qr_casos.cliente = tbl_usuarios_evalua.idusuarioevalua')     
                  ->All();
      }else{
        $varcedulajefe = (new \yii\db\Query())
              ->select(['usua_identificacion'])
              ->from(['tbl_usuarios'])
              ->where(['=','usua_id',$sessiones])
              ->Scalar();
       
        $varcedulas = (new \yii\db\Query())
              ->select(['documento'])
              ->from(['tbl_usuarios_jarvis_cliente'])
              ->where(['=','documento_jefe',$varcedulajefe])
              ->All();
                
          $arralistacedula = array();
          foreach ($varcedulas as $key => $value) {
            array_push($arralistacedula, $value['documento']);
          }
          $arralistacedula2 = implode(", ", $arralistacedula);

          $dataCedulas = explode(",", str_replace(array("#", "'", ";", " "), '', $arralistacedula2));

        $model = (new \yii\db\Query())
              ->select(['tbl_qr_casos.id as idcaso','tbl_qr_casos.numero_caso','tbl_qr_tipos_de_solicitud.tipo_de_dato','tbl_qr_casos.comentario','tbl_qr_casos.cliente','tbl_usuarios_evalua.clientearea','tbl_qr_casos.nombre','tbl_qr_casos.documento','tbl_qr_casos.correo','tbl_qr_estados_casos.estado','tbl_qr_estados_casos.id as idestado','tbl_qr_casos.fecha_creacion', 'tbl_qr_areas.nombre area','tbl_qr_tipologias.tipologia','tbl_qr_casos.id_estado','tbl_qr_estados.nombre estado1'])
              ->from(['tbl_qr_casos'])
              ->join('LEFT OUTER JOIN', 'tbl_qr_tipos_de_solicitud',
                              'tbl_qr_casos.id_solicitud = tbl_qr_tipos_de_solicitud.id') 
              ->join('LEFT OUTER JOIN', 'tbl_qr_estados_casos',
                              'tbl_qr_casos.id_estado_caso = tbl_qr_estados_casos.id')
              ->join('LEFT JOIN', 'tbl_qr_areas', 'tbl_qr_casos.id_area = tbl_qr_areas.id' )
              ->join('LEFT JOIN', 'tbl_qr_tipologias', 'tbl_qr_casos.id_tipologia = tbl_qr_tipologias.id')
              ->join('LEFT JOIN', 'tbl_qr_estados', 'tbl_qr_casos.id_estado = tbl_qr_estados.id_estado')
              ->join('LEFT JOIN', 'tbl_usuarios_evalua', 'tbl_qr_casos.cliente = tbl_usuarios_evalua.idusuarioevalua')
              ->where(['in','tbl_qr_casos.documento',$dataCedulas])     
              ->All();

      }
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
                  ->where(['<>','tbl_qr_casos.id_estado',2])
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
      $model2 = new Casosqyr();   
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
          $valarea = $model2->id_area;
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
      $valestado = 4;
      if ($model6->load($form)) {
        $valtiporesp = $model6->ccdirector;

        Yii::$app->db->createCommand()->update('tbl_qr_casos',[
          'id_area' => $valarea,
          'id_tipologia' => $valtipologia,
          'id_responsable' => $valresponsable,
          'tipo_respuesta' => $valtiporesp,
          'id_estado' => $valestado,          
        ],"id = '$id_caso'")->execute();                
       

        
        $varNumcaso = (new \yii\db\Query())
                ->select(['numero_caso'])
                ->from(['tbl_qr_casos'])
                ->where(['=','id',$id_caso])
                ->Scalar();
        $varasunto = (new \yii\db\Query())
                ->select(['asunto'])
                ->from(['tbl_qr_respuesta_automatica'])
                ->where(['=','id_estado',4])
                ->Scalar();
        $varcuerpo1 = (new \yii\db\Query())
                ->select(['comentario'])
                ->from(['tbl_qr_respuesta_automatica'])
                ->where(['=','id_estado',4])
                ->Scalar();
        $varcuerpo2 = (new \yii\db\Query())
                ->select(['comentario2'])
                ->from(['tbl_qr_respuesta_automatica'])
                ->where(['=','id_estado',4])
                ->Scalar();
        $vardocumentojefe = (new \yii\db\Query())
                ->select(['usua_identificacion'])
                ->from(['tbl_usuarios'])
                ->where(['=','usua_id',$valresponsable])
                ->Scalar();
        $varcorreo = (new \yii\db\Query())
                ->select(['email_corporativo'])
                ->from(['tbl_usuarios_jarvis_cliente'])
                ->where(['=','documento',$vardocumentojefe])
                ->Scalar();

        //envio de correo a responsable
        $message = "<html><body>";
        $message .= "<h3>CX-Management</h3>";   
        $message .= $varcuerpo1;
        $message .= $varNumcaso;
        $message .= $varcuerpo2;             
        $message .= "<br><br>Que tengas un buen día";
        $message .= "<br><br><h3>Equipo Experiencia de Clientes - Konecta</h3>";
        $message .= "<br>https://qa.grupokonecta.local/qa_managementv2/web/index.php";
        $message .= "</body></html>";

        Yii::$app->mailer->compose()
        ->setTo($varcorreo)
        ->setFrom(Yii::$app->params['email_satu_from'])
        ->setSubject($varasunto)
        ->setHtmlBody($message)
        ->send();

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
      
      $varmaxid = (new \yii\db\Query())
        ->select(['MAX(tbl_qr_casos.id)'])
        ->from(['tbl_qr_casos'])
        ->Scalar();
      $varnumcaso = (new \yii\db\Query())
        ->select(['tbl_qr_casos.numero_caso'])
        ->from(['tbl_qr_casos'])
        ->where(['=','id',$varmaxid])
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

             
              $varasunto = (new \yii\db\Query())
                              ->select(['asunto'])
                              ->from(['tbl_qr_respuesta_automatica'])
                              ->where(['=','id_estado',9])
                              ->Scalar();
              $varcuerpo1 = (new \yii\db\Query())
                              ->select(['comentario'])
                              ->from(['tbl_qr_respuesta_automatica'])
                              ->where(['=','id_estado',9])
                              ->Scalar();
              $varcuerpo2 = (new \yii\db\Query())
                              ->select(['comentario2'])
                              ->from(['tbl_qr_respuesta_automatica'])
                              ->where(['=','id_estado',9])
                              ->Scalar();

//se envia correo al solicitante
                $message = "<html><body>";
                $message .= "<h3>CX-Management</h3>";   
                $message .= $varcuerpo1;
                $message .= $caso;
                $message .= "<br><br>";
                $message .= $varcuerpo2;             
                $message .= "<br><br>Que tengas un buen día";
                $message .= "<br><br><h3>Equipo Experiencia de Clientes - Konecta</h3>";
                $message .= "</body></html>";

                Yii::$app->mailer->compose()
                    ->setTo($modelcaso->correo)
                    ->setFrom(Yii::$app->params['email_satu_from'])
                    ->setSubject($varasunto)
                    ->setHtmlBody($message)
                    ->send();

                // correo para grupo CX   
                $message = "<html><body>";
                $message .= "<h3>CX-Management</h3>";
                $message .= "<br>Buen día Equipo ";
                $message .= "<br><br> Tenemos una nueva solicitud la cual fue recibida el día de hoy, con N° de caso: ";
                $message .= $caso;
                $message .= "<br><br> En espera de iniciar el proceso de asignación por parte del Equipo CX.";             
                $message .= "<br><br>Se adjunta el detalle de la PQRS";
                $message .= "<br><br>Feliz Día";
                $message .= "</body></html>"; 
                $varListacorreo = (new \yii\db\Query())
                  ->select(['email'])
                  ->from(['tbl_qr_correos'])
                  ->All(); 
             
              foreach ($varListacorreo as $key => $value) {
                if ($ruta){
                  Yii::$app->mailer->compose()
                  ->setTo($value['email'])
                  ->setFrom(Yii::$app->params['email_satu_from'])
                  ->setSubject("Respuesta nuevo caso QyR - CX-Management")   
                  // revisar anexo    
                  ->attach($ruta)
                  ->setHtmlBody($message)
                  ->send();
                }else{
                  Yii::$app->mailer->compose()
                  ->setTo($value['email'])
                  ->setFrom(Yii::$app->params['email_satu_from'])
                  ->setSubject("Respuesta nuevo caso QyR - CX-Management")                     
                  ->setHtmlBody($message)
                  ->send();
                }
              }

      return $this->redirect('index');
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
    
    /*$txtQuery2 =  new Query;
    $txtQuery2  ->select(['tbl_qr_casos.fecha_creacion','tbl_qr_casos.numero_caso','tbl_qr_clientes.clientes','tbl_qr_casos.nombre','tbl_qr_casos.documento','tbl_qr_casos.correo', 'tbl_qr_areas.nombre area','tbl_qr_tipologias.tipologia', 'tbl_qr_casos.comentario'])
                ->from('tbl_qr_casos')            
                ->join('LEFT JOIN', 'tbl_qr_areas', 'tbl_qr_casos.id_area = tbl_qr_areas.id' )
                ->join('LEFT JOIN', 'tbl_qr_tipologias', 'tbl_qr_casos.id_tipologia = tbl_qr_tipologias.id')
                ->join('LEFT JOIN', 'tbl_qr_tipos_de_solicitud', 'tbl_qr_casos.id_solicitud = tbl_qr_tipos_de_solicitud.id')
                ->join('LEFT JOIN', 'tbl_qr_clientes', 'tbl_qr_casos.cliente = tbl_qr_clientes.id')                  
                ->Where('tbl_qr_casos.id = :id_caso')
                ->addParams([':id_caso'=>$id_caso]);
   
    $command = $txtQuery2->createCommand();
    $dataProvider = $command->queryAll();*/

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
  //Estado en Proceso
  $id_caso = $idcaso;  
  $model = new UploadForm2();
  $model2 = new Casosqyr();   
  $model8 = new Areasqyr();   
  $model3 = new Casosqyr();
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

  $txtQuery4 =  new Query;
  $txtQuery4  ->select(['tbl_qr_casos.archivo2'])
              ->from('tbl_qr_casos')       
              ->Where('tbl_qr_casos.id = :id_caso')
              ->addParams([':id_caso'=>$id_caso]);
 
  $command = $txtQuery4->createCommand();
  $dataanexo = $command->queryScalar();

  $txtQuery5 =  new Query;
  $txtQuery5  ->select(['tbl_qr_casos.numero_caso'])
              ->from('tbl_qr_casos')       
              ->Where('tbl_qr_casos.id = :id_caso')
              ->addParams([':id_caso'=>$id_caso]);
            
  $command = $txtQuery5->createCommand();
  $datanumcaso = $command->queryScalar();
  

  /*$varcuerpo1 = (new \yii\db\Query())
                              ->select(['asunto'])
                              ->from(['tbl_qr_respuesta_automatica'])
                              ->where(['=','id_estado',1])
                              ->Scalar();
  $varcuerpo2 = (new \yii\db\Query())
                              ->select(['comentario'])
                              ->from(['tbl_qr_respuesta_automatica'])
                              ->where(['=','id_estado',1])
                              ->Scalar();*/

        $varNumcaso = (new \yii\db\Query())
                ->select(['numero_caso'])
                ->from(['tbl_qr_casos'])
                ->where(['=','id',$id_caso])
                ->Scalar();
        $varasunto = (new \yii\db\Query())
                ->select(['asunto'])
                ->from(['tbl_qr_respuesta_automatica'])
                ->where(['=','id_estado',8])
                ->Scalar();
        $varcuerpo1 = (new \yii\db\Query())
                ->select(['comentario'])
                ->from(['tbl_qr_respuesta_automatica'])
                ->where(['=','id_estado',8])
                ->Scalar();
        $varcuerpo2 = (new \yii\db\Query())
                ->select(['comentario2'])
                ->from(['tbl_qr_respuesta_automatica'])
                ->where(['=','id_estado',8])
                ->Scalar();
       

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
      $valarea = $model2->id_area;
  }
  if ($model8->load($form)) {
    $valtipologia = $model8->id;
  }
  if ($model3->load($form)) {
    $valcomentario = $model3->nombre;  
    $valresponsable = $model3->id_solicitud;
    $valestado = 8;
  

    Yii::$app->db->createCommand()->update('tbl_qr_casos',[
      'id_area' => $valarea,
      'id_tipologia' => $valtipologia,
      'id_responsable' => $valresponsable,
      'comentario2' => $valcomentario,
      'id_estado' => $valestado,
      'archivo2' => $ruta,
                
    ],"id = '$id_caso'")->execute();
    
  //envio de correo  equipo cx 
    $tmpFile = $ruta;
                
                $message = "<html><body>";
                $message .= "<h3>CX-Management</h3>";
                $message .= "Buen día Equipo <br><br>";
                $message .= $varcuerpo1;
                $message .= $datanumcaso;
                $message .= $varcuerpo2;             
                $message .= "<br><br>Que tengas un buen día";
                $message .= "<br><br><h3>Equipo Experiencia de Clientes - Konecta</h3>";
                $message .= "<br>https://qa.grupokonecta.local/qa_managementv2/web/index.php";
                $message .= "</body></html>";
                $varListacorreo = (new \yii\db\Query())
                  ->select(['email'])
                  ->from(['tbl_qr_correos'])
                  ->All(); 
             
              foreach ($varListacorreo as $key => $value) {

                Yii::$app->mailer->compose()
                    ->setTo($value['email'])
                    ->setFrom(Yii::$app->params['email_satu_from'])
                    ->setSubject($varasunto)                    
                    ->attach($tmpFile)
                    ->setHtmlBody($message)
                    ->send();
              }

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

  $txtQuery5 =  new Query;
  $txtQuery5  ->select(['tbl_qr_casos.numero_caso'])
              ->from('tbl_qr_casos')       
              ->Where('tbl_qr_casos.id = :id_caso')
              ->addParams([':id_caso'=>$id_caso]);

  $command = $txtQuery5->createCommand();
  $datanumcaso = $command->queryScalar();
 
  $txtQuery6 =  new Query;
  $txtQuery6  ->select(['tbl_qr_casos.id_responsable'])
              ->from('tbl_qr_casos')       
              ->Where('tbl_qr_casos.id = :id_caso')
              ->addParams([':id_caso'=>$id_caso]);
             
  $command = $txtQuery6->createCommand();
  $dataresponsable = $command->queryScalar();
   
  $vardocumentojefe = (new \yii\db\Query())
      ->select(['usua_identificacion'])
      ->from(['tbl_usuarios'])
      ->where(['=','usua_id',$dataresponsable])
      ->Scalar();
  $datacorreoresponsable = (new \yii\db\Query())
      ->select(['email_corporativo'])
      ->from(['tbl_usuarios_jarvis_cliente'])
      ->where(['=','documento',$vardocumentojefe])
      ->Scalar();

  /*$txtQuery7 =  new Query;
  $txtQuery7  ->select(['tbl_usuarios_evalua.email_corporativo'])
              ->from('tbl_usuarios_evalua')       
              ->Where('tbl_usuarios_evalua.idusuarioevalua = :id_caso')
              ->addParams([':id_caso'=>$dataresponsable]);
             
  $command = $txtQuery7->createCommand();
  $datacorreoresponsable = $command->queryScalar();*/

  

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
          
          $varasunto = (new \yii\db\Query())
                  ->select(['asunto'])
                  ->from(['tbl_qr_respuesta_automatica'])
                  ->where(['=','id_estado',6])
                  ->Scalar();
          $varcuerpo1 = (new \yii\db\Query())
                  ->select(['comentario'])
                  ->from(['tbl_qr_respuesta_automatica'])
                  ->where(['=','id_estado',6])
                  ->Scalar();
          $varcuerpo2 = (new \yii\db\Query())
                  ->select(['comentario2'])
                  ->from(['tbl_qr_respuesta_automatica'])
                  ->where(['=','id_estado',6])
                  ->Scalar();
         
  $form = Yii::$app->request->post();
  if ($model6->load($form)) {
      $valrespuesta = $model6->ccdirector;  
      
      if($valrespuesta =="Aprobada"){
        $valestado = 5;
        $tmpFile = $dataanexo;
    //envio de correo al gerente con anexo            
                $message = "<html><body>";
                $message .= "<h3>CX-MANAGEMENT</h3>";   
                $message .= "<br><br>";
                $message .= $varcuerpo1;
                $message .= $datanumcaso;
                $message .= $varcuerpo2;             
                $message .= "<br><br>Que tengas un buen día";
                $message .= "<br><br><h3>Equipo Experiencia de Clientes - Konecta</h3>";
                $message .= "<br>https://qa.grupokonecta.local/qa_managementv2/web/index.php";
                $message .= "</body></html>";

                Yii::$app->mailer->compose()
                    ->setTo($datacorreoresponsable)
                    ->setFrom(Yii::$app->params['email_satu_from'])
                    ->setSubject($varasunto)                    
                    ->attach($tmpFile)
                    ->setHtmlBody($message)
                    ->send();

      } else{
//envio de correo al que envio respuesta con anexo
              $tmpFile = $dataanexo;
              $valestado = 4;
            $varasunto = (new \yii\db\Query())
              ->select(['asunto'])
              ->from(['tbl_qr_respuesta_automatica'])
              ->where(['=','id_estado',7])
              ->Scalar();
            $varcuerpo1 = (new \yii\db\Query())
              ->select(['comentario'])
              ->from(['tbl_qr_respuesta_automatica'])
              ->where(['=','id_estado',7])
              ->Scalar();
            $varcuerpo2 = (new \yii\db\Query())
              ->select(['comentario2'])
              ->from(['tbl_qr_respuesta_automatica'])
              ->where(['=','id_estado',7])
              ->Scalar();
            
                $message = "<html><body>";
                $message .= "<h3>CX-MANAGEMENT</h3>";
                $message .= "<br><br>";   
                $message .= $varcuerpo1;
                $message .= $datanumcaso;
                $message .= $varcuerpo2;             
                $message .= "<br><br>Que tengas un buen día";
                $message .= "<br><br><h3>Equipo CX - Konecta</h3>";
                $message .= "<br>https://qa.grupokonecta.local/qa_managementv2/web/index.php";
                $message .= "</body></html>";
                $varListacorreo = (new \yii\db\Query())
                ->select(['email'])
                ->from(['tbl_qr_correos'])
                ->All(); 
           
            foreach ($varListacorreo as $key => $value) {
                Yii::$app->mailer->compose()
                    ->setTo($value['email'])
                    ->setFrom(Yii::$app->params['email_satu_from'])
                    ->setSubject("$varasunto")                    
                    ->attach($tmpFile)
                    ->setHtmlBody($message)
                    ->send();
            }
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

  $txtQuery4 =  new Query;
  $txtQuery4  ->select(['tbl_qr_casos.archivo2'])
              ->from('tbl_qr_casos')       
              ->Where('tbl_qr_casos.id = :id_caso')
              ->addParams([':id_caso'=>$id_caso]); 
  $command = $txtQuery4->createCommand();
  $dataanexo = $command->queryScalar();
 
  
  $txtQuery5 =  new Query;
  $txtQuery5  ->select(['tbl_qr_casos.numero_caso'])
              ->from('tbl_qr_casos')       
              ->Where('tbl_qr_casos.id = :id_caso')
              ->addParams([':id_caso'=>$id_caso]); 
  $command = $txtQuery5->createCommand();
  $datanumcaso = $command->queryScalar();

  $txtQuery6 =  new Query;
  $txtQuery6  ->select(['tbl_qr_casos.correo'])
              ->from('tbl_qr_casos')       
              ->Where('tbl_qr_casos.id = :id_caso')
              ->addParams([':id_caso'=>$id_caso]); 
  $command = $txtQuery6->createCommand();
  $datacorreosolicitud = $command->queryScalar();
   
        $varasunto = (new \yii\db\Query())
                ->select(['asunto'])
                ->from(['tbl_qr_respuesta_automatica'])
                ->where(['=','id_estado',5])
                ->Scalar();
        $varcuerpo1 = (new \yii\db\Query())
                ->select(['comentario'])
                ->from(['tbl_qr_respuesta_automatica'])
                ->where(['=','id_estado',5])
                ->Scalar();
        $varcuerpo2 = (new \yii\db\Query())
                ->select(['comentario2'])
                ->from(['tbl_qr_respuesta_automatica'])
                ->where(['=','id_estado',5])
                ->Scalar();
        

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
      
      if($valrespuesta =="Aprobada"){
        $valestado = 2;
//envio de correo al solictante con anexo
      $tmpFile = $dataanexo;
                $valestado = 2;
                $message = "<html><body>";
                $message .= "<h3>CX-MANAGEMENT</h3>";   
                $message .= "Buen día, <br><br>";
                $message .= $varcuerpo1;
                $message .= $datanumcaso;
                $message .= "<br><br>";
                $message .= $varcuerpo2;             
                $message .= "<br><br>";
                $message .= "<br><br><h3>Equipo Experiencia de Clientes - Konecta</h3>";
                $message .= "<br>https://qa.grupokonecta.local/qa_managementv2/web/index.php";
                $message .= "</body></html>";

                Yii::$app->mailer->compose()
                    ->setTo($datacorreosolicitud)
                    ->setFrom(Yii::$app->params['email_satu_from'])
                    ->setSubject($varasunto)                    
                    ->attach($tmpFile)
                    ->setHtmlBody($message)
                    ->send();
      } else{
//envio de correo al que envio respuesta con anexo
     // $valestado = 8;

      $varasunto = (new \yii\db\Query())
          ->select(['asunto'])
          ->from(['tbl_qr_respuesta_automatica'])
          ->where(['=','id_estado',10])
          ->Scalar();
      $varcuerpo1 = (new \yii\db\Query())
          ->select(['comentario'])
          ->from(['tbl_qr_respuesta_automatica'])
          ->where(['=','id_estado',10])
          ->Scalar();
      $varcuerpo2 = (new \yii\db\Query())
          ->select(['comentario2'])
          ->from(['tbl_qr_respuesta_automatica'])
          ->where(['=','id_estado',10])
          ->Scalar();

      $tmpFile = $dataanexo;
                
                $message = "<html><body>";
                $message .= "<h3>CX-MANAGEMENT</h3>";   
                $message .= "Buen día, <br>";
                $message .= $varcuerpo1;
                $message .= $datanumcaso;
                $message .= "<br><br>";
                $message .= $varcuerpo2;             
                $message .= "<br><br>";
                $message .= "<br><br><h3>Equipo Experiencia de Clientes - Konecta</h3>";
                $message .= "<br>https://qa.grupokonecta.local/qa_managementv2/web/index.php";
                $message .= "</body></html>";
                $varListacorreo = (new \yii\db\Query())
                ->select(['email'])
                ->from(['tbl_qr_correos'])
                ->All(); 
           
            foreach ($varListacorreo as $key => $value) {
                Yii::$app->mailer->compose()
                    ->setTo($value['email'])
                    ->setFrom(Yii::$app->params['email_satu_from'])
                    ->setSubject("Actualización de tu caso QyR - CX-MANAGEMENT")                    
                    ->attach($tmpFile)
                    ->setHtmlBody($message)
                    ->send();
            }
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
public function actionPruebacorreo(){ 
  $datanumcaso = "C-4";
  $tmpFile = 'images/uploads/qyr_1679417953_Carta respuesta Q&R procesada.pdf';   
  $message = "<html><body>";
  $message .= "<h3>CX-MANAGEMENT</h3>";   
  $message .= "Hola, Te enviamos la respuesta de tu caso No.  <?php echo  $datanumcaso; ?>";
  $message .= "<br> Gracias por tu espera, tenemos respuesta a tu caso. Esperamos tu revisión y aceptación de la misma.";             
  $message .= "</body></html>";

  Yii::$app->mailer->compose()
      ->setTo('diego.montoya@grupokonecta.com')
      ->setFrom(Yii::$app->params['email_satu_from'])
      ->setSubject("Actualización de tu caso QyR - CX-MANAGEMENT")                    
      ->attach($tmpFile)
      ->setHtmlBody($message)
      ->send();

 return $this->redirect('index');

}

  }

?>
