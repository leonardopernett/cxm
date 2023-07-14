<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_postulacion_heroes".
 *
 * @property integer $id_postulacion
 * @property string $tipodepostulacion
 * @property string $nombrepostula
 * @property string $cargopostula
 * @property string $embajadorpostular
 * @property string $ciudad
 * @property string $fechahorapostulacion
 * @property string $extensioniteracion
 * @property string $usuariovivexperiencia
 * @property string $historiabuenagente
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 * @property string $idea
 * @property string $estado
 * @property string $valorador
 * @property string $rol
 * @property string $pcrc
 * @property string $cod_pcrc
 */
class PostulacionHeroes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_postulacion_heroes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tipodepostulacion', 'nombrepostula', 'cargopostula', 'embajadorpostular', 'ciudad', 'extensioniteracion', 'usuariovivexperiencia', 'historiabuenagente', 'idea', 'estado', 'valorador'], 'string'],
            [['fechahorapostulacion', 'fechacreacion'], 'safe'],
            [['anulado', 'usua_id'], 'integer'],
            [['rol'], 'string', 'max' => 50],
            [['pcrc', 'cod_pcrc'], 'string', 'max' => 500]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_postulacion' => Yii::t('app', ''),
            'tipodepostulacion' => Yii::t('app', ''),
            'nombrepostula' => Yii::t('app', ''),
            'cargopostula' => Yii::t('app', ''),
            'embajadorpostular' => Yii::t('app', ''),
            'ciudad' => Yii::t('app', ''),
            'fechahorapostulacion' => Yii::t('app', ''),
            'extensioniteracion' => Yii::t('app', ''),
            'usuariovivexperiencia' => Yii::t('app', ''),
            'historiabuenagente' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'idea' => Yii::t('app', ''),
            'estado' => Yii::t('app', ''),
            'valorador' => Yii::t('app', ''),
            'rol' => Yii::t('app', ''),
            'pcrc' => Yii::t('app', ''),
            'cod_pcrc' => Yii::t('app', ''),
        ];
    }
}