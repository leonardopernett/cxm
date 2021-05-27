<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\Url;

/**
 * This is the model class for table "tbl_dashboardspeechcalls".
 *
 * @property integer $iddashboardspeechcalls
 * @property integer $callId
 * @property integer $idcategoria
 * @property string $nombreCategoria
 * @property string $extension
 * @property string $login_id
 * @property string $fechallamada
 * @property integer $callduracion
 * @property string $servicio
 * @property string $fechareal
 * @property string $idredbox
 * @property string $fechacreacion
 * @property integer $anulado
 */
class Dashboardspeechcalls extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_dashboardspeechcalls';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['callId', 'idcategoria', 'callduracion', 'anulado'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['nombreCategoria', 'extension', 'fechallamada', 'servicio', 'fechareal', 'idredbox'], 'string', 'max' => 80],
            [['login_id'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'iddashboardspeechcalls' => Yii::t('app', ''),
            'callId' => Yii::t('app', ''),
            'idcategoria' => Yii::t('app', ''),
            'nombreCategoria' => Yii::t('app', ''),
            'extension' => Yii::t('app', ''),
            'login_id' => Yii::t('app', ''),
            'fechallamada' => Yii::t('app', ''),
            'callduracion' => Yii::t('app', ''),
            'servicio' => Yii::t('app', ''),
            'fechareal' => Yii::t('app', ''),
            'idredbox' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }

    public function buscarsllamadas($params1,$params2,$params3,$params4,$varcategoriass,$varidloginid,$paramscalls){
        $txtprograma = $params1;
        $txtextension = $params2;
        $txtfechainicio = $params3;
        $txtfechafin = $params4;
        $txtcategoria = $varcategoriass;        
        $txtcontieneno = $varidloginid;
        $txtllamadas = $paramscalls;

        if ($txtcontieneno == "1") {
            $query = Dashboardspeechcalls::find()
                    ->where("anulado = 0")
                    ->andwhere("servicio in ('$txtprograma')")
                    ->andwhere("fechallamada between '$txtfechainicio' and '$txtfechafin'")
                    ->andwhere("extension in ('$txtextension')")
                    ->andwhere("idcategoria in ($txtcategoria)")
                    ->groupBy("callId")
                    ->orderBy([
                              'fechallamada' => SORT_DESC
                            ]);
        }else{
            $varlistcallid = Yii::$app->db->createCommand("select callId from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$txtprograma') and fechallamada between '$txtfechainicio' and '$txtfechafin' and extension in ('$txtextension') and idcategoria in ($txtcategoria) group by callId")->queryAll();
            $txtarraylistcallid = array();
            foreach ($varlistcallid as $key => $value) {
                array_push($txtarraylistcallid, $value['callId']);
            }
            $arraycallids = implode(", ", $txtarraylistcallid);

            $query = Dashboardspeechcalls::find()
                    ->where("anulado = 0")
                    ->andwhere("servicio in ('$txtprograma')")
                    ->andwhere("fechallamada between '$txtfechainicio' and '$txtfechafin'")
                    ->andwhere("extension in ('$txtextension')")
                    ->andwhere("idcategoria in ($txtllamadas)")
                    ->andwhere("callId not in ($arraycallids)")
                    ->groupBy("callId")
                    ->orderBy([
                              'fechallamada' => SORT_DESC
                            ]);
        }

        
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $dataProvider;
    }

    public function buscarsllamadasmodel($params1,$params2,$params3,$params4,$paramscalls){
        $txtprograma = $params1;
        $txtextension = $params2;
        $txtfechainicio = $params3;
        $txtfechafin = $params4;
        $txtllamadas = $paramscalls;

        $query = Dashboardspeechcalls::find()
                    ->where("anulado = 0")
                    ->andwhere("servicio in ('$txtprograma')")
                    ->andwhere("fechallamada between '$txtfechainicio' and '$txtfechafin'")
                    ->andwhere("extension in ('$txtextension')")
                    ->andwhere("idcategoria in ('$txtllamadas')")
                    ->orderBy([
                              'fechallamada' => SORT_DESC
                            ]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $dataProvider;
    }

}