<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_basechat_formulario".
 *
 * @property integer $idbaseformulario
 * @property integer $ticked_id
 * @property integer $basesatisfaccion_id
 * @property string $fechacalificacion
 * @property string $fechazendeks
 * @property integer $idlista
 * @property integer $idbaselista
 * @property string $fsolicitud
 * @property string $fsolucion
 * @property string $fobservacion
 * @property string $fprocedimiento
 * @property string $fechacreacion
 * @property integer $anulado
 * @property integer $usua_id
 */
class BasechatFormulario extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_basechat_formulario';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ticked_id', 'basesatisfaccion_id', 'idlista', 'idbaselista', 'anulado', 'usua_id'], 'integer'],
            [['fechacalificacion', 'fechacreacion'], 'safe'],
            [['fechazendeks'], 'string', 'max' => 100],
            [['fsolicitud', 'fsolucion', 'fobservacion', 'fprocedimiento'], 'string', 'max' => 250]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idbaseformulario' => Yii::t('app', ''),
            'ticked_id' => Yii::t('app', ''),
            'basesatisfaccion_id' => Yii::t('app', ''),
            'fechacalificacion' => Yii::t('app', ''),
            'fechazendeks' => Yii::t('app', ''),
            'idlista' => Yii::t('app', ''),
            'idbaselista' => Yii::t('app', ''),
            'fsolicitud' => Yii::t('app', ''),
            'fsolucion' => Yii::t('app', ''),
            'fobservacion' => Yii::t('app', ''),
            'fprocedimiento' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}