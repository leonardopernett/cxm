<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_respuesta_basesatisfaccion_tipificacion".
 *
 * @property integer $id
 * @property string $tipificacion_name
 * @property integer $tipificacion_id
 * @property integer $id_respuesta
 * @property integer $id_basesatisfaccion
 *
 * @property TblRespuestaBasesatisfaccionSubtipificacion[] $tblRespuestaBasesatisfaccionSubtipificacions
 * @property TblRespuestaBaseSatisfaccion $idRespuesta
 */
class RespuestaBasesatisfaccionTipificacion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_respuesta_basesatisfaccion_tipificacion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tipificacion_id', 'id_respuesta', 'id_basesatisfaccion'], 'integer'],
            [['tipificacion_name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'tipificacion_name' => Yii::t('app', 'Tipificacion Name'),
            'tipificacion_id' => Yii::t('app', 'Tipificacion ID'),
            'id_respuesta' => Yii::t('app', 'Id Respuesta'),
            'id_basesatisfaccion' => Yii::t('app', 'Id Basesatisfaccion'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblRespuestaBasesatisfaccionSubtipificacions()
    {
        return $this->hasMany(TblRespuestaBasesatisfaccionSubtipificacion::className(), ['tipificacion_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdRespuesta()
    {
        return $this->hasOne(TblRespuestaBaseSatisfaccion::className(), ['id' => 'id_respuesta']);
    }
}
