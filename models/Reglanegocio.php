<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tbl_reglanegocio".
 *
 * @property integer $id
 * @property string $rn
 * @property integer $pcrc
 * @property integer $cliente
 * @property string $tipo_regla
 * @property integer $cod_industria
 * @property integer $cod_institucion
 * @property string $promotores
 * @property string $neutros
 * @property string $detractores
 * @property integer $id_formulario 
 * @property TblArbols $cliente0
 * @property TblArbols $pcrc0
 * @property Formularios $formulario0
 */
class Reglanegocio extends \yii\db\ActiveRecord {

    public $pcrcName;
    public $clienteName;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_reglanegocio';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['rn', 'pcrc', 'cliente', 'tipo_regla', 'cod_industria', 'cod_institucion', 'encu_diarias', 'tramo1', 'tramo2', 'tramo3', 'tramo4', 'tramo5', 'tramo6', 'tramo7', 'tramo8', 'tramo9', 'tramo10', 'tramo11', 'tramo12', 'tramo13', 'tramo14', 'tramo15', 'tramo16', 'tramo17', 'tramo18', 'tramo19', 'tramo20', 'tramo21', 'tramo22', 'tramo23', 'tramo24', 'encu_mes', 'rango_encuestas'], 'required'],


            [['pcrc', 'id_formulario'], 'integer'],
            [['cod_industria', 'cod_institucion', 'encu_diarias', 'tramo1', 'tramo2', 'tramo3', 'tramo4', 'tramo5', 'tramo6', 'tramo7', 'tramo8', 'tramo9', 'tramo10', 'tramo11', 'tramo12', 'tramo13', 'tramo14', 'tramo15', 'tramo16', 'tramo17', 'tramo18', 'tramo19', 'tramo20', 'tramo21', 'tramo22', 'tramo23', 'tramo24', 'encu_mes', 'rango_encuestas'], 'integer'],
            [['rn', 'correos_notificacion'], 'string', 'max' => 255],
            [['tipo_regla'], 'string', 'max' => 100],
            [['promotores', 'neutros', 'detractores'], 'string', 'max' => 100],
            [['promotores', 'neutros', 'detractores','tipo_regla','rn', 'correos_notificacion'], 'filter', 'filter' => function($value){
                return filter_var($value,FILTER_SANITIZE_STRING) ;
             }],
            ];
    }

   

    


    
    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'rn' => Yii::t('app', 'Rn'),
            'pcrc' => Yii::t('app', 'Pcrc'),
            'cliente' => Yii::t('app', 'Cliente'),
            'tipo_regla' => Yii::t('app', 'Tipo Regla'),
            'cod_industria' => Yii::t('app', 'Código industria'),
            'cod_institucion' => Yii::t('app', 'Código institución'),
            'promotores' => Yii::t('app', 'Promotores'),
            'neutros' => Yii::t('app', 'Neutros'),
            'detractores' => Yii::t('app', 'Detractores'),
            'encu_diarias' => Yii::t('app', 'Encuestas diarias'),
            'tramo1' => Yii::t('app', 'Tramo 1 (12 AM - 1 AM)'),
            'tramo2' => Yii::t('app', 'Tramo 2 (1 AM - 2 AM)'),
            'tramo3' => Yii::t('app', 'Tramo 3 (2 AM - 3 AM)'),
            'tramo4' => Yii::t('app', 'Tramo 4 (3 AM - 4 AM)'),
            'tramo5' => Yii::t('app', 'Tramo 5 (4 AM - 5 AM)'),
            'tramo6' => Yii::t('app', 'Tramo 6 (5 AM - 6 AM)'),
            'tramo7' => Yii::t('app', 'Tramo 7 (6 AM - 7 AM)'),
            'tramo8' => Yii::t('app', 'Tramo 8 (7 AM - 8 AM)'),
            'tramo9' => Yii::t('app', 'Tramo 9 (8 AM - 9 AM)'),
            'tramo10' => Yii::t('app', 'Tramo 10 (9 AM - 10 AM)'),
            'tramo11' => Yii::t('app', 'Tramo 11 (10 AM - 11 AM)'),
            'tramo12' => Yii::t('app', 'Tramo 12 (11 AM - 12 PM)'),
            'tramo13' => Yii::t('app', 'Tramo 13 (12 PM - 1 PM)'),
            'tramo14' => Yii::t('app', 'Tramo 14 (1 PM - 2 PM)'),
            'tramo15' => Yii::t('app', 'Tramo 15 (2 PM - 3 PM)'),
            'tramo16' => Yii::t('app', 'Tramo 16 (3 PM - 4 PM)'),
            'tramo17' => Yii::t('app', 'Tramo 17 (4 PM - 5 PM)'),
            'tramo18' => Yii::t('app', 'Tramo 18 (5 PM - 6 PM)'),
            'tramo19' => Yii::t('app', 'Tramo 19 (6 PM - 7 PM)'),
            'tramo20' => Yii::t('app', 'Tramo 20 (7 PM - 8 PM)'),
            'tramo21' => Yii::t('app', 'Tramo 21 (8 PM - 9 PM)'),
            'tramo22' => Yii::t('app', 'Tramo 22 (9 PM - 10 PM)'),
            'tramo23' => Yii::t('app', 'Tramo 23 (10 PM - 11 PM)'),
            'tramo24' => Yii::t('app', 'Tramo 24 (11 PM - 12 AM)'),
            'rango_encuestas' => Yii::t('app', 'Rango Encuestas'),
            'encu_mes' => Yii::t('app', 'Total encuestas al mes'),
            'correos_notificacion' => Yii::t('app', 'correos_notificacion'),
            'id_formulario' => Yii::t('app', 'Formulario'),
            'pcrcName' => Yii::t('app', 'Pcrc'),
            'clienteName' => Yii::t('app', 'Cliente'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCliente0() {
        return $this->hasOne(Arboles::className(), ['id' => 'cliente']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPcrc0() {
        return $this->hasOne(Arboles::className(), ['id' => 'pcrc']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFormulario0() {
        return $this->hasOne(Formularios::className(), ['id' => 'id_formulario']);
    }

    /**
     * Metodo que consulta en la base de datos los arboles que son hojas
     * @return array
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getArbolesHoja() {

        $rol = Yii::$app->user->identity->rolId;

        return ArrayHelper::map(Arboles::find()
                                ->joinWith('permisosGruposArbols')
                                ->where([
                                    "snhoja" => 1,
                                    "role_id" => $rol])
                                ->orderBy("dsorden ASC")
                                ->all(), 'id', 'dsname_full');
    }

    /**
     * Metodo que retorna los padres de arboles que son hojas
     * @return array
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getpadrecliente($pcrc) {
        $sql = "SELECT ta.id AS id, ta.name AS value FROM tbl_arbols t JOIN tbl_arbols ta ON t.arbol_id=ta.id WHERE t.id='$pcrc'";
        return \Yii::$app->db->createCommand($sql)->queryAll();
    }

    /**
     * Metodo que retorna los registros que son del mismo cliente, pcrc y tipo de regla
     * 23-02-2016 Cambio-> Recibe los paramatros codigo industria e institucion para
     * optimizar la busqueda de reglas de negocio
     * @return array
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getRegistrosPares($pcrc, $cliente, $tipo_regla, $cod_industria, $cod_institucion) {
        $sql = "SELECT * FROM tbl_reglanegocio t  WHERE pcrc='$pcrc' AND cliente='$cliente' AND tipo_regla='$tipo_regla'"
                . " AND cod_industria = '$cod_industria' AND cod_institucion = '$cod_institucion'";
        return \Yii::$app->db->createCommand($sql)->queryAll();
    }

    /**
     * Metodo que el tipo regla de una regla de negocio
     * 
     * @return array
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public static function getReglaNegocio($rn) {

        $form = \app\models\Reglanegocio::find()
                ->select('id,rn,pcrc,cliente,tipo_regla')
                ->where('rn = ' . "'" . $rn . "'")
                ->all();

        $objeto = new \stdClass();
        foreach ($form as $value) {
            $objeto = new \stdClass();
            foreach ($value as $k => $v) {
                $objeto->$k = $v;
            }
        }
        return $objeto;
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
