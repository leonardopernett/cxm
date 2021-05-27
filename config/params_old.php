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
    'server' => "172.20.212.14",
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
        112, //yenith.diez.z
        550, //claudia.pena
        415, //h0061680
        551, //edna.arias
        158, //laura.pino.j
        7, //ruben.figueroa.o
		113, //ronal.usuga.m
		438,//	claudia.correa
		58,//	cindy.mira.am
		431,//	jairo.a.calderon
		28,//	mary.orozco.ar
		1046,//	monica.reyes.s
    ],
    /* LIMITE PARA PARTIR EL QUERY DE EXTRACTAR FORMULARIO */
    'limitQueryExtractarFormulario' => 50000,
    /* IDS DE FORMULARIOS DE ENCUESTAS TELEFÓNICAS */
    'IdFormEncuestaTele' => 676,
   /*RUTA PARA CONSULA DE BUZON*/
	'ruta_buzon' => '/srv/www/htdocs/qa_managementv2/web/buzones_qa',
	'email_satu_from' => 'Ronal.Usuga@allus.com.co',
    'email_satu_to' => 'pipe.echeverri.1@gmail.com',
	/* WEBSERVICE PARA NOTIFIACIONES AMIGO */
    'wsAmigo'=>'https://amigo.allus.com.co/AmigoV1/webservicenotification/WebService.php?wsdl',

];
