<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['/account/login']
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'account/authorize/<client:\w+>' => 'account/authorize',
                'user/<username:[a-z0-9\-\.]+>' => 'user/article',
                'user/<username:[a-z0-9\-\.]+>/<action:[a-z0-9\-]+>' => 'user/<action>',
                'avatar/<u:\d+>/picture.jpg' => 'avatar/picture',
                '<controller:[a-z0-9\-]+>/<id:\d+>-<slug:[a-z0-9\-]+>' => '<controller>/category',
                '<controller:[a-z0-9\-]+>/<id:\d+>-<slug:[a-z0-9\-]+>.html' => '<controller>/view',
                '<controller:[a-z0-9\-]+>/<id:\d+>-<slug:[a-z0-9\-]+>/<action:[a-z0-9\-]+>' => '<controller>/<action>'
            ]
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ],
    'modules' => [
        'redactor' => 'yii\redactor\RedactorModule'
    ],
    'params' => $params,
];
