<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\Url;

/**
 * This is the model class for table "tbl_qr_tipologias".
 *
 * @property integer $id
 * @property integer $id_pilares
 * @property string $tipologia
 * @property integer $usua_id
 * @property string $fechacreacion
 * @property integer $anulado
 */
class Tipologiasqyr extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_qr_tipologias';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_pilares', 'usua_id', 'anulado'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['tipologia'], 'string', 'max' => 80]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', ''),
            'id_pilares' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'tipologia' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }
}