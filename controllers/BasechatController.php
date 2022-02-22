<?php

namespace app\controllers;

ini_set('upload_max_filesize', '50M');

use Yii;
use DateTime;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\db\Query;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Reader_DefaultReadFilter;
use yii\web\UploadedFile;
use app\models\FormUploadtigo;
use app\models\BasechatTigo;
use yii\web\NotFoundHttpException;
use app\models\FormUploadalert;
use PHPExcel_Shared_Date;
use app\models\Evaluados;
use yii\db\ActiveRecord;
use app\models\BasechatFormulario;
use app\models\BasechatCategorias;
use app\models\BasechatMotivos;
use \yii\base\Exception;


    class BaseChatController extends \yii\web\Controller {

        public function behaviors(){
            return[
                'access' => [
                        'class' => AccessControl::classname(),
                        'only' => ['index','importarexcel','showformulariogestionar','cancelarformulario','guardaryenviarformulariogestion','consultarcalificacionsub','showbasechat','registrocategorias','registromotivos','showbasechatview','descargarshow','updateusuarios','elegirimportar','importarexcelcol', 'showbasechatcol', 'descargargestion','exportbol','exportcol'],
                        'rules' => [
                            [
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isHacerMonitoreo() || Yii::$app->user->identity->isVerexterno() || Yii::$app->user->identity->isVerdirectivo();
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
            $model = new BasechatTigo();

            $dataProvider = $model->buscarbasechat(Yii::$app->request->post());

            return $this->render('index',[
                'model' => $model,
                'dataProvider' => $dataProvider,
                ]);
        }

        public function actionImportarexcel2(){
            $model = new FormUploadtigo();

                if (Yii::$app->request->isPost) {
                
                    $model->file = UploadedFile::getInstance($model, 'file');

                    if ($model->file && $model->validate()) {
                        $model->file->saveAs('categorias/' . $model->file->baseName . '.' . $model->file->extension);

                        $fila = 1;
                        if (($gestor = fopen('categorias/' . $model->file->baseName . '.' . $model->file->extension, "r")) !== false) {                            
                            while (($datos = fgetcsv($gestor)) !== false) {
                                $numero = count($datos); 

                                $fila++;
                                for ($c=0; $c < $numero; $c++) { 
                                    $varArray = $datos[$c];
                                    $varDatos = explode(";", utf8_encode($varArray));

                                    
                                }
                            }
                            fclose($gestor);
                        }
                    }
                
                }
           

            return $this->render('importarexcel',[
                'model' => $model,
                ]);
        }


        public function actionImportarexcel(){
            $model = new FormUploadtigo();

                if ($model->load(Yii::$app->request->post()))
                {
                    $model->file = UploadedFile::getInstances($model, 'file');

                    if ($model->file && $model->validate()) {
                        foreach ($model->file as $file) {
                            $fecha = date('Y-m-d-h-i-s');
                            $user = Yii::$app->user->identity->username;
                            $name = $fecha . '-' . $user;
                            $file->saveAs('categorias/' . $name . '.' . $file->extension);
                            $this->Importexcel($name);

                            return $this->redirect('index');
                        }
                    }
               }
           

            return $this->render('importarexcel',[
                'model' => $model,
                ]);
        }

        public function Importexcel($name){
            $inputFile = 'categorias/' . $name . '.xlsx';
                try{
                    $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
                    $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($inputFile);

                }catch(Exception $e)
                {
                    die('Error');
                }

                $sheet = $objPHPExcel->getSheet(0);
                $highestRow = $sheet->getHighestRow();
                $highestcolumn = $sheet->getHighestColumn();

                for( $row = 4; $row <= $highestRow; $row++)
                {
        if ($sheet->getCell("B".$row)->getValue() != null) {

                    $InvDateChange= $sheet->getCell("C".$row)->getValue();
                    $InvDate= PHPExcel_Shared_Date::ExcelToPHPObject($InvDateChange)->format('Y-m-d H:i:s');
                    $anio = date("Y", strtotime($InvDate));
                    $mes = date("m", strtotime($InvDate));
                    $dia = date("d", strtotime($InvDate));
                    $hora = date("H", strtotime($InvDate));
                    $minutos = date("i", strtotime($InvDate));
                    $segundos = date("s", strtotime($InvDate));

                    
                    $fechaencuesta = $anio.'-'.$mes.'-'.$dia.' '.$hora.':'.$minutos.':'.$segundos;
                    

                    $varfechatransaccion = $sheet->getCell("U".$row)->getValue();
                    if ($varfechatransaccion != null) {
                        $varfechatransaccion = PHPExcel_Shared_Date::ExcelToPHPObject($varfechatransaccion)->format('Y-m-d');
                        $anioext = date("Y", strtotime($varfechatransaccion));
                        $mesext = date("m", strtotime($varfechatransaccion));
                        $diaext = date("d", strtotime($varfechatransaccion));
                        $varextraerfecha = $anioext.'-'.$mesext.'-'.$diaext;
                    }else{
                        $varextraerfecha = null;
                    }


                    $varfechartaChange = $sheet->getCell("F".$row)->getValue();
                    $varfecharta = PHPExcel_Shared_Date::ExcelToPHPObject($varfechartaChange)->format('Y-m-d H:i:s');
                    $aniorta = date("Y", strtotime($varfecharta));
                    $mesrta = date("m", strtotime($varfecharta));
                    $diarta = date("d", strtotime($varfecharta));                    
                    $horarta = date("H", strtotime($varfecharta));
                    $minutosrta = date("i", strtotime($varfecharta));
                    $segundosrta = date("s", strtotime($varfecharta));


		    $varrtafecha = $aniorta.'-'.$mesrta.'-'.$diarta.' '.$horarta.':'.$minutosrta.':'.$segundosrta;
                    
                    $varrtafecha = strtotime('2 hours', strtotime($varrtafecha));
                    $varrtafecha = date ( 'Y-m-d H:i:s' , $varrtafecha );

                    $vartipo = $sheet->getCell("IN".$row)->getValue();
                    if ($vartipo == "Mensajería :: WhatsApp") {                        
                        $varpcrc = 3272;
                        $varcliente = 2919;
                        $varrn = 'BW';
                        $varinstitucion = '27';
                        $varindustria = '27';
                    }else{
                         $varpcrc = 3272;
                         $varcliente = 2919;                        
                         $varrn = 'BW';
                         $varinstitucion = '27';
                         $varindustria = '27';
                    }
                    
                    $varnaps = $sheet->getCell("AZ".$row)->getValue();
                    if ($varnaps >= 9) {
                        $txtnps = "FELICITACION";
                    }else{
                        if ($varnaps <= 6) {
                            $txtnps = "CRITICA";
                        }else{
                            $txtnps = "NEUTRO";
                        }
                    }

                    $varcompleteasesor = $sheet->getCell("EE".$row)->getValue();
                    
                    $rest = Yii::$app->db->createCommand("select distinct dusuario_red from tbl_evaluados_tigo where anulado = 0 and usua_tigo = '$varcompleteasesor'")->queryScalar();
                    $varbaseencuesta = $sheet->getCell("B".$row)->getValue();
                    $varfechab = date("Y-m-d H:i:s");

                    Yii::$app->db->createCommand()->insert('tbl_base_satisfaccion',[
                                        'chat_transfer' => $varbaseencuesta,
                                        'estado' => 'Cerrado',
                                        'usado' => 'SI',
                                        'tipo_inbox' => 'NINGUNO',
                                        'created' => $varfechab,
                                        ])->execute();  

                    $varbasesatisfaccionid = Yii::$app->db->createCommand("select id from tbl_base_satisfaccion where chat_transfer = $varbaseencuesta and estado = 'Cerrado' and usado = 'SI' and tipo_inbox = 'NINGUNO' AND created = '$varfechab'")->queryScalar();
                    

                    Yii::$app->db->createCommand()->insert('tbl_basechat_tigob',[                                        
                                        'idtransaccion'=> $sheet->getCell("A".$row)->getValue(),
                                        'idencuesta' => $varbaseencuesta,
                                        'fecha_creacion'=> $fechaencuesta,
                                        'invitation_status'=> $sheet->getCell("D".$row)->getValue(),
                                        'survey_status'=> $sheet->getCell("E".$row)->getValue(),
                                        'fecha_respuesta'=> $varrtafecha,
                                        'has_alert'=> $sheet->getCell("G".$row)->getValue(),
                                        'tipo_alerta'=> $sheet->getCell("H".$row)->getValue(),
                                        'punto_contacto'=> $sheet->getCell("I".$row)->getValue(),
                                        'unit'=> $sheet->getCell("L".$row)->getValue(),
                                        'nombre_cliente'=> $sheet->getCell("N".$row)->getValue(),
                                        'mercado'=> $sheet->getCell("P".$row)->getValue(),
                                        'fecha_transaccion'=> $varextraerfecha,
                                        'telefono_cliente'=> $sheet->getCell("AB".$row)->getValue(),
                                        'journey'=> $sheet->getCell("AM".$row)->getValue(),
                                        'bu'=> $sheet->getCell("AQ".$row)->getValue(),
                                        'nps'=> $sheet->getCell("AZ".$row)->getValue(),
                                        'comentario_adicional'=> $sheet->getCell("BA".$row)->getValue(),
                                        'ces'=> $sheet->getCell("BB".$row)->getValue(),
                                        'csat'=> $sheet->getCell("BC".$row)->getValue(),
                                        'comentario_fcr'=> $sheet->getCell("BD".$row)->getValue(),
                                        'fcr'=> $sheet->getCell("BE".$row)->getValue(),
                                        'comentario_adicionalfcr'=> $sheet->getCell("CQ".$row)->getValue(),
                                        'encuesta_movil'=> $sheet->getCell("DU".$row)->getValue(),
                                        'id_supervisor'=> $sheet->getCell("EA".$row)->getValue(),
                                        'supervisor'=> $sheet->getCell("EB".$row)->getValue(),
                                        'id_agente'=> $sheet->getCell("EE".$row)->getValue(),
                                        'tipo_producto'=> $sheet->getCell("EG".$row)->getValue(),
                                        'team_leader'=> $sheet->getCell("EN".$row)->getValue(),
                                        'id_team_leader'=> $sheet->getCell("EO".$row)->getValue(),
                                        'encuesta_alerta'=> $sheet->getCell("EW".$row)->getValue(),
                                        'forma_envio'=> $sheet->getCell("FD".$row)->getValue(),
                                        'encontro_buscaba'=> $sheet->getCell("FH".$row)->getValue(),
                                        'topicos_nps'=> $sheet->getCell("FM".$row)->getValue(),
                                        'b2b'=> $sheet->getCell("FN".$row)->getValue(),
                                        'conocimiento'=> $sheet->getCell("IP".$row)->getValue(),
                                        'digital_cliente_enc'=> $sheet->getCell("IR".$row)->getValue(),
                                        'uno_mas_contactos'=> $sheet->getCell("IX".$row)->getValue(),
                                        'tipo_canal_digital'=> $sheet->getCell("IY".$row)->getValue(),
                                        'unidad_identificador'=> $sheet->getCell("IZ".$row)->getValue(),
                                        'unidad_nombre'=> $sheet->getCell("JA".$row)->getValue(),
                                        'digital_form'=> $sheet->getCell("JH".$row)->getValue(),
                                        'digital_b2b'=> $sheet->getCell("JO".$row)->getValue(),
                                        'digital_canal'=> $sheet->getCell("JQ".$row)->getValue(),
                                        'tipo_producto_digital'=> $sheet->getCell("JS".$row)->getValue(),
                                        'msisdn'=> $sheet->getCell("JT".$row)->getValue(),
                                        'tipo_ambiente'=> $sheet->getCell("JU".$row)->getValue(),
                                        'email_asesor'=> $sheet->getCell("JW".$row)->getValue(),
                                        'nombre_grupo'=> $sheet->getCell("JX".$row)->getValue(),
                                        'nombre_asesor'=> $sheet->getCell("JY".$row)->getValue(),
                                        'ticked_id'=> $sheet->getCell("JZ".$row)->getValue(),
                                        'ticket_via'=> $sheet->getCell("KB".$row)->getValue(),
                                        'digital_subcanal'=> $sheet->getCell("KH".$row)->getValue(),
                                        'digital_comentarioadic'=> $sheet->getCell("PF".$row)->getValue(),
                                        'tagged_original'=> $sheet->getCell("QI".$row)->getValue(),

                                        
                                        'evaluadoid' => $rest,
                                        'rn' => $varrn,
                                        'institucion' => $varinstitucion,
                                        'industria' => $varindustria,
                                        'pcrc' => $varpcrc,
                                        'cliente' => $varcliente,
                                        'tipologia' => $txtnps,
                                        'estado' => 'Abierto',
                                        'basesatisfaccion_id' => $varbasesatisfaccionid,
                                        'fechacreacion' => date("Y-m-d"),
                                        'anulado' => 0,
                                        'usua_id' => Yii::$app->user->identity->id,

                                        'motivo_contacto'=> $sheet->getCell("QJ".$row)->getValue(),
                                        'categoria'=> $sheet->getCell("QK".$row)->getValue(),
                                        'motivo'=> $sheet->getCell("QL".$row)->getValue(),
                                        'observacion'=> $sheet->getCell("QM".$row)->getValue(),
                                        'imputable'=> $sheet->getCell("QN".$row)->getValue(),
                                        'analista'=> $sheet->getCell("QO".$row)->getValue(),
                                        ])->execute();                
            }
                }
        }

        public function actionShowformulariogestion($basesatisfaccion_id, $preview, $aleatorio = null, $fill_values, $view = "index",$banderaescalado = false, $idtmp = null) {
                
                
                $modelBase = BasechatTigo::find()->where(['basesatisfaccion_id' => $basesatisfaccion_id])->one();
                
                //REDIRECT CORRECTO
                $redct =  'index';
                //DATOS QUE SERAN ENVIADOS AL FORMULARIO
                $data = new \stdClass();
                //CONSULTAS PARA COMPLETAR INFO DE TMPEJECUCIONFORMULARIO
                $evaluado = \app\models\Evaluados::findOne(["dsusuario_red" => trim($modelBase->evaluadoid)]);

                if ((is_null($evaluado) || empty($evaluado) || $evaluado == '') && ($preview != 1 && $fill_values != 1)) {
                    $msg = \Yii::t('app', 'No se encuentra el evaluado asociado: ' . $modelBase->evaluadoid . '. Para realizar la gestión haga la creación de los datos del evaluado y asígnele un equipo y lider');
                    Yii::$app->session->setFlash('danger', $msg);
                    return $this->redirect([$redct]);
                }


                $modelReglaNegocio = \app\models\Reglanegocio::findOne(["cod_industria" => $modelBase->industria, "cod_institucion" => $modelBase->institucion, "pcrc" => $modelBase->pcrc, "rn" => $modelBase->rn]);
                
                $usua_id = Yii::$app->user->identity->id;
                if ($modelBase->usado == "SI" && $modelBase->responsable != Yii::$app->user->identity->username && $preview != 1) {
                    return $this->redirect([$redct]);
                }                

                if ($modelBase->estado != 'Cerrado' && $preview != 1) {
                    //MARCO EL REGISTRO COMO TOMADO
                    $modelBase->usado = "SI";
                    $modelBase->responsable = Yii::$app->user->identity->username;
                    $modelBase->save();
                }

                if (count($modelReglaNegocio) == 0) {
                    $msg = \Yii::t('app', 'Este registro no tiene una parametrización asociada:' . $modelBase->id);
                    Yii::$app->session->setFlash('danger', $msg);
                    return $this->redirect([$redct]);
                }

                /* Recalcular el lider apartir del evaluado */
                //Primero valido que el evaluado pertenezca a un equipo
                $equipoevaluado = null;
                if ((!is_null($evaluado) || !empty($evaluado) || $evaluado != '')) {
                    $equipoevaluado = \app\models\EquiposEvaluados::find()->select('eq.evaluado_id, eq.equipo_id')
                            ->from('tbl_equipos_evaluados eq')
                            ->join('INNER JOIN', 'tbl_evaluados e', 'e.id = eq.evaluado_id')
                            ->where('e.dsusuario_red = "' . $evaluado->dsusuario_red . '"')
                            ->one();
                }
                
                if ((is_null($equipoevaluado) || empty($equipoevaluado) || $equipoevaluado == '') && ($preview != 1 && $fill_values != 1)) {
                    $msg = \Yii::t('app', 'El evaluado ' . $modelBase->evaluadoid . ' no está incluido en algún equipo');
                    Yii::$app->session->setFlash('danger', $msg);
                    return $this->redirect([$redct]);
                }

                if ((!is_null($equipoevaluado) || !empty($equipoevaluado) || $equipoevaluado != '')) {
                    $equipo = \app\models\Equipos::findOne(['id' => $equipoevaluado->equipo_id]);
                    $usuario = \app\models\Usuarios::findOne(['usua_id' => $equipo->usua_id]);
                    
                    $modelBase->lider_equipo = ($modelBase->lider_equipo == '' || is_null($modelBase->lider_equipo)) ? $usuario->usua_nombre : $modelBase->lider_equipo;
                    
                    $modelBase->save();
                }

                /* FIN Recalcular el lider apartir del evaluado */
                /*se valida la variable $banderaescalado, en el caso de ser true es una valoracion escalada y cargo el
                tmpejecucionformulario dependiendo del id tmp en la variable $idtmp. Si es false continua
                con la ejecucion que tiene actualmente*/
                if ($banderaescalado) {
                    $TmpForm = \app\models\Tmpejecucionformularios::findOne(['id' => $idtmp, 'basesatisfaccion_id' => $modelBase->idencuesta]);
                       $data->formulario = \app\models\Formularios::find()->where(['id' => $modelReglaNegocio->id_formulario])->one();
                       if (!isset($TmpForm->subi_calculo)) {
                           
                           if (isset($data->formulario->subi_calculo)) {
                               $TmpForm->subi_calculo = $data->formulario->subi_calculo;
                                         
                               $array_indices_TmpForm = \app\models\Textos::find()
                                       ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                                       ->where('id IN (' . $TmpForm->subi_calculo . ')')
                                       ->asArray()
                                       ->all();
                               foreach ($array_indices_TmpForm as $value) {
                                   $data->indices_calcular[$value['id']] = $value['text'];
                               }
                           }
                       } else {
                           if (isset($data->formulario->subi_calculo)) {
                               $array_indices_TmpForm = \app\models\Textos::find()
                                       ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                                       ->where('id IN (' . $TmpForm->subi_calculo . ')')
                                       ->asArray()
                                       ->all();
                               foreach ($array_indices_TmpForm as $value) {
                                   $data->indices_calcular[$value['id']] = $value['text'];
                               }
                           }
                       }
                       
                }else{
                    $validarEjecucionForm = \app\models\Ejecucionformularios::findOne(['basesatisfaccion_id' => $modelBase->basesatisfaccion_id]);
                    
                   //OBTENGO EL FORMULARIO
                   if (is_null($validarEjecucionForm)) {
                        /* luego de la validacion en la tabla de ejecucionformularios,
                        valido en tmpejecucionformularios si existe para evitar creacion de un tmp adicional */
                        $sneditable = 1;
                        $condition = [
                            "usua_id" => Yii::$app->user->id,
                            "arbol_id" => $modelBase->pcrc,
                            "basesatisfaccion_id" => $modelBase->basesatisfaccion_id,
                            "sneditable" => $sneditable,
                        ];
                        $validarTmpejecucionForm = \app\models\Tmpejecucionformularios::findOne($condition);
                        
                        if (is_null($validarTmpejecucionForm)) {
                            $TmpForm = new \app\models\Tmpejecucionformularios();
                           $TmpForm->dimension_id = 1;
                           $TmpForm->arbol_id = $modelBase->pcrc;
                           $TmpForm->usua_id = Yii::$app->user->id;
                           $TmpForm->formulario_id = $modelReglaNegocio->id_formulario;
                           $TmpForm->created = date("Y-m-d H:i:s");
                           $TmpForm->basesatisfaccion_id = $modelBase->basesatisfaccion_id;
                           if ((!is_null($equipoevaluado) || !empty($equipoevaluado) || $equipoevaluado != '') && (!is_null($evaluado) || !empty($evaluado) || $evaluado != '')) {
                               $TmpForm->usua_id_lider = $equipo->usua_id;
                               $TmpForm->evaluado_id = $evaluado->id;
                           }
                           //busco el formulario al cual esta atado la valoracion a cargar
                           //y valido de q si tenga un formulario, de lo contrario se fija 
                           //en 1 por defecto
                           $data->formulario = \app\models\Formularios::find()->where(['id' => $modelReglaNegocio->id_formulario])->one();
                           if (!isset($TmpForm->subi_calculo)) {
                               
                               if (isset($data->formulario->subi_calculo)) {
                                   $TmpForm->subi_calculo = $data->formulario->subi_calculo;
                                   
                                   $array_indices_TmpForm = \app\models\Textos::find()
                                           ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                                           ->where('id IN (' . $TmpForm->subi_calculo . ')')
                                           ->asArray()
                                           ->all();
                                   foreach ($array_indices_TmpForm as $value) {
                                       $data->indices_calcular[$value['id']] = $value['text'];
                                   }
                               }
                           } else {
                               if (isset($data->formulario->subi_calculo)) {
                                   $array_indices_TmpForm = \app\models\Textos::find()
                                           ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                                           ->where('id IN (' . $TmpForm->subi_calculo . ')')
                                           ->asArray()
                                           ->all();
                                   foreach ($array_indices_TmpForm as $value) {
                                       $data->indices_calcular[$value['id']] = $value['text'];
                                   }
                               }
                           }
                           date_default_timezone_set('America/Bogota');
                           $TmpForm->hora_inicial = date("Y-m-d H:i:s");
                           $TmpForm->save();
                           $TmpForm = \app\models\Tmpejecucionformularios::findOne($TmpForm->id);
                           $TmpForm->dimension_id = ($modelBase->tipo_inbox == 'NORMAL') ? "" : 1;
                        } else {
                            $TmpForm = $validarTmpejecucionForm;
                            $data->formulario = \app\models\Formularios::find()->where(['id' => $modelReglaNegocio->id_formulario])->one();
                               if (!isset($TmpForm->subi_calculo)) {
                                   
                                   if (isset($data->formulario->subi_calculo)) {
                                       $TmpForm->subi_calculo = $data->formulario->subi_calculo;
                                       
                                       $array_indices_TmpForm = \app\models\Textos::find()
                                               ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                                               ->where('id IN (' . $TmpForm->subi_calculo . ')')
                                               ->asArray()
                                               ->all();
                                       foreach ($array_indices_TmpForm as $value) {
                                           $data->indices_calcular[$value['id']] = $value['text'];
                                       }
                                   }
                               } else {
                                   if (isset($data->formulario->subi_calculo)) {
                                       $array_indices_TmpForm = \app\models\Textos::find()
                                               ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                                               ->where('id IN (' . $TmpForm->subi_calculo . ')')
                                               ->asArray()
                                               ->all();
                                       foreach ($array_indices_TmpForm as $value) {
                                           $data->indices_calcular[$value['id']] = $value['text'];
                                       }
                                   }
                               }

                            if($validarTmpejecucionForm->hora_inicial == ""){
                                date_default_timezone_set('America/Bogota');
                                $validarTmpejecucionForm->hora_inicial = date("Y-m-d H:i:s");
                                $validarTmpejecucionForm->save();
                            }
                        }      
                   } else {
                       $formId = \app\models\Ejecucionformularios::llevarATmp($validarEjecucionForm->id, $usua_id);
                       $TmpForm = \app\models\Tmpejecucionformularios::findOne(['id' => $formId['0']['tmp_id'], 'basesatisfaccion_id' => $modelBase->basesatisfaccion_id]);
                       $data->formulario = \app\models\Formularios::find()->where(['id' => $modelReglaNegocio->id_formulario])->one();
                       if (!isset($TmpForm->subi_calculo)) {
                           
                           if (isset($data->formulario->subi_calculo)) {
                               $TmpForm->subi_calculo = $data->formulario->subi_calculo;
                               
                               $array_indices_TmpForm = \app\models\Textos::find()
                                       ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                                       ->where('id IN (' . $TmpForm->subi_calculo . ')')
                                       ->asArray()
                                       ->all();
                               foreach ($array_indices_TmpForm as $value) {
                                   $data->indices_calcular[$value['id']] = $value['text'];
                               }
                           }
                       } else {
                           if (isset($data->formulario->subi_calculo)) {
                               $array_indices_TmpForm = \app\models\Textos::find()
                                       ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                                       ->where('id IN (' . $TmpForm->subi_calculo . ')')
                                       ->asArray()
                                       ->all();
                               foreach ($array_indices_TmpForm as $value) {
                                   $data->indices_calcular[$value['id']] = $value['text'];
                               }
                           }
                        }
                   }   
                }


                
                $data->tmp_formulario = $TmpForm;
                $data->basesatisfaccion = $modelBase;
                if ((!is_null($equipoevaluado) || !empty($equipoevaluado) || $equipoevaluado != '') && (!is_null($evaluado) || !empty($evaluado) || $evaluado != '')) {
                    $data->equipo_id = $equipoevaluado->equipo_id;
                    $data->usua_id_lider = $equipo->usua_id;
                    //NOMBRE DEL EVALUADO
                    $data->evaluado = $evaluado->name;
                } else {
                    $data->equipo_id = "";
                    $data->usua_id_lider = "";
                }

                //INFORMACION ADICIONAL
                $arbol = \app\models\Arboles::findOne($TmpForm->arbol_id);
                $data->info_adicional = [
                    'problemas' => $arbol->snactivar_problemas,
                    'tipo_llamada' => $arbol->snactivar_tipo_llamada
                ];
                $data->ruta_arbol = $arbol->dsname_full;
                $data->dimension = \app\models\Dimensiones::findOne($TmpForm->dimension_id);
                $data->detalles = \app\models\Tmpejecucionbloquedetalles::getAllByFormId($TmpForm->id);
                $data->tmpBloques = \app\models\Tmpejecucionbloques::findAll(['tmpejecucionformulario_id' => $TmpForm->id, 'snnousado' => 1]);
                $data->totalBloques = \app\models\Tmpejecucionbloques::findAll(['tmpejecucionformulario_id' => $TmpForm->id]);
                //CALIFICACIONES
                $tmp_calificaciones_ids = $tmp_tipificaciones_ids = array();
                foreach ($data->detalles as $j => $d) {
                    if (!in_array($d->calificacion_id, $tmp_calificaciones_ids)) {
                        $tmp_calificaciones_ids[] = $d->calificacion_id;
                    }
                    if (!in_array($d->tipificacion_id, $tmp_tipificaciones_ids)) {
                        $tmp_tipificaciones_ids[] = $d->tipificacion_id;
                    }
                    if ($d->tipificacion_id != null) {
                        $data->detalles[$j]->tipif_seleccionados = \app\models\TmpejecucionbloquedetallesTipificaciones::getTipificaciones($d->id);
                    } else {
                        $data->detalles[$j]->tipif_seleccionados = array();
                    }
                }

                //CALIFICACIONES Y TIPIFICACIONES
                $data->calificaciones = \app\models\Calificaciondetalles::getDetallesFromIds($tmp_calificaciones_ids);
                $data->calificacionesArray = \app\models\Calificaciondetalles::getDetallesFromIdsAsArray($tmp_calificaciones_ids);
                $data->tipificaciones = \app\models\Tipificaciondetalles::getDetallesFromIds($tmp_tipificaciones_ids);

                //TRANSACCIONES Y ENFOQUES
                $data->transacciones = \yii\helpers\ArrayHelper::map(\app\models\Transacions::find()->all(), 'id', 'name');
                $data->enfoques = \app\models\Tableroenfoques::find()->asArray()->all();

                //FORMULARIO ID
                $data->formulario_id = $TmpForm->id;
                /* OBTIENE EL LISTADO DETALLADO DE TABLERO DE EXPERIENCIAS Y LLAMADA
                  EN MODO VISUALIZACIÓN FORMULARIO. */
                $data->tablaproblemas = \app\models\Ejecuciontableroexperiencias::
                                find()
                                ->where(["ejecucionformulario_id" => $TmpForm->ejecucionformulario_id])->all();
                $data->tablallamadas = \app\models\Ejecuciontiposllamada::getTabLlamByIdEjeForm($TmpForm->ejecucionformulario_id);
                $data->list_Add_feedbacks = \app\models\Tmpejecucionfeedbacks::getJoinTipoFeedbacks($TmpForm->id);
                $data->preguntas = \app\models\ParametrizacionEncuesta::find()->select("tbl_preguntas.id,tbl_preguntas.pre_indicador,tbl_preguntas.enunciado_pre,tbl_preguntas.categoria,tbl_categorias.nombre")
                                ->join("INNER JOIN", "tbl_preguntas", "tbl_parametrizacion_encuesta.id = tbl_preguntas.id_parametrizacion")
                                ->join("INNER JOIN", "tbl_categorias", "tbl_categorias.id = tbl_preguntas.categoria")
                                ->where(["cliente" => $modelBase->cliente, "programa" => $modelBase->pcrc])->asArray()->all();
                // Aqui
                $data->recategorizar = BasechatTigo::getCategoriaschat($modelBase->basesatisfaccion_id);
                $data->dimension = \app\models\Dimensiones::getDimensionsListForm();
                if (count($data->preguntas) == 0) {
                    $msg = \Yii::t('app', 'No existe parametrización asociada para este formulario:' . $modelBase->basesatisfaccion_id);
                    Yii::$app->session->setFlash('danger', $msg);
                    return $this->redirect([$redct]);
                }
                
                //PREVIEW
                $data->preview = $preview == 1 ? true : false;
                $data->aleatorio = $aleatorio == 1 ? true : false;
                $data->fill_values = $fill_values;
                //VALIDO Q  LA REGLA DE NEGOCIO TENGA UN FORMULARIO ASOCIADO
                $form_val = \app\models\Formularios::findOne($modelReglaNegocio->id_formulario);
        

                if($data->tmp_formulario->hora_inicial != "" AND $data->tmp_formulario->hora_final != ""){
                    $inicial = new DateTime($data->tmp_formulario->hora_inicial);
                    $final = new DateTime($data->tmp_formulario->hora_final);

                    $dteDiff  = $inicial->diff($final);

                    $dteDiff->format("Y-m-d H:i:s");

                    

                    $data->fecha_inicial = $data->tmp_formulario->hora_inicial;
                    $data->fecha_final = $data->tmp_formulario->hora_final;

                    if ($dteDiff->h <= 9){
                        $hour = "0".$dteDiff->h;
                    }else{
                        $hour = $dteDiff->h;
                    }

                    if ($dteDiff->i <= 9){
                        $minute = "0".$dteDiff->i;
                    }else{
                        $minute = $dteDiff->i;
                    }

                    if ($dteDiff->s <= 9){
                        $seconds = "0".$dteDiff->s;
                    }else{
                        $seconds = $dteDiff->s;
                    }

                    $data->minutes = $hour . ":" . $minute . ":" . $seconds;
                }

                

                if (empty($form_val)) {
                    $msg = \Yii::t('app', 'No existe formulario asociada para esta gestión:' . $modelBase->basesatisfaccion_id);
                    Yii::$app->session->setFlash('danger', $msg);
                    return $this->render("showformulariosatisfaccion", ["data" => $data,"view" => $view, "formulario" => false, 'banderaescalado' => false]);
                }

                /* CONSULTO LA TABLA DE RESPOSABILIDAD */
                $data->responsabilidad = ArrayHelper::map(
                                \app\models\Responsabilidad::find()
                                        ->where([
                                            'arbol_id' => $modelBase->pcrc,
                                        ])
                                        ->asArray()->all(), 'nombre', 'nombre', 'tipo'
                );
                


                return $this->render('showformulariosatisfaccion', [
                            'data' => $data,
                            'view' => $view,
                            'formulario' => true,
                            'banderaescalado' => false
                ]);
            }

            public function actionCancelarformulario($id, $tmp_form = null) {

                $model = BasechatTigo::find()->where(['basesatisfaccion_id' => $id])->one();
                
                $redct = 'index';
                if (Yii::$app->user->identity->username == $model->responsable) {
                    $model->usado = "NO";
                    $model->save();
                }
                if (!is_null($tmp_form)) {
                    \app\models\Tmpejecucionformularios::deleteAll(["id" => $tmp_form]);
                }
                return $this->redirect(['index']);
            }

            public function actionConsultarcalificacionsubi() {

                $arrCalificaciones = !$_POST['calificaciones'] ? array() : $_POST['calificaciones'];
                $arrTipificaciones = !isset($_POST['tipificaciones']) ? array() : $_POST['tipificaciones'];
                $arrSubtipificaciones = !isset($_POST['subtipificaciones']) ? array() : $_POST['subtipificaciones'];
                $arrComentariosSecciones = !$_POST['comentarioSeccion'] ? array() : $_POST['comentarioSeccion'];
                $arrCheckPits = !isset($_POST['checkPits']) ? array() : $_POST['checkPits'];
                $arrayForm = $_POST;
                $arrFormulario = [];
                /* Variables para conteo de bloques */
                $arrayCountBloques = [];
                $arrayBloques = [];
                $count = 0;
                /* fin de variables */
                $tmp_id = $_POST['tmp_formulario_id'];
                $basesatisfaccion_id = $_POST['basesatisfaccion_id'];
                $arrFormulario["dimension_id"] = $_POST['dimension'];
                $arrFormulario["dsruta_arbol"] = $_POST['ruta_arbol'];
                $arrFormulario["dscomentario"] = $_POST['comentarios_gral'];
                $arrFormulario["dsfuente_encuesta"] = $_POST['fuente'];
                $arrFormulario["transacion_id"] = $_POST['transacion_id'];
                $arrFormulario["sn_mostrarcalculo"] = 1;
                $modelBase = BasechatTigo::find()->where(['basesatisfaccion_id' => $basesatisfaccion_id])->one();
                
                $arrFormulario["usua_id_lider"] = $_POST['form_lider_id'];
                $arrFormulario["equipo_id"] = $_POST['form_equipo_id'];
                
                //CONSULTA DEL FORMULARIO
                $data = \app\models\Tmpejecucionformularios::findOne($tmp_id);

                if ($_POST['subi_calculo'] != '') {
                    $data->subi_calculo .=',' . $_POST['subi_calculo'];
                    $data->save();
                }
                
                //IF TODOS LOS BLOQUES ESTAN USADOS SETEO ARRAY VACIO
                if (!isset($arrayForm['bloque'])) {
                    $arrayForm['bloque'] = [];
                }
                
                /* INTENTO GUARDAR LOS FORMULARIOS */
                try {
                    /* EDITO EL TMP FORMULARIO */
                    $model = \app\models\Tmpejecucionformularios::find()->where(["id" => $tmp_id])->one();
                    $model->usua_id_actual = Yii::$app->user->identity->id;
                    $model->save();
                    \app\models\Tmpejecucionformularios::updateAll($arrFormulario, ["id" => $tmp_id]);
                    \app\models\Tmpejecucionsecciones::updateAll(['snna' => 0], ['tmpejecucionformulario_id' => $tmp_id]);
                    \app\models\Tmpejecucionbloques::updateAll(['snna' => 0], ['tmpejecucionformulario_id' => $tmp_id]);

                    $bloquesFormtmp = \app\models\Tmpejecucionbloques::findAll(['tmpejecucionformulario_id' => $tmp_id]);
                    foreach ($bloquesFormtmp as $bloquetmp) {
                        if (array_key_exists($bloquetmp->bloque_id, $arrayForm['bloque'])) {
                            $bloquetmp->snnousado = 1;
                            $bloquetmp->save();
                            $arrDetalleForm = [];
                            $arrDetalleForm["calificacion_id"] = -1;
                            $arrDetalleForm["calificaciondetalle_id"] = -1;
                            \app\models\Tmpejecucionbloquedetalles::updateAll($arrDetalleForm, ['tmpejecucionformulario_id' => $tmp_id,
                                'bloque_id' => $bloquetmp->bloque_id]);
                        } else {
                            $bloquetmp->snnousado = 0;
                            $bloquetmp->save();
                        }
                    }
                    /* GUARDO LAS CALIFICACIONES */
                    foreach ($arrCalificaciones as $form_detalle_id => $calif_detalle_id) {
                        $arrDetalleForm = [];
                        //se valida que existan check de pits seleccionaddos y se valida
                        //que exista el del bloquedetalle actual para actualizarlo
                        if (count($arrCheckPits) > 0) {
                            if (isset($arrCheckPits[$form_detalle_id])) {
                                $arrDetalleForm["c_pits"] = $arrCheckPits[$form_detalle_id];
                            }
                        }
                        if (empty($calif_detalle_id)) {
                            $arrDetalleForm["calificaciondetalle_id"] = -1;
                        } else {
                            $arrDetalleForm["calificaciondetalle_id"] = $calif_detalle_id;
                        }
                        \app\models\Tmpejecucionbloquedetalles::updateAll($arrDetalleForm, ["id" => $form_detalle_id]);
                        $calificacion = \app\models\Tmpejecucionbloquedetalles::findOne(["id" => $form_detalle_id]);
                        $calificacionDetalle = \app\models\Calificaciondetalles::findOne(['id' => $calificacion->calificaciondetalle_id]);
                        //Cuento las preguntas en las cuales esta seleccionado el NA
                        //lleno $arrayBloques para tener marcados en que bloques no se selecciono el check
                        if (!in_array($calificacion->bloque_id, $arrayBloques) && (strtoupper($calificacionDetalle->name) == 'NA')) {
                            $arrayBloques[] = $calificacion->bloque_id;
                            //inicio $arrayCountBloques
                            $arrayCountBloques[$count] = [($calificacion->bloque_id) => 1];
                            $count++;
                        } else {
                            if (count($arrayCountBloques) != 0) {
                                //actualizo $arrayCountBloques sumandole 1 cada q encuentra un NA de ese bloque
                                if ((array_key_exists($calificacion->bloque_id, $arrayCountBloques[count($arrayCountBloques) - 1])) && (strtoupper($calificacionDetalle->name) == 'NA')) {
                                    $arrayCountBloques[count($arrayCountBloques) - 1][$calificacion->bloque_id] = ($arrayCountBloques[count($arrayCountBloques) - 1][$calificacion->bloque_id] + 1);
                                }
                            }
                        }
                    }
                    //Actualizo los bloques en los cuales el total de sus preguntas esten seleccionadas en NA
                    foreach ($arrayCountBloques as $dato) {
                        $totalPreguntasBloque = \app\models\Tmpejecucionbloquedetalles::find()->select("COUNT(id) as preguntas")
                                        ->from("tbl_tmpejecucionbloquedetalles")
                                        ->where(['tmpejecucionformulario_id' => $tmp_id, 'bloque_id' => key($dato)])->asArray()->all();
                        if ($dato[key($dato)] == $totalPreguntasBloque["0"]["preguntas"]) {
                            \app\models\Tmpejecucionbloques::updateAll(['snna' => 1], ['tmpejecucionformulario_id' => $tmp_id, 'bloque_id' => key($dato)]);
                        }
                    }
                    //actualizo las secciones, la cuales tienen todos sus bloques con la opcion snna en 1
                    $secciones = \app\models\Tmpejecucionsecciones::findAll(['tmpejecucionformulario_id' => $tmp_id]);
                    foreach ($secciones as $seccion) {
                        $bloquessnna = \app\models\Tmpejecucionformularios::find()->select("s.seccion_id AS id,COUNT(b.id) AS conteo")
                                        ->from("tbl_tmpejecucionformularios f")->join("LEFT JOIN", "tbl_tmpejecucionsecciones s", "s.tmpejecucionformulario_id = f.id")
                                        ->join("LEFT JOIN", "tbl_tmpejecucionbloques b", "b.tmpejecucionseccion_id=s.id")
                                        ->where(['b.snna' => 1, 's.seccion_id' => ($seccion->seccion_id), 'f.id' => $tmp_id])
                                        ->groupBy("s.id")->asArray()->all();
                        $totalBloques = \app\models\Tmpejecucionformularios::find()->select("s.seccion_id AS id,COUNT(b.id) AS conteo")
                                        ->from("tbl_tmpejecucionformularios f")->join("LEFT JOIN", "tbl_tmpejecucionsecciones s", "s.tmpejecucionformulario_id = f.id")
                                        ->join("LEFT JOIN", "tbl_tmpejecucionbloques b", "b.tmpejecucionseccion_id=s.id")
                                        ->where(['s.seccion_id' => ($seccion->seccion_id), 'f.id' => $tmp_id])
                                        ->groupBy("s.id")->asArray()->all();
                        if (count($bloquessnna) > 0) {
                            if ($bloquessnna[0]['conteo'] == $totalBloques[0]['conteo']) {
                                \app\models\Tmpejecucionsecciones::updateAll(['snna' => 1], ['tmpejecucionformulario_id' => $tmp_id, 'seccion_id' => ($seccion->seccion_id)]);
                            }
                        }
                    }
                    /* GUARDO TIPIFICACIONES */
                    foreach ($arrTipificaciones as $form_detalle_id => $tipif_array) {
                        if (empty($tipif_array))
                            continue;

                        \app\models\TmpejecucionbloquedetallesTipificaciones::updateAll(["sncheck" => 0]
                                , ["tmpejecucionbloquedetalle_id" => $form_detalle_id]);

                        \app\models\TmpejecucionbloquedetallesTipificaciones::updateAll(["sncheck" => 1]
                                , "tmpejecucionbloquedetalle_id = '" . $form_detalle_id . "' "
                                . "AND tipificaciondetalle_id IN(" . implode(",", $tipif_array) . ")");
                    }

                    /* GUARDO SUBTIPIFICACIONES */
                    foreach ($arrSubtipificaciones as $form_detalle_id => $subtipif_array) {
                        $sql = "UPDATE `tbl_tmpejecucionbloquedetalles_subtipificaciones` a ";
                        $sql .= "INNER JOIN tbl_tmpejecucionbloquedetalles_tipificaciones b ";
                        $sql .= "ON a.tmpejecucionbloquedetalles_tipificacion_id = b.id ";
                        $sql .= "SET a.sncheck = 1 ";
                        $sql .= "WHERE b.tmpejecucionbloquedetalle_id = " . $form_detalle_id;
                        $sql .= " AND a.tipificaciondetalle_id IN (" . implode(",", $subtipif_array) . ")";
                        $command = \Yii::$app->db->createCommand($sql);
                        $command->execute();
                    }

                    foreach ($arrComentariosSecciones as $secc_id => $comentario) {

                        \app\models\Tmpejecucionsecciones::updateAll(["dscomentario" => $comentario]
                                , [
                            "seccion_id" => $secc_id
                            , "tmpejecucionformulario_id" => $tmp_id
                        ]);
                    }
                    //TODO: descomentar esta linea cuando se quiera usar las notificaciones a Amigo v1
                    /* GUARDAR EL TMP FOMULARIO A LAS EJECUCIONES */
                    \app\models\Tmpejecucionformularios::guardarFormulario($tmp_id);
                    
                    $modelBase->comentario = $arrFormulario["dscomentario"];
                    $modelBase->tipologia = $_POST['categoria'];
                    $modelBase->estado = "Cerrado";
                    $modelBase->usado = "NO";
                    $modelBase->responsabilidad = (isset($_POST['responsabilidad'])) ? $_POST['responsabilidad'] : "";
                    $modelBase->canal = (isset($_POST['canal'])) ? implode(", ", $_POST['canal']) : "";
                    $modelBase->marca = (isset($_POST['marca'])) ? implode(", ", $_POST['marca']) : "";
                    $modelBase->equivocacion = (isset($_POST['equivocacion'])) ? implode(", ", $_POST['equivocacion']) : "";
                    $modelBase->save();

                    Yii::$app->session->setFlash('success', Yii::t('app', 'Indices calculados'));
                    
                } catch (\Exception $exc) {
                    Yii::$app->session->setFlash('danger', Yii::t('app', 'error exception') . ": " . $exc->getMessage());
                }

                //REDIRECT CORRECTO
                return $this->redirect(['basechat/showformulariogestion',
                            'basesatisfaccion_id' => $modelBase->basesatisfaccion_id, 'preview' => 0, 'aleatorio' => 3, 'fill_values' => false, 'banderaescalado' => false]);
            }

            public function actionGuardaryenviarformulariogestion() {

                $arrCalificaciones = !$_POST['calificaciones'] ? array() : $_POST['calificaciones'];
                $arrTipificaciones = !isset($_POST['tipificaciones']) ? array() : $_POST['tipificaciones'];
                $arrSubtipificaciones = !isset($_POST['subtipificaciones']) ? array() : $_POST['subtipificaciones'];
                $arrComentariosSecciones = !$_POST['comentarioSeccion'] ? array() : $_POST['comentarioSeccion'];
                $arrCheckPits = !isset($_POST['checkPits']) ? array() : $_POST['checkPits'];
                $arrayForm = $_POST;
                $arrFormulario = [];
                /* Variables para conteo de bloques */
                $arrayCountBloques = [];
                $arrayBloques = [];
                $count = 0;
                /* fin de variables */
                $tmp_id = $_POST['tmp_formulario_id'];
                $basesatisfaccion_id = $_POST['basesatisfaccion_id'];
                $arrFormulario["dimension_id"] = $_POST['dimension'];
                $arrFormulario["dsruta_arbol"] = $_POST['ruta_arbol'];
                $arrFormulario["dscomentario"] = $_POST['comentarios_gral'];
                $arrFormulario["dsfuente_encuesta"] = $_POST['fuente'];
                $arrFormulario["transacion_id"] = $_POST['transacion_id'];
                $arrFormulario["sn_mostrarcalculo"] = 1;
                $view = (isset($_POST['view']))?$_POST['view']:null;
                $modelBase = BasechatTigo::find()->where(['basesatisfaccion_id' => $basesatisfaccion_id])->one();


                
                $arrFormulario["usua_id_lider"] = $_POST['form_lider_id'];
                $arrFormulario["equipo_id"] = $_POST['form_equipo_id'];
                
                //CONSULTA DEL FORMULARIO
                $data = \app\models\Tmpejecucionformularios::findOne($tmp_id);
                if ($_POST['subi_calculo'] != '') {
                    $data->subi_calculo .=',' . $_POST['subi_calculo'];
                    $data->save();
                }

                date_default_timezone_set('America/Bogota');
                if($data['hora_final'] != ""){
                        $inicial = new DateTime($_POST['hora_modificacion']);
                        $final = new DateTime(date("Y-m-d H:i:s"));

                        $dteDiff  = $inicial->diff($final);

                        $dteDiff->format("Y-m-d H:i:s");

                        $tiempo_modificacion_actual = $dteDiff->h . ":" . $dteDiff->i . ":" . $dteDiff->s;

                        $data->cant_modificaciones = $data->cant_modificaciones + 1;

                        $date = new DateTime($tiempo_modificacion_actual);
                        
                        $suma2 = $this->sumarhoras($data->tiempo_modificaciones, $date->format('H:i:s'));
                        
                        $data->tiempo_modificaciones = $suma2;

                        $data->save();
                }else{
                    $pruebafecha = date("Y-m-d H:i:s");
                    $data->hora_final = $pruebafecha;
                    $data->save();
                }
                
                //IF TODOS LOS BLOQUES ESTAN USADOS SETEO ARRAY VACIO
                if (!isset($arrayForm['bloque'])) {
                    $arrayForm['bloque'] = [];
                }
                /* INTENTO GUARDAR LOS FORMULARIOS */
                try {
                    /* EDITO EL TMP FORMULARIO */
                    $model = \app\models\Tmpejecucionformularios::find()->where(["id" => $tmp_id])->one();
                    $model->usua_id_actual = Yii::$app->user->identity->id;
                    $model->save();
                    
                    \app\models\Tmpejecucionformularios::updateAll($arrFormulario, ["id" => $tmp_id]);
                    \app\models\Tmpejecucionsecciones::updateAll(['snna' => 0], ['tmpejecucionformulario_id' => $tmp_id]);
                    \app\models\Tmpejecucionbloques::updateAll(['snna' => 0], ['tmpejecucionformulario_id' => $tmp_id]);

                    $bloquesFormtmp = \app\models\Tmpejecucionbloques::findAll(['tmpejecucionformulario_id' => $tmp_id]);
                    
                    foreach ($bloquesFormtmp as $bloquetmp) {
                        if (array_key_exists($bloquetmp->bloque_id, $arrayForm['bloque'])) {
                            $bloquetmp->snnousado = 1;
                            $bloquetmp->save();
                            $arrDetalleForm = [];
                            $arrDetalleForm["calificacion_id"] = -1;
                            $arrDetalleForm["calificaciondetalle_id"] = -1;
                            \app\models\Tmpejecucionbloquedetalles::updateAll($arrDetalleForm, ['tmpejecucionformulario_id' => $tmp_id,
                                'bloque_id' => $bloquetmp->bloque_id]);
                        } else {
                            $bloquetmp->snnousado = 0;
                            $bloquetmp->save();
                        }
                    }
                    /* GUARDO LAS CALIFICACIONES */
                    foreach ($arrCalificaciones as $form_detalle_id => $calif_detalle_id) {
                        $arrDetalleForm = [];
                        //se valida que existan check de pits seleccionaddos y se valida
                        //que exista el del bloquedetalle actual para actualizarlo
                        if (count($arrCheckPits) > 0) {
                            if (isset($arrCheckPits[$form_detalle_id])) {
                                $arrDetalleForm["c_pits"] = $arrCheckPits[$form_detalle_id];
                            }
                        }
                        if (empty($calif_detalle_id)) {
                            $arrDetalleForm["calificaciondetalle_id"] = -1;
                        } else {
                            $arrDetalleForm["calificaciondetalle_id"] = $calif_detalle_id;
                        }
                        \app\models\Tmpejecucionbloquedetalles::updateAll($arrDetalleForm, ["id" => $form_detalle_id]);
                        $calificacion = \app\models\Tmpejecucionbloquedetalles::findOne(["id" => $form_detalle_id]);
                        $calificacionDetalle = \app\models\Calificaciondetalles::findOne(['id' => $calificacion->calificaciondetalle_id]);
                        //Cuento las preguntas en las cuales esta seleccionado el NA
                        //lleno $arrayBloques para tener marcados en que bloques no se selecciono el check
                        if (!in_array($calificacion->bloque_id, $arrayBloques) && (strtoupper($calificacionDetalle->name) == 'NA')) {
                            $arrayBloques[] = $calificacion->bloque_id;
                            //inicio $arrayCountBloques
                            $arrayCountBloques[$count] = [($calificacion->bloque_id) => 1];
                            $count++;
                        } else {
                            if (count($arrayCountBloques) != 0) {
                                //actualizo $arrayCountBloques sumandole 1 cada q encuentra un NA de ese bloque
                                if ((array_key_exists($calificacion->bloque_id, $arrayCountBloques[count($arrayCountBloques) - 1])) && (strtoupper($calificacionDetalle->name) == 'NA')) {
                                    $arrayCountBloques[count($arrayCountBloques) - 1][$calificacion->bloque_id] = ($arrayCountBloques[count($arrayCountBloques) - 1][$calificacion->bloque_id] + 1);
                                }
                            }
                        }
                    }
                    //Actualizo los bloques en los cuales el total de sus preguntas esten seleccionadas en NA
                    foreach ($arrayCountBloques as $dato) {
                        $totalPreguntasBloque = \app\models\Tmpejecucionbloquedetalles::find()->select("COUNT(id) as preguntas")
                                        ->from("tbl_tmpejecucionbloquedetalles")
                                        ->where(['tmpejecucionformulario_id' => $tmp_id, 'bloque_id' => key($dato)])->asArray()->all();
                        if ($dato[key($dato)] == $totalPreguntasBloque["0"]["preguntas"]) {
                            \app\models\Tmpejecucionbloques::updateAll(['snna' => 1], ['tmpejecucionformulario_id' => $tmp_id, 'bloque_id' => key($dato)]);
                        }
                    }
                    //actualizo las secciones, la cuales tienen todos sus bloques con la opcion snna en 1
                    $secciones = \app\models\Tmpejecucionsecciones::findAll(['tmpejecucionformulario_id' => $tmp_id]);
                    foreach ($secciones as $seccion) {
                        $bloquessnna = \app\models\Tmpejecucionformularios::find()->select("s.seccion_id AS id,COUNT(b.id) AS conteo")
                                        ->from("tbl_tmpejecucionformularios f")->join("LEFT JOIN", "tbl_tmpejecucionsecciones s", "s.tmpejecucionformulario_id = f.id")
                                        ->join("LEFT JOIN", "tbl_tmpejecucionbloques b", "b.tmpejecucionseccion_id=s.id")
                                        ->where(['b.snna' => 1, 's.seccion_id' => ($seccion->seccion_id), 'f.id' => $tmp_id])
                                        ->groupBy("s.id")->asArray()->all();
                        $totalBloques = \app\models\Tmpejecucionformularios::find()->select("s.seccion_id AS id,COUNT(b.id) AS conteo")
                                        ->from("tbl_tmpejecucionformularios f")->join("LEFT JOIN", "tbl_tmpejecucionsecciones s", "s.tmpejecucionformulario_id = f.id")
                                        ->join("LEFT JOIN", "tbl_tmpejecucionbloques b", "b.tmpejecucionseccion_id=s.id")
                                        ->where(['s.seccion_id' => ($seccion->seccion_id), 'f.id' => $tmp_id])
                                        ->groupBy("s.id")->asArray()->all();
                        if (count($bloquessnna) > 0) {
                            if ($bloquessnna[0]['conteo'] == $totalBloques[0]['conteo']) {
                                \app\models\Tmpejecucionsecciones::updateAll(['snna' => 1], ['tmpejecucionformulario_id' => $tmp_id, 'seccion_id' => ($seccion->seccion_id)]);
                            }
                        }
                    }
                    /* GUARDO TIPIFICACIONES */
                    foreach ($arrTipificaciones as $form_detalle_id => $tipif_array) {
                        if (empty($tipif_array))
                            continue;

                        \app\models\TmpejecucionbloquedetallesTipificaciones::updateAll(["sncheck" => 0]
                                , ["tmpejecucionbloquedetalle_id" => $form_detalle_id]);

                        \app\models\TmpejecucionbloquedetallesTipificaciones::updateAll(["sncheck" => 1]
                                , "tmpejecucionbloquedetalle_id = '" . $form_detalle_id . "' "
                                . "AND tipificaciondetalle_id IN(" . implode(",", $tipif_array) . ")");
                    }

                    /* GUARDO SUBTIPIFICACIONES */
                    foreach ($arrSubtipificaciones as $form_detalle_id => $subtipif_array) {
                        $sql = "UPDATE `tbl_tmpejecucionbloquedetalles_subtipificaciones` a ";
                        $sql .= "INNER JOIN tbl_tmpejecucionbloquedetalles_tipificaciones b ";
                        $sql .= "ON a.tmpejecucionbloquedetalles_tipificacion_id = b.id ";
                        $sql .= "SET a.sncheck = 1 ";
                        $sql .= "WHERE b.tmpejecucionbloquedetalle_id = " . $form_detalle_id;
                        $sql .= " AND a.tipificaciondetalle_id IN (" . implode(",", $subtipif_array) . ")";
                        $command = \Yii::$app->db->createCommand($sql);
                        $command->execute();
                    }

                    foreach ($arrComentariosSecciones as $secc_id => $comentario) {

                        \app\models\Tmpejecucionsecciones::updateAll(["dscomentario" => $comentario]
                                , [
                            "seccion_id" => $secc_id
                            , "tmpejecucionformulario_id" => $tmp_id
                        ]);
                    }
                    //TODO: descomentar esta linea cuando se quiera usar las notificaciones a Amigo v1
                    $tmp_ejecucion = \app\models\Tmpejecucionformularios::findOne(['id' => $tmp_id]);
                    /* GUARDAR EL TMP FOMULARIO A LAS EJECUCIONES */
                    /* GUARDAR en una variable el retorno de la funcion */

                    $validarPasoejecucionform = \app\models\Tmpejecucionformularios::guardarFormulario($tmp_id);
                    /* validacion de guardado exitoso del tmp y paso a las tablas de ejecucion
                      en caso de no cumplirla, se redirige nuevamente al formulario */

                    if (!$validarPasoejecucionform) {
                        Yii::$app->session->setFlash('danger', Yii::t('app', 'error exception tmpejecucion to ejecucion'));
                        return $this->redirect(['basechat/showformulariogestion',
                            'basesatisfaccion_id' => $modelBase->idencuesta, 'preview' => 0, 'fill_values' => false, 'aleatorio' => 3, 'banderaescalado' => false]);
                    }
                    $modelBase->comentario = $arrFormulario["dscomentario"];
                    $modelBase->tipologia = $_POST['categoria'];
                    $modelBase->usado = "NO";
                    $modelBase->responsabilidad = (isset($_POST['responsabilidad'])) ? $_POST['responsabilidad'] : "";
                    $modelBase->canal = (isset($_POST['canal'])) ? implode(", ", $_POST['canal']) : "";
                    $modelBase->marca = (isset($_POST['marca'])) ? implode(", ", $_POST['marca']) : "";
                    $modelBase->equivocacion = (isset($_POST['equivocacion'])) ? implode(", ", $_POST['equivocacion']) : "";
                    $modelBase->save();


                    Yii::$app->session->setFlash('success', Yii::t('app', 'Formulario guardado'));

                    /* TODO: descomentar esta linea cuando se quiera usar las notificaciones a Amigo v1
                     
                     * */
                      $modelEvaluado = \app\models\Evaluados::findOne(["id" => $tmp_ejecucion->evaluado_id]);
                      $ejecucion = \app\models\Ejecucionformularios::find()->where(['evaluado_id' => $tmp_ejecucion->evaluado_id, 'usua_id' => $tmp_ejecucion->usua_id])->orderBy('id DESC')->all();
                      $params = [];
                      $params['titulo'] = 'Te han realizado una valoración';
                      $params['pcrc'] = '';
                      $params['descripcion'] = '';
                      $params['notificacion'] = 'SI';
                      $params['muro'] = 'NO';
                      $params['usuariored'] = $modelEvaluado->dsusuario_red;
                      $params['cedula'] = '';
                      $params['plataforma'] = 'QA';
                      $params['url'] = '' . Url::to(['formularios/showformulariodiligenciadoamigo']) . '?form_id=' . base64_encode($ejecucion[0]->id);
                      //Se comenta webservicesresponse  para QA por caida de Amigo - 13-02-2019 -
                      $webservicesresponse = null;
                      $tmp_ejecucion = \app\models\Tmpejecucionformularios::findOne(['id' => $tmp_id]);
                      if (!$webservicesresponse && $tmp_ejecucion == '') {
                      
                      
                      Yii::$app->session->setFlash('danger', Yii::t('app', 'No se pudo realizar conexión con la plataforma Amigo'));                  
                      }

            
                } catch (\Exception $exc) {
                    Yii::$app->session->setFlash('danger', Yii::t('app', 'error exception') . ": " . $exc->getMessage());
                }
        Yii::$app->db->createCommand("update tbl_basechat_tigo set estado = 'Cerrado' where anulado = 0 and basesatisfaccion_id = $basesatisfaccion_id")->execute(); 

        //REDIRECT CORRECTO
                if ($view == "index") {
                    $redct =  'index';

                    return $this->redirect(['index']);
                } else {
                    $redct = $view;
                    return $this->redirect([$redct]);
                }
            }

        public function actionShowbasechat($basechatid){
            $model = new BasechatFormulario();
            $varticket = null;
            $varencuesta = null;
            $varcliente = null;
            $varagente = null;
            $vartipologia = null;
            $varSentir = null;
            $varPregunta1 = null;
            $varPregunta2 = null;
            $varPregunta3 = null;
            $varPregunta4 = null;
            $varPregunta5 = null;
            $vartipo_producto = null;
            $varvasrchatid = $basechatid;
            $varcreacion = null;
            $varrta = null;

            $varlistencuestas = Yii::$app->db->createCommand("select * from tbl_basechat_tigob where anulado = 0 and idbasechat_tigob = $basechatid")->queryAll();
            foreach ($varlistencuestas as $key => $value) {
                $varticket = $value['ticked_id'];
                $varencuesta = $value['fecha_transaccion'];
                $varcreacion = $value['fecha_creacion'];
                $varrta = $value['fecha_respuesta'];
                $varcliente = $value['nombre_cliente'];
                $varagente = $value['evaluadoid'];
                $vartipologia = $value['tipologia'];
                $varSentir = $value['comentario_adicional'];
                $varPregunta1 = $value['nps'];
                $varPregunta2 = $value['csat'];
                $varPregunta3 = $value['ces'];
                $varPregunta4 = $value['fcr'];
                $varPregunta5 = $value['conocimiento'];
                $vartipo_producto = $value['tipo_producto'];
            }


            return $this->render('showbasechat',[
                'model' => $model,
                'varticket' => $varticket,
                'varencuesta' => $varencuesta,
                'varcreacion' => $varcreacion,
                'varrta' => $varrta,
                'varcliente' => $varcliente,
                'varagente' => $varagente,
                'vartipologia' => $vartipologia,
                'varSentir' => $varSentir,
                'varPregunta1' => $varPregunta1,
                'varPregunta2' => $varPregunta2,
                'varPregunta3' => $varPregunta3,
                'varPregunta4' => $varPregunta4,
                'varPregunta5' => $varPregunta5,
                'vartipo_producto' => $vartipo_producto,
                'varvasrchatid' => $varvasrchatid,
                ]);
        }

        public function actionRegistrocategorias(){
            $model = new BasechatCategorias();

            $form = Yii::$app->request->post();
            if ($model->load($form)) {
                $varnamec = $model->nombrecategoria;
                $varpcrc = $model->pcrc;

                Yii::$app->db->createCommand()->insert('tbl_basechat_categorias',[
                                           'nombrecategoria' => $varnamec,
                                           'fechacreacion' => date("Y-m-d"),
                                           'anulado' => 0,
                                           'usua_id' => Yii::$app->user->identity->id,
                                           'pcrc' => $varpcrc,
                                       ])->execute();

                return $this->redirect(['index']);
            }


            return $this->renderAjax('registrocategorias',[
                'model' => $model,
                ]);
        }

        public function actionRegistromotivos(){
            $model = new BasechatMotivos();

            $form = Yii::$app->request->post();
            if ($model->load($form)) {
                $varnamec = $model->nombrelista;
                $varidlist = $model->idlista;

                Yii::$app->db->createCommand()->insert('tbl_basechat_motivos',[
                                           'nombrelista' => $varnamec,
                                           'idlista' => $varidlist,
                                           'fechacreacion' => date("Y-m-d"),
                                           'anulado' => 0,
                                           'usua_id' => Yii::$app->user->identity->id,
                                       ])->execute();

                return $this->redirect(['index']);
            }

            return $this->renderAjax('registromotivos',[
                'model' => $model,
                ]);
        }

        public function actionCreateshowbasepart1(){
            $txtvarbloque = Yii::$app->request->get("txtvarbloque");
            $txtvaridbloque = Yii::$app->request->get("txtvaridbloque");
            $txtvartxtticket = Yii::$app->request->get("txtvartxtticket");
            $txtvaridtxtFechaHoraclasifi = Yii::$app->request->get("txtvaridtxtFechaHoraclasifi");
            $txtvaridtxtFechaHorasendesk = Yii::$app->request->get("txtvaridtxtFechaHorasendesk");

            $varbasesatisfaccion_id = Yii::$app->db->createCommand("select basesatisfaccion_id from tbl_basechat_tigob where anulado = 0 and ticked_id = $txtvartxtticket")->queryScalar();

            $varcomprobacion = Yii::$app->db->createCommand("select count(1) from tbl_basechat_formulario where anulado = 0 and ticked_id = $txtvartxtticket and basesatisfaccion_id = $varbasesatisfaccion_id and idlista = $txtvarbloque and idbaselista = $txtvaridbloque")->queryScalar();
            if ($varcomprobacion == 0) {
                Yii::$app->db->createCommand()->insert('tbl_basechat_formulario',[
                    'ticked_id' => $txtvartxtticket,
                    'basesatisfaccion_id' => $varbasesatisfaccion_id,
                    'fechacalificacion' => $txtvaridtxtFechaHoraclasifi,
                    'fechazendeks' => $txtvaridtxtFechaHorasendesk,
                    'idlista' => $txtvarbloque,
                    'idbaselista' => $txtvaridbloque,
                    'fsolicitud' => null,
                    'fsolucion' => null,
                    'fobservacion' => null,
                    'fprocedimiento' => null,
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
                    'fechacreacion' => date("Y-m-d"),
                ])->execute(); 

                \Yii::$app->db->createCommand()->insert('tbl_logs', [
                    'usua_id' => Yii::$app->user->identity->id,
                    'usuario' => Yii::$app->user->identity->username,
                    'fechahora' => date('Y-m-d h:i:s'),
                    'ip' => Yii::$app->getRequest()->getUserIP(),
                    'accion' => 'Create',
                    'tabla' => 'tbl_basechat_formulario'
                ])->execute();
            }
            

            $txtrta = 1;
            die(json_encode($txtrta));  

        }

        public function actionCreateshowbasepart2(){
            $txtvartxtticket = Yii::$app->request->get("txtvartxtticket");
            $txtvaridtxtFechaHoraclasifi = Yii::$app->request->get("txtvaridtxtFechaHoraclasifi");
            $txtvaridtxtFechaHorasendesk = Yii::$app->request->get("txtvaridtxtFechaHorasendesk");
            $txtvaridfsolicitud = Yii::$app->request->get("txtvaridfsolicitud");
            $txtvaridfsolucion = Yii::$app->request->get("txtvaridfsolucion");
            $txtvaridfobservacion = Yii::$app->request->get("txtvaridfobservacion");
            $varidfprocedimiento = Yii::$app->request->get("varidfprocedimiento");
	    $txtvaridencuesta = Yii::$app->request->get("txtvaridencuesta");

            $varbasesatisfaccion_id = Yii::$app->db->createCommand("select basesatisfaccion_id from tbl_basechat_tigob where anulado = 0 and ticked_id = $txtvartxtticket")->queryScalar();

            $varcomprobacion = Yii::$app->db->createCommand("select count(1) from tbl_basechat_formulario where anulado = 0 and ticked_id = $txtvartxtticket and basesatisfaccion_id = $varbasesatisfaccion_id and fsolicitud = '$txtvaridfsolicitud' and fsolucion = '$txtvaridfsolucion' and fobservacion = '$txtvaridfobservacion' and fprocedimiento = '$varidfprocedimiento'")->queryScalar();
	
	    $varestado = 'Cerrado';
            if ($varcomprobacion == 0) {
                Yii::$app->db->createCommand()->insert('tbl_basechat_formulario',[
                    'ticked_id' => $txtvartxtticket,
                    'basesatisfaccion_id' => $varbasesatisfaccion_id,
                    'fechacalificacion' => $txtvaridtxtFechaHoraclasifi,
                    'fechazendeks' => $txtvaridtxtFechaHorasendesk,
                    'idlista' => null,
                    'idbaselista' => null,
                    'fsolicitud' => $txtvaridfsolicitud,
                    'fsolucion' => $txtvaridfsolucion,
                    'fobservacion' => $txtvaridfobservacion,
                    'fprocedimiento' => $varidfprocedimiento,
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
                    'fechacreacion' => date("Y-m-d"),
                ])->execute();

                \Yii::$app->db->createCommand()->insert('tbl_logs', [
                    'usua_id' => Yii::$app->user->identity->id,
                    'usuario' => Yii::$app->user->identity->username,
                    'fechahora' => date('Y-m-d h:i:s'),
                    'ip' => Yii::$app->getRequest()->getUserIP(),
                    'accion' => 'Create',
                    'tabla' => 'tbl_basechat_formulario'
                ])->execute();

	            Yii::$app->db->createCommand()->update('tbl_basechat_tigob',[                    
                    'estado' => $varestado,
                ],'idencuesta ='.$txtvaridencuesta.' and ticked_id ='.$txtvartxtticket.'')->execute(); 

                \Yii::$app->db->createCommand()->insert('tbl_logs', [
                    'usua_id' => Yii::$app->user->identity->id,
                    'usuario' => Yii::$app->user->identity->username,
                    'fechahora' => date('Y-m-d h:i:s'),
                    'ip' => Yii::$app->getRequest()->getUserIP(),
                    'accion' => 'Update',
                    'tabla' => 'tbl_basechat_tigob'
                ])->execute();
            }            
            

            $txtrta = 1;
            die(json_encode($txtrta));  

        }

        public function actionShowbasechatview($basechatid){
            $varticket = null;
            $varencuesta = null;
            $varcliente = null;
            $varagente = null;
            $vartipologia = null;
            $varSentir = null;
            $varPregunta1 = null;
            $varPregunta2 = null;
            $varPregunta3 = null;
            $varPregunta4 = null;
            $varPregunta5 = null;
            $vartipo_producto = null;
            $varvasrchatid = null;
            $varzendesk = null;
            $varcreacion = null;
            $varrta = null;

            $varlistencuestas = Yii::$app->db->createCommand("select * from tbl_basechat_tigob where anulado = 0 and idbasechat_tigob = $basechatid")->queryAll();
            foreach ($varlistencuestas as $key => $value) {
                $varticket = $value['ticked_id'];
                $varencuesta = $value['fecha_transaccion'];
                $varcreacion = $value['fecha_creacion'];
                $varrta = $value['fecha_respuesta'];
                $varcliente = $value['nombre_cliente'];
                $varagente = $value['evaluadoid'];
                $vartipologia = $value['tipologia'];
                $varSentir = $value['comentario_adicional'];
                $varPregunta1 = $value['nps'];
                $varPregunta2 = $value['csat'];
                $varPregunta3 = $value['ces'];
                $varPregunta4 = $value['fcr'];
                $varPregunta5 = $value['conocimiento'];
                $vartipo_producto = $value['tipo_producto'];
                $varvasrchatid = $value['basesatisfaccion_id'];
            }

            $varlistshowbase = Yii::$app->db->createCommand("select * from tbl_basechat_formulario where anulado = 0 and ticked_id = $varticket and basesatisfaccion_id = $varvasrchatid")->queryAll();

            $varzendesk = Yii::$app->db->createCommand("select distinct fechazendeks from tbl_basechat_formulario where anulado = 0 and ticked_id = $varticket and basesatisfaccion_id = $varvasrchatid")->queryScalar();

            return $this->render('showbasechatview',[                
                'varticket' => $varticket,
                'varencuesta' => $varencuesta,
                'varcreacion' => $varcreacion,
                'varrta' => $varrta,
                'varcliente' => $varcliente,
                'varagente' => $varagente,
                'vartipologia' => $vartipologia,
                'varSentir' => $varSentir,
                'varPregunta1' => $varPregunta1,
                'varPregunta2' => $varPregunta2,
                'varPregunta3' => $varPregunta3,
                'varPregunta4' => $varPregunta4,
                'varPregunta5' => $varPregunta5,
                'vartipo_producto' => $vartipo_producto,
                'varlistshowbase' => $varlistshowbase,
                'varzendesk'  => $varzendesk,
                ]);
        }

        public function actionDescargarshow(){
            $valist = Yii::$app->db->createCommand("select bt.ticked_id 'NumeroTicket', bt.fecha_creacion 'FechaCreacion', bt.fecha_respuesta 'FechaRespuesta', bt.fecha_transaccion 'FechaTransaccion', bt.nombre_cliente 'Cliente', bt.id_agente 'Agente', bt.tipologia 'Tipologia', bt.tipo_producto 'TipoProducto', bt.comentario_adicional 'SentirCliente', bt.nps 'Pregunta1', bt.csat 'Pregunta2', bt.ces 'Pregunta3', bt.fcr 'Pregunta4', bt.conocimiento 'Pregunta5', bf.fechacalificacion 'FechaCalificacion', bf.fechazendeks 'FechaZendesk',  bt.basesatisfaccion_id  'Basesatisfaccion' from tbl_basechat_categorias bc inner join tbl_basechat_motivos bm on  bc.idlista = bm.idlista   inner join tbl_basechat_formulario bf on bm.idbaselista = bf.idbaselista inner join tbl_basechat_tigob bt on bf.ticked_id = bt.ticked_id and bf.basesatisfaccion_id = bt.basesatisfaccion_id where bf.anulado = 0 and bt.anulado = 0 group by NumeroTicket, Basesatisfaccion")->queryAll();
            return $this->renderAjax('descargarshow',[
                'valist' => $valist,
                ]);
        }

        public function actionUpdateusuarios(){

            $varlistjarvis = Yii::$app->get('dbjarvis2')->createCommand("select ua.usuario, ur.usuario_red from dp_usuarios_red ur inner join dp_usuarios_actualizacion ua on ur.documento = ua.documento  where         ua.usuario like '%@tigo.net.bo%' group by ua.usuario")->queryAll();

            foreach ($varlistjarvis as $key => $value) {
                $varusuatigo = $value['usuario'];
                $varusuak = $value['usuario_red'];

                $varvalida = Yii::$app->db->createCommand("select count(1) from tbl_evaluados_tigo et where et.dusuario_red = '$varusuak' and anulado = 0")->queryScalar();

                if ($varvalida == 0) {
                    Yii::$app->db->createCommand()->insert('tbl_evaluados_tigo',[
                        'usua_tigo' => $varusuatigo,
                        'dusuario_red' => $varusuak,
                        'anulado' => 0,
                        'usua_id' => Yii::$app->user->identity->id,
                        'fechacreacion' => date("Y-m-d"),
                    ])->execute(); 

                    \Yii::$app->db->createCommand()->insert('tbl_logs', [
                        'usua_id' => Yii::$app->user->identity->id,
                        'usuario' => Yii::$app->user->identity->username,
                        'fechahora' => date('Y-m-d h:i:s'),
                        'ip' => Yii::$app->getRequest()->getUserIP(),
                        'accion' => 'Create',
                        'tabla' => 'tbl_evaluados_tigo'
                    ])->execute();
                }

            }

            return $this->redirect(['index']);
        }

public function actionElegirimportar(){

            return $this->renderAjax('elegirimportar');
        }

        public function actionImportarexceltwo(){
            $model = new FormUploadtigo();

                if ($model->load(Yii::$app->request->post()))
                {
                    $model->file = UploadedFile::getInstances($model, 'file');

                    if ($model->file && $model->validate()) {
                        foreach ($model->file as $file) {
                            $fecha = date('Y-m-d-h-i-s');
                            $user = Yii::$app->user->identity->username;
                            $name = $fecha . '-' . $user;
                            $file->saveAs('categorias/' . $name . '.' . $file->extension);
                            $this->Importexceltwo($name);

                            return $this->redirect('index');
                        }
                    }
               }
           

            return $this->render('importarexcel',[
                'model' => $model,
                ]);            
        }

        public function Importexceltwo($name){
            $inputFile = 'categorias/' . $name . '.xlsx';
            try{
                    $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
                    $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($inputFile);

            }catch(Exception $e)
            {
                    die('Error');
            }

                $sheet = $objPHPExcel->getSheet(0);
                $highestRow = $sheet->getHighestRow();
                $highestcolumn = $sheet->getHighestColumn();

            for( $row = 4; $row <= $highestRow; $row++) {

                $varbaseencuesta = $sheet->getCell("B".$row)->getValue();
                $varTickets = $sheet->getCell("JZ".$row)->getValue();

                $varMoticoContacto = $sheet->getCell("QJ".$row)->getValue();
                $varCategoria = $sheet->getCell("QK".$row)->getValue();
                $varMotivos = $sheet->getCell("QL".$row)->getValue();
                $varObservacion = $sheet->getCell("QM".$row)->getValue();
                $varImputable = $sheet->getCell("QN".$row)->getValue();
                $varAnalista = $sheet->getCell("QO".$row)->getValue();

                Yii::$app->db->createCommand()->update('tbl_basechat_tigob',[
                                            'motivo_contacto' => $varMoticoContacto,
                                            'categoria' => $varCategoria,
                                            'motivo' => $varMotivos,
                                            'observacion' => $varObservacion,
                                            'imputable' => $varImputable,
                                            'analista' => $varAnalista,
                                        ],'idencuesta ='.$varbaseencuesta.' and ticked_id ='.$varTickets.'')->execute();               
            
            }

        }

// Diego para imputabilidad de tigo Colombia

    public function actionImportarexcelcol(){
        $model = new FormUploadtigo();

            if ($model->load(Yii::$app->request->post()))
            {
                $model->file = UploadedFile::getInstances($model, 'file');

                if ($model->file && $model->validate()) {
                    foreach ($model->file as $file) {
                        $fecha = date('Y-m-d-h-i-s');
                        $user = Yii::$app->user->identity->username;
                        $name = $fecha . '-' . $user;
                        $file->saveAs('categorias/' . $name . '.' . $file->extension);
                        $this->Importexcelcol($name);

                        return $this->redirect('index');
                    }
                }
           }
       

        return $this->render('importarexcelcol',[
            'model' => $model,
            ]);
    }

    public function Importexcelcol($name){
        $inputFile = 'categorias/' . $name . '.xlsx';
            try{
                $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
                $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFile);

            }catch(Exception $e)
            {
                die('Error');
            }

            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestcolumn = $sheet->getHighestColumn();

            for( $row = 4; $row <= $highestRow; $row++)
            {
    if ($sheet->getCell("A".$row)->getValue() != null) {

     


                $varfechartaChange = $sheet->getCell("AI".$row)->getValue();
                $varfecharta = PHPExcel_Shared_Date::ExcelToPHPObject($varfechartaChange)->format('Y-m-d H:i:s');
                $aniorta = date("Y", strtotime($varfecharta));
                $mesrta = date("m", strtotime($varfecharta));
                $diarta = date("d", strtotime($varfecharta));                    
                $horarta = date("H", strtotime($varfecharta));
                $minutosrta = date("i", strtotime($varfecharta));
                $segundosrta = date("s", strtotime($varfecharta));
	
		
		$varrtafecha = $aniorta.'-'.$mesrta.'-'.$diarta.' '.$horarta.':'.$minutosrta.':'.$segundosrta;
                    
                $varrtafecha = strtotime('2 hours', strtotime($varrtafecha));
                $varrtafecha = date ( 'Y-m-d H:i:s' , $varrtafecha );

                $vartipo = $sheet->getCell("IN".$row)->getValue();
                if ($vartipo == "Mensajer? :: WhatsApp") {                        
                    $varpcrc = 3513;
                    $varcliente = 2919;
                    $varrn = 'BW';
                    $varinstitucion = '27';
                    $varindustria = '27';
                }else{
                     $varpcrc = 3513;
                     $varcliente = 2919;                        
                     $varrn = 'BW';
                     $varinstitucion = '27';
                     $varindustria = '27';
                }
       
                $varnaps = $sheet->getCell("BO".$row)->getValue();
                if ($varnaps >= 9) {
                    $txtnps = "FELICITACION";
                }else{
                    if ($varnaps <= 6) {
                        $txtnps = "CRITICA";
                    }else{
                        $txtnps = "NEUTRO";
                    }
                }

                $varcompleteasesor = $sheet->getCell("BI".$row)->getValue();
                
                $rest = Yii::$app->db->createCommand("select distinct usuario_red from tbl_tmpvaloradosdistribucion where anulado = 0 and correo = '$varcompleteasesor'")->queryScalar();
                $varbaseencuesta = $sheet->getCell("A".$row)->getValue();
                $varfechab = date("Y-m-d H:i:s");

                Yii::$app->db->createCommand()->insert('tbl_base_satisfaccion',[
                                    'chat_transfer' => $varbaseencuesta,
                                    'estado' => 'Cerrado',
                                    'usado' => 'SI',
                                    'tipo_inbox' => 'NINGUNO',
                                    'created' => $varfechab,
                                    ])->execute();  

                $varbasesatisfaccionid = Yii::$app->db->createCommand("select id from tbl_base_satisfaccion where chat_transfer = $varbaseencuesta and estado = 'Cerrado' and usado = 'SI' and tipo_inbox = 'NINGUNO' AND created = '$varfechab'")->queryScalar();
                

                Yii::$app->db->createCommand()->insert('tbl_basechat_tigob',[
                                    'idencuesta' => $varbaseencuesta,
                                    'id_cliente'=> $sheet->getCell("B".$row)->getValue(),
                                    'nombre_cliente'=> $sheet->getCell("C".$row)->getValue(),
                                    'fecha_respuesta'=> $varrtafecha,
                                    'email_cliente'=> $sheet->getCell("D".$row)->getValue(),
                                    'telefono_cliente'=> $sheet->getCell("E".$row)->getValue(),
                                    'mercado'=> $sheet->getCell("F".$row)->getValue(),
                                    'unidad_identificador'=> $sheet->getCell("G".$row)->getValue(),
                                    'unidad_nombre'=> $sheet->getCell("H".$row)->getValue(),
                                    'dispositivo'=> $sheet->getCell("L".$row)->getValue(),
                                    'region'=> $sheet->getCell("AA".$row)->getValue(),
                                    'pais'=> $sheet->getCell("AC".$row)->getValue(),
                                    'ciudad'=> $sheet->getCell("AD".$row)->getValue(),
                                    'email_asesor'=> $sheet->getCell("BI".$row)->getValue(),
				    'id_agente'=> $sheet->getCell("BI".$row)->getValue(),
                                    'form_id'=> $sheet->getCell("AM".$row)->getValue(),
                                    'digital_canal'=> $sheet->getCell("AW".$row)->getValue(),
                                    'account'=> $sheet->getCell("AX".$row)->getValue(),
                                    'tipo_canal_digital'=> $sheet->getCell("BC".$row)->getValue(),
                                    'tipo_producto_digital'=> $sheet->getCell("BE".$row)->getValue(),
                                    'ticked_id'=> $sheet->getCell("BL".$row)->getValue(),
                                    'bot_id'=> $sheet->getCell("BM".$row)->getValue(),
                                    'nps'=> $sheet->getCell("BO".$row)->getValue(),
                                    'comentario_adicional'=> $sheet->getCell("BP".$row)->getValue(),
                                    'csat'=> $sheet->getCell("BQ".$row)->getValue(),
                                    'ces'=> $sheet->getCell("BR".$row)->getValue(),
                                    'fcr'=> $sheet->getCell("BS".$row)->getValue(),
                                    'comentario_fcr'=> $sheet->getCell("BT".$row)->getValue(),
                                    'conocimiento'=> $sheet->getCell("BU".$row)->getValue(),
                                    'claridad'=> $sheet->getCell("BY".$row)->getValue(),
                                    'numero_identidad'=> $sheet->getCell("CL".$row)->getValue(),
                                    'digital_subcanal'=> $sheet->getCell("CQ".$row)->getValue(),
                                    'journey'=> $sheet->getCell("CT".$row)->getValue(),
                                    'website_url'=> $sheet->getCell("CU".$row)->getValue(),

                                    'evaluadoid' => $rest,
                                    'rn' => $varrn,
                                    'institucion' => $varinstitucion,
                                    'industria' => $varindustria,
                                    'pcrc' => $varpcrc,
                                    'cliente' => $varcliente,
                                    'tipologia' => $txtnps,
                                    'estado' => 'Abierto',
                                    'basesatisfaccion_id' => $varbasesatisfaccionid,
                                    'fechacreacion' => date("Y-m-d"),
                                    'anulado' => 0,
                                    'usua_id' => Yii::$app->user->identity->id,
                                    ])->execute();                
        }
            }
    }
  
    public function actionShowbasechatcol($basechatid, $pcrc){
        $model = new BasechatFormulario();
        $varticket = null;
        $varcliente = null;
        $varagente = null;
        $vartipologia = null;
        $varSentir = null;
        $varPregunta1 = null;
        $varPregunta2 = null;
        $varPregunta3 = null;
        $varPregunta4 = null;
        $varPregunta5 = null;
        $vartipo_producto = null;
        $varvasrchatid = $basechatid;
        $varrta = null;
        $varpcrc = $pcrc;
        $varcomentario_fcr = null;

        $varlistencuestas = Yii::$app->db->createCommand("select * from tbl_basechat_tigob where anulado = 0 and idbasechat_tigob = $basechatid")->queryAll();
        foreach ($varlistencuestas as $key => $value) {
            $varticket = $value['ticked_id'];
            $varrta = $value['fecha_respuesta'];
            $varcliente = $value['nombre_cliente'];
            $varagente = $value['evaluadoid'];
            $vartipologia = $value['tipologia'];
            $varSentir = $value['comentario_adicional'];
            $varPregunta1 = $value['nps'];
            $varPregunta2 = $value['csat'];
            $varPregunta3 = $value['ces'];
            $varPregunta4 = $value['fcr'];
            $varPregunta5 = $value['conocimiento'];
            $vartipo_producto = $value['tipo_producto'];
            $varcomentario_fcr = $value['comentario_fcr'];
        }


        return $this->render('showbasechatcol',[
            'model' => $model,
            'varticket' => $varticket,
            'varrta' => $varrta,
            'varcliente' => $varcliente,
            'varagente' => $varagente,
            'vartipologia' => $vartipologia,
            'varSentir' => $varSentir,
            'varPregunta1' => $varPregunta1,
            'varPregunta2' => $varPregunta2,
            'varPregunta3' => $varPregunta3,
            'varPregunta4' => $varPregunta4,
            'varPregunta5' => $varPregunta5,
            'vartipo_producto' => $vartipo_producto,
            'varvasrchatid' => $varvasrchatid,
            'varcomentario_fcr' => $varcomentario_fcr,                
            ]);
    }

    public function actionDescargargestion(){
        $valist = Yii::$app->db->createCommand("select bt.ticked_id 'NumeroTicket', bt.fecha_creacion 'FechaCreacion', bt.fecha_respuesta 'FechaRespuesta', bt.fecha_transaccion 'FechaTransaccion', bt.nombre_cliente 'Cliente', bt.id_agente 'Agente', bt.tipologia 'Tipologia', bt.tipo_producto 'TipoProducto', bt.comentario_adicional 'SentirCliente', bt.nps 'Pregunta1', bt.csat 'Pregunta2', bt.ces 'Pregunta3', bt.fcr 'Pregunta4', bt.conocimiento 'Pregunta5', bf.fechacalificacion 'FechaCalificacion', bf.fechazendeks 'FechaZendesk',  bt.basesatisfaccion_id  'Basesatisfaccion' from tbl_basechat_categorias bc inner join tbl_basechat_motivos bm on  bc.idlista = bm.idlista   inner join tbl_basechat_formulario bf on bm.idbaselista = bf.idbaselista inner join tbl_basechat_tigob bt on bf.ticked_id = bt.ticked_id and bf.basesatisfaccion_id = bt.basesatisfaccion_id where bf.anulado = 0 and bt.anulado = 0 group by NumeroTicket, Basesatisfaccion")->queryAll();
        return $this->renderAjax('descargargestion',[
            'valist' => $valist,
            ]);
    }

     public function actionExportbol(){
        $varCorreo = Yii::$app->request->get("var_Destino");
        $sessiones = Yii::$app->user->identity->id;
	
        $valist = Yii::$app->db->createCommand("select bt.ticked_id 'NumeroTicket', bt.fecha_creacion 'FechaCreacion', bt.fecha_respuesta 'FechaRespuesta', bt.fecha_transaccion 'FechaTransaccion', bt.nombre_cliente 'Cliente', bt.id_agente 'Agente', bt.tipologia 'Tipologia', bt.tipo_producto 'TipoProducto', bt.comentario_adicional 'SentirCliente', bt.nps 'Pregunta1', bt.csat 'Pregunta2', bt.ces 'Pregunta3', bt.fcr 'Pregunta4', bt.conocimiento 'Pregunta5', bf.fechacalificacion 'FechaCalificacion', bf.fechazendeks 'FechaZendesk',  bt.basesatisfaccion_id  'Basesatisfaccion' from tbl_basechat_categorias bc inner join tbl_basechat_motivos bm on  bc.idlista = bm.idlista   inner join tbl_basechat_formulario bf on bm.idbaselista = bf.idbaselista inner join tbl_basechat_tigob bt on bf.ticked_id = bt.ticked_id and bf.basesatisfaccion_id = bt.basesatisfaccion_id where bf.anulado = 0 and bt.anulado = 0 AND bt.pcrc = 3272 group by NumeroTicket, Basesatisfaccion")->queryAll();


	$phpExc = new \PHPExcel();
        $phpExc->getProperties()
                ->setCreator("Konecta")
                ->setLastModifiedBy("Konecta")
                ->setTitle("Listado de Gesti?")
                ->setSubject("Fuentes de Informaci?")
                ->setDescription("Este archivo contiene el listado de gesti?")
                ->setKeywords("Lista de gesti?");
        $phpExc->setActiveSheetIndex(0);

        $phpExc->getActiveSheet()->setShowGridlines(False);

        $styleArray = array(
                'alignment' => array(
                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
            );

        $styleArraySize = array(
                'font' => array(
                        'bold' => true,
                        'size'  => 15,
                ),
                'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ), 
            );

        $styleColor = array( 
                'fill' => array( 
                    'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                    'color' => array('rgb' => '28559B'),
                )
            );

        $styleArrayTitle = array(
                'font' => array(
                  'bold' => false,
                  'color' => array('rgb' => 'FFFFFF')
                )
            );

        $styleArraySubTitle = array(              
                'fill' => array( 
                        'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                        'color' => array('rgb' => '4298B5'),
                )
            );

        $styleArraySubTitle2 = array(              
                'fill' => array( 
                    'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                    'color' => array('rgb' => 'C6C6C6'),
                )
            );  

        // ARRAY STYLE FONT COLOR AND TEXT ALIGN CENTER
        $styleArrayBody = array(
                'font' => array(
                    'bold' => false,
                    'color' => array('rgb' => '2F4F4F')
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => 'DDDDDD')
                    )
                )
            );

        $styleColorLess = array( 
                'fill' => array( 
                    'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                    'color' => array('rgb' => '92DD5B'),
                )
            );

        $styleColorMiddle = array( 
                'fill' => array( 
                    'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                    'color' => array('rgb' => 'E3AD48'),
                )
            );

        $styleColorhigh = array( 
                'fill' => array( 
                    'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                    'color' => array('rgb' => 'DD6D5B'),
                )
            );

        $phpExc->getDefaultStyle()->applyFromArray($styleArrayBody);

        $phpExc->getActiveSheet()->SetCellValue('A1','KONECTA - CX MANAGEMENT - LISTADO DE GESTION TIGO BOLIVIA');
        $phpExc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArrayTitle);
        $phpExc->setActiveSheetIndex(0)->mergeCells('A1:U1');

	$phpExc->getActiveSheet()->SetCellValue('A2','Numero Ticket');
        $phpExc->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArraySubTitle2);

        $phpExc->getActiveSheet()->SetCellValue('B2','Fecha Creación');
        $phpExc->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleArraySubTitle2);

        $phpExc->getActiveSheet()->SetCellValue('C2','Fecha Respuesta');
        $phpExc->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleArraySubTitle2);

        $phpExc->getActiveSheet()->SetCellValue('D2','Fecha Transacción');
        $phpExc->getActiveSheet()->getStyle('D2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleArraySubTitle2);
        
        $phpExc->getActiveSheet()->SetCellValue('E2','Cliente');
        $phpExc->getActiveSheet()->getStyle('E2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleArraySubTitle2);
        
        $phpExc->getActiveSheet()->SetCellValue('F2','Agente');
        $phpExc->getActiveSheet()->getStyle('F2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleArraySubTitle2);
        
        $phpExc->getActiveSheet()->SetCellValue('G2','Tipologia');
        $phpExc->getActiveSheet()->getStyle('G2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleArraySubTitle2);
        
        $phpExc->getActiveSheet()->SetCellValue('H2','Tipo Producto');
        $phpExc->getActiveSheet()->getStyle('H2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleArraySubTitle2);
        
        $phpExc->getActiveSheet()->SetCellValue('I2','Sentir Cliente');
        $phpExc->getActiveSheet()->getStyle('I2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('I2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('I2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('I2')->applyFromArray($styleArraySubTitle2);
        
        $phpExc->getActiveSheet()->SetCellValue('J2','¿Que tan probable es que recomiendas Tigo a tus familiares y amigos?');
        $phpExc->getActiveSheet()->getStyle('J2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('J2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('J2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('J2')->applyFromArray($styleArraySubTitle2);
        
        $phpExc->getActiveSheet()->SetCellValue('K2','¿Que tan satisfecho estas con la atención recibida?');
        $phpExc->getActiveSheet()->getStyle('K2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('K2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('K2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('K2')->applyFromArray($styleArraySubTitle2);
        
        $phpExc->getActiveSheet()->SetCellValue('L2','¿Que tan fácil fue resolver tu consulta/solicitud?');
        $phpExc->getActiveSheet()->getStyle('L2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('L2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('L2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('L2')->applyFromArray($styleArraySubTitle2);
        
        $phpExc->getActiveSheet()->SetCellValue('M2','¿Resolvimos el motivo de tu solicitud?');
        $phpExc->getActiveSheet()->getStyle('M2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('M2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('M2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('M2')->applyFromArray($styleArraySubTitle2);
        
        $phpExc->getActiveSheet()->SetCellValue('N2','¿Que tan satisfecho estas con el conocimiento que demostró el asesor para resolver tu consulta?');
        $phpExc->getActiveSheet()->getStyle('N2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('N2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('N2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('N2')->applyFromArray($styleArraySubTitle2);
        
        $phpExc->getActiveSheet()->SetCellValue('O2','Fecha de calificación');
        $phpExc->getActiveSheet()->getStyle('O2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('O2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('O2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('O2')->applyFromArray($styleArraySubTitle2);
        
        $phpExc->getActiveSheet()->SetCellValue('P2','Fecha Zendesk');
        $phpExc->getActiveSheet()->getStyle('P2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('P2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('P2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('P2')->applyFromArray($styleArraySubTitle2);
        
        $phpExc->getActiveSheet()->SetCellValue('Q2','Categoria & Motivos');
        $phpExc->getActiveSheet()->getStyle('Q2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('Q2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('Q2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('Q2')->applyFromArray($styleArraySubTitle2);
        
        $phpExc->getActiveSheet()->SetCellValue('R2','Solicitud');
        $phpExc->getActiveSheet()->getStyle('R2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('R2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('R2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('R2')->applyFromArray($styleArraySubTitle2);
        
        $phpExc->getActiveSheet()->SetCellValue('S2','Solución');
        $phpExc->getActiveSheet()->getStyle('S2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('S2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('S2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('S2')->applyFromArray($styleArraySubTitle2);
        
        $phpExc->getActiveSheet()->SetCellValue('T2','Observación');
        $phpExc->getActiveSheet()->getStyle('T2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('T2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('T2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('T2')->applyFromArray($styleArraySubTitle2);
        
        $phpExc->getActiveSheet()->SetCellValue('U2','Procedimiento');
        $phpExc->getActiveSheet()->getStyle('U2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('U2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('U2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('U2')->applyFromArray($styleArraySubTitle2);
        
	$numCell = 2;
        foreach ($valist as $key => $value) {
            $varidticket = $value['NumeroTicket'];
            $varbasesatisfaccion = $value['Basesatisfaccion'];
            $vartransaccion = $value['FechaTransaccion'];
            $varcliente = $value['Cliente'];
            $varagente = $value['Agente'];
            $vartipologia = $value['Tipologia'];
            $vartipoproducto = $value['TipoProducto'];
            $varsentircliente = $value['SentirCliente'];
            $varpregunta1 = $value['Pregunta1'];
            $varpregunta2 = $value['Pregunta2'];
            $varpregunta3 = $value['Pregunta3'];
            $varpregunta4 = $value['Pregunta4'];
            $varpregunta5 = $value['Pregunta5'];
            $varcalificacion = $value['FechaCalificacion'];
            $varzendesk = $value['FechaZendesk'];
            $varcreacion = $value['FechaCreacion'];
            $varrespuesta = $value['FechaRespuesta'];

            $varsolicitud = Yii::$app->db->createCommand("select distinct fsolicitud from tbl_basechat_formulario where anulado = 0 and idlista is null and idbaselista is null and ticked_id = $varidticket and basesatisfaccion_id = $varbasesatisfaccion")->queryScalar();
            $varsolucion = Yii::$app->db->createCommand("select distinct fsolucion from tbl_basechat_formulario where anulado = 0 and idlista is null and idbaselista is null and ticked_id = $varidticket and basesatisfaccion_id = $varbasesatisfaccion")->queryScalar();
            $varobservacion = Yii::$app->db->createCommand("select distinct fobservacion from tbl_basechat_formulario where anulado = 0 and idlista is null and idbaselista is null and ticked_id = $varidticket and basesatisfaccion_id = $varbasesatisfaccion")->queryScalar();
            $varprocedimiento = Yii::$app->db->createCommand("select distinct fprocedimiento from tbl_basechat_formulario where anulado = 0 and idlista is null and idbaselista is null and ticked_id = $varidticket and basesatisfaccion_id = $varbasesatisfaccion")->queryScalar();

            $varlistacategorias = Yii::$app->db->createCommand("select concat(bc.nombrecategoria,': ',bm.nombrelista) 'unidos' from tbl_basechat_categorias bc inner join tbl_basechat_motivos bm on bc.idlista = bm.idlista inner join tbl_basechat_formulario bf on bm.idbaselista = bf.idbaselista where  bf.ticked_id = $varidticket    and bf.basesatisfaccion_id = $varbasesatisfaccion")->queryAll();

            $vararraymotivos = array();
              foreach ($varlistacategorias as $key => $value) {
                  array_push($vararraymotivos, $value['unidos']);
              }
            $vartextcm = implode(", ", $vararraymotivos);

          $numCell++;

           $phpExc->getActiveSheet()->setCellValue('A'.$numCell, $varidticket); 
           $phpExc->getActiveSheet()->setCellValue('B'.$numCell, $varcreacion);
	   $phpExc->getActiveSheet()->setCellValue('C'.$numCell, $varrespuesta);
          $phpExc->getActiveSheet()->setCellValue('D'.$numCell, $vartransaccion);
          $phpExc->getActiveSheet()->setCellValue('E'.$numCell, $varcliente);
          $phpExc->getActiveSheet()->setCellValue('F'.$numCell, $varagente);
          $phpExc->getActiveSheet()->setCellValue('G'.$numCell, $vartipologia);
          $phpExc->getActiveSheet()->setCellValue('H'.$numCell, $vartipoproducto);
          $phpExc->getActiveSheet()->setCellValue('I'.$numCell, $varsentircliente);
          $phpExc->getActiveSheet()->setCellValue('J'.$numCell, $varpregunta1);
          $phpExc->getActiveSheet()->setCellValue('K'.$numCell, $varpregunta2);
          $phpExc->getActiveSheet()->setCellValue('L'.$numCell, $varpregunta3);
          $phpExc->getActiveSheet()->setCellValue('M'.$numCell, $varpregunta4);
          $phpExc->getActiveSheet()->setCellValue('N'.$numCell, $varpregunta5);
          $phpExc->getActiveSheet()->setCellValue('O'.$numCell, $varcalificacion);
          $phpExc->getActiveSheet()->setCellValue('P'.$numCell, $varzendesk);
          $phpExc->getActiveSheet()->setCellValue('Q'.$numCell, $vartextcm);
          $phpExc->getActiveSheet()->setCellValue('R'.$numCell, $varsolicitud);
          $phpExc->getActiveSheet()->setCellValue('S'.$numCell, $varsolucion);
          $phpExc->getActiveSheet()->setCellValue('T'.$numCell, $varobservacion);
          $phpExc->getActiveSheet()->setCellValue('U'.$numCell, $varprocedimiento);

	}
        $numCell = $numCell;



	$hoy = getdate();
        $hoy = $hoy['year']."_".$hoy['month']."_".$hoy['mday']."Listado_Gestion";
              
        $objWriter = \PHPExcel_IOFactory::createWriter($phpExc, 'Excel5');
                
        $tmpFile = tempnam(sys_get_temp_dir(), $hoy);
        $tmpFile.= ".xls";

        $objWriter->save($tmpFile);

        $message = "<html><body>";
        $message .= "<h3>Adjunto del archivo tipo listado de la gestion</h3>";
        $message .= "</body></html>";

        Yii::$app->mailer->compose()
                        ->setTo($varCorreo)
                        ->setFrom(Yii::$app->params['email_satu_from'])
                        ->setSubject("Envio Listado de la gestion ")
                        ->attach($tmpFile)
                        ->setHtmlBody($message)
                        ->send();


        $rtaenvio = 1;
        die(json_encode($varCorreo));

      }

      public function actionExportcol(){
        $varCorreo = Yii::$app->request->get("var_Destino");
        $sessiones = Yii::$app->user->identity->id;

        $valist = Yii::$app->db->createCommand("select bt.ticked_id 'NumeroTicket', bt.fecha_creacion 'FechaCreacion', bt.fecha_respuesta 'FechaRespuesta', bt.fecha_transaccion 'FechaTransaccion', bt.nombre_cliente 'Cliente', bt.id_agente 'Agente', bt.tipologia 'Tipologia', bt.tipo_producto 'TipoProducto', bt.comentario_adicional 'SentirCliente', bt.nps 'Pregunta1', bt.csat 'Pregunta2', bt.ces 'Pregunta3', bt.fcr 'Pregunta4', bt.conocimiento 'Pregunta5', bf.fechacalificacion 'FechaCalificacion', bf.fechazendeks 'FechaZendesk',  bt.basesatisfaccion_id  'Basesatisfaccion' from tbl_basechat_categorias bc inner join tbl_basechat_motivos bm on  bc.idlista = bm.idlista   inner join tbl_basechat_formulario bf on bm.idbaselista = bf.idbaselista inner join tbl_basechat_tigob bt on bf.ticked_id = bt.ticked_id and bf.basesatisfaccion_id = bt.basesatisfaccion_id where bf.anulado = 0 and bt.anulado = 0 AND bt.pcrc = 3513group by NumeroTicket, Basesatisfaccion")->queryAll();

        $phpExc = new \PHPExcel();
        $phpExc->getProperties()
                ->setCreator("Konecta")
                ->setLastModifiedBy("Konecta")
                ->setTitle("Listado de Gesti?")
                ->setSubject("Fuentes de Informaci?")
                ->setDescription("Este archivo contiene el listado de gesti?")
                ->setKeywords("Lista de gesti?");
        $phpExc->setActiveSheetIndex(0);

        $phpExc->getActiveSheet()->setShowGridlines(False);

        $styleArray = array(
                'alignment' => array(
                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
            );

        $styleArraySize = array(
                'font' => array(
                        'bold' => true,
                        'size'  => 15,
                ),
                'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ), 
            );

        $styleColor = array( 
                'fill' => array( 
                    'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                    'color' => array('rgb' => '28559B'),
                )
            );

        $styleArrayTitle = array(
                'font' => array(
                  'bold' => false,
                  'color' => array('rgb' => 'FFFFFF')
                )
            );

        $styleArraySubTitle = array(              
                'fill' => array( 
                        'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                        'color' => array('rgb' => '4298B5'),
                )
            );

        $styleArraySubTitle2 = array(              
                'fill' => array( 
                    'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                    'color' => array('rgb' => 'C6C6C6'),
                )
            );  

        // ARRAY STYLE FONT COLOR AND TEXT ALIGN CENTER
        $styleArrayBody = array(
                'font' => array(
                    'bold' => false,
                    'color' => array('rgb' => '2F4F4F')
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => 'DDDDDD')
                    )
                )
            );

        $styleColorLess = array( 
                'fill' => array( 
                    'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                    'color' => array('rgb' => '92DD5B'),
                )
            );

        $styleColorMiddle = array( 
                'fill' => array( 
                    'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                    'color' => array('rgb' => 'E3AD48'),
                )
            );

        $styleColorhigh = array( 
                'fill' => array( 
                    'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                    'color' => array('rgb' => 'DD6D5B'),
                )
            );

        $phpExc->getDefaultStyle()->applyFromArray($styleArrayBody);

        $phpExc->getActiveSheet()->SetCellValue('A1','KONECTA - CX MANAGEMENT - LISTADO DE GESTION TIGO COLOMBIA');
        $phpExc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArrayTitle);
        $phpExc->setActiveSheetIndex(0)->mergeCells('A1:S1');

        $phpExc->getActiveSheet()->SetCellValue('A2','Numero Ticket');
        $phpExc->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArraySubTitle2);

        $phpExc->getActiveSheet()->SetCellValue('B2','Fecha Respuesta');
        $phpExc->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleArraySubTitle2);
        
        $phpExc->getActiveSheet()->SetCellValue('C2','Cliente');
        $phpExc->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleArraySubTitle2);
        
        $phpExc->getActiveSheet()->SetCellValue('D2','Agente');
        $phpExc->getActiveSheet()->getStyle('D2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleArraySubTitle2);
        
        $phpExc->getActiveSheet()->SetCellValue('E2','Tipologia');
        $phpExc->getActiveSheet()->getStyle('E2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleArraySubTitle2);
        
        $phpExc->getActiveSheet()->SetCellValue('F2','Tipo Producto');
        $phpExc->getActiveSheet()->getStyle('F2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleArraySubTitle2);
        
        $phpExc->getActiveSheet()->SetCellValue('G2','Sentir Cliente');
        $phpExc->getActiveSheet()->getStyle('G2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleArraySubTitle2);
        
        $phpExc->getActiveSheet()->SetCellValue('H2','¿Que tan probable es que recomiendas Tigo a tus familiares y amigos?');
        $phpExc->getActiveSheet()->getStyle('H2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleArraySubTitle2);
        
        $phpExc->getActiveSheet()->SetCellValue('I2','¿Que tan satisfecho estas con la atención recibida?');
        $phpExc->getActiveSheet()->getStyle('I2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('I2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('I2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('I2')->applyFromArray($styleArraySubTitle2);
        
        $phpExc->getActiveSheet()->SetCellValue('J2','¿Que tan fácil fue resolver tu consulta/solicitud?');
        $phpExc->getActiveSheet()->getStyle('J2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('J2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('J2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('J2')->applyFromArray($styleArraySubTitle2);
        
        $phpExc->getActiveSheet()->SetCellValue('K2','¿Resolvimos el motivo de tu solicitud?');
        $phpExc->getActiveSheet()->getStyle('K2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('K2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('K2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('K2')->applyFromArray($styleArraySubTitle2);
        
        $phpExc->getActiveSheet()->SetCellValue('L2','¿Que tan satisfecho estas con el conocimiento que demostró el asesor para resolver tu consulta?');
        $phpExc->getActiveSheet()->getStyle('L2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('L2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('L2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('L2')->applyFromArray($styleArraySubTitle2);
        
        $phpExc->getActiveSheet()->SetCellValue('M2','Fecha de calificación');
        $phpExc->getActiveSheet()->getStyle('M2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('M2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('M2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('M2')->applyFromArray($styleArraySubTitle2);
        
        $phpExc->getActiveSheet()->SetCellValue('N2','Fecha Zendesk');
        $phpExc->getActiveSheet()->getStyle('N2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('N2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('N2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('N2')->applyFromArray($styleArraySubTitle2);
        
        $phpExc->getActiveSheet()->SetCellValue('O2','Categoria & Motivos');
        $phpExc->getActiveSheet()->getStyle('O2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('O2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('O2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('O2')->applyFromArray($styleArraySubTitle2);
        
        $phpExc->getActiveSheet()->SetCellValue('P2','Solicitud');
        $phpExc->getActiveSheet()->getStyle('P2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('P2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('P2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('P2')->applyFromArray($styleArraySubTitle2);
        
        $phpExc->getActiveSheet()->SetCellValue('Q2','Solución');
        $phpExc->getActiveSheet()->getStyle('Q2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('Q2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('Q2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('Q2')->applyFromArray($styleArraySubTitle2);
        
        $phpExc->getActiveSheet()->SetCellValue('R2','Observación');
        $phpExc->getActiveSheet()->getStyle('R2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('R2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('R2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('R2')->applyFromArray($styleArraySubTitle2);
        
        $phpExc->getActiveSheet()->SetCellValue('S2','Procedimiento');
        $phpExc->getActiveSheet()->getStyle('S2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('S2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('S2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('S2')->applyFromArray($styleArraySubTitle2);

        
        $numCell = 2;
        foreach ($valist as $key => $value) {
            $varidticket = $value['NumeroTicket'];
            $varbasesatisfaccion = $value['Basesatisfaccion'];
            $vartransaccion = $value['FechaTransaccion'];
            $varcliente = $value['Cliente'];
            $varagente = $value['Agente'];
            $vartipologia = $value['Tipologia'];
            $vartipoproducto = $value['TipoProducto'];
            $varsentircliente = $value['SentirCliente'];
            $varpregunta1 = $value['Pregunta1'];
            $varpregunta2 = $value['Pregunta2'];
            $varpregunta3 = $value['Pregunta3'];
            $varpregunta4 = $value['Pregunta4'];
            $varpregunta5 = $value['Pregunta5'];
            $varcalificacion = $value['FechaCalificacion'];
            $varzendesk = $value['FechaZendesk'];
            $varcreacion = $value['FechaCreacion'];
            $varrespuesta = $value['FechaRespuesta'];

            $varsolicitud = Yii::$app->db->createCommand("select distinct fsolicitud from tbl_basechat_formulario where anulado = 0 and idlista is null and idbaselista is null and ticked_id = $varidticket and basesatisfaccion_id = $varbasesatisfaccion")->queryScalar();
            $varsolucion = Yii::$app->db->createCommand("select distinct fsolucion from tbl_basechat_formulario where anulado = 0 and idlista is null and idbaselista is null and ticked_id = $varidticket and basesatisfaccion_id = $varbasesatisfaccion")->queryScalar();
            $varobservacion = Yii::$app->db->createCommand("select distinct fobservacion from tbl_basechat_formulario where anulado = 0 and idlista is null and idbaselista is null and ticked_id = $varidticket and basesatisfaccion_id = $varbasesatisfaccion")->queryScalar();
            $varprocedimiento = Yii::$app->db->createCommand("select distinct fprocedimiento from tbl_basechat_formulario where anulado = 0 and idlista is null and idbaselista is null and ticked_id = $varidticket and basesatisfaccion_id = $varbasesatisfaccion")->queryScalar();

            $varlistacategorias = Yii::$app->db->createCommand("select concat(bc.nombrecategoria,': ',bm.nombrelista) 'unidos' from tbl_basechat_categorias bc inner join tbl_basechat_motivos bm on bc.idlista = bm.idlista inner join tbl_basechat_formulario bf on bm.idbaselista = bf.idbaselista where  bf.ticked_id = $varidticket    and bf.basesatisfaccion_id = $varbasesatisfaccion")->queryAll();

            $vararraymotivos = array();
            foreach ($varlistacategorias as $key => $value) {
                array_push($vararraymotivos, $value['unidos']);
            }
            $vartextcm = implode(", ", $vararraymotivos);

          $numCell++;

          $phpExc->getActiveSheet()->setCellValue('A'.$numCell, $varidticket); 
          $phpExc->getActiveSheet()->setCellValue('B'.$numCell, $varrespuesta);
          $phpExc->getActiveSheet()->setCellValue('C'.$numCell, $varcliente);
          $phpExc->getActiveSheet()->setCellValue('D'.$numCell, $varagente);
          $phpExc->getActiveSheet()->setCellValue('E'.$numCell, $vartipologia);
          $phpExc->getActiveSheet()->setCellValue('F'.$numCell, $vartipoproducto);
          $phpExc->getActiveSheet()->setCellValue('G'.$numCell, $varsentircliente);
          $phpExc->getActiveSheet()->setCellValue('H'.$numCell, $varpregunta1);
          $phpExc->getActiveSheet()->setCellValue('I'.$numCell, $varpregunta2);
          $phpExc->getActiveSheet()->setCellValue('J'.$numCell, $varpregunta3);
          $phpExc->getActiveSheet()->setCellValue('K'.$numCell, $varpregunta4);
          $phpExc->getActiveSheet()->setCellValue('L'.$numCell, $varpregunta5);
          $phpExc->getActiveSheet()->setCellValue('M'.$numCell, $varcalificacion);
          $phpExc->getActiveSheet()->setCellValue('N'.$numCell, $varzendesk);
          $phpExc->getActiveSheet()->setCellValue('O'.$numCell, $vartextcm);
          $phpExc->getActiveSheet()->setCellValue('P'.$numCell, $varsolicitud);
          $phpExc->getActiveSheet()->setCellValue('Q'.$numCell, $varsolucion);
          $phpExc->getActiveSheet()->setCellValue('R'.$numCell, $varobservacion);
          $phpExc->getActiveSheet()->setCellValue('S'.$numCell, $varprocedimiento);

        }
        $numCell = $numCell;



        $hoy = getdate();
        $hoy = $hoy['year']."_".$hoy['month']."_".$hoy['mday']."Listado_Gestion";
              
        $objWriter = \PHPExcel_IOFactory::createWriter($phpExc, 'Excel5');
                
        $tmpFile = tempnam(sys_get_temp_dir(), $hoy);
        $tmpFile.= ".xls";

        $objWriter->save($tmpFile);

        $message = "<html><body>";
        $message .= "<h3>Adjunto del archivo tipo listado de la gestion</h3>";
        $message .= "</body></html>";

        Yii::$app->mailer->compose()
                        ->setTo($varCorreo)
                        ->setFrom(Yii::$app->params['email_satu_from'])
                        ->setSubject("Envio Listado de la gestion ")
                        ->attach($tmpFile)
                        ->setHtmlBody($message)
                        ->send();

        $rtaenvio = 1;
        die(json_encode($rtaenvio));

      }


    // Fin imputabilidad de tigo Colombia




    }

?>