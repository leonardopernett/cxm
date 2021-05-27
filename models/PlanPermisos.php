<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_plan_permisos".
 *
 * @property integer $idplanpermisos
 * @property integer $usuaidpermiso
 * @property integer $tipopermiso
 * @property integer $arbol_id
 * @property string $fechacreacion
 * @property integer $anulado
 * @property integer $usua_id
 */
class PlanPermisos extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_plan_permisos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['usuaidpermiso', 'tipopermiso', 'arbol_id', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idplanpermisos' => Yii::t('app', ''),
            'usuaidpermiso' => Yii::t('app', ''),
            'tipopermiso' => Yii::t('app', ''),
            'arbol_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}