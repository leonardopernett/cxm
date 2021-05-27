<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_parametrizacion_encuesta".
 *
 * @property integer $id
 * @property integer $cliente
 * @property integer $programa
 *
 * @property TblDetalleparametrizacion[] $tblDetalleparametrizacions
 * @property TblArbols $cliente0
 * @property TblArbols $programa0
 * @property TblPreguntas[] $tblPreguntas
 */
class ParametrizacionEncuesta extends \yii\db\ActiveRecord {

    public $clienteName;
    public $pcrcName;
    //Definicion de variables para guardar datos anteriores 
    public $datosPreguntas;
    public $datosCategoriagestion;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_parametrizacion_encuesta';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['cliente', 'programa'], 'required'],
            [['cliente', 'programa'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'cliente' => Yii::t('app', 'Cliente'),
            'programa' => Yii::t('app', 'Programa'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblDetalleparametrizacions() {
        return $this->hasMany(TblDetalleparametrizacion::className(), ['id_parametrizacion' => 'id']);
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
    public function getPrograma0() {
        return $this->hasOne(Arboles::className(), ['id' => 'programa']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblPreguntas() {
        return $this->hasMany(TblPreguntas::className(), ['id_parametrizacion' => 'id']);
    }

    /**
     * Metodo que retorna los padres de arboles que son hojas
     * @return array
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getpadrecliente($programaId) {
        $sql = "SELECT ta.id AS id, ta.name AS value FROM tbl_arbols t JOIN tbl_arbols ta ON t.arbol_id=ta.id WHERE t.id='$programaId'";
        return \Yii::$app->db->createCommand($sql)->queryAll();
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
                $modelLog->datos_ant = "";
                print_r($this->oldAttributes, true);
                foreach ($this->datosPreguntas as $pregunta) {
                    $modelLog->datos_ant .= print_r($pregunta->oldAttributes, true) . "\n";
                }
                foreach ($this->datosCategoriagestion as $categoriagestion) {
                    $modelLog->datos_ant .="tbl_categoriagestion -> " . print_r($categoriagestion->oldAttributes, true) . "\n";
                    $datos = $categoriagestion->getTblDetalleparametrizacions()->all();
                    foreach ($datos as $detalle) {
                        $modelLog->datos_ant .= "tbl_detalleparametrizacion->" . print_r($detalle->oldAttributes, true) . "\n";
                    }
                }
                $modelPreguntas = Preguntas::find()->where(["id_parametrizacion" => $this->id])->all();
                foreach ($modelPreguntas as $preguntaNew) {
                    $modelLog->datos_nuevos .= print_r($preguntaNew->attributes, true) . "\n";
                }
                $modelCategoriagestion = Categoriagestion::find()->where(["id_parametrizacion" => $this->id])->all();
                foreach ($modelCategoriagestion as $categoriagestionNew) {
                    $modelLog->datos_nuevos .="tbl_categoriagestion -> " . print_r($categoriagestionNew->attributes, true) . "\n";
                    $datos = $categoriagestionNew->getTblDetalleparametrizacions()->all();
                    foreach ($datos as $detalleNew) {
                        $modelLog->datos_nuevos .= "tbl_detalleparametrizacion->" . print_r($detalleNew->attributes, true) . "\n";
                    }
                }
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
    /**
     * 23/02/2016 -> Funcion que permite guardar los datos de las tablas relacionadas a la parametrizacion de una encuesta,
     * con el fin de adjuntar esto al log
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     */
    public function afterFind() {
        $this->datosPreguntas = Preguntas::find()->where(["id_parametrizacion" => $this->id])->all();
        $this->datosCategoriagestion = Categoriagestion::find()
                ->where(["id_parametrizacion" => $this->id])
                ->all();
    }

}
