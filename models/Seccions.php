<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tbl_seccions".
 *
 * @property integer $id
 * @property string $name
 * @property integer $formulario_id
 * @property integer $tiposeccion_id
 * @property integer $nmorden
 * @property string $i1_cdtipo_eval
 * @property string $i2_cdtipo_eval
 * @property string $i3_cdtipo_eval
 * @property string $i4_cdtipo_eval
 * @property string $i5_cdtipo_eval
 * @property string $i6_cdtipo_eval
 * @property string $i7_cdtipo_eval
 * @property string $i8_cdtipo_eval
 * @property string $i9_cdtipo_eval
 * @property string $i10_cdtipo_eval
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
 * @property integer $sndesplegar_comentario
 *
 * @property TblBloques[] $tblBloques
 * @property TblEjecucionseccions[] $tblEjecucionseccions
 * @property TblFormularios $formulario
 * @property TblTiposeccions $tiposeccion
 */
class Seccions extends \yii\db\ActiveRecord {

    public $bloques;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_seccions';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['nmorden', 'formulario_id', 'tiposeccion_id', 'name', 'i1_cdtipo_eval',
            'i2_cdtipo_eval', 'i3_cdtipo_eval', 'i4_cdtipo_eval',
            'i5_cdtipo_eval', 'i6_cdtipo_eval', 'i7_cdtipo_eval',
            'i8_cdtipo_eval', 'i9_cdtipo_eval', 'i10_cdtipo_eval',
            'i1_nmfactor', 'i2_nmfactor', 'i3_nmfactor', 'i4_nmfactor',
            'i5_nmfactor', 'i6_nmfactor', 'i7_nmfactor', 'i8_nmfactor',
            'i9_nmfactor', 'i10_nmfactor'], 'required'],
            [['formulario_id', 'tiposeccion_id', 'nmorden', 'sndesplegar_comentario'],
                'integer'],
            [['i1_nmfactor', 'i2_nmfactor', 'i3_nmfactor', 'i4_nmfactor', 'i5_nmfactor',
            'i6_nmfactor', 'i7_nmfactor', 'i8_nmfactor', 'i9_nmfactor', 'i10_nmfactor'],
                'number'],
            [['nmorden', 'i1_nmfactor', 'i2_nmfactor', 'i3_nmfactor', 'i4_nmfactor', 'i5_nmfactor',
            'i6_nmfactor', 'i7_nmfactor', 'i8_nmfactor', 'i9_nmfactor', 'i10_nmfactor'],
                'default', 'value' => 1],
            [['name'], 'string', 'max' => 100],
            [['i1_cdtipo_eval', 'i2_cdtipo_eval', 'i3_cdtipo_eval', 'i4_cdtipo_eval',
            'i5_cdtipo_eval', 'i6_cdtipo_eval', 'i7_cdtipo_eval', 'i8_cdtipo_eval',
            'i9_cdtipo_eval', 'i10_cdtipo_eval'], 'string', 'max' => 3],
            [['i1_cdtipo_eval', 'i2_cdtipo_eval', 'i3_cdtipo_eval', 'i4_cdtipo_eval',
            'i5_cdtipo_eval', 'i6_cdtipo_eval', 'i7_cdtipo_eval', 'i8_cdtipo_eval',
            'i9_cdtipo_eval', 'i10_cdtipo_eval', 'is_pits', 'sdescripcion'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        $text = Textos::find()->asArray()->all();
        return [
            'id' => Yii::t('app', 'ID'),
            'bloques' => Yii::t('app', 'Bloques'),
            'name' => Yii::t('app', 'Name Seccions'),
            'formulario_id' => Yii::t('app', 'Formulario ID'),
            'formularioName' => Yii::t('app', 'Formulario ID'),
            'tiposeccion_id' => Yii::t('app', 'Tiposeccion ID'),
            'nmorden' => Yii::t('app', 'Nmorden'),
            'i1_cdtipo_eval' => Yii::t('app', 'Tipo Eval') . $text[0]['detexto'],
            'i2_cdtipo_eval' => Yii::t('app', 'Tipo Eval') . $text[1]['detexto'],
            'i3_cdtipo_eval' => Yii::t('app', 'Tipo Eval') . $text[2]['detexto'],
            'i4_cdtipo_eval' => Yii::t('app', 'Tipo Eval') . $text[3]['detexto'],
            'i5_cdtipo_eval' => Yii::t('app', 'Tipo Eval') . $text[4]['detexto'],
            'i6_cdtipo_eval' => Yii::t('app', 'Tipo Eval') . $text[5]['detexto'],
            'i7_cdtipo_eval' => Yii::t('app', 'Tipo Eval') . $text[6]['detexto'],
            'i8_cdtipo_eval' => Yii::t('app', 'Tipo Eval') . $text[7]['detexto'],
            'i9_cdtipo_eval' => Yii::t('app', 'Tipo Eval') . $text[8]['detexto'],
            'i10_cdtipo_eval' => Yii::t('app', 'Tipo Eval') . $text[9]['detexto'],
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
            'sndesplegar_comentario' => Yii::t('app', 'Sndesplegar Comentario'),
            'is_pits' => Yii::t('app', 'is_pits'),
            'sdescripcion' => Yii::t('app', 'Dsdescripcion'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBloques() {
        return $this->hasMany(Bloques::className(), ['seccion_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEjecucionseccions() {
        return $this->hasMany(Ejecucionseccions::className(), ['seccion_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFormulario() {
        return $this->hasOne(Formularios::className(), ['id' => 'formulario_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTiposeccion() {
        return $this->hasOne(Tiposeccions::className(), ['id' => 'tiposeccion_id']);
    }

    /**
     * Metodo para obtener el listado de las opciones
     * para los campos del formulario
     * 
     * @return array
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getOptionsList() {
        return array(
            Yii::t('app', 'formulario_val1') => Yii::t('app', 'formulario_option1'),
            Yii::t('app', 'formulario_val2') => Yii::t('app', 'formulario_option2'),
            Yii::t('app', 'formulario_val3') => Yii::t('app', 'formulario_option3'),
            Yii::t('app', 'formulario_val4') => Yii::t('app', 'formulario_option4')
        );
    }

    /**
     * Metodo para obtener el nombre de la opcion
     * 
     * @param string $id Id de la opcion
     * 
     * @return array
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getOption($id) {
        if (!empty($id)) {
            $array = $this->getOptionsList();
            return $array[$id];
        }
        return null;
    }

    /**
     * Metodo que retorna el listado de tipos de seccion
     * 
     * @return array
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getTipoSeccionsList() {
        return ArrayHelper::map(Tiposeccions::find()->orderBy('name')->asArray()->all(), 'id', 'name');
    }

    /**
     * Metodo que retorna el listado de tipos de seccion
     * 
     * @return array
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getFormulariosList() {
        return ArrayHelper::map(Formularios::find()->orderBy('name')->asArray()->all(), 'id', 'name');
    }

    /**
     * Metodo para cambiar de boolean a string
     * 
     * @param boolean/int $boolean Boolean
     * 
     * @return string
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function stringBoolean($boolean) {
        if ($boolean == 1) {
            return 'SI';
        } else {
            return 'NO';
        }
    }

    /**
     * Metodo que retorna las secciones de un formulario
     * 
     * @return array
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public static function getSeccionsByFormulario($form_id) {

        $form = \app\models\Seccions::find()
                ->select('id,name, nmorden, formulario_id,sndesplegar_comentario')
                ->where('formulario_id = ' . $form_id)
                ->orderBy("nmorden ASC")
                ->asArray()
                ->all();
        $array = array();
        $objeto = new \stdClass();
        foreach ($form as $value) {
            $objeto = new \stdClass();
            foreach ($value as $k => $v) {
                $objeto->$k = $v;
            }
            $array[] = $objeto;
        }
        return $array;
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
