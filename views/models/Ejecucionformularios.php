<?php

namespace app\models;

use Yii;
use yii\base\Exepcion;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tbl_ejecucionformularios".
 *
 * @property integer $id
 * @property integer $dimension_id
 * @property integer $arbol_id
 * @property integer $usua_id
 * @property integer $evaluado_id
 * @property integer $formulario_id
 * @property string $created
 * @property string $dscomentario
 * @property integer $snprocesado_estadisticas
 * @property integer $usua_id_responsable
 * @property string $dsfuente_encuesta
 * @property integer $snenviar_email
 * @property integer $transacion_id
 * @property integer $usua_id_modifica
 * @property string $modified
 * @property string $dsruta_arbol
 * @property integer $usua_id_lider
 * @property integer $equipo_id
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
 * @property double $i1_nmcalculo
 * @property double $i2_nmcalculo
 * @property double $i3_nmcalculo
 * @property double $i4_nmcalculo
 * @property double $i5_nmcalculo
 * @property double $i6_nmcalculo
 * @property double $i7_nmcalculo
 * @property double $i8_nmcalculo
 * @property double $i9_nmcalculo
 * @property double $i10_nmcalculo
 * @property double $pec_rack
 * @property double $scor
 * @property integer $basesatisfaccion_id
 * 
 * @property TblEjecucionfeedbacks[] $tblEjecucionfeedbacks
 * @property TblArbols $arbol
 * @property TblEvaluados $evaluado
 * @property TblFormularios $formulario
 * @property TblTransacions $transacion
 * @property TblDimensions $dimension
 * @property TblEjecucionseccions[] $tblEjecucionseccions
 * @property TblEjecuciontableroexperiencias[] $tblEjecuciontableroexperiencias
 * @property TblEjecuciontiposllamada[] $tblEjecuciontiposllamadas
 * @property BaseSatisfaccion[] $baseSatisfaccion
 */
class Ejecucionformularios extends \yii\db\ActiveRecord {

    public $startDate;
    public $endDate;
    public $tipoReporte;
    public $acentos = array(":");
    public $sinAcentos = array("");
    public $banderaEscalamiento; //variable usada en la vista para identificar en los repotes que fue un escalamiento o valoracion adional

    /**
     * @inheritdoc
     */

    public static function tableName() {
        return 'tbl_ejecucionformularios';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [[/* 'dimension_id', 'arbol_id', 'evaluado_id', 'usua_id', */ 'formulario_id',
            'snprocesado_estadisticas', 'usua_id_responsable', 'snenviar_email',
            'transacion_id', 'usua_id_modifica', 'usua_id_lider', 'equipo_id', 'basesatisfaccion_id', 'sn_mostrarcalculo', 'ejec_principal'], 'integer'],
            [['created', 'modified', 'dimension_id', 'arbol_id', 'evaluado_id', 'usua_id', 'estado', 'banderaEscalamiento'], 'safe'],
            [['dscomentario', 'tipoReporte'], 'string'],
            [['i1_nmfactor', 'i2_nmfactor', 'i3_nmfactor', 'i4_nmfactor', 'i5_nmfactor',
            'i6_nmfactor', 'i7_nmfactor', 'i8_nmfactor', 'i9_nmfactor', 'i10_nmfactor',
            'i1_nmcalculo', 'i2_nmcalculo', 'i3_nmcalculo', 'i4_nmcalculo', 'i5_nmcalculo',
            'i6_nmcalculo', 'i7_nmcalculo', 'i8_nmcalculo', 'i9_nmcalculo', 'i10_nmcalculo',
            'pec_rack', 'score'], 'number'],
            [['dsfuente_encuesta'], 'string', 'max' => 500],
            [['dsruta_arbol'], 'string', 'max' => 300],
            [['subi_calculo'], 'string', 'max' => 50],
            [['i1_cdtipo_eval', 'i2_cdtipo_eval', 'i3_cdtipo_eval', 'i4_cdtipo_eval',
            'i5_cdtipo_eval', 'i6_cdtipo_eval', 'i7_cdtipo_eval', 'i8_cdtipo_eval',
            'i9_cdtipo_eval', 'i10_cdtipo_eval'], 'string', 'max' => 3],
            [['tipoReporte', 'created'], 'required', 'on' => 'experiencias'],
            [['arbol_id', 'created'], 'required', 'on' => 'extractar'],
            [['created'], 'required', 'on' => 'historico'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'dimension_id' => Yii::t('app', 'Dimension'),
            'arbol_id' => Yii::t('app', 'Arbol'),
            'usua_id' => Yii::t('app', 'Valorador'),
            'evaluado_id' => Yii::t('app', 'Valorado'),
            'evaluado_cedula' => Yii::t('app', 'Cedula'),
            'formulario_id' => Yii::t('app', 'Id Formulario'),
            'created' => Yii::t('app', 'Fecha de Valoración'),
            'dscomentario' => Yii::t('app', 'Dscomentario'),
            'snprocesado_estadisticas' => Yii::t('app', 'Snprocesado Estadisticas'),
            'usua_id_responsable' => Yii::t('app', 'Usua Id Responsable'),
            'dsfuente_encuesta' => Yii::t('app', 'Dsfuente Encuesta'),
            'snenviar_email' => Yii::t('app', 'Snenviar Email'),
            'transacion_id' => Yii::t('app', 'Transacion ID'),
            'usua_id_modifica' => Yii::t('app', 'Id Usuario/Modificación'),
            'modified' => Yii::t('app', 'Fecha de Modificación'),
            'dsruta_arbol' => Yii::t('app', 'Dsruta Arbol'),
            'usua_id_lider' => Yii::t('app', 'Lider de Equipo'),
            'equipo_id' => Yii::t('app', 'Equipo'),
            'tipoReporte' => Yii::t('app', 'Tipo de Reporte'),
            'i1_nmfactor' => Yii::t('app', 'I1 Nmfactor'),
            'i2_nmfactor' => Yii::t('app', 'I2 Nmfactor'),
            'i3_nmfactor' => Yii::t('app', 'I3 Nmfactor'),
            'i4_nmfactor' => Yii::t('app', 'I4 Nmfactor'),
            'i5_nmfactor' => Yii::t('app', 'I5 Nmfactor'),
            'i6_nmfactor' => Yii::t('app', 'I6 Nmfactor'),
            'i7_nmfactor' => Yii::t('app', 'I7 Nmfactor'),
            'i8_nmfactor' => Yii::t('app', 'I8 Nmfactor'),
            'i9_nmfactor' => Yii::t('app', 'I9 Nmfactor'),
            'i10_nmfactor' => Yii::t('app', 'I10 Nmfactor'),
            'i1_cdtipo_eval' => Yii::t('app', 'I1 Cdtipo Eval'),
            'i2_cdtipo_eval' => Yii::t('app', 'I2 Cdtipo Eval'),
            'i3_cdtipo_eval' => Yii::t('app', 'I3 Cdtipo Eval'),
            'i4_cdtipo_eval' => Yii::t('app', 'I4 Cdtipo Eval'),
            'i5_cdtipo_eval' => Yii::t('app', 'I5 Cdtipo Eval'),
            'i6_cdtipo_eval' => Yii::t('app', 'I6 Cdtipo Eval'),
            'i7_cdtipo_eval' => Yii::t('app', 'I7 Cdtipo Eval'),
            'i8_cdtipo_eval' => Yii::t('app', 'I8 Cdtipo Eval'),
            'i9_cdtipo_eval' => Yii::t('app', 'I9 Cdtipo Eval'),
            'i10_cdtipo_eval' => Yii::t('app', 'I10 Cdtipo Eval'),
            'i1_nmcalculo' => Yii::t('app', 'PEC'),
            'i2_nmcalculo' => Yii::t('app', 'PENC'),
            'i3_nmcalculo' => Yii::t('app', 'CARIÑO/'),
            'i4_nmcalculo' => Yii::t('app', 'NA'),
            'i5_nmcalculo' => Yii::t('app', 'No'),
            'i6_nmcalculo' => Yii::t('app', 'texto i7'),
            'i7_nmcalculo' => Yii::t('app', 'texto i8'),
            'i8_nmcalculo' => Yii::t('app', 'texto i9'),
            'i9_nmcalculo' => Yii::t('app', 'texto i10'),
            'i10_nmcalculo' => Yii::t('app', 'Mensaje de alerta por nota muy baja'),
            'pec_rack' => Yii::t('app', 'Pec Rack'),
            'score' => Yii::t('app', 'Score'),
            'basesatisfaccion_id' => Yii::t('app', 'ID Gestion Satisfaccion'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEjecucionfeedbacks() {
        return $this->hasMany(Ejecucionfeedbacks::className(), ['ejecucionformulario_id' => 'id']);
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
    public function getArbol() {
        return $this->hasOne(Arbols::className(), ['id' => 'arbol_id']);
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
    public function getFormulario() {
        return $this->hasOne(Formularios::className(), ['id' => 'formulario_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransacion() {
        return $this->hasOne(Transacions::className(), ['id' => 'transacion_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDimension() {
        return $this->hasOne(Dimensiones::className(), ['id' => 'dimension_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEjecucionseccions() {
        return $this->hasMany(Ejecucionseccions::className(), ['ejecucionformulario_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEjecuciontableroexperiencias() {
        return $this->hasMany(Ejecuciontableroexperiencias::className(), ['ejecucionformulario_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEjecuciontiposllamadas() {
        return $this->hasMany(Ejecuciontiposllamada::className(), ['ejecucionformulario_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBasesatisfaccion() {
        return $this->hasMany(BaseSatisfaccion::className(), ['basesatisfaccion_id' => 'id']);
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
     * Metodo que retorna el listado de arboles
     * 
     * @return array
     * @author Daniel Enciso <daniel.enciso@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getArbolsList() {
        return ArrayHelper::map(Arboles::find()->orderBy('dsname_full')->asArray()->all(), 'id', 'dsname_full');
    }

    /**
     * Metodo que permite la busqueda en el reporte de 
     * formularios
     *      
     * 
     * @return \app\models\ActiveDataProvider
     * @author Daniel Enciso <daniel.enciso@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getReportFormularios($bandera) {

        $dates = explode(' - ', $this->created);
        $startDate = $dates[0] . " 00:00:00";
        $endDate = $dates[1] . " 23:59:59";
        $and = "";
        if ($this->evaluado_id != '') {
            $and .= " AND t.evaluado_id IN (" . $this->evaluado_id . ")";
        }
        if ($this->usua_id != '') {
            $and .= " AND t.usua_id IN (" . $this->usua_id . ")";
        }
        if ($this->usua_id_lider != '') {
            $and .= " AND t.usua_id_lider = " . $this->usua_id_lider;
        }
        if ($this->dimension_id != '') {
            $and .= " AND t.dimension_id IN (" . $this->dimension_id . ")";
        }
        if ($this->arbol_id != '') {
            $and .= " AND tra.seleccion_arbol_id IN (" . $this->arbol_id . ")";
            $and .= " AND tra.usua_id = " . Yii::$app->user->identity->id . "";
        }

        if ($this->equipo_id != '') {
            $and .= " AND t.equipo_id = " . $this->equipo_id;
        }
        $sql = "SELECT DISTINCT t.id, t.created,e.name evaluado, 
                e.identificacion eidentificacion, f.name formulario, t.dsruta_arbol nmarbol,
                u.usua_nombre usuario, r.role_nombre, d.name dimension,
                t.usua_id_lider, ul.usua_nombre usuarioLider, eq.name equipoName,  
                ROUND((t.score)*100,2) as score, 
                ROUND( t.pec_rack*100,2 ) as 'pec_rac', 
                ROUND( t.i1_nmcalculo*100,2 ) as 'i1_nmcalculo', 
                ROUND( t.i2_nmcalculo*100,2 ) as 'i2_nmcalculo', 
                ROUND( t.i3_nmcalculo*100,2 ) as 'i3_nmcalculo', 
                ROUND( t.i4_nmcalculo*100,2 ) as 'i4_nmcalculo', 
                ROUND( t.i5_nmcalculo*100,2 ) as 'i5_nmcalculo', 
                ROUND( t.i6_nmcalculo*100,2 ) as 'i6_nmcalculo', 
                ROUND( t.i7_nmcalculo*100,2 ) as 'i7_nmcalculo', 
                ROUND( t.i8_nmcalculo*100,2 ) as 'i8_nmcalculo', 
                ROUND( t.i9_nmcalculo*100,2 ) as 'i9_nmcalculo', 
                ROUND( t.i10_nmcalculo*100,2 ) as 'i10_nmcalculo', 
                t.dscomentario, t.usua_id_modifica, t.modified, 
                t.evaluado_id, t.formulario_id, t.id fid ,t.usua_id ideva, t.ejec_principal, t.estado
                FROM tbl_ejecucionformularios t 
		FORCE INDEX(created_idx)
                INNER JOIN tbl_evaluados e ON e.id = t.evaluado_id 
                INNER JOIN tbl_formularios f ON f.id = t.formulario_id 
                INNER JOIN tbl_usuarios u ON u.usua_id = t.usua_id 
                LEFT JOIN tbl_usuarios ul ON ul.usua_id = t.usua_id_lider 
                LEFT JOIN rel_usuarios_roles ur ON ur.rel_usua_id = t.usua_id 
                LEFT JOIN tbl_equipos eq ON eq.id = t.equipo_id 
                LEFT JOIN tbl_roles r ON r.role_id = ur.rel_role_id ";
        //INNER JOIN tbl_arbols a ON a.id = t.arbol_id 
        $sql .= "INNER JOIN tbl_tmpreportes_arbol tra ON tra.arbol_id = t.arbol_id
                INNER JOIN tbl_dimensions d ON d.id = t.dimension_id                 
                WHERE t.created >= ' " . $startDate . "' 
                AND t.created <= '" . $endDate . "'" . $and . " 
                ORDER BY t.created DESC";

        $count = Yii::$app->get(dbslave)->createCommand($sql)->queryAll();
        if ($bandera) {
            $dataProvider = new SqlDataProvider([
                'sql' => $sql,
                'totalCount' => count($count),
                'pagination' => ['pageSize' => 20,],
            ]);
        } else {
            $dataProvider = new SqlDataProvider([
                'sql' => $sql,
                //'totalCount' => count($count),
                //'pagination' => ['pageSize' => 20,],
            ]);
        }

        return $dataProvider;
    }

    /**
     * Metodo que permite la busqueda en el reporte de 
     * extractar formularios
     *      
     * 
     * @return \app\models\ActiveDataProvider
     * @author Daniel Enciso <daniel.enciso@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getReporteExctractarFormularios() {
        set_time_limit(0);
        $dates = explode(' - ', $this->created);
        $startDate = $dates[0] . " 00:00:00";
        $endDate = $dates[1] . " 23:59:59";

        //PRIMERO CUENTO LOS REGISTROS
        $sqlCount = "SELECT count(f.created) total";

        $sqlCount .= " FROM (tbl_ejecucionformularios f, tbl_formularios xf, tbl_arbols xarbol,  
                  tbl_usuarios xusuarios, tbl_evaluados xevaluados, tbl_transacions xtransacions,
                  tbl_usuarios xusuarios2, tbl_equipos xequipos, rel_usuarios_roles urol, tbl_roles rol, 
                  tbl_arbols xarbol_padre, tbl_dimensions xdim, tbl_ejecucionseccions s, tbl_seccions xs, 
                  tbl_ejecucionbloques b, tbl_bloques xb, tbl_calificaciondetalles xcd, tbl_bloquedetalles xd, 
                  tbl_ejecucionbloquedetalles d) ";

        $sqlCount .= " LEFT JOIN tbl_tipificaciondetalles xtd ON xd.tipificacion_id = xtd.tipificacion_id 
                  LEFT JOIN tbl_ejecucionbloquedetalles_tipificaciones t 
                  ON xtd.id = t.tipificaciondetalle_id AND t.ejecucionbloquedetalle_id = d.id  
                  LEFT JOIN tbl_tipificaciondetalles xstd ON xtd.subtipificacion_id = xstd.tipificacion_id  
                  LEFT JOIN tbl_ejecucionbloquedetalles_subtipificaciones st 
                  ON xstd.id = st.tipificaciondetalle_id AND t.id =  st.ejecucionbloquedetalles_tipificacion_id";


        $sqlCount .= " WHERE f.arbol_id = " . $this->arbol_id . " AND f.created BETWEEN '" . $startDate . "' AND '" . $endDate . "' 
                  AND xf.id = f.formulario_id AND f.arbol_id = xarbol.id AND xtransacions.id = f.transacion_id 
                  AND xarbol.arbol_id = xarbol_padre.id AND f.dimension_id = xdim.id AND f.evaluado_id = xevaluados.id 
                  AND f.usua_id_lider = xusuarios.usua_id AND f.usua_id = xusuarios2.usua_id AND f.equipo_id = xequipos.id 
                  AND f.usua_id = urol.rel_usua_id AND urol.rel_role_id = rol.role_id AND f.id = s.ejecucionformulario_id 
                  AND xs.id = s.seccion_id AND s.id = b.ejecucionseccion_id AND xb.id = b.bloque_id 
                  AND b.id = d.ejecucionbloque_id AND xd.id = d.bloquedetalle_id AND d.calificaciondetalle_id = xcd.id";

        if ($this->dimension_id != '') {
            $sqlCount .= " AND xdim.id = " . $this->dimension_id;
        }

        $sqlCount .= " ORDER BY f.id, xs.nmorden, xs.id, xb.nmorden, xb.id, xd.nmorden, xd.id, xtd.nmorden, xtd.id, xstd.nmorden, xstd.id ";
        $dataProviderCount = Yii::$app->db->createCommand($sqlCount)->queryAll();


        $limitQueryExtractarFormulario = \Yii::$app->params["limitQueryExtractarFormulario"];
        $limitQuery = ceil((int) $dataProviderCount[0]["total"] / $limitQueryExtractarFormulario);

        if ($limitQuery > 1) {

            $limitInicial = 0;
            $limitFinal = $limitQueryExtractarFormulario;
            $dataProviderLimit = array();
            for ($index = 1; $index <= $limitQuery; $index++) {
                $sql = "SELECT f.created 'Fecha' ,f.id fid ,s.id 'sid' , xb.id 'did', xd.id 'cdPregunta', xd.tipificacion_id 'idTipi', 
                xtd.id 'cdTipificacionDetalle', t.tipificaciondetalle_id, xdim.name 'Dimension', 
                xarbol_padre.name 'ArbolPadre',xarbol.name 'Arbol',xf.name 'Formulario', f.evaluado_id, 
                f.i1_nmcalculo 'fi1_nmcalculo', f.i2_nmcalculo 'fi2_nmcalculo', f.i3_nmcalculo 'fi3_nmcalculo', 
                f.i4_nmcalculo 'fi4_nmcalculo', f.i5_nmcalculo 'fi5_nmcalculo', f.i6_nmcalculo 'fi6_nmcalculo', 
                f.i7_nmcalculo 'fi7_nmcalculo', f.i8_nmcalculo 'fi8_nmcalculo', f.i9_nmcalculo 'fi9_nmcalculo', 
                f.i10_nmcalculo 'fi10_nmcalculo', f.i1_nmfactor 'fi1_nmfactor', f.i2_nmfactor 'fi2_nmfactor', 
                f.i3_nmfactor 'fi3_nmfactor', f.i4_nmfactor 'fi4_nmfactor', f.i5_nmfactor 'fi5_nmfactor', 
                f.i6_nmfactor 'fi6_nmfactor', f.i7_nmfactor 'fi7_nmfactor', f.i8_nmfactor 'fi8_nmfactor', 
                f.i9_nmfactor 'fi9_nmfactor', f.i10_nmfactor 'fi10_nmfactor',
                xs.name 'Seccion',
                s.i1_nmcalculo 'si1_nmcalculo', s.i2_nmcalculo 'si2_nmcalculo', s.i3_nmcalculo 'si3_nmcalculo', 
                s.i4_nmcalculo 'si4_nmcalculo', s.i5_nmcalculo 'si5_nmcalculo', s.i6_nmcalculo 'si6_nmcalculo', 
                s.i7_nmcalculo 'si7_nmcalculo', s.i8_nmcalculo 'si8_nmcalculo', s.i9_nmcalculo 'si9_nmcalculo', 
                s.i10_nmcalculo 'si10_nmcalculo', s.i1_nmfactor 'si1_nmfactor', s.i2_nmfactor 'si2_nmfactor', 
                s.i3_nmfactor 'si3_nmfactor', s.i4_nmfactor 'si4_nmfactor', s.i5_nmfactor 'si5_nmfactor', 
                s.i6_nmfactor 'si6_nmfactor', s.i7_nmfactor 'si7_nmfactor', s.i8_nmfactor 'si8_nmfactor', 
                s.i9_nmfactor 'si9_nmfactor', s.i10_nmfactor 'si10_nmfactor',
                xb.name 'Bloque',
                b.i1_nmcalculo 'bi1_nmcalculo', b.i2_nmcalculo 'bi2_nmcalculo', b.i3_nmcalculo 'bi3_nmcalculo', 
                b.i4_nmcalculo 'bi4_nmcalculo', b.i5_nmcalculo 'bi5_nmcalculo', b.i6_nmcalculo 'bi6_nmcalculo', 
                b.i7_nmcalculo 'bi7_nmcalculo', b.i8_nmcalculo 'bi8_nmcalculo', b.i9_nmcalculo 'bi9_nmcalculo', 
                b.i10_nmcalculo 'bi10_nmcalculo', b.i1_nmfactor 'bi1_nmfactor', b.i2_nmfactor 'bi2_nmfactor', 
                b.i3_nmfactor 'bi3_nmfactor', b.i4_nmfactor 'bi4_nmfactor', b.i5_nmfactor 'bi5_nmfactor', 
                b.i6_nmfactor 'bi6_nmfactor', b.i7_nmfactor 'bi7_nmfactor', b.i8_nmfactor 'bi8_nmfactor', 
                b.i9_nmfactor 'bi9_nmfactor', b.i10_nmfactor 'bi10_nmfactor',
                xd.name 'Pregunta', xcd.name 'Respuesta',
                d.i1_nmcalculo 'di1_nmcalculo', d.i2_nmcalculo 'di2_nmcalculo', d.i3_nmcalculo 'di3_nmcalculo', 
                d.i4_nmcalculo 'di4_nmcalculo', d.i5_nmcalculo 'di5_nmcalculo', d.i6_nmcalculo 'di6_nmcalculo', 
                d.i7_nmcalculo 'di7_nmcalculo', d.i8_nmcalculo 'di8_nmcalculo', d.i9_nmcalculo 'di9_nmcalculo', 
                d.i10_nmcalculo 'di10_nmcalculo', d.i1_nmfactor 'di1_nmfactor', d.i2_nmfactor 'di2_nmfactor', 
                d.i3_nmfactor 'di3_nmfactor', d.i4_nmfactor 'di4_nmfactor', d.i5_nmfactor 'di5_nmfactor', 
                d.i6_nmfactor 'di6_nmfactor', d.i7_nmfactor 'di7_nmfactor', d.i8_nmfactor 'di8_nmfactor', 
                d.i9_nmfactor 'di9_nmfactor', d.i10_nmfactor 'di10_nmfactor',
                xtd.name 'Tipificacion',
                xusuarios.usua_nombre 'responsable', xevaluados.name 'evaluado', 
                xevaluados.identificacion 'cedula_evaluado', 
                xusuarios2.usua_nombre 'evaluador', f.dsfuente_encuesta 'fuente', xequipos.name 'equipo', 
                xtd.subtipificacion_id 'cdSubTipificacionDetalle', xstd.id 'cdsubtipificacion', 
                xstd.name 'subtipificacion', st.id 'IDsubtipificacion', xtransacions.name 'transacion', 
                s.dscomentario 'sdscomentario', f.dscomentario 'fdscomentario', xcd.i1_povalor 'i1_poRespuesta', 
                xcd.i2_povalor 'i2_poRespuesta', xcd.i3_povalor 'i3_poRespuesta', xcd.i4_povalor 'i4_poRespuesta', 
                xcd.i5_povalor 'i5_poRespuesta', xcd.i6_povalor 'i6_poRespuesta', xcd.i7_povalor 'i7_poRespuesta', 
                xcd.i8_povalor 'i8_poRespuesta', xcd.i9_povalor 'i9_poRespuesta', xcd.i10_povalor 'i10_poRespuesta', 
                rol.role_nombre rol";

                $sql .= " FROM (tbl_ejecucionformularios f, tbl_formularios xf, tbl_arbols xarbol,  
                  tbl_usuarios xusuarios, tbl_evaluados xevaluados, tbl_transacions xtransacions,
                  tbl_usuarios xusuarios2, tbl_equipos xequipos, rel_usuarios_roles urol, tbl_roles rol, 
                  tbl_arbols xarbol_padre, tbl_dimensions xdim, tbl_ejecucionseccions s, tbl_seccions xs, 
                  tbl_ejecucionbloques b, tbl_bloques xb, tbl_calificaciondetalles xcd, tbl_bloquedetalles xd, 
                  tbl_ejecucionbloquedetalles d) ";

                $sql .= " LEFT JOIN tbl_tipificaciondetalles xtd ON xd.tipificacion_id = xtd.tipificacion_id 
                  LEFT JOIN tbl_ejecucionbloquedetalles_tipificaciones t 
                  ON xtd.id = t.tipificaciondetalle_id AND t.ejecucionbloquedetalle_id = d.id  
                  LEFT JOIN tbl_tipificaciondetalles xstd ON xtd.subtipificacion_id = xstd.tipificacion_id  
                  LEFT JOIN tbl_ejecucionbloquedetalles_subtipificaciones st 
                  ON xstd.id = st.tipificaciondetalle_id AND t.id =  st.ejecucionbloquedetalles_tipificacion_id";


                $sql .= " WHERE f.arbol_id = " . $this->arbol_id . " AND f.created BETWEEN '" . $startDate . "' AND '" . $endDate . "' 
                  AND xf.id = f.formulario_id AND f.arbol_id = xarbol.id AND xtransacions.id = f.transacion_id 
                  AND xarbol.arbol_id = xarbol_padre.id AND f.dimension_id = xdim.id AND f.evaluado_id = xevaluados.id 
                  AND f.usua_id_lider = xusuarios.usua_id AND f.usua_id = xusuarios2.usua_id AND f.equipo_id = xequipos.id 
                  AND f.usua_id = urol.rel_usua_id AND urol.rel_role_id = rol.role_id AND f.id = s.ejecucionformulario_id 
                  AND xs.id = s.seccion_id AND s.id = b.ejecucionseccion_id AND xb.id = b.bloque_id 
                  AND b.id = d.ejecucionbloque_id AND xd.id = d.bloquedetalle_id AND d.calificaciondetalle_id = xcd.id";

                if ($this->dimension_id != '') {
                    $sql .= " AND xdim.id = " . $this->dimension_id;
                }

                $sql .= " ORDER BY f.id, xs.nmorden, xs.id, xb.nmorden, xb.id, xd.nmorden, xd.id, xtd.nmorden, xtd.id, xstd.nmorden, xstd.id ";
                $sql .= " LIMIT  " . $limitInicial . ", " . $limitFinal;
                $limitInicial = $limitInicial + $limitQueryExtractarFormulario;
                $dataProviderLimit = array_merge($dataProviderLimit, Yii::$app->get(dbslave)->createCommand($sql)->queryAll());
            }
            $dataProvider = $dataProviderLimit;
        } else {
            $sql = "SELECT f.created 'Fecha' ,f.id fid ,s.id 'sid' , xb.id 'did', xd.id 'cdPregunta', xd.tipificacion_id 'idTipi', 
                xtd.id 'cdTipificacionDetalle', t.tipificaciondetalle_id, xdim.name 'Dimension', 
                xarbol_padre.name 'ArbolPadre',xarbol.name 'Arbol',xf.name 'Formulario', f.evaluado_id, 
                f.i1_nmcalculo 'fi1_nmcalculo', f.i2_nmcalculo 'fi2_nmcalculo', f.i3_nmcalculo 'fi3_nmcalculo', 
                f.i4_nmcalculo 'fi4_nmcalculo', f.i5_nmcalculo 'fi5_nmcalculo', f.i6_nmcalculo 'fi6_nmcalculo', 
                f.i7_nmcalculo 'fi7_nmcalculo', f.i8_nmcalculo 'fi8_nmcalculo', f.i9_nmcalculo 'fi9_nmcalculo', 
                f.i10_nmcalculo 'fi10_nmcalculo', f.i1_nmfactor 'fi1_nmfactor', f.i2_nmfactor 'fi2_nmfactor', 
                f.i3_nmfactor 'fi3_nmfactor', f.i4_nmfactor 'fi4_nmfactor', f.i5_nmfactor 'fi5_nmfactor', 
                f.i6_nmfactor 'fi6_nmfactor', f.i7_nmfactor 'fi7_nmfactor', f.i8_nmfactor 'fi8_nmfactor', 
                f.i9_nmfactor 'fi9_nmfactor', f.i10_nmfactor 'fi10_nmfactor',
                xs.name 'Seccion',
                s.i1_nmcalculo 'si1_nmcalculo', s.i2_nmcalculo 'si2_nmcalculo', s.i3_nmcalculo 'si3_nmcalculo', 
                s.i4_nmcalculo 'si4_nmcalculo', s.i5_nmcalculo 'si5_nmcalculo', s.i6_nmcalculo 'si6_nmcalculo', 
                s.i7_nmcalculo 'si7_nmcalculo', s.i8_nmcalculo 'si8_nmcalculo', s.i9_nmcalculo 'si9_nmcalculo', 
                s.i10_nmcalculo 'si10_nmcalculo', s.i1_nmfactor 'si1_nmfactor', s.i2_nmfactor 'si2_nmfactor', 
                s.i3_nmfactor 'si3_nmfactor', s.i4_nmfactor 'si4_nmfactor', s.i5_nmfactor 'si5_nmfactor', 
                s.i6_nmfactor 'si6_nmfactor', s.i7_nmfactor 'si7_nmfactor', s.i8_nmfactor 'si8_nmfactor', 
                s.i9_nmfactor 'si9_nmfactor', s.i10_nmfactor 'si10_nmfactor',
                xb.name 'Bloque',
                b.i1_nmcalculo 'bi1_nmcalculo', b.i2_nmcalculo 'bi2_nmcalculo', b.i3_nmcalculo 'bi3_nmcalculo', 
                b.i4_nmcalculo 'bi4_nmcalculo', b.i5_nmcalculo 'bi5_nmcalculo', b.i6_nmcalculo 'bi6_nmcalculo', 
                b.i7_nmcalculo 'bi7_nmcalculo', b.i8_nmcalculo 'bi8_nmcalculo', b.i9_nmcalculo 'bi9_nmcalculo', 
                b.i10_nmcalculo 'bi10_nmcalculo', b.i1_nmfactor 'bi1_nmfactor', b.i2_nmfactor 'bi2_nmfactor', 
                b.i3_nmfactor 'bi3_nmfactor', b.i4_nmfactor 'bi4_nmfactor', b.i5_nmfactor 'bi5_nmfactor', 
                b.i6_nmfactor 'bi6_nmfactor', b.i7_nmfactor 'bi7_nmfactor', b.i8_nmfactor 'bi8_nmfactor', 
                b.i9_nmfactor 'bi9_nmfactor', b.i10_nmfactor 'bi10_nmfactor',
                xd.name 'Pregunta', xcd.name 'Respuesta',
                d.i1_nmcalculo 'di1_nmcalculo', d.i2_nmcalculo 'di2_nmcalculo', d.i3_nmcalculo 'di3_nmcalculo', 
                d.i4_nmcalculo 'di4_nmcalculo', d.i5_nmcalculo 'di5_nmcalculo', d.i6_nmcalculo 'di6_nmcalculo', 
                d.i7_nmcalculo 'di7_nmcalculo', d.i8_nmcalculo 'di8_nmcalculo', d.i9_nmcalculo 'di9_nmcalculo', 
                d.i10_nmcalculo 'di10_nmcalculo', d.i1_nmfactor 'di1_nmfactor', d.i2_nmfactor 'di2_nmfactor', 
                d.i3_nmfactor 'di3_nmfactor', d.i4_nmfactor 'di4_nmfactor', d.i5_nmfactor 'di5_nmfactor', 
                d.i6_nmfactor 'di6_nmfactor', d.i7_nmfactor 'di7_nmfactor', d.i8_nmfactor 'di8_nmfactor', 
                d.i9_nmfactor 'di9_nmfactor', d.i10_nmfactor 'di10_nmfactor',
                xtd.name 'Tipificacion',
                xusuarios.usua_nombre 'responsable', xevaluados.name 'evaluado', 
                xevaluados.identificacion 'cedula_evaluado', 
                xusuarios2.usua_nombre 'evaluador', f.dsfuente_encuesta 'fuente', xequipos.name 'equipo', 
                xtd.subtipificacion_id 'cdSubTipificacionDetalle', xstd.id 'cdsubtipificacion', 
                xstd.name 'subtipificacion', st.id 'IDsubtipificacion', xtransacions.name 'transacion', 
                s.dscomentario 'sdscomentario', f.dscomentario 'fdscomentario', xcd.i1_povalor 'i1_poRespuesta', 
                xcd.i2_povalor 'i2_poRespuesta', xcd.i3_povalor 'i3_poRespuesta', xcd.i4_povalor 'i4_poRespuesta', 
                xcd.i5_povalor 'i5_poRespuesta', xcd.i6_povalor 'i6_poRespuesta', xcd.i7_povalor 'i7_poRespuesta', 
                xcd.i8_povalor 'i8_poRespuesta', xcd.i9_povalor 'i9_poRespuesta', xcd.i10_povalor 'i10_poRespuesta', 
                rol.role_nombre rol";

            $sql .= " FROM (tbl_ejecucionformularios f, tbl_formularios xf, tbl_arbols xarbol,  
                  tbl_usuarios xusuarios, tbl_evaluados xevaluados, tbl_transacions xtransacions,
                  tbl_usuarios xusuarios2, tbl_equipos xequipos, rel_usuarios_roles urol, tbl_roles rol, 
                  tbl_arbols xarbol_padre, tbl_dimensions xdim, tbl_ejecucionseccions s, tbl_seccions xs, 
                  tbl_ejecucionbloques b, tbl_bloques xb, tbl_calificaciondetalles xcd, tbl_bloquedetalles xd, 
                  tbl_ejecucionbloquedetalles d) ";

            $sql .= " LEFT JOIN tbl_tipificaciondetalles xtd ON xd.tipificacion_id = xtd.tipificacion_id 
                  LEFT JOIN tbl_ejecucionbloquedetalles_tipificaciones t 
                  ON xtd.id = t.tipificaciondetalle_id AND t.ejecucionbloquedetalle_id = d.id  
                  LEFT JOIN tbl_tipificaciondetalles xstd ON xtd.subtipificacion_id = xstd.tipificacion_id  
                  LEFT JOIN tbl_ejecucionbloquedetalles_subtipificaciones st 
                  ON xstd.id = st.tipificaciondetalle_id AND t.id =  st.ejecucionbloquedetalles_tipificacion_id";


            $sql .= " WHERE f.arbol_id = " . $this->arbol_id . " AND f.created BETWEEN '" . $startDate . "' AND '" . $endDate . "' 
                  AND xf.id = f.formulario_id AND f.arbol_id = xarbol.id AND xtransacions.id = f.transacion_id 
                  AND xarbol.arbol_id = xarbol_padre.id AND f.dimension_id = xdim.id AND f.evaluado_id = xevaluados.id 
                  AND f.usua_id_lider = xusuarios.usua_id AND f.usua_id = xusuarios2.usua_id AND f.equipo_id = xequipos.id 
                  AND f.usua_id = urol.rel_usua_id AND urol.rel_role_id = rol.role_id AND f.id = s.ejecucionformulario_id 
                  AND xs.id = s.seccion_id AND s.id = b.ejecucionseccion_id AND xb.id = b.bloque_id 
                  AND b.id = d.ejecucionbloque_id AND xd.id = d.bloquedetalle_id AND d.calificaciondetalle_id = xcd.id";

            if ($this->dimension_id != '') {
                $sql .= " AND xdim.id = " . $this->dimension_id;
            }

            $sql .= " ORDER BY f.id, xs.nmorden, xs.id, xb.nmorden, xb.id, xd.nmorden, xd.id, xtd.nmorden, xtd.id, xstd.nmorden, xstd.id ";

            $dataProvider = Yii::$app->get(dbslave)->createCommand($sql)->queryAll();
        }

        $count = count($dataProvider);
        $result = array();
        $result[0] = $dataProvider;
        $result[1] = $count;
        return $result;
    }

    public function extractConsTransORIGINAL() {
        set_time_limit(0);
        $dates = explode(' - ', $this->created);
        $startDate = $dates[0] . " 00:00:00";
        $endDate = $dates[1] . " 23:59:59";

        /* Inicio Variables */
        //INICIO DE TRANSPOSICION DE DATOS
        $textos = $this->getTextosPreguntas();

        $titulos = array();
        // Control del Formulario
        $fid = -1;
        // Control de la seccion
        $sid = -1;
        // Control del bloque
        $did = -1;
        $cdpregunta = -1;
        $cdtipificacion = -1;


        // Variables de control
        $export = false;
        $genTitulos = true;

        /* Archivos */
        $fileName = Yii::$app->basePath . DIRECTORY_SEPARATOR . "web" .
                DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR
                . Yii::t('app', 'Reporte_extractar') . '_' . date('Ymd') . "_" .
                Yii::$app->user->identity->id . ".csv";

        $handleFile = fopen($fileName, 'w');
        /* Titulos */
        $titulos[0] = ['header' => 'Fecha y Hora', 'value' => '0'];
        $titulos[1] = ['header' => 'Dimension', 'value' => '1'];
        $titulos[2] = ['header' => 'Arbol Padre', 'value' => '2'];
        $titulos[3] = ['header' => 'Arbol', 'value' => '3'];
        $titulos[4] = ['header' => 'Formulario', 'value' => '4'];
        $titulos[5] = ['header' => 'Cedula Valorado', 'value' => '5'];
        $titulos[6] = ['header' => 'Valorado', 'value' => '6'];
        $titulos[7] = ['header' => 'Responsable', 'value' => '7'];
        $titulos[8] = ['header' => 'Valorador', 'value' => '8'];
        $titulos[9] = ['header' => 'Rol', 'value' => '9'];
        $titulos[10] = ['header' => 'Fuente', 'value' => '10'];
        $titulos[11] = ['header' => 'Transaccion', 'value' => '11'];
        $titulos[12] = ['header' => 'Equipo', 'value' => '12'];
        $titulos[13] = ['header' => 'Comentario', 'value' => '13'];
        $titulos[14] = ['header' => $textos[0]['titulo'], 'value' => '14'];
        $titulos[15] = ['header' => $textos[1]['titulo'], 'value' => '15'];
        $titulos[16] = ['header' => $textos[2]['titulo'], 'value' => '16'];
        $titulos[17] = ['header' => $textos[3]['titulo'], 'value' => '17'];
        $titulos[18] = ['header' => $textos[4]['titulo'], 'value' => '18'];
        $titulos[19] = ['header' => $textos[5]['titulo'], 'value' => '19'];
        $titulos[20] = ['header' => $textos[6]['titulo'], 'value' => '20'];
        $titulos[21] = ['header' => $textos[7]['titulo'], 'value' => '21'];
        $titulos[22] = ['header' => $textos[8]['titulo'], 'value' => '22'];
        $titulos[23] = ['header' => $textos[9]['titulo'], 'value' => '23'];

        // Generar los tituloos
        $filecontent = "";
        /* foreach ($titulos as $value) {
          $filecontent .= $value['header'] . "|";
          }
          $filecontent .= "\n";
          fwrite($handleFile, $filecontent); */

        //QUERY COMPLETO SIN PARTIR POR LIMITES
        $sql = "SELECT f.created 'Fecha' ,f.id fid ,s.id 'sid' , xb.id 'did', xd.id 'cdPregunta', xd.tipificacion_id 'idTipi', 
                xtd.id 'cdTipificacionDetalle', t.tipificaciondetalle_id, xdim.name 'Dimension', 
                xarbol_padre.name 'ArbolPadre',xarbol.name 'Arbol',xf.name 'Formulario', f.evaluado_id, 
                f.i1_nmcalculo 'fi1_nmcalculo', f.i2_nmcalculo 'fi2_nmcalculo', f.i3_nmcalculo 'fi3_nmcalculo', 
                f.i4_nmcalculo 'fi4_nmcalculo', f.i5_nmcalculo 'fi5_nmcalculo', f.i6_nmcalculo 'fi6_nmcalculo', 
                f.i7_nmcalculo 'fi7_nmcalculo', f.i8_nmcalculo 'fi8_nmcalculo', f.i9_nmcalculo 'fi9_nmcalculo', 
                f.i10_nmcalculo 'fi10_nmcalculo', f.i1_nmfactor 'fi1_nmfactor', f.i2_nmfactor 'fi2_nmfactor', 
                f.i3_nmfactor 'fi3_nmfactor', f.i4_nmfactor 'fi4_nmfactor', f.i5_nmfactor 'fi5_nmfactor', 
                f.i6_nmfactor 'fi6_nmfactor', f.i7_nmfactor 'fi7_nmfactor', f.i8_nmfactor 'fi8_nmfactor', 
                f.i9_nmfactor 'fi9_nmfactor', f.i10_nmfactor 'fi10_nmfactor',
                xs.name 'Seccion',
                s.i1_nmcalculo 'si1_nmcalculo', s.i2_nmcalculo 'si2_nmcalculo', s.i3_nmcalculo 'si3_nmcalculo', 
                s.i4_nmcalculo 'si4_nmcalculo', s.i5_nmcalculo 'si5_nmcalculo', s.i6_nmcalculo 'si6_nmcalculo', 
                s.i7_nmcalculo 'si7_nmcalculo', s.i8_nmcalculo 'si8_nmcalculo', s.i9_nmcalculo 'si9_nmcalculo', 
                s.i10_nmcalculo 'si10_nmcalculo', s.i1_nmfactor 'si1_nmfactor', s.i2_nmfactor 'si2_nmfactor', 
                s.i3_nmfactor 'si3_nmfactor', s.i4_nmfactor 'si4_nmfactor', s.i5_nmfactor 'si5_nmfactor', 
                s.i6_nmfactor 'si6_nmfactor', s.i7_nmfactor 'si7_nmfactor', s.i8_nmfactor 'si8_nmfactor', 
                s.i9_nmfactor 'si9_nmfactor', s.i10_nmfactor 'si10_nmfactor',
                xb.name 'Bloque',
                b.i1_nmcalculo 'bi1_nmcalculo', b.i2_nmcalculo 'bi2_nmcalculo', b.i3_nmcalculo 'bi3_nmcalculo', 
                b.i4_nmcalculo 'bi4_nmcalculo', b.i5_nmcalculo 'bi5_nmcalculo', b.i6_nmcalculo 'bi6_nmcalculo', 
                b.i7_nmcalculo 'bi7_nmcalculo', b.i8_nmcalculo 'bi8_nmcalculo', b.i9_nmcalculo 'bi9_nmcalculo', 
                b.i10_nmcalculo 'bi10_nmcalculo', b.i1_nmfactor 'bi1_nmfactor', b.i2_nmfactor 'bi2_nmfactor', 
                b.i3_nmfactor 'bi3_nmfactor', b.i4_nmfactor 'bi4_nmfactor', b.i5_nmfactor 'bi5_nmfactor', 
                b.i6_nmfactor 'bi6_nmfactor', b.i7_nmfactor 'bi7_nmfactor', b.i8_nmfactor 'bi8_nmfactor', 
                b.i9_nmfactor 'bi9_nmfactor', b.i10_nmfactor 'bi10_nmfactor',
                xd.name 'Pregunta', xcd.name 'Respuesta',
                d.i1_nmcalculo 'di1_nmcalculo', d.i2_nmcalculo 'di2_nmcalculo', d.i3_nmcalculo 'di3_nmcalculo', 
                d.i4_nmcalculo 'di4_nmcalculo', d.i5_nmcalculo 'di5_nmcalculo', d.i6_nmcalculo 'di6_nmcalculo', 
                d.i7_nmcalculo 'di7_nmcalculo', d.i8_nmcalculo 'di8_nmcalculo', d.i9_nmcalculo 'di9_nmcalculo', 
                d.i10_nmcalculo 'di10_nmcalculo', d.i1_nmfactor 'di1_nmfactor', d.i2_nmfactor 'di2_nmfactor', 
                d.i3_nmfactor 'di3_nmfactor', d.i4_nmfactor 'di4_nmfactor', d.i5_nmfactor 'di5_nmfactor', 
                d.i6_nmfactor 'di6_nmfactor', d.i7_nmfactor 'di7_nmfactor', d.i8_nmfactor 'di8_nmfactor', 
                d.i9_nmfactor 'di9_nmfactor', d.i10_nmfactor 'di10_nmfactor',
                xtd.name 'Tipificacion',
                xusuarios.usua_nombre 'responsable', xevaluados.name 'evaluado', 
                xevaluados.identificacion 'cedula_evaluado', 
                xusuarios2.usua_nombre 'evaluador', f.dsfuente_encuesta 'fuente', xequipos.name 'equipo', 
                xtd.subtipificacion_id 'cdSubTipificacionDetalle', xstd.id 'cdsubtipificacion', 
                xstd.name 'subtipificacion', st.id 'IDsubtipificacion', xtransacions.name 'transacion', 
                s.dscomentario 'sdscomentario', f.dscomentario 'fdscomentario', xcd.i1_povalor 'i1_poRespuesta', 
                xcd.i2_povalor 'i2_poRespuesta', xcd.i3_povalor 'i3_poRespuesta', xcd.i4_povalor 'i4_poRespuesta', 
                xcd.i5_povalor 'i5_poRespuesta', xcd.i6_povalor 'i6_poRespuesta', xcd.i7_povalor 'i7_poRespuesta', 
                xcd.i8_povalor 'i8_poRespuesta', xcd.i9_povalor 'i9_poRespuesta', xcd.i10_povalor 'i10_poRespuesta', 
                rol.role_nombre rol";

        $sql .= " FROM (tbl_ejecucionformularios f, tbl_formularios xf, tbl_arbols xarbol,  
                  tbl_usuarios xusuarios, tbl_evaluados xevaluados, tbl_transacions xtransacions,
                  tbl_usuarios xusuarios2, tbl_equipos xequipos, rel_usuarios_roles urol, tbl_roles rol, 
                  tbl_arbols xarbol_padre, tbl_dimensions xdim, tbl_ejecucionseccions s, tbl_seccions xs, 
                  tbl_ejecucionbloques b, tbl_bloques xb, tbl_calificaciondetalles xcd, tbl_bloquedetalles xd, 
                  tbl_ejecucionbloquedetalles d) ";

        $sql .= " LEFT JOIN tbl_tipificaciondetalles xtd ON xd.tipificacion_id = xtd.tipificacion_id 
                  LEFT JOIN tbl_ejecucionbloquedetalles_tipificaciones t 
                  ON xtd.id = t.tipificaciondetalle_id AND t.ejecucionbloquedetalle_id = d.id  
                  LEFT JOIN tbl_tipificaciondetalles xstd ON xtd.subtipificacion_id = xstd.tipificacion_id  
                  LEFT JOIN tbl_ejecucionbloquedetalles_subtipificaciones st 
                  ON xstd.id = st.tipificaciondetalle_id AND t.id =  st.ejecucionbloquedetalles_tipificacion_id";


        $sql .= " WHERE f.arbol_id = " . $this->arbol_id . " AND f.created BETWEEN '" . $startDate . "' AND '" . $endDate . "' 
                  AND xf.id = f.formulario_id AND f.arbol_id = xarbol.id AND xtransacions.id = f.transacion_id 
                  AND xarbol.arbol_id = xarbol_padre.id AND f.dimension_id = xdim.id AND f.evaluado_id = xevaluados.id 
                  AND f.usua_id_lider = xusuarios.usua_id AND f.usua_id = xusuarios2.usua_id AND f.equipo_id = xequipos.id 
                  AND f.usua_id = urol.rel_usua_id AND urol.rel_role_id = rol.role_id AND f.id = s.ejecucionformulario_id 
                  AND xs.id = s.seccion_id AND s.id = b.ejecucionseccion_id AND xb.id = b.bloque_id 
                  AND b.id = d.ejecucionbloque_id AND xd.id = d.bloquedetalle_id AND d.calificaciondetalle_id = xcd.id";

        if ($this->dimension_id != '') {
            $sql .= " AND xdim.id = " . $this->dimension_id;
        }

        $sql .= " ORDER BY f.id, xs.nmorden, xs.id, xb.nmorden, xb.id, xd.nmorden, xd.id, xtd.nmorden, xtd.id, xstd.nmorden, xstd.id ";

        /* ------------------- * -------------------- */
        /* ------------------- * -------------------- */
        /* Ciclo para recuperar por rangos */
        $delta_ciclo = \Yii::$app->params["limitQueryExtractarFormulario"];
        /* ------------------- * -------------------- */
        /* ------------------- * -------------------- */
        $limite_ciclo_inicial = -$delta_ciclo;
        $limite_ciclo_final = $delta_ciclo - 1;
        $newRow = 0;
        $printTitle = true;
        do {
            $data = null;
            $limite_ciclo_inicial += $delta_ciclo;
            $sqlRango = $sql . " LIMIT " . $limite_ciclo_inicial . "," . $limite_ciclo_final . " ";
            $data = Yii::$app->get(dbslave)->createCommand($sqlRango)->queryAll();

            //Codigo nuevo -----------------------------------------------------            
            if (count($data) > 0) {

                foreach ($data as $i => $row) {

                    if ($row['fid'] != $fid) {
                        // Si no es el primer registro se imprime la fila
                        if ($fid != -1) {
                            //CSV PARA MEJORAR EL EXCEL DEL EXTRACTAR
                            $filecontent = "";

                            //MUESTRO LOS ENCABEZADO SOLO UNA VEZ
                            if ($printTitle) {
                                foreach ($titulos as $value) {
                                    $filecontent .= utf8_decode($value['header']) . "|";
                                }
                                $filecontent .= "\n";
                                fwrite($handleFile, $filecontent);
                            }
                            $filecontent = "";
                            $printTitle = false;

                            //IMPRIMO EN EL CSV LOS RESULTADOS QUE VAYAN
                            foreach ($dataProvider as $value) {
                                $tmpCont = implode("|", $value);
                                $filecontent = str_replace(array("\r\n"), ' ', $tmpCont);
                                $filecontent .= "\n";
                                fwrite($handleFile, utf8_decode($filecontent));
                            }
                            // Ya se escribio - Lo puedo liberar
                            $dataProvider = null;
                            $dataProvider = array();
                            $newRow = 0;
                        }

                        $fid = $row['fid'];
                        $sid = -1;
                        $did = -1;
                        $cdpregunta = -1;
                        $cdtipificacion = -1;
                        $newRow++;
                        $iData = 23;

                        $dataProvider[$newRow][0] = $this->vData($data[$i]['Fecha']);
                        $dataProvider[$newRow][1] = $this->vData($data[$i]['Dimension']);
                        $dataProvider[$newRow][2] = $this->vData($data[$i]['ArbolPadre']);
                        $dataProvider[$newRow][3] = $this->vData($data[$i]['Arbol']);
                        $dataProvider[$newRow][4] = $this->vData($data[$i]['Formulario']);
                        $dataProvider[$newRow][5] = $this->vData($data[$i]['cedula_evaluado']);
                        $dataProvider[$newRow][6] = $this->vData($data[$i]['evaluado']);
                        $dataProvider[$newRow][7] = $this->vData($data[$i]['responsable']);
                        $dataProvider[$newRow][8] = $this->vData($data[$i]['evaluador']);
                        $dataProvider[$newRow][9] = $this->vData($data[$i]['rol']);
                        $dataProvider[$newRow][10] = $this->vData($data[$i]['fuente']);
                        $dataProvider[$newRow][11] = $this->vData($data[$i]['transacion']);
                        $dataProvider[$newRow][12] = $this->vData($data[$i]['equipo']);
                        $dataProvider[$newRow][13] = $this->vData($data[$i]['fdscomentario']);
                        $dataProvider[$newRow][14] = $this->vData($data[$i]['fi1_nmcalculo']);
                        $dataProvider[$newRow][15] = $this->vData($data[$i]['fi2_nmcalculo']);
                        $dataProvider[$newRow][16] = $this->vData($data[$i]['fi3_nmcalculo']);
                        $dataProvider[$newRow][17] = $this->vData($data[$i]['fi4_nmcalculo']);
                        $dataProvider[$newRow][18] = $this->vData($data[$i]['fi5_nmcalculo']);
                        $dataProvider[$newRow][19] = $this->vData($data[$i]['fi6_nmcalculo']);
                        $dataProvider[$newRow][20] = $this->vData($data[$i]['fi7_nmcalculo']);
                        $dataProvider[$newRow][21] = $this->vData($data[$i]['fi8_nmcalculo']);
                        $dataProvider[$newRow][22] = $this->vData($data[$i]['fi9_nmcalculo']);
                        $dataProvider[$newRow][23] = $this->vData($data[$i]['fi10_nmcalculo']);
                    }


                    if ($row['sid'] != $sid) {
                        $sid = $row['sid'];
                        $did = -1;

                        $iData++;

                        $titulos[$iData] = ['header' => 'Seccion ' . $this->vData($data[$i]['Seccion']),
                            'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['Seccion']);
                        $iData++;
                        $titulos[$iData] = ['header' => 'Comentario', 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['sdscomentario']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[0]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['si1_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[1]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['si2_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[2]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['si3_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[3]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['si4_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[4]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['si5_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[5]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['si6_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[6]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['si7_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[7]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['si8_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[8]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['si9_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[9]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['si10_nmcalculo']);
                        $iData++;
                    }

                    if ($row['did'] != $did) {
                        $did = $row['did'];
                        $cdpregunta = -1;
                        $titulos[$iData] = ['header' => 'Bloque ' . $this->vData($data[$i]['Bloque']),
                            'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['Bloque']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[0]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['bi1_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[1]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['bi2_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[2]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['bi3_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[3]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['bi4_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[4]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['bi5_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[5]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['bi6_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[6]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['bi7_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[7]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['bi8_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[8]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['bi9_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[9]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['bi10_nmcalculo']);
                        $iData++;
                    }

                    if ($row['cdPregunta'] != $cdpregunta) {
                        $cdpregunta = $row['cdPregunta'];
                        $cdtipificacion = -1;

                        $p = 'P ' . str_replace($this->acentos, $this->sinAcentos, $this->vData($data[$i]['Pregunta']));
                        $titulos[$iData] = ['header' => $p, 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['Pregunta']);
                        $iData++;
                        $r = 'R ' . str_replace($this->acentos, $this->sinAcentos, $this->vData($data[$i]['Pregunta']));
                        $titulos[$iData] = ['header' => $r, 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['Respuesta']);
                        $iData++;
                    }

                    if ($row['idTipi'] != null) {
                        if ($row['cdTipificacionDetalle'] != $cdtipificacion) {
                            $cdtipificacion = $row['cdTipificacionDetalle'];

                            $titulos[$iData] = ['header' => 'TPF ' . $this->vData($data[$i]['Tipificacion']),
                                'value' => '' . $iData . ''];
                            $dataProvider[$newRow][$iData] = $data[$i]['tipificaciondetalle_id'] !=
                                    null ? '1' : '0';
                            $iData++;
                        }

                        if ($row['cdSubTipificacionDetalle'] != null) {
                            $titulos[$iData] = ['header' => 'STPF ' . $this->vData($data[$i]['subtipificacion']),
                                'value' => '' . $iData . ''];
                            $dataProvider[$newRow][$iData] = $data[$i]['IDsubtipificacion'] !=
                                    null ? '1' : '0';
                            $iData++;
                        }
                    }
                } // fin For cada columna del retorno del query

                $export = true;
            } // Fin se hay registros
        } while (count($data) > 0);
        //SI SOLO HABIA UNA VALORACIÓN PINTO LOS TITULOS
        if ($printTitle) {
            foreach ($titulos as $value) {
                $filecontent .= utf8_decode($value['header']) . "|";
            }
            $filecontent .= "\n";
            fwrite($handleFile, $filecontent);
        }
        $filecontent = "";
        $printTitle = false;
        //IMPRIMO EL ULTIMO REGISTRO
        if (isset($dataProvider)) {
            foreach ($dataProvider as $value) {
                $tmpCont = implode("|", $value);
                $filecontent = str_replace(array("\r\n"), ' ', $tmpCont);
                $filecontent .= "\n";
                fwrite($handleFile, $filecontent);
            }
            fclose($handleFile);
        } else {
            $export = false;
        }



        /* $downloadfile = Yii::t('app', 'Reporte_extractar') . '_' . date('Ymd') . ".csv";
          header("Content-Disposition: attachment; filename=" . $downloadfile);
          header("Content-Type: application/force-download");
          header("Content-Transfer-Encoding: binary");
          header("Content-Length: " . strlen($filecontent));
          header("Pragma: no-cache");
          header("Expires: 0");
          echo $filecontent;
          exit; */


        return $export;
    }

    public function extractConsTrans() {
        set_time_limit(0);
        $dates = explode(' - ', $this->created);
        $startDate = $dates[0] . " 00:00:00";
        $endDate = $dates[1] . " 23:59:59";

        /* Inicio Variables */
        //INICIO DE TRANSPOSICION DE DATOS
        $textos = $this->getTextosPreguntas();

        $titulos = array();
        // Control del Formulario
        $fid = -1;
        // Control de la seccion
        $sid = -1;
        // Control del bloque
        $did = -1;
        $cdpregunta = -1;
        $cdtipificacion = -1;


        // Variables de control
        $export = false;
        $genTitulos = true;

        /* Archivos */
        $fileName = Yii::$app->basePath . DIRECTORY_SEPARATOR . "web" .
                DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR
                . Yii::t('app', 'Reporte_extractar') . '_' . date('Ymd') . "_" .
                Yii::$app->user->identity->id . ".xlsx";

        //$handleFile = fopen($fileName, 'w');
        /* Titulos */
        $titulos[0] = ['header' => 'Fecha y Hora', 'value' => '0'];
        $titulos[1] = ['header' => 'Dimension', 'value' => '1'];
        $titulos[2] = ['header' => 'Programa/PCRC Padre', 'value' => '2'];
        $titulos[3] = ['header' => 'Programa/PCRC', 'value' => '3'];
        $titulos[4] = ['header' => 'Formulario', 'value' => '4'];
        $titulos[5] = ['header' => 'Cedula Valorado', 'value' => '5'];
        $titulos[6] = ['header' => 'Valorado', 'value' => '6'];
        $titulos[7] = ['header' => 'Responsable', 'value' => '7'];
        $titulos[8] = ['header' => 'Valorador', 'value' => '8'];
        $titulos[9] = ['header' => 'Rol', 'value' => '9'];
        $titulos[10] = ['header' => 'Fuente', 'value' => '10'];
        $titulos[11] = ['header' => 'Transaccion', 'value' => '11'];
        $titulos[12] = ['header' => 'Equipo', 'value' => '12'];
        $titulos[13] = ['header' => 'Comentario', 'value' => '13'];
        $titulos[14] = ['header' => 'Valoración Adicional y/o Escalada', 'value' => '14'];
        $titulos[15] = ['header' => $textos[0]['titulo'], 'value' => '15'];
        $titulos[16] = ['header' => $textos[1]['titulo'], 'value' => '16'];
        $titulos[17] = ['header' => $textos[2]['titulo'], 'value' => '17'];
        $titulos[18] = ['header' => $textos[3]['titulo'], 'value' => '18'];
        $titulos[19] = ['header' => $textos[4]['titulo'], 'value' => '19'];
        $titulos[20] = ['header' => $textos[5]['titulo'], 'value' => '20'];
        $titulos[21] = ['header' => $textos[6]['titulo'], 'value' => '21'];
        $titulos[22] = ['header' => $textos[7]['titulo'], 'value' => '22'];
        $titulos[23] = ['header' => $textos[8]['titulo'], 'value' => '23'];
        $titulos[24] = ['header' => $textos[9]['titulo'], 'value' => '24'];

        // Generar los tituloos
        $filecontent = "";
        /* foreach ($titulos as $value) {
          $filecontent .= $value['header'] . "|";
          }
          $filecontent .= "\n";
          fwrite($handleFile, $filecontent); */

        //QUERY COMPLETO SIN PARTIR POR LIMITES
        $sql = "SELECT f.created 'Fecha' ,f.id fid ,s.id 'sid' , xb.id 'did', xd.id 'cdPregunta', xd.tipificacion_id 'idTipi', 
                xtd.id 'cdTipificacionDetalle', t.tipificaciondetalle_id, xdim.name 'Dimension', 
                xarbol_padre.name 'ArbolPadre',xarbol.name 'Arbol',xf.name 'Formulario', f.evaluado_id, f.ejec_principal, f.estado ,
                f.i1_nmcalculo 'fi1_nmcalculo', f.i2_nmcalculo 'fi2_nmcalculo', f.i3_nmcalculo 'fi3_nmcalculo', 
                f.i4_nmcalculo 'fi4_nmcalculo', f.i5_nmcalculo 'fi5_nmcalculo', f.i6_nmcalculo 'fi6_nmcalculo', 
                f.i7_nmcalculo 'fi7_nmcalculo', f.i8_nmcalculo 'fi8_nmcalculo', f.i9_nmcalculo 'fi9_nmcalculo', 
                f.i10_nmcalculo 'fi10_nmcalculo', f.i1_nmfactor 'fi1_nmfactor', f.i2_nmfactor 'fi2_nmfactor', 
                f.i3_nmfactor 'fi3_nmfactor', f.i4_nmfactor 'fi4_nmfactor', f.i5_nmfactor 'fi5_nmfactor', 
                f.i6_nmfactor 'fi6_nmfactor', f.i7_nmfactor 'fi7_nmfactor', f.i8_nmfactor 'fi8_nmfactor', 
                f.i9_nmfactor 'fi9_nmfactor', f.i10_nmfactor 'fi10_nmfactor',
                xs.name 'Seccion',
                s.i1_nmcalculo 'si1_nmcalculo', s.i2_nmcalculo 'si2_nmcalculo', s.i3_nmcalculo 'si3_nmcalculo', 
                s.i4_nmcalculo 'si4_nmcalculo', s.i5_nmcalculo 'si5_nmcalculo', s.i6_nmcalculo 'si6_nmcalculo', 
                s.i7_nmcalculo 'si7_nmcalculo', s.i8_nmcalculo 'si8_nmcalculo', s.i9_nmcalculo 'si9_nmcalculo', 
                s.i10_nmcalculo 'si10_nmcalculo', s.i1_nmfactor 'si1_nmfactor', s.i2_nmfactor 'si2_nmfactor', 
                s.i3_nmfactor 'si3_nmfactor', s.i4_nmfactor 'si4_nmfactor', s.i5_nmfactor 'si5_nmfactor', 
                s.i6_nmfactor 'si6_nmfactor', s.i7_nmfactor 'si7_nmfactor', s.i8_nmfactor 'si8_nmfactor', 
                s.i9_nmfactor 'si9_nmfactor', s.i10_nmfactor 'si10_nmfactor',
                xb.name 'Bloque',
                b.i1_nmcalculo 'bi1_nmcalculo', b.i2_nmcalculo 'bi2_nmcalculo', b.i3_nmcalculo 'bi3_nmcalculo', 
                b.i4_nmcalculo 'bi4_nmcalculo', b.i5_nmcalculo 'bi5_nmcalculo', b.i6_nmcalculo 'bi6_nmcalculo', 
                b.i7_nmcalculo 'bi7_nmcalculo', b.i8_nmcalculo 'bi8_nmcalculo', b.i9_nmcalculo 'bi9_nmcalculo', 
                b.i10_nmcalculo 'bi10_nmcalculo', b.i1_nmfactor 'bi1_nmfactor', b.i2_nmfactor 'bi2_nmfactor', 
                b.i3_nmfactor 'bi3_nmfactor', b.i4_nmfactor 'bi4_nmfactor', b.i5_nmfactor 'bi5_nmfactor', 
                b.i6_nmfactor 'bi6_nmfactor', b.i7_nmfactor 'bi7_nmfactor', b.i8_nmfactor 'bi8_nmfactor', 
                b.i9_nmfactor 'bi9_nmfactor', b.i10_nmfactor 'bi10_nmfactor',
                xd.name 'Pregunta', xcd.name 'Respuesta',
                d.i1_nmcalculo 'di1_nmcalculo', d.i2_nmcalculo 'di2_nmcalculo', d.i3_nmcalculo 'di3_nmcalculo', 
                d.i4_nmcalculo 'di4_nmcalculo', d.i5_nmcalculo 'di5_nmcalculo', d.i6_nmcalculo 'di6_nmcalculo', 
                d.i7_nmcalculo 'di7_nmcalculo', d.i8_nmcalculo 'di8_nmcalculo', d.i9_nmcalculo 'di9_nmcalculo', 
                d.i10_nmcalculo 'di10_nmcalculo', d.i1_nmfactor 'di1_nmfactor', d.i2_nmfactor 'di2_nmfactor', 
                d.i3_nmfactor 'di3_nmfactor', d.i4_nmfactor 'di4_nmfactor', d.i5_nmfactor 'di5_nmfactor', 
                d.i6_nmfactor 'di6_nmfactor', d.i7_nmfactor 'di7_nmfactor', d.i8_nmfactor 'di8_nmfactor', 
                d.i9_nmfactor 'di9_nmfactor', d.i10_nmfactor 'di10_nmfactor',
                xtd.name 'Tipificacion',
                xusuarios.usua_nombre 'responsable', xevaluados.name 'evaluado', 
                xevaluados.identificacion 'cedula_evaluado', 
                xusuarios2.usua_nombre 'evaluador', f.dsfuente_encuesta 'fuente', xequipos.name 'equipo', 
                xtd.subtipificacion_id 'cdSubTipificacionDetalle', xstd.id 'cdsubtipificacion', 
                xstd.name 'subtipificacion', st.id 'IDsubtipificacion', xtransacions.name 'transacion', 
                s.dscomentario 'sdscomentario', f.dscomentario 'fdscomentario', xcd.i1_povalor 'i1_poRespuesta', 
                xcd.i2_povalor 'i2_poRespuesta', xcd.i3_povalor 'i3_poRespuesta', xcd.i4_povalor 'i4_poRespuesta', 
                xcd.i5_povalor 'i5_poRespuesta', xcd.i6_povalor 'i6_poRespuesta', xcd.i7_povalor 'i7_poRespuesta', 
                xcd.i8_povalor 'i8_poRespuesta', xcd.i9_povalor 'i9_poRespuesta', xcd.i10_povalor 'i10_poRespuesta', 
                rol.role_nombre rol";

        $sql .= " FROM (tbl_ejecucionformularios f, tbl_formularios xf, tbl_arbols xarbol,  
                  tbl_usuarios xusuarios, tbl_evaluados xevaluados, tbl_transacions xtransacions,
                  tbl_usuarios xusuarios2, tbl_equipos xequipos, rel_usuarios_roles urol, tbl_roles rol, 
                  tbl_arbols xarbol_padre, tbl_dimensions xdim, tbl_ejecucionseccions s, tbl_seccions xs, 
                  tbl_ejecucionbloques b, tbl_bloques xb, tbl_calificaciondetalles xcd, tbl_bloquedetalles xd, 
                  tbl_ejecucionbloquedetalles d) ";

        $sql .= " LEFT JOIN tbl_tipificaciondetalles xtd ON xd.tipificacion_id = xtd.tipificacion_id AND xtd.`snen_uso` = 1 
                  LEFT JOIN tbl_ejecucionbloquedetalles_tipificaciones t 
                  ON xtd.id = t.tipificaciondetalle_id AND t.ejecucionbloquedetalle_id = d.id  
                  LEFT JOIN tbl_tipificaciondetalles xstd ON xtd.subtipificacion_id = xstd.tipificacion_id  AND xstd.`snen_uso` = 1 
                  LEFT JOIN tbl_ejecucionbloquedetalles_subtipificaciones st 
                  ON xstd.id = st.tipificaciondetalle_id AND t.id =  st.ejecucionbloquedetalles_tipificacion_id";


        $sql .= " WHERE f.arbol_id = " . $this->arbol_id . " AND f.created BETWEEN '" . $startDate . "' AND '" . $endDate . "' 
                  AND xf.id = f.formulario_id AND f.arbol_id = xarbol.id AND xtransacions.id = f.transacion_id 
                  AND xarbol.arbol_id = xarbol_padre.id AND f.dimension_id = xdim.id AND f.evaluado_id = xevaluados.id 
                  AND f.usua_id_lider = xusuarios.usua_id AND f.usua_id = xusuarios2.usua_id AND f.equipo_id = xequipos.id 
                  AND f.usua_id = urol.rel_usua_id AND urol.rel_role_id = rol.role_id AND f.id = s.ejecucionformulario_id 
                  AND xs.id = s.seccion_id AND s.id = b.ejecucionseccion_id AND xb.id = b.bloque_id 
                  AND b.id = d.ejecucionbloque_id AND xd.id = d.bloquedetalle_id AND d.calificaciondetalle_id = xcd.id";

        if ($this->dimension_id != '') {
            $sql .= " AND xdim.id = " . $this->dimension_id;
        }

        $sql .= " ORDER BY f.id, xs.nmorden, xs.id, xb.nmorden, xb.id, xd.nmorden, xd.id, xtd.nmorden, xtd.id, xstd.nmorden, xstd.id ";

        /* ------------------- * -------------------- */
        /* ------------------- * -------------------- */
        /* Ciclo para recuperar por rangos */
        $delta_ciclo = \Yii::$app->params["limitQueryExtractarFormulario"];
        /* ------------------- * -------------------- */
        /* ------------------- * -------------------- */
        $limite_ciclo_inicial = -$delta_ciclo;
        $limite_ciclo_final = $delta_ciclo - 1;
        $newRow = 0;
        $printTitle = true;
        $fila = 2;
        $objPHPexcel = new \PHPExcel();
        $objPHPexcel->setActiveSheetIndex(0);
        do {
            $data = null;
            $limite_ciclo_inicial += $delta_ciclo;
            $sqlRango = $sql . " LIMIT " . $limite_ciclo_inicial . "," . $limite_ciclo_final . " ";
            $data = Yii::$app->get(dbslave)->createCommand($sqlRango)->queryAll();

            //Codigo nuevo -----------------------------------------------------            
            if (count($data) > 0) {

                foreach ($data as $i => $row) {

                    if ($row['fid'] != $fid) {
                        // Si no es el primer registro se imprime la fila
                        if ($fid != -1) {
                            //CSV PARA MEJORAR EL EXCEL DEL EXTRACTAR
                            $filecontent = "";

                            //MUESTRO LOS ENCABEZADO SOLO UNA VEZ
                            /*
                              if ($printTitle) {
                              foreach ($titulos as $value) {
                              $filecontent .= utf8_decode($value['header']) . "|";
                              }
                              //$filecontent .= "\n";
                              //fwrite($handleFile, $filecontent);
                              } */
                            $filecontent = "";
                            $printTitle = false;

                            //IMPRIMO EN EL CSV LOS RESULTADOS QUE VAYAN
                            foreach ($dataProvider as $value) {
                                $tmpCont = implode("|", $value);
                                $filecontent = str_replace(array("\r\n"), ' ', $tmpCont);
                                $objPHPexcel->getActiveSheet()->setCellValue('A' . $fila, $filecontent);
                                //$filecontent .= "\n";
                                //fwrite($handleFile, utf8_decode($filecontent));
                                $fila++;
                            }
                            // Ya se escribio - Lo puedo liberar
                            $dataProvider = null;
                            $dataProvider = array();
                            $newRow = 0;
                        }

                        $fid = $row['fid'];
                        $sid = -1;
                        $did = -1;
                        $cdpregunta = -1;
                        $cdtipificacion = -1;
                        $newRow++;
                        $iData = 23;

                        $dataProvider[$newRow][0] = $this->vData($data[$i]['Fecha']);
                        $dataProvider[$newRow][1] = $this->vData($data[$i]['Dimension']);
                        $dataProvider[$newRow][2] = $this->vData($data[$i]['ArbolPadre']);
                        $dataProvider[$newRow][3] = $this->vData($data[$i]['Arbol']);
                        $dataProvider[$newRow][4] = $this->vData($data[$i]['Formulario']);
                        $dataProvider[$newRow][5] = $this->vData($data[$i]['cedula_evaluado']);
                        $dataProvider[$newRow][6] = $this->vData($data[$i]['evaluado']);
                        $dataProvider[$newRow][7] = $this->vData($data[$i]['responsable']);
                        $dataProvider[$newRow][8] = $this->vData($data[$i]['evaluador']);
                        $dataProvider[$newRow][9] = $this->vData($data[$i]['rol']);
                        $dataProvider[$newRow][10] = $this->vData($data[$i]['fuente']);
                        $dataProvider[$newRow][11] = $this->vData($data[$i]['transacion']);
                        $dataProvider[$newRow][12] = $this->vData($data[$i]['equipo']);
                        $dataProvider[$newRow][13] = $this->vData($data[$i]['fdscomentario']);
                        if ($data[$i]['ejec_principal'] != '' && $data[$i]['estado'] != '') {
                            $dataProvider[$newRow][14] ='Valoración Escalada';
                        } else {
                            if ($data[$i]['ejec_principal'] != '') {
                                $dataProvider[$newRow][14] ='Valoración Adicional, Id valoración principal:' . $data[$i]['ejec_principal'];
                            }else{
                                $dataProvider[$newRow][14] = 'N/A';
                            }
                        }
                        $dataProvider[$newRow][15] = $this->vData($data[$i]['fi1_nmcalculo']);
                        $dataProvider[$newRow][16] = $this->vData($data[$i]['fi2_nmcalculo']);
                        $dataProvider[$newRow][17] = $this->vData($data[$i]['fi3_nmcalculo']);
                        $dataProvider[$newRow][18] = $this->vData($data[$i]['fi4_nmcalculo']);
                        $dataProvider[$newRow][19] = $this->vData($data[$i]['fi5_nmcalculo']);
                        $dataProvider[$newRow][20] = $this->vData($data[$i]['fi6_nmcalculo']);
                        $dataProvider[$newRow][21] = $this->vData($data[$i]['fi7_nmcalculo']);
                        $dataProvider[$newRow][22] = $this->vData($data[$i]['fi8_nmcalculo']);
                        $dataProvider[$newRow][23] = $this->vData($data[$i]['fi9_nmcalculo']);
                        $dataProvider[$newRow][24] = $this->vData($data[$i]['fi10_nmcalculo']);
                    }


                    if ($row['sid'] != $sid) {
                        $sid = $row['sid'];
                        $did = -1;

                        $iData++;

                        $titulos[$iData] = ['header' => 'Seccion ' . $this->vData($data[$i]['Seccion']),
                            'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['Seccion']);
                        $iData++;
                        $titulos[$iData] = ['header' => 'Comentario', 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['sdscomentario']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[0]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['si1_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[1]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['si2_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[2]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['si3_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[3]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['si4_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[4]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['si5_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[5]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['si6_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[6]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['si7_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[7]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['si8_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[8]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['si9_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[9]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['si10_nmcalculo']);
                        $iData++;
                    }

                    if ($row['did'] != $did) {
                        $did = $row['did'];
                        $cdpregunta = -1;
                        $titulos[$iData] = ['header' => 'Bloque ' . $this->vData($data[$i]['Bloque']),
                            'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['Bloque']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[0]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['bi1_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[1]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['bi2_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[2]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['bi3_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[3]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['bi4_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[4]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['bi5_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[5]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['bi6_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[6]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['bi7_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[7]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['bi8_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[8]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['bi9_nmcalculo']);
                        $iData++;
                        $titulos[$iData] = ['header' => $textos[9]['titulo'], 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['bi10_nmcalculo']);
                        $iData++;
                    }

                    if ($row['cdPregunta'] != $cdpregunta) {
                        $cdpregunta = $row['cdPregunta'];
                        $cdtipificacion = -1;

                        $p = 'P ' . str_replace($this->acentos, $this->sinAcentos, $this->vData($data[$i]['Pregunta']));
                        $titulos[$iData] = ['header' => $p, 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['Pregunta']);
                        $iData++;
                        $r = 'R ' . str_replace($this->acentos, $this->sinAcentos, $this->vData($data[$i]['Pregunta']));
                        $titulos[$iData] = ['header' => $r, 'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $this->vData($data[$i]['Respuesta']);
                        $iData++;
                    }

                    if ($row['idTipi'] != null) {
                        if ($row['cdTipificacionDetalle'] != $cdtipificacion) {
                            $cdtipificacion = $row['cdTipificacionDetalle'];

                            $titulos[$iData] = ['header' => 'TPF ' . $this->vData($data[$i]['Tipificacion']),
                                'value' => '' . $iData . ''];
                            $dataProvider[$newRow][$iData] = $data[$i]['tipificaciondetalle_id'] !=
                                    null ? '1' : '0';
                            $iData++;
                        }

                        if ($row['cdSubTipificacionDetalle'] != null) {
                            $titulos[$iData] = ['header' => 'STPF ' . $this->vData($data[$i]['subtipificacion']),
                                'value' => '' . $iData . ''];
                            $dataProvider[$newRow][$iData] = $data[$i]['IDsubtipificacion'] !=
                                    null ? '1' : '0';
                            $iData++;
                        }
                    }
                } // fin For cada columna del retorno del query

                $export = true;
            } // Fin se hay registros
        } while (count($data) > 0);
        //SI SOLO HABIA UNA VALORACIÓN PINTO LOS TITULOS
        /*
          if ($printTitle) {
          foreach ($titulos as $value) {
          $filecontent .= utf8_decode($value['header']) . "|";
          }
          //$filecontent .= "\n";
          //fwrite($handleFile, $filecontent);
          } */
        $filecontent = "";
        $printTitle = false;
        //IMPRIMO EL ULTIMO REGISTRO
        if (isset($dataProvider)) {
            foreach ($dataProvider as $value) {
                $tmpCont = implode("|", $value);
                $filecontent = str_replace(array("\r\n"), ' ', $tmpCont);
                $objPHPexcel->getActiveSheet()->setCellValue('A' . $fila, $filecontent);
                //$filecontent .= "\n";
                //fwrite($handleFile, $filecontent);
                $fila++;
            }
            //fclose($handleFile);
        } else {
            $export = false;
        }
        $arrayTitulos = [];

        $column = 'A';
        foreach ($titulos as $key => $value) {
            $arrayTitulos[] = $value['header'];
        }
        for ($index = 0; $index < count($arrayTitulos); $index++) {
            $objPHPexcel->getActiveSheet()->setCellValue($column . '1', $arrayTitulos[$index]);
            $column++;
        }
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPexcel);
        $objWriter->save($fileName);


        /* $downloadfile = Yii::t('app', 'Reporte_extractar') . '_' . date('Ymd') . ".csv";
          header("Content-Disposition: attachment; filename=" . $downloadfile);
          header("Content-Type: application/force-download");
          header("Content-Transfer-Encoding: binary");
          header("Content-Length: " . strlen($filecontent));
          header("Pragma: no-cache");
          header("Expires: 0");
          echo $filecontent;
          exit; */


        return $export;
    }

    /**
     * 
     * @return array
     * @author Daniel Enciso <daniel.enciso@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getTextosPreguntas() {

        $sql = " SELECT t.id, t.detexto as 'titulo' FROM tbl_textos t";
        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    /**
     * 
     * @return array
     * @author Felipe echeverri <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getArbolesByRoles() {

        $rol = Yii::$app->user->identity->rolId;

        return ArrayHelper::map(Arboles::find()
                                ->joinWith('permisosGruposArbols')
                                ->where([
                                    "sncrear_formulario" => 1,
                                    "snhoja" => 1,
                                    "role_id" => $rol])
                                ->andWhere(['not', ['formulario_id' => null]])
                                ->orderBy("dsorden ASC")
                                ->all(), 'id', 'dsname_full');
    }

    /**
     * 
     * @return array
     * @author Felipe echeverri <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getArbolesByPermisos() {

        $rol = Yii::$app->user->identity->rolId;

        return ArrayHelper::map(Arboles::find()
                                ->joinWith('permisosGruposArbols')
                                ->where([
                                    "sncrear_formulario" => 1,
                                    "snhoja" => 1,
                                    "role_id" => $rol,
                                    "snver_grafica" => 1])
                                ->andWhere(['not', ['formulario_id' => null]])
                                ->orderBy("dsorden ASC")
                                ->all(), 'id', 'dsname_full');
    }

    /**
     * Metodo que permite generar la consulta para mostrar el reporte de experiencias
     *
     * @return array
     * @author Sebastian Orozco  <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public static function reporteExperiencias($arbol, $feini, $fefin, $tipo, $bandera) {

        try {

            if ($arbol != "") {
                $arbol_where = " AND ej.`arbol_id`='$arbol' ";
            } else {
                $arbol_where = "";
            }
            switch ($tipo) {
                case '1':
                    $sql = "SELECT  IFNULL(ej.created,'') as 'fecha', IFNULL(t.dsenfoque,'') as 'enfoque', IFNULL(t.dsproblema,'') as 'problema', IFNULL(t.detalle,'') as 'detalle'
                    FROM `tbl_ejecucionformularios` ej 
                    INNER JOIN tbl_ejecuciontableroexperiencias t ON ej.id = t.ejecucionformulario_id
                    WHERE DATE(ej.`created`)<='$fefin' AND 
                    DATE(ej.`created`)>='$feini' $arbol_where 
                    ORDER BY ej.created DESC";
                    break;
                case '2':
                    $sql = "SELECT  IFNULL(MONTHNAME(`created`),'') as 'mes', IFNULL(e.name,'') as 'nombre', IFNULL(COUNT(*),0) as 'Cantidad' 
                    FROM `tbl_ejecucionformularios` ej, `tbl_ejecuciontableroexperiencias` et, `tbl_tableroenfoques` e  
                    WHERE ej.id = et.`ejecucionformulario_id` AND et.`tableroenfoque_id` =  e.id $arbol_where AND DATE(ej.`created`)>='$feini' AND DATE(ej.`created`)<='$fefin' GROUP BY 1,2 
                    ORDER BY ej.created DESC";
                    break;
                case '3':
                    $sql = "SELECT   IFNULL(problemas.mes,'') as 'mes', IFNULL(problemas.pname,'') as 'nombre_problema', IFNULL(problemas.ename,'') as 'nombre_enfoque', IFNULL(problemas.cuenta,'') as 'cantidad_problemas', IFNULL(monitoreos.cuenta,'') as 'cantidad_monitoreos', ROUND(IFNULL((problemas.cuenta / monitoreos.cuenta)*100,0),2) as 'peso_problema_(%)' 
                    FROM  
                    ( SELECT MONTHNAME(`created`) mes, p.name pname, enf.name ename, COUNT(*) cuenta FROM `tbl_ejecucionformularios` ej, `tbl_ejecuciontableroexperiencias` et, `tbl_tableroproblemadetalles` e, `tbl_tableroenfoques` enf, `tbl_tableroproblemas` p   WHERE ej.id = et.`ejecucionformulario_id` AND et.`tableroproblemadetalle_id` =  e.id AND e.`tableroproblema_id` = p.id AND e.`tableroenfoque_id` = enf.`id` $arbol_where AND DATE(ej.`created`)>='$feini' AND DATE(ej.`created`)<='$fefin'  
                         GROUP BY 1,2,3) problemas,    ( SELECT MONTHNAME(`created`) mes , COUNT(*) cuenta FROM `tbl_ejecucionformularios` ej WHERE DATE(ej.`created`)>='$feini' AND DATE(ej.`created`)<='$fefin' $arbol_where GROUP BY 1) monitoreos  
                    WHERE  monitoreos.mes = problemas.mes 
                    ORDER BY monitoreos.mes DESC";
                    break;
                default:
                    $sql = "SELECT  IFNULL(ej.created,'') as 'fecha', IFNULL(t.dsenfoque,'') as 'enfoque', IFNULL(t.dsproblema,'') as 'problema', IFNULL(t.detalle,'') as 'detalle'
                    FROM `tbl_ejecucionformularios` ej 
                    INNER JOIN tbl_ejecuciontableroexperiencias t ON ej.id = t.ejecucionformulario_id
                    WHERE DATE(ej.`created`)<='$fefin' AND 
                    DATE(ej.`created`)>='$feini' $arbol_where 
                    ORDER BY ej.created DESC";
                    break;
            }


            $count = Yii::$app->db->createCommand($sql)->queryAll();


            if ($bandera) {
                $dataProvider = new SqlDataProvider([
                    'sql' => $sql,
                    'totalCount' => count($count),
                    'pagination' => ['pageSize' => 20,],
                ]);
            } else {
                $dataProvider = new SqlDataProvider([
                    'sql' => $sql,
                    'pagination' => ['pageSize' => count($count),],
                ]);
            }


            return $dataProvider;
        } catch (Exception $e) {
            \Yii::error($e->getMessage(), 'exception');
        }
    }

    /**
     * Metodo borra formularios
     * 
     * @param int    $id   Id del formulario
     * 
     * @return boolean
     * @author Felipe Echevereii <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function borrarForm($id) {
        try {
            $sql = 'CALL sp_formulario_borrar("' . $id . '")';
            $command = \Yii::$app->db->createCommand($sql);
            $command->execute();
            return true;
        } catch (Exception $exc) {
            \Yii::error($exc->getMessage(), 'exception');
            return false;
        }
    }

    /**
     * Metodo para crear un TMP
     * 
     * @param int $id
     * @param int $usua_id
     * @return mixed
     * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public static function llevarATmp($id, $usua_id) {
        $sql = "SELECT f_formulario_ejecucion2tmp('" . $id . "','" . $usua_id . "') as tmp_id";
        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    /**
     * Si viene algún dato retorna el dato, de lo contrario retorne " - "
     * 
     * @return string
     */
    public function vData($data) {
        if ($data == null) {
            return " - ";
        } else {
            return $data;
        }
    }

    /**
     * Metodo para etractar y ordenar los formularios
     * 
     * @return \app\models\ArrayDataProvider
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function extrctarFormulario() {
        $data = $this->getReporteExctractarFormularios();
        $textos = $this->getTextosPreguntas();
        $dataProvider = array();
        $titulos = array();

        //Codigo nuevo -----------------------------------------------------
        if (count($data[0]) > 0) {
            $titulos[0] = ['header' => 'Fecha y Hora', 'value' => '0'];
            $titulos[1] = ['header' => 'Dimension', 'value' => '1'];
            $titulos[2] = ['header' => 'Programa/PCRC Padre', 'value' => '2'];
            $titulos[3] = ['header' => 'Programa/PCRC', 'value' => '3'];
            $titulos[4] = ['header' => 'Formulario', 'value' => '4'];
            $titulos[5] = ['header' => 'Cedula Valorado', 'value' => '5'];
            $titulos[6] = ['header' => 'Valorado', 'value' => '6'];
            $titulos[7] = ['header' => 'Responsable', 'value' => '7'];
            $titulos[8] = ['header' => 'Valorador', 'value' => '8'];
            $titulos[9] = ['header' => 'Rol', 'value' => '9'];
            $titulos[10] = ['header' => 'Fuente', 'value' => '10'];
            $titulos[11] = ['header' => 'Transaccion', 'value' => '11'];
            $titulos[12] = ['header' => 'Equipo', 'value' => '12'];
            $titulos[13] = ['header' => 'Comentario', 'value' => '13'];
            $titulos[14] = ['header' => $textos[0]['titulo'], 'value' => '14'];
            $titulos[15] = ['header' => $textos[1]['titulo'], 'value' => '15'];
            $titulos[16] = ['header' => $textos[2]['titulo'], 'value' => '16'];
            $titulos[17] = ['header' => $textos[3]['titulo'], 'value' => '17'];
            $titulos[18] = ['header' => $textos[4]['titulo'], 'value' => '18'];
            $titulos[19] = ['header' => $textos[5]['titulo'], 'value' => '19'];
            $titulos[20] = ['header' => $textos[6]['titulo'], 'value' => '20'];
            $titulos[21] = ['header' => $textos[7]['titulo'], 'value' => '21'];
            $titulos[22] = ['header' => $textos[8]['titulo'], 'value' => '22'];
            $titulos[23] = ['header' => $textos[9]['titulo'], 'value' => '23'];

            // Control del Formulario
            $fid = -1;
            // Control de la seccion
            $sid = -1;
            // Control del bloque
            $did = -1;
            $cdpregunta = -1;
            $cdtipificacion = -1;
            $newRow = 1;

            foreach ($data[0] as $i => $row) {
                if ($row['fid'] != $fid) {
                    $fid = $row['fid'];
                    $sid = -1;
                    $did = -1;
                    $cdpregunta = -1;
                    $cdtipificacion = -1;
                    $newRow++;
                    $iData = 23;

                    $dataProvider[$newRow][0] = $this->vData($data[0][$i]['Fecha']);
                    $dataProvider[$newRow][1] = $this->vData($data[0][$i]['Dimension']);
                    $dataProvider[$newRow][2] = $this->vData($data[0][$i]['ArbolPadre']);
                    $dataProvider[$newRow][3] = $this->vData($data[0][$i]['Arbol']);
                    $dataProvider[$newRow][4] = $this->vData($data[0][$i]['Formulario']);
                    $dataProvider[$newRow][5] = $this->vData($data[0][$i]['cedula_evaluado']);
                    $dataProvider[$newRow][6] = $this->vData($data[0][$i]['evaluado']);
                    $dataProvider[$newRow][7] = $this->vData($data[0][$i]['responsable']);
                    $dataProvider[$newRow][8] = $this->vData($data[0][$i]['evaluador']);
                    $dataProvider[$newRow][9] = $this->vData($data[0][$i]['rol']);
                    $dataProvider[$newRow][10] = $this->vData($data[0][$i]['fuente']);
                    $dataProvider[$newRow][11] = $this->vData($data[0][$i]['transacion']);
                    $dataProvider[$newRow][12] = $this->vData($data[0][$i]['equipo']);
                    $dataProvider[$newRow][13] = $this->vData($data[0][$i]['fdscomentario']);
                    $dataProvider[$newRow][14] = $this->vData($data[0][$i]['fi1_nmcalculo']);
                    $dataProvider[$newRow][15] = $this->vData($data[0][$i]['fi2_nmcalculo']);
                    $dataProvider[$newRow][16] = $this->vData($data[0][$i]['fi3_nmcalculo']);
                    $dataProvider[$newRow][17] = $this->vData($data[0][$i]['fi4_nmcalculo']);
                    $dataProvider[$newRow][18] = $this->vData($data[0][$i]['fi5_nmcalculo']);
                    $dataProvider[$newRow][19] = $this->vData($data[0][$i]['fi6_nmcalculo']);
                    $dataProvider[$newRow][20] = $this->vData($data[0][$i]['fi7_nmcalculo']);
                    $dataProvider[$newRow][21] = $this->vData($data[0][$i]['fi8_nmcalculo']);
                    $dataProvider[$newRow][22] = $this->vData($data[0][$i]['fi9_nmcalculo']);
                    $dataProvider[$newRow][23] = $this->vData($data[0][$i]['fi10_nmcalculo']);
                }


                if ($row['sid'] != $sid) {
                    $sid = $row['sid'];
                    $did = -1;

                    $iData++;

                    $titulos[$iData] = ['header' => 'Seccion ' . $this->vData($data[0][$i]['Seccion']),
                        'value' => '' . $iData . ''];
                    $dataProvider[$newRow][$iData] = $this->vData($data[0][$i]['Seccion']);
                    $iData++;
                    $titulos[$iData] = ['header' => 'Comentario', 'value' => '' . $iData . ''];
                    $dataProvider[$newRow][$iData] = $this->vData($data[0][$i]['sdscomentario']);
                    $iData++;
                    $titulos[$iData] = ['header' => $textos[0]['titulo'], 'value' => '' . $iData . ''];
                    $dataProvider[$newRow][$iData] = $this->vData($data[0][$i]['si1_nmcalculo']);
                    $iData++;
                    $titulos[$iData] = ['header' => $textos[1]['titulo'], 'value' => '' . $iData . ''];
                    $dataProvider[$newRow][$iData] = $this->vData($data[0][$i]['si2_nmcalculo']);
                    $iData++;
                    $titulos[$iData] = ['header' => $textos[2]['titulo'], 'value' => '' . $iData . ''];
                    $dataProvider[$newRow][$iData] = $this->vData($data[0][$i]['si3_nmcalculo']);
                    $iData++;
                    $titulos[$iData] = ['header' => $textos[3]['titulo'], 'value' => '' . $iData . ''];
                    $dataProvider[$newRow][$iData] = $this->vData($data[0][$i]['si4_nmcalculo']);
                    $iData++;
                    $titulos[$iData] = ['header' => $textos[4]['titulo'], 'value' => '' . $iData . ''];
                    $dataProvider[$newRow][$iData] = $this->vData($data[0][$i]['si5_nmcalculo']);
                    $iData++;
                    $titulos[$iData] = ['header' => $textos[5]['titulo'], 'value' => '' . $iData . ''];
                    $dataProvider[$newRow][$iData] = $this->vData($data[0][$i]['si6_nmcalculo']);
                    $iData++;
                    $titulos[$iData] = ['header' => $textos[6]['titulo'], 'value' => '' . $iData . ''];
                    $dataProvider[$newRow][$iData] = $this->vData($data[0][$i]['si7_nmcalculo']);
                    $iData++;
                    $titulos[$iData] = ['header' => $textos[7]['titulo'], 'value' => '' . $iData . ''];
                    $dataProvider[$newRow][$iData] = $this->vData($data[0][$i]['si8_nmcalculo']);
                    $iData++;
                    $titulos[$iData] = ['header' => $textos[8]['titulo'], 'value' => '' . $iData . ''];
                    $dataProvider[$newRow][$iData] = $this->vData($data[0][$i]['si9_nmcalculo']);
                    $iData++;
                    $titulos[$iData] = ['header' => $textos[9]['titulo'], 'value' => '' . $iData . ''];
                    $dataProvider[$newRow][$iData] = $this->vData($data[0][$i]['si10_nmcalculo']);
                    $iData++;
                }

                if ($row['did'] != $did) {
                    $did = $row['did'];
                    $cdpregunta = -1;
                    $titulos[$iData] = ['header' => 'Bloque ' . $this->vData($data[0][$i]['Bloque']),
                        'value' => '' . $iData . ''];
                    $dataProvider[$newRow][$iData] = $this->vData($data[0][$i]['Bloque']);
                    $iData++;
                    $titulos[$iData] = ['header' => $textos[0]['titulo'], 'value' => '' . $iData . ''];
                    $dataProvider[$newRow][$iData] = $this->vData($data[0][$i]['bi1_nmcalculo']);
                    $iData++;
                    $titulos[$iData] = ['header' => $textos[1]['titulo'], 'value' => '' . $iData . ''];
                    $dataProvider[$newRow][$iData] = $this->vData($data[0][$i]['bi2_nmcalculo']);
                    $iData++;
                    $titulos[$iData] = ['header' => $textos[2]['titulo'], 'value' => '' . $iData . ''];
                    $dataProvider[$newRow][$iData] = $this->vData($data[0][$i]['bi3_nmcalculo']);
                    $iData++;
                    $titulos[$iData] = ['header' => $textos[3]['titulo'], 'value' => '' . $iData . ''];
                    $dataProvider[$newRow][$iData] = $this->vData($data[0][$i]['bi4_nmcalculo']);
                    $iData++;
                    $titulos[$iData] = ['header' => $textos[4]['titulo'], 'value' => '' . $iData . ''];
                    $dataProvider[$newRow][$iData] = $this->vData($data[0][$i]['bi5_nmcalculo']);
                    $iData++;
                    $titulos[$iData] = ['header' => $textos[5]['titulo'], 'value' => '' . $iData . ''];
                    $dataProvider[$newRow][$iData] = $this->vData($data[0][$i]['bi6_nmcalculo']);
                    $iData++;
                    $titulos[$iData] = ['header' => $textos[6]['titulo'], 'value' => '' . $iData . ''];
                    $dataProvider[$newRow][$iData] = $this->vData($data[0][$i]['bi7_nmcalculo']);
                    $iData++;
                    $titulos[$iData] = ['header' => $textos[7]['titulo'], 'value' => '' . $iData . ''];
                    $dataProvider[$newRow][$iData] = $this->vData($data[0][$i]['bi8_nmcalculo']);
                    $iData++;
                    $titulos[$iData] = ['header' => $textos[8]['titulo'], 'value' => '' . $iData . ''];
                    $dataProvider[$newRow][$iData] = $this->vData($data[0][$i]['bi9_nmcalculo']);
                    $iData++;
                    $titulos[$iData] = ['header' => $textos[9]['titulo'], 'value' => '' . $iData . ''];
                    $dataProvider[$newRow][$iData] = $this->vData($data[0][$i]['bi10_nmcalculo']);
                    $iData++;
                }

                if ($row['cdPregunta'] != $cdpregunta) {
                    $cdpregunta = $row['cdPregunta'];
                    $cdtipificacion = -1;

                    $p = 'P ' . str_replace($this->acentos, $this->sinAcentos, $this->vData($data[0][$i]['Pregunta']));
                    $titulos[$iData] = ['header' => $p, 'value' => '' . $iData . ''];
                    $dataProvider[$newRow][$iData] = $this->vData($data[0][$i]['Pregunta']);
                    $iData++;
                    $r = 'R ' . str_replace($this->acentos, $this->sinAcentos, $this->vData($data[0][$i]['Pregunta']));
                    $titulos[$iData] = ['header' => $r, 'value' => '' . $iData . ''];
                    $dataProvider[$newRow][$iData] = $this->vData($data[0][$i]['Respuesta']);
                    $iData++;
                }

                if ($row['idTipi'] != null) {
                    if ($row['cdTipificacionDetalle'] != $cdtipificacion) {
                        $cdtipificacion = $row['cdTipificacionDetalle'];

                        $titulos[$iData] = ['header' => 'TPF ' . $this->vData($data[0][$i]['Tipificacion']),
                            'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $data[0][$i]['tipificaciondetalle_id'] !=
                                null ? '1' : '0';
                        $iData++;
                    }

                    if ($row['cdSubTipificacionDetalle'] != null) {
                        $titulos[$iData] = ['header' => 'STPF ' . $this->vData($data[0][$i]['subtipificacion']),
                            'value' => '' . $iData . ''];
                        $dataProvider[$newRow][$iData] = $data[0][$i]['IDsubtipificacion'] !=
                                null ? '1' : '0';
                        $iData++;
                    }
                }
            }
        }

        $array[] = new \yii\data\ArrayDataProvider([
            'allModels' => $dataProvider,
        ]);
        $array[] = $titulos;
        $array[] = $dataProvider;
        return $array;
    }

    public function llenarTtmpReportes($user_admin, $usua_id, $evaluado_id, $fingreso_formulario_ini, $fingreso_formulario_fin) {
        try {
            $sql = "DELETE FROM tbl_tmpreportes_arbol WHERE usua_id = $usua_id;";
            $sql .= "CALL sp_llenar_tmpreportes($user_admin,$usua_id,$evaluado_id,'$fingreso_formulario_ini','$fingreso_formulario_fin');";
            $command = \Yii::$app->db->createCommand($sql);
            $command->execute();
            return true;
        } catch (Exception $exc) {
            \Yii::error($exc->getMessage(), 'exception');
            return false;
        }
    }

    /**
     * Metodo que permite la busqueda en el reporte de 
     * formularios
     *      
     * 
     * @return \app\models\ActiveDataProvider
     * @author sebastian.orozco <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getReportformulariosamigo($bandera) {

        $and = "";
        if ($this->evaluado_id != '') {
            $and .= " AND t.evaluado_id = " . $this->evaluado_id;
        }

        $sql = "SELECT DISTINCT t.id, t.created,e.name evaluado, 
                e.identificacion eidentificacion, f.name formulario, t.dsruta_arbol nmarbol,
                u.usua_nombre usuario, r.role_nombre, d.name dimension,
                t.usua_id_lider, ul.usua_nombre usuarioLider, eq.name equipoName,  
                ROUND((t.score)*100,2) as score, 
                ROUND( t.pec_rack*100,2 ) as 'pec_rac', 
                ROUND( t.i1_nmcalculo*100,2 ) as 'i1_nmcalculo', 
                ROUND( t.i2_nmcalculo*100,2 ) as 'i2_nmcalculo', 
                ROUND( t.i3_nmcalculo*100,2 ) as 'i3_nmcalculo', 
                ROUND( t.i4_nmcalculo*100,2 ) as 'i4_nmcalculo', 
                ROUND( t.i5_nmcalculo*100,2 ) as 'i5_nmcalculo', 
                ROUND( t.i6_nmcalculo*100,2 ) as 'i6_nmcalculo', 
                ROUND( t.i7_nmcalculo*100,2 ) as 'i7_nmcalculo', 
                ROUND( t.i8_nmcalculo*100,2 ) as 'i8_nmcalculo', 
                ROUND( t.i9_nmcalculo*100,2 ) as 'i9_nmcalculo', 
                ROUND( t.i10_nmcalculo*100,2 ) as 'i10_nmcalculo', 
                t.dscomentario, t.usua_id_modifica, t.modified, 
                t.evaluado_id, t.formulario_id, t.id fid 
                FROM tbl_ejecucionformularios t 
FORCE INDEX(created_idx)
                INNER JOIN tbl_evaluados e ON e.id = t.evaluado_id 
                INNER JOIN tbl_formularios f ON f.id = t.formulario_id 
                INNER JOIN tbl_usuarios u ON u.usua_id = t.usua_id 
                INNER JOIN tbl_usuarios ul ON ul.usua_id = t.usua_id_lider 
                INNER JOIN rel_usuarios_roles ur ON ur.rel_usua_id = t.usua_id 
                INNER JOIN tbl_equipos eq ON eq.id = t.equipo_id 
                INNER JOIN tbl_roles r ON r.role_id = ur.rel_role_id ";
        //INNER JOIN tbl_arbols a ON a.id = t.arbol_id 
        $sql .= "INNER JOIN tbl_tmpreportes_arbol tra ON tra.arbol_id = t.arbol_id
                INNER JOIN tbl_dimensions d ON d.id = t.dimension_id 
                WHERE t.created >= ' " . $this->startDate . "' 
                AND t.created <= '" . $this->endDate . "'" . $and . " 
                ORDER BY t.created DESC";

        $count = Yii::$app->get(dbslave)->createCommand($sql)->queryAll();
        if ($bandera) {
            $dataProvider = new SqlDataProvider([
                'sql' => $sql,
                'totalCount' => count($count),
                'pagination' => ['pageSize' => 20,],
            ]);
        } else {
            $dataProvider = new SqlDataProvider([
                'sql' => $sql,
                'totalCount' => count($count),
                'pagination' => ['pageSize' => count($count),],
            ]);
        }
        return $dataProvider;
    }

    /**
     * Metodo que permite la busqueda en el reporte de 
     * formularios
     *      
     * 
     * @return \app\models\ActiveDataProvider
     * @author sebastian.orozco <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getReportencuestasamigo($bandera) {

        $and = "";
        if ($this->evaluado_id != '') {
            $and .= " AND agente = '" . $this->evaluado_id . "'";
        }

        $sql = "SELECT *, a.id fid, b.name cliente FROM tbl_base_satisfaccion a
                INNER JOIN tbl_arbols b ON a.pcrc = b.id ";
        //INNER JOIN tbl_arbols a ON a.id = t.arbol_id 
        $sql .= "WHERE a.created >= '" . $this->startDate . "' 
                AND a.created <= '" . $this->endDate . "'" . $and . " 
                ORDER BY a.created DESC";
        $count = Yii::$app->db->createCommand($sql)->queryAll();
        if ($bandera) {
            $dataProvider = new SqlDataProvider([
                'sql' => $sql,
                'totalCount' => count($count),
                'pagination' => ['pageSize' => 20,],
            ]);
        } else {
            $dataProvider = new SqlDataProvider([
                'sql' => $sql,
                'totalCount' => count($count),
                'pagination' => ['pageSize' => count($count),],
            ]);
        }

        //echo "<pre>";
        //print_r($dataProvider); die;
        return $dataProvider;
    }

   /**
     * Funcion que retorna el promedio de la metrica dependiendo del rango de fecha,
     * dimension y arbol
     * @author sebastian.orozco <sebastian.orozco@ingeneo.com.co>
     * @param type $arbol_id
     * @param type $dimension_id
     * @param type $fecha
     * @param type $metrica
     * @param type $banderaGrafica
     * @return Object Ejecucionformularios
     */
    public static function getDatabygraf($arbol_id, $dimension_id, $fecha, $metrica, $banderaGrafica, $model, $control, $volumenes = false, $segundoCalifPer = false) {
        $fechas = explode(' - ', $fecha);
        $fechaIni = $fechas[0] . " 00:00:00";
        $fechaFin = $fechas[1] . " 23:59:59";
        $baseConsulta = $metrica;
        $groupBy = ($banderaGrafica) ? 'je.arbol_id' : 'je.dimension_id';
        $sql = Ejecucionformularios::find()->select("SUM(je." . $baseConsulta . ")/COUNT(je.id) promedio,arbol_id, je.id, dimension_id, COUNT(je.id) total,je.*")
                ->from('`tbl_ejecucionformularios` je')
                ->where("je.dimension_id IN (" . $dimension_id . ") AND je.created BETWEEN '" . $fechaIni . "' AND '" . $fechaFin . "' AND je.arbol_id IN (" . $arbol_id . ")");
        
        if($segundoCalifPer){            
            $sql = Ejecucionformularios::find()->select("SUM(je." . $baseConsulta . ")/COUNT(je.id) promedio,arbol_id, je.id, dimension_id, COUNT(je.id) total,je.*")
                ->from('`tbl_ejecucionformularios` je')
                ->where("je.dimension_id IN (" . $dimension_id . ") AND sc.s_fecha BETWEEN '" . $fechaIni . "' AND '" . $fechaFin . "' AND je.arbol_id IN (" . $arbol_id . ")");
            $sql->join('INNER JOIN', 'tbl_segundo_calificador sc', 'je.id = sc.id_ejecucion_formulario');
        }
        $sql->groupBy($groupBy);
        if ($model->valorador != '') {
            if($segundoCalifPer){
                $sql->andWhere("sc.id_responsable IN (" . $model->valorador . ")");
            }else{
                $sql->andWhere("je.usua_id IN (" . $model->valorador . ")");
            }
        }
        if ($model->rol != '') {
            $sql->join('INNER JOIN', 'rel_usuarios_roles rr', 'rr.rel_usua_id = je.usua_id');
            $sql->addSelect('rr.*');
            $sql->andWhere("rr.rel_role_id IN (" . $model->rol . ")");
        }
        if ($model->equiposvalorador != '') {
           /* $sql->join('INNER JOIN', 'rel_grupos_usuarios rr', 'rr.usuario_id = je.usua_id');
            $sql->addSelect('rr.*');
            $sql->andWhere("rr.grupo_id IN (" . $model->equiposvalorador . ")");*/
            $modelequipoValoradores = RelEquiposEvaluadores::find()->where('equipo_id IN ('.$model->equiposvalorador.')')->asArray()->all();
            $arrayIdsusuarios = [];
            foreach ($modelequipoValoradores as $key => $value) {
                $arrayIdsusuarios[]=$value['evaluadores_id'];
            }
            $idsUsuarios = implode(',', $arrayIdsusuarios);
            $wherePersonas="";
            if($segundoCalifPer){
                $wherePersonas .= " AND sc.id_responsable IN (" . $idsUsuarios . ") ";
            }else{
                $wherePersonas .= " AND e.usua_id IN (" . $idsUsuarios . ") ";
            }
            //$sql->andWhere("je.usua_id IN (" . $idsUsuarios . ")");
        }
        //DATOS DE VOLUMENES
        if($volumenes){
            $sql->join('INNER JOIN', 'tbl_base_satisfaccion satu', 'satu.id = je.basesatisfaccion_id');
        }
        //WHERE VOLUMEN
        switch ($metrica) {
            case 13:
                $sql->andWhere("satu.tipologia I = 'FELICITACION'");
                break;
            case 14:
                $sql->andWhere("satu.tipologia = 'FELICITACIÓN CON BUZÓN'");
                break;
            case 15:
                $sql->andWhere("satu.tipologia = 'CRITICA'");
                break;
            case 16:
                $sql->andWhere("satu.tipologia = 'CRITICA POR BUZÓN'");
                break;
            case 17:
                $sql->andWhere("satu.tipologia = 'CRITICA PENALIZABLE'");
                break;                        
            case 19:
                $sql->andWhere("satu.responsabilidad = 'MARCA'");
                break;
            case 20:
                $sql->andWhere("satu.responsabilidad = 'CANAL'");
                break;
            case 21:
                $sql->andWhere("satu.responsabilidad = 'COMPARTIDA'");
                break;
            case 22:
                $sql->andWhere("satu.responsabilidad = 'EQUIVOCACION'");
                break;
            default:
                break;
        }
        //var_dump($sql);exit;
        return $sql->asArray()->all();
    }

    /**
     * Funcion que retorna el promedio de la metrica dependiendo del corte, dimension
     * y arbol
     * @author sebastian.orozco <sebastian.orozco@ingeneo.com.co>
     * @param type $dimension_id
     * @param type $corte
     * @param type $metrica
     * @return type
     */
    public static function getDatabytable($dimension_id, $corte, $metrica, $arbol, $model) {
        $baseConsulta = $metrica;
        $groupBy = 'je.dimension_id';
        $sql = Ejecucionformularios::find()->select("SUM(je." . $baseConsulta . ")/COUNT(je.id) promedio, je.dimension_id, je.*")
                ->from('`tbl_ejecucionformularios` je')
                ->where("je.dimension_id IN (" . $dimension_id . ") AND je.created BETWEEN '" . $corte['fechaI'] . "' AND '" . $corte['fechaF'] . "' AND je.arbol_id IN (" . $arbol . ")")
                ->groupBy($groupBy)
                ->orderBy($groupBy . " ASC ");
        if ($model->valorador != '') {
            //$sql->addSelect('rr.*');
            $sql->andWhere("je.usua_id IN (" . $model->valorador . ")");
        }
        if ($model->rol != '') {
            $sql->join('INNER JOIN', 'rel_usuarios_roles rr', 'rr.rel_usua_id = je.usua_id');
            $sql->addSelect('rr.*');
            $sql->andWhere("rr.rel_role_id IN (" . $model->rol . ")");
        }
        if ($model->equiposvalorador != '') {
            /*$sql->join('INNER JOIN', 'rel_grupos_usuarios rr', 'rr.usuario_id = je.usua_id');
            $sql->addSelect('rr.*');
            $sql->andWhere("rr.grupo_id IN (" . $model->equiposvalorador . ")");*/
            $modelequipoValoradores = RelEquiposEvaluadores::find()->where('equipo_id IN ('.$model->equiposvalorador.')')->asArray()->all();
            $arrayIdsusuarios = [];
            foreach ($modelequipoValoradores as $key => $value) {
                $arrayIdsusuarios[]=$value['evaluadores_id'];
            }
            $idsUsuarios = implode(',', $arrayIdsusuarios);
            $sql->andWhere("je.usua_id IN (" . $idsUsuarios . ")");
        }
        return $sql->asArray()->all();
    }
    /**
     * Funcion que retorna el promedio de la metrica dependiendo del corte, dimension
     * y arbol
     * @author sebastian.orozco <sebastian.orozco@ingeneo.com.co>
     * @param type $dimension_id
     * @param type $corte
     * @param type $metrica
     * @return type
     */
    public static function getDatabytableproceso($dimension_id, $corte, $metrica, $arbol, $model) {
        $baseConsulta = $metrica;
        $groupBy = 'je.dimension_id, je.arbol_id';
        $sql = Ejecucionformularios::find()->select("SUM(je." . $baseConsulta . ")/COUNT(je.id) promedio, je.dimension_id, je.*")
                ->from('`tbl_ejecucionformularios` je')
                ->where("je.dimension_id IN (" . $dimension_id . ") AND je.created BETWEEN '" . $corte['fechaI'] . "' AND '" . $corte['fechaF'] . "' AND je.arbol_id IN (" . $arbol . ")")
                ->groupBy($groupBy)
                ->orderBy($groupBy . " ASC ");
        if ($model->valorador != '') {
            //$sql->addSelect('rr.*');
            $sql->andWhere("je.usua_id IN (" . $model->valorador . ")");
        }
        if ($model->rol != '') {
            $sql->join('INNER JOIN', 'rel_usuarios_roles rr', 'rr.rel_usua_id = je.usua_id');
            $sql->addSelect('rr.*');
            $sql->andWhere("rr.rel_role_id IN (" . $model->rol . ")");
        }
        if ($model->equiposvalorador != '') {
            /*$sql->join('INNER JOIN', 'rel_grupos_usuarios rr', 'rr.usuario_id = je.usua_id');
            $sql->addSelect('rr.*');
            $sql->andWhere("rr.grupo_id IN (" . $model->equiposvalorador . ")");*/
            $modelequipoValoradores = RelEquiposEvaluadores::find()->where('equipo_id IN ('.$model->equiposvalorador.')')->asArray()->all();
            $arrayIdsusuarios = [];
            foreach ($modelequipoValoradores as $key => $value) {
                $arrayIdsusuarios[]=$value['evaluadores_id'];
            }
            $idsUsuarios = implode(',', $arrayIdsusuarios);
            $sql->andWhere("je.usua_id IN (" . $idsUsuarios . ")");
        }
        return $sql->asArray()->all();
    }
    /**
     * Funcion que retorna el promedio de la metrica dependiendo del rango de fecha,
     * dimension, arbol, rol, valorador o equipo
     * @author sebastian.orozco <sebastian.orozco@ingeneo.com.co>
     * @param type $arbol_id
     * @param type $dimension_id
     * @param type $fecha
     * @param type $metrica
     * @param type $banderaGrafica
     * @return Object Ejecucionformularios
     */
    public static function getDatabytabletotal($dimension_id, $fecha, $metrica, $arbol, $model) {
        $fechas = explode(' - ', $fecha);
        $fechaIni = $fechas[0] . " 00:00:00";
        $fechaFin = $fechas[1] . " 23:59:59";
        $baseConsulta = $metrica;
        $groupBy = 'je.dimension_id';
        $sql = Ejecucionformularios::find()->select("SUM(je." . $baseConsulta . ")/COUNT(je.id) promedio, je.dimension_id,je.*")
                ->from('`tbl_ejecucionformularios` je')
                ->where("je.dimension_id IN (" . $dimension_id . ") AND je.created BETWEEN '" . $fechaIni . "' AND '" . $fechaFin . "' AND je.arbol_id IN (" . $arbol . ")")
                ->groupBy($groupBy)
                ->orderBy($groupBy . " ASC ");
        if ($model->valorador != '') {
            $sql->andWhere("je.usua_id IN (" . $model->valorador . ")");
        }
        if ($model->rol != '') {
            $sql->join('INNER JOIN', 'rel_usuarios_roles rr', 'rr.rel_usua_id = je.usua_id');
            $sql->addSelect('rr.*');
            $sql->andWhere("rr.rel_role_id IN (" . $model->rol . ")");
        }
        if ($model->equiposvalorador != '') {
            /*$sql->join('INNER JOIN', 'rel_grupos_usuarios rr', 'rr.usuario_id = je.usua_id');
            $sql->addSelect('rr.*');
            $sql->andWhere("rr.grupo_id IN (" . $model->equiposvalorador . ")");*/
            $modelequipoValoradores = RelEquiposEvaluadores::find()->where('equipo_id IN ('.$model->equiposvalorador.')')->asArray()->all();
            $arrayIdsusuarios = [];
            foreach ($modelequipoValoradores as $key => $value) {
                $arrayIdsusuarios[]=$value['evaluadores_id'];
            }
            $idsUsuarios = implode(',', $arrayIdsusuarios);
            $sql->andWhere("je.usua_id IN (" . $idsUsuarios . ")");           
        }
        return $sql->asArray()->all();
    }

    /**
     * Funcion que retorna el promedio de la metrica dependiendo del rango de fecha,
     * dimension, arbol, rol, valorador y equipo
     * @author sebastian.orozco <sebastian.orozco@ingeneo.com.co>
     * @param type $arbol_id
     * @param type $dimension_id
     * @param type $fecha
     * @param type $metrica
     * @param type $banderaGrafica
     * @return Object Ejecucionformularios
     */
    public static function getDatabygrafgrupo($arbol_id, $dimension_id, $fecha, $metrica, $model, $volumenes = false, $segundoCalifPer = false) {
        $fechas = explode(' - ', $fecha);
        $fechaIni = $fechas[0] . " 00:00:00";
        $fechaFin = $fechas[1] . " 23:59:59";
        $baseConsulta = $metrica;
        $groupBy = 'ar.arbol_id';
        $sql = Ejecucionformularios::find()->select("SUM(je." . $baseConsulta . ")/COUNT(je.id) promedio,je.arbol_id, dimension_id, COUNT(je.id) total, je.*")
                ->from('`tbl_ejecucionformularios` je')
                //->join('INNER JOIN', '`tbl_arbols` ar', 'ar.id = `je`.`arbol_id`')
                ->where("je.dimension_id IN (" . $dimension_id . ") AND je.created BETWEEN '" . $fechaIni . "' AND '" . $fechaFin . "' AND je.arbol_id IN (" . $arbol_id . ")");
        if($segundoCalifPer){            
            $sql = Ejecucionformularios::find()->select("SUM(je." . $baseConsulta . ")/COUNT(je.id) promedio,je.arbol_id, dimension_id, COUNT(je.id) total, je.*")
                ->from('`tbl_ejecucionformularios` je')
                //->join('INNER JOIN', '`tbl_arbols` ar', 'ar.id = `je`.`arbol_id`')
                ->where("je.dimension_id IN (" . $dimension_id . ") AND sc.s_fecha BETWEEN '" . $fechaIni . "' AND '" . $fechaFin . "' AND je.arbol_id IN (" . $arbol_id . ")");
            $sql->join('INNER JOIN', 'tbl_segundo_calificador sc', 'je.id = sc.id_ejecucion_formulario');
        }
        if ($model->valorador != '') {
            if($segundoCalifPer){
                $sql->andWhere("sc.id_responsable IN (" . $model->valorador . ")");
            }else{
                $sql->andWhere("je.usua_id IN (" . $model->valorador . ")");
            }
        }
        if ($model->rol != '') {
            $sql->join('INNER JOIN', 'rel_usuarios_roles rr', 'rr.rel_usua_id = je.usua_id');
            $sql->addSelect('rr.*');
            $sql->andWhere("rr.rel_role_id IN (" . $model->rol . ")");
        }
        if ($model->equiposvalorador != '') {
            $modelequipoValoradores = RelEquiposEvaluadores::find()->where('equipo_id IN ('.$model->equiposvalorador.')')->asArray()->all();
            $arrayIdsusuarios = [];
            foreach ($modelequipoValoradores as $key => $value) {
                $arrayIdsusuarios[]=$value['evaluadores_id'];
            }
            $idsUsuarios = implode(',', $arrayIdsusuarios);
            $sql->andWhere("je.usua_id IN (" . $idsUsuarios . ")");
            /*$sql->join('INNER JOIN', 'rel_grupos_usuarios rr', 'rr.usuario_id = je.usua_id');
            $sql->addSelect('rr.*');
            $sql->andWhere("rr.grupo_id IN (" . $model->equiposvalorador . ")");*/
        }
        //DATOS DE VOLUMENES
        if($volumenes){
            $sql->join('INNER JOIN', 'tbl_base_satisfaccion satu', 'satu.id = je.basesatisfaccion_id');
        }
        //WHERE VOLUMEN
        switch ($metrica) {
            case 13:
                $sql->andWhere("satu.tipologia I = 'FELICITACION'");
                break;
            case 14:
                $sql->andWhere("satu.tipologia = 'FELICITACIÓN CON BUZÓN'");
                break;
            case 15:
                $sql->andWhere("satu.tipologia = 'CRITICA'");
                break;
            case 16:
                $sql->andWhere("satu.tipologia = 'CRITICA POR BUZÓN'");
                break;
            case 17:
                $sql->andWhere("satu.tipologia = 'CRITICA PENALIZABLE'");
                break;                        
            case 19:
                $sql->andWhere("satu.responsabilidad = 'MARCA'");
                break;
            case 20:
                $sql->andWhere("satu.responsabilidad = 'CANAL'");
                break;
            case 21:
                $sql->andWhere("satu.responsabilidad = 'COMPARTIDA'");
                break;
            case 22:
                $sql->andWhere("satu.responsabilidad = 'EQUIVOCACION'");
                break;
            default:
                break;
        }
        $result = $sql->asArray()->all();
        return $result;
    }

    /**
     * Funcion que retorna el promedio de la metrica dependiendo del rango de fecha,
     * dimension, arbol, rol, valorador o equipo para el reporte en exccel
     * @author sebastian.orozco <sebastian.orozco@ingeneo.com.co>
     * @param type $arbol_id
     * @param type $dimension_id
     * @param type $fecha
     * @param type $metrica
     * @param type $banderaGrafica
     * @return Object Ejecucionformularios
     */
    public static function getDatabygrafexcelpersona($arbol_id, $dimension_id, $fecha, $metrica, $model) {

        $fechas = explode(' - ', $fecha);
        $fechaIni = $fechas[0] . " 00:00:00";
        $fechaFin = $fechas[1] . " 23:59:59";
        $baseConsulta = $metrica;
        //$groupBy = ($banderaGrafica) ? 'je.arbol_id' : 'je.dimension_id';
        $sql = Ejecucionformularios::find()->select("SUM(je." . $baseConsulta . ")/COUNT(je.id) promedio,je.arbol_id, je.id, je.dimension_id, COUNT(je.id) total,je.*")
                ->from('`tbl_ejecucionformularios` je')
                ->where("je.dimension_id IN (" . $dimension_id . ") AND je.created BETWEEN '" . $fechaIni . "' AND '" . $fechaFin . "' AND je.arbol_id IN (" . $arbol_id . ")");
        
        if($baseConsulta == 'usua_id'){
            $sql = Ejecucionformularios::find()->select("SUM(je." . $baseConsulta . ")/COUNT(je.id) promedio,je.arbol_id, je.id, je.dimension_id, COUNT(je.id) total,je.*")
                ->from('`tbl_ejecucionformularios` je')
                ->where("je.dimension_id IN (" . $dimension_id . ") AND sc.s_fecha BETWEEN '" . $fechaIni . "' AND '" . $fechaFin . "' AND je.arbol_id IN (" . $arbol_id . ")");            
            $sql->join('INNER JOIN', 'tbl_segundo_calificador sc', 'je.id = sc.id_ejecucion_formulario');
        }
        
        //->groupBy($groupBy)->asArray()  
        $sql->join('INNER JOIN', 'tbl_usuarios u', 'u.usua_id = je.usua_id');
        $sql->addSelect('u.*');
        $sql->groupBy('je.usua_id,je.arbol_id,je.dimension_id');
        if ($model->valorador != '') {
            if($baseConsulta == 'usua_id'){
                $sql->andWhere("sc.id_responsable IN (" . $model->valorador . ")");
            }else{
                $sql->andWhere("je.usua_id IN (" . $model->valorador . ")");
            }
        }
        if ($model->rol != '') {
            $sql->join('INNER JOIN', 'rel_usuarios_roles rr', 'rr.rel_usua_id = je.usua_id');
            //$sql->join('INNER JOIN', 'tbl_usuarios u', 'u.usua_id = je.usua_id');
            $sql->addSelect('rr.*');
            $sql->andWhere("rr.rel_role_id IN (" . $model->rol . ")");
           // $sql->groupBy('je.usua_id,je.arbol_id,je.dimension_id');
        }
        if ($model->equiposvalorador != '') {
            /*$sql->join('INNER JOIN', 'rel_grupos_usuarios rr', 'rr.usuario_id = je.usua_id');
            $sql->join('INNER JOIN', 'tbl_usuarios u', 'u.usua_id = je.usua_id');
            $sql->addSelect('rr.*, u.*');
            $sql->andWhere("rr.grupo_id IN (" . $model->equiposvalorador . ")");*/
           //$sql->join('INNER JOIN', 'tbl_usuarios u', 'u.usua_id = je.usua_id');
            //$sql->addSelect('u.*');
            //$sql->groupBy('je.usua_id,je.arbol_id,je.dimension_id');
            $modelequipoValoradores = RelEquiposEvaluadores::find()->where('equipo_id IN ('.$model->equiposvalorador.')')->asArray()->all();
            $arrayIdsusuarios = [];
            foreach ($modelequipoValoradores as $key => $value) {
                $arrayIdsusuarios[]=$value['evaluadores_id'];
            }
            $idsUsuarios = implode(',', $arrayIdsusuarios);
            //$sql->andWhere("je.usua_id IN (" . $idsUsuarios . ")");
            if($baseConsulta == 'usua_id'){
                $sql->andWhere("sc.id_responsable IN (" . $idsUsuarios . ")");
            }else{
                $sql->andWhere("je.usua_id IN (" . $idsUsuarios . ")");
            }
        }
        return $sql->asArray()->all();
    }

    /**
     * Funcion que retorna el promedio de la metrica dependiendo del rango de fecha,
     * dimension, arbol, rol, valorador o equipo
     * @author sebastian.orozco <sebastian.orozco@ingeneo.com.co>
     * @param type $arbol_id
     * @param type $dimension_id
     * @param type $fecha
     * @param type $metrica
     * @param type $banderaGrafica
     * @return Object Ejecucionformularios
     */
    public static function getDatabytabletotalpersona($dimension_id, $fecha, $metrica, $arbol, $model) {
        $fechas = explode(' - ', $fecha);
        $fechaIni = $fechas[0] . " 00:00:00";
        $fechaFin = $fechas[1] . " 23:59:59";
        $baseConsulta = $metrica;       
        //$groupBy = 'je.dimension_id';
        $sql = Ejecucionformularios::find()->select("SUM(je." . $baseConsulta . ")/COUNT(je.id) promedio, je.dimension_id, COUNT(je.id) total,je.*")
                ->from('`tbl_ejecucionformularios` je')
                ->where("je.dimension_id IN (" . $dimension_id . ") AND je.created BETWEEN '" . $fechaIni . "' AND '" . $fechaFin . "' AND je.arbol_id IN (" . $arbol . ")");
        
        if($baseConsulta == 'usua_id'){
            $sql = Ejecucionformularios::find()->select("SUM(je." . $baseConsulta . ")/COUNT(je.id) promedio, je.dimension_id, COUNT(je.id) total,je.*")
                ->from('`tbl_ejecucionformularios` je')
                ->where("je.dimension_id IN (" . $dimension_id . ") AND sc.s_fecha BETWEEN '" . $fechaIni . "' AND '" . $fechaFin . "' AND je.arbol_id IN (" . $arbol . ")");
            $sql->join('INNER JOIN', 'tbl_segundo_calificador sc', 'je.id = sc.id_ejecucion_formulario');
        }
        
        //$sql->orderBy('je.usua_id,je.arbol_id,je.dimension_id Asc');
        $sql->join('INNER JOIN', 'tbl_usuarios u', 'u.usua_id = je.usua_id');
        $sql->addSelect('u.*');
        $sql->groupBy('je.usua_id,je.arbol_id,je.dimension_id');
        if ($model->valorador != '') {
            if($baseConsulta == 'usua_id'){
                $sql->andWhere("sc.id_responsable IN (" . $model->valorador . ")");
            }else{
                $sql->andWhere("je.usua_id IN (" . $model->valorador . ")");
            }            
        }
        if ($model->rol != '') {
            $sql->join('INNER JOIN', 'rel_usuarios_roles rr', 'rr.rel_usua_id = je.usua_id');
            //$sql->join('INNER JOIN', 'tbl_usuarios u', 'u.usua_id = je.usua_id');
            $sql->addSelect('rr.*');
            $sql->andWhere("rr.rel_role_id IN (" . $model->rol . ")");
            //$sql->groupBy('je.usua_id,je.arbol_id,je.dimension_id');
        }
        if ($model->equiposvalorador != '') {
            /*$sql->join('INNER JOIN', 'rel_grupos_usuarios rr', 'rr.usuario_id = je.usua_id');
            $sql->addSelect('rr.*');
            $sql->andWhere("rr.grupo_id IN (" . $model->equiposvalorador . ")");*/
            //$sql->join('INNER JOIN', 'tbl_usuarios u', 'u.usua_id = je.usua_id');
            //$sql->addSelect('u.*');
            $modelequipoValoradores = RelEquiposEvaluadores::find()->where('equipo_id IN ('.$model->equiposvalorador.')')->asArray()->all();
            $arrayIdsusuarios = [];
            foreach ($modelequipoValoradores as $key => $value) {
                $arrayIdsusuarios[]=$value['evaluadores_id'];
            }
            $idsUsuarios = implode(',', $arrayIdsusuarios);
            if($baseConsulta == 'usua_id'){
                $sql->andWhere("sc.id_responsable IN (" . $idsUsuarios . ")");
            }else{
                $sql->andWhere("je.usua_id IN (" . $idsUsuarios . ")");
            }            
            //$sql->groupBy('je.usua_id,je.arbol_id,je.dimension_id');
        }
        return $sql->asArray()->all();
    }
    /**
     * Funcion que retorna el promedio de la metrica dependiendo del rango de fecha,
     * dimension, arbol, rol, valorador o equipo
     * @author sebastian.orozco <sebastian.orozco@ingeneo.com.co>
     * @param type $arbol_id
     * @param type $dimension_id
     * @param type $fecha
     * @param type $metrica
     * @param type $banderaGrafica
     * @return Object Ejecucionformularios
     */
    public static function getDatabytabletotalexcel($dimension_id, $fecha, $metrica, $arbol, $model, $volumenes = false, $idMetrica = null) {
        $fechas = explode(' - ', $fecha);
        $fechaIni = $fechas[0] . " 00:00:00";
        $fechaFin = $fechas[1] . " 23:59:59";
        $baseConsulta = $metrica;
        $groupBy = 'je.dimension_id,je.arbol_id';
        $sql = Ejecucionformularios::find()->select("SUM(je." . $baseConsulta . ")/COUNT(je.id) promedio, je.dimension_id,COUNT(je.id) total,je.*")
                ->from('`tbl_ejecucionformularios` je')
                ->where("je.dimension_id IN (" . $dimension_id . ") AND je.created BETWEEN '" . $fechaIni . "' AND '" . $fechaFin . "' AND je.arbol_id IN (" . $arbol . ")")
                ->groupBy($groupBy)
                ->orderBy($groupBy . " ASC ");
        if ($model->valorador != '') {
            $sql->andWhere("je.usua_id IN (" . $model->valorador . ")");
        }
        if ($model->rol != '') {
            $sql->join('INNER JOIN', 'rel_usuarios_roles rr', 'rr.rel_usua_id = je.usua_id');
            $sql->addSelect('rr.*');
            $sql->andWhere("rr.rel_role_id IN (" . $model->rol . ")");
        }
        if ($model->equiposvalorador != '') {
            /*$sql->join('INNER JOIN', 'rel_grupos_usuarios rr', 'rr.usuario_id = je.usua_id');
            $sql->addSelect('rr.*');
            $sql->andWhere("rr.grupo_id IN (" . $model->equiposvalorador . ")");*/
            $modelequipoValoradores = RelEquiposEvaluadores::find()->where('equipo_id IN ('.$model->equiposvalorador.')')->asArray()->all();
            $arrayIdsusuarios = [];
            foreach ($modelequipoValoradores as $key => $value) {
                $arrayIdsusuarios[]=$value['evaluadores_id'];
            }
            $idsUsuarios = implode(',', $arrayIdsusuarios);
            $sql->andWhere("je.usua_id IN (" . $idsUsuarios . ")");
        }
        
        //DATOS DE VOLUMENES
        if($volumenes){
            $sql->join("INNER JOIN", "tbl_base_satisfaccion satu", "satu.id = je.basesatisfaccion_id");
            
            if(!is_null($idMetrica)){
                switch ($idMetrica) {
                    case 13:
                        $sql->andWhere("satu.tipologia = 'FELICITACION'");
                        break;
                    case 14:
                        $sql->andWhere("satu.tipologia = 'FELICITACIÓN CON BUZÓN'");
                        break;
                    case 15:
                        $sql->andWhere("satu.tipologia = 'CRITICA'");
                        break;
                    case 16:
                        $sql->andWhere("satu.tipologia = 'CRITICA POR BUZÓN'");
                        break;
                    case 17:
                        $sql->andWhere("satu.tipologia = 'CRITICA PENALIZABLE'");
                        break;                        
                    case 19:
                        $sql->andWhere("satu.responsabilidad = 'MARCA'");
                        break;
                    case 20:
                        $sql->andWhere("satu.responsabilidad = 'CANAL'");
                        break;
                    case 21:
                        $sql->andWhere("satu.responsabilidad = 'COMPARTIDA'");
                        break;
                    case 22:
                        $sql->andWhere("satu.responsabilidad = 'EQUIVOCACION'");
                        break;
                    default:
                        break;
                }
            }
        }
        //WHERE VOLUMEN
        /*var_dump($baseConsulta);exit;
        switch ($metrica) {
            case 13:
                $sql->andWhere("satu.tipologia = 'FELICITACION'");
                break;
            case 14:
                $sql->andWhere("satu.tipologia = 'FELICITACIÓN CON BUZÓN'");
                break;
            case 15:
                $sql->andWhere("AND satu.tipologia = 'CRITICA'");
                break;
            case 16:
                $sql->andWhere("AND satu.tipologia = 'CRITICA POR BUZÓN'");
                break;
            case 17:
                $sql->andWhere("AND satu.tipologia = 'CRITICA PENALIZABLE'");
                break;                        
            case 19:
                $sql->andWhere("AND satu.responsabilidad = 'MARCA'");
                break;
            case 20:
                $sql->andWhere("AND satu.responsabilidad = 'CANAL'");
                break;
            case 21:
                $sql->andWhere("AND satu.responsabilidad = 'COMPARTIDA'");
                break;
            case 22:
                $sql->andWhere("AND satu.responsabilidad = 'EQUIVOCACION'");
                break;
            default:
                break;
        }*/
        
        return $sql->asArray()->all();
    }
    
    /**
     * Funcion que retorna el promedio de la metrica dependiendo del corte, dimension
     * y arbol
     * @author sebastian.orozco <sebastian.orozco@ingeneo.com.co>
     * @param type $dimension_id
     * @param type $corte
     * @param type $metrica
     * @return type
     */
    public static function getDatabytableexcel($dimension_id, $corte, $metrica, $arbol, $model, $volumenes = false, $idMetrica = null) {
        $baseConsulta = $metrica;
        $groupBy = 'je.dimension_id,je.arbol_id';
        $sql = Ejecucionformularios::find()->select("SUM(je." . $baseConsulta . ")/COUNT(je.id) promedio,COUNT(je.id) total, je.dimension_id,je.arbol_id, je.*")
                ->from('`tbl_ejecucionformularios` je')
                ->where("je.dimension_id IN (" . $dimension_id . ") AND je.created BETWEEN '" . $corte['fechaI'] . "' AND '" . $corte['fechaF'] . "' AND je.arbol_id IN (" . $arbol . ")")
                ->groupBy($groupBy)
                ->orderBy($groupBy . " ASC ");
        if ($model->valorador != '') {
            //$sql->addSelect('rr.*');
            $sql->andWhere("je.usua_id IN (" . $model->valorador . ")");
        }
        if ($model->rol != '') {
            $sql->join('INNER JOIN', 'rel_usuarios_roles rr', 'rr.rel_usua_id = je.usua_id');
            $sql->addSelect('rr.*');
            $sql->andWhere("rr.rel_role_id IN (" . $model->rol . ")");
        }
        if ($model->equiposvalorador != '') {
            /*$sql->join('INNER JOIN', 'rel_grupos_usuarios rr', 'rr.usuario_id = je.usua_id');
            $sql->addSelect('rr.*');
            $sql->andWhere("rr.grupo_id IN (" . $model->equiposvalorador . ")");*/
            $modelequipoValoradores = RelEquiposEvaluadores::find()->where('equipo_id IN ('.$model->equiposvalorador.')')->asArray()->all();
            $arrayIdsusuarios = [];
            foreach ($modelequipoValoradores as $key => $value) {
                $arrayIdsusuarios[]=$value['evaluadores_id'];
            }
            $idsUsuarios = implode(',', $arrayIdsusuarios);
            $sql->andWhere("je.usua_id IN (" . $idsUsuarios . ")");
        }
        
        //DATOS DE VOLUMENES
        if($volumenes){
            $sql->join("INNER JOIN", "tbl_base_satisfaccion satu", "satu.id = je.basesatisfaccion_id");
            
            if(!is_null($idMetrica)){
                switch ($idMetrica) {
                    case 13:
                        $sql->andWhere("satu.tipologia = 'FELICITACION'");
                        break;
                    case 14:
                        $sql->andWhere("satu.tipologia = 'FELICITACIÓN CON BUZÓN'");
                        break;
                    case 15:
                        $sql->andWhere("satu.tipologia = 'CRITICA'");
                        break;
                    case 16:
                        $sql->andWhere("satu.tipologia = 'CRITICA POR BUZÓN'");
                        break;
                    case 17:
                        $sql->andWhere("satu.tipologia = 'CRITICA PENALIZABLE'");
                        break;                        
                    case 19:
                        $sql->andWhere("satu.responsabilidad = 'MARCA'");
                        break;
                    case 20:
                        $sql->andWhere("satu.responsabilidad = 'CANAL'");
                        break;
                    case 21:
                        $sql->andWhere("satu.responsabilidad = 'COMPARTIDA'");
                        break;
                    case 22:
                        $sql->andWhere("satu.responsabilidad = 'EQUIVOCACION'");
                        break;
                    default:
                        break;
                }
            }
        }
        return $sql->asArray()->all();
    }
    
    /**
     * Funcion que retorna el promedio de la metrica dependiendo del rango de fecha,
     * dimension, arbol, rol, valorador o equipo para el reporte en exccel
     * @author sebastian.orozco <sebastian.orozco@ingeneo.com.co>
     * @param type $arbol_id
     * @param type $dimension_id
     * @param type $fecha
     * @param type $metrica
     * @param type $banderaGrafica
     * @return Object Ejecucionformularios
     */
    public static function getDatabygrafexcelpersonacount($arbol_id, $dimension_id, $fecha, $metrica, $model) {

        $fechas = explode(' - ', $fecha);
        $fechaIni = $fechas[0] . " 00:00:00";
        $fechaFin = $fechas[1] . " 23:59:59";
        $baseConsulta = $metrica;
        //$groupBy = ($banderaGrafica) ? 'je.arbol_id' : 'je.dimension_id';
        $sql = Ejecucionformularios::find()->select("SUM(je." . $baseConsulta . ")/COUNT(je.id) promedio,je.arbol_id, je.id, je.dimension_id, COUNT(je.id) total,je.*")
                ->from('`tbl_ejecucionformularios` je')
                ->where("je.dimension_id IN (" . $dimension_id . ") AND je.created BETWEEN '" . $fechaIni . "' AND '" . $fechaFin . "' AND je.arbol_id IN (" . $arbol_id . ")");
        //->groupBy($groupBy)->asArray()  

        if ($model->valorador != '') {
            $sql->join('INNER JOIN', 'tbl_usuarios u', 'u.usua_id = je.usua_id');
            $sql->addSelect('u.*');
            $sql->andWhere("je.usua_id IN (" . $model->valorador . ")");
            $sql->groupBy('je.usua_id,je.arbol_id,je.dimension_id');
        }
        if ($model->rol != '') {
            $sql->join('INNER JOIN', 'rel_usuarios_roles rr', 'rr.rel_usua_id = je.usua_id');
            $sql->join('INNER JOIN', 'tbl_usuarios u', 'u.usua_id = je.usua_id');
            $sql->addSelect('rr.*, u.*');
            $sql->andWhere("rr.rel_role_id IN (" . $model->rol . ")");
            $sql->groupBy('je.usua_id,je.arbol_id,je.dimension_id');
        }
        if ($model->equiposvalorador != '') {
            /*$sql->join('INNER JOIN', 'rel_grupos_usuarios rr', 'rr.usuario_id = je.usua_id');
            $sql->join('INNER JOIN', 'tbl_usuarios u', 'u.usua_id = je.usua_id');
            $sql->addSelect('rr.*, u.*');
            $sql->andWhere("rr.grupo_id IN (" . $model->equiposvalorador . ")");*/
            $sql->join('INNER JOIN', 'tbl_usuarios u', 'u.usua_id = je.usua_id');
            $sql->addSelect('u.*');
            $modelequipoValoradores = RelEquiposEvaluadores::find()->where('equipo_id IN ('.$model->equiposvalorador.')')->asArray()->all();
            $arrayIdsusuarios = [];
            foreach ($modelequipoValoradores as $key => $value) {
                $arrayIdsusuarios[]=$value['evaluadores_id'];
            }
            $idsUsuarios = implode(',', $arrayIdsusuarios);
            $sql->andWhere("je.usua_id IN (" . $idsUsuarios . ")");
            $sql->groupBy('je.usua_id,je.arbol_id,je.dimension_id');
        }
        return $sql->count();
    }
}
