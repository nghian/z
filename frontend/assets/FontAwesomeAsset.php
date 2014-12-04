<?php
/**
 * Created by PhpStorm.
 * User: TruongNhat
 * Date: 11/20/2014
 * Time: 4:45 PM
 */

namespace frontend\assets;

use yii\web\AssetBundle;

class FontAwesomeAsset extends AssetBundle
{
    public $sourcePath = '@bower/font-awesome';
    public $css = ['css/font-awesome.css'];
    public $depends = [
        'yii\bootstrap\BootstrapAsset'
    ];
} 