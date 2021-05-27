<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;

$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-3">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;
    $valor = null;

   $txtcontar = count($txtidbloque);
 

?>

<div class="capaCero" style="display: inline">
    <a id="dlink" style="display:none;"></a>
    <button  class="btn btn-info" style="background-color: #4298B4" id="btn">Exportar a Excel</button>
</div>
<div class="capaUno" style="display: none">
    <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
        <thead>
           
            <th class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Fecha y Hora de la valoracion') ?></th>
            <th class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Fecha y Hora de la llamada') ?></th>
            <th class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Dimension') ?></th>
            <th class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Programa/Pcrc Padre') ?></th>
            <th class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Programa/Pcrc') ?></th>
            <th class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Formulario') ?></th>            
            <th class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Cedula Valorado') ?></th>
            <th class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Valorado') ?></th>
            <th class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Responsable') ?></th>            
            <th class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Valorador') ?></th>
            <th class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Rol') ?></th>
            <th class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Id Externo Speech') ?></th>            
            <th class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Usuario de Agente') ?></th>
            <th class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Equipo') ?></th>
            <th class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Duracion') ?></th>
            <th class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Extension') ?></th>
            <th class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'ESCUCHA FOCALIZADA') ?></th>
            <th class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Indicadores Globales') ?></th>
            <th class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Variable') ?></th>
            <th class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Motivos de Contacto') ?></th>
            <th class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Motivos de llamadas') ?></th>
            <th class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Punto de Dolor') ?></th>
            <th class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Llamada bien categorizada Si/No') ?></th>
            <th class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', '% Dedicacion') ?></th>
            <th class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Agente') ?></th>
            <th class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Marca') ?></th>
            <th class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Canal') ?></th>
            <th class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Detalle Cualitativo') ?></th>
            <th class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Mapa de Interesados 1') ?></th>
            <th class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Mapa de interesados 2') ?></th>
            <th class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Atributos de Calidad') ?></th>
        </thead>
    <tbody>
            <?php
                $txtdataID = null;
                $txtUsuanombre = null;
                $txtNombre = null;
                $txtDsname = null;
                $txtDimensions = null;
                $varUsuaid = null;
                $varRolid = null;
                $varequipoid= null;
                $txtValorador = null;
                $txtArbol_id = null;
                $txtFechacreacion = null;
                $txtTecnico = null;
                $txttitulo = 'ESCUCHA FOCALIZADA';
                $txtcanti=0;
                if ($txtcontar > 1) {
                   $txtValorador = $txtidbloque[1];
                   $txtArbol_id = $txtidbloque[2];
                   $txtFechacreacion = $txtidbloque[3];
                   $txtTecnico = $txtidbloque[4];
                   $txtFecha = explode(" ", $txtidbloque[3]);
                   $txtcanti = count($txtFecha);
                     if ($txtcanti > 1) {
                        $txtfechaini = $txtFecha[0];
                        $txtfechafin = $txtFecha[2]; 
                     }                  
                 }
                 $sessiones = Yii::$app->user->identity->id;   
                // $sessiones = 2270;
                 $varAnulado = 0;
                 $varMed = 2;
                 $varBog = 98;
                 $varK = 1;
                 $rol =  new Query;
   	         $rol      ->select(['tbl_roles.role_id'])
        	           ->from('tbl_roles')
                           ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                                 'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
               	           ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                           'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                           ->where('tbl_usuarios.usua_id = '.$sessiones.'');                    
   	        $command = $rol->createCommand();
   	        $roles = $command->queryScalar();
  
              if ($roles == "270"){

                        $querys =  new Query;
                        $querys     ->select(['tbl_controlvoc_bloque1.idbloque1', 'tbl_controlvoc_bloque1.fechacreacion','tbl_usuarios.usua_nombre', 'rel_usuarios_roles.rel_role_id', 'tbl_evaluados.name', 'tbl_arbols.dsname_full', 'tbl_arbols.arbol_id', 'tbl_arbols.formulario_id', 'tbl_evaluados.identificacion', 'tbl_controlvoc_bloque1.dimensions', 'tbl_equipos.usua_id', 'tbl_equipos.id'])->distinct()
                                    ->from('tbl_controlvoc_bloque1')
                                    ->join('LEFT JOIN', 'tbl_arbols',
                                            'tbl_controlvoc_bloque1.arbol_id = tbl_arbols.id')
                                    ->join('LEFT JOIN', 'tbl_usuarios',
                                            'tbl_controlvoc_bloque1.valorador_id = tbl_usuarios.usua_id')
                                    ->join('LEFT JOIN', 'rel_usuarios_roles',
                                            'tbl_usuarios.usua_id = rel_usuarios_roles.rel_usua_id')
                                    ->join('LEFT JOIN', 'tbl_evaluados',
                                            'tbl_controlvoc_bloque1.tecnico_id = tbl_evaluados.id')
                                    ->join('LEFT JOIN', 'tbl_equipos_evaluados',
                                            'tbl_evaluados.id = tbl_equipos_evaluados.evaluado_id')
                                    ->join('LEFT JOIN', 'tbl_equipos',
                                            'tbl_equipos.id = tbl_equipos_evaluados.evaluado_id' );
                           $querys  -> where('tbl_arbols.activo = '.$varAnulado.'');
                           $querys  -> andwhere('tbl_arbols.arbol_id != '.$varK.'');

                   	if ($txtValorador != null) { 
			   $querys  -> andwhere('tbl_usuarios.usua_id = '.$txtValorador.'');
                    		}
 			if ($txtArbol_id != null)  { 
			   $querys      -> andwhere('tbl_arbols.id = '.$txtArbol_id.'');
                                } 			
 			if ($txtTecnico != null) { 
			       $querys    -> andwhere('tbl_evaluados.id = '.$txtTecnico.'');
                               }
                        if ($txtcanti > 1) {
			       $querys    -> andwhere("tbl_controlvoc_bloque1.fechacreacion between '$txtfechaini' and '$txtfechafin'");
                                }
                 }
                 else{
                        $querys =  new Query;
                        $querys     ->select(['tbl_controlvoc_bloque1.idbloque1', 'tbl_controlvoc_bloque1.fechacreacion','tbl_usuarios.usua_nombre', 'rel_usuarios_roles.rel_role_id', 'tbl_evaluados.name', 'tbl_arbols.dsname_full', 'tbl_arbols.arbol_id', 'tbl_arbols.formulario_id', 'tbl_evaluados.identificacion', 'tbl_controlvoc_bloque1.dimensions', 'tbl_equipos.usua_id', 'tbl_equipos.id'])->distinct()
                                    ->from('tbl_controlvoc_bloque1')
                                    ->join('LEFT JOIN', 'tbl_arbols',
                                            'tbl_controlvoc_bloque1.arbol_id = tbl_arbols.id')
                                    ->join('LEFT JOIN', 'tbl_usuarios',
                                            'tbl_controlvoc_bloque1.valorador_id = tbl_usuarios.usua_id')
                                    ->join('LEFT JOIN', 'rel_usuarios_roles',
                                            'tbl_usuarios.usua_id = rel_usuarios_roles.rel_usua_id')
                                    ->join('LEFT JOIN', 'tbl_evaluados',
                                            'tbl_controlvoc_bloque1.tecnico_id = tbl_evaluados.id')
                                    ->join('LEFT JOIN', 'tbl_equipos_evaluados',
                                            'tbl_evaluados.id = tbl_equipos_evaluados.evaluado_id')
                                    ->join('LEFT JOIN', 'tbl_equipos',
                                            'tbl_equipos.id = tbl_equipos_evaluados.evaluado_id' );
                           $querys  -> where('tbl_usuarios.usua_id = '.$sessiones.'');
                           $querys  -> andwhere('tbl_arbols.activo = '.$varAnulado.'');
                           $querys  -> andwhere('tbl_arbols.arbol_id != '.$varK.'');

                   	if ($txtValorador != null) { 
			   $querys  -> andwhere('tbl_usuarios.usua_id = '.$txtValorador.'');
                    		}
 			if ($txtArbol_id != null)  { 
			   $querys      -> andwhere('tbl_arbols.id = '.$txtArbol_id.'');
                                } 			
 			if ($txtTecnico != null) { 
			       $querys    -> andwhere('tbl_evaluados.id = '.$txtTecnico.'');
                               }
                        if ($txtcanti > 1) {
			       $querys    -> andwhere("tbl_controlvoc_bloque1.fechacreacion between '$txtfechaini' and '$txtfechafin'");
                                }
                    }                  
                    $command = $querys->createCommand();
                    $query = $command->queryAll();

                    foreach ($query as $key => $value) {
                            $txtdataID = $value['idbloque1'];
                            $txtFechacreacion = $value['fechacreacion'];
                            $txtUsuanombre = $value['usua_nombre'];
                            $varRolid = $value['rel_role_id'];
                            $txtProgramapcrc = $value['dsname_full'];
                            $varProgramapcrc = $value['arbol_id'];
                            $varFormularioid = $value['formulario_id'];
                            $txtNombre = $value['name'];
                            $txtDsname = $value['identificacion'];
                            $txtDimensions = $value['dimensions'];
                            $varUsuaid = $value['usua_id'];
                            $varequipoid = $value['id'];

                    
                    $txtIdVoc = $txtdataID;                    
                    $txtProgramapadre = Yii::$app->db->createCommand("select name from tbl_arbols where arbol_id = $varProgramapcrc")->queryScalar();
                    $txtFormularioname = Yii::$app->db->createCommand("select name from tbl_formularios where id = $varFormularioid")->queryScalar();
                    
                    if ($varUsuaid != null) { 
                       $txtRespon = Yii::$app->db->createCommand("select usua_nombre from tbl_usuarios where usua_id = $varUsuaid")->queryScalar();
                      }
                      else
                      {
                        $txtRespon = '';
                      }
                    if ($varRolid != null) {
                       $txtRolname = Yii::$app->db->createCommand("select role_nombre from tbl_roles where role_id = $varRolid")->queryScalar();
                      }
                      else
                      {
                        $txtRolname = '';
                      }
                    if ($varequipoid!= null) {
                       $txtNombreequipo = Yii::$app->db->createCommand("select name from tbl_equipos where id = $varequipoid")->queryScalar();

                      }
                      else
                      {
                        $txtNombreequipo = '';
                      }
                    
                    $varValorador = Yii::$app->db->createCommand("select valorador_id from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();
                     
                    $varArbol = Yii::$app->db->createCommand("select arbol_id from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();
                    $txtArbol = Yii::$app->db->createCommand("select name from tbl_arbols where id = $varArbol and activo = 0")->queryScalar(); 
                    $varTecnico = Yii::$app->db->createCommand("select tecnico_id from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();
                    $txtNombreTecnico = Yii::$app->db->createCommand("select name from tbl_evaluados where id = $varTecnico")->queryScalar(); 
                    $txtDimensiones = Yii::$app->db->createCommand("select dimensions from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();
                    $txtFecha = Yii::$app->db->createCommand("select fechahora from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();
                    $txtAgente = Yii::$app->db->createCommand("select usuagente from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();
                    $txtDuracion = Yii::$app->db->createCommand("select duracion from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();
                    $txtExtension = Yii::$app->db->createCommand("select extencion from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();
                    $txtSpeech = Yii::$app->db->createCommand("select numidextsp from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();

                    $varIndiGlo = Yii::$app->db->createCommand("select indicadorglobal from tbl_controlvoc_bloque2 where idbloque1 = '$txtIdVoc'")->queryScalar();
            $txtIndiGlo = Yii::$app->db->createCommand("select nombrelistap from tbl_controlvoc_listadopadre where idlistapadrevoc = '$varIndiGlo'")->queryScalar();
            if ($varIndiGlo == 0){
            $txtIndiGlo = 'N/A';
            }
            $varVariable = Yii::$app->db->createCommand("select variable from tbl_controlvoc_bloque2 where idbloque1 = '$txtIdVoc'")->queryScalar();
            $txtVariable = Yii::$app->db->createCommand("select nombrelistap from tbl_controlvoc_listadopadre where idlistapadrevoc = '$varVariable'")->queryScalar();
            if ($varVariable == 0){
            $txtVariable = 'N/A';
            }
            $varMotivoContacto = Yii::$app->db->createCommand("select moticocontacto from tbl_controlvoc_bloque2 where idbloque1 = '$txtIdVoc'")->queryScalar();  
            $txtMotivoContacto = Yii::$app->db->createCommand("select nombrelistap from tbl_controlvoc_listadopadre where idlistapadrevoc = '$varMotivoContacto'")->queryScalar();
            if ($varMotivoContacto == 0){
            $txtMotivoContacto = 'N/A';
            }            
            $varMotivoLlamada = Yii::$app->db->createCommand("select motivollamadas from tbl_controlvoc_bloque2 where idbloque1 = '$txtIdVoc'")->queryScalar(); 
            $txtMotivoLlamada = Yii::$app->db->createCommand("select nombrelistah from tbl_controlvoc_listadohijo where idlistahijovoc = '$varMotivoLlamada'")->queryScalar();
            if ($varMotivoLlamada == 0){
            $txtMotivoLlamada = 'N/A';
            }
            $varPuntoDolor = Yii::$app->db->createCommand("select puntodolor from tbl_controlvoc_bloque2 where idbloque1 = '$txtIdVoc'")->queryScalar(); 
            $txtPuntoDolor = Yii::$app->db->createCommand("select nombrelistap from tbl_controlvoc_listadopadre where idlistapadrevoc = '$varPuntoDolor'")->queryScalar();
            if ($varPuntoDolor == 0){
            $txtPuntoDolor = 'N/A';
            }
            $varLlamadaCategorizada = Yii::$app->db->createCommand("select categoria from tbl_controlvoc_bloque2 where idbloque1 = '$txtIdVoc'")->queryScalar(); 
            if ($varLlamadaCategorizada != '1') {
                $txtLlamadaCategorizada = "Si";
            }else{
                $txtLlamadaCategorizada = "No";
            }
            if ($varLlamadaCategorizada == 0){
            $txtLlamadaCategorizada = 'N/A';
            }

            $txtPorcentaje = Yii::$app->db->createCommand("select indicadorvar from tbl_controlvoc_bloque2 where idbloque1 = '$txtIdVoc'")->queryScalar(); 
            $varAgente = Yii::$app->db->createCommand("select agente from tbl_controlvoc_bloque2 where idbloque1 = '$txtIdVoc'")->queryScalar(); 
            $txtAgente = Yii::$app->db->createCommand("select nombrelistap from tbl_controlvoc_listadopadre where idlistapadrevoc = '$varAgente'")->queryScalar();
            if ($varAgente == 0){
            $txtAgente = 'N/A';
            }
            $varMarca = Yii::$app->db->createCommand("select marca from tbl_controlvoc_bloque2 where idbloque1 = '$txtIdVoc'")->queryScalar(); 
            $txtMarca = Yii::$app->db->createCommand("select nombrelistap from tbl_controlvoc_listadopadre where idlistapadrevoc = '$varMarca'")->queryScalar(); 
            if ($varMarca == 0){
            $txtMarca = 'N/A';
            }
            $varCanal = Yii::$app->db->createCommand("select canal from tbl_controlvoc_bloque2 where idbloque1 = '$txtIdVoc'")->queryScalar(); 
            $txtCanal = Yii::$app->db->createCommand("select nombrelistap from tbl_controlvoc_listadopadre where idlistapadrevoc = '$varCanal'")->queryScalar(); 
            if ($varCanal == 0){
            $txtCanal = 'N/A';
            }
            $txtDcualitativos = Yii::$app->db->createCommand("select detalle from tbl_controlvoc_bloque2 where idbloque1 = '$txtIdVoc'")->queryScalar(); 
            
            $varMapaInteresados1 = Yii::$app->db->createCommand("select mapa1 from tbl_controlvoc_bloque2 where idbloque1 = '$txtIdVoc'")->queryScalar(); 
            $txtMapaInteresados1 = Yii::$app->db->createCommand("select nombrelistap from tbl_controlvoc_listadopadre where idlistapadrevoc = '$varMapaInteresados1'")->queryScalar(); 
            if ($varMapaInteresados1 == 0){
            $txtMapaInteresados1 = 'N/A';
            }
            $varMapaInteresados2 = Yii::$app->db->createCommand("select mapa2 from tbl_controlvoc_bloque2 where idbloque1 = '$txtIdVoc'")->queryScalar(); 
            $txtMapaInteresados2 = Yii::$app->db->createCommand("select nombrelistap from tbl_controlvoc_listadopadre where idlistapadrevoc = '$varMapaInteresados2'")->queryScalar();
            if ($varMapaInteresados2 == 0){
            $txtMapaInteresados2 = 'N/A';
            }
            $varatributos = Yii::$app->db->createCommand("select interesados from tbl_controlvoc_bloque2 where idbloque1 = '$txtIdVoc'")->queryScalar(); 
            $txtatributos = Yii::$app->db->createCommand("select nombrelistap from tbl_controlvoc_listadopadre where idlistapadrevoc = '$varatributos'")->queryScalar();  
            if ($varatributos == 0){
            $txtatributos = 'N/A';
            }
            ?>
                <tr>
                    <td class="text-center"><?php echo $txtFechacreacion; ?></td>
                    <td class="text-center"><?php echo $txtFecha; ?></td>                    
                    <td class="text-center"><?php echo $txtDimensions; ?></td> 
                    <td class="text-center"><?php echo $txtProgramapadre; ?></td> 
                    <td class="text-center"><?php echo $txtProgramapcrc; ?></td>
                    <td class="text-center"><?php echo $txtFormularioname; ?></td>                  
                    <td class="text-center"><?php echo $txtDsname; ?></td>
                    <td class="text-center"><?php echo $txtNombre; ?></td>
                    <td class="text-center"><?php echo $txtRespon; ?></td>
                    <td class="text-center"><?php echo $txtUsuanombre; ?></td>
                    <td class="text-center"><?php echo $txtRolname; ?></td>                    
                    <td class="text-center"><?php echo $txtSpeech; ?></td>
                    <td class="text-center"><?php echo $txtAgente; ?></td>
                    <td class="text-center"><?php echo $txtNombreequipo; ?></td>
                    <td class="text-center"><?php echo $txtDuracion; ?></td>
                    <td class="text-center"><?php echo $txtExtension; ?></td>
                    <td class="text-center" style="background-color: #4A7EC0; color: #fff"><?php echo $txttitulo; ?></td>
                    <td class="text-center"><?php echo $txtIndiGlo; ?></td>
                    <td class="text-center"><?php echo $txtVariable; ?></td>
                    <td class="text-center"><?php echo $txtMotivoContacto; ?></td>
                    <td class="text-center"><?php echo $txtMotivoLlamada; ?></td>
                    <td class="text-center"><?php echo $txtPuntoDolor; ?></td>
                    <td class="text-center"><?php echo $txtLlamadaCategorizada; ?></td>
                    <td class="text-center"><?php echo $txtPorcentaje; ?></td>
                    <td class="text-center"><?php echo $txtAgente; ?></td>
                    <td class="text-center"><?php echo $txtMarca; ?></td>
                    <td class="text-center"><?php echo $txtCanal; ?></td>                    
                    <td class="text-center"><?php echo $txtDcualitativos; ?></td>
                    <td class="text-center"><?php echo $txtMapaInteresados1; ?></td>
                    <td class="text-center"><?php echo $txtMapaInteresados2; ?></td>
                    <td class="text-center"><?php echo $txtatributos; ?></td>
                </tr>
            <?php } ?>
    </tbody>
    </table>
</div>
<script type="text/javascript" charset="UTF-8">
var tableToExcel = (function () {
        var uri = 'data:application/vnd.ms-excel;base64,',
            template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><meta charset="utf-8"/><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>',
            base64 = function (s) {
                return window.btoa(unescape(encodeURIComponent(s)))
            }, format = function (s, c) {
                return s.replace(/{(\w+)}/g, function (m, p) {
                    return c[p];
                })
            }
        return function (table, name) {
            if (!table.nodeType) table = document.getElementById(table)
            var ctx = {
                worksheet: name || 'Worksheet',
                table: table.innerHTML
            }
            console.log(uri + base64(format(template, ctx)));
            document.getElementById("dlink").href = uri + base64(format(template, ctx));
            document.getElementById("dlink").download = "DashBoard Formulario Voc";
            document.getElementById("dlink").traget = "_blank";
            document.getElementById("dlink").click();

        }
    })();
    function download(){
        $(document).find('tfoot').remove();
        var name = document.getElementById("name");
        tableToExcel('tblData', 'Archivo Voc', name+'.xls')
        //setTimeout("window.location.reload()",0.0000001);

    }
    var btn = document.getElementById("btn");
    btn.addEventListener("click",download);

</script>

