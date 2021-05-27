<?php

$arr = array("1R");
$rn = $arr[array_rand($arr)];

$arrNom = array("Felipe", "Alx", "Jenny", "Mario", "David");
$nom = $arrNom[array_rand($arrNom)];

$curl_post_data = array(
    'ano' => '2015',
    'mes' => '07',
    'dia' => '26',
    'hora' => '165034',
    'ani' => 'ANI',
    'identificacion' => ''.rand(1111111,9999999),
    'nombre' => $nom,
    'agente' => 'kelly.cano.a',//
    'ext' => '',
    'rn' => $rn,
    'pregunta1' => 'No aplica',//.rand(1, 5),//
    'pregunta2' => 'No aplica',//.rand(1, 5),//
    'pregunta3' => 'No aplica',//.rand(1, 5),//
    'pregunta4' => 'No aplica',//.rand(1, 5),//
    'pregunta5' => 'No aplica',//.rand(1, 5),//
    'pregunta6' => 'No aplica',//.rand(0, 9),//
    'pregunta7' => 'No aplica',//.rand(1, 2),//
    'pregunta8' => 'No aplica',//.rand(0, 9),//
    'pregunta9' => 'No aplica', //.rand(0, 9),//
    'connid' => '123abc',//
    'industria' => '1',
    'institucion' => '2',
);

$service_url = "http://localhost/qa_management_YII2/web/index.php/basesatisfaccion/baseinicial";
$client=new SoapClient($service_url);
var_dump($client->insertBasesatisfaccion($curl_post_data));

exit;


//next example will insert new conversation
$service_url = 'http://localhost/qa-yii/web/index.php/basesatisfaccionws/create';
$curl = curl_init($service_url);


$arrSol = array("SI", "NO");
$solucion = $arrSol[array_rand($arrSol)];

$curl_post_data = array(
    'identificacion' => rand(1111111,9999999),
    'nombre' => $nom,
    'ani' => 'ANI',
    'agente1' => 'kelly.cano.a',
    'anio' => date('Y'),
    'mes' => date('m'),
    'dia' => date('d'),
    'hora' => date('H'),
    'rn' => $rn,
    'satisfaccion_general' => rand(1, 5),
    'tiempo_contestar' => rand(1, 5),
    'agilidad_servicio' => rand(1, 5),
    'claridad_informacion' => rand(1, 5),
    'expresion_verbal' => rand(1, 5),
    'actitud_servicio' => rand(1, 5),
    'satisfaccion_organizacion' => rand(1, 5),
    'solucion_llamada' => $solucion,
    'recomendacion_linea' => rand(1, 9),
    'refid' => 'REFID'
);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
$curl_response = curl_exec($curl);
if ($curl_response === false) {
    $info = curl_getinfo($curl);
    curl_close($curl);
    die('error occured during curl exec. Additioanl info: ' . var_export($info));
}
curl_close($curl);
$decoded = json_decode($curl_response);
echo '<pre>' . __FILE__ . ':' . __LINE__ . ' {' . print_r($decoded, true) . '}';
exit;
if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
    die('error occured: ' . $decoded->response->errormessage);
}
echo 'response ok!';
var_export($decoded->response);


