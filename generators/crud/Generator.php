<?php

namespace app\generators\crud;

use app\generators\params\GiiHelper;
use Yii;
use yii\db\Schema;
use yii\gii\CodeFile;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * This generator generates an extended version of Giiant-CRUDs.
 *
 * @author Fredy Nurman Saleh <email@fredyns.net>
 */
class Generator extends \schmunk42\giiant\generators\crud\Generator
{
    /**
     * {@inheritdoc}
     */
    public $templates = [
        'template' => '@app/generators/crud/template',
    ];

    /**
     * {@inheritdoc}
     */
    public $template = 'template';

    /**
     * @var array of available table-options to generate
     */
    public $tableOptions = [];

    /**
     * @var string[] selected table name to generate
     */
    public $tableNames = [];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        $paramsFolder = Yii::getAlias('@app') . '/config/gii/';
        $paramsFiles = FileHelper::findFiles($paramsFolder, ['only' => ['*.php'], 'recursive' => false]);
        foreach ($paramsFiles as $paramsFile) {
            $paramsFile = str_replace($paramsFolder, '', $paramsFile);
            $tableName = str_replace('.php', '', $paramsFile);
            $this->tableOptions[$tableName] = $tableName;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'My CRUD';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return "Customized CRUD generator.";
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['tableNames', 'required'],
            ['tableNames', 'each', 'rule' => ['string']],
            ['tableNames', 'each', 'rule' => ['in', 'range' => $this->tableOptions]],
        ];
    }

    public function generate()
    {
        if (empty($this->tableNames)) {
            return [];
        }

        /**
         * set custom provider
         */
        $this->providerList = $this->getMyProviderList();

        $files = [];
        foreach ($this->tableNames as $tableName) {
            $params = $this->generateParams($tableName);
            if (empty($params)) {
                continue;
            }

            if (!empty($params['formClassName'])) {
                $files[] = $this->generateForm($params);
            }

            $files[] = $this->generateSearch($params);
            $files[] = $this->generateController($params);

            $viewPath = $this->getViewPath();
            $templatePath = $this->getTemplatePath() . '/views';
            $softDeleteViews = ['index-all.php', 'index-deleted.php'];

            foreach (scandir($templatePath) as $file) {
                if (in_array($file, $softDeleteViews) && !$params['isSoftDelete']) { // skip softDelete views when not necessary
                    continue;
                }
                if (is_file("$templatePath/$file") && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    $files[] = new CodeFile("{$viewPath}/{$file}", $this->render("views/{$file}", $params));
                }
            }
        }

        // result
        return $files;
    }

    /**
     * generate params to render code files
     * @return array
     */
    public function generateParams($tableName)
    {
        $params = GiiHelper::readParams($tableName);
        if (empty($params)) {
            return;
        }

        // sync vars
        $this->enableI18N = $params['i18n'];
        $this->messageCategory = $params['messageCategory'];
        $this->moduleNs = $params['moduleNamespace'];
        $this->modelClass = $params['modelClass'];
        $this->searchModelClass = $params['searchClass'];
        $this->controllerNs = $params['controllerNamespace'];
        $this->controllerClass = $params['controllerClass'];

        // additions
        $params['modelSlug'] = Inflector::camel2id($params['modelClassName'], '-', true);
        $params['model'] = new $this->modelClass;
        $params['urlParams'] = $this->generateUrlParams();

        return $params;
    }

    public function generateSearch($params)
    {
        $filePath = GiiHelper::pathForClass($params['searchNamespace']) . "/" . $params['searchClassName'] . ".php";
        return new CodeFile($filePath, $this->render('search-model.php', $params));
    }

    public function generateForm($params)
    {
        $filePath = GiiHelper::pathForClass($params['formNamespace']) . "/" . $params['formClassName'] . ".php";
        return new CodeFile($filePath, $this->render('form-model.php', $params));
    }

    /**
     * generate controller class
     * @param array $params
     * @return CodeFile
     */
    public function generateController($params)
    {
        $filePath = GiiHelper::pathForClass($params['controllerNamespace']) . "/" . $params['controllerClassName'] . ".php";
        return new CodeFile($filePath, $this->render('controller.php', $params));
    }

    public function generateSearchRules()
    {
        if (($table = $this->getTableSchema()) === false) {
            return ["[['" . implode("', '", $this->getColumnNames()) . "'], 'safe']"];
        }

        $types = [];
        $skipCols = ['updated_at', 'is_deleted', 'deleted_at'];
        foreach ($table->columns as $column) {
            if ($column->name === 'created_at') {
                $types['safe'][] = $column->name;
                continue;
            }
            if (in_array($column->name, $skipCols) or substr_compare($column->name, 'at', -2, 2, true) === 0) {
                // skip several + timestamp columns
                continue;
            }
            switch ($column->type) {
                case Schema::TYPE_TINYINT:
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
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
                case Schema::TYPE_TIME:
                    $types['safe'][] = $column->name;
                    break;
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                    $types['datetime'][] = $column->name;
                    break;
                default:
                    $types['safe'][] = $column->name;
                    break;
            }
        }

        $rules = [];
        foreach ($types as $type => $columns) {
            switch ($type) {
                case 'date':     //--- added
                    $rules[] = "[['" . implode("', '", $columns) . "'], 'date', 'format' => 'yyyy-MM-dd']";
                    break;
                case 'datetime':
                    $rules[] = "[['" . implode("', '", $columns) . "'], 'date', 'format' => 'yyyy-MM-dd HH:mm:ss']";    //--- added
                    break;
                case 'safe':
                    $column_list = implode("', '", $columns);
                    $rules[] = <<<TXT
[
                ['{$column_list}'],
                \\fredyns\\stringcleaner\\yii2\\PlaintextValidator::class,
            ]
TXT;
                    break;
                default:
                    $rules[] = "[['" . implode("', '", $columns) . "'], '$type']";
                    break;
            }
        }

        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function generateSearchConditions()
    {
        $columns = [];
        if (($table = $this->getTableSchema()) === false) {
            $class = $this->modelClass;
            /* @var $model \yii\base\Model */
            $model = new $class();
            foreach ($model->attributes() as $attribute) {
                $columns[$attribute] = 'unknown';
            }
        } else {
            foreach ($table->columns as $column) {
                // skip timestamp filter
                if (strpos($column->name, '_at') === FALSE) {
                    $columns[$column->name] = $column->type;
                }
            }
        }

        $likeConditions = [];
        $hashConditions = [];
        foreach ($columns as $column => $type) {
            switch ($type) {
                case Schema::TYPE_TINYINT:
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                case Schema::TYPE_BOOLEAN:
                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                    $hashConditions[] = "static::tableName().'.{$column}' => \$this->{$column},";
                    break;
                default:
                    $likeKeyword = $this->getClassDbDriverName() === 'pgsql' ? 'ilike' : 'like';
                    $likeConditions[] = "->andFilterWhere(['{$likeKeyword}', static::tableName().'.{$column}', \$this->{$column}])";
                    break;
            }
        }

        $conditions = [];
        if (!empty($hashConditions)) {
            $conditions[] = "\$query->andFilterWhere([\n"
                . str_repeat(' ', 12) . implode("\n" . str_repeat(' ', 12), $hashConditions)
                . "\n" . str_repeat(' ', 8) . "]);\n";
        }
        if (!empty($likeConditions)) {
            $conditions[] = "\$query\n"
                . str_repeat(' ', 12) . implode("\n" . str_repeat(' ', 12), $likeConditions) . ";\n";
        }

        return $conditions;
    }

    /**
     * @return array List of providers. Keys and values contain the same strings
     */
    public function getMyProviderList()
    {
        $files = FileHelper::findFiles(
            __DIR__ . DIRECTORY_SEPARATOR . 'providers', [
                'only' => ['*.php'],
                'recursive' => false,
            ]
        );

        foreach ($files as $file) {
            require_once $file;
        }

        $providers = array_filter(
            get_declared_classes(), function ($a) {
            return stripos($a, __NAMESPACE__ . '\providers') !== false;
        }
        );

        return array_combine($providers, $providers);
    }

}