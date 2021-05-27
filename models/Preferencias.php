<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_preferencias".
 *
 * @property integer $id
 * @property integer $usua_id
 * @property string $ids_arbols
 * @property integer $dimension_id
 *
 * @property TblUsuarios $usua
 */
class Preferencias extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_preferencias';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['usua_id', 'ids_arbols', 'dimension_id'], 'required', 'on' => 'preferencias'],
            [['usua_id', 'dimension_id'], 'integer'],
            [['ids_arbols'], 'string', 'max' => 1024]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'usua_id' => Yii::t('app', 'Usua ID'),
            'ids_arbols' => Yii::t('app', 'Ids Arbols'),
            'dimension_id' => Yii::t('app', 'Dimension ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsua() {
        return $this->hasOne(Usuarios::className(), ['usua_id' => 'usua_id']);
    }

    public static function getPreferencias() {

        $preferencias = Preferencias::find()
                ->where(["usua_id" => \Yii::$app->user->identity->id])
                ->all();

        $return = new \stdClass();
        $return->ids_arbols = [];
        $return->dimension_id = '';

        if (isset($preferencias[0]->ids_arbols)) {
            $return->ids_arbols = explode(",", $preferencias[0]->ids_arbols);
        }
        if (isset($preferencias[0]->dimension_id)) {
            $return->dimension_id = $preferencias[0]->dimension_id;
        }
        return $return;
    }

    public static function setEstadisticaDef($arbol_ids = array(), $dimension_id = NULL) {

        $preferencias = Preferencias::find()
                ->where(["usua_id" => \Yii::$app->user->identity->id])
                ->all();

        $objData = new Preferencias();

        $objData->ids_arbols = implode(",", $arbol_ids);
        $objData->scenario = 'preferencias';
        if (!empty($dimension_id)) {
            $objData->dimension_id = $dimension_id;
        }

        if (!empty($preferencias)) {
            $objData->updateAll(
                    ["dimension_id" => $dimension_id
                , "ids_arbols" => $objData->ids_arbols]
                    , ["id" => $preferencias[0]->id]);
        } else {
            $objData->usua_id = \Yii::$app->user->identity->id;
            $objData->save();
        }
    }

}
