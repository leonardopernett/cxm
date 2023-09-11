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
use app\models\UploadForm2;
use app\models\FormUploadtigo;
use app\models\AlertasTipoalerta;
use app\models\AlertasTipoencuestas;
use app\models\Alertas;
use app\models\Controlcorreogrupal;
use app\models\Correogrupal;
use app\models\AlertasPermisoseliminar;
use app\models\AlertasEliminaralertas;
use app\models\AlertasEncuestasalertas;
use Exception;

  class AlertascxmController extends Controller {

    public function behaviors(){
        return[
            'access' => [
                'class' => AccessControl::classname(),
                'only' => ['index','registraalerta','parametrizaralertas','correogrupal','textcorreo','reportealerta','eliminaralerta','reportealertaeliminadas','restauraralerta','alertaencuesta','notificacionalertas','descargaralertas'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function() {
                            return Yii::$app->user->identity->isReportes() || Yii::$app->user->identity->isVerexterno() || Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isVerusuatlmast() || Yii::$app->user->identity->isAdminSistema();
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
        $varUsuario_Index = Yii::$app->user->identity->id;
        $varCCUsuario_Index = (new \yii\db\Query())
                            ->select(['tbl_usuarios.usua_identificacion'])
                            ->from(['tbl_usuarios'])
                            ->where(['=','tbl_usuarios.usua_id',$varUsuario_Index])
                            ->scalar(); 

        $varDocumentos_Index = [':varDocumentoName'=>$varCCUsuario_Index];

        $varNameJarvis_Index = Yii::$app->dbjarvis->createCommand('
        SELECT dp_datos_generales.primer_nombre FROM dp_datos_generales
            WHERE 
                dp_datos_generales.documento = :varDocumentoName
            GROUP BY dp_datos_generales.documento ')->bindValues($varDocumentos_Index)->queryScalar();

        $varEncuestasMesActual = (new \yii\db\Query())
                            ->select([
                                'tbl_alertas_tipoencuestas.tipoencuestas',
                                'COUNT(tbl_alertas_encuestasalertas.id_encuestasalertas) AS varCantidadEncuestas'
                            ])
                            ->from(['tbl_alertascx'])

                            ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                  'tbl_arbols.id = tbl_alertascx.pcrc')

                            ->join('LEFT OUTER JOIN', 'tbl_alertas_encuestasalertas',
                                  'tbl_alertas_encuestasalertas.id_alerta = tbl_alertascx.id')

                            ->join('LEFT OUTER JOIN', 'tbl_alertas_tipoencuestas',
                                  'tbl_alertas_tipoencuestas.id_tipoencuestas = tbl_alertas_encuestasalertas.id_tipoencuestas')

                            ->where(['>=','tbl_alertascx.fecha',date('Y-m-01').' 00:00:00'])
                            ->andwhere(['=','tbl_alertas_encuestasalertas.anulado',0])
                            ->groupby(['tbl_alertas_encuestasalertas.id_encuestasalertas'])
                            ->all(); 


        $varAlertasMesActual = (new \yii\db\Query())
                            ->select([
                                'tbl_alertascx.tipo_alerta',
                                'COUNT(tbl_alertas_tipoalerta.id_tipoalerta) AS varCantidadTipo'
                            ])
                            ->from(['tbl_alertascx'])

                            ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                  'tbl_arbols.id = tbl_alertascx.pcrc')

                            ->join('LEFT OUTER JOIN', 'tbl_alertas_tipoalerta',
                                  'tbl_alertas_tipoalerta.tipoalerta = tbl_alertascx.tipo_alerta')

                            ->where(['>=','tbl_alertascx.fecha',date('Y-m-01').' 00:00:00'])
                            ->groupby(['tbl_alertas_tipoalerta.id_tipoalerta'])
                            ->all(); 


        $varEliminadasMesActual = (new \yii\db\Query())
                            ->select([
                                'tbl_alertas_eliminaralertas.id_eliminaralertas'
                            ])
                            ->from(['tbl_alertas_eliminaralertas'])
                            ->where(['=','tbl_alertas_eliminaralertas.anulado',0])
                            ->count(); 
      
      
        return $this->render('index',[
            'varEncuestasMesActual' => $varEncuestasMesActual,
            'varAlertasMesActual' => $varAlertasMesActual,
            'varEliminadasMesActual' => $varEliminadasMesActual,
            'varNameJarvis_Index' => $varNameJarvis_Index,
        ]);
    }

    public function actionParametrizaralertas(){
        $modelTipo = new AlertasTipoalerta();
      
        $varDataListTipo = (new \yii\db\Query())
                        ->select(['*'])
                        ->from(['tbl_alertas_tipoalerta'])
                        ->where(['=','tbl_alertas_tipoalerta.anulado',0])
                        ->all();  

        $modelEncuestas = new AlertasTipoencuestas();

        $varDataListEncuesta = (new \yii\db\Query())
                        ->select(['*'])
                        ->from(['tbl_alertas_tipoencuestas'])
                        ->where(['=','tbl_alertas_tipoencuestas.anulado',0])
                        ->all();  

        $modelPermisos = new AlertasPermisoseliminar();

        $varDataListPermisos = (new \yii\db\Query())
                        ->select(['*'])
                        ->from(['tbl_alertas_permisoseliminar'])
                        ->where(['=','tbl_alertas_permisoseliminar.anulado',0])
                        ->all(); 

        return $this->render('parametrizaralertas',[
            'modelTipo' => $modelTipo,
            'modelEncuestas' => $modelEncuestas,
            'varDataListTipo' => $varDataListTipo,
            'varDataListEncuesta' => $varDataListEncuesta,
            'modelPermisos' => $modelPermisos,
            'varDataListPermisos' => $varDataListPermisos,
        ]);
    }

    public function actionIngresartipoalerta(){

        $txtvaridtipoalerta = Yii::$app->request->get("txtvaridtipoalerta");

        Yii::$app->db->createCommand()->insert('tbl_alertas_tipoalerta',[
                    'tipoalerta' => $txtvaridtipoalerta,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
        ])->execute(); 

        die(json_encode($txtvaridtipoalerta));

    }

    public function actionEliminartipo($id){
        Yii::$app->db->createCommand()->update('tbl_alertas_tipoalerta',[
                    'anulado' => 1,                                                
        ],'id_tipoalerta ='.$id.'')->execute();

        return $this->redirect(['parametrizaralertas']);
    }

    public function actionIngresartipoencuesta(){

        $txtvaridtipoencuesta = Yii::$app->request->get("txtvaridtipoencuesta");
        $txtvaridPeso = Yii::$app->request->get("txtvaridPeso");

        Yii::$app->db->createCommand()->insert('tbl_alertas_tipoencuestas',[
                    'tipoencuestas' => $txtvaridtipoencuesta,
                    'peso' => $txtvaridPeso,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
        ])->execute(); 

        die(json_encode($txtvaridtipoencuesta));

    }

    public function actionEliminarencuesta($id){
        Yii::$app->db->createCommand()->update('tbl_alertas_tipoencuestas',[
                    'anulado' => 1,                                                
        ],'id_tipoencuestas ='.$id.'')->execute();

        return $this->redirect(['parametrizaralertas']);
    }

    public function actionIngresarpermisos(){
        $txtvarIdUsuario = Yii::$app->request->get("txtvarIdUsuario");

        Yii::$app->db->createCommand()->insert('tbl_alertas_permisoseliminar',[
                    'id_usuario' => $txtvarIdUsuario,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
        ])->execute(); 

        die(json_encode($txtvarIdUsuario));
    }

    public function actionEliminarpermiso($id){
        Yii::$app->db->createCommand()->update('tbl_alertas_permisoseliminar',[
                    'anulado' => 1,                                                
        ],'id_permisoseliminar ='.$id.'')->execute();

        return $this->redirect(['parametrizaralertas']);
    }

    public function actionRegistraalerta($id_procesos){
        $model = new Alertas();
        $modelArchivo = new UploadForm2();
        $varCorreos = null;
        $ruta = null;
        $varEnvios = 0;

        $form = Yii::$app->request->post();
        if ($model->load($form)) {
            
            if ($modelArchivo->load($form)) {
                $modelArchivo->file = UploadedFile::getInstance($modelArchivo, 'file');
                if ($modelArchivo->file && $modelArchivo->validate()) {
                  foreach ($modelArchivo->file as $file) {
                    $user = Yii::$app->user->identity->username;
                    $ruta = date("YmdHis") . $user . str_replace(' ', '', $modelArchivo->file->baseName. ".".$modelArchivo->file->extension);
                    $rutaServidor = 'alertas/'.$ruta;

                    $modelArchivo->file->saveAs( $rutaServidor ); 
                  }
                } 
            }

            if ($model->remitentes == "") {
                $varListCorreos = (new \yii\db\Query())
                        ->select(['c2.nombre', 'c1.usua_id', 'u.usua_email'])
                        ->from(['tbl_usuarios u'])
                        ->join('LEFT OUTER JOIN', 'tbl_correogrupal c1',
                              'u.usua_id = c1.usua_id')
                        ->join('LEFT OUTER JOIN', 'tbl_correogrupal c2',
                              'c1.nombre = c2.nombre')
                        ->where(['=','c2.idcg',$model->archivo_adjunto])
                        ->all(); 

                $varArrayCorreos = array();
                foreach ($varListCorreos as $value) {
                    array_push($varArrayCorreos, $value['usua_email']);
                }
                $varCorreos = implode(", ", $varArrayCorreos);
            }else{
                $varCorreos = $model->remitentes;
            }
            
            $varFechas = date('Y-m-d H:i:s');


            Yii::$app->db->createCommand()->insert('tbl_alertascx',[
                    'fecha' => $varFechas,
                    'pcrc' => $model->pcrc,
                    'valorador' => Yii::$app->user->identity->id,  
                    'tipo_alerta' => $model->tipo_alerta,
                    'archivo_adjunto' => $ruta,
                    'remitentes' => $varCorreos,
                    'asunto' => $model->asunto,
                    'comentario' => $model->comentario,                                  
            ])->execute();

            $varIdAlertas = (new \yii\db\Query())
                        ->select(['tbl_alertascx.id'])
                        ->from(['tbl_alertascx'])
                        ->where(['=','tbl_alertascx.fecha',$varFechas])
                        ->andwhere(['=','tbl_alertascx.pcrc',$model->pcrc])
                        ->andwhere(['=','tbl_alertascx.valorador',Yii::$app->user->identity->id])
                        ->andwhere(['=','tbl_alertascx.tipo_alerta',$model->tipo_alerta])
                        ->andwhere(['=','tbl_alertascx.archivo_adjunto',$ruta])
                        ->andwhere(['=','tbl_alertascx.asunto',$model->asunto])
                        ->scalar(); 

            $this->enviarcorreos($varIdAlertas);

            $varEnvios = 1;
            
        }

        return $this->render('registraalerta',[
            'model' => $model,
            'modelArchivo' => $modelArchivo,
            'id_procesos' => $id_procesos,
            'varEnvios' => $varEnvios,
        ]);
    }

    public function enviarcorreos($varIdAlertas){
        $varFechas_correo = null;
        $varPcrc_correo = null;
        $varValorador_correo = null;
        $varTipoAlerta_correo = null;
        $varArchivo_correo = null;
        $varRemitentes_correo = null;
        $varAsuntos_correo = null;
        $varComentarios_correo = null;

        $varHtml = null;

        $varLisDataCorreos = (new \yii\db\Query())
                        ->select(['*'])
                        ->from(['tbl_alertascx'])
                        ->where(['=','tbl_alertascx.id',$varIdAlertas])
                        ->all();

        foreach ($varLisDataCorreos as $value) {
            $varFechas_correo = $value['fecha'];
            $varPcrc_correo = (new \yii\db\Query())
                                ->select(['tbl_arbols.name'])
                                ->from(['tbl_arbols'])
                                ->where(['=','tbl_arbols.id',$value['pcrc']])
                                ->scalar();
            $varValorador_correo = (new \yii\db\Query())
                                ->select(['tbl_usuarios.usua_nombre'])
                                ->from(['tbl_usuarios'])
                                ->where(['=','tbl_usuarios.usua_id',$value['valorador']])
                                ->scalar();
            $varTipoAlerta_correo = $value['tipo_alerta'];
            $varArchivo_correo = $value['archivo_adjunto'];
            $varRemitentes_correo = $value['remitentes'];
            $varAsuntos_correo = $value['asunto'];
            $varComentarios_correo = $value['comentario'];
        }

        $target_path = "alertas/" . $varArchivo_correo;

        $varHtml = 
        "
            <!DOCTYPE html>
            <html>
            <head>
                <title>Envio de Alertas</title>
            </head>
            <body class='text-center'>
                <h1><label style='font-family: sans-serif; color: #1d2d4f;'>Informe de Alertas CX-Management</label></h1>
                <hr>
                <h4><label style='font-family: sans-serif;'>Actualmente se tiene una alerta que fue realizada desde CX-Management. Para validar la alerta te recomendamos ingresar al módulo de reporte alertas de CXM y buscarlo para ver resultados.</a></label></h4>
                <hr>
                <h3><label style='font-family: sans-serif;'>Comentario Alerta...</a></h3><br>
                <h5><label style='font-family: sans-serif;'>".$varComentarios_correo."</a></h5>
                <hr>
                <h4><label style='font-family: sans-serif;'>¡Hola equipo! Te comentamos que nos encantaria saber tú opinión, por eso te invitamos a ingresar a CXM y responder la encuesta en el siguiente link <a href='https://qa.grupokonecta.local/qa_managementv2/web/index.php/alertascxm/alertaencuesta?id_alerta=".$varIdAlertas."'>Ingresar a la encuesta</a></label></h4>
                <hr>
                <h7><label style='font-family: sans-serif;'>© CX-Management 2023 - Desarrollado por Konecta</a></label></h>

            </body>
            </html>        
        ";

        $varListData_correos = explode(", ", $varRemitentes_correo);        

        if (count($varListData_correos) >= 2) {
            for ($i=0; $i < count($varListData_correos); $i++) { 
                $varCorreo_correo = $varListData_correos[$i];

                Yii::$app->mailer->compose()
                        ->setTo($varCorreo_correo)
                        ->setFrom(Yii::$app->params['email_satu_from'])
                        ->setSubject('Alertas CXM - '.$varAsuntos_correo)
                        ->attach($target_path)
                        ->setHtmlBody($varHtml)
                        ->send();
            }
        }else{
            Yii::$app->mailer->compose()
                        ->setTo($varListData_correos)
                        ->setFrom(Yii::$app->params['email_satu_from'])
                        ->setSubject('Alertas CXM - '.$varAsuntos_correo)
                        ->attach($target_path)
                        ->setHtmlBody($varHtml)
                        ->send();
        }  

    }

    public function actionCorreogrupal(){
        $model = new Correogrupal();

        $dataProvider = (new \yii\db\Query())
                        ->select(['tbl_correogrupal.idcg', 'tbl_correogrupal.nombre'])
                        ->from(['tbl_correogrupal'])
                        ->groupby(['tbl_correogrupal.nombre'])
                        ->all(); 

        $varUsers = null;

        $form = Yii::$app->request->post();
        if ($model->load($form)) {

            $varListData = explode(",", $model->usua_id);
            for ($i=0; $i < count($varListData); $i++) { 
                $varUsers = $varListData[$i];
             
                Yii::$app->db->createCommand()->insert('tbl_correogrupal',[
                    'nombre' => $model->nombre,
                    'nombre2' => $model->nombre,
                    'usua_id' => $varUsers,  
                    'fechacreacion' => date('Y-m-d'),                                
                ])->execute();   
            }

            return $this->redirect(['correogrupal']);
        }

        return $this->render('correogrupal', [
            'model'=>$model,
            'dataProvider'=>$dataProvider,
        ]);
    }

    public function actionTextcorreo(){

        return $this->render('textcorreo');
    }

    public function actionReportealerta(){
        $model = new Alertas();
        $varDataResultado = array();
        $arrayDataPcrc = array();
        $arrayDataUsers = array();
        $varDataTipos = array();
        $varDataEncuestas = array();
        $varDataProceso = array();
        $varDataTecnico = array();
        $varDataEncuestasTipos = array();


        $form = Yii::$app->request->post();
        if ($model->load($form)) {
            $varFecha_BD = explode(" ", $model->fecha);

            $varFechaInicio_BD = $varFecha_BD[0];
            $varFechaFin_BD = date('Y-m-d',strtotime($varFecha_BD[2]));
            
            if ($model->pcrc) {
                for ($i=0; $i < count($model->pcrc); $i++) { 
                    array_push($arrayDataPcrc, $model->pcrc);
                }
                $varDataPcrc =  explode(",", str_replace(array("#", "'", ";", " "), '', implode(", ",$arrayDataPcrc)));

                $varClientes = (new \yii\db\Query())
                            ->select([
                                'tbl_arbols.id'
                            ])
                            ->from(['tbl_arbols'])
                            ->where(['in','tbl_arbols.id',$varDataPcrc])
                            ->andwhere(['=','tbl_arbols.snhoja',0])
                            ->all();

                if (count($varClientes) > 0) {
                    $arrayListaPcrcClientes = array();
                    foreach ($varClientes as $value) {
                        array_push($arrayListaPcrcClientes, $value['id']);
                    }

                    $varListaPcrcCliente = (new \yii\db\Query())
                                            ->select([
                                                'tbl_arbols.id'
                                            ])
                                            ->from(['tbl_arbols'])
                                            ->where(['in','tbl_arbols.arbol_id',$arrayListaPcrcClientes])
                                            ->andwhere(['=','tbl_arbols.activo',0])     
                                            ->all();

                    foreach ($varListaPcrcCliente as $value) {
                        array_push($arrayDataPcrc, $value['id']);
                    }
                    
                }else{
                    $arrayDataPcrc = $varDataPcrc;
                }
            } 
            

            if ($model->valorador) {
                for ($i=0; $i < count($model->valorador); $i++) { 
                    array_push($arrayDataUsers, $model->valorador);
                }
            }
            

            $varDataResultado = (new \yii\db\Query())
                            ->select([
                                'tbl_alertascx.id',
                                'tbl_alertascx.fecha', 
                                'tbl_arbols.name',
                                'tbl_usuarios.usua_id',
                                'tbl_usuarios.usua_nombre', 
                                'tbl_alertascx.tipo_alerta'
                            ])
                            ->from(['tbl_alertascx'])

                            ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                  'tbl_arbols.id = tbl_alertascx.pcrc')

                            ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                  'tbl_usuarios.usua_id = tbl_alertascx.valorador')

                            ->where(['between','tbl_alertascx.fecha',$varFechaInicio_BD.' 00:00:00',$varFechaFin_BD.' 23:59:59'])
                            ->andfilterwhere(['in','tbl_alertascx.valorador',$arrayDataUsers])
                            ->andfilterwhere(['in','tbl_alertascx.pcrc',$arrayDataPcrc])
                            ->all(); 

            $varDataTipos = (new \yii\db\Query())
                            ->select([
                                'tbl_alertascx.tipo_alerta',
                                'COUNT(tbl_alertas_tipoalerta.id_tipoalerta) AS varCantidadTipo'
                            ])
                            ->from(['tbl_alertascx'])

                            ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                  'tbl_arbols.id = tbl_alertascx.pcrc')

                            ->join('LEFT OUTER JOIN', 'tbl_alertas_tipoalerta',
                                  'tbl_alertas_tipoalerta.tipoalerta = tbl_alertascx.tipo_alerta')

                            ->where(['between','tbl_alertascx.fecha',$varFechaInicio_BD.' 00:00:00',$varFechaFin_BD.' 23:59:59'])
                            ->andfilterwhere(['in','tbl_alertascx.valorador',$arrayDataUsers])
                            ->andfilterwhere(['in','tbl_alertascx.pcrc',$arrayDataPcrc])
                            ->groupby(['tbl_alertas_tipoalerta.id_tipoalerta'])
                            ->all(); 

            $varDataEncuestas = (new \yii\db\Query())
                            ->select([
                                'tbl_alertas_tipoencuestas.tipoencuestas',
                                'COUNT(tbl_alertas_encuestasalertas.id_encuestasalertas) AS varCantidadEncuestas'
                            ])
                            ->from(['tbl_alertascx'])

                            ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                  'tbl_arbols.id = tbl_alertascx.pcrc')

                            ->join('LEFT OUTER JOIN', 'tbl_alertas_encuestasalertas',
                                  'tbl_alertas_encuestasalertas.id_alerta = tbl_alertascx.id')

                            ->join('LEFT OUTER JOIN', 'tbl_alertas_tipoencuestas',
                                  'tbl_alertas_tipoencuestas.id_tipoencuestas = tbl_alertas_encuestasalertas.id_tipoencuestas')

                            ->where(['between','tbl_alertascx.fecha',$varFechaInicio_BD.' 00:00:00',$varFechaFin_BD.' 23:59:59'])
                            ->andfilterwhere(['in','tbl_alertascx.valorador',$arrayDataUsers])
                            ->andfilterwhere(['in','tbl_alertascx.pcrc',$arrayDataPcrc])
                            ->andwhere(['=','tbl_alertas_encuestasalertas.anulado',0])
                            ->groupby(['tbl_alertas_encuestasalertas.id_encuestasalertas'])
                            ->all(); 

            $varDataEncuestasTipos = (new \yii\db\Query())
                            ->select([
                                'tbl_alertas_tipoencuestas.tipoencuestas',
                                'COUNT(tbl_alertas_encuestasalertas.id_encuestasalertas) AS varCantidadEncuestas'
                            ])
                            ->from(['tbl_alertascx'])

                            ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                  'tbl_arbols.id = tbl_alertascx.pcrc')

                            ->join('LEFT OUTER JOIN', 'tbl_alertas_encuestasalertas',
                                  'tbl_alertas_encuestasalertas.id_alerta = tbl_alertascx.id')

                            ->join('LEFT OUTER JOIN', 'tbl_alertas_tipoencuestas',
                                  'tbl_alertas_tipoencuestas.id_tipoencuestas = tbl_alertas_encuestasalertas.id_tipoencuestas')

                            ->where(['between','tbl_alertascx.fecha',$varFechaInicio_BD.' 00:00:00',$varFechaFin_BD.' 23:59:59'])
                            ->andfilterwhere(['in','tbl_alertascx.valorador',$arrayDataUsers])
                            ->andfilterwhere(['in','tbl_alertascx.pcrc',$arrayDataPcrc])
                            ->andwhere(['=','tbl_alertas_encuestasalertas.anulado',0])
                            ->groupby(['tbl_alertas_tipoencuestas.tipoencuestas'])
                            ->all(); 


            $varDataProceso = (new \yii\db\Query())
                            ->select([
                                'a.id AS varIdPcrc', 'a.name AS varProgramaPcrc', 'aa.name AS varCliente', 
                                'COUNT(a.id) AS varConteoPcrc', 'COUNT(tbl_alertas_encuestasalertas.id_encuestasalertas) AS varConteoEncuestas'
                            ])
                            ->from(['tbl_alertascx'])

                            ->join('LEFT OUTER JOIN', 'tbl_arbols a',
                                  'a.id = tbl_alertascx.pcrc')

                            ->join('LEFT OUTER JOIN', 'tbl_arbols aa',
                                  'aa.id = a.arbol_id')

                            ->join('LEFT OUTER JOIN', 'tbl_alertas_encuestasalertas',
                                  'tbl_alertas_encuestasalertas.id_alerta = tbl_alertascx.id')

                            ->where(['between','tbl_alertascx.fecha',$varFechaInicio_BD.' 00:00:00',$varFechaFin_BD.' 23:59:59'])
                            ->andfilterwhere(['in','tbl_alertascx.valorador',$arrayDataUsers])
                            ->andfilterwhere(['in','tbl_alertascx.pcrc',$arrayDataPcrc])
                            ->groupby(['a.id'])
                            ->all();

            $varDataTecnico = (new \yii\db\Query())
                            ->select([
                                'a.id AS varIdPcrc_tecnico', 'a.name AS varProgramaPcrc_tecnico', 'aa.name AS varCliente_tecnico', 
                                'COUNT(a.id) AS varConteoPcrc_tecnico', 'COUNT(tbl_alertas_encuestasalertas.id_encuestasalertas) AS varConteoEncuestas_tecnico',
                                'tbl_usuarios.usua_nombre AS varValorador_tecnico'
                            ])
                            ->from(['tbl_alertascx'])

                            ->join('LEFT OUTER JOIN', 'tbl_arbols a',
                                  'a.id = tbl_alertascx.pcrc')

                            ->join('LEFT OUTER JOIN', 'tbl_arbols aa',
                                  'aa.id = a.arbol_id')

                            ->join('LEFT OUTER JOIN', 'tbl_alertas_encuestasalertas',
                                  'tbl_alertas_encuestasalertas.id_alerta = tbl_alertascx.id')

                            ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                  'tbl_usuarios.usua_id = tbl_alertascx.valorador')

                            ->where(['between','tbl_alertascx.fecha',$varFechaInicio_BD.' 00:00:00',$varFechaFin_BD.' 23:59:59'])
                            ->andfilterwhere(['in','tbl_alertascx.valorador',$arrayDataUsers])
                            ->andfilterwhere(['in','tbl_alertascx.pcrc',$arrayDataPcrc])
                            ->groupby(['tbl_usuarios.usua_id'])
                            ->all();

        }

        return $this->render('reportealerta',[
            'model' => $model,
            'varDataResultado' => $varDataResultado,
            'varDataTipos' => $varDataTipos,
            'varDataEncuestas' => $varDataEncuestas,
            'varDataProceso' => $varDataProceso,
            'varDataTecnico' => $varDataTecnico,
            'varDataEncuestasTipos' => $varDataEncuestasTipos,
        ]);
    }

    public function actionEliminaralerta($id){
        $model = new AlertasEliminaralertas();

        $varLisDataEliminar = (new \yii\db\Query())
                            ->select([
                                'tbl_alertascx.fecha', 
                                'tbl_arbols.name',
                                'tbl_usuarios.usua_nombre', 
                                'tbl_alertascx.tipo_alerta'
                            ])
                            ->from(['tbl_alertascx'])

                            ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                  'tbl_arbols.id = tbl_alertascx.pcrc')

                            ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                  'tbl_usuarios.usua_id = tbl_alertascx.valorador')

                            ->where(['=','tbl_alertascx.id',$id])
                            ->all(); 

        $varFecha_Eliminar = null;
        $varPcrc_Eliminar = null;
        $varValorador_Eliminar = null;
        $varTipoAlerta_Eliminar = null;
        foreach ($varLisDataEliminar as $value) {
            $varFecha_Eliminar = $value['fecha'];
            $varPcrc_Eliminar = $value['name'];
            $varValorador_Eliminar = $value['usua_nombre'];
            $varTipoAlerta_Eliminar = $value['tipo_alerta'];
        }

        $form = Yii::$app->request->post();
        if ($model->load($form)) {

            Yii::$app->db->createCommand()->insert('tbl_alertas_eliminaralertas',[
                    'id_alerta' => $id,
                    'comentarios' => $model->comentarios,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id                                 
            ])->execute();

            $varDataAlertacx = (new \yii\db\Query())
                            ->select(['*'])
                            ->from(['tbl_alertascx'])
                            ->where(['=','tbl_alertascx.id',$id])
                            ->all(); 

            foreach ($varDataAlertacx as $value) {
                Yii::$app->db->createCommand()->insert('tbl_alertas_copiaalertaseliminar',[
                    'id_alerta' => $value['id'],
                    'fecha' => $value['fecha'],
                    'pcrc' => $value['pcrc'],
                    'valorador' => $value['valorador'],
                    'tipo_alerta' => $value['tipo_alerta'],
                    'archivo_adjunto' => $value['archivo_adjunto'],
                    'remitentes' => $value['remitentes'],
                    'asunto' => $value['asunto'],
                    'comentario' => $value['comentario'],
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
                ])->execute();
            }

            Alertas::findOne($id)->delete();


            return $this->redirect(['reportealerta']);
        }

        return $this->render('eliminaralerta',[
            'model' => $model,
            'varFecha_Eliminar' => $varFecha_Eliminar,
            'varPcrc_Eliminar' => $varPcrc_Eliminar,
            'varValorador_Eliminar' => $varValorador_Eliminar,
            'varTipoAlerta_Eliminar' => $varTipoAlerta_Eliminar,
        ]);
    }

    public function actionReportealertaeliminadas(){
        $model = new AlertasEliminaralertas();
        $varDataListEliminados = null;

        $form = Yii::$app->request->post();
        if ($model->load($form)) {
            $varFechas_Eliminar = explode(" ", $model->fechacreacion);

            $varFechasInicio_Eliminar = $varFechas_Eliminar[0];
            $varFechasFin_Eliminar = date('Y-m-d',strtotime($varFechas_Eliminar[2]));

            $varDataListEliminados = (new \yii\db\Query())
                            ->select([
                                'tbl_alertas_eliminaralertas.id_eliminaralertas',
                                'tbl_alertas_copiaalertaseliminar.id_copiaalertaseliminar',
                                'tbl_alertas_eliminaralertas.fechacreacion AS varFechaEliminacion',
                                's.usua_nombre AS varQuienElimina',
                                'tbl_alertas_copiaalertaseliminar.id_alerta AS varIdAlertaOriginal',
                                'tbl_alertas_copiaalertaseliminar.fecha AS varFechaAlertaOriginal',
                                'tbl_arbols.name AS varPcrcOriginal',
                                'u.usua_nombre AS varValorador',
                                'tbl_alertas_copiaalertaseliminar.tipo_alerta AS vartipoAlertaOriginal'
                            ])
                            ->from(['tbl_alertas_eliminaralertas'])

                            ->join('LEFT OUTER JOIN', 'tbl_alertas_copiaalertaseliminar',
                                  'tbl_alertas_copiaalertaseliminar.id_alerta = tbl_alertas_eliminaralertas.id_alerta')

                            ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                  'tbl_arbols.id = tbl_alertas_copiaalertaseliminar.pcrc')

                            ->join('LEFT OUTER JOIN', 'tbl_usuarios u',
                                  'u.usua_id = tbl_alertas_copiaalertaseliminar.valorador')

                            ->join('LEFT OUTER JOIN', 'tbl_usuarios s',
                                  's.usua_id = tbl_alertas_eliminaralertas.usua_id')

                            ->where(['between','tbl_alertas_eliminaralertas.fechacreacion',$varFechasInicio_Eliminar,$varFechasFin_Eliminar])
                            ->andwhere(['in','tbl_alertas_eliminaralertas.anulado',0])
                            ->andwhere(['=','tbl_alertas_copiaalertaseliminar.anulado',0])
                            ->all(); 
        }

        return $this->render('reportealertaeliminadas',[
            'model' => $model,
            'varDataListEliminados' => $varDataListEliminados,
        ]);
    }

    public function actionRestauraralerta($ideliminaralertas,$idcopiaseliminar){

        $varDatalistResutaurar = (new \yii\db\Query())
                            ->select([
                                'tbl_alertas_copiaalertaseliminar.id_alerta',
                                'tbl_alertas_copiaalertaseliminar.fecha',
                                'tbl_alertas_copiaalertaseliminar.pcrc',
                                'tbl_alertas_copiaalertaseliminar.valorador',
                                'tbl_alertas_copiaalertaseliminar.tipo_alerta',
                                'tbl_alertas_copiaalertaseliminar.archivo_adjunto',
                                'tbl_alertas_copiaalertaseliminar.remitentes',
                                'tbl_alertas_copiaalertaseliminar.asunto',
                                'tbl_alertas_copiaalertaseliminar.comentario'
                            ])
                            ->from(['tbl_alertas_eliminaralertas'])

                            ->join('LEFT OUTER JOIN', 'tbl_alertas_copiaalertaseliminar',
                                  'tbl_alertas_copiaalertaseliminar.id_alerta = tbl_alertas_eliminaralertas.id_alerta')

                            ->where(['=','tbl_alertas_eliminaralertas.id_eliminaralertas',$ideliminaralertas])
                            ->andwhere(['=','tbl_alertas_eliminaralertas.anulado',0])
                            ->andwhere(['=','tbl_alertas_copiaalertaseliminar.anulado',0])
                            ->all(); 

        foreach ($varDatalistResutaurar as $value) {
            Yii::$app->db->createCommand()->insert('tbl_alertascx',[
                    'fecha' => $value['fecha'],
                    'pcrc' => $value['pcrc'],
                    'valorador' => $value['valorador'],
                    'tipo_alerta' => $value['tipo_alerta'],
                    'archivo_adjunto' => $value['archivo_adjunto'],
                    'remitentes' => $value['remitentes'],
                    'asunto' => $value['asunto'],
                    'comentario' => $value['comentario'],                               
            ])->execute();
        }

        Yii::$app->db->createCommand()->update('tbl_alertas_eliminaralertas',[
                    'anulado' => 1,                                                
        ],'id_eliminaralertas ='.$ideliminaralertas.'')->execute();

        Yii::$app->db->createCommand()->update('tbl_alertas_copiaalertaseliminar',[
                    'anulado' => 1,                                                
        ],'id_copiaalertaseliminar ='.$idcopiaseliminar.'')->execute();

        Yii::$app->db->createCommand()->update('tbl_alertas_encuestasalertas',[
                    'anulado' => 0,                                                
        ],'id_alerta ='.$ideliminaralertas.'')->execute();

        return $this->redirect(['index']);
    }

    public function actionAlertaencuesta($id_alerta){
        $model = new AlertasEncuestasalertas();
        $varMensajes_encuestas = 0;
        $varConteoUrl = null;
        $varIdUser = Yii::$app->user->identity->id;
        $varidentificacion = (new \yii\db\Query())
                            ->select(['tbl_usuarios.usua_identificacion'])
                            ->from(['tbl_usuarios'])
                            ->where(['=','tbl_usuarios.usua_id',$varIdUser])
                            ->scalar();
        $varDocumentos = [':varDocumentoName'=>$varidentificacion];

        $varNameJarvis = Yii::$app->dbjarvis->createCommand('
        SELECT dp_datos_generales.primer_nombre FROM dp_datos_generales
            WHERE 
                dp_datos_generales.documento = :varDocumentoName
            GROUP BY dp_datos_generales.documento ')->bindValues($varDocumentos)->queryScalar();

        $varConteoEncuestas = (new \yii\db\Query())
                            ->select(['*'])
                            ->from(['tbl_alertas_encuestasalertas'])
                            ->where(['=','tbl_alertas_encuestasalertas.id_alerta',$id_alerta])
                            ->andwhere(['=','tbl_alertas_encuestasalertas.usua_id',$varIdUser])
                            ->andwhere(['=','tbl_alertas_encuestasalertas.anulado',0])
                            ->count();

        $varListEncuestas = (new \yii\db\Query())
                            ->select(['*'])
                            ->from(['tbl_alertas_tipoencuestas'])
                            ->where(['=','tbl_alertas_tipoencuestas.anulado',0])
                            ->all(); 

        $varUrlArchivo = (new \yii\db\Query())
                            ->select(['tbl_alertascx.archivo_adjunto'])
                            ->from(['tbl_alertascx'])
                            ->where(['=','tbl_alertascx.id',$id_alerta])
                            ->scalar(); 

        $varDataListAlertaEncuesta = (new \yii\db\Query())
                            ->select([
                                'tbl_alertascx.id',
                                'tbl_alertascx.fecha', 
                                'tbl_arbols.name',
                                'tbl_usuarios.usua_id',
                                'tbl_usuarios.usua_nombre', 
                                'tbl_alertascx.tipo_alerta',
                                'tbl_alertascx.archivo_adjunto',
                                'tbl_alertascx.remitentes',
                                'tbl_alertascx.asunto',
                                'tbl_alertascx.comentario',
                            ])
                            ->from(['tbl_alertascx'])

                            ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                  'tbl_arbols.id = tbl_alertascx.pcrc')

                            ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                  'tbl_usuarios.usua_id = tbl_alertascx.valorador')

                            ->where(['=','tbl_alertascx.id',$id_alerta])
                            ->all(); 

        $varId_encuesta = null;
        $varFecha_encuesta = null;
        $varName_encuesta = null;
        $varUsuaNombre_encuesta = null;
        $varTipoAlerta_encuesta = null;
        $varArchivo_encuesta = null;
        $varRemitentes_encuesta = null;
        $varAsunto_encuesta = null;
        $varComentarios_encuesta = null;
        foreach ($varDataListAlertaEncuesta as $value) {
            $varId_encuesta = $value['id'];
            $varFecha_encuesta = $value['fecha'];
            $varName_encuesta = $value['name'];
            $varUsuaNombre_encuesta = $value['usua_nombre'];
            $varTipoAlerta_encuesta = $value['tipo_alerta'];
            $varArchivo_encuesta = $value['archivo_adjunto'];
            $varRemitentes_encuesta = $value['remitentes'];
            $varAsunto_encuesta = $value['asunto'];
            $varComentarios_encuesta = $value['comentario'];
        }

        $varConteoArchivo = strlen($varUrlArchivo);
        $varConteoUrl = substr($varUrlArchivo, -3);

        $form = Yii::$app->request->post();
        if ($model->load($form)) {

            if ($model->id_tipoencuestas != "") {
                Yii::$app->db->createCommand()->insert('tbl_alertas_encuestasalertas',[
                    'id_alerta' => $id_alerta,
                    'id_tipoencuestas' => $model->id_tipoencuestas,
                    'comentarios' => $model->comentarios,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                           
                ])->execute();

                $varMensajes_encuestas = 1;
            }
            
        }

        return $this->render('alertaencuesta',[
            'model' => $model,
            'varListEncuestas' => $varListEncuestas,
            'varUrlArchivo' => $varUrlArchivo,
            'varConteoEncuestas' => $varConteoEncuestas,
            'varNameJarvis' => $varNameJarvis,
            'varMensajes_encuestas' => $varMensajes_encuestas,
            'varConteoUrl' => $varConteoUrl,
            'varId_encuesta' => $varId_encuesta,
            'varFecha_encuesta' => $varFecha_encuesta,
            'varName_encuesta' => $varName_encuesta,
            'varUsuaNombre_encuesta' => $varUsuaNombre_encuesta,
            'varTipoAlerta_encuesta' => $varTipoAlerta_encuesta,
            'varArchivo_encuesta' => $varArchivo_encuesta,
            'varRemitentes_encuesta' => $varRemitentes_encuesta,
            'varAsunto_encuesta' => $varAsunto_encuesta,
            'varComentarios_encuesta' => $varComentarios_encuesta,
        ]);
    }

    public function actionVerimagenalerta($varArchivo){

        return $this->renderAjax('verimagenalerta',[
            'varArchivo' => $varArchivo,
        ]);
    }

    public function actionVeralerta($idalerta){

        $varDataListVerAlerta = (new \yii\db\Query())
                            ->select([
                                'tbl_alertascx.id',
                                'tbl_alertascx.fecha', 
                                'tbl_arbols.name',
                                'tbl_usuarios.usua_id',
                                'tbl_usuarios.usua_nombre', 
                                'tbl_alertascx.tipo_alerta',
                                'tbl_alertascx.archivo_adjunto',
                                'tbl_alertascx.remitentes',
                                'tbl_alertascx.asunto',
                                'tbl_alertascx.comentario',
                            ])
                            ->from(['tbl_alertascx'])

                            ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                  'tbl_arbols.id = tbl_alertascx.pcrc')

                            ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                  'tbl_usuarios.usua_id = tbl_alertascx.valorador')

                            ->where(['=','tbl_alertascx.id',$idalerta])
                            ->all(); 

        $varId_ver = null;
        $varFecha_ver = null;
        $varName_ver = null;
        $varUsuaNombre = null;
        $varTipoAlerta_ver = null;
        $varArchivo_ver = null;
        $varRemitentes_ver = null;
        $varAsunto_ver = null;
        $varComentarios_ver = null;
        foreach ($varDataListVerAlerta as $value) {
            $varId_ver = $value['id'];
            $varFecha_ver = $value['fecha'];
            $varName_ver = $value['name'];
            $varUsuaNombre = $value['usua_nombre'];
            $varTipoAlerta_ver = $value['tipo_alerta'];
            $varArchivo_ver = $value['archivo_adjunto'];
            $varRemitentes_ver = $value['remitentes'];
            $varAsunto_ver = $value['asunto'];
            $varComentarios_ver = $value['comentario'];
        }

        $varDataListEncuesta = (new \yii\db\Query())
                            ->select(['*'])
                            ->from(['tbl_alertas_encuestasalertas'])

                            ->where(['=','tbl_alertas_encuestasalertas.id_alerta',$idalerta])
                            ->andwhere(['=','tbl_alertas_encuestasalertas.anulado',0])
                            ->all(); 

        $varid_tipoencuestas_ver = null;
        $varcomentariosencuestas_ver = null;
        foreach ($varDataListEncuesta as $value) {
            $varid_tipoencuestas_ver = $value['id_tipoencuestas'];
            $varcomentariosencuestas_ver = $value['comentarios'];
        }

        return $this->renderAjax('veralerta',[
            'idalerta' => $idalerta,
            'varId_ver' => $varId_ver,
            'varFecha_ver' => $varFecha_ver,
            'varName_ver' => $varName_ver,
            'varUsuaNombre' => $varUsuaNombre,
            'varTipoAlerta_ver' => $varTipoAlerta_ver,
            'varArchivo_ver' => $varArchivo_ver,
            'varRemitentes_ver' => $varRemitentes_ver,
            'varAsunto_ver' => $varAsunto_ver,
            'varComentarios_ver' => $varComentarios_ver,
            'varid_tipoencuestas_ver' => $varid_tipoencuestas_ver,
            'varcomentariosencuestas_ver' => $varcomentariosencuestas_ver,
            'varDataListEncuesta' => $varDataListEncuesta,
            'varDataListVerAlerta' => $varDataListVerAlerta,
        ]);
    }

    public function actionEnviaralertados($id_enviados){
        $model = new Alertas();

        $varDataListenviadosAlerta = (new \yii\db\Query())
                            ->select([
                                'tbl_alertascx.id',
                                'tbl_alertascx.fecha', 
                                'tbl_arbols.name',
                                'tbl_usuarios.usua_id',
                                'tbl_usuarios.usua_nombre', 
                                'tbl_alertascx.tipo_alerta',
                                'tbl_alertascx.archivo_adjunto',
                                'tbl_alertascx.remitentes',
                                'tbl_alertascx.asunto',
                                'tbl_alertascx.comentario',
                            ])
                            ->from(['tbl_alertascx'])

                            ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                  'tbl_arbols.id = tbl_alertascx.pcrc')

                            ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                  'tbl_usuarios.usua_id = tbl_alertascx.valorador')

                            ->where(['=','tbl_alertascx.id',$id_enviados])
                            ->all(); 

        $varId_enviados = null;
        $varFecha_enviados = null;
        $varName_enviados = null;
        $varUsuaNombre_enviados = null;
        $varTipoAlerta_enviados = null;
        $varArchivo_enviados = null;
        $varRemitentes_enviados = null;
        $varAsunto_enviados = null;
        $varComentarios_enviados = null;
        foreach ($varDataListenviadosAlerta as $value) {
            $varId_enviados = $value['id'];
            $varFecha_enviados = $value['fecha'];
            $varName_enviados = $value['name'];
            $varUsuaNombre_enviados = $value['usua_nombre'];
            $varTipoAlerta_enviados = $value['tipo_alerta'];
            $varArchivo_enviados = $value['archivo_adjunto'];
            $varRemitentes_enviados = $value['remitentes'];
            $varAsunto_enviados = $value['asunto'];
            $varComentarios_enviados = $value['comentario'];
        }

        $form = Yii::$app->request->post();
        if ($model->load($form)) {
            $varHtml_enviados = 
            "
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Envio de Alertas</title>
                </head>
                <body class='text-center'>
                    <h1><label style='font-family: sans-serif; color: #1d2d4f;'>Informe de Alertas CX-Management</label></h1>
                    <hr>
                    <h5><label style='font-family: sans-serif;'>Actualmente se tiene una alerta que fue realizada desde CX-Management. Para validar la alerta te recomendamos ingresar al módulo de reporte alertas de CXM y buscarlo para ver resultados.</a></label></h5>
                    <hr>
                    <h3><label style='font-family: sans-serif;'>Comentario Alerta...</a></h3><br>
                    <h5><label style='font-family: sans-serif;'>".$varComentarios_enviados."</a></h5>
                    <hr>
                    <h5><label style='font-family: sans-serif;'>¡Hola equipo! Te comentamos que nos encantaria saber tú opinión, por eso te invitamos a ingresar a CXM y responder la encuesta en el siguiente link <a href='https://qa.grupokonecta.local/qa_managementv2/web/index.php/alertascxm/alertaencuesta?id_alerta=".$id_enviados."'>Ingresar a la encuesta</a></label></h5>
                    <hr>
                    <h7><label style='font-family: sans-serif;'>© CX-Management 2023 - Desarrollado por Konecta</a></label></h>

                </body>
                </html>   
            ";

            $target_path_enviados = "alertas/" . $varArchivo_enviados;
            $varListData_enviados = explode(", ", $model->remitentes);        

            if (count($varListData_enviados) >= 2) {
                for ($i=0; $i < count($varListData_enviados); $i++) { 
                    $varCorreo_enviados = $varListData_enviados[$i];

                    Yii::$app->mailer->compose()
                            ->setTo($varCorreo_enviados)
                            ->setFrom(Yii::$app->params['email_satu_from'])
                            ->setSubject('Alertas CXM - '.$varAsunto_enviados)
                            ->attach($target_path_enviados)
                            ->setHtmlBody($varHtml_enviados)
                            ->send();
                }
            }else{
                Yii::$app->mailer->compose()
                            ->setTo($varListData_enviados)
                            ->setFrom(Yii::$app->params['email_satu_from'])
                            ->setSubject('Alertas CXM - '.$varAsunto_enviados)
                            ->attach($target_path_enviados)
                            ->setHtmlBody($varHtml_enviados)
                            ->send();
            } 

            return $this->redirect(['reportealerta']);
        }

        return $this->render('enviaralertados',[
            'model' => $model,
            'id_enviados' => $id_enviados,
            'varId_enviados' => $varId_enviados,
            'varFecha_enviados' => $varFecha_enviados,
            'varName_enviados' => $varName_enviados,
            'varUsuaNombre_enviados' => $varUsuaNombre_enviados,
            'varTipoAlerta_enviados' => $varTipoAlerta_enviados,
            'varArchivo_enviados' => $varArchivo_enviados,
            'varRemitentes_enviados' => $varRemitentes_enviados,
            'varAsunto_enviados' => $varAsunto_enviados,
            'varComentarios_enviados' => $varComentarios_enviados,
        ]);
    }

    public function actionNotificacionalertas(){
        $model = new Alertas();
        $varDataResultado_Notas = null;
        $varCantidadEncuestas_Notas = null;

        $varUsuarioActual = Yii::$app->user->identity->id;
        $varCCUsuario = (new \yii\db\Query())
                            ->select(['tbl_usuarios.usua_identificacion'])
                            ->from(['tbl_usuarios'])
                            ->where(['=','tbl_usuarios.usua_id',$varUsuarioActual])
                            ->scalar(); 

        $varDocumentos_Notas = [':varDocumentoName'=>$varCCUsuario];

        $varNameJarvis_Notas = Yii::$app->dbjarvis->createCommand('
        SELECT dp_datos_generales.primer_nombre FROM dp_datos_generales
            WHERE 
                dp_datos_generales.documento = :varDocumentoName
            GROUP BY dp_datos_generales.documento ')->bindValues($varDocumentos_Notas)->queryScalar();

        $varUsuarioRed = (new \yii\db\Query())
                            ->select(['tbl_usuarios.usua_usuario'])
                            ->from(['tbl_usuarios'])
                            ->where(['=','tbl_usuarios.usua_id',$varUsuarioActual])
                            ->scalar(); 

        $form = Yii::$app->request->post();
        if ($model->load($form)) {
            $varFecha_BD_N = explode(" ", $model->fecha);

            $varFechaInicio_BD_N = $varFecha_BD_N[0];
            $varFechaFin_BD_N = date('Y-m-d',strtotime($varFecha_BD_N[2]));

            $varDataResultado_Notas = (new \yii\db\Query())
                                ->select([
                                    'tbl_alertascx.id',
                                    'tbl_alertascx.fecha', 
                                    'tbl_arbols.name',
                                    'tbl_usuarios.usua_id',
                                    'tbl_usuarios.usua_nombre', 
                                    'tbl_alertascx.tipo_alerta',
                                    'tbl_alertascx.asunto'
                                ])
                                ->from(['tbl_alertascx'])

                                ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                    'tbl_arbols.id = tbl_alertascx.pcrc')

                                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                    'tbl_usuarios.usua_id = tbl_alertascx.valorador')

                                ->where(['like','tbl_alertascx.remitentes',$varUsuarioRed])
                                ->andwhere(['between','tbl_alertascx.fecha',$varFechaInicio_BD_N,$varFechaFin_BD_N])
                                ->all();

            if (count($varDataResultado_Notas) == 0) {

                $varCorreoJarvis_Notas = Yii::$app->dbjarvis->createCommand('
                SELECT dp_usuarios_red.email FROM dp_usuarios_red
                    WHERE 
                        dp_usuarios_red.documento =  :varDocumentoName')->bindValues($varDocumentos_Notas)->queryScalar();

                $varDataResultado_Notas = (new \yii\db\Query())
                                ->select([
                                    'tbl_alertascx.id',
                                    'tbl_alertascx.fecha', 
                                    'tbl_arbols.name',
                                    'tbl_usuarios.usua_id',
                                    'tbl_usuarios.usua_nombre', 
                                    'tbl_alertascx.tipo_alerta',
                                    'tbl_alertascx.asunto'
                                ])
                                ->from(['tbl_alertascx'])

                                ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                    'tbl_arbols.id = tbl_alertascx.pcrc')

                                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                    'tbl_usuarios.usua_id = tbl_alertascx.valorador')

                                ->where(['like','tbl_alertascx.remitentes',$varCorreoJarvis_Notas])
                                ->andwhere(['between','tbl_alertascx.fecha',$varFechaInicio_BD_N,$varFechaFin_BD_N])
                                ->all();
            }


            $varArraIdAlertas = array();
            foreach ($varDataResultado_Notas as $value) {
                array_push($varArraIdAlertas, $value['id']);
            }
            $varListadoIdAlertas =  explode(",", str_replace(array("#", "'", ";", " "), '', implode(", ",$varArraIdAlertas)));

            $varCantidadEncuestas_Notas = (new \yii\db\Query())
                                ->select(['tbl_alertas_tipoencuestas.id_tipoencuestas'])
                                ->from(['tbl_alertas_tipoencuestas'])
                                ->join('LEFT OUTER JOIN', 'tbl_alertas_encuestasalertas',
                                        'tbl_alertas_tipoencuestas.id_tipoencuestas = tbl_alertas_encuestasalertas.id_tipoencuestas')
                                ->where(['in','tbl_alertas_encuestasalertas.id_alerta',$varListadoIdAlertas])
                                ->andwhere(['=','tbl_alertas_encuestasalertas.usua_id',Yii::$app->user->identity->id])
                                ->andwhere(['=','tbl_alertas_encuestasalertas.anulado',0])
                                ->count(); 
        }

        return $this->render('notificacionalertas',[
            'varNameJarvis_Notas' => $varNameJarvis_Notas,
            'varDataResultado_Notas' => $varDataResultado_Notas,
            'varCantidadEncuestas_Notas' => $varCantidadEncuestas_Notas,   
            'model' => $model,    
        ]);
    }

    public function actionDescargaralertas(){
        $model = new Alertas();
        $varDataResultado_D = array();


        $form = Yii::$app->request->post();
        if ($model->load($form)) {
            $varFecha_BD_D = explode(" ", $model->fecha);

            $varFechaInicio_BD_D = $varFecha_BD_D[0];
            $varFechaFin_BD_D = date('Y-m-d',strtotime($varFecha_BD_D[2]));

            $varDataResultado_D = (new \yii\db\Query())
                            ->select([
                                'tbl_alertascx.id',
                                'tbl_alertascx.fecha', 
                                'tbl_arbols.name',
                                'tbl_usuarios.usua_id',
                                'tbl_usuarios.usua_nombre', 
                                'tbl_alertascx.tipo_alerta'
                            ])
                            ->from(['tbl_alertascx'])

                            ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                  'tbl_arbols.id = tbl_alertascx.pcrc')

                            ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                  'tbl_usuarios.usua_id = tbl_alertascx.valorador')

                            ->where(['between','tbl_alertascx.fecha',$varFechaInicio_BD_D.' 00:00:00',$varFechaFin_BD_D.' 23:59:59'])
                            ->all(); 
        }

        return $this->render('descargaralertas',[
            'model' => $model,
            'varDataResultado_D' => $varDataResultado_D,
        ]);
    }




}

?>


