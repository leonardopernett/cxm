<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_gestor_evaluacion_novedad_jefecolaborador".
 *
 * @property int $id
 * @property int $id_evaluacion_nombre
 * @property int $id_estado_novedad
 * @property int $id_tipo_novedad
 * @property int $id_jefe_solicitante
 * @property int|null $id_colaborador_actual
 * @property int|null $cc_colaborador_actual
 * @property string|null $cc_colaborador_nuevo
 * @property string|null $comentarios_solicitud
 * @property int|null $aprobado 1: Aprobado para realizar cambio, 0: No aprobado
 * @property string|null $comentarios_no_aprobado
 * @property string|null $fecha_gestionado
 * @property int|null $gestionadopor
 * @property string|null $fechacreacion
 * @property int|null $usua_id
 * @property int|null $anulado 1: Eliminado logicamente, 0: Activo
 */
class GestorEvaluacionNovedadJefecolaborador extends \yii\db\ActiveRecord
{
    /**
    * {@inheritdoc}
    */
    public static function tableName()
    {
        return 'tbl_gestor_evaluacion_novedad_jefecolaborador';
    }

    /**
    * {@inheritdoc}
    */
    public function rules()
    {
        return [
            [['id_evaluacion_nombre', 'id_estado_novedad', 'id_tipo_novedad', 'id_jefe_solicitante'], 'required'],
            [['id_evaluacion_nombre', 'id_estado_novedad', 'id_tipo_novedad', 'id_jefe_solicitante', 'id_colaborador_actual', 'aprobado', 'gestionadopor', 'usua_id', 'anulado'], 'integer'],
            [['fecha_gestionado', 'fechacreacion'], 'safe'],
            [['cc_colaborador_nuevo', 'cc_colaborador_actual'], 'string', 'max' => 20],
            [['comentarios_solicitud', 'comentarios_no_aprobado'], 'string', 'max' => 300],
        ];
    }

    /**
    * {@inheritdoc}
    */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'id_evaluacion_nombre' => Yii::t('app', ''),
            'id_estado_novedad' => Yii::t('app', ''),
            'id_tipo_novedad' => Yii::t('app', ''),
            'id_jefe_solicitante' => Yii::t('app', ''),
            'id_colaborador_actual' => Yii::t('app', ''),
            'cc_colaborador_actual' => Yii::t('app', ''),            
            'cc_colaborador_nuevo' => Yii::t('app', ''),
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