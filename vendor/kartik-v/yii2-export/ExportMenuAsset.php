<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014
 * @package yii2-export
 * @version 1.2.1
 */

namespace kartik\export;

//use kartik\widgets\AssetBundle;
use kartik\base\AssetBundle;

/**
 * Asset bundle for ExportMenu Widget (for export menu data)
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class ExportMenuAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('js', ['js/kv-export-data']);
        $this->setupAssets('css', ['css/kv-export-data']);
        parent::init();
    }
}