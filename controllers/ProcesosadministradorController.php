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
use \yii\base\Exception;


  class ProcesosadministradorController extends \yii\web\Controller {

    public function behaviors(){
      return[
        'access' => [
            'class' => AccessControl::classname(),
            'only' => ['index','viewresponsability','categoriascxm','viewescucharmas','deletepermisos','viewusuariosencuestas','importarusuarios','deletesip','buscarurls','calcularurls','parametrizarplan','deletecontrol'],
            'rules' => [
              [
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema();
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

        ini_set("max_execution_time", "900");
        ini_set("memory_limit", "1024M");
        ini_set( 'post_max_size', '1024M' );

        ignore_user_abort(true);
        set_time_limit(900);

        $paramsBuscar = [':varFechaInicio' => $varFechaInicio,':varFechaFin' => $varFechaFin];
        $varlista = Yii::$app->db->createCommand('
            SELECT b.connid, b.fecha_satu FROM tbl_base_satisfaccion b 
                WHERE 
                    b.buzon LIKE "%/srv/www/htdocs/qa_managementv2/web/buzones_qa%"
                        AND b.fecha_satu BETWEEN :varFechaInicio AND :varFechaFin')->bindValues($paramsBuscar)->queryAll();


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
                  CURLOPT_URL => 'https://api-kaliope.analiticagrupokonectacloud.com/status-by-connid',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS =>'{"connid": "'.$txtvaridruta.'"}',
                  CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                  ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);
                ob_clean();                

                $response = json_decode(iconv( "Windows-1252", "UTF-8", $response ),true);

                if (!empty($response)) {
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
    

  }

?>
