<?php

namespace app\models;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\base\Exception;

/**
 * This is the model class for table "tbl_base_satisfaccion".
 *
 * @property integer $id
 * @property string $identificacion
 * @property string $nombre
 * @property string $ani
 * @property string $agente
 * @property string $agente2
 * @property integer $ano
 * @property integer $mes
 * @property integer $dia
 * @property integer $hora
 * @property string $chat_transfer
 * @property string $ext
 * @property string $rn
 * @property string $industria
 * @property string $institucion
 * @property integer $pcrc
 * @property integer $cliente
 * @property string $tipo_servicio
 * @property string $pregunta1
 * @property string $pregunta2
 * @property string $pregunta3
 * @property string $pregunta4
 * @property string $pregunta5
 * @property string $pregunta6
 * @property string $pregunta7
 * @property string $pregunta8
 * @property string $pregunta9
 * @property string $pregunta10
 * @property string $connid
 * @property string $tipo_encuesta
 * @property string $comentario
 * @property string $lider_equipo
 * @property string $coordinador
 * @property string $jefe_operaciones
 * @property string $tipologia
 * @property string $estado
 * @property string $llamada
 * @property string $buzon
 * @property string $responsable
 * @property string $usado
 * @property string $fecha_gestion
 * @property integer $id_lider_equipo
 * @property integer $lider_equipo
 * @property integer $cc_lider
 * @property string $tipo_inbox
 * @property string $responsabilidad
 * @property string $canal
 * @property string $marca
 * @property string $equivocacion

 * @property TblArbols $pcrc0
 * @property TblArbols $cliente0
 */
class BaseSatisfaccion extends \yii\db\ActiveRecord {

    public $fecha;
    public $startDate;
    public $endDate;
    public $refresh;
    public $dimension;

    const OP_AND = 'Y';
    const OP_OR = 'O';

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_base_satisfaccion';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['identificacion', 'nombre', 'ani', 'agente', 'ano', 'mes', 'dia', 'hora', 'rn', 'industria', 'institucion', 'connid'], 'required', 'on' => 'webservice'],
            [['identificacion', 'nombre', 'ani', 'agente', 'ano', 'mes', 'dia', 'hora', 'rn', 'pcrc', 'industria', 'institucion', 'connid'], 'required', 'on' => 'encuestamanual'],
            [['ano', 'mes', 'dia', 'hora', 'pcrc', 'cliente', 'id_lider_equipo', 'dimension'], 'integer'],
            [['comentario', 'estado', 'llamada', 'buzon', 'usado', 'lider_equipo', 'cc_lider', 'tipo_inbox', 'responsabilidad'
            , 'canal', 'marca', 'equivocacion'], 'string'],
            [['fecha_gestion'], 'safe'],
            [['rn'], 'string', 'max' => 2],
            [['industria', 'institucion'], 'string', 'max' => 3],
            [['tipo_encuesta'], 'string', 'max' => 4],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'identificacion' => Yii::t('app', 'Identificacion'),
            'nombre' => Yii::t('app', 'Nombre'),
            'ani' => Yii::t('app', 'Ani'),
            'agente' => Yii::t('app', 'Agente'),
            'agente2' => Yii::t('app', 'Agente2'),
            'ano' => Yii::t('app', 'Ano'),
            'mes' => Yii::t('app', 'Mes'),
            'dia' => Yii::t('app', 'Dia'),
            'hora' => Yii::t('app', 'Hora'),
            'chat_transfer' => Yii::t('app', 'Chat Transfer'),
            'ext' => Yii::t('app', 'Ext'),
            'rn' => Yii::t('app', 'Rn'),
            'industria' => Yii::t('app', 'Industria'),
            'institucion' => Yii::t('app', 'Institucion'),
            'pcrc' => Yii::t('app', 'Pcrc'),
            'cliente' => Yii::t('app', 'Cliente'),
            'tipo_servicio' => Yii::t('app', 'Tipo Servicio'),
            'pregunta1' => Yii::t('app', 'Pregunta1'),
            'pregunta2' => Yii::t('app', 'Pregunta2'),
            'pregunta3' => Yii::t('app', 'Pregunta3'),
            'pregunta4' => Yii::t('app', 'Pregunta4'),
            'pregunta5' => Yii::t('app', 'Pregunta5'),
            'pregunta6' => Yii::t('app', 'Pregunta6'),
            'pregunta7' => Yii::t('app', 'Pregunta7'),
            'pregunta8' => Yii::t('app', 'Pregunta8'),
            'pregunta9' => Yii::t('app', 'Pregunta9'),
            'pregunta10' => Yii::t('app', 'Pregunta10'),
            'connid' => Yii::t('app', 'Connid'),
            'tipo_encuesta' => Yii::t('app', 'Tipo encuesta'),
            'comentario' => Yii::t('app', 'Comentario'),
            'lider_equipo' => Yii::t('app', 'Lider Equipo'),
            'cc_lider' => Yii::t('app', 'Identificacion Equipo'),
            'coordinador' => Yii::t('app', 'Coordinador'),
            'jefe_operaciones' => Yii::t('app', 'Jefe Operaciones'),
            'tipologia' => Yii::t('app', 'Tipologia'),
            'estado' => Yii::t('app', 'Estado'),
            'llamada' => Yii::t('app', 'Llamada'),
            'buzon' => Yii::t('app', 'Buzon'),
            'responsable' => Yii::t('app', 'Responsable'),
            'usado' => Yii::t('app', 'Usado'),
            'date' => Yii::t('app', 'Fecha'),
            'fecha_gestion' => Yii::t('app', 'Fecha Gestion'),
            'id_lider_equipo' => Yii::t('app', 'ID Líder  Equipo'),
            'refresh' => Yii::t('app', 'Buscar llamadas'),
            'tipo_inbox' => Yii::t('app', 'tipo_inbox'),
            'responsabilidad' => Yii::t('app', 'Responsabilidad'),
            'canal' => Yii::t('app', 'Canal'),
            'marca' => Yii::t('app', 'Marca'),
            'equivocacion' => Yii::t('app', 'Equivocacion'),
            'dimension' => Yii::t('app', 'Dimension'),
        ];
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
    public function getCliente0() {
        return $this->hasOne(Arboles::className(), ['id' => 'cliente']);
    }

    /**
     * Retorna el listado de estados
     * @return array
     */
    public function estadosList() {
        return [
            'Abierto' => 'Abierto',
            'En Proceso' => 'En Proceso',
            'Por Contactar al cliente' => 'Por Contactar al cliente',
            'Cerrado' => 'Cerrado',
            'Escalado' => 'Escalado'
        ];
    }

    /**
     * Retorna el listado de estados
     * @return array
     */
    public function tipologiasList() {
        return \yii\helpers\ArrayHelper::map(
                        BaseSatisfaccion::find()
                                ->groupBy(['tipologia'])
                                ->all(), 'tipologia', 'tipologia');
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
            case "En Proceso":
                $spanEstado = Html::tag('span', $estado, ['class' => 'label label-info']);
                break;
            case "Por Contactar al cliente":
                $spanEstado = Html::tag('span', $estado, ['class' => 'label label-info']);
                break;
            case "Cerrado":
                $spanEstado = Html::tag('span', $estado, ['class' => 'label label-default']);
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

    public static function getCategorias($id) {
        $modelBaseSatisfaccion = BaseSatisfaccion::findOne($id);
        try {
            $sql = 'SELECT tbl_categoriagestion.name as value, tbl_categoriagestion.name as label'
                    . ' FROM tbl_categoriagestion JOIN tbl_parametrizacion_encuesta p ON p.id= id_parametrizacion '
                    . ' WHERE p.cliente =' . $modelBaseSatisfaccion->cliente . ' AND p.programa=' . $modelBaseSatisfaccion->pcrc;
            $categorias = \Yii::$app->db->createCommand($sql)->queryAll();
            $categorias[] = ["value" => "NEUTRO", "label" => "NEUTRO"];
            $categorias[] = ["value" => "CRITICA POR BUZÓN", "label" => "CRITICA POR BUZÓN"];
            $categorias[] = ["value" => "FELICITACIÓN CON BUZÓN", "label" => "FELICITACIÓN CON BUZÓN"];
            return ArrayHelper::map($categorias, 'value', 'label');
        } catch (Exception $e) {
            \Yii::error($e->getMessage(), 'exception');
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
