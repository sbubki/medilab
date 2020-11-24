<?php

namespace app\generators\params;

use Yii;
use yii\base\NotSupportedException;
use yii\db\mysql\ColumnSchema;
use yii\db\Schema;
use yii\gii\CodeFile;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * Generate parameters for Gii
 *
 * @package app\generators\params
 * @author Fredy Nurman Saleh <email@fredyns.net>
 */
class Generator extends \schmunk42\giiant\generators\model\Generator
{
    public $renew = false;
    public $templates = [
        'template' => '@app/generators/params/template',
    ];
    public $template = 'template';
    public $tableName = '*';
    public $modelClasses = [];
    public $allParams = [];

    public function init()
    {
        parent::init();

        // default form value
        foreach ($this->getTableNames() as $tableName) {
            $this->allParams[$tableName] = GiiHelper::readParams($tableName);
            if (isset($this->allParams[$tableName]['modelNamespace']) && isset($this->allParams[$tableName]['modelClassName'])) {
                $this->modelClasses[$tableName] = $this->allParams[$tableName]['modelNamespace'] . "\\"
                    . $this->allParams[$tableName]['modelClassName'];
            } else {
                $this->modelClasses[$tableName] = "app\\models\\" . Inflector::id2camel($tableName, '_');
            }
        }

        // sorting
        ksort($this->modelClasses);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Gii Params';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Prepare parameters for custom Gii code generator.';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['modelClasses', 'each', 'rule' => ['string']],
            ['renew', 'safe']
        ];
    }

    /**
     * all form fields for saving in saved forms.
     *
     * @return array
     */
    public function formAttributes()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function requiredTemplates()
    {
        return ['params.php'];
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $files = [];
        foreach ($this->modelClasses as $tableName => $modelClass) {
            $this->allParams[$tableName]['tableName'] = $tableName;
            $this->allParams[$tableName]['modelClass'] = $modelClass;
            $this->allParams[$tableName]['modelNamespace'] = StringHelper::dirname($modelClass);
            $this->allParams[$tableName]['modelClassName'] = StringHelper::basename($modelClass);
            $filePath = GiiHelper::getParamsPath($tableName);
            $files[] = new CodeFile($filePath, $this->render('params.php', $this->allParams[$tableName]));
        }

        return $files;
    }

    /**
     * {@inheritdoc}
     */
    public function getTableNames()
    {
        parent::getTableNames();

        if ($this->tableName === '*') {
            $skipTables = [
                // Yii tables
                'migration', 'yii_session',
                // uploaded file
                'uploaded_file',
                // user extension
                'user', 'profile', 'social_account', 'token',
                // rbac
                'auth_assignment', 'auth_item', 'auth_item_child', 'auth_rule',
                // menu table
                'menu',
            ];

            foreach ($skipTables as $skipTable) {
                $key = array_search($skipTable, $this->tableNames);
                if ($key !== FALSE) {
                    unset($this->tableNames[$key]);
                }
            }
        }

        return $this->tableNames;
    }

    /**
     * Generates the attribute labels for the specified table.
     * @param \yii\db\TableSchema $table the table schema
     * @return array the generated attribute labels (name => label)
     */
    public function generateLabels($table)
    {
        $labels = [];
        foreach ($table->columns as $column) {
            if (!strcasecmp($column->name, 'id')) {
                $labels[$column->name] = 'ID';
            } else {
                $label = Inflector::camel2words($column->name); // generate label
                if (!empty($label) && substr_compare($label, ' id', -3, 3, true) === 0) {
                    $label = substr($label, 0, -3); // remove suffix (ID)
                }
                $labels[$column->name] = $label;
            }
        }

        return $labels;
    }

    public function getRelations($tableName)
    {
        if (empty($this->_allRelations)) {
            $this->_allRelations = $this->generateRelations();
        }

        return isset($this->_allRelations[$tableName]) ? $this->_allRelations[$tableName] : [];
    }

    private $_allRelations;

    public function generateRelations()
    {
        $db = $this->getDbConnection();
        $relations = [];
        $schemaNames = $this->getSchemaNames();
        foreach ($schemaNames as $schemaName) {
            foreach ($db->getSchema()->getTableSchemas($schemaName) as $originTableSchema) {
                /* @var $originTableSchema \yii\db\TableSchema */
                foreach ($originTableSchema->foreignKeys as $dbmsRelationInfo) {
                    $relatedTableName = ArrayHelper::remove($dbmsRelationInfo, 0);
                    /* @var $relatedTableSchema \yii\db\TableSchema */
                    $relatedTableSchema = $db->getTableSchema($relatedTableName);

                    if ($relatedTableSchema === null) {
                        // Foreign key could point to non-existing table: https://github.com/yiisoft/yii2-gii/issues/34
                        // skip if not mentioned in metadata
                        continue;
                    }

                    /**
                     * menentukan class & namespace
                     */
                    $originModelClass = $this->getModelClass($originTableSchema->fullName);
                    $originModelNameSpace = StringHelper::dirname($originModelClass);
                    $originModelClassName = StringHelper::basename($originModelClass);
                    $relatedModelClass = $this->getModelClass($relatedTableSchema->fullName);
                    $relatedModelNameSpace = StringHelper::dirname($relatedModelClass);
                    $relatedModelClassName = StringHelper::basename($relatedModelClass);

                    /**
                     * mulai generate sesuai definisi FK tabel
                     */
                    $foreignKeys = array_keys($dbmsRelationInfo);
                    $foreignKey = $foreignKeys[0];

                    /**
                     * menambahkan info-relasi dr tabel utama ke tabel relasi
                     * misal: tabel transaksi hasOne klien
                     */
                    $relationName = $this->generateRelationName($relations, $originTableSchema, $foreignKey, false);
                    $originalAlias = $this->isNeedAlias(false, $relatedTableName, $foreignKey);

                    $originalRelation = [
                        'alias' => $originalAlias,
                        'namespace' => $relatedModelNameSpace,
                        'className' => $relatedModelClassName,
                        'foreignKey' => array_flip($dbmsRelationInfo),

                    ];
                    $relations[$originTableSchema->fullName]['hasOne'][$relationName] = $originalRelation;

                    /**
                     * menambahkan relasi kebalikannya
                     * misal: tabel klien hasMany transaksi
                     */
                    $hasMany = $this->isHasManyRelation($originTableSchema, $foreignKeys);
                    $hasWhat = $hasMany ? 'hasMany' : 'hasOne';
                    $relatedRelationName = $this->generateRelationName($relations, $relatedTableSchema, $originModelClassName, $hasMany, $foreignKey);
                    $relatedAlias = $this->isNeedAlias($hasMany, $relatedTableName, $foreignKey);

                    $relatedRelation = [
                        'alias' => $relatedAlias,
                        'namespace' => $originModelNameSpace,
                        'className' => $originModelClassName,
                        'foreignKey' => $dbmsRelationInfo,
                        'hasSoftDelete' => ($relatedTableSchema->getColumn('is_deleted') !== null)
                    ];
                    $relations[$relatedTableSchema->fullName][$hasWhat][$relatedRelationName] = $relatedRelation;
                }

                if (($junctionFks = $this->checkJunctionTable($originTableSchema)) === false) {
                    continue;
                }

                // ToDo: extend implementation
                $rel = $this->generateManyManyRelations($originTableSchema, $junctionFks, $relations);

                foreach ($rel as $tableName => $junctionList) {
                    foreach ($junctionList as $junctionName => $junction) {
                        $relations[$tableName]['hasJunction'][$junctionName] = $junction;
                    }
                }
            }
        }

        return $relations;
    }

    /**
     * {@inheritdoc}
     */
    public function generateRelationName($relations, $table, $key, $multiple, $fk = NULL)
    {
        if ($multiple && $fk) {
            if (strcasecmp($fk, 'id')) {
                if (substr_compare($fk, 'id', -2, 2, true) === 0) {
                    $fk = rtrim(substr($fk, 0, -2), '_');
                } elseif (substr_compare($fk, 'id', 0, 2, true) === 0) {
                    $fk = ltrim(substr($fk, 2, strlen($fk)), '_');
                }
            }

            if (strpos($table->fullName, $fk) === FALSE) {
                // shall generate something like 'BooksAsAuthor'
                return Inflector::id2camel(Inflector::pluralize($key) . '_as_' . $fk, '_');
            }
        }

        // else use default generator
        return parent::generateRelationName($relations, $table, $key, $multiple);
    }

    /**
     * @param $tableName
     * @return mixed|null
     * @throws \Exception
     */
    public function getModelClass($tableName)
    {
        $default = $this->ns . "\\" . $this->generateClassName($tableName);
        return ArrayHelper::getValue($this->modelClasses, $tableName, $default);
    }

    public function isNeedAlias($hasMany, $refTable, $fk)
    {
        if ($hasMany) {
            return FALSE;
        }

        $tablename = strtolower($refTable);
        $key = strtolower($fk);

        if (!empty($key) && strcasecmp($key, 'id')) {
            if (substr_compare($key, 'id', -2, 2, true) === 0) {
                $key = rtrim(substr($key, 0, -2), '_');
            } elseif (substr_compare($key, 'id', 0, 2, true) === 0) {
                $key = ltrim(substr($key, 2, strlen($key)), '_');
            }
        }

        return ($tablename !== $key);
    }

    /**
     * @inheritdoc
     */
    public function generateManyManyRelations($table, $fks, $relations)
    {
        $db = $this->getDbConnection();
        $rel = [];

        foreach ($fks as $pair) {
            list($firstKey, $secondKey) = $pair;
            $table0 = $firstKey[0];
            $table1 = $secondKey[0];
            unset($firstKey[0], $secondKey[0]);

            $table0Schema = $db->getTableSchema($table0);
            $table1Schema = $db->getTableSchema($table1);

            // @see https://github.com/yiisoft/yii2-gii/issues/166
            if ($table0Schema === null || $table1Schema === null) {
                continue;
            }

            /**
             * menentukan class & namespace
             */
            $modelClass_0 = $this->getModelClass($table0);
            $modelNameSpace_0 = StringHelper::dirname($modelClass_0);
            $modelClassName_0 = StringHelper::basename($modelClass_0);
            $modelClass_1 = $this->getModelClass($table1);
            $modelNameSpace_1 = StringHelper::dirname($modelClass_1);
            $modelClassName_1 = StringHelper::basename($modelClass_1);
            $isSameNameSpace = ($modelNameSpace_0 == $modelNameSpace_1);
            $modelMention_0 = $isSameNameSpace ? $modelClassName_0 : "\\" . $modelClass_0;
            $modelMention_1 = $isSameNameSpace ? $modelClassName_1 : "\\" . $modelClass_1;

            $link0 = $this->generateRelationLink(array_flip($secondKey));
            $viaLink0 = $this->generateRelationLink($firstKey);
            $relationName0 = $this->generateRelationName($relations, $table0Schema, key($secondKey), true);

            $rel[$table0Schema->fullName][$relationName0] = [
                'alias' => FALSE,
                'namespace' => $modelNameSpace_1,
                'className' => $modelClassName_1,
                'query' => "return \$this->hasMany($modelMention_1::class, $link0)"
                    . "->viaTable('" . $this->generateTableName($table->name) . "', $viaLink0);",
            ];

            $link1 = $this->generateRelationLink(array_flip($firstKey));
            $viaLink1 = $this->generateRelationLink($secondKey);
            $relationName1 = $this->generateRelationName($relations, $table1Schema, key($firstKey), true);

            $rel[$table1Schema->fullName][$relationName1] = [
                'alias' => FALSE,
                'namespace' => $modelNameSpace_0,
                'className' => $modelClassName_0,
                'query' => "return \$this->hasMany($modelMention_0::class, $link1)->viaTable('"
                    . $this->generateTableName($table->name) . "', $viaLink1);",
            ];
        }

        return $rel;
    }

    /**
     * @param \yii\db\TableSchema $tableSchema
     */
    public function getSafeAttributes($tableSchema)
    {
        $skipAttributes = ['id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'is_deleted', 'deleted_at', 'deleted_by'];

        return array_diff($tableSchema->columnNames, $skipAttributes);
    }

    /**
     * Generates validation rules for the specified table and add enum value validation.
     *
     * @param \yii\db\TableSchema $table the table schema
     * @param array $dictionaries
     * @return array[]
     * @throws \Exception
     */
    public function getGroupedRules($tableSchema, $dictionaries = [])
    {
        $types = [];
        $lengths = [];
        $stringcols = [];
        foreach ($tableSchema->columns as $column) {
            if ($this->isNoRule($column)) {             //--- replaced with own function
                continue;
            }
            if (!$column->allowNull && $column->defaultValue === null) {
                $types['required'][] = $column->name;
            }
            switch ($column->type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                    if (substr_compare($column->name, '_at', -3, 3, true) === 0) {
                        $types['timestamp'][] = $column->name;
                        break;
                    }
                case Schema::TYPE_BIGINT:
                case Schema::TYPE_TINYINT:
                    $types['integer'][] = $column->name;
                    break;
                case Schema::TYPE_BOOLEAN:
                    $types['boolean'][] = $column->name;
                    break;
                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                    $types['number'][] = $column->name;
                    break;
                case Schema::TYPE_DATE:
                    $types['date'][] = $column->name;
                    break;
                case Schema::TYPE_TIME:
                    $types['time'][] = $column->name;
                    break;
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                    $types['datetime'][] = $column->name;
                    break;
                case Schema::TYPE_JSON:
                    $types['safe'][] = $column->name;
                    break;
                default: // strings
                    $stringcols[] = $column->name;
                    if ($column->size > 0) {
                        $lengths[$column->size][] = $column->name;
                    } else {
                        $types['string'][] = $column->name;
                    }
            }
        }

        $groups = [
            'filter' => [],
            'default' => [],
            'required' => [],
            'type' => [],
            'format' => [],
            'restriction' => [],
            'constraint' => [],
            'safe' => [],
        ];

        if ($stringcols) {
            $groups['filter'][] = [
                $stringcols,
                "\\fredyns\\stringcleaner\\yii2\\PlaintextValidator",
            ];
        }

        $driverName = $this->getDbDriverName();
        foreach ($types as $type => $columns) {
            if ($driverName === 'pgsql' && $type === 'integer') {
                $groups['default'][] = [$columns, 'default', 'value' => null];
            }

            if ($type === 'timestamp') {
                foreach ($columns as $columnName) {
                    $groups['format'][] = [
                        $columnName,
                        'date',
                        'format' => 'yyyy-MM-dd HH:mm:ss',
                        'timeZone' => Yii::$app->timeZone,
                        'timestampAttribute' => $columnName,
                        'timestampAttributeTimeZone' => 'UTC',
                        'when' => function ($model, $attribute) {
                            return !is_numeric($model->{$attribute});
                        },
                    ];
                }
            } else if ($type === 'datetime') {
                $groups['format'][] = [$columns, 'date', 'format' => 'yyyy-MM-dd HH:mm:ss'];
            } else if ($type === 'date') {
                $groups['format'][] = [$columns, 'date', 'format' => 'yyyy-MM-dd'];
            } else if ($type === 'time') {
                $groups['format'][] = [$columns, 'date', 'format' => 'HH:mm'];
            } else {
                $groups['type'][] = [$columns, $type];
            }
        }
        ksort($lengths);
        foreach ($lengths as $length => $columns) {
            $groups['type'][] = [$columns, 'string', 'max' => $length];
        }

        $dictionaryGroups = [];
        if ($dictionaries) {
            foreach ($dictionaries as $attributeName => $dictionaryClass) {
                $dictionaryGroups[$dictionaryClass][] = $attributeName;
            }
        }
        if ($dictionaryGroups) {
            foreach ($dictionaryGroups as $dictionaryClass => $attributeNames) {
                $groups['restriction'][] = [$attributeNames, 'in', 'dictionary' => $dictionaryClass];
            }
        }

        $db = $this->getDbConnection();

        // Unique indexes rules
        try {
            $uniqueIndexes = array_merge($db->getSchema()->findUniqueIndexes($tableSchema), [$tableSchema->primaryKey]);
            $uniqueIndexes = array_unique($uniqueIndexes, SORT_REGULAR);
            foreach ($uniqueIndexes as $uniqueColumns) {
                // Avoid validating auto incremental columns
                if (!$this->isColumnAutoIncremental($tableSchema, $uniqueColumns)) {
                    $attributesCount = count($uniqueColumns);

                    if ($attributesCount === 1) {
                        $groups['restriction'][] = [$uniqueColumns, 'unique'];
                    } elseif ($attributesCount > 1) {
                        $groups['restriction'][] = [$uniqueColumns, 'unique', 'targetAttribute' => $uniqueColumns];
                    }
                }
            }
        } catch (NotSupportedException $e) {
            // doesn't support unique indexes information...do nothing
        }

        // Exist rules for foreign keys
        foreach ($tableSchema->foreignKeys as $dbmsRelationInfo) {
            $refTable = $dbmsRelationInfo[0];
            $refTableSchema = $db->getTableSchema($refTable);
            if ($refTableSchema === null) {
                // Foreign key could point to non-existing table: https://github.com/yiisoft/yii2-gii/issues/34
                continue;
            }
            unset($dbmsRelationInfo[0]);

            /**
             * menentukan class & namespace
             */
            $originModelClass = $this->getModelClass($tableSchema->fullName);
            $originModelNameSpace = StringHelper::dirname($originModelClass);
            $relatedModelClass = $this->getModelClass($refTable);
            $relatedModelNameSpace = StringHelper::dirname($relatedModelClass);
            $relatedModelClassName = StringHelper::basename($relatedModelClass);
            $isSameNameSpace = ($originModelNameSpace == $relatedModelNameSpace);
            $relatedModelMention = $isSameNameSpace ? $relatedModelClassName : "\\" . $relatedModelClass;
            $attributes = array_keys($dbmsRelationInfo);
            $groups['constraint'][] = [
                $attributes,
                'exist',
                'skipOnError' => true,
                'targetClass' => $relatedModelMention,
                'targetAttribute' => $dbmsRelationInfo,
            ];
        }

        return $groups;
    }

    /**
     * check whether a column skip the rule generator
     * @param \yii\db\ColumnSchema $column
     * @return boolean
     */
    public function isNoRule(\yii\db\ColumnSchema $column)
    {
        $skipedColums = [
            // keys
            'id', 'uid',
            // soft-delete
            'is_deleted', 'deleted_at', 'deleted_by',
            // blamable
            'created_by', 'updated_by',
            // timestamp
            'created_at', 'updated_at',
        ];

        return ($column->autoIncrement or in_array($column->name, $skipedColums));
    }

    /**
     * @param \yii\db\TableSchema $tableSchema the table schema
     * @return array
     */
    public function getMatchingDictionaries($tableSchema)
    {
        $dictionaries = [];
        foreach ($tableSchema->columns as $column) {
            $key = $column->name;
            if (empty($key) or !strcasecmp($key, 'id')) {
                continue;
            }

            if (substr_compare($key, 'id', -2, 2, true) === 0) {
                $key = rtrim(substr($key, 0, -2), '_');
            } elseif (substr_compare($key, 'id_', 0, 3, true) === 0) {
                $key = ltrim(substr($key, 3, strlen($key)), '_');
            }

            $possibleDictionary = "app\\dictionaries\\" . Inflector::camelize($key);
            if (class_exists($possibleDictionary)) {
                $dictionaries[$column->name] = $possibleDictionary;
            }
        }
        return $dictionaries;
    }
}