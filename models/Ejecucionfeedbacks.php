<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "tbl_ejecucionfeedbacks".
 *
 * @property integer $id
 * @property integer $tipofeedback_id
 * @property integer $ejecucionformulario_id
 * @property integer $usua_id
 * @property string $created
 * @property integer $usua_id_lider
 * @property integer $evaluado_id
 * @property integer $snavisar
 * @property integer $snaviso_revisado
 * @property string $dsaccion_correctiva
 * @property string $feaccion_correctiva
 * @property integer $nmescalamiento
 * @property string $feescalamiento
 * @property string $dscausa_raiz
 * @property string $dscompromiso
 * @property string $dscomentario
 * @property integer $basessatisfaccion_id
 *
 * @property TblEjecucionformularios $ejecucionformulario
 * @property TblTipofeedbacks $tipofeedback
 */
class Ejecucionfeedbacks extends \yii\db\ActiveRecord {

    public $startDate;
    public $endDate;
    public $categoriaFeedback;
    public $catfeedback;
    public $arbol_id;
    public $dimension_id;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_ejecucionfeedbacks';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['usua_id', 'catfeedback', 'tipofeedback_id', 'usua_id_lider', 'dscomentario'], 'required', 'on' => 'crear'],
            [['usua_id', 'catfeedback', 'tipofeedback_id', 'dscomentario'], 'required', 'on' => 'crearAjax'],
            [['created'], 'required', 'on' => 'reporte'],
            [['tipofeedback_id', 'ejecucionformulario_id', 'usua_id', 'usua_id_lider',
            'evaluado_id', 'snavisar', 'snaviso_revisado', 'nmescalamiento', 'basessatisfaccion_id', 'dimension_id'], 'integer'],
            [['created', 'feaccion_correctiva', 'feescalamiento', 'arbol_id'], 'safe'],
            [['dsaccion_correctiva', 'dscausa_raiz', 'dscompromiso', 'dscomentario'],
                'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'tipofeedback_id' => Yii::t('app', 'Tipofeedback ID'),
            'ejecucionformulario_id' => Yii::t('app', 'Formulario ID'),
            'usua_id' => Yii::t('app', 'Evaluador'),
            'created' => Yii::t('app', 'Fecha de Creacion del Feedback'),
            'usua_id_lider' => Yii::t('app', 'Lider de Equipo'),
            'evaluado_id' => Yii::t('app', 'Evaluado ID'),
            'snavisar' => Yii::t('app', 'Snavisar'),
            'snaviso_revisado' => Yii::t('app', 'Gestionado'),
            'dsaccion_correctiva' => Yii::t('app', 'Dsaccion Correctiva'),
            'feaccion_correctiva' => Yii::t('app', 'Fecha Ejecución del Feedback'),
            'nmescalamiento' => Yii::t('app', 'Nmescalamiento'),
            'feescalamiento' => Yii::t('app', 'Feescalamiento'),
            'dscausa_raiz' => Yii::t('app', 'Dscausa Raiz'),
            'dscompromiso' => Yii::t('app', 'Dscompromiso'),
            'dscomentario' => Yii::t('app', 'Dscomentario'),
            'catfeedback' => Yii::t('app', 'Categoriafeedbacks'),
            'basessatisfaccion_id' => Yii::t('app', 'basessatisfaccion_id'),
            'arbol_id' => Yii::t('app', 'Arbol'),
            'dimension_id' => Yii::t('app', 'Dimension'),
            
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEjecucionformulario() {
        return $this->hasOne(Ejecucionformularios::className(), ['id' => 'ejecucionformulario_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipofeedback() {
        return $this->hasOne(Tipofeedbacks::className(), ['id' => 'tipofeedback_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvaluado() {
        return $this->hasOne(Evaluados::className(), ['id' => 'evaluado_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario() {
        return $this->hasOne(Usuarios::className(), ['usua_id' => 'usua_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuariolider() {
        return $this->hasOne(Usuarios::className(), ['usua_id' => 'usua_id_lider']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBasesatisfaccion() {
        return $this->hasOne(BaseSatisfaccion::className(), ['id' => 'basessatisfaccion_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDimension() {
        return $this->hasOne(Dimensiones::className(), ['id' => 'dimension_id']);
    }

    /**
     * Retorna las opciones para el dropdown de 
     * gestionado     
     * 
     * @return array
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function gestionadoOptionList() {
        return ['' => 'Seleccione', 1 => 'SI', 0 => 'NO', 2 => 'NA'];
    }

    /**
     * Obtiene la opcion
     * 
     * @param int $opcion Opciones de gestionado
     * 
     * @return string
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getGestionado($opcion) {
        if (!is_null($opcion)) {
            $list = $this->gestionadoOptionList();
            return $list[$opcion];
        }
        return '';
    }

    /**
     * Metodo que permite la busqueda en el reporte de 
     * feebacks
     *      
     * 
     * @return \app\models\ActiveDataProvider
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getReportFeedbacks() {
        $query = Ejecucionfeedbacks::find()
                ->select(['id' => 'tbl_ejecucionfeedbacks.id',
                    'created' => 'tbl_ejecucionfeedbacks.created',
                    'snaviso_revisado' => 'tbl_ejecucionfeedbacks.snaviso_revisado',
                    'usua_id_lider' => 'tbl_ejecucionfeedbacks.usua_id_lider',
                    'usua_id' => 'tbl_ejecucionfeedbacks.usua_id',
                    'evaluado_id' => 'tbl_ejecucionfeedbacks.evaluado_id',
                    'ejecucionformulario_id' => 'tbl_ejecucionfeedbacks.ejecucionformulario_id',
                    'feaccion_correctiva' => 'tbl_ejecucionfeedbacks.feaccion_correctiva',
                    'tipofeedback_id' => 'tbl_ejecucionfeedbacks.tipofeedback_id',
                    'dscausa_raiz' => 'tbl_ejecucionfeedbacks.dscausa_raiz',
                    'dsaccion_correctiva' => 'tbl_ejecucionfeedbacks.dsaccion_correctiva',
                    'dscompromiso' => 'tbl_ejecucionfeedbacks.dscompromiso',
                    'dscomentario' => 'tbl_ejecucionfeedbacks.dscomentario',
                    'basessatisfaccion_id' => 'e.basesatisfaccion_id',
                    'dimension_id' => 'e.dimension_id'
                ])
                ->distinct()
                ->join('LEFT JOIN', 'tbl_ejecucionformularios e', 'e.id = tbl_ejecucionfeedbacks.ejecucionformulario_id')
                ->join('LEFT JOIN', 'tbl_evaluados ev', 'ev.id = tbl_ejecucionfeedbacks.evaluado_id')
                ->join('LEFT JOIN', 'tbl_formularios f', 'f.id = e.formulario_id')
                ->join('LEFT JOIN', 'tbl_usuarios u', 'u.usua_id = tbl_ejecucionfeedbacks.usua_id')
                ->join('INNER JOIN', 'tbl_tipofeedbacks ti', 'ti.id = tbl_ejecucionfeedbacks.tipofeedback_id')
                ->join('INNER JOIN', 'tbl_categoriafeedbacks ca', 'ca.id = ti.categoriafeedback_id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->andFilterWhere([
            'tbl_ejecucionfeedbacks.evaluado_id' => $this->evaluado_id,
            'tbl_ejecucionfeedbacks.usua_id' => $this->usua_id,
            'tbl_ejecucionfeedbacks.usua_id_lider' => $this->usua_id_lider,
            'tbl_ejecucionfeedbacks.snaviso_revisado' => $this->snaviso_revisado,
        ]);
        if ($this->arbol_id != '') {

            $arbolesid = $this->arbol_id;
            $validarbol = Yii::$app->get('dbslave')->createCommand("select count(1) from tbl_arbols where id in ($arbolesid) and arbol_id in (0,1,2,98)")->queryScalar();
            if ($validarbol == 1) {
                $varlistarbol = Yii::$app->get('dbslave')->createCommand("select a.id from tbl_arbols a where a.arbol_id in ($arbolesid) group by a.id")->queryAll();
                $vararrayCC = array();
                foreach ($varlistarbol as $key => $value) {
                  array_push($vararrayCC, $value['id']);
                }
                $varCC = implode(", ", $vararrayCC);

                $query->andWhere('e.arbol_id IN (' . $varCC . ')');
            }else{
                $query->andWhere('e.arbol_id IN (' . $this->arbol_id . ')');
            } 

            // $query->andWhere('e.arbol_id IN (' . $this->arbol_id . ')');
        }
        if ($this->dimension_id != '') {
            $query->andWhere('e.dimension_id IN (' . $this->dimension_id . ')');
        }
        $query->andFilterWhere(['between', 'DATE(tbl_ejecucionfeedbacks.created)',
            $this->startDate, $this->endDate]);

        return $dataProvider;

    }

    /**
     * Metodo que deuvuelve las alertas de un usuario
     * feebacks
     *      
     * 
     * @return \app\models\Ejecucionfeedbacks
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public static function getAlertas($usua_id, $limit = 15, $offset = 0) {

        return Ejecucionfeedbacks::find()
                        ->select('tbl_ejecucionfeedbacks.created, ev.name evaluado,
                    f.name formulario, u.usua_nombre usuario, 
                    tbl_ejecucionfeedbacks.evaluado_id, 
                    e.formulario_id, e.arbol_id, 
                    tbl_ejecucionfeedbacks.id, 
                    tbl_ejecucionfeedbacks.dscomentario, 
                    e.id idForm')
                        ->join('LEFT JOIN', 'tbl_ejecucionformularios e', 'e.id = tbl_ejecucionfeedbacks.ejecucionformulario_id')
                        ->join('INNER JOIN', 'tbl_evaluados ev', 'ev.id = tbl_ejecucionfeedbacks.evaluado_id')
                        ->join('LEFT JOIN', 'tbl_formularios f', 'f.id = e.formulario_id')
                        ->join('LEFT JOIN', 'tbl_usuarios u', 'u.usua_id = tbl_ejecucionfeedbacks.usua_id')
                        ->where('snaviso_revisado = 0 AND ( e.usua_id_lider = ' . $usua_id . ' '
                                . 'OR tbl_ejecucionfeedbacks.usua_id_lider = ' . $usua_id . '  '
                                . 'OR EXISTS ( '
                                . 'SELECT x.id FROM tbl_arbols_usuarios x '
                                . 'WHERE  x.usua_id = ' . $usua_id . ' '
                                . 'AND x.arbol_id = e.arbol_id ) )')
                        ->orderBy("tbl_ejecucionfeedbacks.created DESC")
                        ->limit($limit)
                        ->offset($offset)
                        ->asArray()
                        ->all();
    }

    /**
     * Metodo para saber si un feedback esta relacionado con un formulario
     * 
     * @param int $feedback_id
     * @return boolean
     * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public static function hasFormulario($feedback_id) {
        $ejefeedback = Ejecucionfeedbacks::findOne($feedback_id);
        return !is_null($ejefeedback->ejecucionformulario_id);
    }

    /**
     * Metodo para obtemer el tipo de feedback
     * 
     * @param int $id
     * @return array
     * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public static function getSelectTipoFeedback($id) {
        return Ejecucionfeedbacks::find()
                        ->select(static::tableName() . ".id, "
                                . "t.snaccion_correctiva, "
                                . "t.sncausa_raiz, "
                                . "t.sncompromiso")
                        ->distinct()
                        ->join('INNER JOIN', 'tbl_tipofeedbacks t'
                                , 't.id = ' . static::tableName() . '.tipofeedback_id')
                        ->where([static::tableName() . ".id" => $id])
                        ->asArray()
                        ->all();
    }

    /**
     * Metodo que permite la busqueda en el reporte de 
     * feebacks
     *      
     * 
     * @return \app\models\ActiveDataProvider
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getReportfeedbacksamigo() {
        $query = Ejecucionfeedbacks::find()
                ->select(['id' => 'tbl_ejecucionfeedbacks.id',
                    'created' => 'tbl_ejecucionfeedbacks.created',
                    'snaviso_revisado' => 'tbl_ejecucionfeedbacks.snaviso_revisado',
                    'usua_id_lider' => 'tbl_ejecucionfeedbacks.usua_id_lider',
                    'usua_id' => 'tbl_ejecucionfeedbacks.usua_id',
                    'evaluado_id' => 'tbl_ejecucionfeedbacks.evaluado_id',
                    'ejecucionformulario_id' => 'tbl_ejecucionfeedbacks.ejecucionformulario_id',
                    'feaccion_correctiva' => 'tbl_ejecucionfeedbacks.feaccion_correctiva',
                    'tipofeedback_id' => 'tbl_ejecucionfeedbacks.tipofeedback_id',
                    'dscausa_raiz' => 'tbl_ejecucionfeedbacks.dscausa_raiz',
                    'dsaccion_correctiva' => 'tbl_ejecucionfeedbacks.dsaccion_correctiva',
                    'dscompromiso' => 'tbl_ejecucionfeedbacks.dscompromiso',
                    'dscomentario' => 'tbl_ejecucionfeedbacks.dscomentario',
                    'basessatisfaccion_id' => 'e.basesatisfaccion_id'
                ])
                ->distinct()
                ->join('LEFT JOIN', 'tbl_ejecucionformularios e', 'e.id = tbl_ejecucionfeedbacks.ejecucionformulario_id')
                ->join('LEFT JOIN', 'tbl_evaluados ev', 'ev.id = tbl_ejecucionfeedbacks.evaluado_id')
                ->join('LEFT JOIN', 'tbl_formularios f', 'f.id = e.formulario_id')
                ->join('LEFT JOIN', 'tbl_usuarios u', 'u.usua_id = tbl_ejecucionfeedbacks.usua_id')
                ->join('INNER JOIN', 'tbl_tipofeedbacks ti', 'ti.id = tbl_ejecucionfeedbacks.tipofeedback_id')
                ->join('INNER JOIN', 'tbl_categoriafeedbacks ca', 'ca.id = ti.categoriafeedback_id');

        $query->andFilterWhere([
            'tbl_ejecucionfeedbacks.evaluado_id' => $this->evaluado_id,
        ]);

        $query->andFilterWhere(['between', 'DATE(tbl_ejecucionfeedbacks.created)',
            $this->startDate, $this->endDate]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $dataProvider;
    }

    public static function getAlertasdashboard($usua_id, $limit = 15, $offset = 0) {

        return Ejecucionfeedbacks::find()
                        ->select('tbl_ejecucionfeedbacks.created, ev.name evaluado, u.usua_nombre usuario, 
                    tbl_ejecucionfeedbacks.evaluado_id, 
                    tbl_ejecucionfeedbacks.id, 
                    tbl_ejecucionfeedbacks.dscomentario,
                    ul.usua_nombre lider')
                        ->join('LEFT JOIN', 'tbl_evaluados ev', 'ev.id = tbl_ejecucionfeedbacks.evaluado_id')
                        ->join('LEFT JOIN', 'tbl_usuarios u', 'u.usua_id = tbl_ejecucionfeedbacks.usua_id')
                        ->join('LEFT JOIN', 'tbl_usuarios ul', 'ul.usua_id = tbl_ejecucionfeedbacks.usua_id_lider')
                        ->join('LEFT JOIN', 'tbl_equipos_evaluadores ee', 'ee.usua_id = ' . $usua_id)
                        ->where('snaviso_revisado = 0 AND ( tbl_ejecucionfeedbacks.usua_id_lider = ' . $usua_id . ' OR 
                            tbl_ejecucionfeedbacks.usua_id_lider IN (
                                SELECT `evaluadores_id`
                                FROM `tbl_rel_equipos_evaluadores`
                                WHERE `equipo_id` = ee.id
                        ))')
                        ->andWhere('tbl_ejecucionfeedbacks.ejecucionformulario_id IS NULL AND tbl_ejecucionfeedbacks.basessatisfaccion_id IS NULL')
                        ->orderBy("tbl_ejecucionfeedbacks.created DESC")
                        ->limit($limit)
                        ->offset($offset)
                        ->asArray()
                        ->all();
    }

    /**
     * Metodo para traer el resumen de los feedbacks agrupados por PCRC
     *      
     * 
     * @return array
     * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getResumenFeedback() {
        $where1 = "DATE(subejf.created) BETWEEN '" . $this->startDate . "' AND '" . $this->endDate . "'";
        $where2 = "DATE(tbl_ejecucionfeedbacks.created) BETWEEN '" . $this->startDate . "' AND '" . $this->endDate . "'";
        if (!empty($this->evaluado_id)) {
            $where1 .= " AND subejf.evaluado_id = " . $this->evaluado_id;
            $where2 .= " AND tbl_ejecucionfeedbacks.evaluado_id = " . $this->evaluado_id;
        }
        if (!empty($this->usua_id)) {
            $where1 .= " AND subejf.usua_id = " . $this->usua_id;
            $where2 .= " AND tbl_ejecucionfeedbacks.usua_id = " . $this->usua_id;
        }
        if (!empty($this->usua_id_lider)) {
            $where1 .= " AND subejf.usua_id_lider = " . $this->usua_id_lider;
            $where2 .= " AND tbl_ejecucionfeedbacks.usua_id_lider = " . $this->usua_id_lider;
        }
        if (!empty($this->arbol_id)) {

            $arbolesid = $this->arbol_id;
            $validarbol = Yii::$app->get('dbslave')->createCommand("select count(1) from tbl_arbols where id in ($arbolesid) and arbol_id in (0,1,2,98)")->queryScalar();
            if ($validarbol == 1) {
                $varlistarbol = Yii::$app->get('dbslave')->createCommand("select a.id from tbl_arbols a where a.arbol_id in ($arbolesid) group by a.id")->queryAll();
                $vararrayCC = array();
                foreach ($varlistarbol as $key => $value) {
                  array_push($vararrayCC, $value['id']);
                }
                $varCC = implode(", ", $vararrayCC);

                $where1 .= " AND esub.arbol_id IN (" . $varCC . ")";
                $where2 .= " AND e.arbol_id IN (" . $varCC . ")";
            }else{
                $where1 .= " AND esub.arbol_id IN (" . $this->arbol_id . ")";
                $where2 .= " AND e.arbol_id IN (" . $this->arbol_id . ")";
            } 

            // $where1 .= " AND esub.arbol_id IN (" . $this->arbol_id . ")";
            // $where2 .= " AND e.arbol_id IN (" . $this->arbol_id . ")";
        }
        if (!empty($this->dimension_id)) {
            $where1 .= " AND esub.dimension_id IN (" . $this->dimension_id . ")";
            $where2 .= " AND e.dimension_id IN (" . $this->dimension_id . ")";
        }

        $sql = "SELECT 
                megatabla.id_arbol,
                megatabla.cliente AS cliente, 
                megatabla.name AS pcrc,
                SUM(megatabla.totalFeedbacks) AS totalFeedbacks, 
                megatabla.cantidadG AS cantidadG,
                COUNT(megatabla.evaluado)  AS totalEvaluados,  
                (((megatabla.cantidadG)*100)/SUM(megatabla.totalFeedbacks)) AS PorcGest,
                AVG(megatabla.resta) AS promedioGest
                 FROM 

                (
                SELECT megatablaTemp.*, COUNT(megatablaTemp.id_arbol) AS totalFeedbacks FROM
                (

                (SELECT e.arbol_id AS id_arbol, ap.name AS cliente, a.name, tbl_ejecucionfeedbacks.evaluado_id AS evaluado,
                (
                SELECT COUNT(*) FROM `tbl_ejecucionfeedbacks` subejf 
                LEFT JOIN `tbl_ejecucionformularios` `esub` ON esub.id = subejf.ejecucionformulario_id  
                WHERE   subejf.snaviso_revisado = 1 AND esub.arbol_id = e.arbol_id 
                AND " . $where1 . "
                ) AS cantidadG, 

                TIMESTAMPDIFF(MINUTE,`tbl_ejecucionfeedbacks`.created,`tbl_ejecucionfeedbacks`.feaccion_correctiva) AS resta
                FROM `tbl_ejecucionfeedbacks` 
                LEFT JOIN `tbl_ejecucionformularios` `e` ON e.id = tbl_ejecucionfeedbacks.ejecucionformulario_id 
                LEFT JOIN `tbl_evaluados` `ev` ON ev.id = tbl_ejecucionfeedbacks.evaluado_id 
                LEFT JOIN `tbl_formularios` `f` ON f.id = e.formulario_id 
                LEFT JOIN `tbl_usuarios` `u` ON u.usua_id = tbl_ejecucionfeedbacks.usua_id 
                INNER JOIN `tbl_tipofeedbacks` `ti` ON ti.id = tbl_ejecucionfeedbacks.tipofeedback_id 
                INNER JOIN `tbl_categoriafeedbacks` `ca` ON ca.id = ti.categoriafeedback_id 
                INNER JOIN `tbl_arbols` a ON a.id = e.arbol_id
                INNER JOIN `tbl_arbols` ap ON ap.id = a.arbol_id
                WHERE " . $where2 . "
                ORDER BY resta DESC
                )
                ) AS megatablaTemp GROUP BY megatablaTemp.id_arbol, megatablaTemp.evaluado
                ) AS megatabla

                GROUP BY megatabla.id_arbol
                ORDER BY megatabla.cliente ASC";

        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    /**
     * Metodo para traer el detalles de los feedbacks agrupados por lider
     *      
     * 
     * @return array
     * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getDetalleLiderFeedback() {
        $where1 = "DATE(subejf.created) BETWEEN '" . $this->startDate . "' AND '" . $this->endDate . "'";
        $where2 = "DATE(tbl_ejecucionfeedbacks.created) BETWEEN '" . $this->startDate . "' AND '" . $this->endDate . "'";
        if (!empty($this->evaluado_id)) {
            $where1 .= " AND subejf.evaluado_id = " . $this->evaluado_id;
            $where2 .= " AND tbl_ejecucionfeedbacks.evaluado_id = " . $this->evaluado_id;
        }
        if (!empty($this->usua_id)) {
            $where1 .= " AND subejf.usua_id = " . $this->usua_id;
            $where2 .= " AND tbl_ejecucionfeedbacks.usua_id = " . $this->usua_id;
        }
        if (!empty($this->usua_id_lider)) {
            $where1 .= " AND subejf.usua_id_lider = " . $this->usua_id_lider;
            $where2 .= " AND tbl_ejecucionfeedbacks.usua_id_lider = " . $this->usua_id_lider;
        }
        if (!empty($this->arbol_id)) {

            $arbolesid = $this->arbol_id;
            $validarbol = Yii::$app->get('dbslave')->createCommand("select count(1) from tbl_arbols where id in ($arbolesid) and arbol_id in (0,1,2,98)")->queryScalar();
            if ($validarbol == 1) {
                $varlistarbol = Yii::$app->get('dbslave')->createCommand("select a.id from tbl_arbols a where a.arbol_id in ($arbolesid) group by a.id")->queryAll();
                $vararrayCC = array();
                foreach ($varlistarbol as $key => $value) {
                  array_push($vararrayCC, $value['id']);
                }
                $varCC = implode(", ", $vararrayCC);

                $where1 .= " AND esub.arbol_id IN (" . $varCC . ")";
                $where2 .= " AND e.arbol_id IN (" . $varCC . ")";
            }else{
                $where1 .= " AND esub.arbol_id IN (" . $this->arbol_id . ")";
                $where2 .= " AND e.arbol_id IN (" . $this->arbol_id . ")";
            } 

            // $where1 .= " AND esub.arbol_id IN (" . $this->arbol_id . ")";
            // $where2 .= " AND e.arbol_id IN (" . $this->arbol_id . ")";
        }
        if (!empty($this->dimension_id)) {
            $where1 .= " AND esub.dimension_id IN (" . $this->dimension_id . ")";
            $where2 .= " AND e.dimension_id IN (" . $this->dimension_id . ")";
        }

        $sql = "SELECT 
                megatabla.lider AS lider,
                megatabla.id_arbol,
                megatabla.nombreLider AS nombreLider,
                megatabla.cliente AS cliente, 
                megatabla.name AS pcrc,
                SUM(megatabla.totalFeedbacks) AS totalFeedbacks, 
                SUM(megatabla.gestiones) AS cantidadG, 
                COUNT(megatabla.evaluado)  AS totalEvaluados,  
                (((SUM(megatabla.gestiones))*100)/SUM(megatabla.totalFeedbacks)) AS PorcGest,
                AVG(megatabla.resta) AS promedioGest  
                 FROM 

                (SELECT megatablaTemp.*, 
                COUNT(megatablaTemp.id_arbol) AS totalFeedbacks, 
                COUNT(IF(megatablaTemp.`snaviso_revisado`=1,1,NULL)) AS gestiones	 
                FROM	
                (
                SELECT tbl_ejecucionfeedbacks.`snaviso_revisado`, e.arbol_id AS id_arbol, usl.usua_nombre AS nombreLider, 
                tbl_ejecucionfeedbacks.usua_id_lider AS lider,
                ap.name AS cliente, a.name, tbl_ejecucionfeedbacks.evaluado_id AS evaluado,
                TIMESTAMPDIFF(MINUTE,`tbl_ejecucionfeedbacks`.created,`tbl_ejecucionfeedbacks`.feaccion_correctiva) AS resta
                FROM `tbl_ejecucionfeedbacks` 
                LEFT JOIN `tbl_ejecucionformularios` `e` ON e.id = tbl_ejecucionfeedbacks.ejecucionformulario_id 
                LEFT JOIN `tbl_evaluados` `ev` ON ev.id = tbl_ejecucionfeedbacks.evaluado_id 
                LEFT JOIN `tbl_formularios` `f` ON f.id = e.formulario_id 
                LEFT JOIN `tbl_usuarios` `u` ON u.usua_id = tbl_ejecucionfeedbacks.usua_id 
                INNER JOIN `tbl_tipofeedbacks` `ti` ON ti.id = tbl_ejecucionfeedbacks.tipofeedback_id 
                INNER JOIN `tbl_categoriafeedbacks` `ca` ON ca.id = ti.categoriafeedback_id 
                INNER JOIN `tbl_arbols` a ON a.id = e.arbol_id
                INNER JOIN `tbl_arbols` ap ON ap.id = a.arbol_id
                LEFT JOIN `tbl_usuarios` `usl` ON usl.usua_id = tbl_ejecucionfeedbacks.usua_id_lider
                WHERE " . $where2 . "
                ORDER BY resta DESC
                ) AS megatablaTemp GROUP BY megatablaTemp.lider, megatablaTemp.id_arbol, megatablaTemp.evaluado
                ) AS megatabla

                GROUP BY megatabla.lider, id_arbol
                ORDER BY megatabla.cliente ASC";


        return Yii::$app->db->createCommand($sql)->queryAll();
    }

}
