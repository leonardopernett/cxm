<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_tiposllamadas".
 *
 * @property integer $id
 * @property string $name
 *
 * @property TblArbols[] $tblArbols
 * @property TblTiposllamadasdetalles[] $tblTiposllamadasdetalles
 */
class Tiposllamadas extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_tiposllamadas';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 45],
            //[['name'], 'match', 'not' => true, 'pattern' => '/[^a-zA-Z\s()_-]/']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblArbols() {
        return $this->hasMany(TblArbols::className(), ['tiposllamada_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblTiposllamadasdetalles() {
        return $this->hasMany(TblTiposllamadasdetalles::className(), ['tiposllamada_id' => 'id']);
    }

    /**
     * 23/02/2016 -> Funcion que permite llevar un log o registro de los datos modificados
     * @param type $insert
     * @return boolean
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     */
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            if ($insert == false) {
                $modelLog = new Logeventsadmin();
                $modelLog->datos_nuevos = print_r($this->attributes, true);
                $modelLog->datos_ant = print_r($this->oldAttributes, true);
                $modelLog->fecha_modificacion = date("Y-m-d H:i:s");
                $modelLog->usuario_modificacion = Yii::$app->user->identity->username;
                $modelLog->id_usuario_modificacion = Yii::$app->user->identity->id;
                $modelLog->tabla_modificada = $this->tableName();
                $modelLog->save();
            }
            return true;
        } else {
            return false;
        }
    }
}
