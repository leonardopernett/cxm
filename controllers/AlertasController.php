<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\data\ArrayDataProvider;
use app\models\Alertas;
use app\models\UploadForm;


class AlertasController extends Controller
{

        public function actions() {
            return [
                'error' => [
                'class' => 'yii\web\ErrorAction',
                ]
            ];
        }

        public function actionError() {

            //ERROR PRESENTADO
            $exception = Yii::$app->errorHandler->exception;

            if ($exception !== null) {
                //VARIABLES PARA LA VISTA ERROR
                $code = $exception->statusCode;
                $name = $exception->getName() . " (#$code)";
                $message = $exception->getMessage();
                //RENDERIZO LA VISTA
                return $this->render('error', [
                            'name' => $name,
                            'message' => $message,
                            'exception' => $exception,
                ]);
            }
        }

            /** Alertas German Mejia Vieco **/

            /**
             * Creacion Alertas
             * * @author German Mejia Vieco
             */

            public function actionIndex() {

                $model = new UploadForm();

                $searchModel = new BaseSatisfaccionSearch();
                $listo = 0;

                if (Yii::$app->request->isPost) {
                     
                    $model->archivo_adjunto = UploadedFile::getInstances($model, 'archivo_adjunto');
                    $user = Yii::$app->user->identity->username;
                    $archivo = date("YmdHis") . $user . str_replace(' ', '', $model->archivo_adjunto['0']->name); 
                    
                    if ($model->upload()) {

                            $modelup = new Alertas();

                            
                            $modelup->fecha = date("Y-m-d H:i:s");
                            $modelup->pcrc = Yii::$app->request->post('BaseSatisfaccionSearch')['pcrc'];
                            $modelup->valorador = Yii::$app->user->identity->id;
                            $modelup->tipo_alerta = Yii::$app->request->post('tipo_alerta');
                            $modelup->archivo_adjunto = $archivo;
                            $modelup->remitentes = Yii::$app->request->post('remitentes');
                            $modelup->asunto = Yii::$app->request->post('asunto');
                            $modelup->comentario = Yii::$app->request->post('comentario');

                            $listo = 1;
                            $modelup->save();
                            return $this->render('alertas', [
                            'searchModel' => $searchModel,
                            'model' => $model,
                            'listo' => $listo,
                ]);
                    }else{
                        
                        $listo = 2;
                    }
                }

                return $this->render('alertas', [
                            'searchModel' => $searchModel,
                            'model' => $model,
                            'listo' => $listo,
                ]);
            }

            /**
             * Envio de correo para alertas
             * * @author German Mejia Vieco
             */

            public function enviarcorreoalertas($fecha, $pcrc, $valorador, $tipo_alerta, $archivo_adjunto, $remitentes, $asunto, $comentario){

                $equipos = \app\models\Arboles::find()->where(['id' => $pcrc])->all();
                $usuario = \app\models\Usuarios::find()->where(['usua_id' => $valorador])->all();

                $destinatario = explode(",", $remitentes); 

                $target_path = "alertas/" . $archivo_adjunto;

                $html ='<div class="col-md-6">
                            <div class="card1 mb" style="display:grid; place-items:center;">
                                <img src="../../images/cxx.png" style="width:120px;" >'
                                "<h2>¡Hola Equipo!</h2>
                                <h3><b>Haz recibido una nueva alerta</b></h3>
                                <h4>Fecha de envio  :</h4>". $fecha 
                                "<h4>Tipo de alerta:</h4>". $tipo_alerta 
                                "<h4>Asunto:</h4>". $asunto  
                                "<h4>Programa PCRC:</h4>". $equipos['0']->name 
                                "<h4>Valorador:</h4> ". $usuario['0']->usua_nombre  
                                "<h4>Comentarios:</h4>". $comentario  
                                "<br><br>"
                                "<h4>Nos encataría saber tu opinión te invitamos a ingresar a <b>CXM</b> y responder la siguiente encuesta.</h4>
                                <br>"
                                '<div style="heigth: 200px;">'
                                <?= Html::a('Ingresar a CXM',  ['encuestasatifaccion'], ['class' => 'btn btn-success btn-lg',//index es para que me redireccione o sea volver a el inicio 
                                                        'style' => 'background-color: #337ab7; ',//color del boton  
                                                        'data-toggle' => 'tooltip',
                                                        'title' => 'Ingresar a CXM'])//titulo del boton  
                                ?>
                                "</div>
                                <br>"
                                '<img src="../../images/link.png" class="img-responsive">'
                            "</div>
                        </div>";

                foreach ($destinatario as $send) {
                    Yii::$app->mailer->compose()
                        ->setTo($send)
                        ->setFrom(Yii::$app->params['email_satu_from'])
                        ->setSubject($asunto)
                        ->attach($target_path)
                        ->setHtmlBody($html)
                        ->send();
                }
                        
            }

            /** Alertas German Mejia Vieco **/

            /**
             * Creacion Alertas
             * * @author German Mejia Vieco
             */

            public function actionAlertasvaloracion() {
                $model = new Alertas();

                $dataProvider = $model->searchTodos();

                $resumenFeedback = (new \yii\db\Query())
                            ->select('b.name AS Programa, c.name AS Cliente, count(a.pcrc) AS Count')
                            ->from('tbl_alertascx a')
                            ->join('INNER JOIN', 'tbl_arbols b', 'b.id = a.pcrc')
                            ->join('INNER JOIN', 'tbl_arbols c', 'b.arbol_id = c.id')
                            ->groupBy('a.pcrc')
                            ->all();
                            

                $detalleLiderFeedback = (new \yii\db\Query())
                            ->select('d.usua_nombre AS Tecnico, b.name AS Programa, c.name AS Cliente, count(a.pcrc) AS Count')
                            ->from('tbl_alertascx a')
                            ->join('INNER JOIN', 'tbl_arbols b', 'b.id = a.pcrc')
                            ->join('INNER JOIN', 'tbl_arbols c', 'b.arbol_id = c.id')
                            ->join('INNER JOIN', 'tbl_usuarios d', 'a.valorador = d.usua_id')
                            ->groupBy('a.valorador, a.pcrc')
                            ->all();


                if ($model->load(Yii::$app->request->post()) && $model->validate()) {

                    $post = Yii::$app->request->post('BaseSatisfaccionSearch');


                    $fecha = $post['fecha'];
                    $pcrc = $post['pcrc'];
                    $responsable = $post['responsable'];

                    if ($fecha != ""){
                        $dates = explode(' - ', $fecha);
                        $startDate = $dates[0];
                        $endDate = $dates[1];
                        $xfecha = 'date(a.fecha) BETWEEN "' . $startDate . '" AND "' . $endDate . '"';
                    }else{
                        $xfecha = "";
                    }

                    if($pcrc != ""){
                        $xpcrc = 'pcrc ="' . $pcrc . '"';
                    }else{
                        $xpcrc = "";
                    }

                    if($responsable != ""){
                        $xresponsable = 'a.valorador ="' . $responsable . '"';
                    }else{
                        $xresponsable = "";
                    }

                    $detalleLiderFeedback = (new \yii\db\Query())
                            ->select('d.usua_nombre AS Tecnico, b.name AS Programa, c.name AS Cliente, count(a.pcrc) AS Count')
                            ->from('tbl_alertascx a')
                            ->join('INNER JOIN', 'tbl_arbols b', 'b.id = a.pcrc')
                            ->join('INNER JOIN', 'tbl_arbols c', 'b.arbol_id = c.id')
                            ->join('INNER JOIN', 'tbl_usuarios d', 'a.valorador = d.usua_id')
                            ->andWhere(':xfecha')
                            ->andWhere(':xpcrc')
                            ->andWhere(':xresponsable')
                            ->addParams([':xfecha'=>$xfecha,':xpcrc'=>$xpcrc,':xresponsable'=>$xresponsable])
                            ->groupBy('a.valorador, a.pcrc')
                            ->all();


                    $resumenFeedback = (new \yii\db\Query())
                            ->select('b.name AS Programa, c.name AS Cliente, count(a.pcrc) AS Count')
                            ->from('tbl_alertascx a')
                            ->join('INNER JOIN', 'tbl_arbols b', 'b.id = a.pcrc')
                            ->join('INNER JOIN', 'tbl_arbols c', 'b.arbol_id = c.id')
                            ->andWhere(':xfecha')
                            ->andWhere(':xpcrc')
                            ->andWhere(':xresponsable')
                            ->addParams([':xfecha'=>$xfecha,':xpcrc'=>$xpcrc,':xresponsable'=>$xresponsable])
                            ->groupBy('a.pcrc')
                            ->all();

                    $dataProvider = (new \yii\db\Query())
                            ->select('a.id as xid, fecha, b.name AS Programa, tipo_alerta, d.usua_nombre AS Tecnico')
                            ->from('tbl_alertascx a')
                            ->join('INNER JOIN', 'tbl_arbols b', 'b.id = a.pcrc')
                            ->join('INNER JOIN', 'tbl_usuarios d', 'a.valorador = d.usua_id')
                            ->andWhere(':xfecha')
                            ->andWhere(':xpcrc')
                            ->andWhere(':xresponsable')
                            ->addParams([':xfecha'=>$xfecha,':xpcrc'=>$xpcrc,':xresponsable'=>$xresponsable])
			    ->orderBy('fecha DESC')
                            ->all();

                }
                return $this->render('alertasview', [
                            'model' => $model,
                            'dataProvider' => $dataProvider,
                            'resumenFeedback' => $resumenFeedback,
                            'detalleLiderFeedback' => $detalleLiderFeedback,
                        ]);
            }


            /**
             * Displays a single BaseSatisfaccion model.
             * @param integer $id
             * @return mixed
             */
            public function actionVeralertas($id) {


                $model = (new \yii\db\Query())
                            ->select('a.fecha AS Fecha, b.name AS Programa, d.usua_nombre AS Tecnico, a.tipo_alerta AS Tipoalerta, a.archivo_adjunto AS Adjunto, a.remitentes AS Destinatarios, a.asunto AS Asunto, a.comentario AS Comentario')
                            ->from('tbl_alertascx a')
                            ->join('INNER JOIN', 'tbl_arbols b', 'b.id = a.pcrc')
                            ->join('INNER JOIN', 'tbl_usuarios d', 'a.valorador = d.usua_id')
                            ->andWhere('a.id = :id')
                            ->addParams([':id'=>$id])
                            ->all();


                return $this->render('veralertas', [
                    'model' => $model['0'],
                ]);

            }


}


