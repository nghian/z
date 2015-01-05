<?php
namespace frontend\assets;


use yii\web\AssetBundle;

class AvatarAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = ['css/jquery.cropper.min.css'];
    public $js = [
        'js/jquery.cropper.min.js',
        'js/jquery.avatar.js'
    ];
    public $depends = ['yii\bootstrap\BootstrapPluginAsset'];
}