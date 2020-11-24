<?php

namespace app\generators\model;

use app\generators\params\GiiHelper;
use Yii;
use yii\gii\CodeFile;
use yii\helpers\ArrayHelper;

/**
 * This generator will generate one or multiple ActiveRecord classes for the specified database table.
 *
 * @author Fredy Nurman Saleh <email@fredyns.net>
 */
class Generator extends \yii\gii\Generator
{
    /**
     * {@inheritdoc}
     */
    public $templates = [
        'template' => '@app/generators/model/template',
    ];

    /**
     * {@inheritdoc}
     */
    public $template = 'template';

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'My Model';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'My generator generates an ActiveRecord class and base class for the specified database table.';
    }

    /**
     * {@inheritdoc}
     */
    public function requiredTemplates()
    {
        return ['model.php'];
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $paramsFolder = Yii::getAlias('@app') . '/config/gii/';
        $files = [];
        Yii::debug('info', "scandir: {$paramsFolder}");
        foreach (scandir($paramsFolder) as $paramsFile) {
            $tableName = str_replace('.php', '', $paramsFile);
            $params = GiiHelper::readParams($tableName);
            if (empty($params)) {
                Yii::debug('info', "params file {$paramsFile} is empty");
                continue;
            }
            Yii::debug('info', "reading params from {$paramsFile}");
            $this->enableI18N = ArrayHelper::getValue($params, 'i18n', false);
            $this->messageCategory = ArrayHelper::getValue($params, 'messageCategory', "models");
            $filePath = GiiHelper::pathForClass($params['modelNamespace']) . "/" . $params['modelClassName'] . ".php";
            $files[] = new CodeFile($filePath, $this->render('model.php', $params));
            Yii::debug('info', "generating file: {$filePath}");
        }

        return $files;
    }

}