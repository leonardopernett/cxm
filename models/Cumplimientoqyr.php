<?php

namespace app\models;

use Yii;
use yii\db\Query;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "tbl_qr_cumplimiento".
 *
 * @property integer $id_cumplimiento
 * @property integer $indicador
 * @property integer $diaverde1
 * @property integer $diaverde2
 * @property integer $diaamarillo1
 * @property integer $diaamarillo2
 * @property integer $diarojo1
 * @property integer $diarojo2
 * @property string $mensaje1
 * @property string $mensaje2
 * @property string $mensaje3
 * @property string $fechacreacion
 * @property integer $anulado
 * @property integer $usua_id
 */
class Cumplimientoqyr extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_qr_cumplimiento';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_cumplimiento', 'anulado','usua_id','indicador','diaverde1','diaverde2','diaamarillo1','diaamarillo2','diarojo1','diarojo2'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['mensaje1','mensaje2','mensaje3'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_cumplimiento' => Yii::t('app', ''),
            'indicador' => Yii::t('app', ''),
            'diaverde1' => Yii::t('app', ''),
            'diaverde2' => Yii::t('app', ''),
            'diaamarillo1' => Yii::t('app', ''),
            'diaamarillo2' => Yii::t('app', ''),
            'diarojo1' => Yii::t('app', ''),
            'diarojo2' => Yii::t('app', ''),
            'mensaje1' => Yii::t('app', ''),
            'mensaje2' => Yii::t('app', ''),
            'mensaje3' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacrecion' => Yii::t('app', ''),
        ];
    }
}
