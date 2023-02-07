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


  class ApiformulariosController extends \yii\web\Controller {

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
                'actions' => ['index','apisesiones','apibloques','apiformularios','apipreguntas','apitipificaciones','apitipificacionescontiene','apigeneral'],
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema();
                        },
              ],
              [
                'actions' => ['apisesiones','apibloques','apiformularios','apipreguntas','apitipificaciones','apitipificacionescontiene','apigeneral'],
                'allow' => true,

              ],
            ],

        ],

           


        
      ];
    }
  
    public function init(){
      $this->enableCsrfValidation = false;
    }

    public function actionIndex(){

      return $this->render('index');
    }

    public function actionApisesiones(){


      $datapost = file_get_contents('php://input');
      $data_post = json_decode($datapost,true);
  
      if (
           !isset($data_post["idarbol"]) 
        || !isset($data_post["fechaInicio"]) 
        || !isset($data_post["fechaFin"]) 
        || empty($data_post["idarbol"]) 
        || empty($data_post["fechaInicio"]) 
        || empty($data_post["fechaFin"]) 
      ) {
        die(json_encode(array("status"=>"0","data"=>"Algunos de los campos de las sesiones obligatorios no se enviaron correctamente")));
      }

      $varIdArbol = $data_post["idarbol"];   
      $varFechaInicio = $data_post["fechaInicio"];
      $varFechaFin = $data_post["fechaFin"];


      $paramsBusqueda = [':Arbol_id' => $varIdArbol, ':Fecha_inicio' => $varFechaInicio.' 00:00:00', ':Fecha_Fin' => $varFechaFin.' 23:59:59'];
      $varListSecciones = Yii::$app->db->createCommand('
        SELECT s.id, s.name AS Seccion FROM tbl_seccions s
          INNER JOIN tbl_ejecucionformularios ef ON 
            s.formulario_id = ef.formulario_id
          WHERE 
            ef.arbol_id IN (:Arbol_id)
              AND ef.created BETWEEN :Fecha_inicio AND :Fecha_Fin
            GROUP BY s.id')->bindValues($paramsBusqueda)->queryAll();

      $arraydatas = array();
      foreach ($varListSecciones as $key => $value) {
        $varIdSeccion = $value['id'];
        $varNombreSeccion = $value['Seccion'];

        $paramsSessiones = [':Id_Session' => $varIdSeccion];
        $varListScoreS = Yii::$app->db->createCommand('
        SELECT
          ef.id AS Id_Formulario,
          ROUND(
            (COALESCE(es.i1_nmcalculo,0)) + 
            (COALESCE(es.i2_nmcalculo,0)) +
            (COALESCE(es.i3_nmcalculo,0)) +
            (COALESCE(es.i4_nmcalculo,0)) +
            (COALESCE(es.i5_nmcalculo,0)) +
            (COALESCE(es.i6_nmcalculo,0)) +
            (COALESCE(es.i7_nmcalculo,0)) +
            (COALESCE(es.i8_nmcalculo,0)) +
            (COALESCE(es.i9_nmcalculo,0)) +
            (COALESCE(es.i10_nmcalculo,0))
          ,1) AS Score
            FROM tbl_ejecucionformularios ef
              INNER JOIN tbl_ejecucionseccions es ON 
                ef.id = es.ejecucionformulario_id
              WHERE 
                es.seccion_id = :Id_Session ')->bindValues($paramsSessiones)->queryAll();

        
        foreach ($varListScoreS as $key => $value) {          

          array_push($arraydatas, array("Id_Formulario "=>$value['Id_Formulario'],"Seccion "=>$varNombreSeccion,"Score"=>$value['Score']));
        }        

      }      
      
      die(json_encode(array("status"=>"1","data"=>$arraydatas)));
      
    }     

    public function actionApibloques(){

      $datapost = file_get_contents('php://input');
      $data_post = json_decode($datapost,true);
  
      if (
           !isset($data_post["idarbol"]) 
        || !isset($data_post["fechaInicio"]) 
        || !isset($data_post["fechaFin"]) 
        || empty($data_post["idarbol"]) 
        || empty($data_post["fechaInicio"]) 
        || empty($data_post["fechaFin"]) 
      ) {
        die(json_encode(array("status"=>"0","data"=>"Algunos de los campos de Bloques obligatorios no se enviaron correctamente")));
      }

      $varIdArbol = $data_post["idarbol"];   
      $varFechaInicio = $data_post["fechaInicio"];
      $varFechaFin = $data_post["fechaFin"];


      $paramsBusqueda = [':Arbol_id' => $varIdArbol, ':Fecha_inicio' => $varFechaInicio.' 00:00:00', ':Fecha_Fin' => $varFechaFin.' 23:59:59'];
      $varListBloques = Yii::$app->db->createCommand('
        SELECT  ef.id, s.id AS id_Seccion, b.id AS id_Bloque, b.name AS Bloque FROM tbl_bloques b
          INNER JOIN tbl_seccions s ON 
            b.seccion_id = s.id
          INNER JOIN tbl_ejecucionformularios ef ON 
            s.formulario_id = ef.formulario_id   
          WHERE 
            ef.arbol_id IN  (:Arbol_id)
              AND ef.created BETWEEN :Fecha_inicio AND :Fecha_Fin
            GROUP BY b.id
              ORDER BY ef.id')->bindValues($paramsBusqueda)->queryAll();


      $arraydatab = array();
      foreach ($varListBloques as $key => $value) {
        $varIdSecciones = $value['id_Seccion'];
        $varIdBloques = $value['id_Bloque'];
        $varNombreBloque = $value['Bloque'];

        $paramsBloques = [':IdSeccion' => $varIdSecciones, 'IdBloques' => $varIdBloques];
        $varListScoreB = Yii::$app->db->createCommand('
        SELECT
          ef.id AS id_Formulario,
          ROUND(
            (COALESCE(eb.i1_nmcalculo,0)) + 
            (COALESCE(eb.i2_nmcalculo,0)) +
            (COALESCE(eb.i3_nmcalculo,0)) +
            (COALESCE(eb.i4_nmcalculo,0)) +
            (COALESCE(eb.i5_nmcalculo,0)) +
            (COALESCE(eb.i6_nmcalculo,0)) +
            (COALESCE(eb.i7_nmcalculo,0)) +
            (COALESCE(eb.i8_nmcalculo,0)) +
            (COALESCE(eb.i9_nmcalculo,0)) +
            (COALESCE(eb.i10_nmcalculo,0))
          ,1) AS Score
            FROM tbl_ejecucionbloques eb    
              INNER JOIN tbl_ejecucionseccions es ON 
                eb.ejecucionseccion_id = es.id
              INNER JOIN tbl_ejecucionformularios ef ON 
                es.ejecucionformulario_id = ef.id
              WHERE 
                es.seccion_id = :IdSeccion
                  AND eb.bloque_id = :IdBloques
                    ORDER BY ef.id')->bindValues($paramsBloques)->queryAll();

        foreach ($varListScoreB as $key => $value) {
          array_push($arraydatab, array("Id_Formulario "=>$value['id_Formulario'],"Id_Bloque "=>$varIdBloques,"Bloque "=>$varNombreBloque,"Score"=>$value['Score']));          
        }

      }

      die(json_encode(array("status"=>"1","data"=>$arraydatab)));
      
    }     

    public function actionApiformularios(){

      $datapost = file_get_contents('php://input');
      $data_post = json_decode($datapost,true);
  
      if (
           !isset($data_post["idarbol"]) 
        || !isset($data_post["fechaInicio"]) 
        || !isset($data_post["fechaFin"]) 
        || empty($data_post["idarbol"]) 
        || empty($data_post["fechaInicio"]) 
        || empty($data_post["fechaFin"]) 
      ) {
        die(json_encode(array("status"=>"0","data"=>"Algunos de los campos de Formularios obligatorios no se enviaron correctamente")));
      }

      $varIdArbol = $data_post["idarbol"];   
      $varFechaInicio = $data_post["fechaInicio"];
      $varFechaFin = $data_post["fechaFin"];


      $paramsFormularios = [':Arbol_id' => $varIdArbol, ':Fecha_inicio' => $varFechaInicio.' 00:00:00', ':Fecha_Fin' => $varFechaFin.' 23:59:59'];
      $varListFormularios = Yii::$app->db->createCommand('
       SELECT 
        f.id, f.basesatisfaccion_id AS ID_Encuesta, f.created AS FechaYHora, f.hora_inicial AS HoraInicialValoracion, 
        f.hora_final AS HoraFinalValoracion, f.cant_modificaciones AS CantModificaciones, f.tiempo_modificaciones AS TiempoModificaciones, d.name AS Dimension, aa.name AS ArbolPadre, a.id AS IDArbol, a.name AS Arbol, ff.name AS Formulario, 
        re.cod_pcrc AS CentroCostos, re.pcrc AS Pcrc,
        uq.usua_nombre AS Lider, e.name AS Asesor, e.identificacion AS CedulaEvaluado, e.name AS NombreEvaluado, (select ux.usua_nombre from tbl_usuarios ux where ux.usua_id = f.usua_id_lider) AS Rsponsable,   uu.usua_nombre AS Evaluador, r.role_nombre AS rol, f.dsfuente_encuesta AS Fuente, t.name AS Transacciones, eq.name AS Equipo, f.dscomentario AS Comentarios, f.score AS Score, f.i1_nmcalculo AS PEC

        FROM tbl_ejecucionformularios f
          INNER JOIN tbl_dimensions d ON 
            f.dimension_id = d.id
          INNER JOIN tbl_arbols a ON 
            f.arbol_id = a.id
          INNER JOIN tbl_arbols aa ON 
            a.arbol_id = aa.id
          INNER JOIN tbl_formularios ff ON 
            f.formulario_id = ff.id
          INNER JOIN tbl_evaluados e ON 
            f.evaluado_id = e.id
          INNER JOIN tbl_usuarios u ON 
            f.usua_id = u.usua_id
          INNER JOIN tbl_usuarios uu ON 
            u.usua_id = uu.usua_id
          INNER JOIN rel_usuarios_roles ur ON 
            uu.usua_id = ur.rel_usua_id
          INNER JOIN tbl_roles r ON 
            ur.rel_role_id = r.role_id
          INNER JOIN tbl_transacions t ON 
            f.transacion_id = t.id
          INNER JOIN tbl_equipos eq ON 
            f.equipo_id = eq.id
          INNER JOIN tbl_usuarios uq ON 
            uq.usua_id = eq.usua_id          
          LEFT JOIN tbl_registro_ejec_cliente re ON 
            re.ejec_form_id = f.id

          WHERE
            f.arbol_id IN (:Arbol_id )
              AND f.created BETWEEN :Fecha_inicio AND :Fecha_Fin')->bindValues($paramsFormularios)->queryAll();

      $arraydataf = array();
      foreach ($varListFormularios as $key => $value) {
      

        array_push($arraydataf, array("Id_Formulario "=>$value['id'],"Id_Encuesta "=>$value['ID_Encuesta'],"Fecha&Hora "=>$value['FechaYHora'],"Hora_Inicio_Valoracion "=>$value['HoraInicialValoracion'],"Hora_Fin_Valoracion "=>$value['HoraFinalValoracion'],"Cantidad_Modificaciones "=>$value['CantModificaciones'],"Tiempo_Modificaciones "=>$value['TiempoModificaciones'],"Dimensiones "=>$value['Dimension'],"Arbol_Padre "=>$value['ArbolPadre'],"Id_Pcrc "=>$value['IDArbol'],"Programa_Pcrc "=>$value['Arbol'],"Formulario "=>$value['Formulario'],"Nombre_Lider "=>$value['Lider'],"Nombre_Asesor "=>$value['Asesor'],"Identificacion_Asesor "=>$value['CedulaEvaluado'],"Responsable "=>$value['Rsponsable'],"Nombre_Evaluador "=>$value['Evaluador'],"Rol "=>$value['rol'],"Fuente "=>$value['Fuente'],"Transacciones "=>$value['Transacciones'],"Equipo "=>$value['Equipo'],"Comentarios "=>$value['Comentarios'],"Score "=>$value['Score'],"PEC "=>$value['PEC'],"CentroCostos"=>$value['CentroCostos'],"Canal"=>$value['Pcrc']));  
      }

      die(json_encode(array("status"=>"1","data"=>$arraydataf)));

    }


    public function actionApipreguntas(){

      $datapost = file_get_contents('php://input');
      $data_post = json_decode($datapost,true);
  
      if (
           !isset($data_post["idarbol"]) 
        || !isset($data_post["fechaInicio"]) 
        || !isset($data_post["fechaFin"]) 
        || empty($data_post["idarbol"]) 
        || empty($data_post["fechaInicio"]) 
        || empty($data_post["fechaFin"]) 
      ) {
        die(json_encode(array("status"=>"0","data"=>"Algunos de los campos de Preguntas obligatorios no se enviaron correctamente")));
      }

      $varIdArbol = $data_post["idarbol"];   
      $varFechaInicio = $data_post["fechaInicio"];
      $varFechaFin = $data_post["fechaFin"];

      $paramsBusqueda = [':Arbol_id' => $varIdArbol, ':Fecha_inicio' => $varFechaInicio.' 00:00:00', ':Fecha_Fin' => $varFechaFin.' 23:59:59'];
      $varListPreguntas = Yii::$app->db->createCommand('
      SELECT ef.id AS Id_Formulario, b.id AS Id_Bloque, b.name AS Bloque, bd.id AS Id_Pregunta, bd.name AS Pregunta, 
        cd.id AS Id_Respuesta, cd.name AS Respuesta FROM tbl_calificaciondetalles cd
                INNER JOIN tbl_ejecucionbloquedetalles ebd ON
                   ebd.calificaciondetalle_id = cd.id
                INNER JOIN tbl_bloquedetalles bd ON
                  ebd.bloquedetalle_id = bd.id  
                INNER JOIN tbl_bloques b ON 
                  b.id = bd.bloque_id
                INNER JOIN tbl_ejecucionbloques eb ON
                  b.id = eb.bloque_id AND eb.id = ebd.ejecucionbloque_id
                INNER JOIN tbl_ejecucionseccions es ON
                  eb.ejecucionseccion_id = es.id
                INNER JOIN tbl_seccions s ON
                  s.id = es.seccion_id
                INNER JOIN tbl_ejecucionformularios ef ON 
                  es.ejecucionformulario_id = ef.id
          WHERE 
            ef.arbol_id IN  (:Arbol_id)
              AND ef.created BETWEEN :Fecha_inicio AND :Fecha_Fin')->bindValues($paramsBusqueda)->queryAll();

      $arraydatap = array();
      foreach ($varListPreguntas as $key => $value) {

        array_push($arraydatap, array("Id_Formulario "=>$value['Id_Formulario'],"Id_Bloque"=>$value['Id_Bloque'],"Bloque"=>$value['Bloque'],"Id_Pregunta"=>$value['Id_Pregunta'],"Pregunta "=>$value['Pregunta'],"Id_Respuesta"=>$value['Id_Respuesta'],"Respuesta "=>$value['Respuesta'])); 

      }

      die(json_encode(array("status"=>"1","data"=>$arraydatap)));
    }

    public function actionApitipificaciones(){

      $datapost = file_get_contents('php://input');
      $data_post = json_decode($datapost,true);
  
      if (
           !isset($data_post["idarbol"]) 
        || !isset($data_post["fechaInicio"]) 
        || !isset($data_post["fechaFin"]) 
        || empty($data_post["idarbol"]) 
        || empty($data_post["fechaInicio"]) 
        || empty($data_post["fechaFin"]) 
      ) {
        die(json_encode(array("status"=>"0","data"=>"Algunos de los campos obligatorios no se enviaron correctamente")));
      }

      $varIdArbol = $data_post["idarbol"];   
      $varFechaInicio = $data_post["fechaInicio"];
      $varFechaFin = $data_post["fechaFin"]; 

      $paramsBusqueda = [':Arbol_id' => $varIdArbol, ':Fecha_inicio' => $varFechaInicio.' 00:00:00', ':Fecha_Fin' => $varFechaFin.' 23:59:59'];
      $varListTipificaciones = Yii::$app->db->createCommand('
      SELECT ef.id AS Id_Formulario, es.seccion_id AS Id_sesiones, b.id AS Id_Bloques,
      b.id AS Id_Preguntas, td.id AS id_Tipificacion,  
      td.name AS Tipificaciones,  if(ebt.tipificaciondetalle_id IS NULL, 0,1) AS Rtatipi, ef.created AS FechaValoracion
                
        FROM tbl_tipificaciondetalles td
            INNER JOIN tbl_bloquedetalles bd ON 
              td.tipificacion_id = bd.tipificacion_id
            INNER JOIN tbl_bloques b ON 
              bd.bloque_id = b.id
            INNER JOIN tbl_ejecucionbloques eb ON
              b.id = eb.bloque_id         
            INNER JOIN tbl_ejecucionbloquedetalles ebd ON 
              ebd.ejecucionbloque_id = eb.id
            LEFT JOIN tbl_ejecucionbloquedetalles_tipificaciones ebt ON 
              ebt.tipificaciondetalle_id = td.id AND ebt.ejecucionbloquedetalle_id = ebd.id
            INNER JOIN tbl_ejecucionseccions es ON
              eb.ejecucionseccion_id = es.id
            INNER JOIN tbl_ejecucionformularios ef ON 
              es.ejecucionformulario_id = ef.id
                
        WHERE
          ef.formulario_id IN  (:Arbol_id)
            AND ef.created BETWEEN :Fecha_inicio AND :Fecha_Fin
        GROUP BY ef.id, td.id')->bindValues($paramsBusqueda)->queryAll();

      $arraydatapi = array();
      foreach ($varListTipificaciones as $key => $value) {

        array_push($arraydatapi, array("Id_Formulario "=>$value['Id_Formulario'],"Id_sesiones"=>$value['Id_sesiones'],"Id_Bloques"=>$value['Id_Bloques'],"Id_Preguntas"=>$value['Id_Preguntas'],"Id_Tipificacion "=>$value['id_Tipificacion'],"Tipificaciones "=>$value['Tipificaciones'],'RespuestaTipificacion'=>$value['Rtatipi'],"Fechavaloracion"=>$value['FechaValoracion'])); 
      }

      die(json_encode(array("status"=>"1","data"=>$arraydatapi)));

    }
    
    public function actionApitipificacionescontiene(){

      $datapost = file_get_contents('php://input');
      $data_post = json_decode($datapost,true);
  
      if (
           !isset($data_post["idarbol"]) 
        || !isset($data_post["fechaInicio"]) 
        || !isset($data_post["fechaFin"]) 
        || empty($data_post["idarbol"]) 
        || empty($data_post["fechaInicio"]) 
        || empty($data_post["fechaFin"]) 
      ) {
        die(json_encode(array("status"=>"0","data"=>"Algunos de los campos obligatorios no se enviaron correctamente")));
      }

      $varIdArbol = $data_post["idarbol"];   
      $varFechaInicio = $data_post["fechaInicio"];
      $varFechaFin = $data_post["fechaFin"]; 

      $paramsBusqueda = [':Arbol_id' => $varIdArbol, ':Fecha_inicio' => $varFechaInicio.' 00:00:00', ':Fecha_Fin' => $varFechaFin.' 23:59:59'];
      $varListTipificaciones = Yii::$app->db->createCommand('
      SELECT ef.id AS Id_Formulario, es.seccion_id AS Id_sesiones, b.id AS Id_Bloques,
      b.id AS Id_Preguntas, td.id AS id_Tipificacion,  
      td.name AS Tipificaciones,  "1" AS Rtatipi, ef.created AS FechaValoracion
                
        FROM tbl_tipificaciondetalles td
            INNER JOIN tbl_bloquedetalles bd ON 
              td.tipificacion_id = bd.tipificacion_id
            INNER JOIN tbl_bloques b ON 
              bd.bloque_id = b.id
            INNER JOIN tbl_ejecucionbloques eb ON
              b.id = eb.bloque_id         
            INNER JOIN tbl_ejecucionbloquedetalles ebd ON 
              ebd.ejecucionbloque_id = eb.id
            LEFT JOIN tbl_ejecucionbloquedetalles_tipificaciones ebt ON 
              ebt.tipificaciondetalle_id = td.id AND ebt.ejecucionbloquedetalle_id = ebd.id
            INNER JOIN tbl_ejecucionseccions es ON
              eb.ejecucionseccion_id = es.id
            INNER JOIN tbl_ejecucionformularios ef ON 
              es.ejecucionformulario_id = ef.id
                
        WHERE
          ef.formulario_id IN  (:Arbol_id)
            AND ef.created BETWEEN :Fecha_inicio AND :Fecha_Fin
              AND ebt.tipificaciondetalle_id IS NOT NULL 
        GROUP BY ef.id, td.id')->bindValues($paramsBusqueda)->queryAll();

      $arraydatapi = array();
      foreach ($varListTipificaciones as $key => $value) {

        array_push($arraydatapi, array("Id_Formulario "=>$value['Id_Formulario'],"Id_sesiones"=>$value['Id_sesiones'],"Id_Bloques"=>$value['Id_Bloques'],"Id_Preguntas"=>$value['Id_Preguntas'],"Id_Tipificacion "=>$value['id_Tipificacion'],"Tipificaciones "=>$value['Tipificaciones'],'RespuestaTipificacion'=>$value['Rtatipi'],"Fechavaloracion"=>$value['FechaValoracion'])); 
      }

      die(json_encode(array("status"=>"1","data"=>$arraydatapi)));

    }  

    public function actionApigeneral(){

      $datapostG = file_get_contents('php://input');
      $data_postG = json_decode($datapostG,true);
  
      if (
           !isset($data_postG["idarbol"]) 
        || !isset($data_postG["fechaInicio"]) 
        || !isset($data_postG["fechaFin"]) 
        || empty($data_postG["idarbol"]) 
        || empty($data_postG["fechaInicio"]) 
        || empty($data_postG["fechaFin"]) 
      ) {
        die(json_encode(array("status"=>"0","data"=>"Algunos de los campos obligatorios no se enviaron correctamente")));
      }

      $varIdArbolG = $data_postG["idarbol"];   
      $varFechaInicioG = $data_postG["fechaInicio"];
      $varFechaFinG = $data_postG["fechaFin"];

      $arraydatafG = array();
      for ($i=0; $i < count($varIdArbolG); $i++) { 
        $varArboles = $varIdArbolG[$i];

        $paramsFormulariosG = [':Arbol_idG' => $varArboles, ':Fecha_inicioG' => $varFechaInicioG.' 00:00:00', ':Fecha_FinG' => $varFechaFinG.' 23:59:59'];

        $varListFormulariosG = Yii::$app->db->createCommand('
         SELECT 
          f.id, f.basesatisfaccion_id AS ID_Encuesta, f.created AS FechaYHora, f.hora_inicial AS HoraInicialValoracion, 
          f.hora_final AS HoraFinalValoracion, f.cant_modificaciones AS CantModificaciones, f.tiempo_modificaciones AS TiempoModificaciones, d.name AS Dimension, aa.name AS ArbolPadre, a.id AS IDArbol, a.name AS Arbol, ff.name AS Formulario, uq.usua_nombre AS Lider, e.name AS Asesor, e.identificacion AS CedulaEvaluado, e.name AS NombreEvaluado, (select ux.usua_nombre from tbl_usuarios ux where ux.usua_id = f.usua_id_lider) AS Rsponsable,   uu.usua_nombre AS Evaluador, r.role_nombre AS rol, f.dsfuente_encuesta AS Fuente, t.name AS Transacciones, eq.name AS Equipo, f.dscomentario AS Comentarios, f.score AS Score,  
            ROUND( f.i1_nmcalculo*100,2 ) AS PEC, 
            ROUND( f.i2_nmcalculo*100,2 ) AS PENC, 
            ROUND( f.i3_nmcalculo*100,2 ) AS SPC_FRC, 
            ROUND( f.i4_nmcalculo*100,2 ) AS CARINO_WOW, 
            ROUND( f.i5_nmcalculo*100,2 ) AS Indice_de_Proceso, 
            ROUND( f.i6_nmcalculo*100,2 ) AS Indice_de_Experiencia, 
            ROUND( f.i7_nmcalculo*100,2 ) AS Cumplimiento_Promesa_de_Marca, 
            ROUND( f.i8_nmcalculo*100,2 ) AS Desempeño_del_Canal, 
            ROUND( f.i9_nmcalculo*100,2 ) AS Desempeño_del_Agente, 
            ROUND( f.i10_nmcalculo*100,2 ) AS Habilidad_Comercial 

          FROM tbl_ejecucionformularios f
            INNER JOIN tbl_dimensions d ON 
              f.dimension_id = d.id
            INNER JOIN tbl_arbols a ON 
              f.arbol_id = a.id
            INNER JOIN tbl_arbols aa ON 
              a.arbol_id = aa.id
            INNER JOIN tbl_formularios ff ON 
              f.formulario_id = ff.id
            INNER JOIN tbl_evaluados e ON 
              f.evaluado_id = e.id
            INNER JOIN tbl_usuarios u ON 
              f.usua_id = u.usua_id
            INNER JOIN tbl_usuarios uu ON 
              u.usua_id = uu.usua_id
            INNER JOIN rel_usuarios_roles ur ON 
              uu.usua_id = ur.rel_usua_id
            INNER JOIN tbl_roles r ON 
              ur.rel_role_id = r.role_id
            INNER JOIN tbl_transacions t ON 
              f.transacion_id = t.id
            INNER JOIN tbl_equipos eq ON 
              f.equipo_id = eq.id
            INNER JOIN tbl_usuarios uq ON 
              uq.usua_id = eq.usua_id

            WHERE
              a.id IN (:Arbol_idG)
              AND f.created BETWEEN :Fecha_inicioG AND :Fecha_FinG')->bindValues($paramsFormulariosG)->queryAll();

        foreach ($varListFormulariosG as $key => $value) {
          array_push($arraydatafG, array("Id_Formulario "=>$value['id'],"Id_Encuesta "=>$value['ID_Encuesta'],"Fecha&Hora "=>$value['FechaYHora'],"Hora_Inicio_Valoracion "=>$value['HoraInicialValoracion'],"Hora_Fin_Valoracion "=>$value['HoraFinalValoracion'],"Cantidad_Modificaciones "=>$value['CantModificaciones'],"Tiempo_Modificaciones "=>$value['TiempoModificaciones'],"Dimensiones "=>$value['Dimension'],"Arbol_Padre "=>$value['ArbolPadre'],"Id_Pcrc "=>$value['IDArbol'],"Programa_Pcrc "=>$value['Arbol'],"Formulario "=>$value['Formulario'],"Nombre_Lider "=>$value['Lider'],"Nombre_Asesor "=>$value['Asesor'],"Identificacion_Asesor "=>$value['CedulaEvaluado'],"Responsable "=>$value['Rsponsable'],"Nombre_Evaluador "=>$value['Evaluador'],"Rol "=>$value['rol'],"Fuente "=>$value['Fuente'],"Transacciones "=>$value['Transacciones'],"Equipo "=>$value['Equipo'],"Comentarios "=>$value['Comentarios'],"Score "=>$value['Score'],"PEC "=>$value['PEC'],"PENC "=>$value['PENC'],"SPC_FRC "=>$value['SPC_FRC'],"CARINO_WOW "=>$value['CARINO_WOW'],"Indice_de_Proceso "=>$value['Indice_de_Proceso'],"Indice_de_Experiencia "=>$value['Indice_de_Experiencia'],"Cumplimiento_Promesa_de_Marca "=>$value['Cumplimiento_Promesa_de_Marca'],"Desempeño_del_Canal "=>$value['Desempeño_del_Canal'],"Desempeño_del_Agente "=>$value['Desempeño_del_Agente'],"Habilidad_Comercia l"=>$value['Habilidad_Comercial']));
        }
      }

      die(json_encode(array("status"=>"1","data"=>$arraydatafG)));

    }
       
    

  }

?>
