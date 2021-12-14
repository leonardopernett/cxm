<?php

namespace app\models;

use Yii;
use yii\helpers\Html;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "tbl_segundo_calificador".
 *
 * @property integer $id_segundo_calificador
 * @property string $estado_sc
 * @property string $argumento
 * @property integer $id_solicitante
 * @property integer $id_evaluador
 * @property integer $id_responsable
 * @property integer $b_segundo_envio
 * @property integer $id_ejecucion_formulario
 * @property integer $b_editar
 * @property TblEvaluados $idSolicitante
 * @property TblEjecucionformularios $idEjecucionFormulario
 */
class SegundoCalificador extends \yii\db\ActiveRecord {

    public $argumentoAsesor;
    public $argumentoLider;
    public $argumentoLiderEvaluadores;
    public $formulario;
    public $tipo_notifi;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_segundo_calificador';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['estado_sc', 'argumento'], 'string'],
            [['id_solicitante', 'id_evaluador', 'id_ejecucion_formulario'], 'required'],
            [['argumentoLider'], 'required', 'on' => 'liderevaluado'],
            [['argumentoAsesor'], 'required', 'on' => 'asesor'],
            [['argumentoLiderEvaluadores'], 'required', 'on' => 'liderevaluador'],
            [['id_solicitante', 'id_evaluador', 'id_responsable', 'b_segundo_envio', 'id_ejecucion_formulario', 'b_editar'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id_segundo_calificador' => Yii::t('app', 'Id Segundo Calificador'),
            'estado_sc' => Yii::t('app', 'Estado Sc'),
            'argumento' => Yii::t('app', 'Argumento'),
            'argumentoLider' => Yii::t('app', 'Argumento'),
            'argumentoAsesor' => Yii::t('app', 'Argumento'),
            'id_solicitante' => Yii::t('app', 'Id Solicitante'),
            'id_evaluador' => Yii::t('app', 'Id Evaluador'),
            'id_responsable' => Yii::t('app', 'Id Responsable'),
            'b_segundo_envio' => Yii::t('app', 'B Segundo Envio'),
            'id_ejecucion_formulario' => Yii::t('app', 'Id Ejecucion Formulario'),
            's_fecha' => Yii::t('app', 'Fecha'),
            'id_caso' => Yii::t('app', 'ID Caso'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdSolicitante() {
        return $this->hasOne(Evaluados::className(), ['id' => 'id_solicitante']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdValorador() {
        return $this->hasOne(Usuarios::className(), ['usua_id' => 'id_evaluador']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdResponsable() {
        return $this->hasOne(Usuarios::className(), ['usua_id' => 'id_responsable']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelUsuariosRoles() {
        return $this->hasOne(RelUsuariosRoles::className(), ['rel_usua_id' => 'id_responsable']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdEjecucionFormulario() {
        return $this->hasOne(Ejecucionformularios::className(), ['id' => 'id_ejecucion_formulario']);
    }

    /**
     * Retorna el listado de estados
     * @return array
     */
    public function getEstados($estado) {
        $spanEstado = "";
        switch ($estado) {
            case "Abierto":
                $spanEstado = Html::tag('span', $estado, ['class' => 'label label-warning']);
                break;
            case "Aceptado":
                $spanEstado = Html::tag('span', $estado, ['class' => 'label label-success']);
                break;
            case "Rechazado":
                $spanEstado = Html::tag('span', $estado, ['class' => 'label label-danger']);
                break;
            case "Escalado":
                $spanEstado = Html::tag('span', $estado, ['class' => 'label label-info']);
                break;
            default:
                $spanEstado = Html::tag('span', 'SIN ESTADO', ['class' => 'label label-default']);
                break;
        }
        return $spanEstado;
    }

    /**
     * Metodo que permite la busqueda en el reporte de 
     * segundo calificador
     *      
     * 
     * @return \app\models\ActiveDataProvider
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getReportSegundoCalificador() {
        $query = SegundoCalificador::find()
                /*->select(['id_segundo_calificador' => 'tbl_segundo_calificador.id_segundo_calificador',
                    'id_caso' => 'tbl_segundo_calificador.id_caso',
                    'estado_sc' => 'tbl_segundo_calificador.estado_sc',
                    'argumento' => 'tbl_segundo_calificador.argumento',
                    'solicitante' => 'ev.name',
                    'evaluador' => 'evaluador.usua_nombre',
                    'responsable' => 'responsable.usua_nombre',
                    'gestionado' => 'tbl_segundo_calificador.gestionado',
                    's_fecha' => 'tbl_segundo_calificador.s_fecha'
                ])                
                ->join('LEFT JOIN', 'tbl_evaluados ev', 'ev.id = tbl_segundo_calificador.id_solicitante')
                ->join('LEFT JOIN', 'tbl_usuarios evaluador', 'evaluador.usua_id = tbl_segundo_calificador.id_evaluador')
                ->join('LEFT JOIN', 'tbl_usuarios responsable', 'responsable.usua_id = tbl_segundo_calificador.id_responsable')*/;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        
        
        $query->orderBy('id_caso DESC');
        return $dataProvider;
    }

}
