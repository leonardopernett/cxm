<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\BaseSatisfaccion;
use yii\helpers\ArrayHelper;

/**
 * BaseSatisfaccionSearch represents the model behind the search form about `app\models\BaseSatisfaccion`.
 */
class BaseSatisfaccionSearch extends BaseSatisfaccion {

    public $acentos = array(":");
    public $sinAcentos = array("");

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'ano', 'mes', 'dia', 'hora', 'pcrc', 'cliente', 'id_lider_equipo', 'dimension'], 'integer'],
            [['identificacion', 'nombre', 'ani', 'agente', 'agente2',
            'chat_transfer', 'ext', 'rn', 'industria', 'institucion',
            'tipo_servicio', 'pregunta1', 'pregunta2', 'pregunta3',
            'pregunta4', 'pregunta5', 'pregunta6', 'pregunta7',
            'pregunta8', 'pregunta9', 'pregunta10', 'connid', 'tipo_encuesta', 'comentario',
            'lider_equipo', 'cc_lider', 'coordinador', 'jefe_operaciones', 'tipologia',
            'estado', 'llamada', 'buzon', 'responsable', 'usado',
            'fecha_gestion', 'fecha', 'tipo_inbox'], 'safe'],
            [['fecha'], 'required', 'on' => 'reporte_satisfaccion_indicadores'],
            [['fecha', 'pcrc'], 'required', 'on' => 'reporte_satisfaccion'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params) {
        $query = BaseSatisfaccion::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false
        ]);
        $query->orderBy("id DESC");
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'ano' => $this->ano,
            'mes' => $this->mes,
            'dia' => $this->dia,
            'hora' => $this->hora,
            'pcrc' => $this->pcrc,
            'cliente' => $this->cliente,
            'fecha_gestion' => $this->fecha_gestion,
            'id_lider_equipo' => $this->id_lider_equipo
        ]);

        $query->andFilterWhere(['like', 'identificacion', $this->identificacion])
                ->andFilterWhere(['like', 'nombre', $this->nombre])
                ->andFilterWhere(['like', 'ani', $this->ani])
                ->andFilterWhere(['like', 'agente', $this->agente])
                ->andFilterWhere(['like', 'agente2', $this->agente2])
                ->andFilterWhere(['like', 'chat_transfer', $this->chat_transfer])
                ->andFilterWhere(['like', 'ext', $this->ext])
                ->andFilterWhere(['like', 'rn', $this->rn])
                ->andFilterWhere(['like', 'industria', $this->industria])
                ->andFilterWhere(['like', 'institucion', $this->institucion])
                ->andFilterWhere(['like', 'tipo_servicio', $this->tipo_servicio])
                ->andFilterWhere(['like', 'pregunta1', $this->pregunta1])
                ->andFilterWhere(['like', 'pregunta2', $this->pregunta2])
                ->andFilterWhere(['like', 'pregunta3', $this->pregunta3])
                ->andFilterWhere(['like', 'pregunta4', $this->pregunta4])
                ->andFilterWhere(['like', 'pregunta5', $this->pregunta5])
                ->andFilterWhere(['like', 'pregunta6', $this->pregunta6])
                ->andFilterWhere(['like', 'pregunta7', $this->pregunta7])
                ->andFilterWhere(['like', 'pregunta8', $this->pregunta8])
                ->andFilterWhere(['like', 'pregunta9', $this->pregunta9])
                ->andFilterWhere(['like', 'pregunta10', $this->pregunta10])
                ->andFilterWhere(['like', 'connid', $this->connid])
                ->andFilterWhere(['like', 'tipo_encuesta', $this->tipo_encuesta])
                ->andFilterWhere(['like', 'comentario', $this->comentario])
                ->andFilterWhere(['like', 'lider_equipo', $this->lider_equipo])
                ->andFilterWhere(['like', 'cc_lider', $this->lider_equipo])
                ->andFilterWhere(['like', 'coordinador', $this->coordinador])
                ->andFilterWhere(['like', 'jefe_operaciones', $this->jefe_operaciones])
                ->andFilterWhere(['like', 'tipologia', $this->tipologia])
                ->andFilterWhere(['like', 'estado', $this->estado])
                ->andFilterWhere(['like', 'llamada', $this->llamada])
                ->andFilterWhere(['like', 'buzon', $this->buzon])
                ->andFilterWhere(['like', 'responsable', $this->responsable])
                ->andFilterWhere(['like', 'usado', $this->usado]);

        return $dataProvider;
    }

    /**
     * Metodo para filtrar el Inbox
     * @return ActiveDataProvider
     */
    public function searchGestion($tipo_inbox = "NORMAL") {
        /*
         * Se agrega validacion en la cual se mira en que grupos esta el usuario
         * logueado para permitir la visualizacion de solo los arboles que esten
         * atados a dichos grupos
         */
        $cadenaIdarboles = '';
        $idArbolesPermiso = [];
        $sql = 'SELECT tgu.*,pga.*,rgu.* FROM tbl_grupos_usuarios tgu '
                . 'INNER JOIN rel_grupos_usuarios rgu ON rgu.grupo_id = tgu.grupos_id '
                . 'INNER JOIN tbl_permisos_grupos_arbols pga ON tgu.grupos_id = pga.grupousuario_id '
                . ' INNER JOIN tbl_arbols a ON a.id = pga.arbol_id'
                . ' WHERE rgu.usuario_id =' . Yii::$app->user->identity->id . '  GROUP BY pga.arbol_id';
        $queryGrupos = Yii::$app->db->createCommand($sql)->queryAll();
        foreach ($queryGrupos as $value) {
            $idArbolesPermiso[] = $value['arbol_id'];
        }
        $cadenaIdarboles = implode(',', $idArbolesPermiso);
        //fin de consulta de arboles con el permiso de vista
        if (!empty($this->fecha)) {
            $dates = explode(' - ', $this->fecha);
            $this->startDate = $dates[0];
            $this->endDate = $dates[1];

            $startYear = date("Y", strtotime($this->startDate));
            $startMonth = date("m", strtotime($this->startDate));
            $startDay = date("d", strtotime($this->startDate));
            $endYear = date("Y", strtotime($this->endDate));
            $endMonth = date("m", strtotime($this->endDate));
            $endDay = date("d", strtotime($this->endDate));
            $condition = 'TIMESTAMP(CONCAT(ano,"-", mes,"-",dia))  >= "'
                    . $startYear . '-' . $startMonth . '-' . $startDay
                    . '" AND TIMESTAMP(CONCAT(ano,"-", mes,"-",dia)) <= "'
                    . $endYear . '-' . $endMonth . '-' . $endDay . '"';
            $query = BaseSatisfaccion::find()->from('tbl_base_satisfaccion b')->where($condition);
        } else {
            $query = BaseSatisfaccion::find()->from('tbl_base_satisfaccion b');
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false
        ]);

        if ($this->dimension != '') {
            $query->join('INNER JOIN', 'tbl_ejecucionformularios ej', 'ej.basesatisfaccion_id = b.id');
            $query->andWhere("ej.dimension_id = " . $this->dimension);
        }

        $query->andFilterWhere([
            'pcrc' => $this->pcrc,
            'cliente' => $this->cliente,
            'id_lider_equipo' => $this->id_lider_equipo
        ]);

        //TIPO DE INBOX
        $query->andFilterWhere([
            'tipo_inbox' => $tipo_inbox,
        ]);

        $query->andFilterWhere(['like', 'estado', $this->estado]);
        if ($this->responsable == '1') {
            $query->andWhere('responsable IS NULL');
        } else {
            $query->andFilterWhere(['like', 'responsable', $this->responsable]);
        }

        $query->andFilterWhere(['tipologia' => $this->tipologia]);
        //where con los arboles permitidos
        $query->andWhere('pcrc IN (' . $cadenaIdarboles . ')');
        if ($this->agente != '') {
            $evaluado = Evaluados::findOne(['id' => $this->agente]);
            $query->andFilterWhere(['agente' => $evaluado->dsusuario_red]);
        }
        $query->orderBy("id DESC");

        return $dataProvider;
    }

    /**
     * Crea el reporte por usuario y retorna los datos
     * @return \yii\data\ArrayDataProvider
     */
    public function reporteSatisfaccion() {
        $result = [];
        if (!empty($this->startDate) && !empty($this->endDate)) {

            $startYear = date("Y", strtotime($this->startDate));
            $startMonth = date("m", strtotime($this->startDate));
            $startDay = date("d", strtotime($this->startDate));
            $endYear = date("Y", strtotime($this->endDate));
            $endMonth = date("m", strtotime($this->endDate));
            $endDay = date("d", strtotime($this->endDate));
            if (empty($this->pcrc)) {
                $condition = 'TIMESTAMP(CONCAT(ano,"-", mes,"-",dia))  >= "'
                        . $startYear . '-' . $startMonth . '-' . $startDay
                        . '" AND TIMESTAMP(CONCAT(ano,"-", mes,"-",dia)) <= "'
                        . $endYear . '-' . $endMonth . '-' . $endDay . '"';
                $basesatisfaccion = BaseSatisfaccion::find()->where($condition)->groupBy(['pcrc'])->asArray()->all();

                if (count($basesatisfaccion) > 0) {
                    foreach ($basesatisfaccion as $value) {
                        $sqlvalidar = "SELECT categoria, pre_indicador, enunciado_pre
FROM tbl_preguntas pp
JOIN tbl_parametrizacion_encuesta pe ON pe.id = pp.id_parametrizacion
WHERE pe.programa = " . $value['pcrc'];
                        $count = Yii::$app->db->createCommand($sqlvalidar)->queryAll();
                        if (count($count) > 0) {
                            $sql = 'CALL sp_llenar_tmpreporte_satisfaccion("'
                                    . $value['pcrc'] . '", "'
                                    . Yii::$app->user->identity->id . '","'
                                    . $startYear . '","'
                                    . $startMonth . '","'
                                    . $startDay . '","'
                                    . $endYear . '","'
                                    . $endMonth . '","'
                                    . $endDay . '")';
                            $command = \Yii::$app->db->createCommand($sql);
                            $command->execute();

                            $a[] = TmpreporteSatisfaccion::find()
                                    ->where(['usua_id' => Yii::$app->user->identity->id])
                                    ->all();
                            $result = call_user_func_array('array_merge', $a);
                        }
                    }
                }
            } else {
                $condition = 'TIMESTAMP(CONCAT(ano,"-", mes,"-",dia))  >= "'
                        . $startYear . '-' . $startMonth . '-' . $startDay
                        . '" AND TIMESTAMP(CONCAT(ano,"-", mes,"-",dia)) <= "'
                        . $endYear . '-' . $endMonth . '-' . $endDay . '" and pcrc =' . $this->pcrc;
                $existPcrc = BaseSatisfaccion::find()->where($condition)->asArray()->all();
                $sqlvalidar = "SELECT categoria, pre_indicador, enunciado_pre
FROM tbl_preguntas pp
JOIN tbl_parametrizacion_encuesta pe ON pe.id = pp.id_parametrizacion
WHERE pe.programa = " . $this->pcrc;
                $count = Yii::$app->db->createCommand($sqlvalidar)->queryAll();
                if (count($existPcrc) > 0) {
                    if (count($count) > 0) {
                        $sql = 'CALL sp_llenar_tmpreporte_satisfaccion("'
                                . $this->pcrc . '", "'
                                . Yii::$app->user->identity->id . '","'
                                . $startYear . '","'
                                . $startMonth . '","'
                                . $startDay . '","'
                                . $endYear . '","'
                                . $endMonth . '","'
                                . $endDay . '")';
                        $command = \Yii::$app->db->createCommand($sql);
                        $command->execute();
                    }
                }
                //Extraemos los resultados del reporte -------------------------
                $result = TmpreporteSatisfaccion::find()
                        ->where(['usua_id' => Yii::$app->user->identity->id])
                        ->all();
            }
        }
        return new \yii\data\ArrayDataProvider([
            'allModels' => $result,
        ]);
    }

    /**
     * Metodo que crea el reporte con el historico de base satisfaccion
     * 
     * @return array
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function historicobasesatisafaccion() {
        $titulos = array();
        $dataProvider = array();
        if (!empty($this->startDate) && !empty($this->endDate)) {
            $startYear = date("Y", strtotime($this->startDate));
            $startMonth = date("m", strtotime($this->startDate));
            $startDay = date("d", strtotime($this->startDate));
            $endYear = date("Y", strtotime($this->endDate));
            $endMonth = date("m", strtotime($this->endDate));
            $endDay = date("d", strtotime($this->endDate));
            $condition = 'TIMESTAMP(CONCAT(b.ano,"-",b.mes,"-",b.dia))  >= "'
                    . $startYear . '-' . $startMonth . '-' . $startDay
                    . '" AND TIMESTAMP(CONCAT(b.ano,"-",b.mes,"-",b.dia)) <= "'
                    . $endYear . '-' . $endMonth . '-' . $endDay . '" AND b.pcrc =' . $this->pcrc;
            $result = BaseSatisfaccion::find()
                    ->select("b.id AS id_base,b.*,r.id AS id_respuesta_base,r.*,t.id AS id_respuesta_tipif,t.*,s.id AS id_respuesta_subt,s.*"
                            . ",(SELECT COUNT(base.id) FROM tbl_base_satisfaccion base LEFT JOIN tbl_respuesta_base_satisfaccion re ON re.id_basesatisfaccion =  base.id LEFT JOIN tbl_respuesta_basesatisfaccion_tipificacion te ON re.id = te.id_respuesta
                            LEFT JOIN tbl_respuesta_basesatisfaccion_subtipificacion se ON te.id = se.tipificacion_id WHERE base.pcrc = b.pcrc AND  re.respuesta <> 'on' AND b.id=base.id GROUP BY base.id) AS conteo
                            ")
                    ->from("tbl_base_satisfaccion b")
                    ->join("LEFT JOIN", "tbl_respuesta_base_satisfaccion r", "r.id_basesatisfaccion = b.id")
                    ->join("LEFT JOIN", "tbl_respuesta_basesatisfaccion_tipificacion t", "t.id_respuesta = r.id")
                    ->join("LEFT JOIN", "tbl_respuesta_basesatisfaccion_subtipificacion s", "t.id = s.tipificacion_id")
                    ->where($condition)
                    //->andWhere("r.respuesta <> 'on'")
                    ->orderBy("conteo DESC")
                    ->asArray()
                    ->all();
            if (count($result) == 0) {
                $array[] = new \yii\data\ArrayDataProvider([
                    'allModels' => $dataProvider,
                ]);
                $array[] = $titulos;
                return $array;
            }
            $modelpcrc = Arboles::findOne(["id" => $this->pcrc]);
            $modelcliente = Arboles::findOne(["id" => $modelpcrc->arbol_id]);
            $preguntas = Preguntas::find()
                    ->join("INNER JOIN", "tbl_parametrizacion_encuesta", "tbl_parametrizacion_encuesta.id=tbl_preguntas.id_parametrizacion")
                    ->where(["programa" => $this->pcrc])
                    ->orderBy("tbl_preguntas.id ASC")
                    ->all();

            $index = 0;
            $modelInicial = BaseSatisfaccion::find()->where(['id' => $result[$index]['id_base']])->asArray()->all();
            $idBase = $result[$index]['id_base'];
            $idRespuesta = 0;
            $idTipif = 0;
            $idSubtipif = 0;
            unset($modelInicial[0]['id_lider_equipo']);

            unset($modelInicial[0]['agente2']);
            unset($modelInicial[0]['coordinador']);
            unset($modelInicial[0]['jefe_operaciones']);
            unset($modelInicial[0]['usado']);
            $modelEvaluado = Evaluados::find()->where(["dsusuario_red" => $modelInicial[0]["agente"]])->asArray()->one();
            $titulos[] = ['header' => 'Id', 'value' => '0'];
            $titulos[] = ['header' => 'Año', 'value' => '6'];
            $titulos[] = ['header' => 'Mes', 'value' => '7'];
            $titulos[] = ['header' => 'Dia', 'value' => '8'];
            $titulos[] = ['header' => 'Hora', 'value' => '9'];
            $titulos[] = ['header' => 'Identificación Cliente', 'value' => '1'];
            $titulos[] = ['header' => 'Nombre Cliente', 'value' => '2'];
            $titulos[] = ['header' => 'ANI', 'value' => '3'];
            $titulos[] = ['header' => 'Identificación  Agente', 'value' => '5'];
            $titulos[] = ['header' => 'Nombre  Agente', 'value' => '39'];
            $titulos[] = ['header' => 'Usuario de red Agente', 'value' => '4'];
            $titulos[] = ['header' => 'Identificación Líder de equipo', 'value' => '32'];
            $titulos[] = ['header' => 'Nombre Lider de Equipo', 'value' => '31'];
            $titulos[] = ['header' => 'Chat Transfer', 'value' => '10'];
            $titulos[] = ['header' => 'Extensión', 'value' => '11'];
            $titulos[] = ['header' => 'RN', 'value' => '12'];
            $titulos[] = ['header' => 'Industria', 'value' => '13'];
            $titulos[] = ['header' => 'Institución', 'value' => '14'];
            $titulos[] = ['header' => 'Programa/Pcrc', 'value' => '15'];
            $titulos[] = ['header' => 'Cliente', 'value' => '16'];
            $titulos[] = ['header' => 'Tipo Servicio', 'value' => '17'];
            for ($i = 0; $i < count($preguntas); $i++) {
                //Resto el numero de headers mayores a 17
                $titulos[] = ['header' => '' . $preguntas[$i]->enunciado_pre, 'value' => '' . (count($titulos) - 3)];
            }
            $titulos[] = ['header' => 'Connid', 'value' => '28'];
            $titulos[] = ['header' => 'Tipo encuesta', 'value' => '29'];
            $titulos[] = ['header' => 'Comentario', 'value' => '30'];
            $titulos[] = ['header' => 'Tipología', 'value' => '33'];
            $titulos[] = ['header' => 'Estado', 'value' => '34'];
            $titulos[] = ['header' => 'Llamada', 'value' => '35'];
            $titulos[] = ['header' => 'Buzón', 'value' => '36'];
            $titulos[] = ['header' => 'Responsable Gestión', 'value' => '37'];
            $titulos[] = ['header' => 'Fecha gestión', 'value' => '38'];

            $modelInicial[0]['pcrc'] = $modelpcrc->name;
            $modelInicial[0]['cliente'] = $modelcliente->name;
            $modelInicial[0]['nombreAgente'] = $modelEvaluado['name'];
            $dataProvider[] = array_values($modelInicial[0]);
            foreach ($result as $value) {
                if ($value['id_base'] != $idBase) {
                    $index++;
                    $modelInicial = BaseSatisfaccion::find()->where(['id' => $value['id_base']])->asArray()->all();
                    unset($modelInicial[0]['id_lider_equipo']);
                    unset($modelInicial[0]['coordinador']);
                    unset($modelInicial[0]['jefe_operaciones']);
                    unset($modelInicial[0]['usado']);
                    unset($modelInicial[0]['agente2']);
                    $modelInicial[0]['pcrc'] = $modelpcrc->name;
                    $modelInicial[0]['cliente'] = $modelcliente->name;
                    $modelEvaluado = Evaluados::find()->where(["dsusuario_red" => $modelInicial[0]["agente"]])->asArray()->one();
                    $modelInicial[0]['nombreAgente'] = $modelEvaluado['name'];
                    $dataProvider[] = array_values($modelInicial[0]);
                    $idBase = $value['id_base'];
                }
                if ($idRespuesta != $value['id_respuesta_base'] && strtolower($value['respuesta']) != 'on') {
                    $idRespuesta = $value['id_respuesta_base'];
                    $posResp = 0;
                    $preguntaValue = \app\models\Bloquedetalles::find()->where(['id' => $value['text_pregunta']])->asArray()->all();
                    $preguntaValue[0]['name'] = (isset($preguntaValue[0]['name'])) ? $preguntaValue[0]['name'] : 'Pregunta Eliminada';
                    for ($i = 0; $i < count($titulos); $i++) {
                        if ($titulos[$i]['header'] == ($value['text_pregunta'] . '-' . $preguntaValue[0]['name'])) {
                            $posResp = $titulos[$i]['value'];
                        }
                    }
                    if ($posResp != 0) {
                        $dataProvider[$index][$posResp] = $value['respuesta'];
                    } else {
                        $titulos[] = ['header' => '' . $value['text_pregunta'] . '-' . $preguntaValue[0]['name'], 'value' => '' . count($titulos)];
                        $dataProvider[$index][] = $value['respuesta'];
                    }
                }
                if ($idTipif != $value['id_respuesta_tipif']) {
                    $pos = 0;
                    $idTipif = $value['id_respuesta_tipif'];
                    $tipiValue = \app\models\RespuestaBasesatisfaccionTipificacion::findOne($value['id_respuesta_tipif']);
                    if (!empty($tipiValue)) {
                        for ($i = 0; $i < count($titulos); $i++) {
                            if ($titulos[$i]['header'] == $tipiValue->tipificacion_name) {
                                $pos = $titulos[$i]['value'];
                            }
                        }
                        if ($pos != 0) {
                            $dataProvider[$index][$pos] = $value['tipificacion_name'];
                        } else {
                            $titulos[] = ['header' => '' . $tipiValue->tipificacion_name, 'value' => '' . count($titulos)];
                            $dataProvider[$index][(count($titulos) - 1)] = $value['tipificacion_name'];
                        }
                    }
                }
                if ($idSubtipif != $value['id_respuesta_subt']) {
                    $pos = 0;
                    $idSubtipif = $value['id_respuesta_subt'];
                    $subtipiValue = \app\models\RespuestaBasesatisfaccionSubtipificacion::findOne($value['id_respuesta_subt']);
                    if (!empty($subtipiValue)) {
                        for ($i = 0; $i < count($titulos); $i++) {
                            if ($titulos[$i]['header'] == $subtipiValue->subtificacion_name) {
                                $pos = $titulos[$i]['value'];
                            }
                        }
                        if ($pos != 0) {
                            $dataProvider[$index][$pos] = $value['subtificacion_name'];
                        } else {
                            $titulos[] = ['header' => '' . $subtipiValue->subtificacion_name, 'value' => '' . count($titulos)];
                            $dataProvider[$index][(count($titulos) - 1)] = $value['subtificacion_name'];
                        }
                    }
                }
            }
        }
        $array[] = new \yii\data\ArrayDataProvider([
            'allModels' => $dataProvider,
        ]);
        $array[] = $titulos;
        return $array;
    }

    public function rptControlSatisfaccion() {
        $result = [];
        $rowResult = [];
        $titulos = [
            [
                'attribute' => 'cliente',
                'header' => Yii::t('app', 'Cliente'),
            ],
            [
                'attribute' => 'pcrc',
                'header' => Yii::t('app', 'Pcrc'),
            ],
            [
                'attribute' => 'totalEncuestas',
                'header' => Yii::t('app', 'Encuestas Totales'),
            ],
        ];

        if (!empty($this->startDate) && !empty($this->endDate)) {

            $startYear = date("Y", strtotime($this->startDate));
            $startMonth = date("m", strtotime($this->startDate));
            $startDay = date("d", strtotime($this->startDate));
            $endYear = date("Y", strtotime($this->endDate));
            $endMonth = date("m", strtotime($this->endDate));
            $endDay = date("d", strtotime($this->endDate));
            $condition = 'TIMESTAMP(CONCAT(ano,"-", mes,"-",dia))  >= "'
                    . $startYear . '-' . $startMonth . '-' . $startDay
                    . '" AND TIMESTAMP(CONCAT(ano,"-", mes,"-",dia)) <= "'
                    . $endYear . '-' . $endMonth . '-' . $endDay . '" and pcrc =' . $this->pcrc;
            $existPcrc = static::find()->where($condition)->asArray()->all();
            if (count($existPcrc) > 0) {
                $pcrc = $data = \app\models\Arboles::findOne($this->pcrc);
                //CLIENTE
                $cliente = \app\models\Arboles::findOne($pcrc->arbol_id);
                $sql = '
                    SELECT id, tipologia, estado, count(*) as total, 
                    SUM(case when estado = "Cerrado" then 1 else 0 end) as gestionados 
                    FROM tbl_base_satisfaccion
                    WHERE ' . $condition . '
                    GROUP BY tipologia';
                $command = \Yii::$app->db->createCommand($sql);
                $resultQuery = $command->queryAll();
                if (count($resultQuery) > 0) {
                    $rowResult['pcrc'] = $pcrc->name;
                    $rowResult['cliente'] = $cliente->name;
                    $rowResult['totalEncuestas'] = count($existPcrc);
                    foreach ($resultQuery as $data) {
                        $header = strtoupper($data['tipologia']);
                        $headerGestionado = strtoupper($data['tipologia'] . ' Gestionados');
                        if (empty($header) || $header == '') {
                            continue;
                        }
                        $titulos[] = [
                            'attribute' => $header,
                            'header' => $header,
                        ];
                        $titulos[] = [
                            'attribute' => $headerGestionado,
                            'header' => $headerGestionado,
                        ];
                        $rowResult[$header] = $data['total'];
                        $rowResult[$headerGestionado] = $data['gestionados'];
                    }
                    $result[] = $rowResult;
                }
            }
        }

        $allModels = new \yii\data\ArrayDataProvider([
            'allModels' => $result,
        ]);
        return [ 'titulos' => $titulos, 'result' => $allModels];
    }

    public function extractConsTransSatisfaccion() {
        set_time_limit(0);
        $startDate = $this->startDate . " 00:00:00";
        $endDate = $this->endDate . " 23:59:59";
        $dimension = $this->dimension;
        /* Inicio Variables */
        //INICIO DE TRANSPOSICION DE DATOS
        $textos = $this->getTextosPreguntas();
        $preguntas = Preguntas::find()
                ->join("INNER JOIN", "tbl_parametrizacion_encuesta", "tbl_parametrizacion_encuesta.id=tbl_preguntas.id_parametrizacion")
                ->where(["programa" => $this->pcrc])
                ->orderBy("tbl_preguntas.id ASC")
                ->all();
        $modelpcrc = Arboles::findOne(["id" => $this->pcrc]);
        $modelcliente = Arboles::findOne(["id" => $modelpcrc->arbol_id]);
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

        /* Archivos */
        $fileName = Yii::$app->basePath . DIRECTORY_SEPARATOR . "web" .
                DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR
                . Yii::t('app', 'Reporte_Gestion') . '_' . date('Ymd') . "_" .
                Yii::$app->user->identity->id . ".xlsx";

        /* Titulos */
        $titulos[] = ['header' => 'Id', 'value' => '0'];
        $titulos[] = ['header' => 'Año', 'value' => '6'];
        $titulos[] = ['header' => 'Mes', 'value' => '7'];
        $titulos[] = ['header' => 'Dia', 'value' => '8'];
        $titulos[] = ['header' => 'Hora', 'value' => '9'];
        $titulos[] = ['header' => 'Identificación Cliente', 'value' => '1'];
        $titulos[] = ['header' => 'Nombre Cliente', 'value' => '2'];
        $titulos[] = ['header' => 'ANI', 'value' => '3'];
        $titulos[] = ['header' => 'Usuario de red Agente', 'value' => '4'];
        $titulos[] = ['header' => 'Identificación  Agente', 'value' => '5'];
        
        $titulos[] = ['header' => 'Chat Transfer', 'value' => '10'];
        $titulos[] = ['header' => 'Extensión', 'value' => '11'];
        $titulos[] = ['header' => 'RN', 'value' => '12'];
        $titulos[] = ['header' => 'Industria', 'value' => '13'];
        $titulos[] = ['header' => 'Institución', 'value' => '14'];
        $titulos[] = ['header' => 'Programa/Pcrc', 'value' => '15'];
        $titulos[] = ['header' => 'Cliente', 'value' => '16'];
        $titulos[] = ['header' => 'Tipo Servicio', 'value' => '17'];
        for ($i = 0; $i < count($preguntas); $i++) {
            //Resto el numero de headers mayores a 17
            $titulos[] = ['header' => '' . $preguntas[$i]->enunciado_pre, 'value' => '' . (count($titulos) - 3)];
        }
        $titulos[] = ['header' => 'Connid', 'value' => '28'];
        $titulos[] = ['header' => 'Tipo encuesta', 'value' => '29'];
        $titulos[] = ['header' => 'Comentario', 'value' => '30'];
        $titulos[] = ['header' => 'Tipología', 'value' => '33'];
        $titulos[] = ['header' => 'Estado', 'value' => '34'];
        $titulos[] = ['header' => 'Llamada', 'value' => '35'];
        $titulos[] = ['header' => 'Buzón', 'value' => '36'];
        $titulos[] = ['header' => 'Responsable Gestión', 'value' => '37'];
        $titulos[] = ['header' => 'Fecha Modificación', 'value' => '38'];


        $titulos[40] = ['header' => 'Responsabilidad', 'value' => '40'];
        $titulos[41] = ['header' => 'Canal', 'value' => '41'];
        $titulos[42] = ['header' => 'Marca', 'value' => '42'];
        $titulos[43] = ['header' => 'Equivocacion', 'value' => '43'];
        $titulos[66] = ['header' => 'Fecha gestión ', 'value' => '66'];
        $titulos[44] = ['header' => 'Dimension', 'value' => '44'];
        $titulos[45] = ['header' => 'Programa/PCRC Padre', 'value' => '45'];
        $titulos[46] = ['header' => 'Programa/PCRC', 'value' => '46'];
        $titulos[47] = ['header' => 'Formulario', 'value' => '47'];
        $titulos[48] = ['header' => 'Cedula Valorado', 'value' => '48'];
        $titulos[49] = ['header' => 'Valorado', 'value' => '49'];
        $titulos[50] = ['header' => 'Valorador', 'value' => '50'];
        $titulos[51] = ['header' => 'Rol', 'value' => '51'];
        $titulos[52] = ['header' => 'Fuente', 'value' => '52'];
        $titulos[53] = ['header' => 'Transaccion', 'value' => '53'];
        $titulos[54] = ['header' => 'Equipo', 'value' => '54'];
        $titulos[55] = ['header' => 'Comentario', 'value' => '55'];
        $titulos[56] = ['header' => $textos[0]['titulo'], 'value' => '56'];
        $titulos[57] = ['header' => $textos[1]['titulo'], 'value' => '57'];
        $titulos[58] = ['header' => $textos[2]['titulo'], 'value' => '58'];
        $titulos[59] = ['header' => $textos[3]['titulo'], 'value' => '59'];
        $titulos[60] = ['header' => $textos[4]['titulo'], 'value' => '60'];
        $titulos[61] = ['header' => $textos[5]['titulo'], 'value' => '61'];
        $titulos[62] = ['header' => $textos[6]['titulo'], 'value' => '62'];
        $titulos[63] = ['header' => $textos[7]['titulo'], 'value' => '63'];
        $titulos[64] = ['header' => $textos[8]['titulo'], 'value' => '64'];
        $titulos[65] = ['header' => $textos[9]['titulo'], 'value' => '65'];

        // Generar los tituloos
        $filecontent = "";

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
                rol.role_nombre rol, sat.*, f.modified";

        $sql .= " FROM (tbl_ejecucionformularios f, tbl_formularios xf, tbl_arbols xarbol,  
                  tbl_usuarios xusuarios, tbl_evaluados xevaluados, tbl_transacions xtransacions,
                  tbl_usuarios xusuarios2, tbl_equipos xequipos, rel_usuarios_roles urol, tbl_roles rol, 
                  tbl_arbols xarbol_padre, tbl_dimensions xdim, tbl_ejecucionseccions s, tbl_seccions xs, 
                  tbl_ejecucionbloques b, tbl_bloques xb, tbl_calificaciondetalles xcd, tbl_bloquedetalles xd, 
                  tbl_ejecucionbloquedetalles d, tbl_base_satisfaccion sat) ";

        $sql .= " LEFT JOIN tbl_tipificaciondetalles xtd ON xd.tipificacion_id = xtd.tipificacion_id 
                  LEFT JOIN tbl_ejecucionbloquedetalles_tipificaciones t 
                  ON xtd.id = t.tipificaciondetalle_id AND t.ejecucionbloquedetalle_id = d.id  
                  LEFT JOIN tbl_tipificaciondetalles xstd ON xtd.subtipificacion_id = xstd.tipificacion_id  
                  LEFT JOIN tbl_ejecucionbloquedetalles_subtipificaciones st 
                  ON xstd.id = st.tipificaciondetalle_id AND t.id =  st.ejecucionbloquedetalles_tipificacion_id";


        $sql .= " WHERE f.arbol_id = " . $this->pcrc . " AND sat.fecha_satu BETWEEN '" . $startDate . "' AND '" . $endDate . "' 
                  AND xf.id = f.formulario_id AND f.arbol_id = xarbol.id AND xtransacions.id = f.transacion_id 
                  AND xarbol.arbol_id = xarbol_padre.id AND f.dimension_id = xdim.id AND f.evaluado_id = xevaluados.id 
                  AND f.usua_id_lider = xusuarios.usua_id AND f.usua_id = xusuarios2.usua_id AND f.equipo_id = xequipos.id 
                  AND f.usua_id = urol.rel_usua_id AND urol.rel_role_id = rol.role_id AND f.id = s.ejecucionformulario_id 
                  AND xs.id = s.seccion_id AND s.id = b.ejecucionseccion_id AND xb.id = b.bloque_id 
                  AND b.id = d.ejecucionbloque_id AND xd.id = d.bloquedetalle_id AND d.calificaciondetalle_id = xcd.id
                  AND f.basesatisfaccion_id IS NOT NULL
                  AND sat.id = f.basesatisfaccion_id ";

        if ($dimension != '') {
            $sql .= " AND xdim.id = " . $dimension;
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
        $arrayIds = [];
        do {
            $data = null;
            $limite_ciclo_inicial += $delta_ciclo;
            $sqlRango = $sql . " LIMIT " . $limite_ciclo_inicial . "," . $limite_ciclo_final . " ";
            $data = Yii::$app->db->createCommand($sqlRango)->queryAll();

            //Codigo nuevo -----------------------------------------------------            
            if (count($data) > 0) {

                foreach ($data as $i => $row) {

                    if ($row['fid'] != $fid) {
                        // Si no es el primer registro se imprime la fila
                        if ($fid != -1) {
                            //CSV PARA MEJORAR EL EXCEL DEL EXTRACTAR
                            $filecontent = "";
                            $printTitle = false;

                            //IMPRIMO EN EL CSV LOS RESULTADOS QUE VAYAN
                            foreach ($dataProvider as $value) {
                                $arrayIds[] = $value['0'];
                                $tmpCont = implode("|", $value);
                                $filecontent = str_replace(array("\r\n"), ' ', $tmpCont);
                                $objPHPexcel->getActiveSheet()->setCellValue('A' . $fila, $filecontent);
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
                        $iData = 66;
                        unset($data[$i]['id_lider_equipo']);
                        unset($data[$i]['coordinador']);
                        unset($data[$i]['jefe_operaciones']);
                        unset($data[$i]['usado']);
                        unset($data[$i]['agente2']);
                        $dataProvider[$newRow][0] = $this->vData($data[$i]['id']);
                        $dataProvider[$newRow][6] = $this->vData($data[$i]['ano']);
                        $dataProvider[$newRow][7] = $this->vData($data[$i]['mes']);
                        $dataProvider[$newRow][8] = $this->vData($data[$i]['dia']);
                        $dataProvider[$newRow][9] = $this->vData($data[$i]['hora']);
                        $dataProvider[$newRow][1] = $this->vData($data[$i]['identificacion']);
                        $dataProvider[$newRow][2] = $this->vData($data[$i]['nombre']);
                        $dataProvider[$newRow][3] = $this->vData($data[$i]['ani']);
                        $dataProvider[$newRow][4] = $this->vData($data[$i]['agente']);
                        $dataProvider[$newRow][5] = $this->vData($data[$i]['cc_agente']);

                        $dataProvider[$newRow][10] = $this->vData($data[$i]['chat_transfer']);
                        $dataProvider[$newRow][11] = $this->vData($data[$i]['ext']);
                        $dataProvider[$newRow][12] = $this->vData($data[$i]['rn']);
                        $dataProvider[$newRow][13] = $this->vData($data[$i]['industria']);
                        $dataProvider[$newRow][14] = $this->vData($data[$i]['institucion']);
                        $dataProvider[$newRow][15] = $this->vData($modelpcrc->name);
                        $dataProvider[$newRow][16] = $this->vData($modelcliente->name);
                        $dataProvider[$newRow][17] = $this->vData($data[$i]['tipo_servicio']);
                        $dataProvider[$newRow][18] = $this->vData($data[$i]['pregunta1']);
                        $dataProvider[$newRow][19] = $this->vData($data[$i]['pregunta2']);
                        $dataProvider[$newRow][20] = $this->vData($data[$i]['pregunta3']);
                        $dataProvider[$newRow][21] = $this->vData($data[$i]['pregunta4']);
                        $dataProvider[$newRow][22] = $this->vData($data[$i]['pregunta5']);
                        $dataProvider[$newRow][23] = $this->vData($data[$i]['pregunta6']);
                        $dataProvider[$newRow][24] = $this->vData($data[$i]['pregunta7']);
                        $dataProvider[$newRow][25] = $this->vData($data[$i]['pregunta8']);
                        $dataProvider[$newRow][26] = $this->vData($data[$i]['pregunta9']);
                        $dataProvider[$newRow][27] = $this->vData($data[$i]['pregunta10']);
                        $dataProvider[$newRow][28] = $this->vData($data[$i]['connid']);
                        $dataProvider[$newRow][29] = $this->vData($data[$i]['tipo_encuesta']);
                        $dataProvider[$newRow][30] = $this->vData($data[$i]['comentario']);
                        $dataProvider[$newRow][33] = $this->vData($data[$i]['tipologia']);
                        $dataProvider[$newRow][34] = $this->vData($data[$i]['estado']);
                        $dataProvider[$newRow][35] = $this->vData($data[$i]['llamada']);
                        $dataProvider[$newRow][36] = $this->vData($data[$i]['buzon']);
                        $dataProvider[$newRow][37] = $this->vData($data[$i]['responsable']);
                        $dataProvider[$newRow][38] = $this->vData($data[$i]['modified']); // se toma fecha de modificacion tbl_ejecucionformularios


                        $dataProvider[$newRow][40] = $this->vData($data[$i]['responsabilidad']);
                        $dataProvider[$newRow][41] = $this->vData($data[$i]['canal']);
                        $dataProvider[$newRow][42] = $this->vData($data[$i]['marca']);
                        $dataProvider[$newRow][43] = $this->vData($data[$i]['equivocacion']);
                        $dataProvider[$newRow][66] = $this->vData($data[$i]['Fecha']);
                        $dataProvider[$newRow][44] = $this->vData($data[$i]['Dimension']);
                        $dataProvider[$newRow][45] = $this->vData($data[$i]['ArbolPadre']);
                        $dataProvider[$newRow][46] = $this->vData($data[$i]['Arbol']);
                        $dataProvider[$newRow][47] = $this->vData($data[$i]['Formulario']);
                        $dataProvider[$newRow][48] = $this->vData($data[$i]['cedula_evaluado']);
                        $dataProvider[$newRow][49] = $this->vData($data[$i]['evaluado']);
                        $dataProvider[$newRow][50] = $this->vData($data[$i]['evaluador']);
                        $dataProvider[$newRow][51] = $this->vData($data[$i]['rol']);
                        $dataProvider[$newRow][52] = $this->vData($data[$i]['fuente']);
                        $dataProvider[$newRow][53] = $this->vData($data[$i]['transacion']);
                        $dataProvider[$newRow][54] = $this->vData($data[$i]['equipo']);
                        $dataProvider[$newRow][55] = $this->vData($data[$i]['fdscomentario']);
                        $dataProvider[$newRow][56] = $this->vData($data[$i]['fi1_nmcalculo']);
                        $dataProvider[$newRow][57] = $this->vData($data[$i]['fi2_nmcalculo']);
                        $dataProvider[$newRow][58] = $this->vData($data[$i]['fi3_nmcalculo']);
                        $dataProvider[$newRow][59] = $this->vData($data[$i]['fi4_nmcalculo']);
                        $dataProvider[$newRow][60] = $this->vData($data[$i]['fi5_nmcalculo']);
                        $dataProvider[$newRow][61] = $this->vData($data[$i]['fi6_nmcalculo']);
                        $dataProvider[$newRow][62] = $this->vData($data[$i]['fi7_nmcalculo']);
                        $dataProvider[$newRow][63] = $this->vData($data[$i]['fi8_nmcalculo']);
                        $dataProvider[$newRow][64] = $this->vData($data[$i]['fi9_nmcalculo']);
                        $dataProvider[$newRow][65] = $this->vData($data[$i]['fi10_nmcalculo']);
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
        $filecontent = "";
        $printTitle = false;
        //IMPRIMO EL ULTIMO REGISTRO
        if (isset($dataProvider)) {
            foreach ($dataProvider as $value) {
                $arrayIds[] = $value['0'];
                $tmpCont = implode("|", $value);
                $filecontent = str_replace(array("\r\n"), ' ', $tmpCont);
                $objPHPexcel->getActiveSheet()->setCellValue('A' . $fila, $filecontent);
                $fila++;
            }
        } else {
            $export = false;
        }

        if ($dimension == '') {
            //CONSULTO E IMPRIMO LOS REGISTROS Q ESTEN GESTIONADOS
            $idsIn = implode(',', $arrayIds);
            if ($idsIn != '') {
                $result = BaseSatisfaccion::find()
                        ->select("b.*")
                        ->from("tbl_base_satisfaccion b")
                        ->where("b.id NOT IN (" . $idsIn . ") AND b.fecha_satu BETWEEN '" . $startDate . "' AND '" . $endDate . "' AND b.pcrc =" . $this->pcrc)
                        //->andWhere("r.respuesta <> 'on'")
                        ->asArray()
                        ->all();
            } else {
                $result = BaseSatisfaccion::find()
                        ->select("b.*")
                        ->from("tbl_base_satisfaccion b")
                        ->where("b.fecha_satu BETWEEN '" . $startDate . "' AND '" . $endDate . "' AND b.pcrc = " . $this->pcrc)
                        //->andWhere("r.respuesta <> 'on'")
                        ->asArray()
                        ->all();
            }
            $newRow = 1;
            $dataProvider = null;
            $dataProvider = array();

            if (count($result) != 0) {
                foreach ($result as $id => $satu) {
                    unset($satu['id_lider_equipo']);
                    unset($satu['coordinador']);
                    unset($satu['jefe_operaciones']);
                    unset($satu['usado']);
                    unset($satu['agente2']);
                    $dataProvider[$newRow][0] = $this->vData($satu['id']);
                    $dataProvider[$newRow][6] = $this->vData($satu['ano']);
                    $dataProvider[$newRow][7] = $this->vData($satu['mes']);
                    $dataProvider[$newRow][8] = $this->vData($satu['dia']);
                    $dataProvider[$newRow][9] = $this->vData($satu['hora']);
                    $dataProvider[$newRow][1] = $this->vData($satu['identificacion']);
                    $dataProvider[$newRow][2] = $this->vData($satu['nombre']);
                    $dataProvider[$newRow][3] = $this->vData($satu['ani']);
                    $dataProvider[$newRow][4] = $this->vData($satu['agente']);
                    $dataProvider[$newRow][5] = $this->vData($satu['cc_agente']);
                    $dataProvider[$newRow][10] = $this->vData($satu['chat_transfer']);
                    $dataProvider[$newRow][11] = $this->vData($satu['ext']);
                    $dataProvider[$newRow][12] = $this->vData($satu['rn']);
                    $dataProvider[$newRow][13] = $this->vData($satu['industria']);
                    $dataProvider[$newRow][14] = $this->vData($satu['institucion']);
                    $dataProvider[$newRow][15] = $this->vData($modelpcrc->name);
                    $dataProvider[$newRow][16] = $this->vData($modelcliente->name);
                    $dataProvider[$newRow][17] = $this->vData($satu['tipo_servicio']);
                    $dataProvider[$newRow][18] = $this->vData($satu['pregunta1']);
                    $dataProvider[$newRow][19] = $this->vData($satu['pregunta2']);
                    $dataProvider[$newRow][20] = $this->vData($satu['pregunta3']);
                    $dataProvider[$newRow][21] = $this->vData($satu['pregunta4']);
                    $dataProvider[$newRow][22] = $this->vData($satu['pregunta5']);
                    $dataProvider[$newRow][23] = $this->vData($satu['pregunta6']);
                    $dataProvider[$newRow][24] = $this->vData($satu['pregunta7']);
                    $dataProvider[$newRow][25] = $this->vData($satu['pregunta8']);
                    $dataProvider[$newRow][26] = $this->vData($satu['pregunta9']);
                    $dataProvider[$newRow][27] = $this->vData($satu['pregunta10']);
                    $dataProvider[$newRow][28] = $this->vData($satu['connid']);
                    $dataProvider[$newRow][29] = $this->vData($satu['tipo_encuesta']);
                    $dataProvider[$newRow][30] = $this->vData($satu['comentario']);
                    $dataProvider[$newRow][33] = $this->vData($satu['tipologia']);
                    $dataProvider[$newRow][34] = $this->vData($satu['estado']);
                    $dataProvider[$newRow][35] = $this->vData($satu['llamada']);
                    $dataProvider[$newRow][36] = $this->vData($satu['buzon']);
                    $dataProvider[$newRow][37] = $this->vData($satu['responsable']);
                    $dataProvider[$newRow][38] = '-';
                    $tmpCont = implode("|", $dataProvider[$newRow]);
                    $filecontent = str_replace(array("\r\n"), ' ', $tmpCont);
                    $objPHPexcel->getActiveSheet()->setCellValue('A' . $fila, $filecontent);
                    $fila++;
                    $newRow++;
                }
                $export = true;
            } /*else {
                $export = false;
            }*/
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

        return $export;
    }

    public function getTextosPreguntas() {

        $sql = " SELECT t.id, t.detexto as 'titulo' FROM tbl_textos t";
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

}
