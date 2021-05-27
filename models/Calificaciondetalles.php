<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_calificaciondetalles".
 *
 * @property integer $id
 * @property string $name
 * @property integer $sndespliega_tipificaciones
 * @property integer $calificacion_id
 * @property integer $nmorden
 * @property double $i1_povalor
 * @property double $i2_povalor
 * @property double $i3_povalor
 * @property double $i4_povalor
 * @property double $i5_povalor
 * @property double $i6_povalor
 * @property double $i7_povalor
 * @property double $i8_povalor
 * @property double $i9_povalor
 * @property double $i10_povalor
 * @property integer $i1_snopcion_na
 * @property integer $i2_snopcion_na
 * @property integer $i3_snopcion_na
 * @property integer $i4_snopcion_na
 * @property integer $i5_snopcion_na
 * @property integer $i6_snopcion_na
 * @property integer $i7_snopcion_na
 * @property integer $i8_snopcion_na
 * @property integer $i9_snopcion_na
 * @property integer $i10_snopcion_na
 *
 * @property TblCalificacions $calificacion
 * @property TblEjecucionbloquedetalles[] $tblEjecucionbloquedetalles
 */
class Calificaciondetalles extends \yii\db\ActiveRecord {

    public $calificacionName;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_calificaciondetalles';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['sndespliega_tipificaciones', 'calificacion_id', 'nmorden', 'i1_snopcion_na',
            'i2_snopcion_na', 'i3_snopcion_na', 'i4_snopcion_na', 'i5_snopcion_na',
            'i6_snopcion_na', 'i7_snopcion_na', 'i8_snopcion_na', 'i9_snopcion_na',
            'i10_snopcion_na'], 'integer'],
            [['name', 'calificacion_id'], 'required'],
            [['i1_povalor', 'i2_povalor', 'i3_povalor', 'i4_povalor', 'i5_povalor',
            'i6_povalor', 'i7_povalor', 'i8_povalor', 'i9_povalor', 'i10_povalor', 'c_pits'],
                'number'],
            [['name'], 'string', 'max' => 100],
                //[['name'], 'match', 'not' => true, 'pattern' => '/[^a-zA-Z\s()_-]/'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        $text = Textos::find()->asArray()->all();
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'sndespliega_tipificaciones' => Yii::t('app', 'Sndespliega Tipificaciones'),
            'calificacion_id' => Yii::t('app', 'Calificacion ID'),
            'calificacionName' => Yii::t('app', 'Calificacion ID'),
            'nmorden' => Yii::t('app', 'Nmorden'),
            'i1_povalor' => Yii::t('app', 'valor') . $text[0]['detexto'],
            'i2_povalor' => Yii::t('app', 'valor') . $text[1]['detexto'],
            'i3_povalor' => Yii::t('app', 'valor') . $text[2]['detexto'],
            'i4_povalor' => Yii::t('app', 'valor') . $text[3]['detexto'],
            'i5_povalor' => Yii::t('app', 'valor') . $text[4]['detexto'],
            'i6_povalor' => Yii::t('app', 'valor') . $text[5]['detexto'],
            'i7_povalor' => Yii::t('app', 'valor') . $text[6]['detexto'],
            'i8_povalor' => Yii::t('app', 'valor') . $text[7]['detexto'],
            'i9_povalor' => Yii::t('app', 'valor') . $text[8]['detexto'],
            'i10_povalor' => Yii::t('app', 'valor') . $text[9]['detexto'],
            'i1_snopcion_na' => $text[0]['detexto'] . Yii::t('app', 'Na'),
            'i2_snopcion_na' => $text[1]['detexto'] . Yii::t('app', 'Na'),
            'i3_snopcion_na' => $text[2]['detexto'] . Yii::t('app', 'Na'),
            'i4_snopcion_na' => $text[3]['detexto'] . Yii::t('app', 'Na'),
            'i5_snopcion_na' => $text[4]['detexto'] . Yii::t('app', 'Na'),
            'i6_snopcion_na' => $text[5]['detexto'] . Yii::t('app', 'Na'),
            'i7_snopcion_na' => $text[6]['detexto'] . Yii::t('app', 'Na'),
            'i8_snopcion_na' => $text[7]['detexto'] . Yii::t('app', 'Na'),
            'i9_snopcion_na' => $text[8]['detexto'] . Yii::t('app', 'Na'),
            'i10_snopcion_na' => $text[9]['detexto'] . Yii::t('app', 'Na'),
            'c_pits' => Yii::t('app', 'pits'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCalificacion() {
        return $this->hasOne(Calificacions::className(), ['id' => 'calificacion_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEjecucionbloquedetalles() {
        return $this->hasMany(Ejecucionbloquedetalles::className(), ['calificaciondetalle_id' => 'id']);
    }

    /**
     * Convierte el booleano en string
     * 
     * @param type $boolean
     * @return string
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getStringBoolean($boolean) {
        if ($boolean == 1) {
            return 'SI';
        } else {
            return 'NO';
        }
    }

    /**
     * Metodo que retorna el detalle de calificaciones de un formulario
     * 
     * @return array
     * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public static function getDetallesFromIds($ids = array()) {
        $cal = \app\models\Calificaciondetalles::find()
                ->where(['IN', "calificacion_id", $ids])
                ->orderBy('nmorden ASC')
                ->asArray()
                ->all();
        $return = array();
        foreach ($cal as $r) {
            $return[$r["calificacion_id"]][$r["id"]] = $r;
        }
        return $return;
    }

    /**
     * Metodo que retorna el detalle de calificaciones de un formulario en array
     * 
     * @return array
     * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public static function getDetallesFromIdsAsArray($ids = array()) {
        return \yii\helpers\ArrayHelper::map(\app\models\Calificaciondetalles::find()
                                ->where(['IN', "calificacion_id", $ids])
                                ->asArray()
                                ->all(), 'id', 'name');
    }

}
