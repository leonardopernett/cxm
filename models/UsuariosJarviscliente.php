<?php

namespace app\models;

use Yii;
use yii\db\Query;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "tbl_usuarios_jarvis_cliente".
 *
 * @property integer $idusuarioevalua
 * @property string $nombre_completo
 * @property string $documento
 * @property integer $id_dp_cargos
 * @property integer $id_dp_posicion
 * @property integer $id_dp_funciones
 * @property string $posicion
 * @property string $funcion
 * @property string $usuario_red
 * @property string $email_corporativo
 * @property string $documento_jefe
 * @property string $nombre_jefe
 * @property integer $id_cargo_jefe
 * @property string $cargo_jefe
 * @property string $directorarea
 * @property string $clientearea
 * @property string $fechacrecion
 * @property integer $anulado
 * @property integer $usua_id
 */
class UsuariosJarviscliente extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_usuarios_jarvis_cliente';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idusuarioevalua', 'id_dp_cargos', 'id_dp_posicion', 'id_dp_funciones','id_cargo_jefe','anulado','usua_id'], 'integer'],
            [['fechacrecion'], 'safe'],
            [['posicion', 'funcion', 'usuario_red', 'email_corporativo', 'documento_jefe', 'documento'], 'string', 'max' => 150],
            [['nombre_jefe', 'cargo_jefe', 'directorarea', 'clientearea', 'nombre_completo', ], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idusuarioevalua' => Yii::t('app', ''),
            'id_dp_cargos' => Yii::t('app', ''),
            'id_dp_posicion' => Yii::t('app', ''),
            'id_dp_funciones' => Yii::t('app', ''),
            'id_cargo_jefe' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacrecion' => Yii::t('app', ''),
            'posicion' => Yii::t('app', ''),
            'funcion' => Yii::t('app', ''),
            'usuario_red' => Yii::t('app', ''),
            'email_corporativo' => Yii::t('app', ''),
            'documento_jefe' => Yii::t('app', ''),
            'nombre_jefe' => Yii::t('app', ''),
            'cargo_jefe' => Yii::t('app', ''),
            'directorarea' => Yii::t('app', ''),
            'clientearea' => Yii::t('app', ''),
            'nombre_completo' => Yii::t('app', ''),
            'documento' => Yii::t('app', ''),
        ];
    }
}
