<?php

namespace app\models;

use Yii;
use PDO;
use yii\helpers\HtmlPurifier;
use yii\helpers\ArrayHelper;
use yii\base\Exception;

/**
 * This is the model class for table "tbl_formularios".
 *
 * @property integer $id
 * @property string $name
 * @property integer $nmorden
 * @property string $i1_cdtipo_eval
 * @property string $i2_cdtipo_eval
 * @property string $i3_cdtipo_eval
 * @property string $i4_cdtipo_eval
 * @property string $i5_cdtipo_eval
 * @property string $i6_cdtipo_eval
 * @property string $i7_cdtipo_eval
 * @property string $i8_cdtipo_eval
 * @property string $i9_cdtipo_eval
 * @property string $i10_cdtipo_eval
 * @property double $i1_nmfactor
 * @property double $i2_nmfactor
 * @property double $i3_nmfactor
 * @property double $i4_nmfactor
 * @property double $i5_nmfactor
 * @property double $i6_nmfactor
 * @property double $i7_nmfactor
 * @property double $i8_nmfactor
 * @property double $i9_nmfactor
 * @property double $i10_nmfactor
 *
 * @property TblArbols[] $arbols
 * @property TblEjecucionformularios[] $tblEjecucionformularios
 * @property TblSeccions[] $tblSeccions
 */
class Formularios extends \yii\db\ActiveRecord {

    public $secciones;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_formularios';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name'],'maxlength' => 100],
            [['nmorden'], 'integer'],
            [['name', 'i1_cdtipo_eval', 'i2_cdtipo_eval', 'i3_cdtipo_eval',
            'i4_cdtipo_eval', 'i5_cdtipo_eval', 'i6_cdtipo_eval',
            'i7_cdtipo_eval', 'i8_cdtipo_eval', 'i9_cdtipo_eval',
            'i10_cdtipo_eval', 'i1_nmfactor', 'i2_nmfactor', 'i3_nmfactor',
            'i4_nmfactor', 'i5_nmfactor', 'i6_nmfactor', 'i7_nmfactor',
            'i8_nmfactor', 'i9_nmfactor', 'i10_nmfactor','id_plantilla_form'], 'required'],
            [['name'], function ($attribute) {
            $this->$attribute = \yii\helpers\HtmlPurifier::process($this->$attribute);
        }],
            [['i1_nmfactor', 'i2_nmfactor', 'i3_nmfactor', 'i4_nmfactor',
            'i5_nmfactor', 'i6_nmfactor', 'i7_nmfactor', 'i8_nmfactor',
            'i9_nmfactor', 'i10_nmfactor'], 'number'],
            [['name','subi_calculo'], 'string', 'max' => 100],
            [['i1_cdtipo_eval', 'i2_cdtipo_eval', 'i3_cdtipo_eval',
            'i4_cdtipo_eval', 'i5_cdtipo_eval', 'i6_cdtipo_eval',
            'i7_cdtipo_eval', 'i8_cdtipo_eval', 'i9_cdtipo_eval',
            'i10_cdtipo_eval'], 'string', 'max' => 3],
            [['i1_nmfactor'], 'default', 'value' => 1],
            [['name', 'i1_cdtipo_eval', 'i2_cdtipo_eval', 'i3_cdtipo_eval',
            'i4_cdtipo_eval', 'i5_cdtipo_eval', 'i6_cdtipo_eval',
            'i7_cdtipo_eval', 'i8_cdtipo_eval', 'i9_cdtipo_eval',
            'i10_cdtipo_eval', 'i1_nmfactor', 'i2_nmfactor', 'i3_nmfactor',
            'i4_nmfactor', 'i5_nmfactor', 'i6_nmfactor', 'i7_nmfactor',
            'i8_nmfactor', 'i9_nmfactor', 'i10_nmfactor', 'id_plantilla_form'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        $text = Textos::find()->asArray()->all();
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name Formulario'),
            'nmorden' => Yii::t('app', 'Nmorden'),
            'i1_cdtipo_eval' => Yii::t('app', 'Tipo Eval') . $text[0]['detexto'],
            'i2_cdtipo_eval' => Yii::t('app', 'Tipo Eval') . $text[1]['detexto'],
            'i3_cdtipo_eval' => Yii::t('app', 'Tipo Eval') . $text[2]['detexto'],
            'i4_cdtipo_eval' => Yii::t('app', 'Tipo Eval') . $text[3]['detexto'],
            'i5_cdtipo_eval' => Yii::t('app', 'Tipo Eval') . $text[4]['detexto'],
            'i6_cdtipo_eval' => Yii::t('app', 'Tipo Eval') . $text[5]['detexto'],
            'i7_cdtipo_eval' => Yii::t('app', 'Tipo Eval') . $text[6]['detexto'],
            'i8_cdtipo_eval' => Yii::t('app', 'Tipo Eval') . $text[7]['detexto'],
            'i9_cdtipo_eval' => Yii::t('app', 'Tipo Eval') . $text[8]['detexto'],
            'i10_cdtipo_eval' => Yii::t('app', 'Tipo Eval') . $text[9]['detexto'],
            'i1_nmfactor' => Yii::t('app', 'Factor') . $text[0]['detexto'],
            'i2_nmfactor' => Yii::t('app', 'Factor') . $text[1]['detexto'],
            'i3_nmfactor' => Yii::t('app', 'Factor') . $text[2]['detexto'],
            'i4_nmfactor' => Yii::t('app', 'Factor') . $text[3]['detexto'],
            'i5_nmfactor' => Yii::t('app', 'Factor') . $text[4]['detexto'],
            'i6_nmfactor' => Yii::t('app', 'Factor') . $text[5]['detexto'],
            'i7_nmfactor' => Yii::t('app', 'Factor') . $text[6]['detexto'],
            'i8_nmfactor' => Yii::t('app', 'Factor') . $text[7]['detexto'],
            'i9_nmfactor' => Yii::t('app', 'Factor') . $text[8]['detexto'],
            'i10_nmfactor' => Yii::t('app', 'Factor') . $text[9]['detexto'],
            'id_plantilla_form' => Yii::t('app', 'plantilla_form'),
            'subi_calculo'=>Yii::t('app', 'subi_calculo')
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArbols() {
        return $this->hasMany(Arboles::className(), ['formulario_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEjecucionformularios() {
        return $this->hasMany(Ejecucionformularios::className(), ['formulario_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblSeccions() {
        return $this->hasMany(Seccions::className(), ['formulario_id' => 'id']);
    }

    /**
     * Metodo para obtener el listado de las opciones
     * para los campos del formulario
     * 
     * @return array
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getOptionsList() {
        return array(
            Yii::t('app', 'formulario_val1') => Yii::t('app', 'formulario_option1'),
            Yii::t('app', 'formulario_val2') => Yii::t('app', 'formulario_option2'),
            Yii::t('app', 'formulario_val3') => Yii::t('app', 'formulario_option3'),
            Yii::t('app', 'formulario_val4') => Yii::t('app', 'formulario_option4')
        );
    }

    /**
     * Metodo para obtener el nombre de la opcion
     * 
     * @param string $id Id de la opcion
     * 
     * @return array
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getOption($id) {
        if (!empty($id)) {
            $array = $this->getOptionsList();
            return $array[$id];
        }
        return null;
    }

    /**
     * Metodo que ejecuta el store procedure 
     * para duplicar formularios
     * 
     * @param int    $id   Id del formulario
     * @param string $name Nombre del formulario
     * 
     * @return boolean
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function duplicateForm($id, $name) {
        try {
            $sql = 'CALL sp_formulario_duplicar("' . $id . '", "' . $name . '")';
            $command = \Yii::$app->db->createCommand($sql);
            $command->execute();
            return true;
        } catch (Exception $exc) {
            \Yii::error($exc->getMessage(), 'exception');
            return false;
        }
    }

    /**
     * Validar antes de borrar
     * 
     * @return boolean
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function beforeDelete() {
        if (parent::beforeDelete()) {
            $arboles = $this->getArbols()->asArray()->all();
            if (count($arboles) > 0) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * Metodo para obtener la url de la llamada o de la pantalla
     * 
     * @param int $evaluadoId Id del evaluado
     * 
     * @return array
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getEnlaces($evaluadoId) {
        if (!empty($evaluadoId) && is_numeric($evaluadoId)) {
            $usuarioRed = $this->_getUsuarioRed($evaluadoId);
            if ($usuarioRed) {
                $result = $this->_getResult($usuarioRed);
                if ($result && count($result) > 0) {
                    return $result;
                }
            }
        } else {
            \Yii::error(__FILE__ . ':' . __LINE__
                    . ': ###### Evaluado Id vacio o no es numerico #####', 'redbox');
        }
        return false;
    }

    /**
     * Obtiene el usuario de red de un evaliadoId
     * 
     * @param int $evaluadoId Id del evaluado
     * 
     * @return boolean
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    private function _getUsuarioRed($evaluadoId) {
        $evaluado = \app\models\Evaluados::findOne($evaluadoId);
        if (empty($evaluado) || empty($evaluado->dsusuario_red)) {
            \Yii::error(__FILE__ . ':' . __LINE__
                    . ': ##### Evaluado (' . $evaluadoId . ') no encontrado o no '
                    . 'tiene usuario de red en tbl_evaluados #####', 'redbox');
            return false;
        }
        return $evaluado->dsusuario_red;
    }

    /**
     * Obtine el idRel del usuario en la base de datos SQL
     * 
     * @param string $usuarioRed Usuario de red
     * 
     * @return boolean
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    private function _getResult($usuarioRed) {
        $result = false;
        // CONSULTA DE DATOS EN BD MEDELLIN --------------------------------
        /* CONSULTO SQL CON LIBRERIAS REALIZADAS EN PHP PURO */
        $server = Yii::$app->params["server"];
        $user = Yii::$app->params["user"];
        $pass = Yii::$app->params["pass"];
        $db = Yii::$app->params["db"];

        $idRel = $this->_consultDB($usuarioRed, $server, $user, $pass, $db);

        if (!$idRel) {

            //CONSULTA EN BD BOGOTA --------------------------------------------
            $server = Yii::$app->params["serverBog"];
            $user = Yii::$app->params["userBog"];
            $pass = Yii::$app->params["passBog"];
            $db = Yii::$app->params["dbBog"];

            $idRel = $this->_consultDB($usuarioRed, $server, $user, $pass, $db);

            if (is_numeric($idRel)) {
                $wsdl = \Yii::$app->params["wsdl_redbox_bogota"];
                $result = $this->getDataWS($idRel, $wsdl);
            }
        } else {
            $wsdl = \Yii::$app->params["wsdl_redbox"];
            $result = $this->getDataWS($idRel, $wsdl);
        }

        return $result;
    }

    /**
     * 
     * 
     * @param type $server
     * @param type $user
     * @param type $pass
     * @param type $db
     * @return boolean
     */
    private function _consultDB($usuarioRed, $server, $user, $pass, $db) {
        if (!empty($usuarioRed) && !empty($server) && !empty($user) && !empty($pass) && !empty($db)) {
            try {

                $table = "Llamada" . date('Ym');
                $numDias = \Yii::$app->params["dias_llamadas"];
                $dia = date('d');
                $endDate = date('Y-m-d');
                if ($dia > 4) {
                    $startDate = date('Y-m-d', strtotime('-' . $numDias . ' day'));
                } elseif ($dia == 2 || $dia == 3 || $dia == 4) {
                    $startDate = date('Y-m') . '-01';
                } elseif ($dia == 1) {
                    $startDate = date('Y-m-d', strtotime('-' . $numDias . ' day'));
                    $table = "Llamada" . date('Ym', strtotime('-1 month'));
                }else{
                    #code
                }
                //CONECTO A AL SERVIDOR
                $connection = mssql_connect($server, $user, $pass);

                //SI NO HUBO CONEXION
                if (!$connection) {
                    \Yii::error(__FILE__ . ':' . __LINE__
                            . ': ##### Not connected : ' . mssql_get_last_message() . ' #####', 'redbox');
                    return false;
                }

                //SELECCIONO LA BASE DE DATOS
                $db_selected = mssql_select_db($db, $connection);
                if (!$db_selected) {
                    \Yii::error(__FILE__ . ':' . __LINE__
                            . ': ##### Can\'t use db : ' . mssql_get_last_message() . ' #####', 'redbox');
                    return false;
                }

                //QUERY QUE ME RETORNA LOS DATOS
                $query = "SELECT TOP 1 IdReLL FROM "
                        . $table . " WHERE GPLoginWindows = '"
                        . $usuarioRed
                        . "' AND IdGrabacionPantalla IS NOT NULL "
                        . " AND CONVERT(date,Date_Create) BETWEEN '" . $startDate
                        . "' AND '" . $endDate . "'"
                        . " ORDER BY NEWID()";

                $result = mssql_query($query);
                $idRelF = mssql_fetch_array($result);
                $idRel = $idRelF[0];

                if (!is_null($idRel)) {
                    return $idRel;
                } else {
                    \Yii::error(__FILE__ . ':' . __LINE__
                            . ': ##### Usuario (' . $usuarioRed . ') no encontrado '
                            . 'en Tabla SQL '
                            . $table . ' #####', 'redbox');
                    return false;
                }
            } catch (Exception $exc) {
                \Yii::error('#####' . __FILE__ . ':' . __LINE__
                        . $exc->getMessage() . '#####', 'redbox');
                return false;
            } catch (\PDOException $exc) {
                \Yii::error('#####' . __FILE__ . ':' . __LINE__
                        . $exc->getMessage() . '#####', 'redbox');
                return false;
            }
        }
    }

    /**
     * Obtiene la informacion de la primera llamada en el servicio web
     * 
     * @param int $idRel Id de llamadas
     * 
     * @return boolean
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getDataWS($idRel, $wsdl) {
        $arrayLlamadas = [];
        if (!empty($idRel) && !empty($wsdl) && Yii::$app->webservices->webServices($wsdl)) {
            $params = ["strIDRELL" => $idRel];
            if (Yii::$app->webservices->executeMethodWs("ASULDeterminarLLamadaAsociadaXID", $params)) {
                $respuesta = Yii::$app->webservices->getResponse()->ASULDeterminarLLamadaAsociadaXIDResult;
                $xml = simplexml_load_string($respuesta);
                if (isset($xml->RESULTADO->LLAMADAS) && count($xml->RESULTADO->LLAMADAS) >
                        0) {
                    $count = 1;
                    foreach ($xml->RESULTADO->LLAMADAS as $llamda) {
                        $llamadaKey = 'LLAMADA_' . $count;
                        $url = (string) $llamda->$llamadaKey->URLPANTALLA;
                        if (!empty($url)) {
                            $arrayLlamadas[] = [
                                'llamada' => $url
                            ];
                        }
                        $count++;
                    }
                    return $arrayLlamadas;
                } else {
                    \Yii::error('#####' . __FILE__ . ':' . __LINE__
                            . 'Informacion no encontrada en servicio web #####', 'redbox');
                }
            }
        }
        return false;
    }

    /**
     * 23/02/2016 -> Funcion que permite llevar un log o registro de los datos modificados
     * @param type $insert
     * @return boolean
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     */
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            if ($insert == false) {
                $modelLog = new Logeventsadmin();
                $modelLog->datos_nuevos = print_r($this->attributes, true);
                $modelLog->datos_ant = print_r($this->oldAttributes, true);
                $modelLog->fecha_modificacion = date("Y-m-d H:i:s");
                $modelLog->usuario_modificacion = Yii::$app->user->identity->username;
                $modelLog->id_usuario_modificacion = Yii::$app->user->identity->id;
                $modelLog->tabla_modificada = $this->tableName();
                $modelLog->save();
            }
            return true;
        } else {
            return false;
        }
    }

}
