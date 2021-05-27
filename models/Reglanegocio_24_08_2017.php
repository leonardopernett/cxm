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
            [['rn', 'pcrc', 'cliente', 'tipo_regla', 'cod_industria',
            'cod_industria', 'cod_institucion', 'encu_diarias', 'encu_mes', 'rango_encuestas'], 'required'],
            [['pcrc', 'id_formulario'], 'integer'],
            [['cod_industria', 'cod_institucion', 'encu_diarias', 'encu_mes', 'rango_encuestas'], 'integer'],
            [['rn', 'correos_notificacion'], 'string', 'max' => 255],
            [['tipo_regla'], 'string', 'max' => 100],
            [['promotores', 'neutros', 'detractores'], 'string', 'max' => 100],
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
            'encu_mes' => Yii::t('app', 'Total encuestas al mes'),
            'rango_encuestas' => Yii::t('app', 'Rango encuestas'),
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
