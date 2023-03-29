<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_valoracion_datogeneral".
 *
 * @property integer $id_datogeneral
 * @property integer $id_clientenuevo
 * @property string $arbol_id
 * @property string $cc_asesor
 * @property string $cc_valorador
 * @property string $comentario
 * @property string $score
 * @property string $dimension
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class Valoraciondatogeneral extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_valoracion_datogeneral';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_clientenuevo', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['arbol_id', 'cc_asesor', 'cc_valorador', 'comentario', 'score', 'dimension'], 'string', 'max' => 3]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_datogeneral' => Yii::t('app', ''),
            'id_clientenuevo' => Yii::t('app', ''),
            'arbol_id' => Yii::t('app', ''),
            'cc_asesor' => Yii::t('app', ''),
            'cc_valorador' => Yii::t('app', ''),
            'comentario' => Yii::t('app', ''),
            'score' => Yii::t('app', ''),
            'dimension' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}