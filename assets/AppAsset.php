<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/qa.css',
        'css/font-awesome/css/font-awesome.css',
    ];
    public $js = [
        'js/bootbox.min.js',
        'js/swal_plugin.js',
        'js/mains.js',
        'js/messages.js',
        'js/messages2.js',
        'js/messages3.js',
        'js/mains1.js',        
	    'js/mains2.js',
        'js/mains3.js',
        'js/messages4.js',
        'js/messages5.js',
        'js/messages6.js',
        'js/messages7.js',
        'js/messages8.js',
        'js/messages9.js',
        'js/messages10.js',
        'js/messages11.js',
        'js/messages12.js',
        'js/powerbi.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}
