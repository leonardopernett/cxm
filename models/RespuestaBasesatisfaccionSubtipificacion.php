<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_respuesta_basesatisfaccion_subtipificacion".
 *
 * @property integer $id
 * @property string $subtificacion_name
 * @property integer $subtipificacion_id
 * @property integer $tipificacion_id
 * @property integer $id_basesatisfaccion
 *
 * @property TblRespuestaBasesatisfaccionTipificacion $tipificacion
 */
class RespuestaBasesatisfaccionSubtipificacion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_respuesta_basesatisfaccion_subtipificacion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subtipificacion_id', 'tipificacion_id', 'id_basesatisfaccion'], 'integer'],
            [['subtificacion_name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'subtificacion_name' => Yii::t('app', 'Subtificacion Name'),
            'subtipificacion_id' => Yii::t('app', 'Subtipificacion ID'),
            'tipificacion_id' => Yii::t('app', 'Tipificacion ID'),
            'id_basesatisfaccion' => Yii::t('app', 'Id Basesatisfaccion'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipificacion()
    {
        return $this->hasOne(TblRespuestaBasesatisfaccionTipificacion::className(), ['id' => 'tipificacion_id']);
    }
}
