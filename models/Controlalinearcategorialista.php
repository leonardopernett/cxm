<?php

namespace app\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "tbl_categorias_alinear".
 *
 * @property integer $id_categ_ali
 * @property integer $sesion_id
 * @property integer $arbol_id
 * @property string $participan_nombre
 * @property string $fechacreacion
 * @property integer $anulado
 */
class Controlalinearcategorialista extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_categorias_alinear';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fechacreacion'], 'safe'],
            [['anulado'], 'integer'],
            [['arbol_id'], 'integer'],
            [['sesion_id'], 'integer'],
            [['categoria_nombre'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_categ_ali' => Yii::t('app', ''),
            'sesion_id' => Yii::t('app', ''),
            'arbol_id' => Yii::t('app', ''),
            'categoria_nombre' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }
}