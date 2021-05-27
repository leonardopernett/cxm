<?php

namespace app\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "tbl_controlvoc_sessionlista".
 *
 * @property integer $idsessionvoc
 * @property string $nombresession
 * @property string $fechacreacion
 * @property integer $anulado
 */
class ControlvocSessionlista extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_controlvoc_sessionlista';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fechacreacion'], 'safe'],
            [['anulado'], 'integer'],
            [['nombresession'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idsessionvoc' => Yii::t('app', ''),
            'nombresession' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }
}