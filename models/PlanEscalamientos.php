<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_plan_escalamientos".
 *
 * @property integer $idplanjustificar
 * @property integer $idtcs
 * @property string $justificacion
 * @property string $correo
 * @property string $comentarios
 * @property integer $Estado
 * @property string $fechacreacion
 * @property integer $anulado
 * @property integer $usua_id
 */
class PlanEscalamientos extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_plan_escalamientos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idtcs', 'Estado', 'anulado', 'usua_id', 'asesorid', 'arbol_id', 'cantidadjustificar'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['justificacion', 'correo'], 'string', 'max' => 150],
            [['comentarios','negargestion'], 'string', 'max' => 250]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idplanjustificar' => Yii::t('app', ''),
            'idtcs' => Yii::t('app', ''),
            'justificacion' => Yii::t('app', ''),
            'correo' => Yii::t('app', ''),
            'comentarios' => Yii::t('app', ''),
            'Estado' => Yii::t('app', ''),
            'arbol_id' => Yii::t('app', ''),
            'cantidadjustificar' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'asesorid' => Yii::t('app', ''),
            'negargestion' => Yii::t('app', ''),
        ];
    }
}