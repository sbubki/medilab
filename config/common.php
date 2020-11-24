<?php
/**
 * common configuration for console & web app
 */
if (file_exists(__DIR__ . '/host.php')) {
    $hostConfig = require __DIR__ . '/host.php'; // custom host configuration, if any
} else {
    $hostConfig = [];
}

$commonConfig = [
    'id' => 'medilab',
    'name' => 'Medilab',
    'timeZone' => 'Asia/Jakarta',
    // 'language' => 'id-ID',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
        '@tests' => '@app/tests',
    ],
      'bootstrap' => [
          'log',
          //    uncomment these if using queue
          //    'queue', // The component registers its own console commands
      ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
            //  'class' => 'yii\mongodb\Cache', // sample for using mongo
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    //  'class' => 'yii\mongodb\log\MongoDbTarget', // sample for using mongo
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'pgsql:host=db;port=5432;dbname=medilab',
            //  'dsn' => 'mysql:host=localhost;dbname=medilab', // sample for MySQL
            'username' => 'postgres',
            'password' => 'pg_secret',
            'charset' => 'utf8',
            // Schema cache options (for production environment)
            //'enableSchemaCache' => true,
            //'schemaCacheDuration' => 60,
            //'schemaCache' => 'cache',
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => 'redis',
            'port' => 6379,
            'database' => 0,
        ],
        //  uncomment if using mongodb
        //  'mongodb' => [
        //    'class' => 'yii\mongodb\Connection',
        //    'dsn' => 'mongodb://mg_user:mg_secret@mongo:27017/bki_lab',
        //  ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
            // these lines are mailer sample
            //'useFileTransport' => false,
            // 'transport' => [
            //    'class' => 'Swift_SmtpTransport',
            //    'encryption' => 'tls',
            //    'host' => 'smtp.gmail.com',
            //    'port' => '587',
            //    'username' => 'your_email@gmail.com',
            //    'password' => 'your_secret',
            // ],
        ],
        //  uncomment these if using queue
        //  'queue' => [
        //      'class' => 'yii\queue\redis\Queue',
        //      'as log' => 'yii\queue\LogBehavior',
        //  ],
        //  uncomment these if using RBAC
        //  'authManager' => [
        //    'class' => 'yii\rbac\DbManager',
        //  ],
    ],
    'params' => [
        'adminEmail' => 'itsbubki@gmail.com',
        'senderEmail' => 'itsbubki@gmail.com',
        'senderName' => 'IT of BKI-SBU',
    ],
];

return \yii\helpers\ArrayHelper::merge($commonConfig, $hostConfig); // merge & overwrite sub-items with host config
