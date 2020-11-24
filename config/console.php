<?php

$common = require __DIR__ . '/common.php';
$config = [
    'id' => 'medilab-cli',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'app\commands',
    /*
    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    */
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return \yii\helpers\ArrayHelper::merge($common, $config); // merge & overwrite sub-items with console config
