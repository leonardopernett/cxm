<?php

return [
    /* MODO DESARROLLO */
    'dev_mode' => false,
    /* MODO DE CONEXIÓN */
    'auth_mode' => 'LDAP', /* LDAP o DATABASE */
    /* PARAMETROS LDAP */
    'LDAP_SERVER1' => "172.20.1.220",
    'LDAP_accsufix' => '@multienlace.com.co',
    /* VALORACIONES POR MES */
    'valoracionesMes' => 5,
    /* URL WS REDBOX MEDELLIN */
    'wsdl_redbox' => 'http://172.20.212.12/ASULWSRedboxReproducirAudio/ASULWSREDBOXReproducirAudioPantalla.asmx?wsdl',
    /* URL WS REDBOX BOGOTA */
    'wsdl_redbox_bogota' => 'http://172.20.12.31/ASULWSRedboxReproducirAudio/ASULWSREDBOXReproducirAudioPantalla.asmx?wsdl',
    /* VALIDAR URL DE WEB SERVICES */
    'validate_url_ws' => true,
    /* NUMERO DE DIAS ATRAS PARA CONSULTA EN REDBOX */
    'dias_llamadas' => 5,
    /* CONEXION REDBOX MEDELLIN*/
    'server' => "172.20.212.14\REDBOXSIAR",
    'user' => "consultaqa",
    'pass' => "Allus2015",
    'db' => "REDBOX-SIAR",
    /* CONEXION REDBOX BOGOTA */
    'serverBog' => "172.20.12.32",
    'userBog' => "consultaqa",
    'passBog' => "Allus2015",
    'dbBog' => "REDBOX-SIAR",
    /* ID DE USUARIOS QUE PUEDEN ELIMINAR VALORACIONES */
     'idUsersDelete' => [
        70, //Luisa.londono
        438, //claudia.correa
        991, //juan.cartagena
        69, //catalina.monsaleve
        223,//vanessa.montoya.v
        556,//brandon.rodriguez.g
        1251,//jarlem.salcedo.du
        473,//jonathan.lopera.g 
	2953,//Andersson Moreno
	882, //Monica.Acosta.l
        2271, //Juliana.montes
        1650, //Jesica.mendoza
	881, //carolina.zapata
	3468, //geraldin.vargas
	3229, //engie.guerrero
	57, //yancy.ruiz
	637, //andres.vanegas.a

    ],
    /* LIMITE PARA PARTIR EL QUERY DE EXTRACTAR FORMULARIO */
    'limitQueryExtractarFormulario' => 50000,
    /* IDS DE FORMULARIOS DE ENCUESTAS TELEFÓNICAS */
    'IdFormEncuestaTele' => 676,
   /*RUTA PARA CONSULA DE BUZON*/
	'ruta_buzon' => '/srv/www/htdocs/qa_managementv2/web/buzones_qa',
	'email_satu_from' => 'QA@grupokonecta.com.co',
    'email_satu_to' => 'gmejiav@grupokonecta.com',
    'email_reporte_desempeno' => 'paazcarate@grupokonecta.co',
	/* WEBSERVICE PARA NOTIFIACIONES AMIGO */
    'wsAmigo'=>'https://amigo.allus.com.co/AmigoV1/webservicenotification/WebService.php?wsdl',
	'email_envio_proceso'=>'german.mejia@allus.com.co',
	'lista_plantilla'=>[1=>'plantilla1',2=>'plantilla2',3=>'plantilla3',4=>'plantilla4']
];