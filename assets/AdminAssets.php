<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace lnch\users\assets;

use yii\web\AssetBundle;

/**
 * @author Tom Lynch <tom@lnch.co.uk>
 * @since 2.0
 */
class AdminAssets extends AssetBundle
{
    public $sourcePath = '@lnch/users';

    // public $publishOptions = [
    //     'only' => [
    //         'css/',
    //     ]
    // ];

    public $css = [
        'css/admin.css',
    ];
    
    public $js = [
    
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
