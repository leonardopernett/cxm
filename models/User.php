<?php

namespace app\models;

use yii\db\mssql\PDO;

class User extends \yii\base\Object implements \yii\web\IdentityInterface {

    public $id;
    public $username;
    public $password;
    public $authKey;
    public $accessToken;
    public $fullName;
    public $rolId;
    public $cuadroMando;
    public $estadisticasPersonas;
    public $hacerMonitoreo;
    public $reportes;
    public $modificarMonitoreo;
    public $adminSistema;
    public $adminProcesos;
    public $edEqipoValorado;
    public $verInboxAleatorio;
    public $verDesempeno;
    public $verAbogado;
    public $jefeOP;
    public $verTecDesempeno;
    public $verAlertas;
    public $verEvaluacion;
    public $verExterno;
    public $verBA;
    public $verDirectivo;
    public $verAsesormas;
    public $verUsuatlmast;
    public $hacerValoracion;
    public $grupousuarioid;
    /**
     * Fin user by Id
     * 
     * @param type $id
     * 
     * @return \static
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public static function findIdentity($id) {
        $fnIdentity = \Yii::$app->session['fnIdentity'];
        if (empty($fnIdentity)) {
            $sql = "SELECT usua_id as id, usua_usuario as username, "
                    . "usua_usuario as authKey, usua_usuario as accessToken, "
                    . "usua_nombre as fullName, rel_role_id as rolId, "
                    . "per_cuadrodemando as cuadroMando, "
                    . "per_estadisticaspersonas as estadisticasPersonas, "
                    . "per_hacermonitoreo as hacerMonitoreo, "
                    . "per_reportes as reportes, "
                    . "per_modificarmonitoreo as modificarMonitoreo, "
                    . "per_adminsistema as adminSistema, "
                    . "per_desempeno as verDesempeno, "
                    . "per_abogado as verAbogado, "
                    . "per_jefeop as jefeOP, "
                    . "per_tecdesempeno as verTecDesempeno, "
                    . "per_alertas as verAlertas, "
                    . "per_evaluacion as verEvaluacion, "
                    . "per_externo as verExterno, "
                    . "per_ba as verBA, "
                    . "per_directivo as verDirectivo, "
                    . "per_asesormas as verAsesormas, "
                    . "per_usuatlmast as verUsuatlmast, "
                    . "per_adminprocesos as adminProcesos, "
                    . "per_editarequiposvalorados as edEqipoValorado, "
                    . "per_inboxaleatorio as verInboxAleatorio, per_realizar_valoracion AS hacerValoracion, "
                    . "rl.grupos_id grupousuarioid "
                    . "FROM tbl_usuarios u "
                    . "JOIN rel_grupos_usuarios r ON u.usua_id = r.usuario_id "
                    . "JOIN tbl_grupos_usuarios rl ON rl.grupos_id =  r.grupo_id "
                    . "JOIN rel_usuarios_roles rlr ON rlr.rel_usua_id = u.usua_id "
                    . "JOIN tbl_roles AS rol ON rol.role_id = rlr.rel_role_id "
                    . "WHERE usua_id = :id";
            $command = \Yii::$app->db->createCommand($sql);
            $command->bindParam(":id", $id, PDO::PARAM_STR);
            $user = $command->queryOne();
            \Yii::$app->session['fnIdentity'] = $user;
        } else {
            $user = \Yii::$app->session['fnIdentity'];
        }

        if ($user) {
            return new static($user);
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        foreach (static::$users as $user) {
            if ($user['accessToken'] === $token) {
                return new static($user);
            }
        }

        return null;
    }

    /**
     * Finds user by username
     *
     * @param  string      $username
     * 
     * @return static|null
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public static function findByUsername($username) {
        $sql = "SELECT usua_id as id, usua_usuario as username, "
                . "usua_usuario as authKey, usua_usuario as accessToken, "
                . "usua_nombre as fullName, rel_role_id as rolId, "
                . "per_cuadrodemando as cuadroMando, "
                . "per_estadisticaspersonas as estadisticasPersonas, "
                . "per_hacermonitoreo as hacerMonitoreo, "
                . "per_reportes as reportes, "
                . "per_modificarmonitoreo as modificarMonitoreo, "
                . "per_adminsistema as adminSistema, "
                . "per_desempeno as verDesempeno, "
                . "per_abogado as verAbogado, "
                . "per_jefeop as jefeOP, "
                . "per_tecdesempeno as verTecDesempeno, "
                . "per_alertas as verAlertas, "
                . "per_evaluacion as verEvaluacion, "
                . "per_externo as verExterno, "
                . "per_ba as verBA, "
                . "per_directivo as verDirectivo, "
                . "per_asesormas as verAsesormas, "
                . "per_usuatlmast as verUsuatlmast, "
                . "per_adminprocesos as adminProcesos, "
                . "per_editarequiposvalorados as edEqipoValorado, "
                . "per_inboxaleatorio as verInboxAleatorio, per_realizar_valoracion AS hacerValoracion, "
                . "rl.grupos_id grupousuarioid "
                . "FROM tbl_usuarios u "
                . "JOIN rel_grupos_usuarios r ON u.usua_id = r.usuario_id "
                . "JOIN tbl_grupos_usuarios rl ON rl.grupos_id =  r.grupo_id "
                . "JOIN rel_usuarios_roles rlr ON rlr.rel_usua_id = u.usua_id "
                . "JOIN tbl_roles AS rol ON rol.role_id = rlr.rel_role_id "
                . "WHERE usua_usuario = :username AND usua_activo = 'S'";
        $command = \Yii::$app->db->createCommand($sql);
        $command->bindParam(":username", $username, PDO::PARAM_STR);
        $user = $command->queryOne();

        if ($user) {
            return new static($user);
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Retorna el nombre completo del usuario registrado
     * 
     * @return string
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getFullName() {
        return $this->fullName;
    }

    /**
     * Retorna el Id del rol
     * 
     * @return int
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getRolId() {
        return $this->rolId;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey() {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey) {
        return $this->authKey === $authKey;
    }

    /**
     * Valida si tiene permisos de cuadro de mando
     * 
     * @return boolean
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function isCuadroMando() {
        return ($this->cuadroMando) ? true : false;
    }

    /**
     * Valida si el usuario tiene permisos para ver estadisticas
     * de personas
     * Apartir de QA v4.1 esta funcion da permiso a el menu de control
     * @return boolean
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function isControlProcesoCX() {
        return ($this->estadisticasPersonas) ? true : false;
    }

    /**
     * Valida si el usuario logueado tiene permisos de 
     * hacer monitoreo
     * 
     * @return boolean
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function isHacerMonitoreo() {
        return ($this->hacerMonitoreo) ? true : false;
    }

    /**
     * Vallida si el usuario tiene permisos para ver reportes
     * 
     * @return boolean
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function isReportes() {
        return ($this->reportes) ? true : false;
    }

    /**
     * Valida si el usuario tiene permisos para modificar monitoreo
     * 
     * @return boolean
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function isModificarMonitoreo() {
        return ($this->modificarMonitoreo) ? true : false;
    }

    /**
     * Valida si el usuario tiene permisos de administrador
     * 
     * @return boolean
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function isAdminSistema() {
        return ($this->adminSistema) ? true : false;
    }

    /**
     * Valida si el usuario tiene permisos para procesos
     * 
     * @return boolean
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function isAdminProcesos() {
        return ($this->adminProcesos) ? true : false;
    }

    /**
     * Valida si el usuario tiene permisos para editar equipo valorado
     * 
     * @return boolean
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function isEdEqipoValorado() {
        return ($this->edEqipoValorado) ? true : false;
    }

    /**
     * Valida si el usuario tiene permisos para ver el inbox aleatorio
     * 
     * @return boolean
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function isVerInboxAleatorio() {
        return ($this->verInboxAleatorio) ? true : false;
    }

    /**
     * Valida si el usuario tiene permisos para administrar desempeno
     * 
     * @return boolean
     * @author German
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function isVerDesempeno() {
        return ($this->verDesempeno) ? true : false;
    }

    /**
     * Valida si el usuario tiene permisos de Abogado
     * 
     * @return boolean
     * @author German
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function isVerAbogado() {
        return ($this->verAbogado) ? true : false;
    }

        /**
     * Valida si el usuario tiene permisos de desempeno
     * 
     * @return boolean
     * @author German
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function isVerTecDesempeno() {
        return ($this->verTecDesempeno) ? true : false;
    }

            /**
     * Valida si el usuario tiene permisos para realizar alertas
     * 
     * @return boolean
     * @author German
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function isVeralertas() {
        return ($this->verAlertas) ? true : false;
    }

           /**
     * Valida si el usuario tiene permisos para realizar evaluaciones
     * 
     * @return boolean
     * @author Diego
     * @copyright Konecta
     * @version Release: $Id$
     */
    public function isVerevaluacion() {
        return ($this->verEvaluacion) ? true : false;
    }

    /**
     * Valida si el usuario tiene permisos para los tecnicos externo
     * 
     * @return boolean
     * @author Andersson
     * @copyright Konecta
     * @version Release: $Id$
     */
    public function isVerexterno() {
        return ($this->verExterno) ? true : false;
    }

    /**
     * Valida si el usuario tiene permisos para los tecnicos externo
     * 
     * @return boolean
     * @author Andersson
     * @copyright Konecta
     * @version Release: $Id$
     */
    public function isVerBA() {
        return ($this->verBA) ? true : false;
    }

    /**
     * Valida si el usuario tiene permisos para los tecnicos externo
     * 
     * @return boolean
     * @author Andersson
     * @copyright Konecta
     * @version Release: $Id$
     */
    public function isVerdirectivo() {
        return ($this->verDirectivo) ? true : false;
    }    

    /**
     * Valida si el usuario tiene permisos para los asesores a Escuchar +
     * 
     * @return boolean
     * @author Andersson
     * @copyright Konecta
     * @version Release: $Id$
     */
    public function isVerasesormas() {
        return ($this->verAsesormas) ? true : false;
    }

    /**
     * Valida si el usuario tiene permisos para el personal de Tlmark y AST
     * 
     * @return boolean
     * @author Andersson
     * @copyright Konecta
     * @version Release: $Id$
     */
    public function isVerusuatlmast() {
        return ($this->verUsuatlmast) ? true : false;
    }

    /**
     * Valida si el usuario tiene permisos de jefeOP
     * 
     * @return boolean
     * @author German
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function isJefeOP() {
        return ($this->jefeOP) ? true : false;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password) {

        $error = new \stdClass();

        /* SI LA AUTENTICACION ES CONTRA BASE DE DATOS RETORNO */
        if (\Yii::$app->params["auth_mode"] != "LDAP") {
            $error->hasError = false;
            $error->msgError = "";
            return $error;
        }

        /* BUSCO LA CLAVE EN LDAP */
        if (!function_exists("ldap_connect")) {
            $error->hasError = true;
            $error->msgError = \Yii::t("app", "No se encuentra la extension LDAP");
            return $error;
        }

        /* CONEXION A LDAP - Multienlace*/
        $ldapconn = ldap_connect(\Yii::$app->params["LDAP_SERVER1"]);

        /* BUSQUEDA DE USUARIO - Multienlace*/
        $ldapbind = @ldap_bind($ldapconn, $this->username . \Yii::$app->params["LDAP_accsufix"], utf8_decode($password));
        
        if (!$ldapbind) {

            /* CONEXION LDAP HORAE - AST */
            $ldapconnh = ldap_connect(\Yii::$app->params["LDAP_SERVERH"]);

            /* BUSQUEDA DE USUARIO - AST */
            $ldapbindh = @ldap_bind($ldapconnh, $this->username . \Yii::$app->params["LDAP_accsufixh"], utf8_decode($password));

            if (!$ldapbindh) {
                
                /* CONEXION LDAP ODESSA - TLMARK */
                $ldapconno = ldap_connect(\Yii::$app->params["LDAP_SERVERO"]);

                /* BUSQUEDA DE USUARIO ODESSA - TLMARK*/
                $ldapbindo = @ldap_bind($ldapconno, $this->username . \Yii::$app->params["LDAP_accsufixo"], utf8_decode($password));

                if (!$ldapbindo) {
                    
                    $ErrNUM = ldap_errno($ldapconno);

                    switch ($ErrNUM) {

                        case 81:
                            $error->hasError = true;
                            $error->msgError = \Yii::t("app", "No se puede conectar al servidor LDAP");
                            break;
                        case 49:
                            $error->hasError = true;
                            $error->msgError = \Yii::t("app", "Usuario o contraseña no valida");
                            break;

                        default:
                            $error->hasError = true;
                            $error->msgError = \Yii::t("app", "Error en LDAP") . ldap_error($ldapbindo);
                            break;
                    }
                    return $error;

                }

            }
            
        }

        $error->hasError = false;
        $error->msgError = "";
        return $error;
    }

}
