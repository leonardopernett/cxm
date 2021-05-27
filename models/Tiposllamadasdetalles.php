<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_tiposllamadasdetalles".
 *
 * @property integer $id
 * @property string $name
 * @property integer $tiposllamada_id
 *
 * @property TblEjecuciontiposllamada[] $tblEjecuciontiposllamadas
 * @property TblTiposllamadas $tiposllamada
 * @property TblTmptiposllamada[] $tblTmptiposllamadas
 */
class Tiposllamadasdetalles extends \yii\db\ActiveRecord {

    public $tipollamadaName;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_tiposllamadasdetalles';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['tiposllamada_id'], 'required'],
            [['tiposllamada_id'], 'integer'],
            [['name'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'tiposllamada_id' => Yii::t('app', 'Tiposllamada ID'),
            'tipollamadaName' => Yii::t('app', 'Tiposllamada ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEjecuciontiposllamadas() {
        return $this->hasMany(Ejecuciontiposllamada::className(), ['tiposllamadasdetalle_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTiposllamada() {
        return $this->hasOne(Tiposllamadas::className(), ['id' => 'tiposllamada_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTmptiposllamadas() {
        return $this->hasMany(Tmptiposllamada::className(), ['tiposllamadasdetalle_id' => 'id']);
    }

    /**
     * Retorna el detalle de tablero problemas
     * 
     * @param int $tipollamada_id
     * @return Array
     * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public static function getAllLlamdasDetByLlamaID($tipollamada_id) {
        return Tiposllamadasdetalles::find()
                        ->select("id, name")
                        ->where("tiposllamada_id = " . $tipollamada_id)
                        ->orderBy('name ASC')
                        ->asArray()
                        ->all();
    }

}
