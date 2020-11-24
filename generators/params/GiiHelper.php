<?php

namespace app\generators\params;

use fredyns\stringcleaner\StringCleaner;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

class GiiHelper
{
    /**
     * @param string $table name
     * @return string
     */
    public static function getParamsPath($table)
    {
        return Yii::getAlias('@app') . '/config/gii/' . $table . '.php';
    }

    /**
     * @param string $tableName
     * @return array
     */
    public static function readParams($tableName)
    {
        $filePath = static::getParamsPath($tableName);
        Yii::debug('info', "load params from {$filePath}");

        if (!file_exists($filePath)) {
            Yii::debug('error', "params file {$filePath} not found");
            return [];
        }

        $params = require $filePath;
        if (empty($params) or !is_array($params)) {
            Yii::debug('error', "params file {$filePath} unreadable");
            return [];
        }

        // formatting
        $tableSchema = Yii::$app->db->getTableSchema($tableName);
        $params['tableSchema'] = $tableSchema;
        $params['isSoftDelete'] = ($tableSchema->getColumn('is_deleted') !== null);
        $params['moduleNamespace'] = ($params['moduleId'] === 'app') ? 'app' : "app\\modules\\{$params['moduleId']}";

        if (isset($params['modelClassName'])) {
            $params['modelSlug'] = Inflector::camel2id($params['modelClassName'], '-', true);
        } else {
            $params['modelSlug'] = null;
        }

        $prefixes = ['model', 'search', 'form', 'controller'];
        foreach ($prefixes as $prefix) {
            $paramName = $prefix . "Class";
            $params[$prefix . "Class"] = false;
            if (isset($params[$prefix . "Namespace"]) && isset($params[$prefix . "ClassName"])) {
                if (!empty($params[$prefix . "Namespace"]) && !empty($params[$prefix . "ClassName"])) {
                    $params[$prefix . "Class"] = $params[$prefix . "Namespace"] . "\\" . $params[$prefix . "ClassName"];
                }
            }
        }

        return $params;
    }

    /**
     * @param $class or class name
     * @return string
     */
    public static function pathForClass($class)
    {
        Yii::debug('info', "ensure directory for: {$class}");

        $namespaceSeparator = "\\";
        $class = ltrim($class, $namespaceSeparator);

        if (DIRECTORY_SEPARATOR !== $namespaceSeparator) {
            $class = str_replace($namespaceSeparator, DIRECTORY_SEPARATOR, $class);
        }

        // trim 'app' prefix
        $toTrim = 'app' . DIRECTORY_SEPARATOR;
        $trimStart = strlen($toTrim);
        $findTrim = strpos($class, $toTrim);
        if ($findTrim === 0) {
            $class = substr($class, $trimStart);
        }

        $path = Yii::getAlias('@app') . DIRECTORY_SEPARATOR . $class;
        $directory = pathinfo($path, PATHINFO_EXTENSION) ? StringHelper::dirname($path) : $path;
        if (!file_exists($directory)) {
            Yii::debug('info', "creating directory: {$directory}");
            mkdir($directory, 0775, true);
        }

        return $path;
    }

    public static function minimizeArrayCode($code)
    {
        $code = StringCleaner::cleanSpaces($code);
        $code = str_replace('[ [', '[[', $code);
        $code = str_replace(" ]", ']', $code);

        return $code;
    }

}