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
use Exception;

  class AlertascxmController extends Controller {

    public function behaviors(){
        return[
            'access' => [
                'class' => AccessControl::classname(),
                'only' => ['index','registraalerta','parametrizaralertas','correogrupal','textcorreo'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function() {
                            return Yii::$app->user->identity->isReportes() || Yii::$app->user->identity->isVerexterno() || Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isVerusuatlmast();
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
      
      
        return $this->render('index');
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

        return $this->render('parametrizaralertas',[
            'modelTipo' => $modelTipo,
            'modelEncuestas' => $modelEncuestas,
            'varDataListTipo' => $varDataListTipo,
            'varDataListEncuesta' => $varDataListEncuesta,
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

    public function actionRegistraalerta($id_procesos){
        $model = new Alertas();
        $modelArchivo = new UploadForm2();
        $varCorreos = null;
        $ruta = null;

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

            return $this->redirect(['registraalerta']);
        }

        return $this->render('registraalerta',[
            'model' => $model,
            'modelArchivo' => $modelArchivo,
            'id_procesos' => $id_procesos,
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
        $varListData_correos = null;

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
                    <table id='tblListadoGrupales'>
                        <thead>
                            <tr>
                                <th class='text-center' align='text-center' scope='col' style='background-color: #C6C6C6;'><label style='font-size: 13px; margin: 30px;'>
                                    <img src='/qa_pruebas/web/images/cx.png' alt='Card image cap' style='height: 110px;'>
                                </th>
                                <th class='text-center' align='text-center' scope='col' style='background-color: #C6C6C6;'><label style='font-size: 18px; margin: 50px;'>Informe de Alertas CX-Management</label></th>
                            </tr>
                            <tr>
                                <th class='text-center' align='text-center' scope='col'>
                                    <label style='font-size: 15px;'><em class='fas fa-envelope' style='font-size: 60px; color: #FFC72C;  margin: 30px;'></em></label>
                                </th>
                                <th class='text-center' align='text-center' scope='col'>                    
                                    <label style='font-size: 13px; margin: 50px;'>¡Hola equipo! Te comentamos que se ha hecho una alerta desde la herramienta de CXM. A continuación te presentamos los datos de la alerta.</label>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class='text-center' align='text-center' >
                                    <label style='font-size: 15px;'><em class='fas fa-hand-point-right' style='font-size: 60px; color: #FFC72C;  margin: 30px;'></em></label>
                                </td>
                                <td class='text-left' align='text-left'>
                                    <label style='font-size: 12px;'><label style='font-size: 15px;'><p>* Fecha de envio: ".$varFechas_correo." </p></label></label>
                                    <br>
                                    <label style='font-size: 12px;'><label style='font-size: 15px;'><p>* Valorador: ".$varValorador_correo." </p></label></label>
                                    <br>
                                    <label style='font-size: 12px;'><label style='font-size: 15px;'><p>* Tipo de Alerta: ".$varTipoAlerta_correo." </p></label></label>
                                    <br>
                                    <label style='font-size: 12px;'><label style='font-size: 15px;'><p>* Programa/Pcrc: ".$varPcrc_correo." </p></label></label>
                                    <br>
                                    <label style='font-size: 12px;'><label style='font-size: 15px;'><p>* Asunto: ".$varAsuntos_correo." </p></label></label>
                                    <br>
                                    <label style='font-size: 12px;'><label style='font-size: 15px;'><p>* Comentarios: ".$varComentarios_correo." </p></label></label>
                                    <br>
                                    <label style='font-size: 12px;'><label style='font-size: 15px;'><p>* Archivo Adjunto: ".$varArchivo_correo."</p></label></label>
                                </td>
                            </tr>
                            <tr>
                                <td class='text-center' align='text-center' colspan='2' style='background-color: #C6C6C6;'>
                                    <label style='font-size: 12px;  margin: 30px;'>¡Hola equipo! Te comentamos que nos encantaria saber tú opinión, por eso te invitamos a ingresar a CXM y responder la encuesta en el siguiente link <a href='http://localhost:8080/qa_pruebas/web/index.php/alertascxm/alertaencuesta?id_alerta=".$varIdAlertas."'></a></label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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


}

?>


