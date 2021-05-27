<?php

namespace app\models;

use Yii;
use yii\helpers\Url;
use yii\db\Query;

/**
 * This is the model class for table "tbl_tmpspeechcalls".
 *
 * @property integer $idtmpdashservicio
 * @property integer $callId
 * @property integer $idcategoria
 * @property string $nombreCategoria
 * @property string $extension
 * @property string $fechallamada
 * @property integer $callduracion
 * @property string $servicio
 * @property string $fechacreacion
 * @property integer $anulado
 * @property integer $usua_id
 */
class DashboardtmpSpeech extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_tmpspeechcalls';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['callId', 'idcategoria', 'callduracion', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['nombreCategoria', 'extension', 'fechallamada', 'servicio'], 'string', 'max' => 80]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idtmpdashservicio' => Yii::t('app', ''),
            'callId' => Yii::t('app', ''),
            'idcategoria' => Yii::t('app', ''),
            'nombreCategoria' => Yii::t('app', ''),
            'extension' => Yii::t('app', ''),
            'fechallamada' => Yii::t('app', ''),
            'callduracion' => Yii::t('app', ''),
            'servicio' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }

    public function GenerarReporteSpeech($param1,$param2,$param3,$param4){
        $sessiones = Yii::$app->user->identity->id;
        $varParam1 = $param1;
        $varParam2 = $param2;
        $varParam3 = $param3;
        $varParam4 = $param4;

        $FechaActual = date("Y-m-d");

        $varIndicador = "Indicador";
        $varVariable = "Variable";   
        $varMotivo = "Detalle motivo contacto"; 

        $txtConteo = Yii::$app->db->createCommand("select distinct count(*) from tbl_dashboardcategorias where clientecategoria like '%$varParam3%' and tipocategoria like '%$varMotivo%' and anulado = 0")->queryScalar(); 

        $phpExc = new \PHPExcel();

        $phpExc->getProperties()
                        ->setCreator("Konecta")
                        ->setLastModifiedBy("Konecta")
                        ->setTitle("Dashboard Speech General - ".$varParam3." -")
                        ->setSubject("Dashboard Speech General - ".$varParam3." -")
                        ->setDescription("Este archivo contiene el proceso de las comparaciones con las categorias y las llamadas en Speech,")
                        ->setKeywords("Dashboard Speech General - ".$varParam3." -");
        $phpExc->setActiveSheetIndex(0);

        $numCell = 1;
                $phpExc->getActiveSheet()->setCellValue('A'.$numCell, 'FechaLlamada');
                $phpExc->getActiveSheet()->setCellValue('B'.$numCell, 'Login_ID');
                $phpExc->getActiveSheet()->setCellValue('C'.$numCell, 'CALLID');                
                $lastColumn = 'C';
                if ($txtConteo != 0) {
                    $dataList = Yii::$app->db->createCommand("select distinct nombre from tbl_dashboardcategorias where clientecategoria like '%$varParam3%' and tipocategoria in ('$varVariable','$varMotivo') and anulado = 0")->queryAll();
                }else{
                    $dataList = Yii::$app->db->createCommand("select distinct nombre from tbl_dashboardcategorias where clientecategoria like '%$varParam3%' and tipocategoria in ('$varVariable') and anulado = 0")->queryAll();
                }

                foreach ($dataList as $key => $value) {
                    $lastColumn++;
                    $vartxtDataList = $value['nombre'];
                    $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $vartxtDataList);                    
                }
                $numCell = $numCell++ + 1;

         if ($txtConteo != 0) {
            $txtQuery   =  new Query;
            $txtQuery   ->select(['tbl_tmpspeechcalls.callId','tbl_tmpspeechcalls.extension','tbl_tmpspeechcalls.fechallamada'])->distinct()
                        ->from('tbl_tmpspeechcalls')
                        ->join('LEFT OUTER JOIN', 'tbl_dashboardcategorias',
                                'tbl_tmpspeechcalls.idcategoria = tbl_dashboardcategorias.idcategoria')
                        ->where(['like','tbl_tmpspeechcalls.servicio', $varParam3])
                        ->andwhere(['like','tbl_dashboardcategorias.clientecategoria', $varParam3])
                        ->andwhere('tbl_tmpspeechcalls.usua_id ='.$sessiones.'')
                        ->andwhere(['in','tbl_dashboardcategorias.tipocategoria',[$varVariable, $varMotivo]])
                        ->andwhere(['between','tbl_tmpspeechcalls.fechallamada', $varParam2, $varParam1]);                    
            $command = $txtQuery->createCommand();
            $dataListCall = $command->queryAll(); 
        }else{
            $txtQuery   =  new Query;
            $txtQuery   ->select(['tbl_tmpspeechcalls.callId','tbl_tmpspeechcalls.extension','tbl_tmpspeechcalls.fechallamada'])->distinct()
                        ->from('tbl_tmpspeechcalls')
                        ->join('LEFT OUTER JOIN', 'tbl_dashboardcategorias',
                                'tbl_tmpspeechcalls.idcategoria = tbl_dashboardcategorias.idcategoria')
                        ->where(['like','tbl_tmpspeechcalls.servicio', $varParam3])
                        ->andwhere(['like','tbl_dashboardcategorias.clientecategoria', $varParam3])
                        ->andwhere('tbl_tmpspeechcalls.usua_id ='.$sessiones.'')
                        ->andwhere(['in','tbl_dashboardcategorias.tipocategoria',[$varVariable]])
                        ->andwhere(['between','tbl_tmpspeechcalls.fechallamada', $varParam2, $varParam1]);                    
            $command = $txtQuery->createCommand();
            $dataListCall = $command->queryAll(); 
        }      

        foreach ($dataListCall as $key => $value) {
            $txtCallId = $value['callId']; 
            $txtFechaCalls = $value['fechallamada'];
            $txtExtension = $value['extension'];

            $phpExc->getActiveSheet()->setCellValue('A'.$numCell, $txtFechaCalls);
            $phpExc->getActiveSheet()->setCellValue('B'.$numCell, $txtExtension);      
            $phpExc->getActiveSheet()->setCellValue('C'.$numCell, $txtCallId);          

            if ($txtConteo != 0) {
                $txtQuery2   =  new Query;
                $txtQuery2   ->select(['tbl_dashboardcategorias.idcategoria', "(select count(*) from tbl_tmpspeechcalls where tbl_tmpspeechcalls.callId = '$txtCallId' and tbl_tmpspeechcalls.fechallamada between '$varParam2' and '$varParam1' and tbl_tmpspeechcalls.usua_id = '$sessiones' and tbl_tmpspeechcalls.idcategoria = tbl_dashboardcategorias.idcategoria and tbl_tmpspeechcalls.fechacreacion = '$FechaActual') as Contar"])->distinct()
                            ->from('tbl_dashboardcategorias')
                            ->where(['like','tbl_dashboardcategorias.clientecategoria', $varParam3])
                            ->andwhere(['in','tbl_dashboardcategorias.tipocategoria',[$varVariable, $varMotivo]])
                            ->andwhere('tbl_dashboardcategorias.anulado = 0');                    
                $command2 = $txtQuery2->createCommand();
                $dataListCall2 = $command2->queryAll();                
            }else{
                $txtQuery2   =  new Query;
                $txtQuery2   ->select(['tbl_dashboardcategorias.idcategoria', "(select count(*) from tbl_tmpspeechcalls where tbl_tmpspeechcalls.callId = '$txtCallId' and tbl_tmpspeechcalls.fechallamada between '$varParam2' and '$varParam1' and tbl_tmpspeechcalls.usua_id = '$sessiones' and tbl_tmpspeechcalls.idcategoria = tbl_dashboardcategorias.idcategoria and tbl_tmpspeechcalls.fechacreacion = '$FechaActual') as Contar"])->distinct()
                            ->from('tbl_dashboardcategorias')
                            ->where(['like','tbl_dashboardcategorias.clientecategoria', $varParam3])
                            ->andwhere(['in','tbl_dashboardcategorias.tipocategoria',[$varVariable]])
                            ->andwhere('tbl_dashboardcategorias.anulado = 0');                    
                $command2 = $txtQuery2->createCommand();
                $dataListCall2 = $command2->queryAll();                  
            }



            $lastColumn1 = 'C';
            foreach ($dataListCall2 as $key => $value) {
                $txtCuentaRta = $value['Contar'];
                $lastColumn1++;
                $phpExc->getActiveSheet()->setCellValue($lastColumn1.$numCell, $txtCuentaRta);
            }

	    $numCell++;
        } 	
        $numCell = $numCell + 2;  


        $hoy = getdate();
        $hoy = $hoy['year']."_".$hoy['month']."_".$hoy['mday']."_".$hoy['hours']."_".$hoy['minutes'];
              
        $objWriter = \PHPExcel_IOFactory::createWriter($phpExc, 'Excel5');
                
        $tmpFile = tempnam(sys_get_temp_dir(), $hoy);
        $tmpFile.= ".xls";

        $objWriter->save($tmpFile);

        $message = "<html><body>";
        $message .= "<h3>Se ha realizado el envio correcto de las valoraciones.</h3>";
        $message .= "</body></html>";

        // var_dump($sessiones);
        // var_dump($varParam3);
        // var_dump($varParam1);
        // var_dump($varParam2);
        // var_dump($dataListCall);

        Yii::$app->mailer->compose()
                        ->setTo($varParam4)
                        ->setFrom(Yii::$app->params['email_satu_from'])
                        ->setSubject("Ejemplo Speech")
                        ->attach($tmpFile)
                        ->setHtmlBody($message)
                        ->send();

        $rtaenvio = 1;
                die(json_encode($rtaenvio));
    }
}