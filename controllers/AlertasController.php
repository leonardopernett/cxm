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

                            //$this->enviarcorreoalertas($modelup->fecha, $modelup->pcrc, $modelup->valorador, $modelup->tipo_alerta, $modelup->archivo_adjunto, $modelup->remitentes, $modelup->asunto, $modelup->comentario);

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

            $html = "<table align='center' border='2'>
                <tr>
                    <th style='padding: 10px;'>Fecha de Envio</th>
                    <th style='padding: 10px;'>Programa</th>
                    <th style='padding: 10px;'>Valorador</th>
                    <th style='padding: 10px;'>Tipo de Alerta</th>
                    <th style='padding: 10px;'>Asunto</th>
                    <th style='padding: 10px;'>Comentario</th>
                </tr>
                <tr>
                    <td style='padding: 10px;'>" . $fecha . "</td>
                    <td style='padding: 10px;'>" . $equipos['0']->name . "</td>
                    <td style='padding: 10px;'>" . $usuario['0']->usua_nombre . "</td>
                    <td style='padding: 10px;'>" . $tipo_alerta . "</td>
                    <td style='padding: 10px;'>" . $asunto  . "</td>
                    <td style='padding: 10px;'>" . $comentario  . "</td>
                </tr>
            </table>";

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


                if(Yii::$app->request->get('prueba') == "exportar"){
                    
                }else{
                    
                }

                
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
                            ->andWhere($xfecha)
                            ->andWhere($xpcrc)
                            ->andWhere($xresponsable)
                            ->groupBy('a.valorador, a.pcrc')
                            ->all();


                    $resumenFeedback = (new \yii\db\Query())
                            ->select('b.name AS Programa, c.name AS Cliente, count(a.pcrc) AS Count')
                            ->from('tbl_alertascx a')
                            ->join('INNER JOIN', 'tbl_arbols b', 'b.id = a.pcrc')
                            ->join('INNER JOIN', 'tbl_arbols c', 'b.arbol_id = c.id')
                            ->andWhere($xfecha)
                            ->andWhere($xpcrc)
                            ->andWhere($xresponsable)
                            ->groupBy('a.pcrc')
                            ->all();

                    $dataProvider = (new \yii\db\Query())
                            ->select('a.id as xid, fecha, b.name AS Programa, tipo_alerta, d.usua_nombre AS Tecnico')
                            ->from('tbl_alertascx a')
                            ->join('INNER JOIN', 'tbl_arbols b', 'b.id = a.pcrc')
                            ->join('INNER JOIN', 'tbl_usuarios d', 'a.valorador = d.usua_id')
                            ->andWhere($xfecha)
                            ->andWhere($xpcrc)
                            ->andWhere($xresponsable)
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
                            ->andWhere('a.id ="' . $id . '"')
                            ->all();


                return $this->render('veralertas', [
                    'model' => $model['0'],
                ]);

            }


}


