<?php

namespace app\models;

use Yii;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tbl_tmpreportes".
 *
 * @property integer $id
 * @property integer $usua_id
 * @property string $col1
 * @property string $col2
 * @property string $col3
 * @property string $col4
 * @property string $col5
 * @property string $col6
 * @property string $col7
 * @property string $col8
 * @property string $col9
 * @property string $col10
 * @property string $col11
 * @property string $col12
 * @property string $col13
 * @property string $col14
 * @property string $col15
 * @property string $col16
 * @property string $col17
 * @property string $col18
 * @property string $col19
 * @property string $col20
 * @property integer $loggedUserId
 * @property double $score
 * @property string $endDate
 * @property string $startDate
 * @property integer $arbol_id
 * @property integer $usua_id_lider
 * @property string $usuario
 * @property integer $formulario_id
 * @property string $formulario
 * @property string $evaluado
 * @property integer $evaluado_id
 * @property integer $equipo_id
 * @property string $dimension
 * @property integer $dimension_id
 * @property string $created
 */
class Tmpreportes extends \yii\db\ActiveRecord {

    public $created;
    public $agrupacion;
    public $lider;
    public $nro_monitoreos;
    public $dimension_id;
    public $dimension;
    public $equipo_id;
    public $pregunta_id;
    public $evaluado_id;
    public $evaluado;
    public $formulario;
    public $formulario_id;
    public $usuario;
    public $usua_id_lider;
    public $arbol_id;
    public $startDate;
    public $endDate;
    public $score;
    public $carino;
    public $na;
    public $no;
    public $texto7;
    public $texto8;
    public $texto9;
    public $texto10;
    public $si;
    public $pec;
    public $spc;
    public $n_valoraciones;
    public $PENC;
    public $Score;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_tmpreportes';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['created'], 'required'],
            [['usua_id'], 'integer'],
            [['col1', 'col2', 'col3', 'col4', 'col5', 'col6', 'col7'
            , 'col8', 'col9', 'col10', 'col11', 'col12', 'col13'
            , 'col14', 'col15', 'col16', 'col17', 'col18'
            , 'col19', 'col20'], 'string', 'max' => 150],
            [['equipo_id', 'dimension_id', 'evaluado_id', 'arbol_id', 'pregunta_id'], 'safe'],
            [['created', 'dimension_id', 'arbol_id', 'pregunta_id'], 'required', 'on' => 'variables']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'created' => Yii::t('app', 'Fecha de Valoración'),
            'dimension_id' => Yii::t('app', 'Dimensión'),
            'equipo_id' => Yii::t('app', 'Equipo'),
            'agrupacion' => Yii::t('app', 'Agrupacion'),
            'nro_monitoreos' => Yii::t('app', 'Nro Monitoreos'),
            'lider' => Yii::t('app', 'Lider'),
            'evaluado_id' => Yii::t('app', 'Evaluado'),
            'formulario_id' => Yii::t('app', 'Formulario'),
            'arbol_id' => Yii::t('app', 'Arbol'),
            'pregunta_id' => Yii::t('app', 'Pregunta'),
            'usua_id' => Yii::t('app', 'Valorador'),
            'id' => Yii::t('app', 'ID'),
            'carino' => Yii::t('app', 'Cariño'),
            'na' => Yii::t('app', 'NA'),
            'no' => Yii::t('app', 'NO'),
            'texto7' => Yii::t('app', 'texto i7'),
            'texto8' => Yii::t('app', 'texto i8'),
            'texto9' => Yii::t('app', 'texto i9'),
            'texto10' => Yii::t('app', 'texto i10'),
            'si' => Yii::t('app', 'SI'),
            'pec' => Yii::t('app', 'Pec'),
            'n_valoraciones' => Yii::t('app', 'Número de Valoraciones'),
            'spc' => Yii::t('app', 'Spc/ Frc'),
            'col1' => Yii::t('app', 'Col1'),
            'col2' => Yii::t('app', 'Col2'),
            'col3' => Yii::t('app', 'Col3'),
            'col4' => Yii::t('app', 'Col4'),
            'col5' => Yii::t('app', 'Col5'),
            'col6' => Yii::t('app', 'Col6'),
            'col7' => Yii::t('app', 'Col7'),
            'col8' => Yii::t('app', 'Col8'),
            'col9' => Yii::t('app', 'Col9'),
            'col10' => Yii::t('app', 'Col10'),
            'col11' => Yii::t('app', 'Col11'),
            'col12' => Yii::t('app', 'Col12'),
            'col13' => Yii::t('app', 'Col13'),
            'col14' => Yii::t('app', 'Col14'),
            'col15' => Yii::t('app', 'Col15'),
            'col16' => Yii::t('app', 'Col16'),
            'col17' => Yii::t('app', 'Col17'),
            'col18' => Yii::t('app', 'Col18'),
            'col19' => Yii::t('app', 'Col19'),
            'col20' => Yii::t('app', 'Col20'),
        ];
    }

    /**
     * Metodo que retorna el listado de dimensiones
     * 
     * @return array
     * @author Daniel Enciso <daniel.enciso@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getDimensionsList() {
        return ArrayHelper::map(Dimensiones::find()->orderBy('name')->asArray()->all(), 'id', 'name');
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getEvaluados() {
        return $this->hasOne(Evaluados::className(), ['name' => 'col4']);
    }

    /**
     * Metodo que permite la busqueda en el reporte de 
     * valorados
     *      
     * 
     * @return \app\models\ActiveDataProvider
     * @author Daniel Enciso <daniel.enciso@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getReportValorados() {

        $loggedUserId = Yii::$app->user->identity->id;
        $command = \Yii::$app->db->createCommand();
        $command->delete('tbl_tmpreportes', 'usua_id=:usua_id', array(':usua_id' => $loggedUserId));
        $command->execute();
        $sql = 'CALL sp_reporte_ind_evaluados1("' . $loggedUserId . '","' . $this->startDate . '","' . $this->endDate . '","' . $this->usua_id . '","' . $this->dimension_id . '","' . $this->equipo_id . '")';
        $command = \Yii::$app->db->createCommand($sql);
        $command->execute();
        $query = Tmpreportes::find()->where(["usua_id" => $loggedUserId])->orderBy("id");
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
     * Metodo que ejecuta el store procedure 
     * para generar el reporte por variables
     * 
     * @param int    $id   Id del formulario
     * @param string $name Nombre del formulario
     * 
     * @return boolean
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function reporteTrasponer($v_usua_id, $fechaInicial, $fechaFinal, $arbol_id, $pregunta_id, $dimensio_id) {
        try {
           
            $command = \Yii::$app->db->createCommand();
            $command->delete('tbl_tmpreportes', 'usua_id=:usua_id', array(':usua_id' => $v_usua_id));
            $startDate = $fechaInicial . " 00:00:00";
            $endDate = $fechaFinal . " 23:59:59";
            $sql = 'CALL sp_reporte_trasponer("' . $v_usua_id . '", "' . $startDate . '","' . $endDate . '","' . $arbol_id . '","' . $pregunta_id . '","' . $dimensio_id . '")';            
            $command = \Yii::$app->db->createCommand($sql);
            $command->execute();
            return true;
        } catch (Exception $exc) {
            \Yii::error($exc->getMessage(), 'exception');
            return false;
        }
    }

    /**
     * Metodo que busca en la tabla de reportes y devuelve los datos del reporte
     * 
     * @param int    $id   Id del formulario
     * @param string $name Nombre del formulario
     * 
     * @return boolean
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getReporteTrasponer($id) {
        try {
            $query = Tmpreportes::find()->where('usua_id = '.$id);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (Exception $exc) {
            \Yii::error($exc->getMessage(), 'exception');
            return null;
        }
    }

    /**
     * Metodo que retorna el listado de arboles
     * 
     * @return array
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getArbolesByRoles() {
        
        $grupo = Yii::$app->user->identity->grupousuarioid;
        
        return ArrayHelper::map(Arboles::find()
                                ->joinWith('permisosGruposArbols')
                                ->where([
                                    //"sncrear_formulario" => 1,
                                    "snhoja" => 1,
                                    "grupousuario_id" => $grupo,
                                    "snver_grafica" => 1])
                                ->andWhere(['not', ['formulario_id' => null]])
                                ->orderBy("dsorden ASC")
                                ->all(), 'id', 'dsname_full');
    }

    public static function getPreguntasByArbol($id) {
        try {
            $sql = 'SELECT  bd.`id` AS id, CONCAT( SUBSTRING(s.`name`,1,15) ,">",
                SUBSTRING(b.`name`,1,15), ">", SUBSTRING(bd.`name`,1,50)) AS name  
            FROM tbl_arbols a INNER JOIN `tbl_seccions` s 
            On s.`formulario_id` = a.`formulario_id` INNER JOIN `tbl_bloques` b 
            ON b.`seccion_id` = s.`id` INNER JOIN `tbl_bloquedetalles` bd 
            ON bd.`bloque_id` = b.`id` WHERE a.id =' . $id;
            return \Yii::$app->db->createCommand($sql)->queryAll();
        } catch (Exception $e) {
            \Yii::error($exc->getMessage(), 'exception');
        }
    }

}
