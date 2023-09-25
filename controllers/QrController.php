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
use app\models\Cumplido;
use app\models\Tipologiasqyr;

  class QrController extends \yii\web\Controller {

    public function behaviors(){
      return[
        'access' => [
            'class' => AccessControl::classname(),
            'only' => ['index','viewqyr', 'listartipologia','verqyr','cargadatodoc', 'crearqyrn'],
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
          
    $modelcumplido = new Cumplido();
    $varAlerta = 0;

    $sessiones = Yii::$app->user->identity->id;
    
    if($sessiones == "3205" || $sessiones == "2953" || $sessiones == "57" || $sessiones == "1475" || $sessiones == "69" || $sessiones == "1699" || $sessiones == "5658" || $sessiones == "3468" || $sessiones == "7952"){ 
      $model = (new \yii\db\Query())
                ->select(['tbl_qr_casos.fecha_respuesta','tbl_qr_casos.archivo','tbl_qr_casos.id as idcaso',
                'tbl_qr_casos.numero_caso',
                'tbl_qr_tipos_de_solicitud.tipo_de_dato','tbl_qr_casos.comentario',
                'tbl_qr_casos.cliente','tbl_usuarios_evalua.clientearea','tbl_qr_casos.nombre',
                'tbl_qr_casos.documento','tbl_qr_casos.correo','tbl_qr_estados_casos.estado',
                'tbl_qr_estados_casos.id as idestado','tbl_qr_casos.fecha_creacion',
                  'tbl_qr_areas.nombre area','tbl_qr_tipologias.tipologia','tbl_qr_casos.id_estado','tbl_qr_estados.nombre estado1'])
                ->from(['tbl_qr_casos'])
                ->join('LEFT OUTER JOIN', 'tbl_qr_tipos_de_solicitud',
                                'tbl_qr_casos.id_solicitud = tbl_qr_tipos_de_solicitud.id') 
                ->join('LEFT OUTER JOIN', 'tbl_qr_estados_casos',
                                'tbl_qr_casos.id_estado_caso = tbl_qr_estados_casos.id')
                ->join('LEFT JOIN', 'tbl_qr_areas', 'tbl_qr_casos.id_area = tbl_qr_areas.id' )
                ->join('LEFT JOIN', 'tbl_qr_tipologias', 'tbl_qr_casos.id_tipologia = tbl_qr_tipologias.id')
                ->join('LEFT JOIN', 'tbl_qr_estados', 'tbl_qr_casos.id_estado = tbl_qr_estados.id_estado')
                ->join('LEFT JOIN', 'tbl_usuarios_evalua', 'tbl_qr_casos.cliente = tbl_usuarios_evalua.idusuarioevalua')  
                ->where(['=','tbl_qr_casos.estatus',0])
                ->orderBy(['idcaso' => SORT_DESC])
                ->all();
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
            ->select(['tbl_qr_casos.archivo','tbl_qr_casos.id as idcaso','tbl_qr_casos.numero_caso','tbl_qr_tipos_de_solicitud.tipo_de_dato','tbl_qr_casos.comentario','tbl_qr_casos.cliente','tbl_usuarios_evalua.clientearea','tbl_qr_casos.nombre','tbl_qr_casos.documento','tbl_qr_casos.correo','tbl_qr_estados_casos.estado','tbl_qr_estados_casos.id as idestado','tbl_qr_casos.fecha_creacion', 'tbl_qr_areas.nombre area','tbl_qr_tipologias.tipologia','tbl_qr_casos.id_estado','tbl_qr_estados.nombre estado1'])
            ->from(['tbl_qr_casos'])
            ->join('LEFT OUTER JOIN', 'tbl_qr_tipos_de_solicitud',
                            'tbl_qr_casos.id_solicitud = tbl_qr_tipos_de_solicitud.id') 
            ->join('LEFT OUTER JOIN', 'tbl_qr_estados_casos',
                            'tbl_qr_casos.id_estado_caso = tbl_qr_estados_casos.id')
            ->join('LEFT JOIN', 'tbl_qr_areas', 'tbl_qr_casos.id_area = tbl_qr_areas.id' )
            ->join('LEFT JOIN', 'tbl_qr_tipologias', 'tbl_qr_casos.id_tipologia = tbl_qr_tipologias.id')
            ->join('LEFT JOIN', 'tbl_qr_estados', 'tbl_qr_casos.id_estado = tbl_qr_estados.id_estado')
            ->join('LEFT JOIN', 'tbl_usuarios_evalua', 'tbl_qr_casos.cliente = tbl_usuarios_evalua.idusuarioevalua')
            ->where(['in','tbl_qr_casos.id_responsable',$sessiones])   
            ->andwhere(['=','tbl_qr_casos.estatus',0])
            ->orderBy(['idcaso' => SORT_DESC])
            ->All();
    }

    $dataProviderInfo = (new \yii\db\Query())
              ->select(['*'])
              ->from(['tbl_qr_casos'])
              ->All();

    $varCantSolicitud = (new \yii\db\Query())
    ->select(['COUNT(e.id) as Cantidadd','e.tipo_de_dato as Nombree'])
    ->from(['tbl_qr_tipos_de_solicitud e'])
    ->join('INNER JOIN','tbl_qr_casos c','e.id = c.id_solicitud')
    ->groupBy(['e.id'])
    ->All();
 

    $modelcaso = new Casosqyr(); 
    $modelo = new UploadForm2();
    $ruta = null;

    $varIdSesion = Yii::$app->user->identity->id;
    $varNombreCompleto = (new \yii\db\Query())
                            ->select(['tbl_usuarios.usua_nombre'])                            
                            ->from(['tbl_usuarios'])
                            ->where(['=','tbl_usuarios.usua_id',$varIdSesion])
                            ->Scalar();

    $varDocumentoCompleto = (new \yii\db\Query())
                            ->select(['tbl_usuarios.usua_identificacion'])                            
                            ->from(['tbl_usuarios'])
                            ->where(['=','tbl_usuarios.usua_id',$varIdSesion])
                            ->Scalar();
    
    $varParamsDoc = [":varDocumento"=>$varDocumentoCompleto];
    $varCorreoCompleto = Yii::$app->dbjarvis->createCommand('
    SELECT dp_actualizacion_datos.email_personal 
      FROM  dp_actualizacion_datos
        WHERE 
          dp_actualizacion_datos.documento = :varDocumento ')->bindValues($varParamsDoc)->queryScalar();
    
    $form = Yii::$app->request->post();  

    if($modelo->load($form)){

      $modelo->file = UploadedFile::getInstance($modelo, 'file');
      if ($modelo->file && $modelo->validate()) {
        foreach ($modelo->file as $file) {
          $ruta = 'images/documentos/'."qyr_".time()."_".$modelo->file->baseName. ".".$modelo->file->extension;
          $modelo->file->saveAs($ruta); 
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
                'documento' => $varDocumentoCompleto,
                'nombre' => $varNombreCompleto,
                'correo' => $varCorreoCompleto,
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
      

      $varHtml = 
      "
      <html lang='en'>

        <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Document</title>
        </head>

        <body style='font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 12px;'>
          <br><br><br><br>
          <div style='width: 450px; position: relative;'>
            <img src='https://i.ibb.co/8xbhr7W/k.png' alt='' width='80' style='position: absolute; top:-35px;right: -20px; border: 2px solid #002855 ;'>
            <div style='  box-shadow: -1px 0px 5px 0px rgba(0,0,0,0.75); border-radius: 10px; padding: 5px 50px; display: flex;'>

              <div style='width: 100%;'>
                      
                <h2 style='color: #002855; margin-bottom: 5px; font-size: 35px;'>
                  ¡Hola Equipo! 
                </h2>       
                  
                <div style='width: 50px; height: 5px; background-color: #FFC72C; margin-bottom: 15px;'></div>

                <br><br>
                <p style='text-align: justify;margin: 0; color: #040B25; font-weight: 500;'>Te notificamos que han dado respuesta a tu caso de PQRSF:</p>
                <br>
                <p style='text-align: justify;margin: 0; color: #040B25; font-weight: 500;'>Número:".$caso."</p>
                <br>
                <p style='color:#040B25;text-align: justify'>".$varcuerpo2."</p>
                <br><br>
                <p style='text-align: justify;margin: 0; color: #040B25; font-weight: 500;'>¡Que tengas un excelente día!</p>
                <br><br><br>

                <br>
                <div style='text-align: center; margin-bottom: 10px;'>
                    <a style='border:1px solid #FFC72C; background-color: #FFC72C; color:white; padding: 3px 10px; border-radius: 40px; font-weight: bold; text-decoration: none;' href='https://qa.grupokonecta.local/qa_managementv2/web/index.php'>Ingresar a CXM </a>
                </div>
                  
              </div>

              <div class='div'>
                  <img src='' alt=''>
              </div>

            </div>
          </div>
        </body>
      </html>  ";
         

          Yii::$app->mailer->compose()
              ->setTo($modelcaso->correo)
              ->setFrom(Yii::$app->params['email_satu_from'])
              ->setSubject($varasunto)
              ->setHtmlBody($varHtml)
              ->send();

          // // correo para grupo CX   
          



            $varHtml = 
            "
            <html lang='en'>
  
              <head>
              <meta charset='UTF-8'>
              <meta name='viewport' content='width=device-width, initial-scale=1.0'>
              <title>Document</title>
              </head>
  
              <body style='font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 12px;'>
                <br><br><br><br>
                <div style='width: 450px; position: relative;'>
                  <img src='https://i.ibb.co/8xbhr7W/k.png' alt='' width='80' style='position: absolute; top:-35px;right: -20px; border: 2px solid #002855 ;'>
                  <div style='  box-shadow: -1px 0px 5px 0px rgba(0,0,0,0.75); border-radius: 10px; padding: 5px 50px; display: flex;'>
  
                    <div style='width: 100%;'>
                            
                      <h2 style='color: #002855; margin-bottom: 5px; font-size: 35px;'>
                        ¡Hola Equipo! 
                      </h2>       
                        
                      <div style='width: 50px; height: 5px; background-color: #FFC72C; margin-bottom: 15px;'></div>
  
                      <br><br>
                      <p style='text-align: justify;margin: 0; color: #040B25; font-weight: 500;'>Te notificamos que han dado respuesta a tu caso de PQRSF:</p>
                      <br>
                      <p style='text-align: justify;margin: 0; color: #040B25; font-weight: 500;'>Número:".$caso."</p>
                      <br>
                      <p style='color:#040B25;text-align: justify'>".$varcuerpo2."</p>
                      <br><br>
                      <p style='text-align: justify;margin: 0; color: #040B25; font-weight: 500;'>¡Que tengas un excelente día!</p>
                      <br><br><br>
  
                      <br>
                      <div style='text-align: center; margin-bottom: 10px;'>
                          <a style='border:1px solid #FFC72C; background-color: #FFC72C; color:white; padding: 3px 10px; border-radius: 40px; font-weight: bold; text-decoration: none;' href='https://qa.grupokonecta.local/qa_managementv2/web/index.php'>Ingresar a CXM </a>
                      </div>
                        
                    </div>
  
                    <div class='div'>
                        <img src='' alt=''>
                    </div>
  
                  </div>
                </div>
              </body>
            </html>  ";
  

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
            ->setHtmlBody($varHtml)
            ->send();
          }else{
            Yii::$app->mailer->compose()
            ->setTo($value['email'])
            ->setFrom(Yii::$app->params['email_satu_from'])
            ->setSubject("Respuesta nuevo caso QyR - CX-Management")                     
            ->setHtmlBody($varHtml)
            ->send();
          }
        }

      $varAlerta = 1;    
          
      return $this->redirect(['index','varAlerta' => base64_encode($varAlerta)]);
    }
                        
              
      return $this->render('index',[
          'dataProviderInfo' => $dataProviderInfo,
          'model' => $model,
          'modelcumplido' => $modelcumplido,
          'modelcaso' => $modelcaso,
          'modelo' => $modelo,
          'varCantSolicitud'=>$varCantSolicitud,
      ]);
  }

  public function actionCrearqyrn(){
    $modelcaso = new Casosqyr(); 
    $model = new UploadForm2();
    $ruta = null;

    $varIdSesion = Yii::$app->user->identity->id;
    $varNombreCompleto = (new \yii\db\Query())
                            ->select(['tbl_usuarios.usua_nombre'])                            
                            ->from(['tbl_usuarios'])
                            ->where(['=','tbl_usuarios.usua_id',$varIdSesion])
                            ->Scalar();

    $varDocumentoCompleto = (new \yii\db\Query())
                            ->select(['tbl_usuarios.usua_identificacion'])                            
                            ->from(['tbl_usuarios'])
                            ->where(['=','tbl_usuarios.usua_id',$varIdSesion])
                            ->Scalar();
    
    $varParamsDoc = [":varDocumento"=>$varDocumentoCompleto];
    $varCorreoCompleto = Yii::$app->dbjarvis->createCommand('
    SELECT dp_actualizacion_datos.email_personal 
      FROM  dp_actualizacion_datos
	      WHERE 
		      dp_actualizacion_datos.documento = :varDocumento ')->bindValues($varParamsDoc)->queryScalar();
    
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
                  'documento' => $varDocumentoCompleto,
                  'nombre' => $varNombreCompleto,
                  'correo' => $varCorreoCompleto,
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

                // Yii::$app->mailer->compose()
                //     ->setTo($modelcaso->correo)
                //     ->setFrom(Yii::$app->params['email_satu_from'])
                //     ->setSubject($varasunto)
                //     ->setHtmlBody($message)
                //     ->send();

                // // correo para grupo CX   
                // $message = "<html><body>";
                // $message .= "<h3>CX-Management</h3>";
                // $message .= "<br>Buen día Equipo ";
                // $message .= "<br><br> Tenemos una nueva solicitud la cual fue recibida el día de hoy, con N° de caso: ";
                // $message .= $caso;
                // $message .= "<br><br> En espera de iniciar el proceso de asignación por parte del Equipo CX.";             
                // $message .= "<br><br>Se adjunta el detalle de la PQRS";
                // $message .= "<br><br>Feliz Día";
                // $message .= "</body></html>"; 
                // $varListacorreo = (new \yii\db\Query())
                //   ->select(['email'])
                //   ->from(['tbl_qr_correos'])
                //   ->All(); 
             
              // foreach ($varListacorreo as $key => $value) {
              //   if ($ruta){
              //     Yii::$app->mailer->compose()
              //     ->setTo($value['email'])
              //     ->setFrom(Yii::$app->params['email_satu_from'])
              //     ->setSubject("Respuesta nuevo caso QyR - CX-Management")   
              //     // revisar anexo    
              //     ->attach($ruta)
              //     ->setHtmlBody($message)
              //     ->send();
              //   }else{
              //     Yii::$app->mailer->compose()
              //     ->setTo($value['email'])
              //     ->setFrom(Yii::$app->params['email_satu_from'])
              //     ->setSubject("Respuesta nuevo caso QyR - CX-Management")                     
              //     ->setHtmlBody($message)
              //     ->send();
              //   }
              // }

      return $this->redirect('index');
    }
    
    return $this->renderAjax('crearqyrn',[
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
    $txtQuery2  ->select(['tbl_qr_casos.id','tbl_qr_casos.archivo','tbl_qr_casos.revision_gerente','tbl_qr_casos.revision_cx','tbl_qr_casos.fecha_revision_gerente','tbl_qr_casos.fecha_revisioncx',
                        'tbl_qr_casos.fecha_asignacion','tbl_qr_casos.fecha_respuesta','tbl_qr_casos.fecha_creacion',
                        'tbl_qr_casos.numero_caso','tbl_qr_clientes.clientes','tbl_qr_casos.nombre','tbl_qr_casos.documento',
                        'tbl_qr_casos.correo', 'tbl_qr_areas.nombre area','tbl_qr_tipologias.tipologia', 'tbl_qr_casos.comentario',
                        'tbl_usuarios.usua_nombre','tbl_qr_casos.tipo_respuesta','tbl_qr_tipos_de_solicitud.tipo_de_dato',
                        'tbl_qr_estados.nombre estado','tbl_qr_casos.comentario2','tbl_qr_casos.archivo2'])
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

      
    $dataProviderInfo = (new \yii\db\Query())
                ->select(['*'])
                ->from(['tbl_qr_casos'])
                ->where(['=','tbl_qr_casos.id',$id_caso])
                ->All();

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

  public function actionCargadatodoc(){ 
    $txtvarusua_id = Yii::$app->request->post('varusua_id');
    $txtRta = Yii::$app->db->createCommand("SELECT u.usua_identificacion documento, uj.email_corporativo email FROM tbl_usuarios u
    inner join tbl_usuarios_jarvis_cliente uj on
    u.usua_identificacion = uj.documento
    WHERE u.usua_id =:txtId")
    ->bindValue(':txtId',$txtvarusua_id)
    ->queryAll();
    die(json_encode($txtRta));

  }

  public function actionDeleteqr($idcaso){

    Yii::$app->db->createCommand()->update('tbl_qr_casos',[
      'estatus' => 1,                                               
    ],'id ='.$idcaso.'')->execute();

    return $this->redirect(['index']);

  }

  public function actionDevolver($idcaso){
    Yii::$app->db->createCommand()->update('tbl_qr_casos',[
      'id_area' => null,
      'id_tipologia' => null,
      'id_responsable' => null,
      'tipo_respuesta' => null,
      'id_estado' => 9,
                
    ],"id = '$idcaso'")->execute();
    
    // correo para grupo CX   
    $message = "<html><body>";
    $message .= "<h3>CX-Management</h3>";
    $message .= "<br>Buen día Equipo ";
    $message .= "<br><br> Tenemos una solicitud devuelta la cual fue recibida el día de hoy, con N° de caso: ";
    $message .= $idcaso;
    $message .= "<br><br> En espera de iniciar el proceso de Re-asignación por parte del Equipo CX.";             
    $message .= "<br><br>Se adjunta el detalle de la PQRS";
    $message .= "<br><br>Feliz Día";
    $message .= "</body></html>"; 
    
    $varListacorreo = (new \yii\db\Query())
                ->select(['email'])
                ->from(['tbl_qr_correos'])
                ->All(); 
            
    foreach ($varListacorreo as $key => $value) {
      
        Yii::$app->mailer->compose()
                ->setTo($value['email'])
                ->setFrom(Yii::$app->params['email_satu_from'])
                ->setSubject("Devolución caso QyR - CX-Management")                     
                ->setHtmlBody($message)
                ->send();
      
    }

    return $this->redirect(['index']);
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
  
  public function actionAsignarqyr($idcaso){

    $model = new UploadForm2();
    $id_caso = $idcaso;
    $model2 = new Casosqyr();
    $model18 = new Tipologiasqyr();   
    $model8 = new Areasqyr();   
    $model3 = new Usuarios();
    $model4 = new Tipopqrs();
    $model5 = new Estadosqyr();
    $model6 = new HojavidaDatadirector();
    $model7 = new HojavidaDatagerente();
    $model9 = new Casosqyr();
    $model12 = new HojavidaDatadirector();
    $ruta = null;
    $tmpFile = null;
    $model13 = new HojavidaDatadirector();
    $fechaHoraActual = date("Y-m-d H:i:s"); 


    $txtQuery2 =  new Query;
    $txtQuery2  ->select([
                  'tbl_qr_casos.id',
                  'tbl_qr_casos.archivo','tbl_qr_casos.revision_gerente',
                  'tbl_qr_casos.revision_cx','tbl_qr_casos.fecha_revision_gerente','tbl_qr_casos.fecha_revisioncx',
                  'tbl_qr_casos.fecha_asignacion','tbl_qr_casos.fecha_respuesta','tbl_qr_casos.fecha_creacion',
                  'tbl_qr_casos.numero_caso','tbl_qr_clientes.clientes','tbl_qr_casos.nombre','tbl_qr_casos.documento',
                  'tbl_qr_casos.correo', 'tbl_qr_areas.nombre area','tbl_qr_tipologias.tipologia', 'tbl_qr_casos.comentario',
                  'tbl_usuarios.usua_nombre','tbl_qr_casos.tipo_respuesta','tbl_qr_tipos_de_solicitud.tipo_de_dato',
                  'tbl_qr_estados.nombre estado','tbl_qr_casos.comentario2','tbl_qr_casos.archivo2'])
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
    $txtQuery3    ->select(['tbl_qr_casos.correo'])
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
    //ok

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
        ->select(['tbl_usuarios.usua_email'])
        ->from(['tbl_usuarios'])
        ->where(['=','tbl_usuarios.usua_identificacion',$vardocumentojefe])
        ->Scalar();

        $txtQuery7 =  new Query;
        $txtQuery7  ->select(['tbl_qr_casos.correo'])
                    ->from('tbl_qr_casos')       
                    ->Where('tbl_qr_casos.id = :id_caso')
                    ->addParams([':id_caso'=>$id_caso]); 
        $command = $txtQuery7->createCommand();
        $datacorreosolicitud = $command->queryScalar();
              
    $command = $txtQuery5->createCommand();
    $datanumcaso = $command->queryScalar();
  
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


    $dataProviderInfo = (new \yii\db\Query())
                  ->select(['*'])
                  ->from(['tbl_qr_casos'])
                  ->where(['=','tbl_qr_casos.id',$id_caso])
                  ->All();


    $valestado = (new \yii\db\Query())
                  ->select(['id_estado'])
                  ->from(['tbl_qr_casos'])
                  ->where(['=','id',$idcaso])
                  ->scalar();
   
    
    // //si estado es igual a abierto va a asignar
    if($valestado == 9){
      $varAlerta = 0;
      $form = Yii::$app->request->post();

      //me trae el id del area y id del usuario
      if ($model2->load($form)) {
          $valarea = $model2->id_area; 
          $valresponsable = $model2->id_responsable;     
      }

      if ($model8->load($form)) {
        $valtipologia = $model8->id; 
      }
      
      if ($model4->load($form)) {
        $valtipoqr = $model4->id;
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
          'fecha_asignacion' => $fechaHoraActual,
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

        $varParams = [':varDocumento'=>$vardocumentojefe];     
        
        $varcorreo = Yii::$app->dbjarvis->createCommand('
        SELECT 
          email 
        FROM dp_usuarios_red 
          WHERE 
            dp_usuarios_red.documento = :varDocumento ')->bindValues($varParams)->queryScalar();

        //envio de correo a responsable   

        $varHtml = 
        "
        <html lang='en'>

          <head>
          <meta charset='UTF-8'>
          <meta name='viewport' content='width=device-width, initial-scale=1.0'>
          <title>Document</title>
          </head>

          <body style='font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 12px;'>
            <br><br><br><br>
            <div style='width: 450px; position: relative;'>
              <img src='https://i.ibb.co/8xbhr7W/k.png' alt='' width='80' style='position: absolute; top:-35px;right: -20px; border: 2px solid #002855 ;'>
              <div style='  box-shadow: -1px 0px 5px 0px rgba(0,0,0,0.75); border-radius: 10px; padding: 5px 50px; display: flex;'>

                <div style='width: 100%;'>
                        
                  <h2 style='color: #002855; margin-bottom: 5px; font-size: 35px;'>
                    ¡Hola Equipo! 
                  </h2>       
                    
                  <div style='width: 50px; height: 5px; background-color: #FFC72C; margin-bottom: 15px;'></div>

                  <br><br>
                  <p style='text-align: justify;margin: 0; color: #040B25; font-weight: 500;'>Te notificamos que han dado respuesta a tu caso de PQRSF:</p>
                  <br>
                  <p style='text-align: justify;margin: 0; color: #040B25; font-weight: 500;'>Número:".$datanumcaso."</p>
                  <br>
                  <p style='color:#040B25;text-align: justify'>".$varcuerpo2."</p>
                  <br><br>
                  <p style='text-align: justify;margin: 0; color: #040B25; font-weight: 500;'>¡Que tengas un excelente día!</p>
                  <br><br><br>

                  <br>
                  <div style='text-align: center; margin-bottom: 10px;'>
                      <a style='border:1px solid #FFC72C; background-color: #FFC72C; color:white; padding: 3px 10px; border-radius: 40px; font-weight: bold; text-decoration: none;' href='https://qa.grupokonecta.local/qa_managementv2/web/index.php'>Ingresar a CXM </a>
                  </div>
                    
                </div>

                <div class='div'>
                    <img src='' alt=''>
                </div>

              </div>
            </div>
          </body>
        </html>  ";


        Yii::$app->mailer->compose()
        ->setTo($varcorreo)
        ->setFrom(Yii::$app->params['email_satu_from'])
        ->setSubject($varasunto)
        ->setHtmlBody($varHtml)
        ->send();

          $varAlerta = 1;    
              
          return $this->redirect(['index','varAlerta' => base64_encode($varAlerta)]);
       
      }
      
    }
    //ok

    //si estado es igual a 4 es en proceso para responder 
    if ($valestado == 4) {
      $varAlerta = 0;

      
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

      if ($model8->load($form)) {
        $valtipologia = $model8->id;
      }
      if ($model2->load($form)) {
        $valarea = $model2->id_area;
        $valcomentario = $model2->nombre;      
        $valresponsable = $model2->id_solicitud;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 
        $valestado = 8;

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
        
        Yii::$app->db->createCommand()->update('tbl_qr_casos',[
          'id_estado' => $valestado,
          'archivo2' => $ruta,
          'fecha_respuesta' => $fechaHoraActual,
                    
        ],"id = '$id_caso'")->execute();
          
          //envio de correo  equipo cx 
          $tmpFile = $ruta;
                      
         

          $varHtml = 
          "
          <html lang='en'>

            <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Document</title>
            </head>

            <body style='font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 12px;'>
              <br><br><br><br>
              <div style='width: 450px; position: relative;'>
                <img src='https://i.ibb.co/8xbhr7W/k.png' alt='' width='80' style='position: absolute; top:-35px;right: -20px; border: 2px solid #002855 ;'>
                <div style='  box-shadow: -1px 0px 5px 0px rgba(0,0,0,0.75); border-radius: 10px; padding: 5px 50px; display: flex;'>

                  <div style='width: 100%;'>
                          
                    <h2 style='color: #002855; margin-bottom: 5px; font-size: 35px;'>
                      ¡Hola Equipo! 
                    </h2>       
                      
                    <div style='width: 50px; height: 5px; background-color: #FFC72C; margin-bottom: 15px;'></div>

                    <br><br>
                    <p style='text-align: justify;margin: 0; color: #040B25; font-weight: 500;'>Te notificamos que han dado respuesta a tu caso de PQRSF:</p>
                    <br>
                    <p style='text-align: justify;margin: 0; color: #040B25; font-weight: 500;'>Número:".$datanumcaso."</p>
                    <br>
                    <p style='color:#040B25;text-align: justify'>".$varcuerpo2."</p>
                    <br><br>
                    <p style='text-align: justify;margin: 0; color: #040B25; font-weight: 500;'>¡Que tengas un excelente día!</p>
                    <br><br><br>

                    <br>
                    <div style='text-align: center; margin-bottom: 10px;'>
                        <a style='border:1px solid #FFC72C; background-color: #FFC72C; color:white; padding: 3px 10px; border-radius: 40px; font-weight: bold; text-decoration: none;' href='https://qa.grupokonecta.local/qa_managementv2/web/index.php'>Ingresar a CXM </a>
                    </div>
                      
                  </div>

                  <div class='div'>
                      <img src='' alt=''>
                  </div>

                </div>
              </div>
            </body>
          </html>  ";


                  $varListacorreo = (new \yii\db\Query())
                    ->select(['email'])
                    ->from(['tbl_qr_correos'])
                    ->All(); 
              
                foreach ($varListacorreo as $key => $value) {

                  if ($ruta == null) {
                    Yii::$app->mailer->compose()
                      ->setTo($value['email'])
                      ->setFrom(Yii::$app->params['email_satu_from'])
                      ->setSubject($varasunto)  
                      ->setHtmlBody($varHtml)
                      ->send();
                  }else{
                    Yii::$app->mailer->compose()
                      ->setTo($value['email'])
                      ->setFrom(Yii::$app->params['email_satu_from'])
                      ->setSubject($varasunto)                    
                      ->attach($tmpFile)
                      ->setHtmlBody($varHtml)
                      ->send();
                  }
                  
        }

        $varAlerta = 1;    
              
        return $this->redirect(['index','varAlerta' => base64_encode($varAlerta)]);

      }
    }
    //ok
   
    // //si estado es igual revision cx va a guardar la respuesta y envir correo 
    if($valestado == 8){
      $varAlerta = 0;
     
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
      if ($model13->load($form)) {
          $valrespuesta = $model13->ccdirector;  
          $valestado = 5;
         
          if($valrespuesta == "Aprobada"){

           
            $tmpFile = $dataanexo;
            //envio de correo al gerente con anexo            
            $varHtml = 
            "
            <html lang='en'>
  
              <head>
              <meta charset='UTF-8'>
              <meta name='viewport' content='width=device-width, initial-scale=1.0'>
              <title>Document</title>
              </head>
  
              <body style='font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 12px;'>
                <br><br><br><br>
                <div style='width: 450px; position: relative;'>
                  <img src='https://i.ibb.co/8xbhr7W/k.png' alt='' width='80' style='position: absolute; top:-35px;right: -20px; border: 2px solid #002855 ;'>
                  <div style='  box-shadow: -1px 0px 5px 0px rgba(0,0,0,0.75); border-radius: 10px; padding: 5px 50px; display: flex;'>
  
                    <div style='width: 100%;'>
                            
                      <h2 style='color: #002855; margin-bottom: 5px; font-size: 35px;'>
                        ¡Hola Equipo! 
                      </h2>       
                        
                      <div style='width: 50px; height: 5px; background-color: #FFC72C; margin-bottom: 15px;'></div>
  
                      <br><br>
                      <p style='text-align: justify;margin: 0; color: #040B25; font-weight: 500;'>Te notificamos que han dado respuesta a tu caso de PQRSF:</p>
                      <br>
                      <p style='text-align: justify;margin: 0; color: #040B25; font-weight: 500;'>Número:".$datanumcaso."</p>
                      <br>
                      <p style='color:#040B25;text-align: justify'>".$varcuerpo2."</p>
                      <br><br>
                      <p style='text-align: justify;margin: 0; color: #040B25; font-weight: 500;'>¡Que tengas un excelente día!</p>
                      <br><br><br>
  
                      <br>
                      <div style='text-align: center; margin-bottom: 10px;'>
                          <a style='border:1px solid #FFC72C; background-color: #FFC72C; color:white; padding: 3px 10px; border-radius: 40px; font-weight: bold; text-decoration: none;' href='https://qa.grupokonecta.local/qa_managementv2/web/index.php'>Ingresar a CXM </a>
                      </div>
                        
                    </div>
  
                    <div class='div'>
                        <img src='' alt=''>
                    </div>
  
                  </div>
                </div>
              </body>
            </html>  ";
  
    
            if ($tmpFile != "") {
              Yii::$app->mailer->compose()
                        ->setTo($datacorreoresponsable)
                        ->setFrom(Yii::$app->params['email_satu_from'])
                        ->setSubject($varasunto)                    
                        ->attach($tmpFile)
                        ->setHtmlBody($varHtml)
                        ->send();
            }else{
              Yii::$app->mailer->compose()
                        ->setTo($datacorreoresponsable)
                        ->setFrom(Yii::$app->params['email_satu_from'])
                        ->setSubject($varasunto) 
                        ->setHtmlBody($varHtml)
                        ->send();
            }
                    
    
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
                
                  //envio de correo al gerente con anexo            
                  $varHtml = 
                  "
                  <html lang='en'>
        
                    <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>Document</title>
                    </head>
        
                    <body style='font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 12px;'>
                      <br><br><br><br>
                      <div style='width: 450px; position: relative;'>
                        <img src='https://i.ibb.co/8xbhr7W/k.png' alt='' width='80' style='position: absolute; top:-35px;right: -20px; border: 2px solid #002855 ;'>
                        <div style='  box-shadow: -1px 0px 5px 0px rgba(0,0,0,0.75); border-radius: 10px; padding: 5px 50px; display: flex;'>
        
                          <div style='width: 100%;'>
                                  
                            <h2 style='color: #002855; margin-bottom: 5px; font-size: 35px;'>
                              ¡Hola Equipo! 
                            </h2>       
                              
                            <div style='width: 50px; height: 5px; background-color: #FFC72C; margin-bottom: 15px;'></div>
        
                            <br><br>
                            <p style='text-align: justify;margin: 0; color: #040B25; font-weight: 500;'>Te notificamos que han dado respuesta a tu caso de PQRSF:</p>
                            <br>
                            <p style='text-align: justify;margin: 0; color: #040B25; font-weight: 500;'>Número:".$datanumcaso."</p>
                            <br>
                            <p style='color:#040B25;text-align: justify'>".$varcuerpo2."</p>
                            <br><br>
                            <p style='text-align: justify;margin: 0; color: #040B25; font-weight: 500;'>¡Que tengas un excelente día!</p>
                            <br><br><br>
        
                            <br>
                            <div style='text-align: center; margin-bottom: 10px;'>
                                <a style='border:1px solid #FFC72C; background-color: #FFC72C; color:white; padding: 3px 10px; border-radius: 40px; font-weight: bold; text-decoration: none;' href='https://qa.grupokonecta.local/qa_managementv2/web/index.php'>Ingresar a CXM </a>
                            </div>
                              
                          </div>
        
                          <div class='div'>
                              <img src='' alt=''>
                          </div>
        
                        </div>
                      </div>
                    </body>
                  </html>  ";
        

                    $varListacorreo = (new \yii\db\Query())
                    ->select(['email'])
                    ->from(['tbl_qr_correos'])
                    ->All(); 
               
                foreach ($varListacorreo as $key => $value) {
    
                  if ($tmpFile != "") {
                    Yii::$app->mailer->compose()
                        ->setTo($value['email'])
                        ->setFrom(Yii::$app->params['email_satu_from'])
                        ->setSubject($varasunto)                    
                        ->attach($tmpFile)
                        ->setHtmlBody($varHtml)
                        ->send();
                  }else{
                    Yii::$app->mailer->compose()
                        ->setTo($value['email'])
                        ->setFrom(Yii::$app->params['email_satu_from'])
                        ->setSubject($varasunto) 
                        ->setHtmlBody($varHtml)
                        ->send();
                  }
                    
                }
        }
    
        Yii::$app->db->createCommand()->update('tbl_qr_casos',[     
          'id_estado' => $valestado,    
          'fecha_revisioncx' => $fechaHoraActual,  
          'revision_cx' => Yii::$app->user->identity->id,
        ],"id = '$id_caso'")->execute(); 

        $varAlerta = 1;    
              
        return $this->redirect(['index','varAlerta' => base64_encode($varAlerta)]);
    
      }
    } 
    // //ok
    
    // //si estado es igual revision comercial va a guardar la respuesta y envir correo 
    if($valestado = 5){
      $varAlerta = 0;

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

      $form = Yii::$app->request->post();
      if ($model12->load($form)) {
          $valrespuesta = $model12->ccdirector;  
          
        if($valrespuesta =="Aprobada"){
          $valestado = 2;
          //envio de correo al solictante con anexo
            $tmpFile = $dataanexo;

            $varHtml = 
            "
            <html lang='en'>
  
              <head>
              <meta charset='UTF-8'>
              <meta name='viewport' content='width=device-width, initial-scale=1.0'>
              <title>Document</title>
              </head>
  
              <body style='font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 12px;'>
                <br><br><br><br>
                <div style='width: 450px; position: relative;'>
                  <img src='https://i.ibb.co/8xbhr7W/k.png' alt='' width='80' style='position: absolute; top:-35px;right: -20px; border: 2px solid #002855 ;'>
                  <div style='  box-shadow: -1px 0px 5px 0px rgba(0,0,0,0.75); border-radius: 10px; padding: 5px 50px; display: flex;'>
  
                    <div style='width: 100%;'>
                            
                      <h2 style='color: #002855; margin-bottom: 5px; font-size: 35px;'>
                        ¡Hola Equipo! 
                      </h2>       
                        
                      <div style='width: 50px; height: 5px; background-color: #FFC72C; margin-bottom: 15px;'></div>
  
                      <br><br>
                      <p style='text-align: justify;margin: 0; color: #040B25; font-weight: 500;'>Te notificamos que han dado respuesta a tu caso de PQRSF:</p>
                      <br>
                      <p style='text-align: justify;margin: 0; color: #040B25; font-weight: 500;'>Número:".$datanumcaso."</p>
                      <br>
                      <p style='color:#040B25;text-align: justify'>".$varcuerpo2."</p>
                      <br><br>
                      <p style='text-align: justify;margin: 0; color: #040B25; font-weight: 500;'>¡Que tengas un excelente día!</p>
                      <br><br><br>
  
                      <br>
                      <div style='text-align: center; margin-bottom: 10px;'>
                          <a style='border:1px solid #FFC72C; background-color: #FFC72C; color:white; padding: 3px 10px; border-radius: 40px; font-weight: bold; text-decoration: none;' href='https://qa.grupokonecta.local/qa_managementv2/web/index.php'>Ingresar a CXM </a>
                      </div>
                        
                    </div>
  
                    <div class='div'>
                        <img src='' alt=''>
                    </div>
  
                  </div>
                </div>
              </body>
            </html>  ";
  
      
                      if ($tmpFile != "") {
                        Yii::$app->mailer->compose()
                          ->setTo($datacorreosolicitud)
                          ->setFrom(Yii::$app->params['email_satu_from'])
                          ->setSubject($varasunto)                    
                          ->attach($tmpFile)
                          ->setHtmlBody($varHtml)
                          ->send();
                      }else{
                        Yii::$app->mailer->compose()
                          ->setTo($datacorreosolicitud)
                          ->setFrom(Yii::$app->params['email_satu_from'])
                          ->setSubject($varasunto)     
                          ->setHtmlBody($varHtml)
                          ->send();
                      }
                    
          }else{
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
    
              //envia
          $tmpFile = $dataanexo;
                    
          $varHtml = 
          "
          <html lang='en'>

            <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Document</title>
            </head>

            <body style='font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 12px;'>
              <br><br><br><br>
              <div style='width: 450px; position: relative;'>
                <img src='https://i.ibb.co/8xbhr7W/k.png' alt='' width='80' style='position: absolute; top:-35px;right: -20px; border: 2px solid #002855 ;'>
                <div style='  box-shadow: -1px 0px 5px 0px rgba(0,0,0,0.75); border-radius: 10px; padding: 5px 50px; display: flex;'>

                  <div style='width: 100%;'>
                          
                    <h2 style='color: #002855; margin-bottom: 5px; font-size: 35px;'>
                      ¡Hola Equipo! 
                    </h2>       
                      
                    <div style='width: 50px; height: 5px; background-color: #FFC72C; margin-bottom: 15px;'></div>

                    <br><br>
                    <p style='text-align: justify;margin: 0; color: #040B25; font-weight: 500;'>Te notificamos que han dado respuesta a tu caso de PQRSF:</p>
                    <br>
                    <p style='text-align: justify;margin: 0; color: #040B25; font-weight: 500;'>Número:".$datanumcaso."</p>
                    <br>
                    <p style='color:#040B25;text-align: justify'>".$varcuerpo2."</p>
                    <br><br>
                    <p style='text-align: justify;margin: 0; color: #040B25; font-weight: 500;'>¡Que tengas un excelente día!</p>
                    <br><br><br>

                    <br>
                    <div style='text-align: center; margin-bottom: 10px;'>
                        <a style='border:1px solid #FFC72C; background-color: #FFC72C; color:white; padding: 3px 10px; border-radius: 40px; font-weight: bold; text-decoration: none;' href='https://qa.grupokonecta.local/qa_managementv2/web/index.php'>Ingresar a CXM </a>
                    </div>
                      
                  </div>

                  <div class='div'>
                      <img src='' alt=''>
                  </div>

                </div>
              </div>
            </body>
          </html>  ";



                    $varListacorreo = (new \yii\db\Query())
                    ->select(['email'])
                    ->from(['tbl_qr_correos'])
                    ->All(); 
               
                foreach ($varListacorreo as $key => $value) {
    
                  if ($tmpFile != "") {
                    Yii::$app->mailer->compose()
                        ->setTo($value['email'])
                        ->setFrom(Yii::$app->params['email_satu_from'])
                        ->setSubject("Actualización de tu caso QyR - CX-MANAGEMENT")                    
                        ->attach($tmpFile)
                        ->setHtmlBody($varHtml)
                        ->send();
                  }else{
                    Yii::$app->mailer->compose()
                        ->setTo($value['email'])
                        ->setFrom(Yii::$app->params['email_satu_from'])
                        ->setSubject("Actualización de tu caso QyR - CX-MANAGEMENT") 
                        ->setHtmlBody($varHtml)
                        ->send();
                  }
                    
                } 
        }
    
        Yii::$app->db->createCommand()->update('tbl_qr_casos',[     
          'id_estado' => $valestado,   
          'fecha_revision_gerente' => $fechaHoraActual,  
          'revision_gerente' => Yii::$app->user->identity->id,     
        ],"id = '$id_caso'")->execute();   

        $varAlerta = 1;    
              
        return $this->redirect(['index','varAlerta' => base64_encode($varAlerta)]);;
    
      }
    } 
    //ok
              
    return $this->render('asignarqyr',[
      'dataprovider' => $dataProvider, 
      'dataProviderInfo' => $dataProviderInfo,    
      'model2' => $model2,
      'model3' => $model3,
      'model4' => $model4,
      'model5' => $model5,
      'model6' => $model6,
      'model7' => $model7,
      'model8' => $model8,
      'model9' => $model9,
      'model' => $model,
      'model12' => $model12,
      'model13' => $model13,

    ]);
  }

  public function actionCorreo(){

    return $this->render('correo');
  }

  }
?>
