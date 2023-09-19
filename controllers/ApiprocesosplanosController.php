<?php

namespace app\controllers;


use Yii;
use DateTime;
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
use app\models\UploadForm2;
use app\models\ControlValoracionesComdata;


  class ApiprocesosplanosController extends \yii\web\Controller {

    public $acentos = array(":");
    public $sinAcentos = array("");

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
                'actions' => ['procesosplanos'],
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema();
                        },
              ],
              [
                'actions' => ['procesosplanos'],
                'allow' => true,

              ],
            ],

        ],

           


        
      ];
    }
  
    public function init(){
      $this->enableCsrfValidation = false;
    }

    public function actionProcesosplanos() {
        set_time_limit(0);

        $varFechainicio_P = date('Y-m-d H:00:00', strtotime('-1 hour'));
        $varFechafin_P = date('Y-m-d H:59:59', strtotime('-1 hour'));
        $varHora = date('H', strtotime('-1 hour'));

        //Obtener lista de PCRC para exportar
        $array_pcrc = (new \yii\db\Query())
                    ->select('arbol_id')
                    ->from('tbl_control_valoraciones_comdata')
                    ->where(['anulado' => 0])
                    ->all();
                            
	    //controlar que no existen fechas null 
        $txtidformularios = Yii::$app->db->createCommand("Select id from tbl_ejecucionformularios WHERE hora_final IS null")->queryAll();
        if ($txtidformularios){
            foreach ($txtidformularios as $key => $value) {
                Yii::$app->db->createCommand()->update('tbl_ejecucionformularios',[
                    'hora_final' => '0000-00-00:00:00:00',
                ],'id ='.$value['id'] .'')->execute();
            }
        }
        //fechas null

        //inicio foreach array_pcrc
        foreach ($array_pcrc as $value) {

            $dataProvider = array();
            
            $arbol_id = $value['arbol_id'];

            /* Inicio Variables */
            //INICIO DE TRANSPOSICION DE DATOS
            $textos = $this->getTextosPreguntas(); 

            $titulos = array();
            // Control del Formulario
            $fid = -1;
            // Control de la seccion
            $sid = -1;
            // Control del bloque
            $did = -1;
            $cdpregunta = -1;
            $cdtipificacion = -1;

            // Variables de control
            $export = false;

            /* Archivos */
            $fileName = Yii::$app->basePath . DIRECTORY_SEPARATOR . "web" .
                    DIRECTORY_SEPARATOR . "valoracionescxm_comdata" . DIRECTORY_SEPARATOR
                    . Yii::t('app', 'Reporte_extractar') . '_' . date('Ymd') . "_" .
                    $varHora . "_" . $arbol_id . ".xlsx";
            
            /* Titulos */
            $titulos[0] = ['header' => 'Fecha y Hora', 'value' => '0'];
            $titulos[1] = ['header' => 'Hora Inicial Valoracion', 'value' => '1'];
            $titulos[2] = ['header' => 'Hora Final Valoracion', 'value' => '2'];
            $titulos[3] = ['header' => 'Tiempo total Valoracion', 'value' => '3'];
            $titulos[4] = ['header' => 'Hora Inicial Modificacion', 'value' => '4'];
            $titulos[5] = ['header' => 'Hora Final Modificacion', 'value' => '5'];

            $titulos[6] = ['header' => 'Dimension', 'value' => '6'];
            $titulos[7] = ['header' => 'Programa/PCRC Padre', 'value' => '7'];
            $titulos[8] = ['header' => 'Programa/PCRC', 'value' => '8'];
            $titulos[9] = ['header' => 'Formulario', 'value' => '9'];
            $titulos[10] = ['header' => 'Cedula Valorado', 'value' => '10'];
            $titulos[11] = ['header' => 'Valorado', 'value' => '11'];
            $titulos[12] = ['header' => 'Responsable', 'value' => '12'];
            $titulos[13] = ['header' => 'Valorador', 'value' => '13'];
            $titulos[14] = ['header' => 'Rol', 'value' => '14'];
            $titulos[15] = ['header' => 'Fuente', 'value' => '15'];
            $titulos[16] = ['header' => 'Transaccion', 'value' => '16'];
            $titulos[17] = ['header' => 'Equipo', 'value' => '17'];
            $titulos[18] = ['header' => 'Comentario', 'value' => '18'];

            if($arbol_id == '3104') {
                $titulos[19] = ['header' => 'ANI', 'value' => '19'];
                $titulos[20] = ['header' => 'Nombre', 'value' => '20'];
                $titulos[21] = ['header' => 'Identificacion', 'value' => '21'];
                $titulos[22] = ['header' => 'RN', 'value' => '22'];
                $titulos[23] = ['header' => 'Tipologia', 'value' => '23'];
                $titulos[24] = ['header' => 'En una escala del 0 al 10 - Cuan probable es que recomiendes al Call Center de Avon a un amigo o familia', 'value' => '24'];
                $titulos[25] = ['header' => 'Motivo contacto', 'value' => '25'];
                $titulos[26] = ['header' => 'Etapa del viaje', 'value' => '26'];
                $titulos[27] = ['header' => 'ID Encuesta', 'value' => '27'];
                $titulos[28] = ['header' => 'Email', 'value' => '28'];
                $titulos[29] = ['header' => 'Usuario COSV', 'value' => '29'];
                $titulos[30] = ['header' => 'Servicio Konecta', 'value' => '30'];
                $titulos[31] = ['header' => 'Nota anterior NPS', 'value' => '31'];
                $titulos[32] = ['header' => 'Valoracion Adicional y/o Escalada', 'value' => '32'];
                $titulos[33] = ['header' => 'Score', 'value' => '33'];        
                $titulos[34] = ['header' => $textos[0]['titulo'], 'value' => '34'];
                $titulos[35] = ['header' => $textos[1]['titulo'], 'value' => '35'];
                $titulos[36] = ['header' => $textos[2]['titulo'], 'value' => '36'];
                $titulos[37] = ['header' => $textos[3]['titulo'], 'value' => '37'];
                $titulos[38] = ['header' => $textos[4]['titulo'], 'value' => '38'];
                $titulos[39] = ['header' => $textos[5]['titulo'], 'value' => '39'];
                $titulos[40] = ['header' => $textos[6]['titulo'], 'value' => '40'];
                $titulos[41] = ['header' => $textos[7]['titulo'], 'value' => '41'];
                $titulos[42] = ['header' => $textos[8]['titulo'], 'value' => '42'];
                $titulos[43] = ['header' => $textos[9]['titulo'], 'value' => '43'];
            }
            else{
                $titulos[19] = ['header' => 'Valoracion Adicional y/o Escalada', 'value' => '19'];
                $titulos[20] = ['header' => 'Score', 'value' => '20'];        
                $titulos[21] = ['header' => $textos[0]['titulo'], 'value' => '21'];
                $titulos[22] = ['header' => $textos[1]['titulo'], 'value' => '22'];
                $titulos[23] = ['header' => $textos[2]['titulo'], 'value' => '23'];
                $titulos[24] = ['header' => $textos[3]['titulo'], 'value' => '24'];
                $titulos[25] = ['header' => $textos[4]['titulo'], 'value' => '25'];
                $titulos[26] = ['header' => $textos[5]['titulo'], 'value' => '26'];
                $titulos[27] = ['header' => $textos[6]['titulo'], 'value' => '27'];
                $titulos[28] = ['header' => $textos[7]['titulo'], 'value' => '28'];
                $titulos[29] = ['header' => $textos[8]['titulo'], 'value' => '29'];
                $titulos[30] = ['header' => $textos[9]['titulo'], 'value' => '30'];
            }

            // Generar los tituloos
            $filecontent = "";

            //QUERY COMPLETO SIN PARTIR POR LIMITES
            $sql = "SELECT f.created 'Fecha' ,f.id fid ,s.id 'sid' , xb.id 'did', xd.id 'cdPregunta', xd.tipificacion_id 'idTipi', 
                    xtd.id 'cdTipificacionDetalle', t.tipificaciondetalle_id, xdim.name 'Dimension', 
                    xarbol_padre.name 'ArbolPadre',xarbol.name 'Arbol',xf.name 'Formulario', f.evaluado_id, f.ejec_principal, f.estado ,
                    f.i1_nmcalculo 'fi1_nmcalculo', f.i2_nmcalculo 'fi2_nmcalculo', f.i3_nmcalculo 'fi3_nmcalculo', 
                    f.i4_nmcalculo 'fi4_nmcalculo', f.i5_nmcalculo 'fi5_nmcalculo', f.i6_nmcalculo 'fi6_nmcalculo', 
                    f.i7_nmcalculo 'fi7_nmcalculo', f.i8_nmcalculo 'fi8_nmcalculo', f.i9_nmcalculo 'fi9_nmcalculo', 
                    f.i10_nmcalculo 'fi10_nmcalculo', f.i1_nmfactor 'fi1_nmfactor', f.i2_nmfactor 'fi2_nmfactor', 
                    f.i3_nmfactor 'fi3_nmfactor', f.i4_nmfactor 'fi4_nmfactor', f.i5_nmfactor 'fi5_nmfactor', 
                    f.i6_nmfactor 'fi6_nmfactor', f.i7_nmfactor 'fi7_nmfactor', f.i8_nmfactor 'fi8_nmfactor', 
                    f.i9_nmfactor 'fi9_nmfactor', f.i10_nmfactor 'fi10_nmfactor',
                    xs.name 'Seccion',
                    s.i1_nmcalculo 'si1_nmcalculo', s.i2_nmcalculo 'si2_nmcalculo', s.i3_nmcalculo 'si3_nmcalculo', 
                    s.i4_nmcalculo 'si4_nmcalculo', s.i5_nmcalculo 'si5_nmcalculo', s.i6_nmcalculo 'si6_nmcalculo', 
                    s.i7_nmcalculo 'si7_nmcalculo', s.i8_nmcalculo 'si8_nmcalculo', s.i9_nmcalculo 'si9_nmcalculo', 
                    s.i10_nmcalculo 'si10_nmcalculo', s.i1_nmfactor 'si1_nmfactor', s.i2_nmfactor 'si2_nmfactor', 
                    s.i3_nmfactor 'si3_nmfactor', s.i4_nmfactor 'si4_nmfactor', s.i5_nmfactor 'si5_nmfactor', 
                    s.i6_nmfactor 'si6_nmfactor', s.i7_nmfactor 'si7_nmfactor', s.i8_nmfactor 'si8_nmfactor', 
                    s.i9_nmfactor 'si9_nmfactor', s.i10_nmfactor 'si10_nmfactor',
                    xb.name 'Bloque',
                    b.i1_nmcalculo 'bi1_nmcalculo', b.i2_nmcalculo 'bi2_nmcalculo', b.i3_nmcalculo 'bi3_nmcalculo', 
                    b.i4_nmcalculo 'bi4_nmcalculo', b.i5_nmcalculo 'bi5_nmcalculo', b.i6_nmcalculo 'bi6_nmcalculo', 
                    b.i7_nmcalculo 'bi7_nmcalculo', b.i8_nmcalculo 'bi8_nmcalculo', b.i9_nmcalculo 'bi9_nmcalculo', 
                    b.i10_nmcalculo 'bi10_nmcalculo', b.i1_nmfactor 'bi1_nmfactor', b.i2_nmfactor 'bi2_nmfactor', 
                    b.i3_nmfactor 'bi3_nmfactor', b.i4_nmfactor 'bi4_nmfactor', b.i5_nmfactor 'bi5_nmfactor', 
                    b.i6_nmfactor 'bi6_nmfactor', b.i7_nmfactor 'bi7_nmfactor', b.i8_nmfactor 'bi8_nmfactor', 
                    b.i9_nmfactor 'bi9_nmfactor', b.i10_nmfactor 'bi10_nmfactor',
                    xd.name 'Pregunta', xcd.name 'Respuesta',
                    d.i1_nmcalculo 'di1_nmcalculo', d.i2_nmcalculo 'di2_nmcalculo', d.i3_nmcalculo 'di3_nmcalculo', 
                    d.i4_nmcalculo 'di4_nmcalculo', d.i5_nmcalculo 'di5_nmcalculo', d.i6_nmcalculo 'di6_nmcalculo', 
                    d.i7_nmcalculo 'di7_nmcalculo', d.i8_nmcalculo 'di8_nmcalculo', d.i9_nmcalculo 'di9_nmcalculo', 
                    d.i10_nmcalculo 'di10_nmcalculo', d.i1_nmfactor 'di1_nmfactor', d.i2_nmfactor 'di2_nmfactor', 
                    d.i3_nmfactor 'di3_nmfactor', d.i4_nmfactor 'di4_nmfactor', d.i5_nmfactor 'di5_nmfactor', 
                    d.i6_nmfactor 'di6_nmfactor', d.i7_nmfactor 'di7_nmfactor', d.i8_nmfactor 'di8_nmfactor', 
                    d.i9_nmfactor 'di9_nmfactor', d.i10_nmfactor 'di10_nmfactor',
                    xtd.name 'Tipificacion',
                    xusuarios.usua_nombre 'responsable', xevaluados.name 'evaluado', 
                    xevaluados.identificacion 'cedula_evaluado', 
                    xusuarios2.usua_nombre 'evaluador', f.dsfuente_encuesta 'fuente', xequipos.name 'equipo', 
                    xtd.subtipificacion_id 'cdSubTipificacionDetalle', xstd.id 'cdsubtipificacion', 
                    xstd.name 'subtipificacion', st.id 'IDsubtipificacion', xtransacions.name 'transacion', 
                    s.dscomentario 'sdscomentario', f.dscomentario 'fdscomentario', xcd.i1_povalor 'i1_poRespuesta', 
                    xcd.i2_povalor 'i2_poRespuesta', xcd.i3_povalor 'i3_poRespuesta', xcd.i4_povalor 'i4_poRespuesta', 
                    xcd.i5_povalor 'i5_poRespuesta', xcd.i6_povalor 'i6_poRespuesta', xcd.i7_povalor 'i7_poRespuesta', 
                    xcd.i8_povalor 'i8_poRespuesta', xcd.i9_povalor 'i9_poRespuesta', xcd.i10_povalor 'i10_poRespuesta', 
                    rol.role_nombre rol, f.hora_inicial 'hora_inicial', f.hora_final 'hora_final', f.score 'score', f.cant_modificaciones 'cant_modificaciones', f.tiempo_modificaciones 'tiempo_modificaciones'";

            if($arbol_id == '3104'){
                $sql .= ", ba.reason3 'reason3', ba.reason4 'reason4', ba.idnps 'idnps', ba.email 'email', ba.nmusuario 'nmusuario', ba.user_seg 'user_seg', ba.nota_anterior 'nota_anterior', bsa.identificacion 'identificacion', bsa.nombre 'nombre', bsa.ani 'ani', bsa.rn 'rn', bsa.tipologia 'tipologia', bsa.pregunta1 'pregunta1'";
            }

            $sql .= " FROM (tbl_ejecucionformularios f, tbl_formularios xf, tbl_arbols xarbol,  
                    tbl_usuarios xusuarios, tbl_evaluados xevaluados, tbl_transacions xtransacions,
                    tbl_usuarios xusuarios2, tbl_equipos xequipos, rel_usuarios_roles urol, tbl_roles rol, 
                    tbl_arbols xarbol_padre, tbl_dimensions xdim, tbl_ejecucionseccions s, tbl_seccions xs, 
                    tbl_ejecucionbloques b, tbl_bloques xb, tbl_calificaciondetalles xcd, tbl_bloquedetalles xd, 
                    tbl_ejecucionbloquedetalles d) ";

            $sql .= " LEFT JOIN tbl_tipificaciondetalles xtd ON xd.tipificacion_id = xtd.tipificacion_id AND xtd.`snen_uso` = 1 
                    LEFT JOIN tbl_ejecucionbloquedetalles_tipificaciones t 
                    ON xtd.id = t.tipificaciondetalle_id AND t.ejecucionbloquedetalle_id = d.id  
                    LEFT JOIN tbl_tipificaciondetalles xstd ON xtd.subtipificacion_id = xstd.tipificacion_id  AND xstd.`snen_uso` = 1 
                    LEFT JOIN tbl_ejecucionbloquedetalles_subtipificaciones st 
                    ON xstd.id = st.tipificaciondetalle_id AND t.id =  st.ejecucionbloquedetalles_tipificacion_id";
            
            if($arbol_id == '3104'){
                $sql .= " LEFT JOIN  tbl_base_Avon ba ON f.basesatisfaccion_id = ba.id LEFT JOIN tbl_base_satisfaccion bsa ON f.basesatisfaccion_id = bsa.id";
            }

            $sql .= " WHERE f.arbol_id in (" . $arbol_id . ") AND f.created BETWEEN '2023-09-18 03:00:00' AND '2023-09-18 06:59:59' 
                    AND xf.id = f.formulario_id AND f.arbol_id = xarbol.id AND xtransacions.id = f.transacion_id 
                    AND xarbol.arbol_id = xarbol_padre.id AND f.dimension_id = xdim.id AND f.evaluado_id = xevaluados.id 
                    AND f.usua_id_lider = xusuarios.usua_id AND f.usua_id = xusuarios2.usua_id AND f.equipo_id = xequipos.id 
                    AND f.usua_id = urol.rel_usua_id AND urol.rel_role_id = rol.role_id AND f.id = s.ejecucionformulario_id 
                    AND xs.id = s.seccion_id AND s.id = b.ejecucionseccion_id AND xb.id = b.bloque_id 
                    AND b.id = d.ejecucionbloque_id AND xd.id = d.bloquedetalle_id AND d.calificaciondetalle_id = xcd.id";

            $sql .= " ORDER BY f.id, xs.nmorden, xs.id, xb.nmorden, xb.id, xd.nmorden, xd.id, xtd.nmorden, xtd.id, xstd.nmorden, xstd.id ";
        
            /* ------------------- * -------------------- */
            /* ------------------- * -------------------- */
            /* Ciclo para recuperar por rangos */
            $delta_ciclo = \Yii::$app->params["limitQueryExtractarFormulario"];
            
            /* ------------------- * -------------------- */
            /* ------------------- * -------------------- */
            $limite_ciclo_inicial = -$delta_ciclo;
            $limite_ciclo_final = $delta_ciclo - 1;  
            $newRow = 0;
            $fila = 2;
            $objPHPexcel = new \PHPExcel();
            $objPHPexcel->setActiveSheetIndex(0);

            //inicio do while
            do {
                $data = null;
                $limite_ciclo_inicial += $delta_ciclo;
                $sqlRango = $sql . " LIMIT " . $limite_ciclo_inicial . "," . $limite_ciclo_final . " ";
                $data = Yii::$app->db->createCommand($sqlRango)->queryAll();   
                
                //inicio si hay registros -----------------------------------------------------            
                if (count($data) > 0) {

                    //inicio foreach $data
                    foreach ($data as $i => $row) {                        

                        if ($row['fid'] != $fid) {
                            // Si no es el primer registro se imprime la fila
                            if ($fid != -1) {

                                //MUESTRO LOS ENCABEZADO SOLO UNA VEZ
                                $filecontent = "";

                                //IMPRIMO EN EL CSV LOS RESULTADOS QUE VAYAN
                                foreach ($dataProvider as $value) {
                                    $tmpCont = implode("|", $value);
                                    $filecontent = str_replace(array("\r\n"), ' ', $tmpCont);
                                    $objPHPexcel->getActiveSheet()->setCellValue('A' . $fila, $filecontent);
                                    $fila++;
                                }
                                // Ya se escribio - Lo puedo liberar
                                $dataProvider = null;
                                $dataProvider = array();
                                $newRow = 0;
                            }

                            $fid = $row['fid'];
                            $sid = -1;
                            $did = -1;
                            $cdpregunta = -1;
                            $cdtipificacion = -1;
                            $newRow++;
                            if($arbol_id == '3104'){  
                                $iData = 42;
                            }else{  
                                $iData = 29;
                            }

                            $dataProvider[$newRow][0] = $data[$i]['Fecha'];
                            $dataProvider[$newRow][1] = $data[$i]['hora_inicial'];
                            $dataProvider[$newRow][2] = $data[$i]['hora_final'];
                            
                            if ($data[$i]['hora_inicial'] != "" AND $data[$i]['hora_final'] != "") {
                                $inicial = new DateTime($data[$i]['hora_inicial']);
                                $final = new DateTime($data[$i]['hora_final']);
                                $dteDiff  = $inicial->diff($final);
                                $dataProvider[$newRow][3] = $dteDiff->h . ":" . $dteDiff->i . ":" . $dteDiff->s;
                            }

                            $dataProvider[$newRow][4] = $data[$i]['cant_modificaciones'];
                            $dataProvider[$newRow][5] = $data[$i]['tiempo_modificaciones'];

                            $dataProvider[$newRow][6] = $data[$i]['Dimension'];
                            $dataProvider[$newRow][7] = $data[$i]['ArbolPadre'];
                            $dataProvider[$newRow][8] = $data[$i]['Arbol'];
                            $dataProvider[$newRow][9] = $data[$i]['Formulario'];
                            $dataProvider[$newRow][10] = $data[$i]['cedula_evaluado'];
                            $dataProvider[$newRow][11] = $data[$i]['evaluado'];
                            $dataProvider[$newRow][12] = $data[$i]['responsable'];
                            $dataProvider[$newRow][13] = $data[$i]['evaluador'];
                            $dataProvider[$newRow][14] = $data[$i]['rol'];
                            $dataProvider[$newRow][15] = $data[$i]['fuente'];
                            $dataProvider[$newRow][16] = $data[$i]['transacion'];
                            $dataProvider[$newRow][17] = $data[$i]['equipo'];
                            $dataProvider[$newRow][18] = $data[$i]['fdscomentario'];

                            if($arbol_id == '3104'){
                                $dataProvider[$newRow][19] = $data[$i]['identificacion'];
                                $dataProvider[$newRow][20] = $data[$i]['nombre'];
                                $dataProvider[$newRow][21] = $data[$i]['ani'];
                                $dataProvider[$newRow][22] = $data[$i]['rn'];
                                $dataProvider[$newRow][23] = $data[$i]['tipologia'];
                                $dataProvider[$newRow][24] = $data[$i]['pregunta1'];
                                $dataProvider[$newRow][25] = $data[$i]['reason3'];
                                $dataProvider[$newRow][26] = $data[$i]['reason4'];
                                $dataProvider[$newRow][27] = $data[$i]['idnps'];
                                $dataProvider[$newRow][28] = $data[$i]['email'];
                                $dataProvider[$newRow][29] = $data[$i]['nmusuario'];
                                $dataProvider[$newRow][30] = $data[$i]['user_seg'];
                                $dataProvider[$newRow][31] = $data[$i]['nota_anterior'];

                                if ($data[$i]['ejec_principal'] != '' && $data[$i]['estado'] != '') {
                                    $dataProvider[$newRow][32] ='Valoración Escalada';
                                } else {
                                    if ($data[$i]['ejec_principal'] != '') {
                                        $dataProvider[$newRow][32] ='Valoración Adicional, Id valoración principal:' . $data[$i]['ejec_principal'];
                                    }else{
                                        $dataProvider[$newRow][32] = 'N/A';
                                    }
                                }
                                $dataProvider[$newRow][33] = $data[$i]['score'];
                                $dataProvider[$newRow][34] = $data[$i]['fi1_nmcalculo'];
                                $dataProvider[$newRow][35] = $data[$i]['fi2_nmcalculo'];
                                $dataProvider[$newRow][36] = $data[$i]['fi3_nmcalculo'];
                                $dataProvider[$newRow][37] = $data[$i]['fi4_nmcalculo'];
                                $dataProvider[$newRow][38] = $data[$i]['fi5_nmcalculo'];
                                $dataProvider[$newRow][39] = $data[$i]['fi6_nmcalculo'];
                                $dataProvider[$newRow][40] = $data[$i]['fi7_nmcalculo'];
                                $dataProvider[$newRow][41] = $data[$i]['fi8_nmcalculo'];
                                $dataProvider[$newRow][42] = $data[$i]['fi9_nmcalculo'];
                                $dataProvider[$newRow][43] = $data[$i]['fi10_nmcalculo'];
                            } else{

                                if ($data[$i]['ejec_principal'] != '' && $data[$i]['estado'] != '') {
                                    $dataProvider[$newRow][19] ='Valoración Escalada';
                                } else {
                                    if ($data[$i]['ejec_principal'] != '') {
                                        $dataProvider[$newRow][19] ='Valoración Adicional, Id valoración principal:' . $data[$i]['ejec_principal'];
                                    }else{
                                        $dataProvider[$newRow][19] = 'N/A';
                                    }
                                }
                                $dataProvider[$newRow][20] = $data[$i]['score'];
                                $dataProvider[$newRow][21] = $data[$i]['fi1_nmcalculo'];
                                $dataProvider[$newRow][22] = $data[$i]['fi2_nmcalculo'];
                                $dataProvider[$newRow][23] = $data[$i]['fi3_nmcalculo'];
                                $dataProvider[$newRow][24] = $data[$i]['fi4_nmcalculo'];
                                $dataProvider[$newRow][25] = $data[$i]['fi5_nmcalculo'];
                                $dataProvider[$newRow][26] = $data[$i]['fi6_nmcalculo'];
                                $dataProvider[$newRow][27] = $data[$i]['fi7_nmcalculo'];
                                $dataProvider[$newRow][28] = $data[$i]['fi8_nmcalculo'];
                                $dataProvider[$newRow][29] = $data[$i]['fi9_nmcalculo'];
                                $dataProvider[$newRow][30] = $data[$i]['fi10_nmcalculo'];
                            }              
                            
                        }


                        if ($row['sid'] != $sid) {
                            $sid = $row['sid'];
                            $did = -1;

                            $iData++;

                            $titulos[$iData] = ['header' => 'Seccion ' . $data[$i]['Seccion'],
                                'value' => '' . $iData . ''];
                            $dataProvider[$newRow][$iData] = $data[$i]['Seccion'];
                            $iData++;
                            $titulos[$iData] = ['header' => 'Comentario', 'value' => '' . $iData . ''];
                            $dataProvider[$newRow][$iData] = $data[$i]['sdscomentario'];
                            $iData++;
                            $titulos[$iData] = ['header' => $textos[0]['titulo'], 'value' => '' . $iData . ''];
                            $dataProvider[$newRow][$iData] = $data[$i]['si1_nmcalculo'];
                            $iData++;
                            $titulos[$iData] = ['header' => $textos[1]['titulo'], 'value' => '' . $iData . ''];
                            $dataProvider[$newRow][$iData] = $data[$i]['si2_nmcalculo'];
                            $iData++;
                            $titulos[$iData] = ['header' => $textos[2]['titulo'], 'value' => '' . $iData . ''];
                            $dataProvider[$newRow][$iData] = $data[$i]['si3_nmcalculo'];
                            $iData++;
                            $titulos[$iData] = ['header' => $textos[3]['titulo'], 'value' => '' . $iData . ''];
                            $dataProvider[$newRow][$iData] = $data[$i]['si4_nmcalculo'];
                            $iData++;
                            $titulos[$iData] = ['header' => $textos[4]['titulo'], 'value' => '' . $iData . ''];
                            $dataProvider[$newRow][$iData] = $data[$i]['si5_nmcalculo'];
                            $iData++;
                            $titulos[$iData] = ['header' => $textos[5]['titulo'], 'value' => '' . $iData . ''];
                            $dataProvider[$newRow][$iData] = $data[$i]['si6_nmcalculo'];
                            $iData++;
                            $titulos[$iData] = ['header' => $textos[6]['titulo'], 'value' => '' . $iData . ''];
                            $dataProvider[$newRow][$iData] = $data[$i]['si7_nmcalculo'];
                            $iData++;
                            $titulos[$iData] = ['header' => $textos[7]['titulo'], 'value' => '' . $iData . ''];
                            $dataProvider[$newRow][$iData] = $data[$i]['si8_nmcalculo'];
                            $iData++;
                            $titulos[$iData] = ['header' => $textos[8]['titulo'], 'value' => '' . $iData . ''];
                            $dataProvider[$newRow][$iData] = $data[$i]['si9_nmcalculo'];
                            $iData++;
                            $titulos[$iData] = ['header' => $textos[9]['titulo'], 'value' => '' . $iData . ''];
                            $dataProvider[$newRow][$iData] = $data[$i]['si10_nmcalculo'];
                            $iData++;
                        }

                        if ($row['did'] != $did) {
                            $did = $row['did'];
                            $cdpregunta = -1;
                            $titulos[$iData] = ['header' => 'Bloque ' . $data[$i]['Bloque'],
                                'value' => '' . $iData . ''];
                            $dataProvider[$newRow][$iData] = $data[$i]['Bloque'];
                            $iData++;
                            $titulos[$iData] = ['header' => $textos[0]['titulo'], 'value' => '' . $iData . ''];
                            $dataProvider[$newRow][$iData] = $data[$i]['bi1_nmcalculo'];
                            $iData++;
                            $titulos[$iData] = ['header' => $textos[1]['titulo'], 'value' => '' . $iData . ''];
                            $dataProvider[$newRow][$iData] = $data[$i]['bi2_nmcalculo'];
                            $iData++;
                            $titulos[$iData] = ['header' => $textos[2]['titulo'], 'value' => '' . $iData . ''];
                            $dataProvider[$newRow][$iData] = $data[$i]['bi3_nmcalculo'];
                            $iData++;
                            $titulos[$iData] = ['header' => $textos[3]['titulo'], 'value' => '' . $iData . ''];
                            $dataProvider[$newRow][$iData] = $data[$i]['bi4_nmcalculo'];
                            $iData++;
                            $titulos[$iData] = ['header' => $textos[4]['titulo'], 'value' => '' . $iData . ''];
                            $dataProvider[$newRow][$iData] = $data[$i]['bi5_nmcalculo'];
                            $iData++;
                            $titulos[$iData] = ['header' => $textos[5]['titulo'], 'value' => '' . $iData . ''];
                            $dataProvider[$newRow][$iData] = $data[$i]['bi6_nmcalculo'];
                            $iData++;
                            $titulos[$iData] = ['header' => $textos[6]['titulo'], 'value' => '' . $iData . ''];
                            $dataProvider[$newRow][$iData] = $data[$i]['bi7_nmcalculo'];
                            $iData++;
                            $titulos[$iData] = ['header' => $textos[7]['titulo'], 'value' => '' . $iData . ''];
                            $dataProvider[$newRow][$iData] = $data[$i]['bi8_nmcalculo'];
                            $iData++;
                            $titulos[$iData] = ['header' => $textos[8]['titulo'], 'value' => '' . $iData . ''];
                            $dataProvider[$newRow][$iData] = $data[$i]['bi9_nmcalculo'];
                            $iData++;
                            $titulos[$iData] = ['header' => $textos[9]['titulo'], 'value' => '' . $iData . ''];
                            $dataProvider[$newRow][$iData] = $data[$i]['bi10_nmcalculo'];
                            $iData++;
                        }

                        if ($row['cdPregunta'] != $cdpregunta) {
                            $cdpregunta = $row['cdPregunta'];
                            $cdtipificacion = -1;

                            $p = 'P ' . str_replace($this->acentos, $this->sinAcentos,$data[$i]['Pregunta']);
                            $titulos[$iData] = ['header' => $p, 'value' => '' . $iData . ''];
                            $dataProvider[$newRow][$iData] = $data[$i]['Pregunta'];
                            $iData++;
                            $r = 'R ' . str_replace($this->acentos, $this->sinAcentos, $data[$i]['Pregunta']);
                            $titulos[$iData] = ['header' => $r, 'value' => '' . $iData . ''];
                            $dataProvider[$newRow][$iData] = $data[$i]['Respuesta'];
                            $iData++;
                        }

                        if ($row['idTipi'] != null) {
                            if ($row['cdTipificacionDetalle'] != $cdtipificacion) {
                                $cdtipificacion = $row['cdTipificacionDetalle'];

                                $titulos[$iData] = ['header' => 'TPF ' . $data[$i]['Tipificacion'],
                                    'value' => '' . $iData . ''];
                                $dataProvider[$newRow][$iData] = $data[$i]['tipificaciondetalle_id'] != null ? '1' : '0';
                                $iData++;
                            }

                            if ($row['cdSubTipificacionDetalle'] != null) {
                                $titulos[$iData] = ['header' => 'STPF ' . $data[$i]['subtipificacion'],
                                    'value' => '' . $iData . ''];
                                $dataProvider[$newRow][$iData] = $data[$i]['IDsubtipificacion'] != null ? '1' : '0';
                                $iData++;
                            }
                        }
                    } // fin For cada columna del retorno del query
                    
                    // }
                    //fin foreach $data

                    $export = true;
                } // Fin se hay registros
            } while (count($data) > 0);
            //fin do while

            $filecontent = "";
            //IMPRIMO EL ULTIMO REGISTRO  
            if (isset($dataProvider)) {
                foreach ($dataProvider as $value) {
                    var_dump($value); 
                    $tmpCont = implode("|", $value);
                    $filecontent = str_replace(array("\r\n"), ' ', $tmpCont);
                    $objPHPexcel->getActiveSheet()->setCellValue('A' . $fila, $filecontent);
                    $fila++;
                }
            } else {
                $export = false;
            }
            $arrayTitulos = [];

            $column = 'A';
            foreach ($titulos as $key => $value) {
                $arrayTitulos[] = $value['header'];
            }
            for ($index = 0; $index < count($arrayTitulos); $index++) {
                $objPHPexcel->getActiveSheet()->setCellValue($column . '1', $arrayTitulos[$index]);
                $column++;
            }

            $objWriter = new \PHPExcel_Writer_Excel2007($objPHPexcel);
            $objWriter->save($fileName);

        }
        //fin foreach array_pcrc

        die(json_encode("Fin proceso"));

    }
    
    
    public function getTextosPreguntas() {
        $sql = " SELECT t.id, t.detexto as 'titulo' FROM tbl_textos t";
        return Yii::$app->db->createCommand($sql)->queryAll();
    }
    

  }

?>
