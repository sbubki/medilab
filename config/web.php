<?php
$common = require __DIR__ . '/common.php';
$config = [
    'id' => 'medilab-web',
    'basePath' => dirname(__DIR__),
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'ArO1YMVjKj3l7tmhv6AMqr0dTG80CBSN',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'session' => [
            'class' => 'yii\redis\Session',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [],
        ],
        'i18n' => [
            'translations' => [
                // fallback configuration. all missing translation would fall to this setting
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                ],
            ],
        ],
    ],
    'modules' => [
        'user' => [
            'class' => 'dektrium\user\Module',
            'enableRegistration' => true,
            'admins' => ['admin', 'fredy.ns'],
            'modelMap' => [
                'User' => 'app\models\User',
                'Profile' => 'app\models\Profile',
            ],
        ],
    ],
];

if (YII_ENV_DEV) {  // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*'],
        'generators' => [
            'params' => [
                'class' => 'app\generators\params\Generator',
            ],
            'my-model' => [
                'class' => 'app\generators\model\Generator',
            ],
            'my-crud' => [
                'class' => 'app\generators\crud\Generator',
            ],
        ],
    ];
}

return \yii\helpers\ArrayHelper::merge($common, $config); // merge & overwrite sub-items with web config
