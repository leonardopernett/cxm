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
use app\models\BaseSatisfaccion; 
use PHPExcel;
use PHPExcel_IOFactory;
use GuzzleHttp;


  class ApitracksaleController extends \yii\web\Controller {

    public function behaviors(){
      return[
        'verbs' => [          
          'class' => VerbFilter::className(),
          'actions' => [
            'delete' => ['post'],
          ],
        ],

        'access' => [
            'class' => AccessControl::classname(),
            'denyCallback' => function ($rule, $action) {
                    $msg = \Yii::t('app', 'The requested Item could not be found.');
                    Yii::$app->session->setFlash('danger', $msg);
                    $url = \yii\helpers\Url::to(['site/index']);
                    return $action->controller->redirect($url);
            },

            
            'rules' => [
              [
                'actions' => ['apitrackencuestas','apitrackencuestasgeneral'],
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema();
                        },
              ],
              [
                'actions' => ['apitrackencuestas','apitrackencuestasgeneral'],
                'allow' => true,

              ],
            ],

        ],
        
      ];
    }
  
    public function init(){
      $this->enableCsrfValidation = false;
    }

    public function actionApitrackencuestasgeneral(){
      
      $varHora = date("H");

      $varHoraInicio = '00:00:00';
      $varHoraFin = '23:59:59';

      $varFecha = date("Y-m-d");      

      $varFechaInicioEspecial_BD = $varFecha.'T'.$varHoraInicio;
      $varFechaFinEspecial_BD = $varFecha.'T'.$varHoraFin;

      $varListadoTrack = (new \yii\db\Query())
                        ->select(['tbl_tracksale_parametrizarformulario.trackservicio'])
                        ->from(['tbl_tracksale_parametrizarformulario'])
                        ->where(['=','tbl_tracksale_parametrizarformulario.anulado',0])
                        ->all(); 

      $varArrayTrack = array();
      foreach ($varListadoTrack as $value) {
        array_push($varArrayTrack, $value['trackservicio']);
      }
      $varArraySale = implode(",", $varArrayTrack);

      ob_start();

      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_SSL_VERIFYPEER=> false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_URL => 'https://api.tracksale.co/v2/report/answer?start=2023-06-01T00:00:00&end=2023-06-04T23:59:59&codes='.$varArraySale.'&tags=true',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
          'Authorization: Bearer 3e4585e710cea793dbbbdfb6fbd21ea0'
        ),
      ));

      $response = curl_exec($curl);

      curl_close($curl);
      ob_clean();

      $varListaDatos = json_decode($response,true); 

      if (count($varListaDatos) != 0) {

        foreach ($varListaDatos as $value) {

          // Datos del Cliente
          $varIdentificacion = $value['id'];
          if ($varIdentificacion == "") {
            $varIdentificacion = "Sin información";
          }

          $varNombreCliente = $value['name'];

          // Datos del Asesor
          $varCantidades = count($value['tags']) - 1;
          $varAsesorRed =  $value['tags'][$varCantidades]['value'];

          $paramsBuscaAsesor = [':varAsesor'=>$varAsesorRed];
          $varDocAsesor = Yii::$app->dbjarvis->createCommand('
            SELECT du.documento FROM  dp_usuarios_red du 
              WHERE 
                du.usuario_red = :varAsesor ')->bindValues($paramsBuscaAsesor)->queryScalar();

          if ($varDocAsesor == '') {
            $varDocAsesor = Yii::$app->dbjarvis->createCommand('
              SELECT du.documento FROM  dp_usuarios_actualizacion du 
                WHERE 
                  du.usuario = :varAsesor ')->bindValues($paramsBuscaAsesor)->queryScalar();
          }

          // Datos fecha Satu
          $varTimes = $value['time'];
          $varSatuFechas = date("Y-m-d H:i:s", $varTimes);          

          // Datos de tiempos Año, Mes, Dia y Hora
          $varAnnio = date("Y", strtotime($varSatuFechas));
          $varMes = date("m", strtotime($varSatuFechas));
          $varDia = date("d", strtotime($varSatuFechas));
          $varHora = date("H", strtotime($varSatuFechas)).date("i", strtotime($varSatuFechas)).date("s", strtotime($varSatuFechas));

          // Datos RN, Cod_Ind, Cod_Ins, Pcrc y Cliente
          $varCampana = $value['campaign_code'];
          $varPcrc = (new \yii\db\Query())
                        ->select(['tbl_arbols.id'])
                        ->from(['tbl_arbols'])
                        ->join('INNER JOIN', 'tbl_tracksale_parametrizarformulario', 
                              'tbl_arbols.id = tbl_tracksale_parametrizarformulario.arbol_id')
                        ->where(['=','tbl_tracksale_parametrizarformulario.anulado',0])
                        ->andwhere(['=','tbl_tracksale_parametrizarformulario.trackservicio',$varCampana])
                        ->scalar();

          $varCliente =  (new \yii\db\Query())
                        ->select(['tbl_arbols.arbol_id'])
                        ->from(['tbl_arbols'])
                        ->where(['=','tbl_arbols.id',$varPcrc])
                        ->scalar();

          $varRn = (new \yii\db\Query())
                          ->select(['tbl_reglanegocio.rn'])
                          ->from(['tbl_reglanegocio']) 
                          ->where(['=','tbl_reglanegocio.pcrc',$varPcrc])
                          ->andwhere(['=','tbl_reglanegocio.cliente',$varCliente])
                          ->groupby(['tbl_reglanegocio.pcrc'])
                          ->scalar();

          $varConInstitucion = (new \yii\db\Query())
                          ->select(['tbl_reglanegocio.cod_institucion'])
                          ->from(['tbl_reglanegocio']) 
                          ->where(['=','tbl_reglanegocio.pcrc',$varPcrc])
                          ->andwhere(['=','tbl_reglanegocio.cliente',$varCliente])
                          ->groupby(['tbl_reglanegocio.pcrc'])
                          ->scalar();

          $varConIndustria = (new \yii\db\Query())
                          ->select(['tbl_reglanegocio.cod_industria'])
                          ->from(['tbl_reglanegocio']) 
                          ->where(['=','tbl_reglanegocio.pcrc',$varPcrc])
                          ->andwhere(['=','tbl_reglanegocio.cliente',$varCliente])
                          ->groupby(['tbl_reglanegocio.pcrc'])
                          ->scalar();

          // Tipo de servicio
          $varServicio = 'TRA';

          // Datos para obtener la respuesta de la encuesta. Por el momento solo se tiene uno solo
          $varPregunta_Uno = $value['nps_answer'];

          // Datos para obtener el connid - Por ahora se concatena hasta que se tenga un id de la llamada.
          $varConnid = '000000'.$varTimes;

          // Datos para obtener los comentarios.
          $varComentarios = $value['nps_comment'];

          // Datos para buscar el lider y equipo
          $varUsuaLider = null;
          $varLider = null;
          $varCCLider = null;
          if (count($varDocAsesor) != 0) {
            $varListaEquipos = (new \yii\db\Query())
                                  ->select([
                                      'tbl_usuarios.usua_id', 'tbl_usuarios.usua_nombre', 'tbl_usuarios.usua_identificacion'
                                  ])
                                  ->from(['tbl_usuarios'])
                                  ->join('LEFT OUTER JOIN', 'tbl_equipos',
                                        'tbl_usuarios.usua_id = tbl_equipos.usua_id')
                                  ->join('LEFT OUTER JOIN', 'tbl_equipos_evaluados',
                                        'tbl_equipos.id = tbl_equipos_evaluados.equipo_id')
                                  ->join('LEFT OUTER JOIN', 'tbl_evaluados',
                                        'tbl_equipos_evaluados.evaluado_id = tbl_evaluados.id')
                                  ->where(['=','tbl_evaluados.identificacion',$varDocAsesor])
                                  ->all();

            foreach ($varListaEquipos as $key => $value) {
              $varUsuaLider = $value['usua_id'];
              $varLider = $value['usua_nombre'];
              $varCCLider = $value['usua_identificacion'];
            }
          }

          $varNoAplica = "NO APLICA";

          $varVerificaConnid = (new \yii\db\Query())
                                  ->select([
                                      'tbl_base_satisfaccion.id'
                                  ])
                                  ->from(['tbl_base_satisfaccion'])
                                  ->where(['=','tbl_base_satisfaccion.ani','TrackSale'])
                                  ->andwhere(['=','tbl_base_satisfaccion.connid',$varConnid])
                                  ->count();

          if ($varVerificaConnid == 0) {

            // Aqui se guarda novedad de asesor
            if ($varDocAsesor == '') {
              Yii::$app->db->createCommand()->insert('tbl_tracksale_novedades',[
                        'tracksale' => $varAsesorRed,
                        'id_novedad' => 1,
                        'motivo_novedad' => 'Asesor no encontrado en Base de Jarvis. Dato relacional '.$varTimes,
                        'anulado' => 0,
                        'usua_id' => 1,
                        'fechacreacion' => date('Y-m-d'),
              ])->execute();
            }

            if ($varPregunta_Uno <= '10') {
              Yii::$app->db->createCommand()->insert('tbl_base_satisfaccion',[
                        'identificacion' => $varIdentificacion,
                        'nombre' => $varNombreCliente,
                        'ani' => 'TrackSale',
                        'agente' => $varAsesorRed,
                        'cc_agente' => $varDocAsesor,
                        'agente2' => null,
                        'ano' => $varAnnio,
                        'mes' => $varMes,
                        'dia' => $varDia,
                        'hora' => $varHora,
                        'chat_transfer' => null,
                        'ext' => null,
                        'rn' => $varRn,
                        'industria' => $varConIndustria,
                        'institucion' => $varConInstitucion,
                        'pcrc' => $varPcrc,
                        'cliente' => $varCliente,
                        'tipo_servicio' => $varServicio,
                        'pregunta1' => $varPregunta_Uno,
                        'pregunta2' => $varNoAplica,
                        'pregunta3' => $varNoAplica,
                        'pregunta4' => $varNoAplica,
                        'pregunta5' => $varNoAplica,
                        'pregunta6' => $varNoAplica,
                        'pregunta7' => $varNoAplica,
                        'pregunta8' => $varNoAplica,
                        'pregunta9' => $varNoAplica,
                        'pregunta10' => $varNoAplica,
                        'connid' => $varConnid,
                        'tipo_encuesta' => 'A',
                        'comentario' => $varComentarios,
                        'id_lider_equipo' => $varUsuaLider,
                        'lider_equipo' => $varLider,
                        'cc_lider' => $varCCLider,
                        'coordinador' => null,
                        'jefe_operaciones' => null,
                        'tipologia' => null,
                        'estado' => 'Abierto',
                        'llamada' => null,
                        'buzon' => null,
                        'responsable' => null,
                        'usado' => 'NO',
                        'fecha_gestion' => $varSatuFechas,
                        'created' => date("Y-m-d h:i:s"),
                        'tipo_inbox' => 'NORMAL',
                        'responsabilidad' => null,
                        'canal' => null,
                        'marca' => null,
                        'equivocacion' => null,
                        'fecha_satu' => $varSatuFechas,
                        'aliados' => 'NAT',
                        'modalidad_encuesta' => null,
              ])->execute();

              Yii::$app->db->createCommand()->insert('tbl_tracksale_baseencuesta',[
                        'arbol_id' => $varPcrc,
                        'cliente' => $varCliente,
                        'id_trackservicio' => $varCampana,
                        'id_trackid' => $varTimes,
                        'time' => $varSatuFechas,                      
                        'anulado' => 0,
                        'usua_id' => 1,
                        'fechacreacion' => date('Y-m-d'),
              ])->execute();

            }else{
              Yii::$app->db->createCommand()->insert('tbl_tracksale_novedades',[
                        'tracksale' => $varAsesorRed,
                        'id_novedad' => 2,
                        'motivo_novedad' => 'Respuesta no concueda con lo parametrizado en la encuesta. Dato relacional '.$varTimes,
                        'anulado' => 0,
                        'usua_id' => 1,
                        'fechacreacion' => date('Y-m-d'),
              ])->execute();
            }
          }         

          
        }       

      }

      $this->Generarrecalculartipologia();

      die();
    }

    public function actionApitrackencuestas(){
      $datapost = file_get_contents('php://input');
      $data_post = json_decode($datapost,true);

      ini_set("max_execution_time", "900");
      ini_set("memory_limit", "1024M");
      ini_set( 'post_max_size', '1024M' );

      ignore_user_abort(true);
      set_time_limit(900);

      $varHora = date("H");

      $varHoraInicio = strval(intval($varHora) - 1).':00:00';
      $varHoraFin = strval(intval($varHora) - 1).':59:59';

      if ($varHoraFin == '23:59:59') {
        $varDias = strval(intval(date("d") -1));
        $varFecha = date("Y-m-").$varDias;
      }else{
        $varFecha = date("Y-m-d");
      }

      $varFechaInicioEspecial_BD = $varFecha.'T'.$varHoraInicio;
      $varFechaFinEspecial_BD = $varFecha.'T'.$varHoraFin;

      $varListadoTrack = (new \yii\db\Query())
                        ->select(['tbl_tracksale_parametrizarformulario.trackservicio'])
                        ->from(['tbl_tracksale_parametrizarformulario'])
                        ->where(['=','tbl_tracksale_parametrizarformulario.anulado',0])
                        ->all(); 

      $varArrayTrack = array();
      foreach ($varListadoTrack as $value) {
        array_push($varArrayTrack, $value['trackservicio']);
      }
      $varArraySale = implode(",", $varArrayTrack);

      ob_start();

      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_SSL_VERIFYPEER=> false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_URL => 'https://api.tracksale.co/v2/report/answer?start=2023-06-01T00:00:00&end=2023-06-04T23:59:59&codes='.$varArraySale.'&tags=true',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
          'Authorization: Bearer 3e4585e710cea793dbbbdfb6fbd21ea0'
        ),
      ));

      $response = curl_exec($curl);

      curl_close($curl);
      ob_clean();

      $varListaDatos = json_decode($response,true); 

      if (count($varListaDatos) != 0) {

        foreach ($varListaDatos as $value) {

          // Datos del Cliente
          $varIdentificacion = $value['id'];
          if ($varIdentificacion == "") {
            $varIdentificacion = "Sin información";
          }

          $varNombreCliente = $value['name'];

          // Datos del Asesor
          $varCantidades = count($value['tags']) - 1;
          $varAsesorRed =  $value['tags'][$varCantidades]['value'];

          $paramsBuscaAsesor = [':varAsesor'=>$varAsesorRed];
          $varDocAsesor = Yii::$app->dbjarvis->createCommand('
            SELECT du.documento FROM  dp_usuarios_red du 
              WHERE 
                du.usuario_red = :varAsesor ')->bindValues($paramsBuscaAsesor)->queryScalar();

          if ($varDocAsesor == '') {
            $varDocAsesor = Yii::$app->dbjarvis->createCommand('
              SELECT du.documento FROM  dp_usuarios_actualizacion du 
                WHERE 
                  du.usuario = :varAsesor ')->bindValues($paramsBuscaAsesor)->queryScalar();
          }

          // Datos fecha Satu
          $varTimes = $value['time'];
          $varSatuFechas = date("Y-m-d H:i:s", $varTimes);          

          // Datos de tiempos Año, Mes, Dia y Hora
          $varAnnio = date("Y", strtotime($varSatuFechas));
          $varMes = date("m", strtotime($varSatuFechas));
          $varDia = date("d", strtotime($varSatuFechas));
          $varHora = date("H", strtotime($varSatuFechas)).date("i", strtotime($varSatuFechas)).date("s", strtotime($varSatuFechas));

          // Datos RN, Cod_Ind, Cod_Ins, Pcrc y Cliente
          $varCampana = $value['campaign_code'];
          $varPcrc = (new \yii\db\Query())
                        ->select(['tbl_arbols.id'])
                        ->from(['tbl_arbols'])
                        ->join('INNER JOIN', 'tbl_tracksale_parametrizarformulario', 
                              'tbl_arbols.id = tbl_tracksale_parametrizarformulario.arbol_id')
                        ->where(['=','tbl_tracksale_parametrizarformulario.anulado',0])
                        ->andwhere(['=','tbl_tracksale_parametrizarformulario.trackservicio',$varCampana])
                        ->scalar();

          $varCliente =  (new \yii\db\Query())
                        ->select(['tbl_arbols.arbol_id'])
                        ->from(['tbl_arbols'])
                        ->where(['=','tbl_arbols.id',$varPcrc])
                        ->scalar();

          $varRn = (new \yii\db\Query())
                          ->select(['tbl_reglanegocio.rn'])
                          ->from(['tbl_reglanegocio']) 
                          ->where(['=','tbl_reglanegocio.pcrc',$varPcrc])
                          ->andwhere(['=','tbl_reglanegocio.cliente',$varCliente])
                          ->groupby(['tbl_reglanegocio.pcrc'])
                          ->scalar();

          $varConInstitucion = (new \yii\db\Query())
                          ->select(['tbl_reglanegocio.cod_institucion'])
                          ->from(['tbl_reglanegocio']) 
                          ->where(['=','tbl_reglanegocio.pcrc',$varPcrc])
                          ->andwhere(['=','tbl_reglanegocio.cliente',$varCliente])
                          ->groupby(['tbl_reglanegocio.pcrc'])
                          ->scalar();

          $varConIndustria = (new \yii\db\Query())
                          ->select(['tbl_reglanegocio.cod_industria'])
                          ->from(['tbl_reglanegocio']) 
                          ->where(['=','tbl_reglanegocio.pcrc',$varPcrc])
                          ->andwhere(['=','tbl_reglanegocio.cliente',$varCliente])
                          ->groupby(['tbl_reglanegocio.pcrc'])
                          ->scalar();

          // Tipo de servicio
          $varServicio = 'TRA';

          // Datos para obtener la respuesta de la encuesta. Por el momento solo se tiene uno solo
          $varPregunta_Uno = $value['nps_answer'];

          // Datos para obtener el connid - Por ahora se concatena hasta que se tenga un id de la llamada.
          $varConnid = '000000'.$varTimes;

          // Datos para obtener los comentarios.
          $varComentarios = $value['nps_comment'];

          // Datos para buscar el lider y equipo
          $varUsuaLider = null;
          $varLider = null;
          $varCCLider = null;
          if (count($varDocAsesor) != 0) {
            $varListaEquipos = (new \yii\db\Query())
                                  ->select([
                                      'tbl_usuarios.usua_id', 'tbl_usuarios.usua_nombre', 'tbl_usuarios.usua_identificacion'
                                  ])
                                  ->from(['tbl_usuarios'])
                                  ->join('LEFT OUTER JOIN', 'tbl_equipos',
                                        'tbl_usuarios.usua_id = tbl_equipos.usua_id')
                                  ->join('LEFT OUTER JOIN', 'tbl_equipos_evaluados',
                                        'tbl_equipos.id = tbl_equipos_evaluados.equipo_id')
                                  ->join('LEFT OUTER JOIN', 'tbl_evaluados',
                                        'tbl_equipos_evaluados.evaluado_id = tbl_evaluados.id')
                                  ->where(['=','tbl_evaluados.identificacion',$varDocAsesor])
                                  ->all();

            foreach ($varListaEquipos as $key => $value) {
              $varUsuaLider = $value['usua_id'];
              $varLider = $value['usua_nombre'];
              $varCCLider = $value['usua_identificacion'];
            }
          }

          $varNoAplica = "NO APLICA";

          $varVerificaConnid = (new \yii\db\Query())
                                  ->select([
                                      'tbl_base_satisfaccion.id'
                                  ])
                                  ->from(['tbl_base_satisfaccion'])
                                  ->where(['=','tbl_base_satisfaccion.ani','TrackSale'])
                                  ->andwhere(['=','tbl_base_satisfaccion.connid',$varConnid])
                                  ->count();

          if ($varVerificaConnid == 0) {

            // Aqui se guarda novedad de asesor
            if ($varDocAsesor == '') {
              Yii::$app->db->createCommand()->insert('tbl_tracksale_novedades',[
                        'tracksale' => $varAsesorRed,
                        'id_novedad' => 1,
                        'motivo_novedad' => 'Asesor no encontrado en Base de Jarvis. Dato relacional '.$varTimes,
                        'anulado' => 0,
                        'usua_id' => 1,
                        'fechacreacion' => date('Y-m-d'),
              ])->execute();
            }

            if ($varPregunta_Uno <= '10') {
              Yii::$app->db->createCommand()->insert('tbl_base_satisfaccion',[
                        'identificacion' => $varIdentificacion,
                        'nombre' => $varNombreCliente,
                        'ani' => 'TrackSale',
                        'agente' => $varAsesorRed,
                        'cc_agente' => $varDocAsesor,
                        'agente2' => null,
                        'ano' => $varAnnio,
                        'mes' => $varMes,
                        'dia' => $varDia,
                        'hora' => $varHora,
                        'chat_transfer' => null,
                        'ext' => null,
                        'rn' => $varRn,
                        'industria' => $varConIndustria,
                        'institucion' => $varConInstitucion,
                        'pcrc' => $varPcrc,
                        'cliente' => $varCliente,
                        'tipo_servicio' => $varServicio,
                        'pregunta1' => $varPregunta_Uno,
                        'pregunta2' => $varNoAplica,
                        'pregunta3' => $varNoAplica,
                        'pregunta4' => $varNoAplica,
                        'pregunta5' => $varNoAplica,
                        'pregunta6' => $varNoAplica,
                        'pregunta7' => $varNoAplica,
                        'pregunta8' => $varNoAplica,
                        'pregunta9' => $varNoAplica,
                        'pregunta10' => $varNoAplica,
                        'connid' => $varConnid,
                        'tipo_encuesta' => 'A',
                        'comentario' => $varComentarios,
                        'id_lider_equipo' => $varUsuaLider,
                        'lider_equipo' => $varLider,
                        'cc_lider' => $varCCLider,
                        'coordinador' => null,
                        'jefe_operaciones' => null,
                        'tipologia' => null,
                        'estado' => 'Abierto',
                        'llamada' => null,
                        'buzon' => null,
                        'responsable' => null,
                        'usado' => 'NO',
                        'fecha_gestion' => $varSatuFechas,
                        'created' => date("Y-m-d h:i:s"),
                        'tipo_inbox' => 'NORMAL',
                        'responsabilidad' => null,
                        'canal' => null,
                        'marca' => null,
                        'equivocacion' => null,
                        'fecha_satu' => $varSatuFechas,
                        'aliados' => 'NAT',
                        'modalidad_encuesta' => null,
              ])->execute();

              Yii::$app->db->createCommand()->insert('tbl_tracksale_baseencuesta',[
                        'arbol_id' => $varPcrc,
                        'cliente' => $varCliente,
                        'id_trackservicio' => $varCampana,
                        'id_trackid' => $varTimes,
                        'time' => $varSatuFechas,                      
                        'anulado' => 0,
                        'usua_id' => 1,
                        'fechacreacion' => date('Y-m-d'),
              ])->execute();

            }else{
              Yii::$app->db->createCommand()->insert('tbl_tracksale_novedades',[
                        'tracksale' => $varAsesorRed,
                        'id_novedad' => 2,
                        'motivo_novedad' => 'Respuesta no concueda con lo parametrizado en la encuesta. Dato relacional '.$varTimes,
                        'anulado' => 0,
                        'usua_id' => 1,
                        'fechacreacion' => date('Y-m-d'),
              ])->execute();
            }
          }         

          
        }       

      }

      $this->Generarrecalculartipologia();

      die();
    }

    public function Generarrecalculartipologia(){

      //TRAIGO LAS ENCUESTAS SIN TIPOLOGIA
      $encuestas = BaseSatisfaccion::find()->select('id')->where("`tipologia` IS NULL")->all();

      if (empty($encuestas)) {
        echo "No hay procesos de actualizacion de tipologias ya que no se encontraron nuevas encuestas con tipologia null";
        exit;
      }
      
      //MENSAJE DE CONTROL                               
      $errores = "";
      foreach ($encuestas as $encuesta) {
        
        $model = BaseSatisfaccion::findOne($encuesta->id);
        $model->tipologia = 'NEUTRO';

        if (!empty($model->pcrc) && !empty($model->cliente)) {
          $sql = '
            SELECT ca.nombre, dp.categoria, p.pre_indicador, 
              dp.configuracion, dp.addNA, cg.name, cg.prioridad, cg.id
            FROM tbl_detalleparametrizacion dp
              JOIN tbl_categoriagestion cg 
                ON dp.id_categoriagestion = cg.id
              JOIN tbl_parametrizacion_encuesta pe 
                ON pe.id = cg.id_parametrizacion
              LEFT JOIN tbl_preguntas p 
                ON p.id_parametrizacion = pe.id 
                  AND p.categoria = dp.categoria
              JOIN tbl_categorias ca ON ca.id = dp.categoria 
            WHERE 
              pe.cliente = ' . $model->cliente
                . ' AND pe.programa = ' . $model->pcrc;
          
          $config = \Yii::$app->db->createCommand($sql)->queryAll();

          $prioridades = ArrayHelper::map($config, 'prioridad', 'name');
          $arrayCumpleRegla = [];

          if (count($config) > 0) {
            $conditon = '';
            $comando = '';
            $i = 1;
            $errorConfig = false;
            
            //Validamos si hay una mala configuracion-------------------                    
            foreach ($config as $value) {
              if (is_null($value['pre_indicador']) || empty($value['pre_indicador'])) {
                $errorConfig = true;
                \Yii::error('Categoria (' . $value['nombre']
                . '), Cliente(' . $model->cliente0->name
                . ') Programa(' . $model->pcrc0->name
                . ')  mal configuada, Por favor revise la '
                . 'configuracion', 'basesatisfaccion');
              }
            }

            if (!$errorConfig) {
              
              foreach ($config as $value) {
              
                if (!empty($value['configuracion'])) {
                  
                  $preExplode = explode('-', $value['configuracion']);
                  $explode = explode('||', $preExplode[0]);
                  
                  if (isset($explode[0]) && isset($explode[1]) && isset($explode[2])) {

                    if (str_replace(['(', ')'], '', $explode[0]) == BaseSatisfaccion::OP_AND) {
                    
                      if (isset($preExplode[1])) {
                      
                        $explodeAddNA = explode('||', $preExplode[1]);
                        $tmpConditon = ' && ($model->'
                          . $value['pre_indicador']
                          . ' '
                          . $explode[1]
                          . ' "'
                          . $explode[2] . '" ';
                          
                        $tmpConditon .= ' || $model->'
                          . $value['pre_indicador']
                          . ' '
                          . $explodeAddNA[1]
                          . ' "'
                          . $explodeAddNA[2] . '" )';
                      
                      } else {
                        
                        $tmpConditon = ' && (is_numeric($model->' . $value['pre_indicador'] . ') && $model->'
                          . $value['pre_indicador']
                          . ' '
                          . $explode[1]
                          . ' "'
                          . $explode[2] . '") ';
                      }
                      
                    } else {
                      
                      $tmpConditon = ' || $model->'
                        . $value['pre_indicador']
                        . ' '
                        . $explode[1]
                        . ' "'
                        . $explode[2] . '" ';
                    
                    }

                    if (!isset($config[$i]['id']) || $value['id'] != $config[$i]['id']) {
                    
                      $conditon = substr($conditon, 4);
                      
                      if (!$conditon) {
                        $tmpConditon = substr($tmpConditon, 4);
                      }
                      
                      $eval = '('
                        . $conditon
                        . $tmpConditon
                        . ')';
                        
                      $conditon = '';
                      
                      $comando .= '#####' . $eval;
                        eval("\$restCond = $eval;");
                      
                      if ($restCond) {
                        
                        $arrayCumpleRegla[] = 'true';
                        $model->tipologia = $value['name'];
                      
                      } else {
                        
                        $arrayCumpleRegla[] = 'false';
                      
                      }
                                            
                    } else {
                      
                      $conditon .= $tmpConditon;
                    
                    }
                    
                  }
                
                }
                
                $i++;
              
              }

              $contarValores = array_count_values($arrayCumpleRegla);

              //Contamos el numero de true en $arrayCumpleRegla---------------
              if (isset($contarValores['true']) && $contarValores['true'] > 1) {
                //sacamos el que tenga prioridad mas alta ------------------                    
                $model->tipologia = $prioridades[min(array_keys($prioridades))];
              }
            }
          }
        }

        
        //GUARDAMOS LOS DATOS-----------------------------------------------
        if (!$model->save()) {
          $errores .= "<br />ID Encuesta: " . $encuesta->id . "<br />";
        }

      }

      if (!empty($errores)) {
      
        echo '<div style="background-color: #f2dede; '
        . 'border-color: #ebccd1; color: #a94442;'
        . ' padding: 10px;">'
        . 'ENCUESTAS QUE NO PUDIERON SER RECALCULADAS: '
        . $errores
        . '</div>';
        
      } else {
      
        echo '<div style="background-color: #dff0d8; '
        . 'border-color: #d6e9c6; color: #3c763d;'
        . ' padding: 10px;">'
        . 'PROCESO TERMINADO CON &Eacute;XITO'
        . '</div>';
      
      }

    }

  }

?>
