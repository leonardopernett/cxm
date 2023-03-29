<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_valoracion_formulariosexcel".
 *
 * @property integer $id_formulariosexcel
 * @property integer $id_clientenuevo
 * @property string $servicio_excel
 * @property integer $formulario_cxm
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class Valoracionformulariosexcel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_valoracion_formulariosexcel';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_clientenuevo', 'formulario_cxm', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['servicio_excel'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_formulariosexcel' => Yii::t('app', ''),
            'id_clientenuevo' => Yii::t('app', ''),
            'servicio_excel' => Yii::t('app', ''),
            'formulario_cxm' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}