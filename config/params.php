<?php

return [
    /* MODO DESARROLLO */
    'dev_mode' => false,
    /* MODO DE CONEXIÓN */
    'auth_mode' => 'LDAP', /* LDAP o DATABASE */
    /* PARAMETROS LDAP */
    'LDAP_SERVER1' => LDAP_SERVER1, //"172.20.1.220",
    'LDAP_accsufix' => '@multienlace.com.co',
    /* VALORACIONES POR MES */
    'valoracionesMes' => 5,
    /* URL WS REDBOX MEDELLIN */
    'wsdl_redbox' => WSDL_REDBOX,
    /* URL WS REDBOX BOGOTA */
    'wsdl_redbox_bogota' => WSDL_REDBOX_BOG,
    /* VALIDAR URL DE WEB SERVICES */
    'validate_url_ws' => true,
    /* NUMERO DE DIAS ATRAS PARA CONSULTA EN REDBOX */
    'dias_llamadas' => 5,
    /* CONEXION REDBOX MEDELLIN*/
    'server' => SERVER,
    'user' => USER,
    'pass' => PASSWORD,
    'db' => DATA_BASE,
    /* CONEXION REDBOX BOGOTA */
    'serverBog' => SERVER_BOG,
    'userBog' => USER_BOG,
    'passBog' => PASSWORD_BOG,
    'dbBog' => DATA_BASE_BOG,
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
	'ruta_buzon' => RUTA_BUZON,
	'email_satu_from' => 'CXM@grupokonecta.com.co',
    'email_satu_to' => 'gmejiav@grupokonecta.com',
    'email_reporte_desempeno' => 'paazcarate@grupokonecta.co',
	/* WEBSERVICE PARA NOTIFIACIONES AMIGO */
    'wsAmigo'=>'https://amigo.allus.com.co/AmigoV1/webservicenotification/WebService.php?wsdl',
	'email_envio_proceso'=>'german.mejia@allus.com.co',
	'lista_plantilla'=>[1=>'plantilla1',2=>'plantilla2',3=>'plantilla3',4=>'plantilla4']
];