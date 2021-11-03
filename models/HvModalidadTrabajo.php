<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_hv_modalidad_trabajo".
 *
 * @property integer $hv_idmodalidad
 * @property string $modalidad
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fecha_creacion
 *
 * @property TblUsuarios $usua
 */
class HvModalidadTrabajo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_hv_modalidad_trabajo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['anulado', 'usua_id'], 'integer'],
            [['fecha_creacion'], 'safe'],
            [['modalidad'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'hv_idmodalidad' => Yii::t('app', 'Hv Idmodalidad'),
            'modalidad' => Yii::t('app', 'Modalidad'),
            'anulado' => Yii::t('app', 'Anulado'),
            'usua_id' => Yii::t('app', 'Usua ID'),
            'fecha_creacion' => Yii::t('app', 'Fecha Creacion'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsua()
    {
        return $this->hasOne(TblUsuarios::className(), ['usua_id' => 'usua_id']);
    }
}