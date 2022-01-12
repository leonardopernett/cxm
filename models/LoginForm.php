<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends Model {

    public $username;
    public $password;
    public $rememberMe = true;
    private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            [['username', 'password'], 'filter', 'filter' => 'trim'],
            [['username', 'password'], function ($attribute) {
            $this->$attribute = \yii\helpers\HtmlPurifier::process($this->$attribute);
        }],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'username' => Yii::t('app', 'Usuario'),
            'password' => Yii::t('app', 'Clave'),
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params) {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            

            /* SI ES MODO DESARROLLO NO VALIDO EL PASSWORD */
            if (\Yii::$app->params["dev_mode"]) {
                
                if (!$user) {
                    $this->addError($attribute, Yii::t("app", "Incorrect username or password"));
                }
                return true;
            }
            
   
            if (!$user) {
                $this->addError($attribute, Yii::t("app", "Incorrect username or password"));
                return;
            }
            
           
            
            $valid = $user->validatePassword($this->password);

            /* SI HAY ERROR LO PINTO */
            if ($valid->hasError) {
                $this->addError($attribute, $valid->msgError);
            }

            /* SI HAY ERRORES O EL USUARIO NO ESTÁ EN LAS TABLAS */
            if (!$user || $valid->hasError) {
                $this->addError($attribute, Yii::t("app", "Incorrect username or password"));
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login() {
        if ($this->validate()) {
            \Yii::$app->db->createCommand()->insert('tbl_usuarioslog', [
                'uslg_usuario' => $this->username,
                'uslg_fechahora' => date('Y-m-d h:i:s'),
                'uslg_ip' => Yii::$app->getRequest()->getUserIP(),
                'uslg_accion' => 'Conexion',
                'uslg_estado' => 'Exitoso'
            ])->execute();
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        } else {
            \Yii::$app->db->createCommand()->insert('tbl_usuarioslog', [
                'uslg_usuario' => $this->username,
                'uslg_fechahora' => date('Y-m-d h:i:s'),
                'uslg_ip' => Yii::$app->getRequest()->getUserIP(),
                'uslg_accion' => 'Conexion',
                'uslg_estado' => 'Fallido'
            ])->execute();
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser() {
        if (!$this->_user) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }

}
