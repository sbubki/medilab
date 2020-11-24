<?php
/**
 * Application configuration shared by all test types
 */
$common = require __DIR__ . '/common.php';
$config = [
    'id' => 'medilab-tests',
    'basePath' => dirname(__DIR__),
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'language' => 'en-US',
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'pgsql:host=db;port=5432;dbname=medilab_test',
            //  'dsn' => 'mysql:host=localhost;dbname=medilab_test', // sample for MySQL
            'username' => 'postgres',
            'password' => 'pg_secret',
            'charset' => 'utf8',
            // Schema cache options (for production environment)
            //'enableSchemaCache' => true,
            //'schemaCacheDuration' => 60,
            //'schemaCache' => 'cache',
        ],
        'mailer' => [
            'useFileTransport' => true,
        ],
        'assetManager' => [
            'basePath' => __DIR__ . '/../web/assets',
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
        'user' => [
            'identityClass' => 'app\models\User',
        ],
        'request' => [
            'cookieValidationKey' => 'test',
            'enableCsrfValidation' => false,
            // but if you absolutely need it set cookie domain to localhost
            /*
            'csrfCookie' => [
                'domain' => 'localhost',
            ],
            */
        ],
    ],
];

return \yii\helpers\ArrayHelper::merge($common, $config); // merge & overwrite sub-items with test config
