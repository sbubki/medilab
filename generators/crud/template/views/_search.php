<?php

use yii\helpers\Inflector;

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
/* @var $indexWidgetType string  */
/* @var $gridColumns string[]  */
/* @var $formLayout string  */
/* @var $modelSlug string  */
/* @var $urlParams string  */

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $model <?= $searchClass ?> */
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="<?= Inflector::camel2id($modelClassName, '-', true) ?>-search">

    <?= "<?php\n" ?>
    $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]);
    ?>

<?php
$count = 0;
foreach ($tableSchema->getColumnNames() as $attribute) {
    if (++$count < 6) {
        echo str_repeat(' ', 4)."<?= ".$generator->generateActiveSearchField($attribute)." ?>\n\n";
    } else {
        echo str_repeat(' ', 4)."<?php // echo ".$generator->generateActiveSearchField($attribute)." ?>\n\n";
    }
}
?>
    <div class="form-group">
        <div class="col-sm-8 col-sm-offset-2">
            <?= '<?= ' ?>Html::submitButton(<?= $generator->generateString('Search') ?>, ['class' => 'btn btn-primary']) ?>
            <?= '<?= ' ?>Html::resetButton(<?= $generator->generateString('Reset') ?>, ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <?= '<?php' ?> ActiveForm::end(); ?>

</div>
