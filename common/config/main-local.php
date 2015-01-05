<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=yii2advanced',
            'username' => 'root',
            'password' => 'langtu',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',
                'username' => 'root@phpsyntax.com',
                'password' => 'halieutinh!2',
                'port' => '587',
                'encryption' => 'tls'
            ],
        ],
        'assetManager' => [
            'bundles' => [
                'yii\bootstrap\BootstrapAsset' => [
                    'basePath' => '@webroot',
                    'baseUrl' => '@web',
                    'css' => [
                        'css/bootstrap.css'
                    ]
                ]
            ]
        ],
        'oauth' => require(__DIR__ . '/authclient.php')
    ],
];
