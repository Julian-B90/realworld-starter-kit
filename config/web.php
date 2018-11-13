<?php

use app\components\ErrorHandler;
use app\modules\api\models\User;

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '2HKgd2eU-F5913edR6CBhWK7xebMH0Ii',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'response' => [
            'format' => \yii\web\Response::FORMAT_JSON,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => User::class,
            'enableAutoLogin' => false,
            'enableSession' => false,
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
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
        'db' => require(__DIR__ . '/db.php'),
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => false,
            'showScriptName' => false,
            'rules' => require(__DIR__ . '/rules.php'),
        ],
        'formatter' => [
            'datetimeFormat' => 'php:Y-m-d\TH:i:s.000\Z',
        ],
        'jwt' => [
            'class' => \sizeg\jwt\Jwt::class,
            'key' => 'secret',
            'supportedAlgs' => [
                'HS256' => 'Lcobucci\JWT\Signer\Hmac\Sha256',
            ]
        ],
        'errorHandler' => [
            'class' => ErrorHandler::class,
            'errorAction' => '//error/index'
        ]
    ],
    'modules' => [
        'api' => ['class' => \app\modules\api\Module::class],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.*', '172.17.0.*', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.*', '::1'],
    ];
}

return $config;
