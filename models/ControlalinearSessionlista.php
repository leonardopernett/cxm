<?php

namespace app\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "tbl_sesion_alinear".
 *
 * @property integer $sesion_id
 * @property string $sesion_nombre
 * @property string $fechacreacion
 * @property integer $anulado
 */
class ControlalinearSessionlista extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_sesion_alinear';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fechacreacion'], 'safe'],
            [['anulado'], 'integer'],
            [['sesion_nombre'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'sesion_id' => Yii::t('app', ''),
            'sesion_nombre' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }
}