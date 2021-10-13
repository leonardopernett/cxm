<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\Exception;
use SoapClient;
use SoapFault;

class WebServices extends Component {

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
     * @param string $login    Login del servicio (Opcional)
     * @param string $password Password del servicio (Opcional)
     * 
     * @return boolean     
     */
    public function webServices($url, $login = NULL, $password = NULL) {
        $this->wsdl = $url;

        //Valido la url del servicio
        if(\Yii::$app->params["validate_url_ws"]){
            $validateWs = $this->validateUrlWebServices();
        }else{
            $validateWs = true;
        }
        

        if ($validateWs) {
            try {
                $socketTime = 5;
                if (!is_null($login) && !is_null($password)) {
                    $this->soapClient = new Soapclient(
                            $this->wsdl,
                            array(
                        'login' => $login,
                        'password' => $password,
                        'cache_wsdl' => WSDL_CACHE_DISK,
                        'default_socket_timeout' => $socketTime,
                            )
                    );
                } else {
                    $this->soapClient = new Soapclient($this->wsdl,
                            array(
                        'cache_wsdl' => WSDL_CACHE_DISK,
                        'default_socket_timeout' => $socketTime,
                    ));
                }
                return true;
            } catch (SoapFault $exc) {
                \Yii::error('#####' . __FILE__ . ':' . __LINE__ . ':'
                        . __FUNCTION__
                        . ':'
                        . $exc->getMessage() . '#####', 'redbox');
                return false;
            }
        }
        return false;
    }

    /**
     * Metodo para validar si el contenido de un string es xml
     * 
     * @param string $string Cadena xml a validar
     * 
     * @return boolean      
     */
    private function validateStringXml($string) {

        try {
            $content = file_get_contents($string);            
            if ($content && !empty($content)) {                
                $resulXml = simplexml_load_string($content);                
                if ($resulXml) {
                    return true;
                }
            }
        } catch (Exception $exc) {
            \Yii::error('#####' . __FILE__ . ':' . __LINE__ . ':' . __FUNCTION__
                    . ': validateStringXml : '
                    . $exc->getMessage() . '#####', 'redbox');
            return false;
        }
        return false;
    }

    /**
     * Metodo para la validacion de servicios web
     * 
     * @param string $url Url del servicio
     * 
     * @return boolean    
     */
    private function validateUrlWebServices() {

        // Primero, compruebo si la URL es una URL valida
        if (!filter_var($this->wsdl, FILTER_VALIDATE_URL)) {
            return false;
        }
        // Inicializo y configuro una peticion con CURL
        $curlInit = curl_init($this->wsdl);

        // Limito el tiempo de espera en 5 segundos y configuro las opciones
        curl_setopt($curlInit, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curlInit, CURLOPT_HEADER, true);
        curl_setopt($curlInit, CURLOPT_NOBODY, true);
        curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, true);

        // Obtengo una respuesta
        $response = curl_exec($curlInit);
        curl_close($curlInit);

        if ($response) {
            if ($this->validateStringXml($this->wsdl)) {                
                return true;
            }
        }

        \Yii::error('#####' . __FILE__ . ':' . __LINE__ . ':' . __FUNCTION__
                . ': Url incorrecta : '
                . $this->wsdl . '#####', 'redbox');

        return false;
    }

    /**
     * Metodo para ejecutar metodos del servicio
     * 
     * @param string $method   Nombre del metodo
     * @param array  $params   Parametros del metodo
     * 
     * @return boolean     
     */
    public function executeMethodWs($method, $params = null) {
        try {
            if (is_string($method) && !empty($method)) {

                if (is_null($params)) {
                    $result = $this->soapClient->$method();
                } else {                    
                    $result = $this->soapClient->$method($params);                    
                }

                if ($result) {
                    $this->response = $result;
                    return true;
                }

                \Yii::error('#####' . __FILE__ . ':' . __LINE__ . ':' 
                        . __FUNCTION__ . ': Error Result' . $result
                        . '#####', 'redbox');
                return false;
            }
        } catch (SoapFault $exc) {
            \Yii::error('#####' . __FILE__ . ':' . __LINE__ . ':' . __FUNCTION__
                    . ':'
                    . $exc->getMessage() . '#####', 'redbox');
            return false;
        } catch (Exception $exc) {
            \Yii::error('#####' . __FILE__ . ':' . __LINE__ . ':' . __FUNCTION__
                    . ':'
                    . $exc->getMessage() . '#####', 'redbox');
            return false;
        }

        return false;
    }

    /**
     * Metodo que retorna la respuesta del servicio
     * @return string
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * M�todo que me retorna todas las funciones del WSDL
     * @return array Array con todas las funciones
     */
    public function getFunctions() {
        return $this->soapClient->__getFunctions();
    }

}
