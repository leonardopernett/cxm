<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tbl_bloquedetalles".
 *
 * @property integer $id
 * @property integer $bloque_id
 * @property string $name
 * @property integer $calificacion_id
 * @property integer $tipificacion_id
 * @property integer $nmorden
 * @property double $i1_nmfactor
 * @property double $i2_nmfactor
 * @property double $i3_nmfactor
 * @property double $i4_nmfactor
 * @property double $i5_nmfactor
 * @property double $i6_nmfactor
 * @property double $i7_nmfactor
 * @property double $i8_nmfactor
 * @property double $i9_nmfactor
 * @property double $i10_nmfactor
 *
 * @property TblBloques $bloque
 * @property TblCalificacions $calificacion
 * @property TblTipificacions $tipificacion
 * @property TblEjecucionbloquedetalles[] $tblEjecucionbloquedetalles
 */
class Bloquedetalles extends \yii\db\ActiveRecord {

    public $bloqueName;
    public $seccionName;
    public $formularioName;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_bloquedetalles';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['bloque_id', 'name', 'calificacion_id', 'i1_nmfactor',
            'i2_nmfactor', 'i3_nmfactor', 'i4_nmfactor', 'i5_nmfactor',
            'i6_nmfactor', 'i7_nmfactor', 'i8_nmfactor', 'i9_nmfactor',
            'i10_nmfactor'], 'required'],
            [['bloque_id', 'name', 'calificacion_id',
            'i1_nmfactor', 'i2_nmfactor', 'i3_nmfactor', 'i4_nmfactor',
            'i5_nmfactor', 'i6_nmfactor', 'i7_nmfactor', 'i8_nmfactor',
            'i9_nmfactor', 'i10_nmfactor'], function ($attribute) {
            $this->$attribute = \yii\helpers\HtmlPurifier::process($this->$attribute);
        }],
            [['id', 'bloque_id', 'calificacion_id', 'tipificacion_id', 'nmorden'],
                'integer'],
            [['i1_nmfactor', 'i2_nmfactor', 'i3_nmfactor', 'i4_nmfactor',
            'i5_nmfactor', 'i6_nmfactor', 'i7_nmfactor', 'i8_nmfactor',
            'i9_nmfactor', 'i10_nmfactor', 'c_pits', 'id_seccion_pits'],
                'number'],
            [['name'], 'string', 'max' => 150],
            [['descripcion'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        $text = Textos::find()->asArray()->all();
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name BloqueDetalle'),
            'bloque_id' => Yii::t('app', 'Bloque ID'),
            'calificacion_id' => Yii::t('app', 'Calificacion ID'),
            'tipificacion_id' => Yii::t('app', 'Tipificacion ID'),
            'nmorden' => Yii::t('app', 'Nmorden'),
            'bloqueName' => Yii::t('app', 'Bloque Name'),
            'seccionName' => Yii::t('app', 'Seccion Name'),
            'formularioName' => Yii::t('app', 'Formulario Name'),
            'i1_nmfactor' => Yii::t('app', 'Factor') . $text[0]['detexto'],
            'i2_nmfactor' => Yii::t('app', 'Factor') . $text[1]['detexto'],
            'i3_nmfactor' => Yii::t('app', 'Factor') . $text[2]['detexto'],
            'i4_nmfactor' => Yii::t('app', 'Factor') . $text[3]['detexto'],
            'i5_nmfactor' => Yii::t('app', 'Factor') . $text[4]['detexto'],
            'i6_nmfactor' => Yii::t('app', 'Factor') . $text[5]['detexto'],
            'i7_nmfactor' => Yii::t('app', 'Factor') . $text[6]['detexto'],
            'i8_nmfactor' => Yii::t('app', 'Factor') . $text[7]['detexto'],
            'i9_nmfactor' => Yii::t('app', 'Factor') . $text[8]['detexto'],
            'i10_nmfactor' => Yii::t('app', 'Factor') . $text[9]['detexto'],
            'c_pits' => Yii::t('app', 'pits'),
            'id_seccion_pits' => Yii::t('app', 'Seccion Pits'),
            'descripcion' => Yii::t('app', 'Dsdescripcion'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBloque() {
        return $this->hasOne(Bloques::className(), ['id' => 'bloque_id']);
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
    public function getTipificacion() {
        return $this->hasOne(Tipificaciones::className(), ['id' => 'tipificacion_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEjecucionbloquedetalles() {
        return $this->hasMany(Ejecucionbloquedetalles::className(), ['bloquedetalle_id' => 'id']);
    }

    /**
     * Metodo que retorna el listado de bloques
     * 
     * @return array
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getBloqueList() {
        return ArrayHelper::map(Bloques::find()->orderBy('name')->asArray()->all(), 'id', 'name');
    }

    /**
     * Retorna el listado de calificaciones
     * 
     * @return array
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getCalificacionList() {
        return ArrayHelper::map(Calificacions::find()->orderBy('name')->asArray()->all(), 'id', 'name');
    }

    /**
     * Retorna el listado de calificaciones
     * 
     * @return array
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getTipificacionList() {
        return ArrayHelper::map(Tipificaciones::find()->orderBy('name')->asArray()->all(), 'id', 'name');
    }

    /**
     * Metodo que retorna los bloque detalle de un bloque dado
     * 
     * @return array
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public static function getBloqueDetaByBloque($form_id) {

        $form = \app\models\Bloquedetalles::find()
                ->select('tbl_bloquedetalles.id,tbl_bloquedetalles.name, nmorden, bloque_id, calificacion_id, tipificacion_id')
                ->where('bloque_id = ' . $form_id)
                ->orderBy("nmorden ASC")
                ->asArray()
                ->all();
        $arr = array();
        foreach ($form as $value) {
            $objeto = new \stdClass();
            foreach ($value as $k => $v) {
                $objeto->$k = $v;
            }
            $arr[] = $objeto;
        }
        return $arr;
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
