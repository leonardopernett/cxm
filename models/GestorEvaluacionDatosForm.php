<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_gestor_evaluacion_datosform".
 *
 * @property int $id_gestor_evaluacion_datosform
 * @property int $id_gestor_evaluacion_formulario
 * @property string|null $tiempo_cargo
 * @property string|null $cargo
 * @property string|null $nom_jefe
 * @property string|null $area_operacion
 * @property string|null $ciudad
 * @property string|null $sociedad
 * @property string|null $fechacreacion
 * @property int|null $usua_id
 * @property int|null $anulado
 */
class GestorEvaluacionDatosForm extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_gestor_evaluacion_datosform';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_gestor_evaluacion_formulario'], 'required'],
            [['id_gestor_evaluacion_formulario', 'usua_id', 'anulado'], 'integer'],
            [['fechacreacion', 'tiempo_cargo'], 'safe'],
            [['tiempo_cargo', 'ciudad', 'sociedad'], 'string', 'max' => 20],
            [['cargo', 'area_operacion'], 'string', 'max' => 100],
            [['nom_jefe'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_gestor_evaluacion_datosform' => Yii::t('app', ''),
            'id_gestor_evaluacion_formulario' => Yii::t('app', ''),
            'tiempo_cargo' => Yii::t('app', ''),
            'cargo' => Yii::t('app', ''),
            'nom_jefe' => Yii::t('app', ''),
            'area_operacion' => Yii::t('app', ''),
            'ciudad' => Yii::t('app', ''),
            'sociedad' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }
}