<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_tmpreportes_arbol".
 *
 * @property integer $id
 * @property integer $usua_id
 * @property integer $evaluado_id
 * @property integer $seleccion_arbol_id
 * @property integer $arbol_id
 * @property integer $control
 * @property string $dsruta_arbol
 * @property integer $snhoja
 */
class TmpreportesArbol extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_tmpreportes_arbol';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['usua_id', 'evaluado_id', 'seleccion_arbol_id', 'arbol_id'], 'required'],
            [['usua_id', 'evaluado_id', 'seleccion_arbol_id', 'arbol_id', 'control', 'snhoja'], 'integer'],
            [['dsruta_arbol'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'usua_id' => 'Usua ID',
            'evaluado_id' => 'Evaluado ID',
            'seleccion_arbol_id' => 'Seleccion Arbol ID',
            'arbol_id' => 'Arbol ID',
            'control' => 'Control',
            'dsruta_arbol' => 'Dsruta Arbol',
            'snhoja' => 'Snhoja',
        ];
    }

}
