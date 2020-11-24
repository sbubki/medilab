<?php

use app\generators\params\GiiHelper;
use Brick\VarExporter\VarExporter;
use schmunk42\giiant\generators\crud\ModelTrait;
use yii\db\Schema;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this \yii\web\View */
/* @var $generator \app\generators\params\Generator */
/* @var $moduleId string */
/* @var $subNamespace string */
/* @var $i18n bool */
/* @var $messageCategory string */
/* @var $tableName string */
/* @var $modelNameSingular string */
/* @var $modelNamePlural string */
/* @var $modelNamespace string */
/* @var $modelClassName string */
/* @var $modelClass string */
/* @var $nameAttribute string */
/* @var $labels string[] */
/* @var $dictionaries array */
/* @var $relations array */
/* @var $groupedRules array */
/* @var $safeAttributes string[] */
/* @var $searchNamespace string */
/* @var $searchClassName string */
/* @var $dateFilters array */
/* @var $timestampFilters array */
/* @var $formNamespace string */
/* @var $formClassName string */
/* @var $controllerNamespace string */
/* @var $controllerClassName string */
/* @var $useGenericActions string */
/* @var $viewPath string */
/* @var $gridColumns string[] */
/* @var $formLayout string */

$tableSchema = Yii::$app->db->getTableSchema($tableName);
$isSoftDelete = ($tableSchema->getColumn('is_deleted') !== null);

$modelNamespaceArray = explode("\\", $modelNamespace);
if (isset($modelNamespaceArray[1]) && $modelNamespaceArray[1] === 'modules' && isset($modelNamespaceArray[2])) {
    $moduleId = $modelNamespaceArray[2];
    $subNamespace = isset($modelNamespaceArray[4]) ? $modelNamespaceArray[4] : '';
} else {
    $moduleId = 'app';
    $subNamespace = (isset($modelNamespaceArray[2])) ? $modelNamespaceArray[2] : '';
}

if (!isset($i18n) or $generator->renew) {
    $i18n = false;
}

if (!isset($messageCategory) or $generator->renew) {
    $messageCategory = ($moduleId === 'app') ? "models" : "{$moduleId}/models";
}

if (!isset($modelNameSingular) or $generator->renew) {
    $modelNameSingular = Inflector::camel2words($modelClassName);
}

if (!isset($modelNamePlural) or $generator->renew) {
    $modelNamePlural = Inflector::pluralize($modelNameSingular);
}

if (!isset($nameAttribute) or $generator->renew) {
    $nameAttribute = ModelTrait::getModelNameAttribute($modelClass);
}

if (!isset($labels) or $generator->renew) {
    $labels = $generator->generateLabels($tableSchema);
}

if (!isset($dictionaries) or $generator->renew) {
    $dictionaries = $generator->getMatchingDictionaries($tableSchema);
}

if (!isset($groupedRules) or $generator->renew) {
    $groupedRules = $generator->getGroupedRules($tableSchema, $dictionaries);
}

if (!isset($relations) or $generator->renew) {
    $relations = $generator->getRelations($tableName);
}
$relationCodes = str_replace("\n", "\n    ", VarExporter::export($relations));
$search = <<<CODE
'foreignKey' => [
                    
CODE;
$replace = <<<CODE
'foreignKey' => [
CODE;
$relationCodes = str_replace($search, $replace, $relationCodes);
$search = <<<CODE
'
                ]
            ]
CODE;
$replace = <<<CODE
']
            ]
CODE;
$relationCodes = str_replace($search, $replace, $relationCodes);
$search = <<<CODE

                ],
                'hasSoftDelete'
CODE;
$replace = <<<CODE
],
                'hasSoftDelete'
CODE;
$relationCodes = str_replace($search, $replace, $relationCodes);

if (!isset($safeAttributes) or $generator->renew) {
    $safeAttributes = $generator->getSafeAttributes($tableSchema);
}

if (!isset($searchNamespace) or $generator->renew) {
    $searchNamespace = "app\\lib\\{$tableName}";
}

if (!isset($searchClassName) or $generator->renew) {
    $searchClassName = "{$modelClassName}Search";
}

if (!isset($dateFilters) or !isset($timestampFilters) or $generator->renew) {
    $skipCols = ['created_at', 'updated_at', 'deleted_at'];
    $dateFilters = [];
    $timestampFilters = [];
    foreach ($tableSchema->columns as $column) {
        if (in_array($column->name, $skipCols)) {
            continue;
        }

        $isInteger = ($column->type === Schema::TYPE_INTEGER);
        $isTimestamp = (substr_compare($column->name, '_at', -3, 3, true) === 0);
        if ($isInteger && $isTimestamp) {
            $filterName = substr($column->name, 0, (strlen($column->name) - 3));
            if ($filterName) {
                $timestampFilters[$filterName] = $column->name;
                continue;
            }
        }

        $isDate = ($column->type === Schema::TYPE_DATE);
        if ($isDate) {
            if (substr_compare($column->name, '_date', -5, 5, true) === 0) {
                $filterName = substr($column->name, 0, (strlen($column->name) - 5));
            } else {
                $filterName = $column->name;
            }
            if ($filterName) {
                $dateFilters[$filterName] = $column->name;
                continue;
            }
        }
    }

}

if (!isset($formNamespace) or $generator->renew) {
    $formNamespace = "";
}

if (!isset($formClassName) or $generator->renew) {
    $formClassName = "";
}

if (!isset($controllerNamespace) or $generator->renew) {
    $controllerNamespace = ($moduleId === "app") ?
        "app\\controllers\\{$subNamespace}" :
        "app\\modules\\{$moduleId}\\controllers\\{$subNamespace}";
    $controllerNamespace = rtrim($controllerNamespace, "\\");
}

if (!isset($controllerClassName) or $generator->renew) {
    $controllerClassName = "{$modelClassName}Controller";
}

if (!isset($useGenericActions) or $generator->renew) {
    $useGenericActions = ['index', 'create', 'view', 'update', 'delete'];
    $softDeleteActions = ['index-deleted', 'index-all', 'restore'];
    if ($isSoftDelete) {
        $useGenericActions = ArrayHelper::merge($useGenericActions, $softDeleteActions);
    }
}

if (!isset($viewPath) or $generator->renew) {
    $viewPath = "@app/views";
}

if (!isset($gridColumns) or $generator->renew) {
    $gridColumns = array_slice($safeAttributes, 0, 6);
}

if (!isset($formLayout) or $generator->renew) {
    $formLayout = "horizontal";
}

echo "<?php\n";
?>
/**
 * parameters for Gii to generate model & CRUD for '<?= $tableName ?>' table
 */
return [
    // resource
    'tableName' => "<?= $tableName ?>",
    // scope
    'moduleId' => "<?= $moduleId ?>",
    'subNamespace' => "<?= $subNamespace ?>",
    // translation
    'i18n' => <?= $i18n ? "true" : "false" ?>,
    'messageCategory' => "<?= $messageCategory ?>",
    // model
    'modelNameSingular' => "<?= $modelNameSingular ?>",
    'modelNamePlural' => "<?= $modelNamePlural ?>",
    'modelNamespace' => "<?= addslashes($modelNamespace) ?>",
    'modelClassName' => "<?= $modelClassName ?>",
    'nameAttribute' => "<?= $nameAttribute ?>", <?= ($nameAttribute === 'id') ? "// ToDo: change attribute\n" : "\n" ?>
    'labels' => [
<?php foreach ($labels as $attribute => $label): ?>
        '<?= $attribute ?>' => "<?= addslashes($label) ?>",
<?php endforeach; ?>
    ],
<?php if (empty($dictionaries)): ?>
    'dictionaries' => [],
<?php else: ?>
    'dictionaries' => [
<?php foreach ($dictionaries as $attribute => $dictionary): ?>
        '<?= $attribute ?>' => \<?= $dictionary ?>::class,
<?php endforeach; ?>
    ],
<?php endif; ?>
    'groupedRules' => [
<?php foreach ($groupedRules as $groupName => $rules): ?>
<?php if (empty($rules)): ?>
        '<?= $groupName ?>' => [],
<?php else: ?>
        '<?= $groupName ?>' => [
<?php
        foreach ($rules as $rule) {
            $ruleCodes = VarExporter::export($rule, VarExporter::INLINE_NUMERIC_SCALAR_ARRAY);
            $ruleCodes = str_replace("\n", "\n            ", $ruleCodes); // indentation
            $ruleCodes = str_replace("\n                0 => ", "\n                ", $ruleCodes); // column names in rule params
            $ruleCodes = str_replace("\n                1 => ", "\n                ", $ruleCodes); // rule name in rule params
            $timeZone = Yii::$app->timeZone;
            $ruleCodes = str_replace("'timeZone' => '{$timeZone}'", "'timeZone' => Yii::\$app->timeZone", $ruleCodes); // timezone param
            $search = <<<CODE
'targetAttribute' => [
                    
CODE;
            $replace = <<<CODE
'targetAttribute' => [
CODE;
            $ruleCodes = str_replace($search, $replace, $ruleCodes);
            $search = <<<CODE
'
                ]
            ]
CODE;
            $replace = <<<CODE
']
            ]
CODE;
            $ruleCodes = str_replace($search, $replace, $ruleCodes);

            if ($dictionaries) {
                foreach ($dictionaries as $dictionaryClass) {
                    $search = "'dictionary' => '" . addslashes($dictionaryClass) . "'"; // class name as string
                    $replace = "'dictionary' => \\" . $dictionaryClass . "::class"; // IDE friendly class name
                    $ruleCodes = str_replace($search, $replace, $ruleCodes);
                }
            }
            $minimizable = ['default', 'required', 'string', 'integer', 'date', 'in', 'unique', 'safe'];
            if (in_array($rule[1], $minimizable)) {
                $ruleCodes = GiiHelper::minimizeArrayCode($ruleCodes);
            }
            echo '            ' . $ruleCodes . ",\n";
        }
?>
        ],
<?php endif; ?>
<?php endforeach; ?>
    ],
    'relations' => <?= $relationCodes ?>,
    'safeAttributes' => ['<?= implode("', '", $safeAttributes) ?>'],
    // search
    'searchNamespace' => "<?= addslashes($searchNamespace) ?>",
    'searchClassName' => "<?= $searchClassName ?>",
    'dateFilters' => <?= str_replace("\n", "\n    ", VarExporter::export($dateFilters)) ?>,
    'timestampFilters' => <?= str_replace("\n", "\n    ", VarExporter::export($timestampFilters)) ?>,
    // form model
    'formNamespace' => "<?= addslashes($formNamespace) ?>",<?= empty($formNamespace) ? " //\"" . addslashes($searchNamespace) . "\",\n" : "" ?>
    'formClassName' => "<?= $formClassName ?>",<?= empty($formClassName) ? " //\"{$modelClassName}Form\",\n" : "" ?>
    // controller
    'controllerNamespace' => "<?= addslashes($controllerNamespace) ?>",
    'controllerClassName' => "<?= $controllerClassName ?>",
    'useGenericActions' => <?= VarExporter::export($useGenericActions, VarExporter::INLINE_NUMERIC_SCALAR_ARRAY) ?>,
    // views
    'viewPath' => "<?= $viewPath ?>",
    'gridColumns' => ['<?= implode("', '", $gridColumns) ?>'],
    'formLayout' => "<?= $formLayout ?>",
];