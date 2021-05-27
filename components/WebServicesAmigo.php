<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\components;

use yii\base\Component;
use yii\base\Exception;
use SoapClient;

/**
 * Description of WebServicesAmigo
 *
 * @author ingeneo
 */
class WebServicesAmigo extends Component {

    /**
     * Parametro de tipo WebServices
     * @var WebServices
     */
    private $soapClient;

    /**
     * Parametro con la respuesta del servicio
     * @var string
     */
    private $response;

    /**
     * Parametro con el wsdl
     * @var string
     */
    private $wsdl;

    /**
     * Constructor para instanciar el servicio web
     * 
     * @param string $url      Url del servicio
     * @param string $method    Metodo que se ejecutara en el ws
     * @param string $params parametros a guardarse
     * 
     * @return boolean     
     */
    public function webServicesAmigo($url, $method = null, $params = null) {
        $this->wsdl = $url;

        try {
             
            if (!filter_var($this->wsdl, FILTER_VALIDATE_URL)) {
               
                return false;
            }
            $socketTime = 5;
            $this->soapClient = new Soapclient($this->wsdl, array(
                'cache_wsdl' => WSDL_CACHE_DISK,
                'default_socket_timeout' => $socketTime,
            ));
            // compruebo si la URL es una URL valida

            $result = $this->soapClient->$method($params['titulo'],
                    $params['descripcion'],$params['pcrc'],$params['notificacion'],
                    $params['muro'],$params['usuariored'],$params['cedula'],
                    $params['plataforma'],$params['url']);
            if ($result == 'EXITO') {
                $this->response = $result;
                return true;
            } else {
                \Yii::error('#####' . __FILE__ . ':' . __LINE__ . ':'
                        . __FUNCTION__
                        . ':'
                        . $result . '#####', 'redbox');
                return false;
            }
            return false;
        } catch (\Exception $exc) {
            \Yii::error('#####' . __FILE__ . ':' . __LINE__ . ':'
                    . __FUNCTION__
                    . ':'
                    . $exc->getMessage() . '#####', 'redbox');
            return false;
        }

        return false;
    }

}
