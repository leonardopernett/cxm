<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

function FusionCharts($chart_type = '', $width = "300", $height = "250", $ID) {
    require_once( 'fusion/FusionCharts_Gen.php' );
    $FC = new FusionCharts($chart_type, $width, $height, $ID);
    $FC->setSWFPath("Charts/");
    return $FC;
}

?>
