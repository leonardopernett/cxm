<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use app\models\ControlProcesosPlan;
use yii\db\Query;

$this->title = 'Hoja de Vida - Data Personal';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sesiones = Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol     ->select(['tbl_roles.role_id'])
                ->from('tbl_roles')
                ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                            'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                ->where('tbl_usuarios.usua_id = '.$sesiones.'');                    
    $command = $rol->createCommand();
    $roles = $command->queryScalar();


?>
<style>
    .card1 {
            height: auto;
            width: auto;
            margin-top: auto;
            margin-bottom: auto;
            background: #FFFFFF;
            position: relative;
            display: flex;
            justify-content: center;
            flex-direction: column;
            padding: 10px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            border-radius: 5px;    
            font-family: "Nunito",sans-serif;
            font-size: 150%;    
            text-align: left;    
    }


    .col-sm-6 {
        width: 100%;
    }

    th {
        text-align: left;
        font-size: smaller;
    }

    .masthead {
        height: 25vh;
        min-height: 100px;
        background-image: url('../../images/ADMINISTRADOR-GENERAL.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

</style>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<!-- Full Page Image Header with Vertically Centered Content -->
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>
<br><br>
<?php
  foreach ($dataProviderInfo as $key => $value) {   
      $varIdAccion = $value['hv_idpersonal'];
      $varIdClientes = $value['IdCliente'];
?>
  <div id="idcapauno" class="capaPrincipal" style="display: inline;">
    <div class="row">
      <div class="col-md-12">
        <div class="card1 mb">
          <label style="font-size: 15px;"><em class="fas fa-info" style="font-size: 15px; color: #FFC72C;"></em> Datos Personales </label>

          <table id="tblDataInfo" class="table table-striped table-bordered tblResDetFreed">
            <caption><?php echo "Resultados"; ?></caption>
            <tbody>
              <tr>
                <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Nombre Completo') ?></label></th>
                <td><label style="font-size: 12px;"><?php echo  $value['NombreFull']; ?></label></td>
                <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Identificación') ?></label></th>
                <td><label style="font-size: 12px;"><?php echo  $value['Identificacion']; ?></label></td>
              </tr>
              <tr>
                <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Número Móvil') ?></label></th>
                <td><label style="font-size: 12px;"><?php echo  $value['Movil']; ?></label></td>
                <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Número Fijo') ?></label></th>
                <td><label style="font-size: 12px;"><?php echo  $value['Fijo']; ?></label></td>
              </tr>
              <tr>
                <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Dirección Oficina') ?></label></th>
                <td><label style="font-size: 12px;"><?php echo  $value['DireccioOficina']; ?></label></td>
                <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Dirección Casa') ?></label></th>
                <td><label style="font-size: 12px;"><?php echo  $value['DireccionCasa']; ?></label></td>
              </tr>
              <tr>
                <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Correo Electrónico') ?></label></th>
                <td><label style="font-size: 12px;"><?php echo  $value['Email']; ?></label></td>
                <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Modalidad Trabajo') ?></label></th>
                <td><label style="font-size: 12px;"><?php echo  $value['Modalidad']; ?></label></td>
              </tr>
              <tr>
                <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Tratamiento de Datos Personales') ?></label></th>
                <td><label style="font-size: 12px;"><?php echo  $value['TratamientoDatos']; ?></label></td>
                <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Susceptibe a Encuestar') ?></label></th>
                <td><label style="font-size: 12px;"><?php echo  $value['Susceptible']; ?></label></td>
              </tr>
              <tr>
                <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Pais') ?></label></th>
                <td><label style="font-size: 12px;"><?php echo  $value['Pais']; ?></label></td>
                <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Ckudad') ?></label></th>
                <td><label style="font-size: 12px;"><?php echo  $value['Ciudad']; ?></label></td>
              </tr>
              <tr>
                <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Indicador Satu') ?></label></th>
                <td><label style="font-size: 12px;"><?php echo  $value['IndicadorSatu'].'%'; ?></label></td>
              </tr>
            </tbody>
          </table>

        </div>
      </div>
    </div>
  </div>
  <hr>
  <div id="idcapados" class="capaDos" style="display: inline;">
    <div class="row">
      <div class="col-md-12">
        <div class="card1 mb">
          <label style="font-size: 15px;"><em class="fas fa-plus-square" style="font-size: 15px; color: #FFC72C;"></em> Datos Laborales </label>

          <table id="tblDataLaboral" class="table table-striped table-bordered tblResDetFreed">
            <caption><?php echo "Resultados"; ?></caption>
            <tbody>
              <tr>
                <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Rol') ?></label></th>
                <td><label style="font-size: 12px;"><?php echo  $value['Rol']; ?></label></td>
                <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Antiguedad de Rol') ?></label></th>
                <td><label style="font-size: 12px;"><?php echo  $value['Antiguedad']; ?></label></td>
              </tr>
              <tr>
                <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Fecha Inicio Como Contacto') ?></label></th>
                <td><label style="font-size: 12px;"><?php echo  $value['FechaContacto']; ?></label></td>
                <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Trabajo Anterior') ?></label></th>
                <td><label style="font-size: 12px;"><?php echo  $value['TrabajoAnterior']; ?></label></td>
              </tr>
              <tr>
                <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Nombre del Jefe') ?></label></th>
                <td><label style="font-size: 12px;"><?php echo  $value['NombreJefe']; ?></label></td>
                <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cargo del Jefe') ?></label></th>
                <td><label style="font-size: 12px;"><?php echo  $value['CargoJefe']; ?></label></td>
              </tr>
              <tr>
                <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Afinidad') ?></label></th>
                <td><label style="font-size: 12px;"><?php echo  $value['Afinidad']; ?></label></td>
                <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Tipo Afinidad') ?></label></th>
                <td><label style="font-size: 12px;"><?php echo  $value['TipoAfinidad']; ?></label></td>
              </tr>
              <tr>
                <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Nivel de Afinidad') ?></label></th>
                <td><label style="font-size: 12px;"><?php echo  $value['NivelAfinidad']; ?></label></td>
              </tr>
            </tbody>
          </table>

        </div>
      </div>
    </div>
  </div>
  <hr>
  <div id="idcapatres" class="capaTres" style="display: inline;">
    <div class="row">
      <div class="col-md-12">
        <div class="card1 mb">
          <label style="font-size: 15px;"><em class="fas fa-check-circle" style="font-size: 15px; color: #FFC72C;"></em> Datos de la Cuenta </label>

          <?php 

            $paramscliente = [':idhvaccion' => $varIdAccion, ':idclientes' => $varIdClientes ];
            $varCliente = Yii::$app->db->createCommand('
              SELECT pc.cliente  FROM tbl_proceso_cliente_centrocosto pc
                INNER JOIN tbl_hojavida_datapcrc hd ON 
                  pc.id_dp_clientes = hd.id_dp_cliente
                INNER JOIN tbl_hojavida_datapersonal dp ON 
                  hd.hv_idpersonal = dp.hv_idpersonal
                WHERE 
                  dp.hv_idpersonal = :idhvaccion AND hd.id_dp_cliente = :idclientes
                GROUP BY pc.id_dp_clientes')->bindValues($paramscliente)->queryScalar();

            $paramsaccion = [':idhvacciones' => $varIdAccion];
            $varListaDirectores = Yii::$app->db->createCommand('
              SELECT  cc.director_programa  AS Programa
                FROM tbl_hojavida_datadirector dc 
                  INNER JOIN tbl_proceso_cliente_centrocosto cc ON 
                    dc.ccdirector = cc.documento_director
                  WHERE dc.hv_idpersonal = :idhvacciones GROUP BY cc.director_programa')->bindValues($paramsaccion)->queryAll();
            
            $varArrayDirectores = array();
            foreach ($varListaDirectores as $key => $value) {
              array_push($varArrayDirectores, $value['Programa']);
            }
            $varDirectoresListado = implode("; ", $varArrayDirectores);


            $varListaGerente = Yii::$app->db->createCommand('
              SELECT  cc.gerente_cuenta  AS Programagerente
                FROM tbl_hojavida_datagerente dc 
                  INNER JOIN tbl_proceso_cliente_centrocosto cc ON 
                    cc.documento_gerente = dc.ccgerente
                  WHERE dc.hv_idpersonal = :idhvacciones GROUP BY cc.gerente_cuenta')->bindValues($paramsaccion)->queryAll();
            
            $varArrayGerentes = array();
            foreach ($varListaGerente as $key => $value) {
              array_push($varArrayGerentes, $value['Programagerente']);
            }
            $varGerentesListado = implode("; ", $varArrayGerentes);

            $varVerificaPcrc = Yii::$app->db->createCommand('
              SELECT GROUP_CONCAT(cc.cod_pcrc," - ",cc.pcrc SEPARATOR "; ") AS Programa
                FROM tbl_hojavida_datapcrc dc 
                  INNER JOIN tbl_proceso_cliente_centrocosto cc ON 
                    dc.cod_pcrc = cc.cod_pcrc
                  WHERE dc.hv_idpersonal = :idhvacciones')->bindValues($paramsaccion)->queryScalar();
          ?>

          <table id="tblDataCuentas" class="table table-striped table-bordered tblResDetFreed">
            <caption><?php echo "Resultados"; ?></caption>
            <tbody>
              <tr>
                <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Servicio') ?></label></th>
                <td><label style="font-size: 12px;"><?php echo  $varCliente; ?></label></td>
              </tr>
              <tr>
                <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Directores') ?></label></th>
                <td><label style="font-size: 12px;"><?php echo  $varDirectoresListado; ?></label></td>
              </tr>
              <tr>
                <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Gerentes') ?></label></th>
                <td><label style="font-size: 12px;"><?php echo  $varGerentesListado; ?></label></td>
              </tr>              
              <tr>
                <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Programa Pcrc') ?></label></th>
                <td><label style="font-size: 12px;"><?php echo  $varVerificaPcrc; ?></label></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div id="idcapacuatro" class="capaCuatro" style="display: inline;">
    <div class="row">
      <div class="col-md-12">
        <div class="card1 mb">
          <label style="font-size: 15px;"><em class="fas fa-plus" style="font-size: 15px; color: #FFC72C;"></em> Datos Acad&eacute;micos </label>

          <?php 
            $varListaAcademica = Yii::$app->db->createCommand('
              SELECT n.hvacademico AS Nivel, c.hv_cursos AS Cursos FROM tbl_hv_nivelacademico n 
                  INNER JOIN tbl_hv_cursosacademico c ON  
                    n.idhvacademico = c.idhvacademico
                  INNER JOIN tbl_hojavida_dataacademica da ON 
                    c.idhvcursosacademico = da.idhvcursosacademico
                  WHERE 
                    da.hv_idpersonal = :idhvacciones')->bindValues($paramsaccion)->queryAll();

            foreach ($varListaAcademica as $key => $value) {              
            
          ?>
            <table id="tblDataAcademico" class="table table-striped table-bordered tblResDetFreed">
              <caption><?php echo "Resultados"; ?></caption>
              <tbody>
                <tr>
                  <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', $value['Nivel']) ?></label></th>
                  <td><label style="font-size: 12px;"><?php echo  $value['Cursos']; ?></label></td>
                </tr>
              </tbody>
            </table>
          <?php
            }
          ?>

        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="capaCinco" style="display: inline;">
    <div class="row">
      <div class="col-md-12">        
        <div class="card1 mb">
          <label style="font-size: 15px;"><em class="fas fa-calendar" style="font-size: 15px; color: #FFC72C;"></em> Eventos: </label> 

          <?php 
            $varListaEventos = Yii::$app->db->createCommand('
              SELECT he.nombre_evento AS Evento, he.tipo_evento AS TipoEvento, c.ciudad AS Ciudad,
                he.fecha_evento_inicio AS FechaEvento, he.asistencia AS comentarios FROM tbl_hv_ciudad c
                INNER JOIN tbl_hojavida_eventos he ON 
                  c.hv_idciudad = he.hv_idciudad
                INNER JOIN tbl_hojavida_asignareventos ha ON 
                  he.hv_ideventos = ha.hv_ideventos
                INNER JOIN tbl_hojavida_datapersonal dp ON 
                  ha.hv_idpersonal = dp.hv_idpersonal
                WHERE 
                  dp.hv_idpersonal = :idhvacciones')->bindValues($paramsaccion)->queryAll();

            
          ?>

            <table id="tblDataEventos" class="table table-striped table-bordered tblResDetFreed">
              <caption><?php echo "Resultados"; ?></caption>
              <thead>
                <tr>
                  <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Evento') ?></label></th>
                  <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Tipo Evento') ?></label></th>
                  <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Ciudad') ?></label></th>
                  <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Fecha Evento') ?></label></th>
                  <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Comentarios') ?></label></th>
                </tr>
              </thead>
              <tbody>
                <?php 
                  foreach ($varListaEventos as $key => $value) { 
                ?>
                <tr>                  
                  <td><label style="font-size: 12px;"><?php echo  $value['Evento']; ?></label></td>
                  <td><label style="font-size: 12px;"><?php echo  $value['TipoEvento']; ?></label></td>
                  <td><label style="font-size: 12px;"><?php echo  $value['Ciudad']; ?></label></td>
                  <td><label style="font-size: 12px;"><?php echo  $value['FechaEvento']; ?></label></td>
                  <td><label style="font-size: 12px;"><?php echo  $value['comentarios']; ?></label></td>
                </tr>
                <?php
                  }
                ?>
              </tbody>
            </table>         

        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="capaCinco" style="display: inline;">
    <div class="row">
      <div class="col-md-12">
        
        <div class="card1 mb">
          <label style="font-size: 15px;"><em class="fas fa-square" style="font-size: 15px; color: #FFC72C;"></em> Complementos: </label> 

          <div class="row">
            <div class="col-md-12">
                <table id="tblDataComplementos" class="table table-striped table-bordered tblResDetFreed">
                  <caption><?php echo "Resultados Complementos"; ?></caption>
                  <thead>
                    <tr>
                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Estado Civil') ?></label></th>
                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Estilo Social') ?></label></th>
                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Dominancia Cerebral') ?></label></th>
                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Hobbies') ?></label></th>
                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Gustos') ?></label></th>
                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cantidad Hijos') ?></label></th>
                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Nombre Hijos') ?></label></th>
                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acciones') ?></label></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                        $varListaComplementos = Yii::$app->db->createCommand('
                          SELECT dc.hv_idcomplemento, hd.estadocivil, dc.cantidadhijos, dc.NombreHijos, d.dominancia,
                          e.estilosocial, g.text, h.text AS hobbies FROM tbl_hojavida_datacomplementos dc
                            INNER JOIN tbl_hojavida_datacivil hd ON 
                              dc.hv_idcivil = hd.hv_idcivil
                            INNER JOIN tbl_hv_dominancias d ON 
                              dc.iddominancia = d.iddominancia
                            INNER JOIN tbl_hv_estilosocial e ON 
                              dc.idestilosocial = e.idestilosocial
                            INNER JOIN tbl_hv_gustos g ON 
                              dc.idgustos = g.id
                            INNER JOIN tbl_hv_hobbies h ON 
                              dc.idhobbies = h.id
                            INNER JOIN tbl_hojavida_datapersonal dp ON 
                              dc.hv_idpersonal = dp.hv_idpersonal
                            WHERE 
                              dp.hv_idpersonal = :idhvacciones')->bindValues($paramsaccion)->queryAll();

                        foreach ($varListaComplementos as $key => $value) { 
                        
                    ?>             
                      <tr>
                        <td><label style="font-size: 12px;"><?php echo  $value['estadocivil']; ?></label></td>
                        <td><label style="font-size: 12px;"><?php echo  $value['cantidadhijos']; ?></label></td>
                        <td><label style="font-size: 12px;"><?php echo  $value['NombreHijos']; ?></label></td>
                        <td><label style="font-size: 12px;"><?php echo  $value['dominancia']; ?></label></td>
                        <td><label style="font-size: 12px;"><?php echo  $value['estilosocial']; ?></label></td>
                        <td><label style="font-size: 12px;"><?php echo  $value['text']; ?></label></td>
                        <td><label style="font-size: 12px;"><?php echo  $value['hobbies']; ?></label></td>
                        <td class="text-center">
                          <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['deletecomplementos','id'=> $value['hv_idcomplemento'], 'idsinfo' =>$idinfo], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
                        </td>
                      </tr>
                    <?php
                      }
                    ?>
                  </tbody>
                </table>
            </div>
          </div>

        </div>

      </div>
    </div>
  </div>
  <hr>
<?php } ?>
<div class="capaBtn" style="display: inline;">
  <div class="row">
    <div class="col-md-12">
      
      <div class="row">
        <div class="col-md-6">
          <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #FFC72C;"></em> Cancelar y regresar: </label> 
            <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #707372',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Regresar']) 
            ?>
          </div>
        </div>

    </div>
  </div>
</div>

<hr>

