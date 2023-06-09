<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_gestor_evaluacion_respuestas".
 *
 * @property integer $id_gestorevaluacionrespuestas
 * @property integer $id_evaluacionnombre
 * @property string $nombre_respuesta
 * @property string $descripcion_respuesta
 * @property integer $valornumerico_respuesta
 * @property string $fechacreacion
 * @property integer $usua_id
 * @property integer $anulado
 *
 * @property TblEvaluacionNombre $idEvaluacionnombre
 */
class GestorEvaluacionRespuestas extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_gestor_evaluacion_respuestas';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [            
            [['id_evaluacionnombre', 'valornumerico_respuesta', 'usua_id', 'anulado'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['nombre_respuesta'], 'string', 'max' => 100],
            [['descripcion_respuesta'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_gestorevaluacionrespuestas' => Yii::t('app', ''),
            'id_evaluacionnombre' => Yii::t('app', ''),
            'nombre_respuesta' => Yii::t('app', ''),
            'descripcion_respuesta' => Yii::t('app', ''),
            'valornumerico_respuesta' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdEvaluacionnombre()
    {
        return $this->hasOne(TblEvaluacionNombre::className(), ['idevaluacionnombre' => 'id_evaluacionnombre']);
    }
}