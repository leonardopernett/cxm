<?php

namespace app\models;

use Yii;
use yii\db\Query;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\base\Exception;

/**
 * This is the model class for table "tbl_basechat_tigob".
 *
 * @property integer $idbasechat_tigob
 * @property integer $idtransaccion
 * @property integer $idencuesta
 * @property string $fecha_creacion
 * @property string $invitation_status
 * @property string $survey_status
 * @property string $fecha_respuesta
 * @property string $has_alert
 * @property string $tipo_alerta
 * @property string $punto_contacto
 * @property string $unit
 * @property string $nombre_cliente
 * @property string $mercado
 * @property string $fecha_transaccion
 * @property string $telefono_cliente
 * @property string $journey
 * @property string $bu
 * @property integer $nps
 * @property string $comentario_adicional
 * @property integer $ces
 * @property integer $csat
 * @property string $comentario_fcr
 * @property string $fcr
 * @property string $comentario_adicionalfcr
 * @property string $encuesta_movil
 * @property string $id_supervisor
 * @property string $supervisor
 * @property string $id_agente
 * @property string $tipo_producto
 * @property string $team_leader
 * @property string $id_team_leader
 * @property integer $encuesta_alerta
 * @property string $forma_envio
 * @property string $encontro_buscaba
 * @property string $topicos_nps
 * @property string $b2b
 * @property integer $conocimiento
 * @property string $digital_cliente_enc
 * @property string $uno_mas_contactos
 * @property string $tipo_canal_digital
 * @property string $unidad_identificador
 * @property string $unidad_nombre
 * @property string $digital_form
 * @property string $digital_b2b
 * @property string $digital_canal
 * @property string $tipo_producto_digital
 * @property string $msisdn
 * @property string $tipo_ambiente
 * @property string $email_asesor
 * @property string $nombre_grupo
 * @property string $nombre_asesor
 * @property integer $ticked_id
 * @property string $ticket_via
 * @property string $digital_subcanal
 * @property string $digital_comentarioadic
 * @property string $tagged_original
 * @property string $evaluadoid
 * @property string $rn
 * @property string $industria
 * @property string $institucion
 * @property string $lider_equipo
 * @property integer $pcrc
 * @property integer $cliente
 * @property string $estado
 * @property string $tipologia
 * @property string $tipo_inbox
 * @property string $usado
 * @property string $responsable
 * @property integer $basesatisfaccion_id
 * @property string $fechacreacion
 * @property integer $anulado
 * @property integer $usua_id
 */
class BasechatTigo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_basechat_tigob';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idtransaccion', 'idencuesta', 'nps', 'ces', 'csat', 'encuesta_alerta', 'conocimiento', 'ticked_id', 'pcrc', 'cliente', 'basesatisfaccion_id', 'anulado', 'usua_id'], 'integer'],
            [['fecha_creacion', 'fecha_respuesta', 'fecha_transaccion', 'fechacreacion'], 'safe'],
            [['estado', 'tipo_inbox', 'usado'], 'string'],
            [['invitation_status', 'survey_status', 'has_alert', 'tipo_alerta', 'punto_contacto', 'unit', 'nombre_cliente', 'mercado', 'journey', 'id_supervisor', 'supervisor', 'id_agente', 'tipo_producto', 'team_leader', 'id_team_leader', 'forma_envio', 'encontro_buscaba', 'unidad_identificador', 'unidad_nombre', 'digital_form', 'tipo_ambiente', 'email_asesor', 'nombre_asesor', 'ticket_via', 'evaluadoid', 'lider_equipo', 'responsable'], 'string', 'max' => 100],
            [['telefono_cliente', 'bu', 'tipo_canal_digital'], 'string', 'max' => 30],
            [['comentario_adicional', 'comentario_adicionalfcr', 'topicos_nps', 'digital_cliente_enc', 'digital_comentarioadic', 'tagged_original'], 'string', 'max' => 500],
            [['comentario_fcr','imputable'], 'string', 'max' => 250],
            [['fcr', 'encuesta_movil', 'tipo_producto_digital'], 'string', 'max' => 20],
            [['b2b', 'uno_mas_contactos', 'digital_b2b'], 'string', 'max' => 5],
            [['digital_canal', 'msisdn', 'nombre_grupo', 'digital_subcanal', 'rn', 'tipologia'], 'string', 'max' => 50],
            [['industria', 'institucion'], 'string', 'max' => 3]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idbasechat_tigob' => Yii::t('app', ''),
            'idtransaccion' => Yii::t('app', ''),
            'idencuesta' => Yii::t('app', ''),
            'fecha_creacion' => Yii::t('app', ''),
            'invitation_status' => Yii::t('app', ''),
            'survey_status' => Yii::t('app', ''),
            'fecha_respuesta' => Yii::t('app', ''),
            'has_alert' => Yii::t('app', ''),
            'tipo_alerta' => Yii::t('app', ''),
            'punto_contacto' => Yii::t('app', ''),
            'unit' => Yii::t('app', ''),
            'nombre_cliente' => Yii::t('app', ''),
            'mercado' => Yii::t('app', ''),
            'fecha_transaccion' => Yii::t('app', ''),
            'telefono_cliente' => Yii::t('app', ''),
            'journey' => Yii::t('app', ''),
            'bu' => Yii::t('app', ''),
            'nps' => Yii::t('app', ''),
            'comentario_adicional' => Yii::t('app', ''),
            'ces' => Yii::t('app', ''),
            'csat' => Yii::t('app', ''),
            'comentario_fcr' => Yii::t('app', ''),
            'fcr' => Yii::t('app', 'Fcr'),
            'comentario_adicionalfcr' => Yii::t('app', ''),
            'encuesta_movil' => Yii::t('app', ''),
            'id_supervisor' => Yii::t('app', ''),
            'supervisor' => Yii::t('app', ''),
            'id_agente' => Yii::t('app', ''),
            'tipo_producto' => Yii::t('app', ''),
            'team_leader' => Yii::t('app', ''),
            'id_team_leader' => Yii::t('app', ''),
            'encuesta_alerta' => Yii::t('app', ''),
            'forma_envio' => Yii::t('app', ''),
            'encontro_buscaba' => Yii::t('app', ''),
            'topicos_nps' => Yii::t('app', ''),
            'b2b' => Yii::t('app', ''),
            'conocimiento' => Yii::t('app', ''),
            'digital_cliente_enc' => Yii::t('app', ''),
            'uno_mas_contactos' => Yii::t('app', ''),
            'tipo_canal_digital' => Yii::t('app', ''),
            'unidad_identificador' => Yii::t('app', ''),
            'unidad_nombre' => Yii::t('app', ''),
            'digital_form' => Yii::t('app', ''),
            'digital_b2b' => Yii::t('app', ''),
            'digital_canal' => Yii::t('app', ''),
            'tipo_producto_digital' => Yii::t('app', ''),
            'msisdn' => Yii::t('app', ''),
            'tipo_ambiente' => Yii::t('app', ''),
            'email_asesor' => Yii::t('app', ''),
            'nombre_grupo' => Yii::t('app', ''),
            'nombre_asesor' => Yii::t('app', ''),
            'ticked_id' => Yii::t('app', ''),
            'ticket_via' => Yii::t('app', ''),
            'digital_subcanal' => Yii::t('app', ''),
            'digital_comentarioadic' => Yii::t('app', ''),
            'tagged_original' => Yii::t('app', ''),
            'evaluadoid' => Yii::t('app', ''),
            'rn' => Yii::t('app', ''),
            'industria' => Yii::t('app', ''),
            'institucion' => Yii::t('app', ''),
            'lider_equipo' => Yii::t('app', ''),
            'pcrc' => Yii::t('app', ''),
            'cliente' => Yii::t('app', ''),
            'estado' => Yii::t('app', ''),
            'tipologia' => Yii::t('app', ''),
            'tipo_inbox' => Yii::t('app', ''),
            'usado' => Yii::t('app', ''),
            'responsable' => Yii::t('app', ''),
            'basesatisfaccion_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'imputable' => Yii::t('app', ''),
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function buscarbasechat($params1){

        $varlimitdata = (new \yii\db\Query())
            ->select(['idbasechat_tigob'])
            ->from(['tbl_basechat_tigob'])
            ->where(['=','anulado',0])
            ->orderBy(['fechacreacion' => SORT_DESC])
            ->limit(10000)
            ->All();

        $arrayListLimit = array();
        foreach ($varlimitdata as $key => $value) {
            array_push($arrayListLimit, $value['idbasechat_tigob']);
        }
        $textArrayLimit = explode(",", str_replace(array("#", "'", ";", " "), '', implode(", ", $arrayListLimit)));
        
        $query = BasechatTigo::find()
                    ->where(['anulado' => '0'])                    
                    ->andwhere(['IN','idbasechat_tigob',$textArrayLimit])
                    ->orderBy([
                              'fecha_creacion' => SORT_DESC
                            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params1) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'pcrc' => $this->pcrc,
            'id_agente' => $this->id_agente,
            'tipologia' => $this->tipologia,
            'estado' => $this->estado,
            'idencuesta' => $this->idencuesta,
            'imputable' => $this->imputable,
        ]);

        
        $query->andFilterWhere(['like', 'pcrc', $this->pcrc]);
        $query->andFilterWhere(['like', 'id_agente', $this->id_agente]);
        $query->andFilterWhere(['like', 'tipologia', $this->tipologia]);
        $query->andFilterWhere(['like', 'estado', $this->estado]);
        $query->andFilterWhere(['like', 'idencuesta', $this->idencuesta]);
        $query->andFilterWhere(['like', 'imputable', $this->imputable]);

        $txtFecha = explode(" ", $this->fecha_respuesta);
        if (count($txtFecha) > 1) {
            $varStarDate = $txtFecha[0].' 00:00:00';
            $varEndDate = $txtFecha[2].' 23:59:59';
         
            $query->andFilterWhere(['between', 'fecha_respuesta', $varStarDate, $varEndDate]);   
        }   

        return $dataProvider;

    }

    public function getnpstigo($opcion){
        $varnps = Yii::$app->db->createCommand("select nps from tbl_basechat_tigob where idbasechat_tigob = $opcion")->queryScalar();

        if ($varnps >= 9) {
            $data = "FELICITACION";
        }else{
            if ($varnps <= 6) {
                $data = "CRITICA";
            }else{
                $data = "NEUTRO";
            }
        }

        return $data;
    }

    public function getclientti($opcion){
        $data = Yii::$app->db->createCommand("select a.name from tbl_arbols a inner join tbl_basechat_tigob b on       a.id = b.cliente where b.idbasechat_tigob = $opcion")->queryScalar();

        return $data;
    }

    public function getpcrcti($opcion){
        $data = Yii::$app->db->createCommand("select a.name from tbl_arbols a inner join tbl_basechat_tigob b on       a.id = b.pcrc where b.idbasechat_tigob = $opcion")->queryScalar();

        return $data;
    }

    public function getnpstipotigo($opcion){
        $varnps = Yii::$app->db->createCommand("select nps from tbl_basechat_tigob where idbasechat_tigob = $opcion")->queryScalar();

        if ($varnps >= 9) {
            $data = "P";
        }else{
            if ($varnps <= 6) {
                $data = "D";
            }else{
                $data = "N";
            }
        }

        return $data;
    }

    public static function getCategoriaschat($id) {
        $modelBaseSatisfaccion = BasechatTigo::find()->where(['basesatisfaccion_id' => $id])->one();
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
     * Retorna el listado de estados
     * @return array
     */
    public function estadoschatList() {
        return [
            'Abierto' => 'Abierto',
            'En Proceso' => 'En Proceso',
            'Por Contactar al cliente' => 'Por Contactar al cliente',
            'Cerrado' => 'Cerrado',
            'Escalado' => 'Escalado'
        ];
    }


}