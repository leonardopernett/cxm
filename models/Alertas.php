<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

class Alertas extends \yii\db\ActiveRecord
{

	//public $startDate;

	public static function tableName() {
        return 'tbl_alertascx';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['fecha', 'pcrc', 'valorador', 'tipo_alerta', 'archivo_adjunto', 'remitentes', 'asunto', 'comentario'], 'required'],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'fecha' => Yii::t('app', 'Tipofeedback ID'),
            'pcrc' => Yii::t('app', 'Formulario ID'),
            'valorador' => Yii::t('app', 'Evaluador'),
            'tipo_alerta' => Yii::t('app', 'Fecha de Creacion del Feedback'),
            'archivo_adjunto' => Yii::t('app', 'Lider de Equipo'),
            'remitentes' => Yii::t('app', 'Evaluado ID'),
            'asunto' => Yii::t('app', 'Snavisar'),
            'comentario' => Yii::t('app', 'Gestionado'),
            
        ];
    }

    // public function getReporteproceso() {
    //     $query = Alertas::find()
    //             ->select(['fecha' => 'tbl_alertascx.fecha',
    //                 'created' => 'tbl_alertascx.created',
    //                 'snaviso_revisado' => 'tbl_alertascx.snaviso_revisado',
    //                 'usua_id_lider' => 'tbl_alertascx.usua_id_lider',
    //                 'usua_id' => 'tbl_alertascx.usua_id',
    //                 'evaluado_id' => 'tbl_alertascx.evaluado_id',
    //                 'ejecucionformulario_id' => 'tbl_alertascx.ejecucionformulario_id',
    //                 'feaccion_correctiva' => 'tbl_alertascx.feaccion_correctiva'
    //             ])
                

    //     $dataProvider = new ActiveDataProvider([
    //         'query' => $query,
    //     ]);

        

    //     return $dataProvider;

    // }

    // public function getReporteproceso() {
    // 	return Alertas::find()
    //                     ->select('b.name AS Programa, c.name AS Cliente, count(a.pcrc)')
    //                     ->from('tbl_alertascx a')
    //                     ->join('INNER JOIN', 'tbl_arbols b', 'b.id = a.pcrc')
    //                     ->join('INNER JOIN', 'tbl_arbols c', 'b.arbol_id = c.id')
    //                     ->all();
    // }


    // SELECT b.name AS Programa, c.name AS Cliente, count(tbl_alertascx.pcrc) FROM tbl_alertascx a
// INNER JOIN tbl_arbols b ON b.id = tbl_alertascx.pcrc
// INNER JOIN tbl_arbols c ON b.arbol_id = c.id
// GROUP BY tbl_alertascx.pcrc;


    // public function getclientealertas() {
    //     $query = Ejecucionfeedbacks::find()
    //             ->select(['id' => 'tbl_ejecucionfeedbacks.id',
    //                 'created' => 'tbl_ejecucionfeedbacks.created',
    //                 'snaviso_revisado' => 'tbl_ejecucionfeedbacks.snaviso_revisado',
    //                 'usua_id_lider' => 'tbl_ejecucionfeedbacks.usua_id_lider',
    //                 'usua_id' => 'tbl_ejecucionfeedbacks.usua_id',
    //                 'evaluado_id' => 'tbl_ejecucionfeedbacks.evaluado_id',
    //                 'ejecucionformulario_id' => 'tbl_ejecucionfeedbacks.ejecucionformulario_id',
    //                 'feaccion_correctiva' => 'tbl_ejecucionfeedbacks.feaccion_correctiva',
    //                 'tipofeedback_id' => 'tbl_ejecucionfeedbacks.tipofeedback_id',
    //                 'dscausa_raiz' => 'tbl_ejecucionfeedbacks.dscausa_raiz',
    //                 'dsaccion_correctiva' => 'tbl_ejecucionfeedbacks.dsaccion_correctiva',
    //                 'dscompromiso' => 'tbl_ejecucionfeedbacks.dscompromiso',
    //                 'dscomentario' => 'tbl_ejecucionfeedbacks.dscomentario',
    //                 'basessatisfaccion_id' => 'e.basesatisfaccion_id',
    //                 'dimension_id' => 'e.dimension_id'
    //             ])
    //             ->distinct()
    //             ->join('LEFT JOIN', 'tbl_ejecucionformularios e', 'e.id = tbl_ejecucionfeedbacks.ejecucionformulario_id')
    //             ->join('LEFT JOIN', 'tbl_evaluados ev', 'ev.id = tbl_ejecucionfeedbacks.evaluado_id')
    //             ->join('LEFT JOIN', 'tbl_formularios f', 'f.id = e.formulario_id')
    //             ->join('LEFT JOIN', 'tbl_usuarios u', 'u.usua_id = tbl_ejecucionfeedbacks.usua_id')
    //             ->join('INNER JOIN', 'tbl_tipofeedbacks ti', 'ti.id = tbl_ejecucionfeedbacks.tipofeedback_id')
    //             ->join('INNER JOIN', 'tbl_categoriafeedbacks ca', 'ca.id = ti.categoriafeedback_id');

    //     $dataProvider = new ActiveDataProvider([
    //         'query' => $query,
    //     ]);

    //     $query->andFilterWhere([
    //         'tbl_ejecucionfeedbacks.evaluado_id' => $this->evaluado_id,
    //         'tbl_ejecucionfeedbacks.usua_id' => $this->usua_id,
    //         'tbl_ejecucionfeedbacks.usua_id_lider' => $this->usua_id_lider,
    //         'tbl_ejecucionfeedbacks.snaviso_revisado' => $this->snaviso_revisado,
    //     ]);
    //     if ($this->arbol_id != '') {
    //         $query->andWhere('e.arbol_id IN (' . $this->arbol_id . ')');
    //     }
    //     if ($this->dimension_id != '') {
    //         $query->andWhere('e.dimension_id IN (' . $this->dimension_id . ')');
    //     }
    //     $query->andFilterWhere(['between', 'DATE(tbl_ejecucionfeedbacks.created)',
    //         $this->startDate, $this->endDate]);

    //     return $dataProvider;

    // }

    // public function getvaloradoralertas() {
    //     $query = Ejecucionfeedbacks::find()
    //             ->select(['id' => 'tbl_ejecucionfeedbacks.id',
    //                 'created' => 'tbl_ejecucionfeedbacks.created',
    //                 'snaviso_revisado' => 'tbl_ejecucionfeedbacks.snaviso_revisado',
    //                 'usua_id_lider' => 'tbl_ejecucionfeedbacks.usua_id_lider',
    //                 'usua_id' => 'tbl_ejecucionfeedbacks.usua_id',
    //                 'evaluado_id' => 'tbl_ejecucionfeedbacks.evaluado_id',
    //                 'ejecucionformulario_id' => 'tbl_ejecucionfeedbacks.ejecucionformulario_id',
    //                 'feaccion_correctiva' => 'tbl_ejecucionfeedbacks.feaccion_correctiva',
    //                 'tipofeedback_id' => 'tbl_ejecucionfeedbacks.tipofeedback_id',
    //                 'dscausa_raiz' => 'tbl_ejecucionfeedbacks.dscausa_raiz',
    //                 'dsaccion_correctiva' => 'tbl_ejecucionfeedbacks.dsaccion_correctiva',
    //                 'dscompromiso' => 'tbl_ejecucionfeedbacks.dscompromiso',
    //                 'dscomentario' => 'tbl_ejecucionfeedbacks.dscomentario',
    //                 'basessatisfaccion_id' => 'e.basesatisfaccion_id',
    //                 'dimension_id' => 'e.dimension_id'
    //             ])
    //             ->distinct()
    //             ->join('LEFT JOIN', 'tbl_ejecucionformularios e', 'e.id = tbl_ejecucionfeedbacks.ejecucionformulario_id')
    //             ->join('LEFT JOIN', 'tbl_evaluados ev', 'ev.id = tbl_ejecucionfeedbacks.evaluado_id')
    //             ->join('LEFT JOIN', 'tbl_formularios f', 'f.id = e.formulario_id')
    //             ->join('LEFT JOIN', 'tbl_usuarios u', 'u.usua_id = tbl_ejecucionfeedbacks.usua_id')
    //             ->join('INNER JOIN', 'tbl_tipofeedbacks ti', 'ti.id = tbl_ejecucionfeedbacks.tipofeedback_id')
    //             ->join('INNER JOIN', 'tbl_categoriafeedbacks ca', 'ca.id = ti.categoriafeedback_id');

    //     $dataProvider = new ActiveDataProvider([
    //         'query' => $query,
    //     ]);

    //     $query->andFilterWhere([
    //         'tbl_ejecucionfeedbacks.evaluado_id' => $this->evaluado_id,
    //         'tbl_ejecucionfeedbacks.usua_id' => $this->usua_id,
    //         'tbl_ejecucionfeedbacks.usua_id_lider' => $this->usua_id_lider,
    //         'tbl_ejecucionfeedbacks.snaviso_revisado' => $this->snaviso_revisado,
    //     ]);
    //     if ($this->arbol_id != '') {
    //         $query->andWhere('e.arbol_id IN (' . $this->arbol_id . ')');
    //     }
    //     if ($this->dimension_id != '') {
    //         $query->andWhere('e.dimension_id IN (' . $this->dimension_id . ')');
    //     }
    //     $query->andFilterWhere(['between', 'DATE(tbl_ejecucionfeedbacks.created)',
    //         $this->startDate, $this->endDate]);

    //     return $dataProvider;

    // }


}