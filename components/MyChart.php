<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\Url;

class MyChart extends Component {

    public function FusionCharts($chart_type = '', $width = "300", $height = "250", $ID) {
        require_once 'fusion/FusionCharts_Gen.php';
        $FC = new \FusionCharts($chart_type, $width, $height, $ID);
        $FC->setSWFPath(Url::to("@web/Charts/"));
        return $FC;
    }

}

?>