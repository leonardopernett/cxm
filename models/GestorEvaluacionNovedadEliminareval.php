<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_gestor_evaluacion_novedad_eliminareval".
 *
 * @property int $id
 * @property int $id_evaluacion_nombre
 * @property int $id_estado_novedad
 * @property int $id_tipo_evaluacion
 * @property int $id_solicitante
 * @property string $cc_solicitante
 * @property string|null $id_evaluado
 * @property string|null $cc_evaluado
 * @property string|null $comentarios_solicitud
 * @property int|null $aprobado 1: Aprobado para realizar cambio, 0: No aprobado
 * @property string|null $comentarios_no_aprobado
 * @property string|null $fecha_gestionado
 * @property int|null $gestionadopor
 * @property string|null $fechacreacion
 * @property int|null $usua_id
 * @property int|null $anulado
 */
class GestorEvaluacionNovedadEliminareval extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
       return 'tbl_gestor_evaluacion_novedad_eliminareval';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
       return [
      [['id_evaluacion_nombre', 'id_estado_novedad', 'id_tipo_evaluacion', 'cc_solicitante', 'id_solicitante', 'id'], 'required'],
      [['id_evaluacion_nombre', 'id_estado_novedad', 'id_tipo_evaluacion', 'aprobado', 'gestionadopor', 'usua_id', 'anulado', 'id_evaluado', 'id'], 'integer'],
      [['fecha_gestionado', 'fechacreacion'], 'safe'],
      [['cc_solicitante', 'cc_evaluado'], 'string', 'max' => 20],
      [['comentarios_solicitud', 'comentarios_no_aprobado'], 'string', 'max' => 300],
       ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
       return [
      'id' => Yii::t('app', ''),
      'id_evaluacion_nombre' => Yii::t('app', ''),
      'id_estado_novedad' => Yii::t('app', ''),
      'id_tipo_evaluacion' => Yii::t('app', ''),
      'id_solicitante' => Yii::t('app', ''),      
      'cc_solicitante' => Yii::t('app', ''),
      'id_evaluado' => Yii::t('app', ''),      
      'cc_evaluado' => Yii::t('app', ''),
      'comentarios_solicitud' => Yii::t('app', ''),
      'aprobado' => Yii::t('app', ''),
      'comentarios_no_aprobado' => Yii::t('app', ''),
      'fecha_gestionado' => Yii::t('app', ''),
      'gestionadopor' => Yii::t('app', ''),
      'fechacreacion' => Yii::t('app', ''),
      'usua_id' => Yii::t('app', ''),
      'anulado' => Yii::t('app', ''),
       ];
    }
}