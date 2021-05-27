<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_control_encuestaaci".
 *
 * @property integer $idcontrolencuestaaci
 * @property integer $idlimeencuesta
 * @property integer $cedula
 * @property string $sede
 * @property string $acipregunta1
 * @property string $acipregunta2
 * @property string $acioficina
 * @property string $acitelefonica
 * @property string $acichat
 * @property string $acicorreo
 * @property string $acimovil
 * @property string $pregunta1
 * @property string $pregunta2
 * @property string $pregunta3
 * @property string $pregunta4
 * @property string $pregunta5
 * @property string $pregunta6
 * @property string $pregunta7
 * @property string $pregunta8
 * @property string $pregunta9
 * @property string $pregunta10
 * @property integer $idtitulosp
 * @property integer $centrocostos
 * @property string $director
 * @property string $gerente
 * @property integer $anulado
 * @property string $fechacreacion
 * @property integer $usua_id
 */
class ControlEncuestaaci extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_control_encuestaaci';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idlimeencuesta', 'cedula', 'idtitulosp', 'centrocostos', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['sede'], 'string', 'max' => 100],
            [['acipregunta1', 'acipregunta2', 'acioficina', 'acitelefonica', 'acichat', 'acicorreo', 'acimovil', 'pregunta1', 'pregunta2', 'pregunta3', 'pregunta4', 'pregunta5', 'pregunta6', 'pregunta7', 'pregunta8', 'pregunta9', 'pregunta10', 'director', 'gerente'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idcontrolencuestaaci' => Yii::t('app', ''),
            'idlimeencuesta' => Yii::t('app', ''),
            'cedula' => Yii::t('app', ''),
            'sede' => Yii::t('app', ''),
            'acipregunta1' => Yii::t('app', ''),
            'acipregunta2' => Yii::t('app', ''),
            'acioficina' => Yii::t('app', ''),
            'acitelefonica' => Yii::t('app', ''),
            'acichat' => Yii::t('app', ''),
            'acicorreo' => Yii::t('app', ''),
            'acimovil' => Yii::t('app', ''),
            'pregunta1' => Yii::t('app', ''),
            'pregunta2' => Yii::t('app', ''),
            'pregunta3' => Yii::t('app', ''),
            'pregunta4' => Yii::t('app', ''),
            'pregunta5' => Yii::t('app', ''),
            'pregunta6' => Yii::t('app', ''),
            'pregunta7' => Yii::t('app', ''),
            'pregunta8' => Yii::t('app', ''),
            'pregunta9' => Yii::t('app', ''),
            'pregunta10' => Yii::t('app', ''),
            'idtitulosp' => Yii::t('app', ''),
            'centrocostos' => Yii::t('app', ''),
            'director' => Yii::t('app', ''),
            'gerente' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}