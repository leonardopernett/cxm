<?php

namespace app\controllers;

ini_set('upload_max_filesize', '50M');

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
use GuzzleHttp;
use app\models\ProcesosAdministrador;
use app\models\Categoriafeedbacks;
use app\models\Tipofeedbacks;
use app\models\Dashboardpermisos;
use app\models\BaseUsuariosip;
use app\models\FormUploadtigo;
use app\models\BaseSatisfaccion; 
use app\models\ControlProcesos;
use app\models\Equipos;
use app\models\ControlParams;
use \yii\base\Exception;
use app\models\IdealServicios;
use app\models\SpeechServicios;
use app\models\Encuestaspersonalsatu;
use app\models\Procesoclientecentroscosto;
use app\models\Evaluados;
use app\models\Corteservicios;
use app\models\Declinaciones;
use app\models\Pilaresgptw;
use app\models\Areaapoyogptw;
use app\models\ProcesosSatisfaccion;
use app\models\DetallesPilaresGptw;
use app\models\IndicadorSatisfaccion;
use app\models\Comdataparametrizarapi;
use app\models\Tipoalertasqyr;
use app\models\Areasqyr;
use app\models\Tipologiasqyr;
use app\models\RespuestaAutomatica;
use app\models\Estadosqyr;
use app\models\Cumplimientoqyr;
use app\models\WorspaceReportesPowerbi;
use app\models\BaseAleatorio;

  class ProcesosadministradorController extends \yii\web\Controller {

    public function behaviors(){
      return[
        'access' => [
            'class' => AccessControl::classname(),
            'only' => ['index','viewresponsability','categoriascxm','viewescucharmas','deletepermisos',
            'viewusuariosencuestas','importarusuarios','deletesip','buscarurls','calcularurls',
            'parametrizarplan','deletecontrol','parametrizarequipos','deleteteamparams','parametrizarasesores',
            'parametrizarpcrc','parametrizarfuncionapcrc','parametrizarresponsabilidad','viewresponsabilidad',
            'adminmensajes','listarnombres','adminpcrc','actualizapcrc','procesopcrc',
            'admingenesys','porconnid','actualizaasesor','gbuscarporasesor','gbuscarporconnid','actualizaservicio',
            'deleteserviciocorte','cortesyservicios','viewmotivosdeclinacion','viewmotivosdeclinacion','deletepilares',
            'deleteareaapoyo','viewareaapoyogptw','viewprocesossatisfaccion','viewdetallepilaresgptw','viewindicadores',
            'adminusuarios','adminapiwiasae','viewtipoalertasqyr','deletealertasqyr',
            'viewareasqyr','varListAreasqyr','viewtipologiasqyr','viewrespuestaautomaticaqyr','deleterespuestaqyr',
            'viewalertacumplimientoqyr','importardocumento','viewcartarespuestaqyr','parametrizarpcrccomdata','deletepcrcs','parametrizarpbi','aleatorioencuestas','viewheroes',
            'parametrizarpcrcatributoscriticos', 'cargarlistapcrc', 'deletepcrcatributoscriticos',
            'parametrizarpcrcvaloracionescomdata', 'cargarlistapcrcvaloracionescomdata', 'deletepcrcvaloracionescomdata'],
            'rules' => [
              [
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema() || Yii::$app->user->identity->isControlProcesoCX();
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
        $model = new ProcesosAdministrador();
      
        return $this->render('index',[
            'model' => $model,
        ]);
    }

    public function actionViewresponsability(){
        $model = new ProcesosAdministrador();
        $txtConteo = 0;
        $varidarbol = null;
        $varListresponsabilidad = null;
        $varnombrepcrc = null;

        $form = Yii::$app->request->post();
        if($model->load($form)){
            $varidarbol = $model->procesos;

            $varListresponsabilidad = Yii::$app->db->createCommand("SELECT * FROM tbl_responsabilidad r WHERE r.arbol_id in ('$varidarbol')")->queryAll();
            $txtConteo = count($varListresponsabilidad);

            $varnombrepcrc = Yii::$app->db->createCommand("SELECT a.name FROM tbl_arbols a WHERE a.id in ('$varidarbol')")->queryScalar();
        }

        return $this->render('viewresponsability',[
            'model' => $model,
            'txtConteo' => $txtConteo,
            'varidarbol' => $varidarbol,
            'varListresponsabilidad' => $varListresponsabilidad,
            'varnombrepcrc' => $varnombrepcrc,
        ]);
    }

    public function actionGetarbolesbyroles($search = null, $id = null) {
        $out = ['more' => false];
        $grupo = Yii::$app->user->identity->grupousuarioid;
        if (!is_null($search)) {
            $data = \app\models\Arboles::find()
                    ->joinWith('permisosGruposArbols')
                    ->join('INNER JOIN', 'tbl_grupos_usuarios', 'tbl_permisos_grupos_arbols.grupousuario_id = tbl_grupos_usuarios.grupos_id')
                    ->select(['id' => 'tbl_arbols.id', 'text' => 'UPPER(tbl_arbols.dsname_full)'])
                    ->where([
                        "sncrear_formulario" => 1,
                        "snhoja" => 1,
                        "grupousuario_id" => $grupo])
                    ->andWhere(['not', ['formulario_id' => null]])
                    ->andWhere('name LIKE "%' . $search . '%" ')
                    ->andWhere('tbl_grupos_usuarios.per_realizar_valoracion = 1')
                    ->orderBy("dsorden ASC")
                    ->asArray()
                    ->all();
            $out['results'] = array_values($data);
        } elseif (!empty($id)) {
            $data = \app\models\Arboles::find()
                    ->joinWith('permisosGruposArbols')
                    ->join('INNER JOIN', 'tbl_grupos_usuarios', 'tbl_permisos_grupos_arbols.grupousuario_id = tbl_grupos_usuarios.grupos_id')
                    ->select(['id' => 'tbl_arbols.id', 'text' => 'UPPER(tbl_arbols.dsname_full)'])
                    ->where([
                        "sncrear_formulario" => 1,
                        "snhoja" => 1,
                        "grupousuario_id" => $grupo])
                    ->andWhere(['not', ['formulario_id' => null]])
                    ->andWhere('tbl_arbols.id = ' . $id)
                    ->andWhere('tbl_grupos_usuarios.per_realizar_valoracion = 1')
                    ->orderBy("dsorden ASC")
                    ->asArray()
                    ->all();
            $out['results'] = array_values($data);
        } else {
            $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
        }
        echo \yii\helpers\Json::encode($out);
    }

    public function actionGenerarregistro(){
        $varidvararboltwo = Yii::$app->request->get('txtvaridvararboltwo');
        $varidvararbol = Yii::$app->request->get('txtvaridvararbol');

        $arbolclon = Yii::$app->db->createCommand("SELECT * FROM tbl_responsabilidad r WHERE r.arbol_id in ('$varidvararboltwo')")->queryAll();

        if (count($arbolclon) != 0) {
            foreach ($arbolclon as $key => $value) {
                Yii::$app->db->createCommand()->insert('tbl_responsabilidad',[
                    'arbol_id' => $varidvararbol,
                    'nombre' => $value['nombre'],
                    'tipo' => $value['tipo'],                                          
                ])->execute();
            }
            $txtrta = 1;
        }else{
            $txtrta = 0;
        }
        

        die(json_encode($txtrta));
    }

    public function actionCategoriascxm(){
        $modelpadre = new Categoriafeedbacks();
        $modelhijo = new Tipofeedbacks();
  
        $dataProvider = $modelpadre->searchlist();
  
        $form = Yii::$app->request->post();
        if ($modelpadre->load($form)) {
  
          if ($modelpadre->name) {
              Yii::$app->db->createCommand()->insert('tbl_categoriafeedbacks',[
                      'name' => $modelpadre->name,                                     
                  ])->execute();
  
            return $this->redirect('categoriascxm',['modelpadre'=>$modelpadre,'dataProvider' => $dataProvider,'modelhijo' => $modelhijo,]);
          }          
        }
  
        if ($modelhijo->load($form)) {
         
         if ($modelhijo->categoriafeedback_id && $modelhijo->name) {
             Yii::$app->db->createCommand()->insert('tbl_tipofeedbacks',[
                      'categoriafeedback_id' => $modelhijo->categoriafeedback_id,
                      'name' => $modelhijo->name,
                      'snaccion_correctiva' => 1,
                      'sncausa_raiz' => 1,
                      'sncompromiso' => 1,
                      'cdtipo_automatico' => 0,
                      'dsmensaje_auto' => 'Generado por el usuario',                             
                  ])->execute();
  
              return $this->redirect('categoriascxm',['modelpadre'=>$modelpadre,'dataProvider' => $dataProvider,'modelhijo' => $modelhijo,]);
         }
              
        }
  
        return $this->render('categoriascxm',[
          'modelpadre' => $modelpadre,
          'dataProvider' => $dataProvider,
          'modelhijo' => $modelhijo,
        ]);
    }

    public function actionViewescucharmas(){
        $model = new Dashboardpermisos();

        $form = Yii::$app->request->post();
        if ($model->load($form)) {
            $paramsBusqueda = [':varid_dp_clientes' => $model->iddashservicio, ':anulado' => 0];

            $varNombreservicio = Yii::$app->db->createCommand('
              SELECT p.cliente FROM tbl_procesos_volumendirector p 
                WHERE p.id_dp_clientes = :varid_dp_clientes
                    AND p.anulado = :anulado
                    GROUP BY p.id_dp_clientes')->bindValues($paramsBusqueda)->queryScalar();

            Yii::$app->db->createCommand()->insert('tbl_dashboardpermisos',[
                                             'iddashservicio' => $model->iddashservicio,
                                             'usuaid' => $model->usuaid,
                                             'nombreservicio' => $varNombreservicio,
                                             'fechacreacion' => date("Y-m-d"),
                                             'anulado' => 0,
                                         ])->execute(); 

            return $this->redirect('viewescucharmas',[
                'model' => $model,
            ]);
        }

        return $this->render('viewescucharmas',[
            'model' => $model,
        ]);
    }
    public function actionViewdetallepilaresgptw(){
        $model = new DetallesPilaresGptw();
        $model2 = new Pilaresgptw();

        $form = Yii::$app->request->post();
        if ($model->load($form)) {

            $varidpilar = $model->id_pilares;
            $varnombre = $model->nombre;

            Yii::$app->db->createCommand()->insert('tbl_detalle_pilaresgptw',[
                                             'id_pilares' => $varidpilar,
                                             'nombre' => $varnombre,
                                             'fechacreacion' => date("Y-m-d"),
                                             'anulado' => 0,
                                             'usua_id' => Yii::$app->user->identity->id,
                                         ])->execute(); 

            return $this->redirect('viewdetallepilaresgptw',[
                'model' => $model,
                'model2' => $model2,
            ]);
        }

        return $this->render('viewdetallepilaresgptw',[
            'model' => $model,
            'model2' => $model2,
        ]);
    }

    public function actionDeletepermisos($id){
        Dashboardpermisos::findOne($id)->delete();

        $model = new Dashboardpermisos();

        return $this->redirect('viewescucharmas',[
            'model' => $model,
        ]);
    }

    public function actionViewusuariosencuestas(){
        $model = new BaseUsuariosip();
        $dataList = 0;
        $ListaRegistro = null;

        $paramsBusquedaCambiado = [':varCambiado' => 1];

        $varCambiado = Yii::$app->db->createCommand('
            SELECT COUNT(bu.idusuariossip) AS Cambiados FROM tbl_base_usuariosip bu
                WHERE 
                    bu.cambios = :varCambiado')->bindValues($paramsBusquedaCambiado)->queryScalar();

        
        $varNoCambiado = Yii::$app->db->createCommand('
            SELECT COUNT(bu.idusuariossip) AS Cambiados FROM tbl_base_usuariosip bu
                WHERE 
                    bu.cambios IS NULL')->queryScalar();

        $varTotalAsesores = Yii::$app->db->createCommand('
            SELECT COUNT(bu.idusuariossip) AS TotalAsesores FROM tbl_base_usuariosip bu')->queryScalar();

        $varFechaMax = Yii::$app->db->createCommand('
            SELECT MAX(bu.fechacreacion) AS FechaMax FROM tbl_base_usuariosip bu')->queryScalar();


        $form = Yii::$app->request->post();
        if ($model->load($form)) {
            $paramsBusquedaEval = [':varIdEval' => $model->evaluados_id];

            $ListaRegistro = Yii::$app->db->createCommand('
            SELECT bu.idusuariossip, bu.comentarios, bu.identificacion, bu.usuariored, bu.usuariosip,  
                if(bu.cambios = 1,"Si","No") AS cambios, bu.fechacambios FROM tbl_base_usuariosip bu
                WHERE 
                    bu.evaluados_id IN (:varIdEval)')->bindValues($paramsBusquedaEval)->queryAll();

            $dataList = count($ListaRegistro);
        }

        return $this->render('viewusuariosencuestas',[
            'model' => $model,
            'dataList' => $dataList,
            'ListaRegistro' => $ListaRegistro,
            'varCambiado' => $varCambiado,
            'varNoCambiado' => $varNoCambiado,
            'varTotalAsesores' => $varTotalAsesores,
            'varFechaMax' => $varFechaMax,
        ]);
    }

    public function actionDeletesip($id){
        BaseUsuariosip::findOne($id)->delete();

        return $this->redirect('viewusuariosencuestas');
    }

    public function actionActualizaprocesos(){
        $paramsBusqueda = [':varAnulado' => 0];

        $varListSip = Yii::$app->db->createCommand('
            SELECT bu.idusuariossip, bu.usuariored, bu.usuariosip, bu.cambios FROM tbl_base_usuariosip bu
                WHERE 
                    bu.anulado = :varAnulado
                GROUP BY bu.usuariosip')->bindValues($paramsBusqueda)->queryAll();

        foreach ($varListSip as $key => $value) {
            $paramsBusquedaSip = [':varUsarioSip' => $value['usuariosip']];
            $varUsariosRed = $value['usuariored'];
            $varIdSip = $value['idusuariossip'];

            
                $varListBaseSip = Yii::$app->db->createCommand('
                SELECT b.id FROM tbl_base_satisfaccion b
                    WHERE 
                     b.agente IN (:varUsarioSip)')->bindValues($paramsBusquedaSip)->queryAll();

                if (count($varListBaseSip) != 0) {
                    foreach ($varListBaseSip as $key => $value) {
                        Yii::$app->db->createCommand('
                            UPDATE tbl_base_satisfaccion 
                                SET agente = :varAgente
                                    WHERE 
                                        id = :VarId')
                            ->bindValue(':VarId', $value['id'])
                            ->bindValue(':varAgente', $varUsariosRed)
                            ->execute(); 
                    }

                    Yii::$app->db->createCommand('
                            UPDATE tbl_base_usuariosip 
                                SET fechacambios = :varFecha, cambios = :varCambios
                                    WHERE 
                                        idusuariossip = :VarIdSip')
                            ->bindValue(':VarIdSip', $varIdSip)
                            ->bindValue(':varFecha', date("Y-m-d"))
                            ->bindValue(':varCambios', 1)
                            ->execute(); 
                }
            
            
        }

        return $this->redirect('viewusuariosencuestas');
    }

    public function actionImportarusuarios(){
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
                        $this->Importexcelusuarios($name);

                        return $this->redirect('viewusuariosencuestas');
                    }
                }
           }

        return $this->renderAjax('importarusuarios',[
            'model' => $model,
        ]);
    }

    public function Importexcelusuarios($name){
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

        for ($row = 2; $row <= $highestRow; $row++) { 
            
            if ($sheet->getCell("A".$row)->getValue() != null) {

                $paramsBusqueda = [':varAsesorCC' => $sheet->getCell("A".$row)->getValue()];

                $varListaSip = Yii::$app->db->createCommand('
                  SELECT COUNT(bu.idusuariossip) FROM tbl_base_usuariosip bu
                    WHERE 
                        bu.identificacion IN (:varAsesorCC)')->bindValues($paramsBusqueda)->queryScalar();

                if ($varListaSip == "0") {
                    $varExisteUsuario = Yii::$app->db->createCommand('
                      SELECT if(COUNT(e.id)=0,0,1) AS rta FROM tbl_evaluados e 
                        WHERE 
                            e.identificacion IN (:varAsesorCC)')->bindValues($paramsBusqueda)->queryScalar();

                    $varIdEvalua = 0;
                    if ($varExisteUsuario != 0) {
                        $varIdEvalua = Yii::$app->db->createCommand('
                      SELECT e.id FROM tbl_evaluados e 
                        WHERE 
                            e.identificacion IN (:varAsesorCC)')->bindValues($paramsBusqueda)->queryScalar();
                    }                

                    Yii::$app->db->createCommand()->insert('tbl_base_usuariosip',[
                                        'usuariored' => $sheet->getCell("D".$row)->getValue(),
                                        'usuariosip' => $sheet->getCell("C".$row)->getValue(),
                                        'evaluados_id' => $varIdEvalua,
                                        'identificacion' => $sheet->getCell("A".$row)->getValue(),
                                        'comentarios' => $sheet->getCell("B".$row)->getValue(),
                                        'existeusuario' => $varExisteUsuario,
                                        'fechacreacion' => date("Y-m-d"),
                                        'anulado' => 0,
                                        'usua_id' => Yii::$app->user->identity->id,
                                        ])->execute();
                }
                 
            }

        }

    }

    public function actionBuscarurls(){
        $model = new BaseSatisfaccion();

        $paramsbusquedaurls = [':varAnulado' => 0];

        $varDataMax = Yii::$app->db->createCommand('
            SELECT CONCAT(MAX(bu.fechaingreso)," - ",bu.cantidadurls) AS Datas FROM tbl_base_urllogs bu
                WHERE 
                    bu.anulado = :varAnulado')->bindValues($paramsbusquedaurls)->queryScalar();


        $form = Yii::$app->request->post();
        if ($model->load($form)) {
            $varFechaSatu = explode(' - ',$model->fecha_gestion);
            $varFechaInicio = $varFechaSatu[0] . ' 00:00:00';
            $varFechaFin = $varFechaSatu[1] . ' 23:59:59';

            $allModels = BaseSatisfaccion::find()
                    ->where(['=','buzon',""])
                    ->andwhere(['BETWEEN','fecha_satu',$varFechaInicio,$varFechaFin]);
            
            try {
                $allModels = $allModels->all();
            } catch (Exception $exc) {
                \Yii::error('Error en consulta Masiva: *****' . $exc->getMessage(), 'redbox');
            }

            $count = 0;

            foreach ($allModels as $nModel) {
                
                if (is_null($nModel->buzon) || empty($nModel->buzon) || $nModel->buzon == "") {
                    $nModel->buzon = $this->_buscarArchivoBuzon(
                                sprintf("%02s", $nModel->dia) . "_" . sprintf("%02s", $nModel->mes) . "_" . $nModel->ano, $nModel->connid);                    
                }

                if (!is_null($nModel->llamada) || (!empty($nModel->buzon) || $nModel->buzon != "")) {
                    $count++;
                }

                try {
                    $nModel->save();
                } catch (Exception $exc) {
                    \Yii::error('Error al momento de guardar el registro: ' . $nModel->id . ' ' . $exc->getMessage() . '#####', 'redbox');
                }

            }

            return $this->redirect(['calcularurls',
                'txtfechainicio' => $varFechaInicio,
                'txtfechafin' => $varFechaFin,
            ]);                       

        }

        return $this->render('buscarurls',[
            'model' => $model,
            'varDataMax' => $varDataMax,
        ]);
    }

    public function actionCalcularurls($txtfechainicio,$txtfechafin){   
        $model = new BaseSatisfaccion();
        $varBuzon  = "/srv/www/htdocs/qa_managementv2/web/buzones_qa";

        $varCantidadUrl = (new \yii\db\Query())
                                    ->select(['id'])
                                    ->from(['tbl_base_satisfaccion'])
                                    ->where(['LIKE','buzon',$varBuzon])
                                    ->andwhere(['BETWEEN','fecha_satu',$txtfechainicio,$txtfechafin])
                                    ->count();        

        $form = Yii::$app->request->post();
        if ($model->load($form)) {
            
            $varFechaInicio = $txtfechainicio;
            $varFechaFin = $txtfechafin;

            Yii::$app->db->createCommand()->insert('tbl_base_urllogs',[
                                        'fechaingreso' => date("Y-m-d"),
                                        'cantidadurls' => $varCantidadUrl,
                                        'fechacreacion' => date("Y-m-d"),
                                        'anulado' => 0,
                                        'usua_id' => Yii::$app->user->identity->id,
                                        ])->execute();

            $this->Buscarkaliope($varFechaInicio,$varFechaFin);

            return $this->redirect('buscarurls');

        }

        return $this->render('calcularurls',[
            'model' => $model,
            'varCantidadUrl' => $varCantidadUrl,
            'txtfechainicio' => $txtfechainicio,
            'txtfechafin' => $txtfechafin,
        ]);
    }

    private function _buscarArchivoBuzon($fechaEncuesta, $connId) {
        $output = NULL;
        try {
            $rutaPrincipalBuzonesLlamadas = \Yii::$app->params["ruta_buzon"];
            $command = "find {$rutaPrincipalBuzonesLlamadas}/Buzones_{$fechaEncuesta} -iname *{$connId}*.wav";
            \Yii::error("COMANDO BUZON: " . $command, 'procesosadministrador');
            file_put_contents("A.TXT", $command);
            $output = exec($command);
        } catch (\yii\base\Exception $exc) {
            \Yii::error($exc->getTraceAsString(), 'procesosadministrador');
            return $output;
        }
        
        return $output;
    }

    public function Buscarkaliope($varFechaInicio,$varFechaFin){
        $vartexto = null;
        $varvalencia = null;
        $txtErrorTranscripcion = "Error al buscar transcipcion";
        $txtErrorEmocional = "Error al buscar valencia emocioanl";

        ini_set("max_execution_time", "900");
        ini_set("memory_limit", "1024M");
        ini_set( 'post_max_size', '1024M' );

        ignore_user_abort(true);
        set_time_limit(900);
        $varBuzon  = "/srv/www/htdocs/qa_managementv2/web/buzones_qa";

        $varlista = (new \yii\db\Query())
                                    ->select(['id','fecha_satu','connid'])
                                    ->from(['tbl_base_satisfaccion'])
                                    ->where(['LIKE','buzon',$varBuzon])
                                    ->andwhere(['BETWEEN','fecha_satu',$varFechaInicio,$varFechaFin])
                                    ->all(); 

        foreach ($varlista as $key => $value) {
            $txtvaridruta = $value['connid'];
            $txtcreated = $value['fecha_satu'];
            
            $paramsConnid = [':varConnid'=>$txtvaridruta];
            $varExiste = Yii::$app->db->createCommand('
                SELECT COUNT(k.connid) FROM tbl_kaliope_transcipcion k 
                    WHERE 
                        k.connid IN (:varConnid)')->bindValues($paramsConnid)->queryScalar();

            if ($varExiste == 0) {               

                ob_start();
                $curl = curl_init();

                curl_setopt_array($curl, array(

                    CURLOPT_SSL_VERIFYPEER=> false,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_URL => KALIOPE_STATUS_BY_CONNID,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS =>'{"connid": "'.$txtvaridruta.'"}',
                    CURLOPT_HTTPHEADER => array(
                      'x-api-key: gFMiqdNjw55uel1Sxvszka2mArOfrcDhPoNjEZyi',
                      'Content-Type: application/json'
                    ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);
                ob_clean();

                if (!$response) {
                  $vartexto += $txtErrorTranscripcion;
                  $varvalencia += $txtErrorEmocional;
                }

                $response = json_decode(iconv( "Windows-1252", "UTF-8", $response ),true);

                if (empty($response)) {
                    $vartexto = $txtErrorTranscripcion;
                    $varvalencia = $txtErrorEmocional;
                }else{
                    $vartexto = $response[0]['transcription'];
                    $varvalencia = $response[0]['valencia'];

                    if ($varvalencia == "NULL" || $varvalencia == "" || $varvalencia == "null") {
                        $varvalencia = "Buzón sin información";
                    }
                   
                    Yii::$app->db->createCommand()->insert('tbl_kaliope_transcipcion',[
                                               'connid' => $txtvaridruta,
                                               'transcripcion' => $vartexto,
                                               'valencia' => $varvalencia,
                                               'fechagenerada' => $txtcreated,
                                               'fechacreacion' => date("Y-m-d"),
                                               'anulado' => 0,
                                               'usua_id' => Yii::$app->user->identity->id,
                                           ])->execute();
                    
                }
            }
            
        }

    }

    public function actionParametrizarplan(){
        $model = new ControlProcesos();

        $varListBloqueos = (new \yii\db\Query())
                                    ->select(['*'])
                                    ->from(['tbl_control_parametros'])
                                    ->orderBy(['fecha_inicio' => SORT_DESC])
                                    ->all(); 

        $form = Yii::$app->request->post();
        if ($model->load($form)) {

            foreach ($varListBloqueos as $key => $value) {
                Yii::$app->db->createCommand('
                            UPDATE tbl_control_parametros 
                                SET anulado = :varAnulado
                                    WHERE 
                                        idcontrolprocesos = :VarId')
                            ->bindValue(':VarId', $value['idcontrolprocesos'])
                            ->bindValue(':varAnulado', 1)
                            ->execute(); 
            }

            $varFechas = explode(" ", $model->fechacreacion);

            $txtFechaInicio = $varFechas[0];
            $txtFechaFin = date('Y-m-d',strtotime($varFechas[2]));

            Yii::$app->db->createCommand()->insert('tbl_control_parametros',[
                    'fecha_inicio' => $txtFechaInicio,
                    'fecha_fin' => $txtFechaFin,
                    'fechacreacion' => date("Y-m-d"),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
            ])->execute();

            return $this->redirect('parametrizarplan');
        }

        return $this->render('parametrizarplan',[
            'model' => $model,
            'varListBloqueos' => $varListBloqueos,
        ]);
    }

    public function actionDeletecontrol($id){
        $paramsEliminar = [':IdControl'=>$id];          

        Yii::$app->db->createCommand('
              DELETE FROM tbl_control_parametros 
                WHERE 
                  idcontrolprocesos = :IdControl')
            ->bindValues($paramsEliminar)
            ->execute();

        return $this->redirect(['parametrizarplan']);
    }

    public function actionParametrizarequipos(){
        $model = new Equipos();

        $varListEquipos = (new \yii\db\Query())
                                    ->select(['*'])
                                    ->from(['tbl_equipo_parametros'])
                                    ->orderBy(['fecha_creacion' => SORT_DESC])
                                    ->all(); 

        $form = Yii::$app->request->post();
        if ($model->load($form)) {
            $txtidEquipo = $model->usua_id;
            $txtComentario = $model->name;

            Yii::$app->db->createCommand()->insert('tbl_equipo_parametros',[
                    'id_equipo' => $txtidEquipo,
                    'comentarios' => $txtComentario,
                    'fecha_creacion' => date("Y-m-d"),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
            ])->execute();

            return $this->redirect(['parametrizarequipos']);
        }

        return $this->render('parametrizarequipos',[
            'model' => $model,
            'varListEquipos' => $varListEquipos,
        ]);
    }

    public function actionViewareaapoyogptw(){
        $model = new Areaapoyogptw();

        $varListAreaapoyo= (new \yii\db\Query())
                                    ->select(['*'])
                                    ->from(['tbl_areasapoyo_gptw'])
                                    ->orderBy(['nombre' => SORT_DESC])
                                    ->where(['=','anulado',0])          
                                    ->all(); 

        $form = Yii::$app->request->post();
        if ($model->load($form)) {
           // $txtidEquipo = $model->usua_id;
            $txtnombre = $model->nombre;

            Yii::$app->db->createCommand()->insert('tbl_areasapoyo_gptw',[
                    'nombre' => $txtnombre,
                    'fechacreacion' => date("Y-m-d"),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
            ])->execute();

            return $this->redirect(['viewareaapoyogptw']);
        }

        return $this->render('viewareaapoyogptw',[
            'model' => $model,
            'varListAreaapoyo' => $varListAreaapoyo,
        ]);
    }

    public function actionViewpilaresgptw(){
        $model = new Pilaresgptw();

        $varListMotivos= (new \yii\db\Query())
                                    ->select(['*'])
                                    ->from(['tbl_pilares_gptw'])
                                    ->orderBy(['nombre_pilar' => SORT_DESC])
                                    ->where(['=','anulado',0])          
                                    ->all(); 

        $form = Yii::$app->request->post();
        if ($model->load($form)) {
           // $txtidEquipo = $model->usua_id;
            $txtnombre = $model->nombre_pilar;

            Yii::$app->db->createCommand()->insert('tbl_pilares_gptw',[
                    'nombre_pilar' => $txtnombre,
                    'fechacreacion' => date("Y-m-d"),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
            ])->execute();

            return $this->redirect(['viewpilaresgptw']);
        }

        return $this->render('viewpilaresgptw',[
            'model' => $model,
            'varListEquipos' => $varListMotivos,
        ]);
    }

    public function actionViewprocesossatisfaccion(){
        $model = new ProcesosSatisfaccion();

        $varListarprocesos= (new \yii\db\Query())
                                    ->select(['*'])
                                    ->from(['tbl_procesos_satisfaccion_cliente'])
                                    ->orderBy(['nombre' => SORT_DESC])
                                    ->where(['=','anulado',0])          
                                    ->all(); 

        $form = Yii::$app->request->post();
        if ($model->load($form)) {
           // $txtidEquipo = $model->usua_id;
            $txtnombre = $model->id_proceso_satis;

            Yii::$app->db->createCommand()->insert('tbl_procesos_satisfaccion_cliente',[
                    'nombre' => $txtnombre,
                    'fechacreacion' => date("Y-m-d"),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
            ])->execute();

            return $this->redirect(['viewprocesossatisfaccion']);
        }

        return $this->render('viewprocesossatisfaccion',[
            'model' => $model,
            'varListarprocesos' => $varListarprocesos,
        ]);
    }

    public function actionViewindicadores(){
        $model = new IndicadorSatisfaccion();

        $varListarindicador= (new \yii\db\Query())
                                    ->select(['*'])
                                    ->from(['tbl_indicadores_satisfaccion_cliente'])
                                    ->orderBy(['nombre' => SORT_DESC])
                                    ->where(['=','anulado',0])          
                                    ->all(); 

        $form = Yii::$app->request->post();
        if ($model->load($form)) {
           // $txtidEquipo = $model->usua_id;
            $txtnombre = $model->nombre;

            Yii::$app->db->createCommand()->insert('tbl_indicadores_satisfaccion_cliente',[
                    'nombre' => $txtnombre,
                    'fechacreacion' => date("Y-m-d"),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
            ])->execute();

            return $this->redirect(['viewindicadores']);
        }

        return $this->render('viewindicadores',[
            'model' => $model,
            'varListarindicador' => $varListarindicador,
        ]);
    }

    public function actionDeleteteamparams($id){
        $paramsEliminar = [':IdControl'=>$id];          

        Yii::$app->db->createCommand('
              DELETE FROM tbl_equipo_parametros 
                WHERE 
                  idequipo_parametros = :IdControl')
            ->bindValues($paramsEliminar)
            ->execute();

        return $this->redirect(['parametrizarequipos']);
    }

    public function actionParametrizarasesores(){
        $model = new FormUploadtigo();        

        $varAsesoresTlm = (new \yii\db\Query())
                                    ->select(['*'])
                                    ->from(['tbl_evaluados'])
                                    ->where(['=','aliado',"TLM"])
                                    ->count();

        $varAsesoresAst = (new \yii\db\Query())
                                    ->select(['*'])
                                    ->from(['tbl_evaluados'])
                                    ->where(['=','aliado',"AST"])
                                    ->count();

        $varAsesoresKnt = (new \yii\db\Query())
                                    ->select(['*'])
                                    ->from(['tbl_evaluados'])
                                    ->where(['IS','aliado',null])
                                    ->count();

        if ($model->load(Yii::$app->request->post())) {
                
            $model->file = UploadedFile::getInstances($model, 'file');

            if ($model->file && $model->validate()) {
                    
                foreach ($model->file as $file) {
                    $fecha = date('Y-m-d-h-i-s');
                    $user = Yii::$app->user->identity->username;
                    $name = $fecha . '-' . $user;
                    $file->saveAs('categorias/' . $name . '.' . $file->extension);
                    $this->Importarasesores($name);

                    return $this->redirect(['parametrizarasesores']);
                }
            }
        }

        return $this->render('parametrizarasesores',[
            'model' => $model,
            'varAsesoresTlm' => $varAsesoresTlm,
            'varAsesoresAst' => $varAsesoresAst,
            'varAsesoresKnt' => $varAsesoresKnt,
        ]);
    }

    public function Importarasesores($name){
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

        for ($row = 3; $row <= $highestRow; $row++) { 
            $varUsuaioRed = $sheet->getCell("B".$row)->getValue();

            $varExisteAsesor = (new \yii\db\Query())
                                    ->select(['*'])
                                    ->from(['tbl_evaluados'])
                                    ->where(['=','dsusuario_red',$varUsuaioRed])
                                    ->count(); 

            if ($varExisteAsesor == "0") {

                $varAliadosExce = $sheet->getCell("E".$row)->getValue();
                if ($varAliadosExce == "KNT") {
                    $varAliados = null;
                }else{
                    $varAliados = $varAliadosExce;
                }

                Yii::$app->db->createCommand()->insert('tbl_evaluados',[
                    'name' => $sheet->getCell("A".$row)->getValue(),
                    'dsusuario_red' => $varUsuaioRed,
                    'identificacion' => $sheet->getCell("C".$row)->getValue(),
                    'email' => $sheet->getCell("D".$row)->getValue(),
                    'fechacreacion' => date("Y-m-d"),
                    'usua_id' => Yii::$app->user->identity->id,                    
                    'aliado' => $varAliados,
                ])->execute();

            }

        }

    }

    public function actionParametrizarpcrc(){
        $model = new ControlParams();

        $varListPcrcs = (new \yii\db\Query())
                                    ->select(['*'])
                                    ->from(['tbl_control_formularios'])
                                    ->where(['=','funciona',1])
                                    ->orderBy(['fecha_creacion' => SORT_DESC])
                                    ->all(); 

        $form = Yii::$app->request->post();
        if ($model->load($form)) {
            $varidArbol = $model->arbol_id;
            $varComentarios = $model->argumentos;

            Yii::$app->db->createCommand()->insert('tbl_control_formularios',[
                    'arbol_id' => $varidArbol,
                    'comentarios' => $varComentarios,
                    'fecha_creacion' => date("Y-m-d"),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
                    'funciona' => 1,
            ])->execute();

            return $this->redirect(['parametrizarpcrc']);
        }

        return $this->render('parametrizarpcrc',[
            'model' => $model,
            'varListPcrcs' => $varListPcrcs,
        ]);
    }

    public function actionDeletepcrcscontrol($id,$valor){
        $paramsEliminar = [':IdControl'=>$id];          

        Yii::$app->db->createCommand('
              DELETE FROM tbl_control_formularios 
                WHERE 
                  idcontrol_formularios = :IdControl')
            ->bindValues($paramsEliminar)
            ->execute();

        if ($valor == 1) {
            return $this->redirect(['parametrizarpcrc']);
        }else{
            return $this->redirect(['parametrizarfuncionapcrc']);
        }
    }

    public function actionParametrizarfuncionapcrc(){
        $model = new ControlParams();

        $varListfuncionPcrcs = (new \yii\db\Query())
                                    ->select(['*'])
                                    ->from(['tbl_control_formularios'])
                                    ->where(['=','funciona',2])
                                    ->orderBy(['fecha_creacion' => SORT_DESC])
                                    ->all(); 

        $form = Yii::$app->request->post();
        if ($model->load($form)) {
            $varidArbol = $model->arbol_id;
            $varComentarios = $model->argumentos;

            Yii::$app->db->createCommand()->insert('tbl_control_formularios',[
                    'arbol_id' => $varidArbol,
                    'comentarios' => $varComentarios,
                    'fecha_creacion' => date("Y-m-d"),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
                    'funciona' => 2,
            ])->execute();

            return $this->redirect(['parametrizarfuncionapcrc']);
        }

        return $this->render('parametrizarfuncionapcrc',[
            'varListfuncionPcrcs' => $varListfuncionPcrcs,
            'model' => $model,
        ]);
    }

    public function actionParametrizarresponsabilidad(){
        $model = new SpeechServicios();
        $varListarResponsabilidad = null;
        $varNombre = null;
        $varProcesos = 0;

        $form = Yii::$app->request->post();
        if ($model->load($form)) {
            $varidpcrc = $model->idllamada;
            
            $varNombre = (new \yii\db\Query())
                          ->select(['name'])
                          ->from(['tbl_arbols'])
                          ->where(['=','id',$varidpcrc])
                          ->scalar(); 

            $varListarResponsabilidad = (new \yii\db\Query())
                          ->select(['*'])
                          ->from(['tbl_responsabilidad_manual'])
                          ->where(['=','arbol_id',$varidpcrc])
                          ->andwhere(['=','anulado',0])
                          ->all();        

            if (count($varListarResponsabilidad) == 0) {
                $varProcesos = 0;
            }else{
                $varProcesos = 1;
            }
            
        }

        return $this->render('parametrizarresponsabilidad',[
            'model' => $model,
            'varListarResponsabilidad' => $varListarResponsabilidad,
            'varNombre' => $varNombre,
            'varProcesos' => $varProcesos,
        ]);
    }

    public function actionViewresponsabilidad(){
        $model = new SpeechServicios();

        $form = Yii::$app->request->post();
        if ($model->load($form)) {
            $varArbolId = $model->arbol_id;
            $varTipos = $model->nameArbol;
            $varResponsabilidad = $model->comentarios;

            Yii::$app->db->createCommand()->insert('tbl_responsabilidad_manual',[
                    'arbol_id' => $varArbolId,
                    'responsabilidad' => $varResponsabilidad,
                    'tipo' => $varTipos,
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
                    'fechacreacion' => date('Y-m-d'),
                ])->execute();

            return $this->redirect(['parametrizarresponsabilidad']);

        }

        return $this->renderAjax('viewresponsabilidad',[
            'model' => $model,
        ]);
    }

    public function actionGuardarclon(){
        $varidarbolescon = Yii::$app->request->get('txtvaridarbolescon');
        $varidarbolessin = Yii::$app->request->get('txtvaridarbolessin');

        $varListManual = (new \yii\db\Query())
                          ->select(['*'])
                          ->from(['tbl_responsabilidad'])
                          ->where(['=','arbol_id',$varidarbolescon])
                          ->all(); 

        if (count($varListManual) != 0) {
            $txtrta = 1;

            foreach ($varListManual as $key => $value) {

                Yii::$app->db->createCommand()->insert('tbl_responsabilidad_manual',[
                    'arbol_id' => $varidarbolessin,
                    'responsabilidad' => $value['nombre'],
                    'tipo' => $value['tipo'],
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
                    'fechacreacion' => date('Y-m-d'),
                ])->execute();

            }

        }else{
            $txtrta = 0;
        }
        

        die(json_encode($txtrta));
    }

    public function actionGuardarclonmanual(){
        $varidarbolescon = Yii::$app->request->get('txtvaridarbolescon');
        $varidarbolessin = Yii::$app->request->get('txtvaridarbolessin');

        $varListManual = (new \yii\db\Query())
                          ->select(['*'])
                          ->from(['tbl_responsabilidad_manual'])
                          ->where(['=','arbol_id',$varidarbolescon])
                          ->all(); 

        if (count($varListManual) != 0) {
            $txtrta = 1;

            foreach ($varListManual as $key => $value) {

                Yii::$app->db->createCommand()->insert('tbl_responsabilidad_manual',[
                    'arbol_id' => $varidarbolessin,
                    'responsabilidad' => $value['nombre'],
                    'tipo' => $value['tipo'],
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
                    'fechacreacion' => date('Y-m-d'),
                ])->execute();

            }

        }else{
            $txtrta = 0;
        }
        

        die(json_encode($txtrta));
    }

    public function actionSubirresponsabilidad(){
        $model = new FormUploadtigo();

        if ($model->load(Yii::$app->request->post())){
            $model->file = UploadedFile::getInstances($model, 'file');

            if ($model->file && $model->validate()) {
                foreach ($model->file as $file) {
                    $fecha = date('Y-m-d-h-i-s');
                    $user = Yii::$app->user->identity->username;
                    $name = $fecha . '-' . $user;
                    $file->saveAs('categorias/' . $name . '.' . $file->extension);
                    $this->Importresponsabilidades($name);

                    return $this->redirect(['parametrizarresponsabilidad']);
                }
            }
        }

        return $this->renderAjax('subirresponsabilidad',[
            'model' => $model,
        ]);
    }

    public function Importresponsabilidades($name){
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

        for ($row = 2; $row <= $highestRow; $row++) { 
            
            if ($sheet->getCell("A".$row)->getValue() != null) {

                Yii::$app->db->createCommand()->insert('tbl_responsabilidad_manual',[
                    'arbol_id' => $sheet->getCell("A".$row)->getValue(),
                    'responsabilidad' => $sheet->getCell("B".$row)->getValue(),
                    'tipo' => $sheet->getCell("C".$row)->getValue(),
                    'fechacreacion' => date("Y-m-d"),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
                ])->execute();
                
                 
            }

        }

    }

    public function actionDeleteresponsabilidad($id){
        $paramsEliminar = [':IdControl'=>$id];          

        Yii::$app->db->createCommand('
              DELETE FROM tbl_responsabilidad_manual 
                WHERE 
                  id_responsabilidad = :IdControl')
            ->bindValues($paramsEliminar)
            ->execute();

        
        return $this->redirect(['parametrizarresponsabilidad']);
        
    }

    public function actionAdminmensajes(){
        $model = new Encuestaspersonalsatu();       

        $varDataList = (new \yii\db\Query())
                        ->select(['COUNT(documentopersonalsatu) AS conteo', 'fechacreacion as fecha'])
                        ->from(['tbl_encuestas_logsenvios'])            
                        ->groupby(['fechacreacion'])
                        ->All();

        $varUltimaFecha = (new \yii\db\Query())
                        ->select(['MAX(fechacreacion) AS maximo'])
                        ->from(['tbl_encuestas_logsenvios']) 
                        ->Scalar();

        $varConteoRegistrados = (new \yii\db\Query())
                        ->select(['*'])
                        ->from(['tbl_encuestas_personalsatu']) 
                        ->count();

        $varConteoNoResgitrados = (new \yii\db\Query())
                        ->select(['*'])
                        ->from(['tbl_encuestas_logsnoenvios']) 
                        ->count();

        $form = Yii::$app->request->post();
        if ($model->load($form)) {

            $paramsBusqueda = [':varPosicionDirector' => '21',':varPosicionGerente' => '24'];

            $varDataJarvis = Yii::$app->dbjarvis->createCommand('
                SELECT
                    dp_posicion.id_dp_posicion, dp_posicion.posicion, dp_datos_generales.nombre_completo, dp_actualizacion_datos.documento, dp_actualizacion_datos.email_corporativo, dp_actualizacion_datos.celular1
                    FROM dp_datos_generales
                        LEFT JOIN dp_actualizacion_datos ON 
                            dp_datos_generales.documento = dp_actualizacion_datos.documento
                        LEFT JOIN dp_distribucion_personal ON 
                            dp_actualizacion_datos.documento = dp_distribucion_personal.documento
                        LEFT JOIN dp_cargos ON 
                            dp_cargos.id_dp_cargos = dp_distribucion_personal.id_dp_cargos
                        LEFT JOIN dp_posicion ON 
                            dp_cargos.id_dp_posicion = dp_posicion.id_dp_posicion                    
                        LEFT JOIN dp_estados ON 
                            dp_estados.id_dp_estados = dp_distribucion_personal.id_dp_estados
                    WHERE
                        dp_distribucion_personal.fecha_actual >= DATE_FORMAT(NOW() ,"%Y-%m-01")
                            AND dp_posicion.id_dp_posicion IN (:varPosicionDirector,:varPosicionGerente)
                GROUP BY dp_distribucion_personal.documento
            ')->bindValues($paramsBusqueda)->queryAll();

            $id = 1;

            Yii::$app->db->createCommand('DELETE FROM tbl_encuestas_personalsatu WHERE distribucion=:id')->bindParam(':id',$id)->execute();

            foreach ($varDataJarvis as $key => $value) {

                $usua_idS = (new \yii\db\Query())
                        ->select(['usua_id'])
                        ->from(['tbl_usuarios'])            
                        ->where(['=','usua_identificacion',$value['documento']])
                        ->groupby(['usua_id'])
                        ->Scalar();


                Yii::$app->db->createCommand()->insert('tbl_encuestas_personalsatu',[
                      'id_dp_posicion' => $value['id_dp_posicion'],
                      'posicion' => $value['posicion'],
                      'personalsatu' => $value['nombre_completo'],  
                      'documentopersonalsatu' => $value['documento'],
                      'correosatu' => $value['email_corporativo'],
                      'movilsatu' => $value['celular1'],
                      'usua_id_satu' => $usua_idS,
                      'fechacreacion' => date('Y-m-d'),
                      'anulado' => 0,
                      'usua_id' => Yii::$app->user->identity->id,  
                      'distribucion' => 1,                                     
                  ])->execute();
            }

            return $this->redirect('adminmensajes');
            
        }

        return $this->render('adminmensajes',[
            'model' => $model,
            'varDataList' => $varDataList,
            'varUltimaFecha' => $varUltimaFecha,
            'varConteoRegistrados' => $varConteoRegistrados,
            'varConteoNoResgitrados' => $varConteoNoResgitrados,
        ]);
    }

    public function actionAddpersonal(){
        $model = new Encuestaspersonalsatu();

        $form = Yii::$app->request->post();
        if ($model->load($form)) {
            $varPosicionesId = $model->id_dp_posicion;
            if ($varPosicionesId == '21') {
                $varPosiciones = "Director";
            }
            if ($varPosicionesId == '24') {
                $varPosiciones = "Gerente";
            }
            $varNombrePersonal = $model->personalsatu;
            $varDocumentoPersonal = $model->documentopersonalsatu;

            $varUsuaid = (new \yii\db\Query())
                        ->select(['usua_id'])
                        ->from(['tbl_usuarios'])            
                        ->where(['=','usua_identificacion',$varDocumentoPersonal])
                        ->groupby(['usua_id'])
                        ->Scalar();

            $varCorreo = $model->correosatu;

            Yii::$app->db->createCommand()->insert('tbl_encuestas_personalsatu',[
                      'id_dp_posicion' => $varPosicionesId,
                      'posicion' => $varPosiciones,
                      'personalsatu' => $varNombrePersonal,  
                      'documentopersonalsatu' => $varDocumentoPersonal,
                      'correosatu' => $varCorreo,
                      'movilsatu' => null,
                      'usua_id_satu' => $varUsuaid,
                      'fechacreacion' => date('Y-m-d'),
                      'anulado' => 0,
                      'usua_id' => Yii::$app->user->identity->id,  
                      'distribucion' => 2,                                     
                  ])->execute();

            return $this->redirect('adminmensajes');

        }

        return $this->render('addpersonal',[
            'model' => $model,
        ]);
    }

    public function actionListarnombres(){
        $txtidposicion = Yii::$app->request->get('id');

          if ($txtidposicion) {
            $txtControl = (new \yii\db\Query())
                        ->select(['personalsatu'])
                        ->from(['tbl_encuestas_personalsatu'])            
                        ->where(['=','id_dp_posicion',$txtidposicion])
                        ->count();

            if ($txtControl > 0) {
              $varListaLideresx = (new \yii\db\Query())
                        ->select(['id_personalsatu','personalsatu'])
                        ->from(['tbl_encuestas_personalsatu'])            
                        ->where(['=','id_dp_posicion',$txtidposicion])
                        ->all();

              echo "<option value='' disabled selected>Seleccionar...</option>";
              foreach ($varListaLideresx as $key => $value) {
                echo "<option value='" . $value['id_personalsatu']. "'>" . $value['personalsatu']. "</option>";
              }
            }else{
              echo "<option>--</option>";
            }
          }else{
            echo "<option>Seleccionar...</option>";
          }          
    }

    public function actionSendalerts(){
        $model = new Encuestaspersonalsatu();

        $form = Yii::$app->request->post();
        if ($model->load($form)) {
            $varIdPersonal = $model->personalsatu;

            $varObtenerCorreo = (new \yii\db\Query())
                                ->select(['correosatu'])
                                ->from(['tbl_encuestas_personalsatu'])            
                                ->where(['=','id_personalsatu',$varIdPersonal])
                                ->scalar();

            if ($varObtenerCorreo != null) {

                $varObtenerDocumento = (new \yii\db\Query())
                                ->select(['documentopersonalsatu'])
                                ->from(['tbl_encuestas_personalsatu'])            
                                ->where(['=','id_personalsatu',$varIdPersonal])
                                ->scalar();

                $varObtenerUsua = (new \yii\db\Query())
                                ->select(['usua_id_satu'])
                                ->from(['tbl_encuestas_personalsatu'])            
                                ->where(['=','id_personalsatu',$varIdPersonal])
                                ->scalar();

                Yii::$app->db->createCommand()->insert('tbl_encuestas_logsenvios',[
                    'documentopersonalsatu' => $varObtenerDocumento,
                    'correosatu' => $varObtenerCorreo,
                    'usua_id_satu' => $varObtenerUsua,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                      
                ])->execute();

                $tmpFile = "images/Alertas_Satu.jpg";
                
                $message = "<html><body>";
                $message .= "<h3>CX-MANAGEMENT</h3>";                
                $message .= "</body></html>";

                Yii::$app->mailer->compose()
                    ->setTo($varObtenerCorreo)
                    ->setFrom(Yii::$app->params['email_satu_from'])
                    ->setSubject("Informe Encuestas de Satisfacción - CX-MANAGEMENT")                    
                    ->attach($tmpFile)
                    ->setHtmlBody($message)
                    ->send();

                return $this->redirect('adminmensajes');

            }

        }

        return $this->render('sendalerts',[
            'model' => $model,
        ]);
    }

    public function actionEnviomasivo(){

        $varListaCorreos = (new \yii\db\Query())
                                ->select(['*'])
                                ->from(['tbl_encuestas_personalsatu'])            
                                ->where(['=','anulado',0])
                                ->all();

        foreach ($varListaCorreos as $key => $value) {
            $varObtenerCorreoMasivo = $value['correosatu'];
            
            Yii::$app->db->createCommand()->insert('tbl_encuestas_logsenvios',[
                    'documentopersonalsatu' => $value[''],
                    'correosatu' => $varObtenerCorreoMasivo,
                    'usua_id_satu' => $value['usua_id_satu'],
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                      
            ])->execute();

            $tmpFile = "images/Alertas_Satu.jpg";

            $message = "<html><body>";
            $message .= "<h3>CX-MANAGEMENT</h3>";
            $message .= "</body></html>";

            Yii::$app->mailer->compose()
                    ->setTo($varObtenerCorreoMasivo)
                    ->setFrom(Yii::$app->params['email_satu_from'])
                    ->setSubject("Informe Encuestas de Satisfacción - CX-MANAGEMENT")
                    ->attach($tmpFile)
                    ->setHtmlBody($message)
                    ->send();

        }


        return $this->redirect('adminmensajes');
    }

    public function actionSendalertsmore(){
        $model = new FormUploadtigo();

        if ($model->load(Yii::$app->request->post())) {
                
            $model->file = UploadedFile::getInstances($model, 'file');

            if ($model->file && $model->validate()) {
                    
                foreach ($model->file as $file) {
                    $fecha = date('Y-m-d-h-i-s');
                    $user = Yii::$app->user->identity->username;
                    $name = $fecha . '-' . $user;
                    $file->saveAs('categorias/' . $name . '.' . $file->extension);
                    $this->Enviomasivos($name);

                    return $this->redirect(['adminmensajes']);
                }
            }
        }

        return $this->render('sendalertsmore',[
            'model' => $model,
        ]);
    }

    public function Enviomasivos($name){
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

        for ($row = 2; $row <= $highestRow; $row++) { 
            $varObtenerDocumento = $sheet->getCell("A".$row)->getValue();

            $varExisteCorreo = (new \yii\db\Query())
                                ->select(['correosatu'])
                                ->from(['tbl_encuestas_personalsatu'])            
                                ->where(['=','documentopersonalsatu',$varObtenerDocumento])
                                ->scalar();

            if ($varExisteCorreo != '') {

                $varObtenerUsua = (new \yii\db\Query())
                                ->select(['usua_id_satu'])
                                ->from(['tbl_encuestas_personalsatu'])            
                                ->where(['=','documentopersonalsatu',$varObtenerDocumento])
                                ->scalar();

                $message = "<html><body>";
                $message .= "<h3>CX-MANAGEMENT</h3>";
                $message .= "</body></html>";

                Yii::$app->mailer->compose()
                    ->setTo($varExisteCorreo)
                    ->setFrom(Yii::$app->params['email_satu_from'])
                    ->setSubject("Informe Encuestas de Satisfacción - CX-MANAGEMENT")                    
                    ->setHtmlBody($message)
                    ->send();

                Yii::$app->db->createCommand()->insert('tbl_encuestas_logsenvios',[
                    'documentopersonalsatu' => $varObtenerDocumento,
                    'correosatu' => $varExisteCorreo,
                    'usua_id_satu' => $varObtenerUsua,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                      
                ])->execute();

            }else{

                Yii::$app->db->createCommand()->insert('tbl_encuestas_logsnoenvios',[
                    'documentopersonalsatu' => $varObtenerDocumento,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                      
                ])->execute();

            }

        }

    }

    public function actionEnviarreportes(){
        $model = new Encuestaspersonalsatu();

        $varDataListReport = (new \yii\db\Query())
                                ->select(['*'])
                                ->from(['tbl_encuestas_logsenvios'])            
                                ->All();

        $form = Yii::$app->request->post();
        if ($model->load($form)) {
            $varFechas = explode(" ", $model->fechacreacion);

            $varFechaInicio = $varFechas[0];;
            $varFechaFin = date('Y-m-d',strtotime($varFechas[2]));;

            $varDataListReport = (new \yii\db\Query())
                                ->select(['*'])
                                ->from(['tbl_encuestas_logsenvios'])            
                                ->where(['between','fechacreacion',$varFechaInicio,$varFechaFin])
                                ->All();
        }

        return $this->render('enviarreportes',[
            'model' => $model,
            'varDataListReport' => $varDataListReport,
        ]);
    }

    public function actionEnviarnoreportes(){
        $model = new Encuestaspersonalsatu();

        $varDataListNoReport = (new \yii\db\Query())
                                ->select(['*'])
                                ->from(['tbl_encuestas_logsnoenvios'])            
                                ->All();

        $form = Yii::$app->request->post();
        if ($model->load($form)) {
            $varFechas = explode(" ", $model->fechacreacion);

            $varFechaInicio = $varFechas[0];;
            $varFechaFin = date('Y-m-d',strtotime($varFechas[2]));;

            $varDataListNoReport = (new \yii\db\Query())
                                ->select(['*'])
                                ->from(['tbl_encuestas_logsnoenvios'])            
                                ->where(['between','fechacreacion',$varFechaInicio,$varFechaFin])
                                ->All();
        }

        return $this->render('enviarnoreportes',[
            'model' => $model,
            'varDataListNoReport' => $varDataListNoReport,
        ]);
    }

    public function actionAdminpcrc(){
        $model = new Procesoclientecentroscosto();

        $varFechaActualizada = (new \yii\db\Query())
                                ->select(['MAX(tbl_proceso_cliente_centrocosto.fechacreacion)'])
                                ->from(['tbl_proceso_cliente_centrocosto'])   
                                ->Scalar();

        $varDataPcrc = (new \yii\db\Query())
                        ->select([
                            'if(tbl_proceso_cliente_centrocosto.estado=1,"Activo","No Activo") AS estado',
                            'count(tbl_proceso_cliente_centrocosto.estado) AS cantidad'
                        ])
                        ->from(['tbl_proceso_cliente_centrocosto'])  
                        ->groupby(['tbl_proceso_cliente_centrocosto.estado']) 
                        ->All();

        $form = Yii::$app->request->post();
        if ($model->load($form)) {
            $varIdCliente = $model->cliente;

            return $this->redirect(array('procesopcrc','iddpclientes'=>$varIdCliente));
        }

        return $this->render('adminpcrc',[
            'model' => $model,
            'varFechaActualizada' => $varFechaActualizada,
            'varDataPcrc' => $varDataPcrc,
        ]);
    }

    public function actionProcesopcrc($iddpclientes){
        $model = new Procesoclientecentroscosto();

        $varNombreCliente = (new \yii\db\Query())
                                ->select(['tbl_proceso_cliente_centrocosto.cliente'])
                                ->from(['tbl_proceso_cliente_centrocosto'])            
                                ->where(['=','tbl_proceso_cliente_centrocosto.id_dp_clientes',$iddpclientes])
                                ->groupby(['tbl_proceso_cliente_centrocosto.cliente'])
                                ->Scalar();

        $varListaPcrc = (new \yii\db\Query())
                                ->select([
                                    'tbl_proceso_cliente_centrocosto.idvolumendirector',
                                    'tbl_proceso_cliente_centrocosto.pcrc', 
                                    'tbl_proceso_cliente_centrocosto.cod_pcrc',
                                    'if(tbl_proceso_cliente_centrocosto.estado=1,"Activo","No activo") AS estado'])
                                ->from(['tbl_proceso_cliente_centrocosto'])            
                                ->where(['=','tbl_proceso_cliente_centrocosto.id_dp_clientes',$iddpclientes])
                                ->All();

        return $this->render('procesopcrc',[
            'model' => $model,
            'iddpclientes' => $iddpclientes,
            'varNombreCliente' => $varNombreCliente,
            'varListaPcrc' => $varListaPcrc,
        ]);
    }

    public function actionAnularpcrc($id,$iddpclientes){

        Yii::$app->db->createCommand('
                            UPDATE tbl_proceso_cliente_centrocosto 
                                SET estado = :varEstado
                                    WHERE 
                                        idvolumendirector = :VarId')
                            ->bindValue(':VarId', $id)
                            ->bindValue(':varEstado', 0)
                            ->execute();

        return $this->redirect(array('procesopcrc','iddpclientes'=>$iddpclientes));
    }

    public function actionActivarpcrc($id,$iddpclientes){

        Yii::$app->db->createCommand('
                            UPDATE tbl_proceso_cliente_centrocosto 
                                SET estado = :varEstado
                                    WHERE 
                                        idvolumendirector = :VarId')
                            ->bindValue(':VarId', $id)
                            ->bindValue(':varEstado', 1)
                            ->execute();

        return $this->redirect(array('procesopcrc','iddpclientes'=>$iddpclientes));
    }

    public function actionActualizapcrccliente($idpcrc){
        $sessiones = Yii::$app->user->identity->id;
        $txtanulado = 0;
        $txtfechacreacion = date('Y-m-d');

        // Se borra procesos de la tabla del servicioen especifico
        Yii::$app->db->createCommand('DELETE FROM tbl_proceso_cliente_centrocosto WHERE id_dp_clientes=:id')->bindParam(':id',$idpcrc)->execute();

        Yii::$app->db->createCommand('DELETE FROM tbl_procesos_volumendirector WHERE id_dp_clientes=:id AND modificar != 1')->bindParam(':id',$idpcrc)->execute();

        // Procesos para buscar e ingresar procesos del servicio nuevo
        $paramsBuscarCliente = [':varAnulado'=>0,':varDocDirOne'=>'111111111',':varDocDirTwo'=>'111111112',':varCliente'=>$idpcrc];

        $varQuery =  Yii::$app->dbjarvis->createCommand('
                SELECT
                    dp_centros_costos.ciudad, 
                    dp_centros_costos.director_programa, dp_centros_costos.documento_director,
                    dp_centros_costos.gerente_cuenta, dp_centros_costos.documento_gerente, 
                    dp_clientes.id_dp_clientes, dp_clientes.cliente, 
                    dp_centros_costos.id_dp_centros_costos, 
                    dp_centros_costos.centros_costos, dp_centros_costos.estado, dp_pcrc.cod_pcrc, 
                    dp_pcrc.pcrc 
                FROM dp_centros_costos

                    INNER JOIN dp_clientes ON
                        dp_centros_costos.id_dp_clientes = dp_clientes.id_dp_clientes
                    INNER JOIN dp_pcrc ON
                        dp_centros_costos.id_dp_centros_costos = dp_pcrc.id_dp_centros_costos
                WHERE 
                    dp_centros_costos.documento_director NOT LIKE :varAnulado
                        AND dp_centros_costos.documento_director NOT LIKE :varDocDirOne
                            AND dp_centros_costos.documento_director NOT LIKE :varDocDirTwo 
                                AND dp_clientes.id_dp_clientes = :varCliente')->bindValues($paramsBuscarCliente)->queryAll();

        foreach ($varQuery as $key => $value) {
            Yii::$app->db->createCommand()->insert('tbl_proceso_cliente_centrocosto',[
                        'ciudad' => $value['ciudad'],
                        'director_programa' => $value['director_programa'],
                        'documento_director' => $value['documento_director'],
                        'gerente_cuenta' => $value['gerente_cuenta'],
                        'documento_gerente' => $value['documento_gerente'],
                        'id_dp_clientes' => $value['id_dp_clientes'],
                        'cliente' => $value['cliente'],
                        'id_dp_centros_costos' => $value['id_dp_centros_costos'],
                        'centros_costos' => $value['centros_costos'],
                        'cod_pcrc' => $value['cod_pcrc'],
                        'pcrc' => $value['pcrc'],
                        'estado' => $value['estado'],
                        'anulado' => $txtanulado,
                        'fechacreacion' => $txtfechacreacion,
                        'feachamodificacion' => null,
                        'usua_id' => $sessiones,
            ])->execute();

            Yii::$app->db->createCommand()->insert('tbl_procesos_volumendirector',[
                'ciudad' => $value['ciudad'],
                'director_programa' => $value['director_programa'],
                'documento_director' => $value['documento_director'],
                'id_dp_clientes' => $value['id_dp_clientes'],
                'cliente' => $value['cliente'],
                'id_dp_centros_costos' => $value['id_dp_centros_costos'],
                'centros_costos' => $value['centros_costos'],
                'cod_pcrc' => $value['cod_pcrc'],
                'pcrc' => $value['pcrc'],
                'estado' => $value['estado'],
                'anulado' => $txtanulado,
                'fechacreacion' => $txtfechacreacion,
                'feachamodificacion' => null,
                'usua_id' => $sessiones,
            ])->execute();

        }

        $varListarDirectores = (new \yii\db\Query())
                                ->select(['id_dp_clientes', 'documento_director'])
                                ->from(['tbl_proceso_cliente_centrocosto'])
                                ->where(['=','anulado',0])
                                ->andwhere(['=','estado',1])
                                ->groupby(['id_dp_clientes'])
                                ->all();


        foreach ($varListarDirectores as $key => $value) {
            $varClienteiddp = $value['id_dp_clientes'];
            $varCCDirector = $value['documento_director'];

            $varActualizarDirectores = (new \yii\db\Query())
                                ->select([
                                    'tbl_hojavida_datadirector.hv_idpersonal',
                                    'tbl_hojavida_datadirector.hv_iddirector'
                                ])
                                ->from(['tbl_hojavida_datadirector'])
                                ->join('INNER JOIN', 'tbl_hojavida_datapcrc', 
                                      'tbl_hojavida_datadirector.hv_idpersonal = tbl_hojavida_datapcrc.hv_idpersonal')
                                ->where(['=','tbl_hojavida_datadirector.anulado',0])
                                ->andwhere(['=','tbl_hojavida_datapcrc.anulado',0])
                                ->andwhere(['=','tbl_hojavida_datapcrc.id_dp_cliente',$varClienteiddp])
                                ->groupby(['tbl_hojavida_datadirector.hv_iddirector'])
                                ->all();

            foreach ($varActualizarDirectores as $key => $value) {
                $varIdDataDirector = $value['hv_iddirector'];

                Yii::$app->db->createCommand()->update('tbl_hojavida_datadirector',[
                                            'ccdirector' => $varCCDirector,
                                        ],'hv_iddirector ='.$varIdDataDirector.'')->execute();
            }
        }

        $varActualizarGerentes = (new \yii\db\Query())
                                ->select([
                                    'tbl_hojavida_datapcrc.id_dp_cliente', 
                                    'tbl_hojavida_datagerente.hv_idgerente', 
                                    'tbl_hojavida_datagerente.hv_idpersonal', 
                                    'tbl_hojavida_datagerente.ccgerente'
                                ])
                                ->from(['tbl_hojavida_datapcrc'])
                                ->join('INNER JOIN', 'tbl_hojavida_datagerente', 
                                      'tbl_hojavida_datapcrc.hv_idpersonal = tbl_hojavida_datagerente.hv_idpersonal')
                                ->where(['=','tbl_hojavida_datagerente.anulado',0])
                                ->andwhere(['=','tbl_hojavida_datapcrc.anulado',0])
                                ->groupby(['tbl_hojavida_datagerente.hv_idgerente'])
                                ->all();

        foreach ($varActualizarGerentes as $key => $value) {
            $varIdHvGerente = $value['hv_idgerente'];
            $varConteoExisteGerente = (new \yii\db\Query())
                                ->select([
                                    'tbl_proceso_cliente_centrocosto.documento_gerente'
                                ])
                                ->from(['tbl_proceso_cliente_centrocosto'])   
                                ->where(['=','tbl_proceso_cliente_centrocosto.anulado',0])
                                ->andwhere(['=','tbl_proceso_cliente_centrocosto.estado',1])
                                ->andwhere(['=','tbl_proceso_cliente_centrocosto.id_dp_clientes',$value['id_dp_cliente']])
                                ->andwhere(['=','tbl_proceso_cliente_centrocosto.documento_gerente',$value['ccgerente']])
                                ->count();

            if ($varConteoExisteGerente == 0) {
                $varCcGerenteNuevo = (new \yii\db\Query())
                                ->select([
                                    'tbl_proceso_cliente_centrocosto.documento_gerente'
                                ])
                                ->from(['tbl_proceso_cliente_centrocosto'])   
                                ->where(['=','tbl_proceso_cliente_centrocosto.anulado',0])
                                ->andwhere(['=','tbl_proceso_cliente_centrocosto.estado',1])
                                ->andwhere(['=','tbl_proceso_cliente_centrocosto.id_dp_clientes',$value['id_dp_cliente']])
                                ->groupby(['tbl_proceso_cliente_centrocosto.id_dp_clientes'])
                                ->scalar();

                Yii::$app->db->createCommand()->update('tbl_hojavida_datagerente',[
                                            'ccgerente' => $varCcGerenteNuevo,
                                        ],'hv_idgerente ='.$varIdHvGerente.'')->execute();
            }
        }


        return $this->redirect(array('procesopcrc','iddpclientes'=>$idpcrc));
    }

    public function actionActualizapcrc(){
        $model = new Procesoclientecentroscosto();

        $sessiones = Yii::$app->user->identity->id;
        $txtanulado = 0;
        $txtfechacreacion = date('Y-m-d');

        // Se ejecuta primero un delete sobre la tabla de procesos centros de costos
        Yii::$app->db->createCommand()->truncateTable('tbl_proceso_cliente_centrocosto')->execute();

        // Se ejecuta en segundo plano la actualizacion de la data de Jarvis

        $paramsBuscar = [':varAnulado'=>0,':varDocDirOne'=>'111111111',':varDocDirTwo'=>'111111112'];

        $varQuery = Yii::$app->dbjarvis->createCommand('
                SELECT
                    dp_centros_costos.ciudad, 
                    dp_centros_costos.director_programa, dp_centros_costos.documento_director,
                    dp_centros_costos.gerente_cuenta, dp_centros_costos.documento_gerente, 
                    dp_clientes.id_dp_clientes, dp_clientes.cliente, 
                    dp_centros_costos.id_dp_centros_costos, 
                    dp_centros_costos.centros_costos, dp_centros_costos.estado, dp_pcrc.cod_pcrc, 
                    dp_pcrc.pcrc 
                FROM dp_centros_costos

                    INNER JOIN dp_clientes ON
                        dp_centros_costos.id_dp_clientes = dp_clientes.id_dp_clientes
                    INNER JOIN dp_pcrc ON
                        dp_centros_costos.id_dp_centros_costos = dp_pcrc.id_dp_centros_costos
                WHERE 
                    dp_centros_costos.documento_director NOT LIKE :varAnulado
                        AND dp_centros_costos.documento_director NOT LIKE :varDocDirOne
                            AND dp_centros_costos.documento_director NOT LIKE :varDocDirTwo ')->bindValues($paramsBuscar)->queryAll();

        foreach ($varQuery as $key => $value) {
            Yii::$app->db->createCommand()->insert('tbl_proceso_cliente_centrocosto',[
                        'ciudad' => $value['ciudad'],
                        'director_programa' => $value['director_programa'],
                        'documento_director' => $value['documento_director'],
                        'gerente_cuenta' => $value['gerente_cuenta'],
                        'documento_gerente' => $value['documento_gerente'],
                        'id_dp_clientes' => $value['id_dp_clientes'],
                        'cliente' => $value['cliente'],
                        'id_dp_centros_costos' => $value['id_dp_centros_costos'],
                        'centros_costos' => $value['centros_costos'],
                        'cod_pcrc' => $value['cod_pcrc'],
                        'pcrc' => $value['pcrc'],
                        'estado' => $value['estado'],
                        'anulado' => $txtanulado,
                        'fechacreacion' => $txtfechacreacion,
                        'feachamodificacion' => null,
                        'usua_id' => $sessiones,
            ])->execute();

        }

        return $this->redirect('index',['model'=>$model]);

    }

    public function actionAdmingenesys(){
        $varCantidadAsesores = (new \yii\db\Query())
                                ->select(['*'])
                                ->from(['tbl_genesys_parametroasesor'])
                                ->where(['=','tbl_genesys_parametroasesor.anulado',0])
                                ->count();

        $varCantidadArbol = (new \yii\db\Query())
                                ->select(['*'])
                                ->from(['tbl_genesys_formularios'])
                                ->where(['=','tbl_genesys_formularios.anulado',0])
                                ->count();

        return $this->render('admingenesys',[
            'varCantidadAsesores' => $varCantidadAsesores,
            'varCantidadArbol' => $varCantidadArbol,
        ]);
    }

    public function actionActualizaasesor(){

        ob_start();
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYPEER=> false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_URL => 'https://login.mypurecloud.com/oauth/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic YWI0ODEyMGYtMWMyYi00NDAxLTkzMzktYjFhM2JlMmYxY2UyOkNtX2loUDF5VE9oWTI3Sjl4ZmhReHJua2F0djQtUnB6bHpQLW1DdVQ5eEk=',
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
            
        ob_clean();

        if (!$response) {
            die(json_encode(array('status' => '0','data'=>'Error al buscar la transcripcion')));
        }

        // $response = json_decode(iconv( "Windows-1252", "UTF-8", $response ),true);

        if (count($response) == 0) {
            die(json_encode(array('status' => '0','data'=>'Transcripcion no encontrada'))); 
        }

        $varProcesosTokenUno = explode(",", $response);
      
        $varProcesosTokenDos = explode(":",$varProcesosTokenUno[0]);
        $varrespuesta = str_replace('"', '', $varProcesosTokenDos[1]);        

        if ($varrespuesta != "") {

            $this->Obtenerasesoresgenesys($varrespuesta);
          
        }        
        
        return $this->render('admingenesys');
    }

    public function Obtenerasesoresgenesys($varrespuesta){
        $usua_id = Yii::$app->user->identity->id; 

        ob_start();

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYPEER=> false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_URL => 'https://api.mypurecloud.com/api/v2/users?pageSize=100&pageNumber=1',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer '.$varrespuesta.''
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
            
        ob_clean();

        if (!$response) {
            die(json_encode(array('status' => '0','data'=>'Error al buscar la transcripcion')));
        }

        $response = json_decode(iconv( "Windows-1252", "UTF-8//IGNORE", $response ),true);

        if (count($response) == 0) {
            die(json_encode(array('status' => '0','data'=>'Transcripcion no encontrada'))); 
        }

        foreach ($response['entities'] as $key => $value) {
            
            if (strlen(strstr($value['name'], 'Agente')) == 0) {
                    
                if ($value['division']['name'] == 'Konecta') {
                    
                    $varGenesysCcAsesorHojaUno = $value['title'];

                    if (is_numeric($varGenesysCcAsesorHojaUno)) {

                        $varComprobacionGenesysHojaUno = (new \yii\db\Query())
                                ->select(['tbl_genesys_parametroasesor.id_genesys'])
                                ->from(['tbl_genesys_parametroasesor'])
                                ->where(['=','tbl_genesys_parametroasesor.anulado',0])
                                ->andwhere(['=','tbl_genesys_parametroasesor.documento_asesor',$varGenesysCcAsesorHojaUno])
                                ->count();

                        if ($varComprobacionGenesysHojaUno == 0) {
                            Yii::$app->db->createCommand()->insert('tbl_genesys_parametroasesor',[
                                      'id_genesys' => $value['id'],
                                      'nombre_asesor' => $value['name'],
                                      'documento_asesor' => $value['title'],
                                      'username_asesor' => $value['username'],
                                      'selfUri' => $value['selfUri'], 
                                      'usua_id' => $usua_id,
                                      'fechacreacion' => date('Y-m-d'),
                                      'anulado' => 0,                         
                            ])->execute();
                        }
                    }                    
                
                }

            }

        }

        for ($i=2; $i <= $response['pageCount']; $i++) { 
            
            ob_start();

            $curltwo = curl_init();

            curl_setopt_array($curltwo, array(
                CURLOPT_SSL_VERIFYPEER=> false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_URL => 'https://api.mypurecloud.com/api/v2/users?pageSize=100&pageNumber='.$i.'',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Bearer '.$varrespuesta.''
                ),
            ));

            $responsetwo = curl_exec($curltwo);

            curl_close($curltwo);
                
            ob_clean();

            if (!$responsetwo) {
                die(json_encode(array('status' => '0','data'=>'Error al buscar la transcripcion')));
            }

            $responsetwo = json_decode(iconv( "Windows-1252", "UTF-8//IGNORE", $responsetwo ),true);

            if (count($responsetwo) == 0) {
                die(json_encode(array('status' => '0','data'=>'Transcripcion no encontrada'))); 
            }

            foreach ($responsetwo['entities'] as $key => $value) {
                                
                if (strlen(strstr($value['name'], 'Agente')) == 0) {
                    
                    if ($value['division']['name'] == 'Konecta') {

                        if ($value['id'] != 'f0c5579d-e761-48d3-8ae9-37e7b7237217') {

                            if ($value['id'] != '383ad4d5-c66d-4bc8-a9cc-41802fe064ca') {
                                
                                $usua_id = Yii::$app->user->identity->id;  
                                

                                $varGenesysCcAsesor = $value['title'];

                                if (is_numeric($varGenesysCcAsesor)) {

                                    $varComprobacionGenesys = (new \yii\db\Query())
                                        ->select(['tbl_genesys_parametroasesor.id_genesys'])
                                        ->from(['tbl_genesys_parametroasesor'])
                                        ->where(['=','tbl_genesys_parametroasesor.anulado',0])
                                        ->andwhere(['=','tbl_genesys_parametroasesor.documento_asesor',$varGenesysCcAsesor])
                                        ->count();

                                    if ($varComprobacionGenesys == 0) {
                                        Yii::$app->db->createCommand()->insert('tbl_genesys_parametroasesor',[
                                              'id_genesys' => $value['id'],
                                              'nombre_asesor' => $value['name'],
                                              'documento_asesor' => $value['title'],
                                              'username_asesor' => $value['username'],
                                              'selfUri' => $value['selfUri'], 
                                              'usua_id' => $usua_id,
                                              'fechacreacion' => date('Y-m-d'),
                                              'anulado' => 0,                         
                                        ])->execute();
                                    }
                                    
                                } 
                            }
                              
                        }                    

                    }

                }
                
            }

            die(json_encode("Aqui vamos"));

        }

    }

    public function actionGbuscarporasesor(){
        $model = new Evaluados();
        $usua_id = Yii::$app->user->identity->id; 
        $varDataList = null;
        $varMensaje = 0;
        $varTmpEvaluadoId = null;

        $form = Yii::$app->request->post();
        if ($model->load($form)) {
            $varDocumentoAsesor = $model->identificacion;
            $varFechas = explode(" ", $model->fechacreacion);

            $varFechasInicio = $varFechas[0];
            $varFechasFin = date('Y-m-d',strtotime($varFechas[2]."+ 1 days")); 

            $varContarDias = (strtotime($varFechasInicio)-strtotime($varFechasFin))/86400;
            $varContarDias = abs($varContarDias); $varContarDias = floor($varContarDias);
            
            if ($varContarDias <= 7) {

                $varFechasFin = date('Y-m-d',strtotime($varFechas[2]));
                $varFechasAsesor = $varFechasInicio."T00:00:00.000Z/".$varFechasFin."T00:00:00.000Z";


                $varValidaAsesor = (new \yii\db\Query())
                                ->select(['tbl_genesys_parametroasesor.id_genesys'])
                                ->from(['tbl_genesys_parametroasesor'])
                                ->where(['=','tbl_genesys_parametroasesor.anulado',0])
                                ->andwhere(['=','tbl_genesys_parametroasesor.documento_asesor',$varDocumentoAsesor])
                                ->scalar();

                if (count($varValidaAsesor) != 0) {
                    
                    ob_start();
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_SSL_VERIFYPEER=> false,
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_URL => 'https://login.mypurecloud.com/oauth/token',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
                        CURLOPT_HTTPHEADER => array(
                            'Authorization: Basic YWI0ODEyMGYtMWMyYi00NDAxLTkzMzktYjFhM2JlMmYxY2UyOkNtX2loUDF5VE9oWTI3Sjl4ZmhReHJua2F0djQtUnB6bHpQLW1DdVQ5eEk=',
                            'Content-Type: application/x-www-form-urlencoded'
                        ),
                    ));

                    $response = curl_exec($curl);

                    curl_close($curl);
                        
                    ob_clean();

                    if (!$response) {
                        die(json_encode(array('status' => '0','data'=>'Error al buscar la transcripcion')));
                    }

                    if (count($response) == 0) {
                        die(json_encode(array('status' => '0','data'=>'Transcripcion no encontrada'))); 
                    }

                    $varProcesosTokenUno = explode(",", $response);
                  
                    $varProcesosTokenDos = explode(":",$varProcesosTokenUno[0]);
                    $varrespuesta = str_replace('"', '', $varProcesosTokenDos[1]);

                    if ($varrespuesta != "") {
                        
                        ob_start();
                        $curlAsesor = curl_init();

                        curl_setopt_array($curlAsesor, array(
                            CURLOPT_SSL_VERIFYPEER=> false,
                            CURLOPT_SSL_VERIFYHOST => false,
                            CURLOPT_URL => 'https://api.mypurecloud.com/api/v2/analytics/conversations/details/query',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS =>'{
                                "interval": "'.$varFechasAsesor.'",
                                "order": "asc",
                                "orderBy": "conversationStart",
                                "paging": {
                                "pageSize": "100",
                                "pageNumber": "1"
                            },
                            "segmentFilters": [
                                {
                                    "type": "and",
                                    "predicates": [
                                        {
                                            "type": "dimension",
                                            "dimension": "userId",
                                            "operator": "matches",
                                            "value": "'.$varValidaAsesor.'"
                                        }
                                    ]
                                }
                            ]
                            }',
                            CURLOPT_HTTPHEADER => array(
                                'Authorization: Bearer '.$varrespuesta.'',
                                'Content-Type: application/json'
                            ),
                        ));

                        $responseAsesor = curl_exec($curlAsesor);

                        curl_close($curlAsesor);

                        ob_clean();

                        if (!$responseAsesor) {
                            die(json_encode(array('status' => '0','data'=>'Error al buscar la transcripcion')));
                        }

                        $responseAsesor = json_decode(iconv( "Windows-1252", "UTF-8//IGNORE", $responseAsesor ),true);

                        if (count($responseAsesor) == 0) {
                            die(json_encode(array('status' => '0','data'=>'Transcripcion no encontrada'))); 
                        }

                        if (count($responseAsesor) != 0) {

                            $varTmpEvaluadoId = (new \yii\db\Query())
                                ->select(['tbl_evaluados.id'])
                                ->from(['tbl_evaluados'])
                                ->where(['=','tbl_evaluados.identificacion',$varDocumentoAsesor])
                                ->scalar();

                            $varConteoUrl = (new \yii\db\Query())
                                ->select(['tbl_genesys_tmpinteracciones.urlgenesys'])
                                ->from(['tbl_genesys_tmpinteracciones'])
                                ->where(['=','tbl_genesys_tmpinteracciones.anulado',0])
                                ->andwhere(['=','tbl_genesys_tmpinteracciones.documento_asesor',$varDocumentoAsesor])
                                ->andwhere(['=','tbl_genesys_tmpinteracciones.evaluado_id',$varTmpEvaluadoId])
                                ->andwhere(['=','tbl_genesys_tmpinteracciones.fechainicio',$varFechasInicio])
                                ->andwhere(['=','tbl_genesys_tmpinteracciones.fechafin',$varFechasFin])
                                ->andwhere(['=','tbl_genesys_tmpinteracciones.usua_id',$usua_id])
                                ->count();

                            if ($varConteoUrl == 0) {

                                if ($responseAsesor['totalHits'] != 0) {
                                    foreach ($responseAsesor['conversations'] as $key => $value) {
                                
                                        Yii::$app->db->createCommand()->insert('tbl_genesys_tmpinteracciones',[
                                              'documento_asesor' => $varDocumentoAsesor,
                                              'evaluado_id' => $varTmpEvaluadoId,
                                              'fechainicio' => $varFechasInicio,
                                              'fechafin' => $varFechasFin,
                                              'connid' => $value['conversationId'], 
                                              'urlgenesys' => 'https://apps.mypurecloud.com/directory/#/engage/admin/interactions/'.$value['conversationId'], 
                                              'usua_id' => $usua_id,
                                              'fechacreacion' => date('Y-m-d'),
                                              'anulado' => 0,                         
                                        ])->execute();

                                    }
                                }else{
                                    $varMensaje = 2;
                                }
                                
                            }                            

                            $varDataList = (new \yii\db\Query())
                                ->select(['*'])
                                ->from(['tbl_genesys_tmpinteracciones'])
                                ->where(['=','tbl_genesys_tmpinteracciones.documento_asesor',$varDocumentoAsesor])
                                ->andwhere(['=','tbl_genesys_tmpinteracciones.evaluado_id',$varTmpEvaluadoId])
                                ->andwhere(['=','tbl_genesys_tmpinteracciones.fechainicio',$varFechasInicio])
                                ->andwhere(['=','tbl_genesys_tmpinteracciones.fechafin',$varFechasFin])
                                ->andwhere(['=','tbl_genesys_tmpinteracciones.usua_id',$usua_id])
                                ->all();

                        }

                    }

                }else{
                    $varMensaje = 2;
                }

            }else{
                $varMensaje = 1;
            }
        }

        return $this->render('gbuscarporasesor',[
            'model' => $model,
            'varDataList' => $varDataList,
            'varMensaje' => $varMensaje,
            'varTmpEvaluadoId' => $varTmpEvaluadoId,
        ]);
    }

    public function actionGbuscarporconnid(){
        $model = new Evaluados();
        $varTmpEvaluadoId = null;
        $varDataList = null;
        $varMensaje = 0;


        return $this->render('gbuscarporconnid',[
            'model' => $model,
            'varDataList' => $varDataList,
            'varMensaje' => $varMensaje,
            'varTmpEvaluadoId' => $varTmpEvaluadoId, 
        ]);
    }


    public function actionActualizaservicio(){
        $model = new SpeechServicios();

        $varLisArbolid = (new \yii\db\Query())
                                ->select(['*'])
                                ->from(['tbl_genesys_formularios'])
                                ->All();

        $form = Yii::$app->request->post();
        if ($model->load($form)) {
            $varArbolid = $model->arbol_id;
            $varNombreCola = $model->pcrc;
            $varIdCola = $model->comentarios;

            
                Yii::$app->db->createCommand()->insert('tbl_genesys_formularios',[
                    'arbol_id' => $varArbolid,
                    'cola_genesys' => $varNombreCola,
                    'id_cola_genesys' => $varIdCola,
                    'usua_id' => Yii::$app->user->identity->id,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,                         
                ])->execute();
            

            return $this->redirect(['actualizaservicio']);
            
        }

        return $this->render('actualizaservicio',[
            'model' => $model,
            'varLisArbolid' => $varLisArbolid,
        ]);
    }

    public function actionDeletegenesysarbol($id){
        $paramsEliminar = [':IdControl'=>$id];          

        Yii::$app->db->createCommand('
              DELETE FROM tbl_genesys_formularios 
                WHERE 
                  id_genesysformularios = :IdControl')
            ->bindValues($paramsEliminar)
            ->execute();

        return $this->redirect(['actualizaservicio']);
    }
     
    public function actionCortesyservicios(){
        $model = new Corteservicios();  //a la variable model le asignamos todo lo que haya en el model

        $varData = (new \yii\db\Query())
        ->select(['tbl_grupo_cortes.nomgrupocorte','tbl_proceso_cliente_centrocosto.cliente','tbl_cortes_servicios.id_corteservicios'])
        ->from(['tbl_cortes_servicios'])
        ->join('LEFT OUTER JOIN', 'tbl_grupo_cortes',
                                  'tbl_grupo_cortes.idgrupocorte = tbl_cortes_servicios.id_corte')
        ->join('LEFT OUTER JOIN', 'tbl_proceso_cliente_centrocosto',
                                  'tbl_proceso_cliente_centrocosto.id_dp_clientes = tbl_cortes_servicios.id_servicio')   
        ->where(['=','tbl_cortes_servicios.anulado',0])                                                
        ->groupby(['tbl_proceso_cliente_centrocosto.id_dp_clientes']) 
        ->all();

        $form = Yii::$app->request->post();
        if ($model->load($form)) {     
            $varIdCorte = $model->id_corte;
            $varIdServicio = $model->id_servicio;

            $varConteo = (new \yii\db\Query())
            ->select(['*'])
            ->from(['tbl_cortes_servicios'])
            ->where(['=','id_servicio',$varIdServicio])
            ->andwhere(['=','id_corte',$varIdCorte]) 
            ->andwhere(['=','anulado',0])
            ->count();

            if ($varConteo == 0 ) {
                Yii::$app->db->createCommand()->insert('tbl_cortes_servicios',[
                    'id_corte' => $varIdCorte,
                    'id_servicio' => $varIdServicio,
                    'usua_id' => Yii::$app->user->identity->id,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,                         
                ])->execute();
    
            } 
            
            return $this->redirect(['cortesyservicios']);                   
        }

        return $this->render('cortesyservicios',[//retornar  la vista 
            'model' => $model, 
            'varData' => $varData, //retornar variables del controlador en la vista 
        ]);
    }

    public function actionDeleteserviciocorte($id){
        $varparametros = [
            ':varid'=> $id
        ];
        Yii::$app->db->createCommand('
              UPDATE tbl_cortes_servicios SET anulado = 1
                WHERE 
                  id_corteservicios = :varid')
            ->bindValues($varparametros)
            ->execute();

        return $this->redirect(['cortesyservicios']);//retornar  la vista 

    }

    public function actionViewmotivosdeclinacion(){
        $model = new Declinaciones();

        $varListMotivos= (new \yii\db\Query())
                                    ->select(['*'])
                                    ->from(['tbl_declinacion_motivo'])
                                    ->orderBy(['nombre' => SORT_DESC])
                                    ->where(['=','anulado',0])          
                                    ->all(); 

        $form = Yii::$app->request->post();
        if ($model->load($form)) {
           
            $txtnombre = $model->nombre;

            Yii::$app->db->createCommand()->insert('tbl_declinacion_motivo',[
                    'nombre' => $txtnombre,
                    'fechacreacion' => date("Y-m-d"),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
            ])->execute();

            return $this->redirect(['viewmotivosdeclinacion']);
        }

        return $this->render('viewmotivosdeclinacion',[
            'model' => $model,
            'varListEquipos' => $varListMotivos,
        ]);
    }
    
    public function actionDeletemotivos($id){
        $paramsEliminar = $id;

        Yii::$app->db->createCommand('
            UPDATE tbl_declinacion_motivo 
                SET anulado = :varAnulado
                WHERE 
                id_declina_motivo = :VarId')
            ->bindValue(':VarId', $paramsEliminar)
            ->bindValue(':varAnulado', 1)
            ->execute();        
            return $this->redirect(['viewmotivosdeclinacion']);
    }
    
    public function actionDeleteareaapoyo($id){
        $paramsEliminar = $id;

        Yii::$app->db->createCommand('
            UPDATE tbl_areasapoyo_gptw 
                SET anulado = :varAnulado
                WHERE 
                id_areaapoyo = :VarId')
            ->bindValue(':VarId', $paramsEliminar)
            ->bindValue(':varAnulado', 1)
            ->execute();        
            return $this->redirect(['viewareaapoyogptw']);
    }

    public function actionDeletepilares($id){
        $paramsEliminar = $id;

        Yii::$app->db->createCommand('
            UPDATE tbl_pilares_gptw 
                SET anulado = :varAnulado
                WHERE 
                id_pilares = :VarId')
            ->bindValue(':VarId', $paramsEliminar)
            ->bindValue(':varAnulado', 1)
            ->execute();        
            return $this->redirect(['viewpilaresgptw']);
    }

    public function actionDeleteprocesosatisfac($id){
        $paramsEliminar = $id;

        Yii::$app->db->createCommand('
            UPDATE tbl_procesos_satisfaccion_cliente 
                SET anulado = :varAnulado
                WHERE 
                id_proceso_satis = :VarId')
            ->bindValue(':VarId', $paramsEliminar)
            ->bindValue(':varAnulado', 1)
            ->execute();        
            return $this->redirect(['viewprocesossatisfaccion']);
    } 

    public function actionDeleteprocesoindicador($id){
        $paramsEliminar = $id;

        Yii::$app->db->createCommand('
            UPDATE tbl_indicadores_satisfaccion_cliente 
                SET anulado = :varAnulado
                WHERE 
                id_indicador = :VarId')
            ->bindValue(':VarId', $paramsEliminar)
            ->bindValue(':varAnulado', 1)
            ->execute();        
            return $this->redirect(['viewindicadores']);
    }

    public function actionAdminusuarios(){
        $model = new FormUploadtigo();  

        $varListaSociedades = (new \yii\db\Query())
                                ->select(['tbl_hojavida_sociedad.sociedad', 'COUNT(tbl_usuarios.usua_id) AS varConteo'])
                                ->from(['tbl_hojavida_sociedad'])
                                ->join('INNER JOIN', 'tbl_usuarios', 
                                  'tbl_hojavida_sociedad.id_sociedad = tbl_usuarios.id_sociedad')
                                ->groupby(['tbl_usuarios.id_sociedad'])
                                ->all();

        $varListarSociedadCXM = (new \yii\db\Query())
                                ->select(['*'])
                                ->from(['tbl_hojavida_sociedad'])
                                ->all();

        $form = Yii::$app->request->post();
        if ($model->load($form)) {

            $model->file = UploadedFile::getInstances($model, 'file');

            if ($model->file && $model->validate()) {
                    
                foreach ($model->file as $file) {
                    $fecha = date('Y-m-d-h-i-s');
                    $user = Yii::$app->user->identity->username;
                    $name = $fecha . '-' . $user;
                    $file->saveAs('categorias/' . $name . '.' . $file->extension);
                    $this->Importarusuariosadmin($name);

                    return $this->redirect(['adminusuarios']);
                }
            }

        }


        return $this->render('adminusuarios',[
            'model' => $model,
            'varListaSociedades' => $varListaSociedades,
            'varListarSociedadCXM' => $varListarSociedadCXM,
        ]);
    }

    public function Importarusuariosadmin($name){
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
            $varExisteUsuario = (new \yii\db\Query())
                            ->select(['*'])
                            ->from(['tbl_usuarios'])
                            ->where(['=','usua_identificacion',$sheet->getCell("C".$i)->getValue()])
                            ->andwhere(['=','usua_usuario',$sheet->getCell("B".$i)->getValue()])
                            ->count();

            if ($varExisteUsuario == 0) {

                $varIdSociedad = (new \yii\db\Query())
                            ->select(['id_sociedad'])
                            ->from(['tbl_hojavida_sociedad'])
                            ->where(['=','sociedad',$sheet->getCell("G".$i)->getValue()])
                            ->scalar();

                if ($varIdSociedad == "") {
                    $varIdSociedad = 4;
                }
                
                Yii::$app->db->createCommand()->insert('tbl_usuarios',[
                  'usua_usuario' => $sheet->getCell("B".$i)->getValue(),
                  'usua_nombre' => $sheet->getCell("A".$i)->getValue(),
                  'usua_email' => $sheet->getCell("D".$i)->getValue(),
                  'usua_identificacion' => $sheet->getCell("C".$i)->getValue(),
                  'usua_activo' => 'S',
                  'usua_estado' => 'D',
                  'fechacreacion' => date("Y-m-d"),                    
                  'id_sociedad' => $varIdSociedad,
                ])->execute(); 

                $varIdUsuario = (new \yii\db\Query())
                            ->select(['usua_id'])
                            ->from(['tbl_usuarios'])
                            ->where(['=','usua_identificacion',$sheet->getCell("C".$i)->getValue()])
                            ->andwhere(['=','usua_usuario',$sheet->getCell("B".$i)->getValue()])
                            ->scalar();

                $varIdRol = (new \yii\db\Query())
                            ->select(['role_id'])
                            ->from(['tbl_roles'])
                            ->where(['=','role_descripcion',$sheet->getCell("E".$i)->getValue()])
                            ->scalar();

                Yii::$app->db->createCommand()->insert('rel_usuarios_roles',[
                  'rel_usua_id' => $varIdUsuario,
                  'rel_role_id' => $varIdRol,
                ])->execute(); 

                $varIdGrupo = (new \yii\db\Query())
                            ->select(['grupos_id'])
                            ->from(['tbl_grupos_usuarios'])
                            ->where(['=','grupo_descripcion',$sheet->getCell("F".$i)->getValue()])
                            ->scalar();

                Yii::$app->db->createCommand()->insert('rel_grupos_usuarios',[
                  'usuario_id' => $varIdUsuario,
                  'grupo_id' => $varIdGrupo,
                ])->execute(); 


            }
        }
    }

    public function actionAdminapiwiasae(){

        $varListaData = (new \yii\db\Query())
                                ->select([
                                    'tbl_comdata_parametrizarapi.id_parametrizarapi',
                                    'tbl_proceso_cliente_centrocosto.cliente', 
                                    'tbl_comdata_parametrizarapi.proyecto_id',
                                    'tbl_comdata_parametrizarapi.sociedadprovieniente',
                                    'tbl_comdata_parametrizarapi.table_id'
                                ])
                                ->from(['tbl_proceso_cliente_centrocosto'])
                                ->join('LEFT OUTER JOIN', 'tbl_comdata_parametrizarapi',
                                  'tbl_proceso_cliente_centrocosto.id_dp_clientes = tbl_comdata_parametrizarapi.id_dp_clientes')
                                ->where(['=','tbl_comdata_parametrizarapi.anulado',0])
                                ->groupby(['tbl_comdata_parametrizarapi.id_parametrizarapi'])
                                ->all();

        return $this->render('adminapiwiasae',[
            'varListaData' => $varListaData,
        ]);
    }

    public function actionAgregarnuevodatoapi(){
        $model = new Comdataparametrizarapi();

        $form = Yii::$app->request->post();
        if ($model->load($form)) {

            $varMaximo = (new \yii\db\Query())
                                ->select([
                                    'max(orden)'
                                ])
                                ->from(['tbl_comdata_parametrizarapi'])
                                ->where(['=','tbl_comdata_parametrizarapi.anulado',0])
                                ->scalar();
            
            Yii::$app->db->createCommand()->insert('tbl_comdata_parametrizarapi',[
                'id_dp_clientes' => $model->id_dp_clientes,
                'sociedadprovieniente' => $model->sociedadprovieniente,
                'proyecto_id' => $model->proyecto_id,
                'dataset_id' => $model->dataset_id,
                'table_id' => $model->table_id,
                'limit' => $model->limit,
                'offset' => $model->offset,
                'cod_pcrc' => $model->cod_pcrc,
                'extension' => $model->extension,
                'fechacreacion' => date("Y-m-d"),                    
                'anulado' => 0,
                'usua_id' => Yii::$app->user->identity->id,
                'orden' =>$varMaximo + 1,
            ])->execute();

            return $this->redirect('adminapiwiasae');

        }

        return $this->renderAjax('agregarnuevodatoapi',[
            'model' => $model,
        ]);
    }

    public function actionDeteleapiwiasae($id){
        $paramsEliminar = $id;

        Yii::$app->db->createCommand('
            UPDATE tbl_comdata_parametrizarapi 
                SET anulado = :varAnulado
                WHERE 
                id_parametrizarapi = :VarId')
            ->bindValue(':VarId', $paramsEliminar)
            ->bindValue(':varAnulado', 1)
            ->execute();        
            return $this->redirect(['adminapiwiasae']);
    }

    public function actionViewtipoalertasqyr(){
        $model = new Tipoalertasqyr();

        $varListAlertas = (new \yii\db\Query())
                                    ->select(['*'])
                                    ->from(['tbl_qr_tipo_alertas'])
                                    ->orderBy(['nombre' => SORT_DESC])
                                    ->where(['=','anulado',0])          
                                    ->all(); 

        $form = Yii::$app->request->post();
        if ($model->load($form)) {
           // $txtidEquipo = $model->usua_id;
            $txtnombre = $model->nombre;

            Yii::$app->db->createCommand()->insert('tbl_qr_tipo_alertas',[
                    'nombre' => $txtnombre,
                    'fechacreacion' => date("Y-m-d"),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
            ])->execute();

            return $this->redirect(['viewtipoalertasqyr']);
        }

        return $this->render('viewtipoalertasqyr',[
            'model' => $model,
            'varListAlertas' => $varListAlertas ,
        ]);
    }

    public function actionDeletealertasqyr($id){
        $paramsEliminar = $id;

        Yii::$app->db->createCommand('
            UPDATE tbl_qr_tipo_alertas 
                SET anulado = :varAnulado
                WHERE 
                id_tipo_alerta = :VarId')
            ->bindValue(':VarId', $paramsEliminar)
            ->bindValue(':varAnulado', 1)
            ->execute();        
            return $this->redirect(['viewtipoalertasqyr']);
    }

    public function actionViewareasqyr(){
        $model = new Areasqyr();

        $varListAreasqyr = (new \yii\db\Query())
                                    ->select(['*'])
                                    ->from(['tbl_qr_areas'])
                                    ->orderBy(['nombre' => SORT_DESC])
                                    ->where(['=','anulado',0])          
                                    ->all(); 

        $form = Yii::$app->request->post();
        if ($model->load($form)) {
           // $txtidEquipo = $model->usua_id;
            $txtnombre = $model->nombre;

            Yii::$app->db->createCommand()->insert('tbl_qr_areas',[
                    'nombre' => $txtnombre,
                    'fechacreacion' => date("Y-m-d"),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
            ])->execute();

            return $this->redirect(['viewareasqyr']);
        }

        return $this->render('viewareasqyr',[
            'model' => $model,
            'varListAreasqyr' => $varListAreasqyr ,
        ]);
    }

    public function actionDeleteareasqyr($id){
        $paramsEliminar = $id;

        Yii::$app->db->createCommand('
            UPDATE tbl_qr_areas 
                SET anulado = :varAnulado
                WHERE 
                id = :VarId')
            ->bindValue(':VarId', $paramsEliminar)
            ->bindValue(':varAnulado', 1)
            ->execute();        
            return $this->redirect(['viewareasqyr']);
    }

    public function actionViewtipologiasqyr(){
        $model2 = new Tipologiasqyr();
        $model = new Areasqyr();

        $form = Yii::$app->request->post();
        if ($model->load($form)) {

            $varid = $model->id;
            $varnombre = $model->nombre;
           

            Yii::$app->db->createCommand()->insert('tbl_qr_tipologias',[
                                             'id_areas' => $varid,
                                             'tipologia' => $varnombre,
                                             'fechacreacion' => date("Y-m-d"),
                                             'anulado' => 0,
                                             'usua_id' => Yii::$app->user->identity->id,
                                         ])->execute(); 

            return $this->redirect('viewtipologiasqyr',[
                'model' => $model,
                'model2' => $model2,
            ]);
        }

        return $this->render('viewtipologiasqyr',[
            'model' => $model,
            'model2' => $model2,
        ]);
    }

    public function actionDeletetipologiaqyr($id){
        $paramsEliminar = $id;

        Yii::$app->db->createCommand('
            UPDATE tbl_qr_tipologias 
                SET anulado = :varAnulado
                WHERE 
                id = :VarId')
            ->bindValue(':VarId', $paramsEliminar)
            ->bindValue(':varAnulado', 1)
            ->execute();        
            return $this->redirect(['viewtipologiasqyr']);
    }

    public function actionViewrespuestaautomaticaqyr(){
        $model = new RespuestaAutomatica();

        $varListRespuesta = (new \yii\db\Query())
                                    ->select(['*'])
                                    ->from(['tbl_qr_respuesta_automatica'])
                                    ->orderBy(['asunto' => SORT_DESC])
                                    ->where(['=','anulado',0])          
                                    ->all(); 

        $form = Yii::$app->request->post();
        if ($model->load($form)) {
           // $txtidEquipo = $model->usua_id;
           
            $txtestado = $model->id_estado;
            $txtnombre = $model->asunto;
            $txtcomentario = $model->comentario;

            Yii::$app->db->createCommand()->insert('tbl_qr_respuesta_automatica',[
                    'asunto' => $txtnombre,
                    'comentario' => $txtcomentario,
                    'id_estado' => $txtestado,
                    'fechacreacion' => date("Y-m-d"),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
            ])->execute();

            return $this->redirect(['viewrespuestaautomaticaqyr']);
        }

        return $this->render('viewrespuestaautomaticaqyr',[
            'model' => $model,
            'varListRespuesta' => $varListRespuesta ,
        ]);
    }

    public function actionDeleterespuestaqyr($id){
        $paramsEliminar = $id;

        Yii::$app->db->createCommand('
            UPDATE varListRespuesta 
                SET anulado = :varAnulado
                WHERE 
                id_respuesta = :VarId')
            ->bindValue(':VarId', $paramsEliminar)
            ->bindValue(':varAnulado', 1)
            ->execute();        
            return $this->redirect(['viewrespuestaautomaticaqyr']);
    }

    public function actionViewestadosqyr(){
        $model = new Estadosqyr();

        $varListAlertas = (new \yii\db\Query())
                                    ->select(['*'])
                                    ->from(['tbl_qr_estados'])
                                    ->orderBy(['nombre' => SORT_DESC])
                                    ->where(['=','anulado',0])          
                                    ->all(); 

        $form = Yii::$app->request->post();
        if ($model->load($form)) {
           // $txtidEquipo = $model->usua_id;
            $txtnombre = $model->nombre;

            Yii::$app->db->createCommand()->insert('tbl_qr_estados',[
                    'nombre' => $txtnombre,
                    'fechacreacion' => date("Y-m-d"),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
            ])->execute();

            return $this->redirect(['viewestadosqyr']);
        }

        return $this->render('viewestadosqyr',[
            'model' => $model,
            'varListAlertas' => $varListAlertas ,
        ]);
    }

    public function actionViewalertacumplimientoqyr(){
        $model = new cumplimientoqyr();

        $varListacumplimiento = (new \yii\db\Query())
                                    ->select(['*'])
                                    ->from(['tbl_qr_cumplimiento'])
                                    ->orderBy(['indicador' => SORT_DESC])
                                    ->where(['=','anulado',0])          
                                    ->all(); 

        $form = Yii::$app->request->post();
        if ($model->load($form)) {
           // $txtidEquipo = $model->usua_id;
            $txtindicador = $model->indicador;
            $txtdia1verde = $model->diaverde1;
            $txtdia2verde = $model->diaverde2;
            $txtdia1amarillo = $model->diaamarillo1;
            $txtdia2amarillo = $model->diaamarillo2;
            $txtdia1rojo = $model->diarojo1;
            $txtdia2rojo= $model->diarojo1;
            $txtmensaje1 = $model->mensaje1;
            $txtmensaje2 = $model->mensaje2;
            $txtmensaje3 = $model->mensaje3;

            Yii::$app->db->createCommand()->insert('tbl_qr_cumplimiento',[
                    'indicador' => $txtindicador,
                    'diaverde1' => $txtdia1verde,
                    'diaverde2' => $txtdia2verde,
                    'diaamarillo1' => $txtdia1amarillo,
                    'diaamarillo2' => $txtdia2amarillo,
                    'diarojo1' => $txtdia1rojo,
                    'diarojo2' => $txtdia2rojo,
                    'mensaje1' => $txtmensaje1,
                    'mensaje2' => $txtmensaje2,
                    'mensaje3' => $txtmensaje3,
                    'fechacreacion' => date("Y-m-d"),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
            ])->execute();

            return $this->redirect(['viewalertacumplimientoqyr']);
        }

        return $this->render('viewalertacumplimientoqyr',[
            'model' => $model,
            'varListacumplimiento' => $varListacumplimiento ,
        ]);
    }

    public function actionViewcartarespuestaqyr(){
        $model = new Estadosqyr();

        $varListAlertas = (new \yii\db\Query())
                                    ->select(['*'])
                                    ->from(['tbl_qr_estados'])
                                    ->orderBy(['nombre' => SORT_DESC])
                                    ->where(['=','anulado',0])          
                                    ->all(); 

        $form = Yii::$app->request->post();
        if ($model->load($form)) {
           // $txtidEquipo = $model->usua_id;
            $txtnombre = $model->nombre;

            Yii::$app->db->createCommand()->insert('tbl_qr_estados',[
                    'nombre' => $txtnombre,
                    'fechacreacion' => date("Y-m-d"),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
            ])->execute();

            return $this->redirect(['viewcartarespuestaqyr']);
        }

        return $this->render('viewcartarespuestaqyr',[
            'model' => $model,
            'varListAlertas' => $varListAlertas ,
        ]);
    }
    
    public function actionImportardocumento(){        
        $model = new UploadForm2();                        
        $ruta = null;
        $id = 'cliente';
  
        $form = Yii::$app->request->post();     
  
        if($model->load($form)){
  
          $model->file = UploadedFile::getInstance($model, 'file');
          if ($model->file && $model->validate()) {
         
            foreach ($model->file as $file) {
              $ruta = 'images/documentos/'."documento_".$id."_".time()."_".$model->file->baseName. ".".$model->file->extension;
            
              $model->file->saveAs( $ruta ); 
            }
          } 
           
          if ($ruta != null) {
           
            return $this->redirect(array('index','varidban'=>0));
          }else{
            $ruta = null;
          }
          
  
        }                  
        
        return $this->renderAjax('importardocumento',[          
          'model' => $model,
          'ruta' => $ruta,
        ]);
      }

      public function actionUpdaterespuestaqyr($idres){        
        $model = new RespuestaAutomatica();
        $model1 = new Estadosqyr();

        $varListRespuesta = (new \yii\db\Query())
                                    ->select(['*'])
                                    ->from(['tbl_qr_respuesta_automatica'])
                                    ->orderBy(['asunto' => SORT_DESC])
                                    ->where(['=','anulado',0])
                                    ->andwhere(['=','id_respuesta',$idres])          
                                    ->all(); 

        $varid = (new \yii\db\Query())
                                    ->select(['id_estado'])
                                    ->from(['tbl_qr_respuesta_automatica'])
                                    ->orderBy(['asunto' => SORT_DESC])
                                    ->where(['=','anulado',0])
                                    ->andwhere(['=','id_respuesta',$idres])       
                                    ->Scalar();
        if($varid){            
            $model1 = Estadosqyr::findOne($varid); 
        }   
        
        $form = Yii::$app->request->post();
        if ($model->load($form)) {
           // $txtidEquipo = $model->usua_id;
           
            $txtestado = $model1->id_estado;
            $txtnombre = $model->asunto;
            $txtcomentario = $model->comentario;
            $txtcomentario2 = $model->comentario2;

            Yii::$app->db->createCommand()->update('tbl_qr_respuesta_automatica',[
                'asunto' => $txtnombre,
                'comentario' => $txtcomentario,
                'comentario2' => $txtcomentario2,
                'id_estado' => $txtestado,
              ],"id_respuesta = '$idres'")->execute(); 

            return $this->redirect(['viewrespuestaautomaticaqyr']);
        }

        return $this->render('updaterespuestaqyr',[
            'model' => $model,
            'model1' => $model1,
            'varListRespuesta' => $varListRespuesta ,
        ]);
        
    }

    /* PROCESO AGREGAR PCRC PARA AJUSTAR SCORE FINAL SEGUN NOTA MALA EN ALGUN ATRIBUTO CRITICO (PEC) ----------------------------------------------------------------- 
       Si no cumple con algun atributo critico (PEC) en una sección, se disminuye el peso total de la seccion al score final,
       Si no cumple con algun atributo no critico (PENC) en una sección, continua disminuyendo el porcentaje de ese atributo al score final,
       Si no cumple con algun atributo no critico y con algun no critico, le resta el peso de la seccion y el porcentaje del atributo no critico al score final
    */

    public function actionParametrizarpcrcatributoscriticos(){
        $model = new ControlParams();

        $form = Yii::$app->request->post();
            if ($model->load($form)) {

                $varidArbol = $model->arbol_id; // id PCRC

                $existe_id_pcrc = (new \yii\db\Query())
                ->select(['id_control_pcrc_pec'])
                ->from(['tbl_control_pcrc_pec'])
                ->where(['=','arbol_id',$varidArbol])
                ->andwhere(['=','anulado', 0])
                ->count();

                // No existe el id del PCRC
                if($existe_id_pcrc==0){

                    $insertar_datos = Yii::$app->db->createCommand()->insert('tbl_control_pcrc_pec',[
                        'arbol_id' => $varidArbol,
                        'fecha_creacion' => date("Y-m-d"),
                        'usua_id' => Yii::$app->user->identity->id,
                    ])->execute();

                    if($insertar_datos>0){
                        Yii::$app->session->setFlash('success_creacion', 'Creación exitosa.');        
                    } else {
                        Yii::$app->session->setFlash('error_creacion', 'Error agregando los datos.');
                    } 
                }

                // Ya existe en nuestra tabla el id_pcrc 
                if($existe_id_pcrc>0){
                    Yii::$app->session->setFlash('error_creacion', 'Ya existe el Programa/PCRC.');
                }    

                return $this->redirect('parametrizarpcrcatributoscriticos');
                
            }

            return $this->render('parametrizarpcrcatributoscriticos',[
                    'model' => $model,
            ]);
    
    }

    //Funcion para obtener la lista de los PCRC agregados que se debe ajustar su score final 
    public function actionCargarlistapcrc() {

        $varListaPcrcs= Yii::$app->db->createCommand("
        SELECT DISTINCT (pec.arbol_id) AS id_pcrc, arbol.name AS nom_pcrc
        FROM tbl_control_pcrc_pec pec
        INNER JOIN tbl_arbols arbol ON arbol.id = pec.arbol_id 
        where anulado = 0")->queryAll();

        $response = [
            'status' => 'success',
            'data' => $varListaPcrcs,
        ];

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $response;

    }

    //Eliminar logicamente un pcrc de la tabla tbl_control_pcrc_pec
    public function actionDeletepcrcatributoscriticos() {
        $paramsEliminar = Yii::$app->request->get('id_pcrc');   

        $result = Yii::$app->db->createCommand('
            UPDATE tbl_control_pcrc_pec 
            SET anulado = :varAnulado
            WHERE 
            arbol_id = :VarId')
        ->bindValue(':VarId', $paramsEliminar)
        ->bindValue(':varAnulado', 1)
        ->execute();

        if ($result>0) {
            $response = [
                'status' => 'success',
                'data' => 'Programa/PCRC eliminado correctamente.',
                ];            
        } else {
            $response = [
                'status' => 'error',
                'data' => 'Error eliminando Programa/PCRC.',
                ];                        
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $response;
    }    

    // FIN PROCESO AGREGAR PCRC PARA AJUSTAR SCORE FINAL SEGUN NOTA MALA EN ALGUN ATRIBUTO CRITICO (PEC) -----------------------------------------------------------------

    public function actionParametrizarpcrccomdata(){
        $model = new ControlParams();

        $varListaPcrcs = (new \yii\db\Query())
                                    ->select(['*'])
                                    ->from(['tbl_control_pcrc_comdata'])
                                    ->orderBy(['fecha_creacion' => SORT_DESC])
                                    ->all(); 

        $form = Yii::$app->request->post();
        if ($model->load($form)) {
            $varidArbol = $model->arbol_id;
            $varMetricas = $model->argumentos;

            Yii::$app->db->createCommand()->insert('tbl_control_pcrc_comdata',[
                    'arbol_id' => $varidArbol,
                    'metrica' => $varMetricas,
                    'fecha_creacion' => date("Y-m-d"),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
            ])->execute();

            return $this->redirect(['parametrizarpcrccomdata']);
        }

        return $this->render('parametrizarpcrccomdata',[
            'varListaPcrcs' => $varListaPcrcs,
            'model' => $model,
        ]);
    }

    public function actionDeletepcrcs($idcon){
        $paramsEliminar = $idcon;         

        Yii::$app->db->createCommand('
        UPDATE tbl_control_pcrc_comdata 
            SET anulado = :varAnulado
            WHERE 
            idcontrolpcrc = :VarId')
        ->bindValue(':VarId', $paramsEliminar)
        ->bindValue(':varAnulado', 1)
        ->execute();

        return $this->redirect(['parametrizarpcrccomdata']);
    }

    public function actionParametrizarpbi(){

        return $this->render('parametrizarpbi');
    }

    public function actionAgregarcredencialespbi(){
        $model = new WorspaceReportesPowerbi();

        $form = Yii::$app->request->post();
        if($model->load($form)){
            $varListHistorico = (new \yii\db\Query())
                                ->select([
                                    '*'
                                ])
                                ->from(['tbl_config_powerbi'])
                                ->all();

            foreach ($varListHistorico as $value) {
                Yii::$app->db->createCommand()->insert('tbl_config_powerbihistorico',[
                    'azure_param' => $value['azure_param'],
                    'azure_content' => $value['azure_content'],
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
                    'fechacreacion' => date("Y-m-d"),
                ])->execute();
            }

            Yii::$app->db->createCommand()->update('tbl_config_powerbi',[
                      'azure_param' => 'azure_tenant_id',                   
                      'azure_content' => $model->id_workspace,                     
            ],'id_config = 1')->execute();

            Yii::$app->db->createCommand()->update('tbl_config_powerbi',[
                      'azure_param' => 'azure_client_id',                   
                      'azure_content' => $model->nombre_workspace,                     
            ],'id_config = 2')->execute();

            Yii::$app->db->createCommand()->update('tbl_config_powerbi',[
                      'azure_param' => 'azure_client_secret',                   
                      'azure_content' => $model->id_reporte,                     
            ],'id_config = 3')->execute();

            return $this->redirect(['parametrizarpbi']);

        }

        return $this->renderAjax('agregarcredencialespbi',[
            'model' => $model,
        ]);
    }

    public function actionVercredencialpbi(){
        $varListCredenciales = (new \yii\db\Query())
                                ->select([
                                    'tbl_config_powerbihistorico.azure_param',
                                    'tbl_config_powerbihistorico.azure_content',
                                    'tbl_config_powerbihistorico.fechacreacion'
                                ])
                                ->from(['tbl_config_powerbihistorico'])
                                ->where(['=','tbl_config_powerbihistorico.anulado',0])
                                ->all();

        return $this->renderAjax('vercredencialpbi',[
            'varListCredenciales' => $varListCredenciales,
        ]);
    }

    public function actionAleatorioencuestas(){
        $model = new BaseAleatorio();

        $varListaAleatorio = (new \yii\db\Query())
                        ->select([
                            'tbl_arbols.name AS varArbol',
                            'COUNT(tbl_reglanegocio.rn) AS varConteo'
                        ])
                        ->from(['tbl_base_aleatorio'])
                        ->join('LEFT OUTER JOIN', 'tbl_reglanegocio',
                              'tbl_reglanegocio.pcrc = tbl_base_aleatorio.arbol_id
                                    AND tbl_reglanegocio.id_formulario = tbl_base_aleatorio.form_id')
                        ->join('LEFT OUTER JOIN', 'tbl_arbols',
                              'tbl_arbols.id = tbl_reglanegocio.pcrc')
                        ->where(['=','tbl_base_aleatorio.anulado',0])
                        ->groupby(['tbl_arbols.id'])
                        ->all();

        $form = Yii::$app->request->post();
        if($model->load($form)){
            $varPcrc = $model->arbol_id;
            $varCliente = (new \yii\db\Query())
                            ->select(['tbl_arbols.formulario_id'])
                            ->from(['tbl_arbols'])
                            ->where(['=','tbl_arbols.id',$varPcrc])
                            ->scalar();

            $varValida = (new \yii\db\Query())
                            ->select(['tbl_base_aleatorio.id_aleatorio'])
                            ->from(['tbl_base_aleatorio'])
                            ->where(['=','tbl_base_aleatorio.arbol_id',$varPcrc])
                            ->andwhere(['=','tbl_base_aleatorio.anulado',0])
                            ->count();

            if ($varValida == 0) {
                Yii::$app->db->createCommand()->insert('tbl_base_aleatorio',[
                    'arbol_id' => $varPcrc,
                    'form_id' => $varCliente,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute(); 
            }            

            return $this->redirect('aleatorioencuestas');
        }

        return $this->render('aleatorioencuestas',[
            'model' => $model,
            'varListaAleatorio' => $varListaAleatorio,
        ]);
    }

    public function actionViewheroes(){

        $varFechaInicio_H = date('Y-m-d', strtotime('first day of last month'));
        
        $varFechaFin_H = date('Y-m-d', strtotime('last day of last month'));
        
        $varListadoHeroesValoraciones = (new \yii\db\Query())
                            ->select([
                                'ef.id',
                                'ef.created',
                                'tbl_arbols.name AS varProgramaPcrc',
                                'tbl_usuarios.usua_identificacion AS varCCValorador',
                                'tbl_usuarios.usua_nombre AS varValorador',
                                'tbl_evaluados.identificacion AS varCCAsesor',
                                'tbl_evaluados.name AS varAsesor',
                                '(
                                    SELECT 
                                        tbl_bloquedetalles.name 
                                    FROM tbl_bloquedetalles
                                        inner join tbl_bloques on
                                            tbl_bloques.id = tbl_bloquedetalles.bloque_id
                                        inner join tbl_ejecucionbloques on
                                            tbl_bloques.id = tbl_ejecucionbloques.bloque_id
                                        inner join tbl_ejecucionseccions on
                                            tbl_ejecucionbloques.ejecucionseccion_id = tbl_ejecucionseccions.id
                                        inner join tbl_seccions on
                                            tbl_seccions.id = tbl_ejecucionseccions.seccion_id
                                    WHERE
                                        tbl_bloques.nmorden = 1 
                                        and tbl_bloquedetalles.nmorden = 2 
                                        and tbl_seccions.nmorden = 1 
                                        and  tbl_ejecucionseccions.ejecucionformulario_id = ef.id) as Pregunta_Analista',
                                '(
                                    SELECT 
                                        tbl_calificaciondetalles.name 
                                    FROM tbl_calificaciondetalles
                                        inner join tbl_ejecucionbloquedetalles on
                                            tbl_ejecucionbloquedetalles.calificaciondetalle_id =tbl_calificaciondetalles.id
                                        inner join tbl_bloquedetalles on
                                            tbl_ejecucionbloquedetalles.bloquedetalle_id = tbl_bloquedetalles.id  
                                        inner join tbl_bloques on
                                            tbl_bloques.id = tbl_bloquedetalles.bloque_id
                                        inner join tbl_ejecucionbloques on
                                            tbl_bloques.id = tbl_ejecucionbloques.bloque_id AND tbl_ejecucionbloques.id = tbl_ejecucionbloquedetalles.ejecucionbloque_id
                                        inner join tbl_ejecucionseccions on
                                            tbl_ejecucionbloques.ejecucionseccion_id = tbl_ejecucionseccions.id
                                        inner join tbl_seccions on
                                            tbl_seccions.id = tbl_ejecucionseccions.seccion_id
                                    where
                                        tbl_seccions.nmorden = 1 
                                        and tbl_bloques.nmorden = 1 
                                        and tbl_bloquedetalles.nmorden = 2 
                                        and  tbl_ejecucionseccions.ejecucionformulario_id = ef.id) AS Respuesta_Analista',
                                '(
                                    SELECT 
                                        tbl_bloquedetalles.name 
                                    FROM tbl_bloquedetalles
                                        inner join tbl_bloques on
                                            tbl_bloques.id = tbl_bloquedetalles.bloque_id
                                        inner join tbl_ejecucionbloques on
                                            tbl_bloques.id = tbl_ejecucionbloques.bloque_id
                                        inner join tbl_ejecucionseccions on
                                            tbl_ejecucionbloques.ejecucionseccion_id = tbl_ejecucionseccions.id
                                        inner join tbl_seccions on
                                            tbl_seccions.id = tbl_ejecucionseccions.seccion_id
                                    where
                                        tbl_bloques.nmorden = 1 
                                        and tbl_bloquedetalles.nmorden = 1 
                                        and tbl_seccions.nmorden = 1 
                                        and  tbl_ejecucionseccions.ejecucionformulario_id = ef.id) as Pregunta_Operacion',
                                '(
                                    SELECT 
                                        tbl_calificaciondetalles.name 
                                    FROM tbl_calificaciondetalles
                                        INNER JOIN tbl_ejecucionbloquedetalles on
                                            tbl_ejecucionbloquedetalles.calificaciondetalle_id =tbl_calificaciondetalles.id
                                        INNER JOIN tbl_bloquedetalles on
                                            tbl_ejecucionbloquedetalles.bloquedetalle_id = tbl_bloquedetalles.id  
                                        inner join tbl_bloques on
                                            tbl_bloques.id = tbl_bloquedetalles.bloque_id
                                        inner join tbl_ejecucionbloques on
                                            tbl_bloques.id = tbl_ejecucionbloques.bloque_id AND tbl_ejecucionbloques.id = tbl_ejecucionbloquedetalles.ejecucionbloque_id
                                        inner join tbl_ejecucionseccions on
                                            tbl_ejecucionbloques.ejecucionseccion_id = tbl_ejecucionseccions.id
                                        inner join tbl_seccions on
                                            tbl_seccions.id = tbl_ejecucionseccions.seccion_id
                                        where
                                            tbl_seccions.nmorden = 1 
                                            and tbl_bloques.nmorden = 1 
                                            and tbl_bloquedetalles.nmorden = 1 
                                            and  tbl_ejecucionseccions.ejecucionformulario_id = ef.id) as Respuesta_Operacion',
                                '(
                                    SELECT 
                                        tbl_tipificaciondetalles.name 
                                    FROM tbl_ejecucionformularios
                                        LEFT JOIN tbl_ejecucionseccions ON 
                                            tbl_ejecucionseccions.ejecucionformulario_id = tbl_ejecucionformularios.id
                                        LEFT JOIN tbl_ejecucionbloques ON 
                                            tbl_ejecucionbloques.ejecucionseccion_id = tbl_ejecucionseccions.id
                                        LEFT JOIN tbl_ejecucionbloquedetalles ON 
                                            tbl_ejecucionbloquedetalles.ejecucionbloque_id = tbl_ejecucionbloques.id
                                        LEFT JOIN tbl_ejecucionbloquedetalles_tipificaciones ON 
                                            tbl_ejecucionbloquedetalles_tipificaciones.ejecucionbloquedetalle_id = tbl_ejecucionbloquedetalles.id
                                        LEFT JOIN tbl_tipificaciondetalles ON 
                                            tbl_tipificaciondetalles.id = tbl_ejecucionbloquedetalles_tipificaciones.tipificaciondetalle_id
                                    WHERE 
                                        tbl_ejecucionformularios.id = ef.id
                                    GROUP BY tbl_ejecucionformularios.id) as Cliente'
                            ])
                            ->from(['tbl_ejecucionformularios ef'])

                            ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                  'tbl_usuarios.usua_id = ef.usua_id')

                            ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                  'tbl_arbols.id = ef.arbol_id')

                            ->join('LEFT OUTER JOIN', 'tbl_evaluados',
                                  'tbl_evaluados.id = ef.evaluado_id')

                            ->where(['between','ef.created',$varFechaInicio_H.' 00:00:00',$varFechaFin_H.' 23:59:59'])
                            ->andwhere(['=','ef.arbol_id',2119])
                            ->all(); 


        return $this->render('viewheroes',[
            'varListadoHeroesValoraciones' => $varListadoHeroesValoraciones,
        ]);
    }

    /* MODULO PARAMETRIZADOR 
    PROCESO PARA GUARDAR O ELIMINAR LOS PCRC QUE SE USARÁN PARA EXPORTAR VALORACIONES MANUALES PARA COMDATA --------------------*/

    //Función que agregaR un PCRC
    public function actionParametrizarpcrcvaloracionescomdata() {

        $model = new ControlParams();
        $form = Yii::$app->request->post();
        
        if ($model->load($form)) {

            $varidArbol = $model->arbol_id; // id PCRC

            $existe_id_pcrc = (new \yii\db\Query())
            ->select(['id'])
            ->from(['tbl_control_valoraciones_comdata'])
            ->where(['=','arbol_id',$varidArbol])
            ->andwhere(['=','anulado', 0])
            ->count();

            // No existe el id del PCRC
            if($existe_id_pcrc==0){

                $insertar_datos = Yii::$app->db->createCommand()->insert('tbl_control_valoraciones_comdata',[
                    'arbol_id' => $varidArbol,
                    'fecha_creacion' => date("Y-m-d"),
                    'usua_id' => Yii::$app->user->identity->id,
                ])->execute();

                if($insertar_datos>0){
                    Yii::$app->session->setFlash('success_creacion', 'Creación exitosa.');        
                } else {
                    Yii::$app->session->setFlash('error_creacion', 'Error agregando los datos.');
                } 
            }

            // Ya existe en nuestra tabla el id_pcrc 
            if($existe_id_pcrc>0){
                Yii::$app->session->setFlash('error_creacion', 'Ya existe el Programa/PCRC.');
            }    

            return $this->redirect('parametrizarpcrcvaloracionescomdata');
            
        }

        return $this->render('parametrizarpcrcvaloracionescomdata',[
                'model' => $model,
        ]);

    }    

    //Funcion para obtener la lista de los PCRC agregados  
    public function actionCargarlistapcrcvaloracionescomdata() {

        $varListaPcrcs= Yii::$app->db->createCommand("
        SELECT DISTINCT (pcrc.arbol_id) AS id_pcrc, arbol.name AS nom_pcrc
        FROM tbl_control_valoraciones_comdata pcrc
        INNER JOIN tbl_arbols arbol ON arbol.id = pcrc.arbol_id 
        where anulado = 0")->queryAll();

        $response = [
            'status' => 'success',
            'data' => $varListaPcrcs,
        ];

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $response;
        
    }

    //Función para eliminar logicamente un PCRC
    public function actionDeletepcrcvaloracionescomdata() {
        $paramsEliminar = Yii::$app->request->get('id_pcrc');   

        $result = Yii::$app->db->createCommand('
            UPDATE tbl_control_valoraciones_comdata 
            SET anulado = :varAnulado
            WHERE 
            arbol_id = :VarId')
        ->bindValue(':VarId', $paramsEliminar)
        ->bindValue(':varAnulado', 1)
        ->execute();

        if ($result>0) {
            $response = [
                'status' => 'success',
                'data' => 'Programa/PCRC eliminado correctamente.',
                ];            
        } else {
            $response = [
                'status' => 'error',
                'data' => 'Error eliminando Programa/PCRC.',
                ];                        
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $response;
    }
    

  }

?>
