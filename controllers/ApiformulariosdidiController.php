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


  class ApiformulariosdidiController extends \yii\web\Controller {

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
                'actions' => ['apiinfogeneral','apiinfosesiones','apiinfobloques','apiinfopreguntas','apiinfotipificacion'],
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema();
                        },
              ],
              [
                'actions' => ['apiinfogeneral','apiinfosesiones','apiinfobloques','apiinfopreguntas','apiinfotipificacion'],
                'allow' => true,

              ],
            ],

        ],

        
      ];
    }
  
    public function init(){
      $this->enableCsrfValidation = false;
    }

    public function actionApiinfogeneral(){

      $datapostG = file_get_contents('php://input');
      $data_postG = json_decode($datapostG,true);
  
      if (
           !isset($data_postG["fechaInicio"]) 
        || !isset($data_postG["fechaFin"]) 
        || empty($data_postG["fechaInicio"]) 
        || empty($data_postG["fechaFin"]) 
      ) {
        die(json_encode(array("status"=>"0","data"=>"Algunos de los campos obligatorios no se enviaron correctamente")));
      }

      $varPcrcPadre = 4041;   
      $varFechaInicio = $data_postG["fechaInicio"];
      $varFechaFin = $data_postG["fechaFin"];


      $varParamsBuscar = [':varPcrcPadre_g'=>intval($varPcrcPadre),':varFechaInicio_g'=>$varFechaInicio.' 00:00:00',':varFechaFin_g'=>$varFechaFin.' 23:59:59',':varAnulado'=>intval(0)];

      $varListFormulariosG = Yii::$app->db->createCommand('
      SELECT 
        f.id, f.basesatisfaccion_id AS ID_Encuesta, f.created AS FechaYHora, 
        f.hora_inicial AS HoraInicialValoracion, f.hora_final AS HoraFinalValoracion, 
        f.cant_modificaciones AS CantModificaciones, f.tiempo_modificaciones AS TiempoModificaciones, 
        d.name AS Dimension, aa.name AS ArbolPadre, a.id AS IDArbol, a.name AS Arbol, 
        ff.name AS Formulario, re.cod_pcrc AS CentroCostos, 
        CONCAT(re.cod_pcrc," - ",pc.pcrc) ProgramaPcrc, uq.usua_nombre AS Lider, 
        e.name AS Asesor, e.identificacion AS CedulaEvaluado, e.name AS NombreEvaluado, 
        (select ux.usua_nombre from tbl_usuarios ux where ux.usua_id = f.usua_id_lider) AS Rsponsable,
        uu.usua_nombre AS Evaluador, r.role_nombre AS rol, f.dsfuente_encuesta AS Fuente, 
        t.name AS Transacciones, eq.name AS Equipo, f.dscomentario AS Comentarios, f.score AS Score,  
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
          LEFT JOIN tbl_registro_ejec_cliente re ON 
            re.ejec_form_id = f.id
          LEFT JOIN tbl_proceso_cliente_centrocosto pc ON 
            pc.cod_pcrc = re.cod_pcrc

        WHERE
          a.arbol_id = :varPcrcPadre_g
            AND a.activo = :varAnulado
                AND f.created BETWEEN :varFechaInicio_g AND :varFechaFin_g
      ')->bindValues($varParamsBuscar)->queryAll();

      $arraydatafG = array();
      foreach ($varListFormulariosG as $value) {
        array_push($arraydatafG, array("Id_Formulario "=>$value['id'],"Id_Encuesta "=>$value['ID_Encuesta'],"Fecha y Hora"=>$value['FechaYHora'],"Hora Inicial Valoracion"=>$value['HoraInicialValoracion'],"Hora Final Valoracion"=>$value['HoraFinalValoracion'],"Cantidad_Modificaciones "=>$value['CantModificaciones'],"Dimension"=>$value['Dimension'],"Programa_PCRC Padre "=>$value['ArbolPadre'],"Id_Pcrc "=>$value['IDArbol'],"Programa_PCRC"=>$value['Arbol'],"Formulario "=>$value['Formulario'],"Centro_Costos"=>$value['CentroCostos'],"Nombre_Pcrc"=>$value['ProgramaPcrc'],"Nombre_Lider "=>$value['Lider'],"Valorado "=>$value['Asesor'],"Cedula Valorado"=>$value['CedulaEvaluado'],"Responsable "=>$value['Rsponsable'],"Valorador "=>$value['Evaluador'],"Rol "=>$value['rol'],"Fuente "=>$value['Fuente'],"Transaccion"=>$value['Transacciones'],"Equipo"=>$value['Equipo'],"Comentario"=>$value['Comentarios'],"Score "=>$value['Score'],"PEC "=>$value['PEC'],"PENC "=>$value['PENC'],"SPC_FRC"=>$value['SPC_FRC'],"CARINO_WOW"=>$value['CARINO_WOW'],"Indice de Proceso"=>$value['Indice_de_Proceso'],"Indice de Experiencia"=>$value['Indice_de_Experiencia'],"Cumplimiento Promesa de Marca"=>$value['Cumplimiento_Promesa_de_Marca'],"Desempeno del Canal"=>$value['Desempeño_del_Canal'],"Desempeno del Agente"=>$value['Desempeño_del_Agente'],"Habilidad_Comercia l"=>$value['Habilidad_Comercial']));  
      }

      die(json_encode(array("status"=>"1","data"=>$arraydatafG)));
    }

    public function actionApiinfosesiones(){

      $datapost_s = file_get_contents('php://input');
      $data_post_s = json_decode($datapost_s,true);
  
      if (
           !isset($data_post_s["fechaInicio"]) 
        || !isset($data_post_s["fechaFin"])
        || empty($data_post_s["fechaInicio"]) 
        || empty($data_post_s["fechaFin"]) 
      ) {
        die(json_encode(array("status"=>"0","data"=>"Algunos de los campos obligatorios no se enviaron correctamente")));
      }

      $varPcrcPadre_s = 4041;   
      $varFechaInicio_s = $data_post_s["fechaInicio"];
      $varFechaFin_s = $data_post_s["fechaFin"];

      $varParamsBuscar_s = [':varPcrcPadre_s'=>intval($varPcrcPadre_s),':varFechaInicio_s'=>$varFechaInicio_s.' 00:00:00',':varFechaFin_s'=>$varFechaFin_s.' 23:59:59'];

      $varListSecciones = Yii::$app->db->createCommand('
        SELECT s.id, s.name AS Seccion FROM tbl_seccions s
          INNER JOIN tbl_ejecucionformularios ef ON 
            s.formulario_id = ef.formulario_id
          INNER JOIN tbl_arbols a ON 
            ef.formulario_id = a.formulario_id
        WHERE 
          a.arbol_id = :varPcrcPadre_s
            AND ef.created BETWEEN :varFechaInicio_s AND :varFechaFin_s
        GROUP BY s.id')->bindValues($varParamsBuscar_s)->queryAll();

      $arraydatas_s = array();
      foreach ($varListSecciones as $value) {
        $varIdSeccion_s = $value['id'];
        $varNombreSeccion_s = $value['Seccion'];

        $paramsSessiones_s = [':Id_Session_s' => $varIdSeccion_s];
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
                es.seccion_id = :Id_Session_s ')->bindValues($paramsSessiones_s)->queryAll();

        foreach ($varListScoreS as $value) {
          array_push($arraydatas_s, array("Id_Formulario "=>$value['Id_Formulario'],"Id_Session "=>$varIdSeccion_s,"Seccion "=>$varNombreSeccion_s,"Score"=>$value['Score']));
        }
      }

      die(json_encode(array("status"=>"1","data"=>$arraydatas_s)));
    }

    public function actionApiinfobloques(){

      $datapost_b = file_get_contents('php://input');
      $data_post_b = json_decode($datapost_b,true);
  
      if (
           !isset($data_post_b["fechaInicio"]) 
        || !isset($data_post_b["fechaFin"])
        || empty($data_post_b["fechaInicio"]) 
        || empty($data_post_b["fechaFin"]) 
      ) {
        die(json_encode(array("status"=>"0","data"=>"Algunos de los campos obligatorios no se enviaron correctamente")));
      }

      $varPcrcPadre_b = 4041;   
      $varFechaInicio_b = $data_post_b["fechaInicio"];
      $varFechaFin_b = $data_post_b["fechaFin"];

      $varParamsBuscar_b = [':varPcrcPadre_b'=>intval($varPcrcPadre_b),':varFechaInicio_b'=>$varFechaInicio_b.' 00:00:00',':varFechaFin_b'=>$varFechaFin_b.' 23:59:59'];

      $varListBloques = Yii::$app->db->createCommand('
        SELECT  ef.id, s.id AS id_Seccion, b.id AS id_Bloque, b.name AS Bloque FROM tbl_bloques b
          INNER JOIN tbl_seccions s ON 
            b.seccion_id = s.id
          INNER JOIN tbl_ejecucionformularios ef ON 
            s.formulario_id = ef.formulario_id
          INNER JOIN tbl_arbols a ON 
            ef.formulario_id = a.formulario_id    
        WHERE 
          a.arbol_id = :varPcrcPadre_b
            AND ef.created BETWEEN :varFechaInicio_b AND :varFechaFin_b
        GROUP BY b.id
          ORDER BY ef.id')->bindValues($varParamsBuscar_b)->queryAll();

      $arraydatab_b = array();
      foreach ($varListBloques as $value) {
        $varIdSecciones_b = $value['id_Seccion'];
        $varIdBloques_b = $value['id_Bloque'];
        $varNombreBloque_b = $value['Bloque'];

        $paramsBloques = [':IdSeccion' => $varIdSecciones_b, 'IdBloques' => $varIdBloques_b];
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

        foreach ($varListScoreB as $value) {
          array_push($arraydatab_b, array("Id_Formulario "=>$value['id_Formulario'],"Id_Sesion"=>$varIdSecciones_b,"Id_Bloque "=>$varIdBloques_b,"Bloque "=>$varNombreBloque_b,"Score"=>$value['Score'])); 
        }
      }

      die(json_encode(array("status"=>"1","data"=>$arraydatab_b)));
    }

    public function actionApiinfopreguntas(){

      $datapost_p = file_get_contents('php://input');
      $data_post_p = json_decode($datapost_p,true);
  
      if (
           !isset($data_post_p["fechaInicio"]) 
        || !isset($data_post_p["fechaFin"])
        || empty($data_post_p["fechaInicio"]) 
        || empty($data_post_p["fechaFin"]) 
      ) {
        die(json_encode(array("status"=>"0","data"=>"Algunos de los campos obligatorios no se enviaron correctamente")));
      }

      $varPcrcPadre_p = 4041;   
      $varFechaInicio_p = $data_post_p["fechaInicio"];
      $varFechaFin_p = $data_post_p["fechaFin"];

      $varParamsBuscar_p = [':varPcrcPadre_p'=>intval($varPcrcPadre_p),':varFechaInicio_p'=>$varFechaInicio_p.' 00:00:00',':varFechaFin_p'=>$varFechaFin_p.' 23:59:59'];

      $varListPreguntas = Yii::$app->db->createCommand('
      SELECT ef.id AS Id_Formulario, s.id AS id_Seccion, b.id AS id_Bloque, b.name AS Bloque, bd.id AS id_pregunta, bd.name AS Pregunta FROM tbl_bloquedetalles bd
        INNER  JOIN tbl_bloques b ON 
          bd.bloque_id = b.id
        INNER JOIN tbl_seccions s ON 
          b.seccion_id = s.id
        INNER JOIN tbl_ejecucionformularios ef ON 
          s.formulario_id = ef.formulario_id
        INNER JOIN tbl_arbols a ON 
          ef.formulario_id = a.formulario_id     
      WHERE 
        a.arbol_id = :varPcrcPadre_p
          AND ef.created BETWEEN :varFechaInicio_p AND :varFechaFin_p')->bindValues($varParamsBuscar_p)->queryAll();

      $arraydatap = array();
      foreach ($varListPreguntas as $value) {
        $paramsRta = [':idSession' => $value['id_Seccion'], ':idBloque' => $value['id_Bloque'], ':idPregunta' => $value['id_pregunta'], ':IdFormulario' => $value['Id_Formulario']];

        $varListRtas = Yii::$app->db->createCommand('
        SELECT  cd.name AS Respuesta FROM tbl_calificaciondetalles cd
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
          WHERE
            s.id = :idSession
              AND b.id = :idBloque
                AND bd.id = :idPregunta 
                  AND es.ejecucionformulario_id = :IdFormulario')->bindValues($paramsRta)->queryScalar();

        array_push($arraydatap, array("Id_Formulario "=>$value['Id_Formulario'],"Pregunta "=>$value['Pregunta'],"Respuesta "=>$varListRtas)); 
      }

      die(json_encode(array("status"=>"1","data"=>$arraydatap)));
    }

    public function actionApiinfotipificacion(){
      $datapost_t = file_get_contents('php://input');
      $data_post_t = json_decode($datapost_t,true);
  
      if (
           !isset($data_post_t["fechaInicio"]) 
        || !isset($data_post_t["fechaFin"])
        || empty($data_post_t["fechaInicio"]) 
        || empty($data_post_t["fechaFin"]) 
      ) {
        die(json_encode(array("status"=>"0","data"=>"Algunos de los campos obligatorios no se enviaron correctamente")));
      }

      $varPcrcPadre_t = 4041;   
      $varFechaInicio_t = $data_post_t["fechaInicio"];
      $varFechaFin_t = $data_post_t["fechaFin"];
 

      $varListadoForms_t = (new \yii\db\Query())
                                ->select(['tbl_arbols.formulario_id'])
                                ->from(['tbl_arbols'])            
                                ->where(['=','tbl_arbols.arbol_id',$varPcrcPadre_t])
                                ->all();

      $arrayListadoForms_t = array();
      foreach ($varListadoForms_t as $value) {
        array_push($arrayListadoForms_t, $value['formulario_id']);
      }

      $listaformulariosarray = implode("', '", $arrayListadoForms_t);

      $ListadoForms_t = explode(",", str_replace(array("#", "'", ";", " "), '', $listaformulariosarray));

      $varParamsBuscar_t = [':varPcrcPadre_t'=>$ListadoForms_t,':varFechaInicio_t'=>$varFechaInicio_t.' 00:00:00',':varFechaFin_t'=>$varFechaFin_t.' 23:59:59'];

      $varListTipificacionescontiene = (new \yii\db\Query())
                            ->select([
                              'ef.id AS Id_Formulario', 
                              'tbl_seccions.id AS Id_sesiones', 
                              'tbl_bloques.id AS Id_Bloques',
                              'b.id AS Id_Preguntas', 
                              'tbl_tipificaciondetalles.id AS id_Tipificacion',  
                              'tbl_tipificaciondetalles.name AS Tipificaciones', 
                              'if(b.id>0,"1","0") AS Rtatipi', 
                              'ef.created AS FechaValoracion'
                            ])
                            ->from(['tbl_tipificaciondetalles'])

                            ->join('LEFT OUTER JOIN', 'tbl_bloquedetalles b',
                                  'b.tipificacion_id = tbl_tipificaciondetalles.tipificacion_id')

                            ->join('LEFT OUTER JOIN', 'tbl_bloques',
                                  'tbl_bloques.id = b.bloque_id')

                            ->join('LEFT OUTER JOIN', 'tbl_ejecucionbloques',
                                  'tbl_bloques.id = tbl_ejecucionbloques.bloque_id')

                            ->join('LEFT OUTER JOIN', 'tbl_ejecucionbloquedetalles',
                                  'tbl_ejecucionbloquedetalles.ejecucionbloque_id = tbl_ejecucionbloques.id 
                                      AND tbl_ejecucionbloquedetalles.bloquedetalle_id = b.id')

                            ->join('LEFT OUTER JOIN', 'tbl_tipificaciondetalles xtd',
                                  'b.tipificacion_id = xtd.tipificacion_id')

                            ->join('LEFT OUTER JOIN', 'tbl_ejecucionbloquedetalles_tipificaciones t',
                                  'xtd.id = t.tipificaciondetalle_id 
                                    AND t.ejecucionbloquedetalle_id = tbl_ejecucionbloquedetalles.id')

                            ->join('LEFT OUTER JOIN', 'tbl_ejecucionseccions',
                                  'tbl_ejecucionbloques.ejecucionseccion_id = tbl_ejecucionseccions.id')

                            ->join('LEFT OUTER JOIN', 'tbl_seccions',
                                  'tbl_seccions.id = tbl_ejecucionseccions.seccion_id')

                            ->join('LEFT OUTER JOIN', 'tbl_ejecucionformularios ef',
                                  'tbl_ejecucionseccions.ejecucionformulario_id = ef.id')

                            ->where(['in','ef.formulario_id',$ListadoForms_t])
                            ->andwhere(['between','ef.created',$varFechaInicio_t.' 00:00:00',$varFechaFin_t.' 23:59:59'])
                            ->andwhere(['is not','t.tipificaciondetalle_id',null])
                            ->groupby(['ef.id','tbl_tipificaciondetalles.id'])
                            ->all();

      $arraydatapicon_t = array();
      foreach ($varListTipificacionescontiene as $key => $value) {

        array_push($arraydatapicon_t, array("Id_Formulario "=>$value['Id_Formulario'],"Id_sesiones"=>$value['Id_sesiones'],"Id_Bloques"=>$value['Id_Bloques'],"Id_Preguntas"=>$value['Id_Preguntas'],"Id_Tipificacion "=>$value['id_Tipificacion'],"Tipificaciones "=>$value['Tipificaciones'],'RespuestaTipificacion'=>$value['Rtatipi'])); 
      }


      die(json_encode(array("status"=>"1","data"=>$arraydatapicon_t)));
    }
    

  }

?>
