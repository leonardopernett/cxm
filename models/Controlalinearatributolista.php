<?php

namespace app\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "tbl_atributos_alinear".
 *
 * @property integer $id_atrib_alin
 * @property integer $id_categ_ali
 * @property string $atributo_nombre
 * @property string $medicion
 * @property string $acuerdo
 * @property string $fechacreacion
 * @property integer $anulado
 */
class Controlalinearatributolista extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_atributos_alinear';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fechacreacion'], 'safe'],
            [['anulado'], 'integer'],
            [['id_categ_ali'], 'integer'],
            [['medicion'], 'string', 'max' => 2],
            [['acuerdo'], 'string', 'max' => 250],
            [['atributo_nombre'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_atrib_alin' => Yii::t('app', ''),
            'id_categ_ali' => Yii::t('app', ''),
            'atributo_nombre' => Yii::t('app', ''),
            'medicion' => Yii::t('app', ''),
	    'acuerdo' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }
}