<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_base_usuariosip".
 *
 * @property integer $idusuariossip
 * @property string $usuariored
 * @property string $indetificacion
 * @property string $comentarios
 * @property string $fechacambios
 * @property integer $cambios
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class BaseUsuariosip extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_base_usuariosip';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fechacambios', 'fechacreacion'], 'safe'],
            [['cambios', 'anulado', 'usua_id', 'existeusuario', 'evaluados_id'], 'integer'],
            [['usuariored', 'identificacion', 'usuariosip'], 'string', 'max' => 50],
            [['comentarios'], 'string', 'max' => 300],
            [['usuariored', 'identificacion', 'usuariosip', 'comentarios'], 'filter', 'filter' => function($value){
                return filter_var($value,FILTER_SANITIZE_STRING) ;
             }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idusuariossip' => Yii::t('app', ''),
            'usuariored' => Yii::t('app', ''),
            'usuariosip' => Yii::t('app', ''),
            'identificacion' => Yii::t('app', ''),
            'evaluados_id' => Yii::t('app', ''),
            'comentarios' => Yii::t('app', ''),
            'fechacambios' => Yii::t('app', ''),
            'cambios' => Yii::t('app', ''),
            'existeusuario' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}