<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_arbols_usuarios".
 *
 * @property integer $id
 * @property integer $arbol_id
 * @property integer $usua_id
 *
 * @property TblArbols $arbol
 * @property TblUsuarios $usua
 */
class ArbolsUsuarios extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_arbols_usuarios';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['arbol_id', 'usua_id'], 'required'],
            [['arbol_id', 'usua_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'arbol_id' => Yii::t('app', 'Arbol ID'),
            'usua_id' => Yii::t('app', 'Usua ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArbol() {
        return $this->hasOne(Arbols::className(), ['id' => 'arbol_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsua() {
        return $this->hasOne(Usuarios::className(), ['usua_id' => 'usua_id']);
    }

    /**
     * Metodo para las graficas del Dashboard
     * 
     * @param int $id Id del arbol
     * @return array 
     * 
     * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public static function getAll_ByArbolID($id) {
        return ArbolsUsuarios::find()
                        ->select("usua_id")
                        ->where(["arbol_id" => $id])
                        ->asArray()
                        ->all();
    }

}
