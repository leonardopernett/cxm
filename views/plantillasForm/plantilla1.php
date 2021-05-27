<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of plantilla1
 * 
 * La plantilla contiene las variables de configuracion, las cuales serviran para el 
 * diseño del formulario y la encuesta
 *
 * @author sebastian.orozco@ingeneo.com.co
 */

$varGuiainspiracion = '<div id="Guiainspiracion" class="col-md-6 guiainspiracion">';
$varDatogenerales = '<div id="Datogenerales" class="col-md-6 datogenerales">';
$varPrincipal = '<div id="Principal" class="col-md-12 principal">';
$varRow = '<div id="Row" class="row">';
$arrayDivs = [];
for ($index = 0; $index < 10; $index++) {
    $arrayDivs[]='<div id="div'.$index.'" class="col-md-12">';
}
$varFin = '</div>';
$cantDivs = 2;
$banderaGuiaInspiracion = 1;
$banderaDatogenerales = 0;
?>
