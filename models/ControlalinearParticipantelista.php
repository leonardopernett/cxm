<?php

namespace app\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "tbl_participantes".
 *
 * @property integer $participan_id
 * @property string $participan_nombre
 * @property string $fechacreacion
 * @property integer $anulado
 */
class ControlalinearParticipantelista extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_participantes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fechacreacion'], 'safe'],
            [['anulado'], 'integer'],
            [['participan_nombre'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'participan_id' => Yii::t('app', ''),
            'participan_nombre' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }
}