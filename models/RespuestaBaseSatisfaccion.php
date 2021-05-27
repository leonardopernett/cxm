<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_respuesta_base_satisfaccion".
 *
 * @property integer $id
 * @property string $text_pregunta
 * @property string $respuesta
 * @property integer $id_basesatisfaccion
 * @property integer $id_bloquedetalle
 *
 * @property TblBaseSatisfaccion $idBasesatisfaccion
 */
class RespuestaBaseSatisfaccion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_respuesta_base_satisfaccion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['text_pregunta', 'respuesta', 'id_basesatisfaccion'], 'required'],
            [['id_basesatisfaccion','id_bloquedetalle'], 'integer'],
            [['text_pregunta', 'respuesta'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'text_pregunta' => Yii::t('app', 'Text Pregunta'),
            'respuesta' => Yii::t('app', 'Respuesta'),
            'id_basesatisfaccion' => Yii::t('app', 'Id Basesatisfaccion'),
            'id_bloquedetalle' => Yii::t('app', 'Id BloqueDetalle'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdBasesatisfaccion()
    {
        return $this->hasOne(TblBaseSatisfaccion::className(), ['id' => 'id_basesatisfaccion']);
    }
}
