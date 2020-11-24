<?php

use yii\db\Schema;
use yii\helpers\StringHelper;

/* @var $this yii\web\View  */
/* @var $generator app\generators\crud\Generator  */
/* resource */
/* @var $tableName string  */
/* @var $tableSchema TableSchema  */
/* @var $model yii\db\ActiveRecord  */
/* scope */
/* @var $moduleId string  */
/* @var $subNamespace string  */
/* translation */
/* @var $i18n bool  */
/* @var $messageCategory string  */
/* model */
/* @var $modelNamespace string  */
/* @var $modelClassName string  */
/* @var $modelClass string  */
/* @var $isSoftDelete bool  */
/* @var $modelNameSingular string  */
/* @var $modelNamePlural string  */
/* @var $labels string[]  */
/* @var $dictionaries array  */
/* @var $relations array  */
/* @var $groupedRules array  */
/* @var $nameAttribute string  */
/* @var $safeAttributes string[]  */
/* search */
/* @var $searchNamespace string  */
/* @var $searchClassName string  */
/* @var $searchClass string  */
/* @var $dateFilters array  */
/* @var $timestampFilters array  */
/* form */
/* @var $formNamespace string  */
/* @var $formClassName string  */
/* @var $formClass string  */
/* controller */
/* @var $controllerNamespace string  */
/* @var $controllerClassName string  */
/* @var $controllerClass string  */
/* @var $useGenericActions string  */
/* @var $useTransaction bool  */
/* view */
/* @var $viewPath string  */
/* @var $gridColumns string[]  */
/* @var $formLayout string  */
/* @var $modelSlug string  */
/* @var $urlParams string  */

$rules = $generator->generateSearchRules();
$searchAttributes = $generator->getSearchAttributes();
$searchConditions = $generator->generateSearchConditions();

echo "<?php\n";
?>

namespace <?= $searchNamespace ?>;

<?php if($dateFilters): ?>
use app\components\DateSearch;
<?php endif;?>
<?php if($timestampFilters): ?>
use app\components\TimestampSearch;
<?php endif;?>
use <?= $generator->modelClass ?>;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * <?= $searchClassName ?> represents the model behind the search form about `<?= $generator->modelClass ?>`.
 */
class <?= $searchClassName ?> extends <?= $modelClassName ?>

{
<?php if($dateFilters OR $timestampFilters): ?>
<?php foreach ($dateFilters as $rangeKey => $columnName): ?>
    public $<?= $rangeKey ?>Search;
<?php endforeach;?>
<?php foreach ($timestampFilters as $rangeKey => $columnName): ?>
    public $<?= $rangeKey ?>Search;
<?php endforeach;?>

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // range search initiations
<?php foreach ($dateFilters as $rangeKey => $columnName): ?>
        $this-><?= $rangeKey ?>Search = new DateSearch([
            'attribute' => '<?= $columnName ?>',
            'field' => static::tableName().'.<?= $columnName ?>',
        ]);
<?php endforeach;?>
<?php foreach ($timestampFilters as $rangeKey => $columnName): ?>
        $this-><?= $rangeKey ?>Search = new TimestampSearch([
            'attribute' => '<?= $columnName ?>',
            'field' => static::tableName().'.<?= $columnName ?>',
        ]);
<?php endforeach;?>
    }
<?php endif;?>

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            <?= implode(",\n            ", $rules) ?>,
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = <?= $modelClassName ?>::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
<?php if($tableSchema->getColumn('id') !== null): ?>
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ],
<?php endif;?>
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        <?= implode("\n        ", $searchConditions) ?>

<?php if($dateFilters OR $timestampFilters): ?>
<?php foreach ($dateFilters as $rangeKey => $columnName): ?>
        $this-><?= $rangeKey ?>Search->applyFilter($query, $this-><?= $columnName ?>);
<?php endforeach;?>
<?php foreach ($timestampFilters as $rangeKey => $columnName): ?>
        $this-><?= $rangeKey ?>Search->applyFilter($query, $this-><?= $columnName ?>);
<?php endforeach;?>

<?php endif;?>
        return $dataProvider;
    }

}