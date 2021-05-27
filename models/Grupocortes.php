<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * This is the model class for table "tbl_grupo_cortes".
 *
 * @property integer $idgrupocorte
 * @property string $nomgrupocorte
 * @property string $fechacreacion
 * @property integer $anulado
 */
class Grupocortes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_grupo_cortes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fechacreacion'], 'safe'],
            [['anulado'], 'integer'],
            [['nomgrupocorte'], 'string', 'max' => 80]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idgrupocorte' => Yii::t('app', ''),
            'nomgrupocorte' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }
}
